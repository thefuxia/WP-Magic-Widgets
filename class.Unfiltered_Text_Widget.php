<?php # -*- coding: utf-8 -*- php-version: 5.4 -*-

/**
 * Similar to the native Text widget, this class offers a plain textarea.
 * And visibility options.
 *
 * @version 2013.05.28
 */
class Unfiltered_Text_Widget extends WP_Widget
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct(
			'unfiltered_text',
			__( 'Unfiltered Text', 'plugin_magic_widgets' ),
			array (
				'description' => __( 'Pure Markup', 'plugin_magic_widgets' )
			),
			array (
				'width'  => 300,
				'height' => 150
			)
		);
	}

	/**
	 * Front end output
	 *
	 * @param  array $args
	 * @param  array $instance
	 * @return void
	 */
	public function widget( $args, $instance )
	{
		do_action( 'tmw_before_show_widget', $instance, $args );

		if ( empty ( $instance[ 'visibility' ] ) ) {
			print $instance[ 'text' ];

			return;
		}

		$user_logged_in = is_user_logged_in();

		switch ( $instance[ 'visibility' ] )
		{
			case 'all':
				print $instance['text'];
				break;

			case 'members':
				print $user_logged_in ? $instance['text'] : '';
				break;

			case 'anonymous':
				print $user_logged_in ? '' : $instance['text'];
				break;

			default: // custom visibility option
				do_action( 'tmw_show_widget', $instance, $args );
		}

		do_action( 'tmw_after_show_widget', $instance, $args );
	}

	/**
	 * Prepares the content
	 *
	 * @param  array $new_instance New content
	 * @param  array $old_instance Old content
	 * @return array New content
	 */
	public function update( $new_instance, $old_instance )
	{
		$visibility = $this->get_visibility_options();

		if ( empty ( $new_instance[ 'visibility' ] )
			or ! isset ( $visibility[ $new_instance[ 'visibility' ] ] )
		)
			$new_instance[ 'visibility' ] = $this->get_default_visibility();

		return $new_instance;
	}

	/**
	 * Backend form
	 *
	 * @param array $instance
	 * @return void
	 */
	public function form( $instance )
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
	 * Create the textarea for the main content
	 *
	 * @param  string $content
	 * @param  string $name
	 * @return string
	 */
	protected function get_textarea( $content, $name )
	{
		return sprintf(
			'<p>
			<textarea class="widefat code" rows="7" cols="20" id="%1$s" name="%2$s">%3$s</textarea>
			</p>',
			$this->get_field_id( $name ),
			$this->get_field_name( $name ),
			$content
		);
	}

	/**
	 * Render visibility radio buttons
	 *
	 * @param  string $current
	 * @param  string $name
	 * @return string
	 */
	protected function get_visibility_html( $current, $name )
	{
		$options = $this->get_visibility_options();
		$out = '<fieldset>
		<legend><b>' . __( 'Visibility', 'plugin_magic_widgets' ) .'</b></legend>
		<ul>';

		foreach ( $options as $key => $label )
		{
			$out .= sprintf(
				'<li>
				<label for="%1$s">
				<input type="radio" name="%2$s" id="%1$s" value="%3$s" %4$s> %5$s
				</label>
				</li>',
				$this->get_field_id( $key ),
				$this->get_field_name( $name ),
				$key,
				checked( $key, $current, FALSE ),
				esc_html( $label )
			);
		}

		return "$out</ul></fieldset>";
	}

	/**
	 * Default options for widget visibility
	 *
	 * @uses   apply_filters tmw_visibility_options
	 * @return array
	 */
	protected function get_visibility_options()
	{
		$options = array (
			'all'       => __( 'All', 'plugin_magic_widgets' ),
			'members'   => __( 'Members only', 'plugin_magic_widgets' ),
			'anonymous' => __( 'Anonymous visitors only', 'plugin_magic_widgets' )
		);

		return apply_filters( 'tmw_visibility_options', $options );
	}

	/**
	 * Get the first visibility options key as default
	 *
	 * @return string
	 */
	protected function get_default_visibility()
	{
		$options = $this->get_visibility_options();
		return key( $options );
	}
}
