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


/**
 * Inject our custom scripts
 *
 * @since       1.1.3
 * @return      void
 */
function log_viewer_add_scripts() {
	?>
	<!-- jQuery -->
	<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>

	<!-- Log Viewer -->
	<link href="http://localhost/ds-plugins/log-viewer/assets/css/log-viewer.css" rel="stylesheet"/>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/log-viewer.js"></script>

	<!-- CodeMirror -->
	<link href="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/lib/codemirror.css" rel="stylesheet"/>
	<link href="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/theme/eclipse.css" rel="stylesheet"/>
	<link href="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/dialog/dialog.css" rel="stylesheet"/>
	<link href="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/scroll/simplescrollbars.css" rel="stylesheet"/>
	<link href="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/search/matchesonscrollbar.css" rel="stylesheet"/>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/lib/codemirror.js"></script>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/search/search.js"></script>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/search/searchcursor.js"></script>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/search/jump-to-line.js"></script>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/search/matchesonscrollbar.js"></script>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/dialog/dialog.js"></script>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/scroll/simplescrollbars.js"></script>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/codemirror/addon/scroll/annotatescrollbar.js"></script>

	<!-- jQuery Confirm -->
	<link href="http://localhost/ds-plugins/log-viewer/assets/js/craftpip/dist/jquery-confirm.min.css" rel="stylesheet"/>
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/craftpip/dist/jquery-confirm.min.js"></script>

	<!-- QueryString -->
	<script src="http://localhost/ds-plugins/log-viewer/assets/js/querystring-0.0.1a.js"></script>
	<?php
}
$ds_runtime->add_action( 'ds_head', 'log_viewer_add_scripts' );