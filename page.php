<?php
/**
 * Display our log viewer page
 *
 * @package     DS3_Log_Viewer\Page
 * @since       1.0.0
 */


global $ds_runtime;


// Bail if not localhost
if( ! $ds_runtime->is_localhost ) {
	return;
}


// Require all the things
require_once( 'header.php' );

$log  = ( ! isset( $_GET['log'] ) ? 'apache-access' : $_GET['log'] );

// Windows is silly...
if( PHP_OS !== 'Darwin' ) {
	$file = '../../apache/logs/';
} else {
	$file = '../../logs/';
}

switch( $log ) {
	case 'apache-access' :
		$file .= 'access_log';
		break;
	case 'apache-error' :
		$file .= 'error_log';
		break;
	case 'php-error' :
		$file .= 'php_error_log';
		break;
	case 'ssl-request' :
		$file .= 'ssl_request_log';
		break;
	case 'mysql-error' :
		if( PHP_OS !== 'Darwin' ) {
			$file = '../../mysql/data/mysql_error.log';
		} else {
			// Get current directory
			$dir = dirname( __FILE__ );

			// Remove the plugin directory.
			$dir = explode( '/', $dir );
			array_pop( $dir );
			array_pop( $dir );

			// Build the MySQL path
			$dir = implode( '/', $dir ) . '/xamppfiles/var/mysql/';
			$files = array();

			$iterator = new DirectoryIterator( $dir );

			foreach( $iterator as $fileinfo ) {
				if( $fileinfo->getExtension() == 'err' ) {
					$files[$fileinfo->getMTime()][] = $fileinfo->getFilename();
				}
			}

			ksort( $files );

			$file = array_shift( $files );
			$file = $dir . $file[0];
		}
		break;
	default :
		$file .= 'access_log';
		break;
}

// Windows is silly...
if( PHP_OS !== 'Darwin' ) {
	$file = str_replace( '_log', '.log', $file );
}

$data = file_get_contents( $file );
?>
	<div class="container">
		<div class="row">
			<h1>Server Logs</h1>
			<div class="btn-group" role="group" aria-label="...">
				<a id="apache-access-log" class="btn btn-success" href="?log=apache-access">Apache Access Log</a>
				<a id="apache-error-log" class="btn btn-warning" href="?log=apache-error">Apache Error Log</a>
				<a id="php-error-log" class="btn btn-danger" href="?log=php-error">PHP Error Log</a>
				<a id="ssl-request-log" class="btn btn-info" href="?log=ssl-request">SSL Request Log</a>
				<a id="mysql-error-log" class="btn btn-primary" href="?log=mysql-error">MySQL Error Log</a>
			</div>

			<textarea class="spacer" id="log-viewer" readonly="readonly"><?php echo $data; ?></textarea>
		</div>
	</div>
<?php
// Require more things
require_once( '../../htdocs/footer.php' );