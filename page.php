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
require_once( 'vendor/autoload.php' );

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;

$log  = ( ! isset( $_GET['log'] ) ? 'apache-access' : $_GET['log'] );

$root_dir = dirname( __FILE__ );

// Remove the plugin directory.
$root_dir = explode( '/', $root_dir );
array_pop( $root_dir );
array_pop( $root_dir );
$root_dir = implode( '/', $root_dir );

$adapter = new Local( $root_dir, LOCK_EX, Local::SKIP_LINKS );
$filesystem = new Filesystem( $adapter );

// Windows is silly...
if( PHP_OS !== 'Darwin' ) {
	$logdir = '/apache/logs/';
	$sqldir = '/mysql/data/';
} else {
	$logdir = '/logs/';
	$sqldir = '/xamppfiles/var/mysql/';
}

switch( $log ) {
	case 'apache-access' :
		$errorlog = $logdir . 'access_log';
		break;
	case 'apache-error' :
		$errorlog = $logdir . 'error_log';
		break;
	case 'php-error' :
		$errorlog = $logdir . 'php_error_log';
		break;
	case 'ssl-request' :
		$errorlog = $logdir . 'ssl_request_log';
		break;
	case 'mysql-error' :
		if( PHP_OS !== 'Darwin' ) {
			$errorlog = $sqldir . 'mysql_error.log';
		} else {
			$dir_contents = $filesystem->listContents( $sqldir );
			$files = array();

			foreach( $dir_contents as $fileinfo ) {
				if( $fileinfo['extension'] == 'err' ) {
					$files[$fileinfo['timestamp']][] = $fileinfo['basename'];
				}
			}

			krsort( $files );

			$errorlog = $sqldir . array_shift( $files )[0];
		}
		break;
	default :
		$file .= 'access_log';
		break;
}

// Windows is silly...
if( PHP_OS !== 'Darwin' ) {
	$errorlog = str_replace( '_log', '.log', $file );
}

if( $filesystem->has( $errorlog ) ) {
	$data = $filesystem->read( $errorlog );
} else {
	$data = '';
}
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