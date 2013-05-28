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
	protected $actions = array ();

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
					'name'          => $name
				,	'id'            => $this->prefix . $action
				,	'description'   => __( 'Use the Unfiltered Text widget.', 'plugin_magic_widgets' )
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
	 * Constructor
	 *
	 * @uses apply_filters( 'magic_widgets_name' )
	 */
	public function __construct()
	{
		// You may change the name per filter.
		// Use add_filter( 'magic_widgets_name', 'your custom_filter', 10, 1 );
		$widgetname = __( 'Unfiltered Text', 'plugin_magic_widgets' );
		parent::__construct(
			'unfiltered_text',
			$widgetname,
			array( 'description' => __( 'Pure Markup', 'plugin_magic_widgets' ) ),
			array( 'width' => 300, 'height' => 150 )
		);
	}

	/**
	 * Output.
	 *
	 * @param  array $args
	 * @param  array $instance
	 * @return void
	 */
	public function widget( Array $args, Array $instance )
	{
		do_action( 'tmw_before_show_widget', $instance, $args );

		if ( empty ( $instance[ 'visibility' ] ) )
			return print $instance[ 'text' ];

		$user_logged_in = is_user_logged_in();

		switch ( $instance[ 'visibility' ] )
		{
			case 'all':
				return print $instance['text'];

			case 'members':
				return print $user_logged_in ? $instance['text'] : '';

			case 'anonymous':
				return print $user_logged_in ? '' : $instance['text'];

			default: // custom visibility option
				do_action( 'tmw_show_widget', $instance, $args );
		}

		do_action( 'tmw_after_show_widget', $instance, $args );
	}

	/**
	 * Prepares the content. Nothing to do here.
	 * @param  array $new_instance New content
	 * @param  array $old_instance Old content
	 * @return array New content
	 */
	public function update( Array $new_instance, Array $old_instance )
	{
		$visibility = $this->get_visibility_options();
		if ( empty ( $new_instance[ 'visibility' ] )
			or ! isset ( $visibility[ $new_instance[ 'visibility' ] ] )
		)
			$new_instance[ 'visibility' ] = $this->get_default_visibility();

		return $new_instance;
	}

	/**
	 * Backend form.
	 * @param array $instance
	 * @return void
	 */
	public function form( Array $instance )
	{
		$instance = wp_parse_args(
			$instance,
			array(
				'text'       => '',
				'visibility' => $this->get_default_visibility()
			)
		);
		$text = format_to_edit( $instance[ 'text' ] );
		print $this->get_textarea( $text, 'text' );
		print $this->get_visibility_html( $instance[ 'visibility' ], 'visibility' );
	}

	/**
	 * Create the textarea for the main content.
	 *
	 * @param  string $content
	 * @param  string $name
	 * @return string
	 */
	protected function get_textarea( $content, $name )
	{
		return sprintf(
			'<p><textarea class="widefat" rows="7" cols="20" id="%1$s" name="%2$s">%3$s</textarea></p>',
			$this->get_field_id( $name ),
			$this->get_field_name( $name ),
			$content
		);
	}

	/**
	 * Render visibility radio buttons.
	 *
	 * @param  string $current
	 * @param  string $name
	 * @return string
	 */
	protected function get_visibility_html( $current, $name )
	{
		$options = $this->get_visibility_options();
		$out = '<fieldset><legend>' . __( 'Visibility', 'plugin_magic_widgets' ) .'</legend><ul>';

		foreach ( $options as $key => $label )
		{
			$out .= sprintf(
				'<li><label for="%1$s"><input type="radio" name="%2$s" id="%1$s" value="%3$s" %4$s> %5$s</label></li>',
				$this->get_field_id( $name ),
				$this->get_field_name( $name ),
				$key,
				checked( $key, $current, FALSE ),
				esc_html( $label )
			);
		}

		return "$out</fieldset>";
	}

	/**
	 * Default options for widget visibility.
	 *
	 * @uses   apply_filters tmw_visibility_options
	 * @return string
	 */
	protected function get_visibility_options()
	{
		$options = array (
			'all'       => __( 'All', 'plugin_magic_widgets' ),
			'members'   => __( 'Logged in users', 'plugin_magic_widgets' ),
			'anonymous' => __( 'Anonymous visitors', 'plugin_magic_widgets' )
		);

		return apply_filters( 'tmw_visibility_options', $options );
	}

	/**
	 * Get the first visibility options key as default.
	 *
	 * @return string
	 */
	protected function get_default_visibility()
	{
		$options = $this->get_visibility_options();
		return key( $options );

	}
}