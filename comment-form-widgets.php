<?php # -*- coding: utf-8 -*-
/*
Plugin Name: Toscho’s Magic Widgets for the Comment Form
Plugin URI:  http://toscho.de/2011/wordpress-magische-widgets/
Description: Additional magic widgets for the comment form. Requires Toscho’s Magic Widgets. Actions are listed in order of appearance.
Version:     1.0
Author:      Thomas Scholz
Author URI:  http://toscho.de
License:     GPL v2
*/
if ( ! function_exists( 'tmw_comment_form' ) )
{
	add_filter( 'magic_widgets_actions', 'tmw_comment_form', 10, 1 );
	function tmw_comment_form( $actions )
	{
		// Add
		$actions['comment_form_before']            = 'Comment Form Before';
		$actions['comment_form_must_log_in_after'] = 'Comment Form Must Log in After';
		$actions['comment_form_top']               = 'Comment Form Top';
		$actions['comment_form_logged_in_after']   = 'Comment Form Logged In After';
		$actions['comment_form_before_fields']     = 'Comment Form Before Fields';
		$actions['comment_form_after_fields']      = 'Comment Form After Fields';
		$actions['comment_form']                   = 'Comment Form';
		$actions['comment_form_after']             = 'Comment Form After';
		$actions['comment_form_comments_closed']   = 'Comment Form Comments Closed';

		return $actions;
	}
}