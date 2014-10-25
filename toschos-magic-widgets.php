<?php
/*
 * Plugin Name: Toscho’s Magic Widgets
 * Plugin URI:  http://toscho.de/2011/wordpress-magische-widgets/
 * Description: Extra widgets for your HTML headers and footers.
 * Version:     2014.10.25
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
	 * Prefix for the widget IDs.
	 * Filled by the constructor.
	 *
	 * @type string
	 */
	protected $prefix = '';

	/**
	 * Handler for the action 'widgets_init'. Instantiates this class.
	 *
	 * @return void
	 */
	public static function init()
	{
		new self;
	}

	/**
	 * Constructor
	 *
	 * Registers the Unfiltered Text widget.
	 */
	public function __construct()
	{
		/** @noinspection PhpUnusedLocalVariableInspection */
		$dummy = __( 'Extra widgets for your HTML headers and footers.', 'plugin_magic_widgets' );
		// Uppercase letters don’t work.
		$this->prefix = strtolower( __CLASS__ ) . '_';

		if ( is_admin() )
			$this->load_language();

		// The extra widget.
		$widget_class = 'Unfiltered_Text_Widget';

		if ( ! class_exists( $widget_class ) ) {
			/** @noinspection PhpIncludeInspection */
			require_once plugin_dir_path( __FILE__ ) . "class.$widget_class.php";
		}

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
		$actions = array (
			'wp_head'       => __( 'Front End Header', 'plugin_magic_widgets' ),
			'wp_footer'     => __( 'Front End Footer', 'plugin_magic_widgets' ),
			'admin_head'    => __( 'Back End Header',  'plugin_magic_widgets' ),
			'admin_footer'  => __( 'Back End Footer',  'plugin_magic_widgets' ),
			'login_head'    => __( 'Log-in Header',    'plugin_magic_widgets' ),
			'login_footer'  => __( 'Log-in Footer',    'plugin_magic_widgets' ),
		);

		/**
		 * Change extra sidebar registrations.
		 * You must return an array.
		 *
		 * @param array $actions
		 */
		$actions = apply_filters( 'magic_widgets_actions', $actions );

		// Register the areas and additional actions.
		foreach ( $actions as $action => $name )
			$this->register_action( $action, $name );
	}

	/**
	 * Register a sidebar for an action.
	 *
	 * @param  string $action Action name
	 * @param  string $name   Sidebar name
	 * @return void
	 */
	private function register_action( $action, $name )
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

	/**
	 * Output
	 *
	 * @return boolean
	 */
	public function print_widget()
	{
		/* In wp-admin/includes/widgets.php:: wp_list_widget_controls() a filter
		 * is set that makes all widgets render the controls instead of the
		 * content. We have to remove it to get the real output.
		 */
		if ( 'admin_footer' === current_filter() )
			remove_filter(
				'dynamic_sidebar_params',
				'wp_list_widget_controls_dynamic_sidebar'
			);

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
