<?php
/*
Plugin Name: Column Inches
Plugin URI: http://www.scottbressler.com/wp/
Description: Adds a column to the Edit Posts page and on the Post edit page with the number of column inches that the content in the post takes up in a print edition.
Author: Scott Bressler
Version: 1.0
Author URI: http://www.scottbressler.com/wp/
License: GPL2
*/

define( 'COLUMN_INCHES_VERSION', '1.0' );
define( 'COLUMN_INCHES_URL', plugins_url(plugin_basename(dirname(__FILE__)).'/') );
define( 'COLUMN_INCHES_DEFAULT_COLUMN_INCHES', 20.0 );
define( 'COLUMN_INCHES_OPTION', 'column_inches' );

require_once( 'settings.php' );

/**
 * Add default options to database
 */
function set_default_column_inches_options() {
	$options = array();
	$options['words_inch'] = array( array('name' => 'Broadsheet', 'count' => COLUMN_INCHES_DEFAULT_COLUMN_INCHES ) );
	$options['column_inches_post_page'] = true;
	add_option( COLUMN_INCHES_OPTION, $options );
}
register_activation_hook(__FILE__, 'set_default_column_inches_options' );

/**
 * Delete options in database
 */
function column_inches_uninstall() {
	delete_option( COLUMN_INCHES_OPTION );
}
if ( function_exists('register_uninstall_hook') )
	register_uninstall_hook(__FILE__, 'column_inches_uninstall');

/**
 * Adds a column to the Edit Posts page for column inches.
 * @param array $edit_columns Array of currently defined columns for the Edit Posts page.
 * @return array Modified array of columns for the Edit Posts page which includes column inches.
 */
function column_inches_column_header($edit_columns) {
	$edit_columns['inches'] = __( 'Inches' );
	return $edit_columns;
}
add_filter( 'manage_posts_columns', 'column_inches_column_header' );

/**
 * Prints the number of columns for a given post to the Edit Posts page.
 */
function column_inches_row($column_name, $post_id) {
	if ($column_name === 'inches') {
		global $post;
		
		// Remove HTML tags
		$post_plaintext = strip_tags( $post->post_content );
		
		// Get column inches from options table
		$options = get_option( COLUMN_INCHES_OPTION );
		$column_inches = $options['words_inch'];
		$words = str_word_count( $post_plaintext );
		$num_counts = count($column_inches);
		
		// Display column inches
		for ($i = 0; $i < $num_counts; $i++) {
			$column_inch = $column_inches[$i];
			$name = $column_inch['name'];
			$inches = ceil( $words / $column_inch['count'] );
			echo "<span title='$name: $inches column inch" . ($inches != 1 ? "es" : "") . "' style='border-bottom: 1px dotted #666; cursor: help;'>$inches</span>";
			if ($num_counts  > 1 && $i < $num_counts - 1)
				echo ' / ';
		}		
	}
}
add_action('manage_posts_custom_column', 'column_inches_row', 10, 2);

/**
 * CSS for Edit Posts page, so that the column with column inches is nice and compact.
 */
function column_inches_css() {
	global $pagenow;
	
	if ($pagenow == 'edit.php') {
		echo "
		<style type='text/css'>
		.column-inches {
			width: 8em;
		}
		</style>
		";
	}
}
add_action('admin_head', 'column_inches_css');

/**
 * Prints column inch calculation script on Post edit page.
 */
function print_column_inches_scripts() {
	$options = get_option( COLUMN_INCHES_OPTION );
	if ( array_key_exists('column_inches_post_page', $options) && $options['column_inches_post_page'] ) {
		echo '<script type="text/javascript">var columnInchSettings = ' . json_encode($options['words_inch']) . '</script>';
		wp_enqueue_script( 'column-inches-post', COLUMN_INCHES_URL . 'js/column-inches-post.js', array('jquery'), '1.0', true );
	}
}
add_action('admin_print_scripts-post.php', 'print_column_inches_scripts');

?>
