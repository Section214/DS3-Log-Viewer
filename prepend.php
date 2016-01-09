<?php
/**
 * Create a menu item within our localhost tools pull down menu
 *
 * @package     DS3_Log_Viewer\Prepend
 * @since       1.0.0
 */


global $ds_runtime;

// Bail if not localhost
if( ! $ds_runtime->is_localhost ) {
	return;
}

// Bail if we're not interested in events
if( $ds_runtime->last_ui_event !== false ) {
	return;
}


/**
 * Add our menu to the localhost page
 *
 * @since       1.0.0
 * @return      void
 */
function log_viewer_add_menu_item() {
	echo '<li><a href="http://localhost/ds-plugins/log-viewer/page.php">Log Viewer</a></li>';
}
$ds_runtime->add_action( 'append_tools_menu', 'log_viewer_add_menu_item' );