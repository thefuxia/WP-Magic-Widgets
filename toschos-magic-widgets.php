<?php
/*
Plugin Name: Toscho’s Magic Widgets
Plugin URI:  http://toscho.de/2011/wordpress-magische-widgets/
Description: Extra widgets for your HTML headers and footers.
Version:     1.1
Author:      Thomas Scholz
Author URI:  http://toscho.de
License:     GPL v2
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
	 * @return void
	 */
	public static function init()
	{
		// Named global variable to make access for other scripts easier.
		if ( empty ( $GLOBALS[ __CLASS__ ] ) )
		{
			$class = __CLASS__;
			$GLOBALS[ $class ] = new $class;
		}
	}

	/**
	 * Constructor. Registers the widget areas and the Unfiltered Text widget.
	 */
	public function __construct()
	{
		// Uppercase letters don’t work.
		$this->prefix = strtolower( __CLASS__ );

		// You may add or remove actions here.
		// Use add_filter( 'magic_widgets_actions', 'your custom_filter', 10, 1 );
		$this->actions = apply_filters( 'magic_widgets_actions', $this->actions );

		// The extra widget.
		register_widget( 'Unfiltered_Text_Widget' );

		// Register the areas and additional actions.
		foreach ( $this->actions as $action => $name )
		{
			register_sidebar(
				array (
					'name'          => $name
				,	'id'            => $this->prefix . '_' . $action
				// Erase all other output
				,	'before_widget' => ''
				,	'after_widget'  => ''
				,	'before_title'  => ''
				,	'after_title'   => ''
				,	'description'   => 'Use the *Unfiltered Text* Widget.'
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
		dynamic_sidebar( $this->prefix . '_' . current_filter() );
	}
}

/**
 * Simpified copy of the native text widget class.
 * @version 1.0
 */
class Unfiltered_Text_Widget extends WP_Widget
{
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