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



	global $theme;

	$theme = wp_get_theme();

	function pegasus_toggleslide_admin_table_css() {
		if ( toggleslide_check_main_theme_name() == 'Pegasus' || toggleslide_check_main_theme_name() == 'Pegasus Child' ) {
			//do nothing
		} else {
			//wp_register_style('toggleslide-admin-table-css', trailingslashit(plugin_dir_url(__FILE__)) . 'css/pegasus-toggleslide-admin-table.css', array(), null, 'all');
			ob_start();
			?>
				pre {
					background-color: #f9f9f9;
					border: 1px solid #aaa;
					page-break-inside: avoid;
					font-family: monospace;
					font-size: 15px;
					line-height: 1.6;
					margin-bottom: 1.6em;
					max-width: 100%;
					overflow: auto;
					padding: 1em 1.5em;
					display: block;
					word-wrap: break-word;
				}
				input[type="text"].code {
					width: 100%;
				}
				table.pegasus-table {
					width: 100%;
					border-collapse: collapse;
					border-color: #777 !important;
				}
				table.pegasus-table th {
					background-color: #f1f1f1;
					text-align: left;
				}
				table.pegasus-table th,
				table.pegasus-table td {
					border: 1px solid #ddd;
					padding: 8px;
				}
				table.pegasus-table tr:nth-child(even) {
					background-color: #f2f2f2;
				}
				table.pegasus-table thead tr { background-color: #282828; }
				table.pegasus-table thead tr td { padding: 10px; }
				table.pegasus-table thead tr td strong { color: white; }
				table.pegasus-table tbody tr:nth-child(0) { background-color: #cccccc; }
				table.pegasus-table tbody tr td { padding: 10px; }
				table.pegasus-table code { color: #d63384; }

			<?php
			// Get the buffered content
			$inline_css = ob_get_clean();

			wp_register_style('toggleslide-admin-table-css', false);
			wp_enqueue_style('toggleslide-admin-table-css');

			wp_add_inline_style('toggleslide-admin-table-css', $inline_css);
		}
	}

	add_action('admin_enqueue_scripts', 'pegasus_toggleslide_admin_table_css');

	function toggleslide_check_main_theme_name() {
		$current_theme_slug = get_option('stylesheet'); // Slug of the current theme (child theme if used)
		$parent_theme_slug = get_option('template');    // Slug of the parent theme (if a child theme is used)

		//error_log( "current theme slug: " . $current_theme_slug );
		//error_log( "parent theme slug: " . $parent_theme_slug );

		if ( $current_theme_slug == 'pegasus' ) {
			return 'Pegasus';
		} elseif ( $current_theme_slug == 'pegasus-child' ) {
			return 'Pegasus Child';
		} else {
			return 'Not Pegasus';
		}
	}

	function pegasus_toggleslide_menu_item() {
		if ( toggleslide_check_main_theme_name() == 'Pegasus' || toggleslide_check_main_theme_name() == 'Pegasus Child' ) {
			//do nothing
		} else {
			//echo 'This is NOT the Pegasus theme';
			add_menu_page(
				"Toggleslide", // Page title
				"Toggleslide", // Menu title
				"manage_options", // Capability
				"pegasus_toggleslide_plugin_options", // Menu slug
				"pegasus_toggleslide_plugin_settings_page", // Callback function
				null, // Icon
				94 // Position in menu
			);
		}
	}
	add_action("admin_menu", "pegasus_toggleslide_menu_item");

	function pegasus_toggleslide_plugin_settings_page() { ?>
	    <div class="wrap pegasus-wrap">
			<h1>Toggleslide Usage</h1>

			<div>
				<h3>Toggleslide Usage 1:</h3>

				<pre >[toggle title="the_title" ]The content for the toggleslide[/toggle]</pre>

				<input
					type="text"
					readonly
					value="<?php echo esc_html('[toggle title="the_title" ]The content for the toggleslide[/toggle]'); ?>"
					class="regular-text code"
					id="my-shortcode"
					onClick="this.select();"
				>
			</div>

			<p style="color:red;">MAKE SURE YOU DO NOT HAVE ANY RETURNS OR <?php echo htmlspecialchars('<br>'); ?>'s IN YOUR SHORTCODES, OTHERWISE IT WILL NOT WORK CORRECTLY</p>

			<div>
				<?php echo pegasus_toggleslide_settings_table(); ?>
			</div>
		</div>
	<?php
	}

	function pegasus_toggleslide_settings_table() {

		$data = json_decode( file_get_contents( plugin_dir_path( __FILE__ ) . 'settings.json' ), true );

		if (json_last_error() !== JSON_ERROR_NONE) {
			return '<p style="color: red;">Error: Invalid JSON provided.</p>';
		}

		// Start building the HTML
		$html = '<table border="0" cellpadding="1" class="table pegasus-table" align="left">
		<thead>
		<tr style="background-color: #282828;">
		<td <span><strong>Name</strong></span></td>
		<td <span><strong>Attribute</strong></span></td>
		<td <span><strong>Options</strong></span></td>
		<td <span><strong>Description</strong></span></td>
		<td <span><strong>Example</strong></span></td>
		</tr>
		</thead>
		<tbody>';

		// Iterate over the data to populate rows
		if (!empty($data['rows'])) {
			foreach ($data['rows'] as $section) {
				// Add section header
				$html .= '<tr >';
				$html .= '<td colspan="5">';
				$html .= '<span>';
				$html .= '<strong>' . htmlspecialchars($section['section_name']) . '</strong>';
				$html .= '</span>';
				$html .= '</td>';
				$html .= '</tr>';

				// Add rows in the section
				foreach ($section['rows'] as $row) {
					$html .= '<tr>
						<td >' . htmlspecialchars($row['name']) . '</td>
						<td >' . htmlspecialchars($row['attribute']) . '</td>
						<td >' . nl2br(htmlspecialchars($row['options'])) . '</td>
						<td >' . nl2br(htmlspecialchars($row['description'])) . '</td>
						<td ><code>' . htmlspecialchars($row['example']) . '</code></td>
					</tr>';
				}
			}
		}

		$html .= '</tbody></table>';

		// Return the generated HTML
		return $html;
	}

	/*
	if( $theme['Name'] === 'Pegasus' || $theme['Name'] === 'Pegasus Child' ) {

		function pegasus_toggle_submenu_item() {
			add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '' )
			add_submenu_page( "pegasus_options", "Pegasus Toggle Options", "Toggle", "manage_options", 'pegasus_toggle_subpage',"pegasus_toggle_plugin_settings_page" );


			add_menu_page("Carousel", "Carousel", "manage_options", "pegasus_carousel_plugin_options", "pegasus_carousel_plugin_settings_page", null, 99);
			add_submenu_page("pegasus_carousel_plugin_options", "Shortcode Usage", "Usage", "manage_options", "pegasus_carousel_plugin_shortcode_options", "pegasus_carousel_plugin_shortcode_settings_page" );
		}

		add_action( "admin_menu", "pegasus_toggle_submenu_item" );

	} else {
		function pegasus_toggle_menu_item() {
			add_menu_page( "Toggle", "Toggle", "manage_options", "pegasus_toggle_plugin_options", "pegasus_toggle_plugin_settings_page", null, 99 );
		}

		add_action( "admin_menu", "pegasus_toggle_menu_item" );
	}
	*/

	function pegasus_toggle_plugin_styles() {

		wp_register_style( 'toggle-plugin-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/toggle.css', array(), null, 'all' );
		//wp_enqueue_style( 'slippery-slider-css', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'css/slippery-slider.css', array(), null, 'all' );

	}
	add_action( 'wp_enqueue_scripts', 'pegasus_toggle_plugin_styles' );

	/*
	* Proper way to enqueue JS
	*/
	function pegasus_toggle_plugin_js() {

		//wp_enqueue_script( 'toggle-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/slippery.js', array( 'jquery' ), null, true );
		wp_register_script( 'pegasus-toggle-plugin-js', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/plugin.js', array( 'jquery' ), null, 'all' );

	} //end function
	add_action( 'wp_enqueue_scripts', 'pegasus_toggle_plugin_js' );


	/*~~~~~~~~~~~~~~~~~~~~
		TOGGLESLIDE
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
		if ( $title ) {
			$output .= '<a id="toggle-trigger-' . $pegasus_toggle_counter . '" class="pegasus-trigger" href="#toggle-content-' . $pegasus_toggle_counter . '" >' . $title . '</a>';
		} else {
			$output .= '<a id="toggle-trigger-' . $pegasus_toggle_counter . '" class="pegasus-trigger" href="#toggle-content-' . $pegasus_toggle_counter . '" >' . 'Toggle ' . $pegasus_toggle_counter . '</a>';
		}
		$output .= '<div id="toggle-content-' . $pegasus_toggle_counter . '" class="toggle-content">';
		$output .= $content;
		$output .= '</div>';
		$output .= '</div>';

		$pegasus_toggle_counter++;


		wp_enqueue_style( 'toggle-plugin-css' );
		wp_enqueue_script( 'pegasus-toggle-plugin-js' );

		return $output;
	}
	add_shortcode( 'toggle', 'pegasus_toggle_func' );

