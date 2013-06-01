<?php
/*
 * Plugin Name: Toscho’s Magic Widgets
 * Plugin URI:  http://toscho.de/2011/wordpress-magische-widgets/
 * Description: Extra widgets for your HTML headers and footers.
 * Version:     2013.05.28
 * Author:      Thomas Scholz
 * Author URI:  http://toscho.de
 * License:     GPL v2
 * Textdomain:  plugin_magic_widgets
 * Domain Path: /languages
*/

add_action( 'widgets_init', array ( 'Toscho_Magic_Widgets', 'init' ), 20 );

/**
 * Master class.
 * @version 1.0
 */
class Toscho_Magic_Widgets
{
	/**
	 * Actions and names for the new Widgets.
	 *
	 * @type array
	 */
	protected $actions = array ();

	/**
	 * Prefix for the widget IDs.
	 * Filled by the constructor.
	 *
	 * @type string
	 */
	protected $prefix = '';

	/**
	 * Handler for the action 'widgets_init'. Instantiates this class.
	 *
	 * @uses   apply_filters tmw_class
	 * @return void
	 */
	public static function init()
	{
		// If want to use another class (an extension maybe),
		// change the class name here.
		$class = apply_filters( 'tmw_class', __CLASS__ );

		// Named global variable to make access for other scripts easier.
		if ( empty ( $GLOBALS[ $class ] ) )
		{
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Constructor
	 *
	 * Registers the Unfiltered Text widget.
	 */
	public function __construct()
	{
		// Uppercase letters don’t work.
		$this->prefix = strtolower( __CLASS__ ) . '_';

		if ( is_admin() )
			$this->load_language();

		// The extra widget.
		$widget_class = 'Unfiltered_Text_Widget';

		if ( ! class_exists( $widget_class ) )
			require_once plugin_dir_path( __FILE__ ) . "class.$widget_class.php";

		register_widget( $widget_class );

		$this->sidebar_actions();
	}

	/**
	 * Set up sidebars and add the print_widget action
	 *
	 * @uses   add_action()
	 * @return void
	 */
	public function sidebar_actions()
	{
		$this->actions = array (
			'wp_head'       => __( 'Front End Header', 'plugin_magic_widgets' ),
			'wp_footer'     => __( 'Front End Footer', 'plugin_magic_widgets' ),
			'admin_head'    => __( 'Back End Header', 'plugin_magic_widgets' ),
			'admin_footer'  => __( 'Back End Footer', 'plugin_magic_widgets' )
		);

		// You may add or remove actions here.
		// Use add_filter( 'magic_widgets_actions', 'your custom_filter', 10, 1 );
		$this->actions = apply_filters( 'magic_widgets_actions', $this->actions );

		// Register the areas and additional actions.
		foreach ( $this->actions as $action => $name )
		{
			register_sidebar(
				array (
					'name'          => $name,
					'id'            => $this->prefix . $action,
					'description'   => __( 'Use the Unfiltered Text widget.', 'plugin_magic_widgets' ),
				// Erase all other output
					'before_widget' => '',
					'after_widget'  => '',
					'before_title'  => '',
					'after_title'   => ''
				)
			);

			add_action( $action, array ( $this, 'print_widget' ) );
		}
	}

	/**
	 * Output
	 *
	 * @return boolean
	 */
	public function print_widget()
	{
		// current_filter() is the name of the action.
		return dynamic_sidebar( $this->prefix . current_filter() );
	}

	/**
	 * Loads translation file.
	 *
	 * @return bool
	 */
	public function load_language()
	{
		$path = plugin_basename( dirname( __FILE__ ) ) . '/languages';
		return load_plugin_textdomain( 'plugin_magic_widgets', FALSE, $path );
	}

	/**
	 * Remove translations from memory.
	 *
	 * @return void
	 */
	public function unload_language()
	{
		unset ( $GLOBALS['l10n']['plugin_magic_widgets'] );
	}
}
