<?php
//---- Column inches settings (the boring stuff) ----//
define( 'SETTINGS_PAGE', 'column-inches' );

/**
 * Adds a separate options menu for column inches settings. This is only called if SETTINGS_PAGE is defined to be column-inches at the top of this file.
 */
function column_inches_menu() {
	add_options_page('Column Inches Options', 'Column Inches', 'manage_options', 'column-inches', 'column_inches_options');
}
if ( SETTINGS_PAGE == 'column-inches' )
	add_action('admin_menu', 'column_inches_menu');

/**
 * Prints the settings for a separate column-inches options page.
 */
function column_inches_options() {
?>
	<div class="wrap">
		<h2>Column Inches Options</h2>
		<form method="post" action="options.php">
<?php
	settings_fields('column-inches');
	do_settings_sections('column-inches');
?>
			<p><input type="submit" class="button-primary" value="<?php echo _('Save Changes'); ?>" /></p>
		</form>
	</div>
<?php
}

/**
 * Adds a settings section, field, and registers the settings for column_inches. These settings are placed on whatever page is defined as SETTINGS_PAGE at the top of this file.
 */
function column_inches_settings_init() {	
	add_settings_section(COLUMN_INCHES_OPTION, 'Column inches', 'column_inches_setting_section_callback', SETTINGS_PAGE);
 	add_settings_field('words_inch', 'Words per column inch', 'words_inch_callback', SETTINGS_PAGE, COLUMN_INCHES_OPTION);
 	add_settings_field('column_inches_post_page', 'Column inches on Post page', 'column_inches_post_page_callback', SETTINGS_PAGE, COLUMN_INCHES_OPTION);
	register_setting(SETTINGS_PAGE, COLUMN_INCHES_OPTION, 'sanitize_words_inch');
}
add_action('admin_init', 'column_inches_settings_init');

/**
 * Callback that prints explanatory text for the column_inches_setting_section.
 */
function column_inches_setting_section_callback() {
	echo '<p>According to Wikipedia, a <a href="http://en.wikipedia.org/wiki/Column_inch">column inch</a> is a measurement  of the amount of content in published works that use multiple columns per page. A column inch is a unit of space one column wide by one inch high.</p>';
	echo '<p>Use this page to set the options for this plugin according to your publication&rsquo;s column inch standards.</p>';
}

/**
 * Prints the settings input field for the number of words per inch there are.
 */
function words_inch_callback() {
	$options = get_option( COLUMN_INCHES_OPTION );
	$words_inch = $options['words_inch'];
	echo "Name: <input name='" . COLUMN_INCHES_OPTION . "[words_inch][1][name]' type='text' value='" . $words_inch[0]['name'] . "' autocomplete='off' />";
	echo " Words per column inch: <input name='" . COLUMN_INCHES_OPTION . "[words_inch][1][count]' type='text' value='" . $words_inch[0]['count'] . "' autocomplete='off' class='small-text' />";
	$i = 2;
	for ( ; $i <= count( $words_inch ); $i++) {
		$words_inch_single = $words_inch[$i-1];
		echo "
		<tr valign='top'>
			<th scope='row'>
				<td>
					Name: <input name='" . COLUMN_INCHES_OPTION . "[words_inch][$i][name]' type='text' value='" . $words_inch_single['name'] . "' autocomplete='off' />
					Words per column inch: <input name='" . COLUMN_INCHES_OPTION . "[words_inch][$i][count]' type='text' value='" . $words_inch_single['count'] . "' autocomplete='off' class='small-text' />
					<a href='#' class='remove_row'>-</a>
				</td>
			</th>
		</tr>";
	}
}

/**
 * Prints the settings checkbox field for whether or not to show the column inches on the Post edit page.
 */
function column_inches_post_page_callback() {
	$checked = "";
	
	// Mark our checkbox as checked if the setting is already true
	$options = get_option( COLUMN_INCHES_OPTION );
	if ( array_key_exists('column_inches_post_page', $options) && $options['column_inches_post_page'] )
		$checked = "checked='checked'";

	echo "<input {$checked} name='" . COLUMN_INCHES_OPTION . "[column_inches_post_page]' type='checkbox' value='1' />
	<span class='description'>Check if you want to view column inch counts next to word counts on the Post edit page</span>";
}

/**
 * Sanitizes the options for this plugin.
 * @param array $options the options for this plugin
 * @return sanitized $options param.
 */
function sanitize_words_inch($options) {
	$options_new = array(); $words_inch = array();
	foreach ($options['words_inch'] as $words_inch_single) {
		if ( !empty( $words_inch_single['name'] ) ) {
			$words_inch_single['name'] = wp_filter_nohtml_kses( $words_inch_single['name'] );
			$words_inch_single['count'] = floatval( $words_inch_single['count'] );
			if ( $words_inch_single['count'] == 0 )
				$words_inch_single['count'] = COLUMN_INCHES_DEFAULT_COLUMN_INCHES;
			$words_inch[] = $words_inch_single;
		}
	}
	$options_new['words_inch'] = $words_inch;
	if ( array_key_exists( 'column_inches_post_page', $options ) )
		$options_new['column_inches_post_page'] = $options['column_inches_post_page'];
	
	return $options_new;
}

/**
 * Prints column inch settings script on column inch settings page to facilitate adding/removing column inch types.
 */
function print_column_inches_settings_scripts() {
	$options = get_option( COLUMN_INCHES_OPTION );
	$count = count( $options['words_inch'] ) + 1;
	wp_enqueue_script( 'column-inches-settings', COLUMN_INCHES_URL . '/js/column-inches-settings.js.php?option=' . COLUMN_INCHES_OPTION . '&count=' . $count, array('jquery'), '1.0', true );
}
add_action('admin_print_scripts-settings_page_column-inches', 'print_column_inches_settings_scripts');
?>
