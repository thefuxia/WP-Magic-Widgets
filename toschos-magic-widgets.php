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
	 * @var array
	 */
	protected $actions = array (
		'wp_head'       => 'Front End Header'
	,	'wp_footer'     => 'Front End Footer'
	,	'admin_head'    => 'Back End Header'
	,	'admin_footer'  => 'Back End Footer'
	);

	/**
	 * Prefix for the widget IDs.
	 * Filled by the constructor.
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Handler for the action 'widgets_init'. Instantiates this class.
	 * @uses apply_filters( 'tmw_class', __CLASS__ )
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
	 * Constructor. Registers the widget areas and the Unfiltered Text widget.
	 *
	 * @uses apply_filters( 'magic_widgets_actions' )
	 */
	public function __construct()
	{
		// Uppercase letters don’t work.
		$this->prefix = strtolower( __CLASS__ ) . '_';

		// You may add or remove actions here.
		// Use add_filter( 'magic_widgets_actions', 'your custom_filter', 10, 1 );
		$this->actions = apply_filters( 'magic_widgets_actions', $this->actions );

		// The extra widget.
		register_widget( 'Unfiltered_Text_Widget' );

		$this->sidebar_actions();
	}

	/**
	 * Set up sidebars and add the print_widget action.
	 *
	 * @uses add_action()
	 * @return void
	 */
	public function sidebar_actions()
	{
		// Register the areas and additional actions.
		foreach ( $this->actions as $action => $name )
		{
			register_sidebar(
				array (
					'name'          => $name
				,	'id'            => $this->prefix . $action
				,	'description'   => 'Use the *Unfiltered Text* Widget.'
				// Erase all other output
				,	'before_widget' => ''
				,	'after_widget'  => ''
				,	'before_title'  => ''
				,	'after_title'   => ''
				)
			);

			add_action( $action, array ( $this, 'print_widget' ) );
		}
	}

	/**
	 * Output.
	 * @return void
	 */
	public function print_widget()
	{
		// current_filter() is the name of the action.
		dynamic_sidebar( $this->prefix . current_filter() );
	}
}

/**
 * Simplified variant of the native text widget class.
 * @version 1.0
 */
class Unfiltered_Text_Widget extends WP_Widget
{
	/**
	 * @uses apply_filters( 'magic_widgets_name' )
	 */
	public function __construct()
	{
		// You may change the name per filter.
		// Use add_filter( 'magic_widgets_name', 'your custom_filter', 10, 1 );
		$widgetname = apply_filters( 'magic_widgets_name', 'Unfiltered Text' );
		parent::__construct(
			'unfiltered_text'
		,	$widgetname
		,	array( 'description' => 'Pure Markup' )
		,	array( 'width' => 300, 'height' => 150 )
		);
	}

	/**
	 * Output.
	 * @param array $args
	 * @param array $instance
	 * @return array
	 */
	public function widget( $args, $instance )
	{
		echo $instance['text'];
	}

	/**
	 * Prepares the content. Nothing to do here.
	 * @param  array $new_instance New content
	 * @param  array $old_instance Old content
	 * @return array New content
	 */
	public function update( $new_instance, $old_instance )
	{
		return $new_instance;
	}

	/**
	 * Backend form.
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance )
	{
		$instance = wp_parse_args( (array) $instance, array( 'text' => '' ) );
		$text     = format_to_edit($instance['text']);
?>
		<textarea class="widefat" rows="7" cols="20" id="<?php
			echo $this->get_field_id('text');
		?>" name="<?php
			echo $this->get_field_name('text');
		?>"><?php
			echo $text;
		?></textarea>
<?php
	}
}