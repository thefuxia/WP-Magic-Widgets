# Magic Widgets

A WordPress plugin that assigns widgets to action hooks.

It defines sidebar areas in `wp_head`, `wp_footer`, `admin_head` and `admin_footer`.
You can change the list, add your own areas or remove the default areas.

Additionally, the plugin creates a new widget, called *Unfiltered Text*.
It is very similar to the regular *Text* widget, but it doesn’t insert any extra markup.

If you want to help: we need more translations.

## Hooks

There is one hook in the main plugin class:

```php
	$actions = array (
		'wp_head'       => __( 'Front End Header', 'plugin_magic_widgets' ),
		'wp_footer'     => __( 'Front End Footer', 'plugin_magic_widgets' ),
		'admin_head'    => __( 'Back End Header', 'plugin_magic_widgets' ),
		'admin_footer'  => __( 'Back End Footer', 'plugin_magic_widgets' )
	);

	$actions = apply_filters( 'magic_widgets_actions', $actions );
```

You can add your own sidebar areas here.

The widget class `Unfiltered_Text_Widget` offers more hooks:

```php
	apply_filters( 'tmw_visibility_options', $options )
```
`$options` is a list of visibility selections:

```php
	$options = array (
		'all'       => __( 'All', 'plugin_magic_widgets' ),
		'members'   => __( 'Members only', 'plugin_magic_widgets' ),
		'anonymous' => __( 'Anonymous visitors only', 'plugin_magic_widgets' )
	);
```

You can add new options for particular roles, languages, visitors with comment
cookies … be creative.

Then you have to hook into the output handler:

```php
	do_action( 'tmw_show_widget', $instance, $args );
```

`$instance['visibility']` will tell you what visibility the user has selected.
This action fires for custom visibility selections only.

There are two other output actions with the same arguments:

```php
	do_action( 'tmw_before_show_widget', $instance, $args );
	do_action( 'tmw_after_show_widget', $instance, $args );
```

Both run on every output, no matter what the `visibility` is.

[Beschreibung auf Deutsch](http://toscho.de/2011/wordpress-plugin-magische-widgets/)