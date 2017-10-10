<?php
/*
Plugin Name: Pegasus Toggle Plugin
Plugin URI:  https://developer.wordpress.org/plugins/the-basics/
Description: This allows you to create toggle on your website with just a shortcode.
Version:     1.0
Author:      Jim O'Brien
Author URI:  https://visionquestdevelopment.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/

function pegasus_toggle_menu_item() {
	add_menu_page("Toggle", "Toggle", "manage_options", "pegasus_toggle_plugin_options", "pegasus_toggle_plugin_settings_page", null, 99);

}
add_action("admin_menu", "pegasus_toggle_menu_item");

function pegasus_toggle_plugin_settings_page() { ?>
	<div class="wrap pegasus-wrap">
		<h1>Toggle Usage</h1>

		<p>Toggle Usage 1: <pre>[toggle title="Title"] <?php echo htmlspecialchars('<p>Get your copy now!Suspendisse vitae bibendum mauris. Nunc iaculis nisl vitae laoreet elementum donec dignissim metus sit.</p>'); ?>[/toggle]</pre></p>


		<p style="color:red;">MAKE SURE YOU DO NOT HAVE ANY RETURNS OR <?php echo htmlspecialchars('<br>'); ?>'s IN YOUR SHORTCODES, OTHERWISE IT WILL NOT WORK CORRECTLY</p>


	</div>
	<?php
}

function pegasus_toggle_plugin_styles() {

	wp_enqueue_style( 'toggle-plugin-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/toggle.css', array(), null, 'all' );
	//wp_enqueue_style( 'slippery-slider-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/slippery-slider.css', array(), null, 'all' );

}
add_action( 'wp_enqueue_scripts', 'pegasus_toggle_plugin_styles' );

/*
 * Proper way to enqueue JS
*/
function pegasus_toggle_plugin_js() {

	//wp_enqueue_script( 'toggle-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/slippery.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'pegasus-toggle-plugin-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/plugin.js', array( 'jquery' ), null, true );

} //end function
add_action( 'wp_enqueue_scripts', 'pegasus_toggle_plugin_js' );


/*~~~~~~~~~~~~~~~~~~~~
	CALLOUT
~~~~~~~~~~~~~~~~~~~~~*/

// [toggle title="title"] text [/toggle]
function pegasus_toggle_func( $atts, $content = null ) {
	$a = shortcode_atts( array(
		'title' => '',
	), $atts );

	$output = '';
	global $pegasus_toggle_counter;

	$pegasus_toggle_counter = $pegasus_toggle_counter ? $pegasus_toggle_counter : 0;

	$title =  "{$a['title']}";

	$output .= '<div class="pegasus-toggle">';
	$output .= '<a id="toggle-trigger-' . $pegasus_toggle_counter . '" class="pegasus-trigger" href="#toggle-content-' . $pegasus_toggle_counter . '" >' . $title . '</a>';
	$output .= '<div id="toggle-content-' . $pegasus_toggle_counter . '" class="toggle-content">';
	$output .= $content;
	$output .= '</div>';
	$output .= '</div>';

	$pegasus_toggle_counter++;

	return $output;
}
add_shortcode( 'toggle', 'pegasus_toggle_func' );

