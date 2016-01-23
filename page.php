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
	$ctrlkey = 'Ctrl';
} else {
	$logdir = '/logs/';
	$sqldir = '/xamppfiles/var/mysql/';
	$ctrlkey = 'Cmd';
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

if( isset( $_GET['action'] ) && $_GET['action'] == 'delete' ) {
	if( $filesystem->has( $errorlog ) ) {
		$filesystem->update( $errorlog, '' );
	}
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

			<div class="col-md-3">
				<div class="log-controls">
					<form>
						<strong>Select Log File</strong>
						<div class="radio">
							<label><input type="radio" name="log" value="apache-access" <?php echo ( $_GET['log'] == 'apache-access' || ! isset( $_GET['log'] ) ? 'checked' : '' ); ?>>Apache Access Log</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="log" value="apache-error" <?php echo ( $_GET['log'] == 'apache-error' ? 'checked' : '' ); ?>>Apache Error Log</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="log" value="php-error" <?php echo ( $_GET['log'] == 'php-error' ? 'checked' : '' ); ?>>PHP Error Log</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="log" value="mysql-error" <?php echo ( $_GET['log'] == 'mysql-error' ? 'checked' : '' ); ?>>MySQL Error Log</label>
						</div>
						<div class="radio">
							<label><input type="radio" name="log" value="ssl-request" <?php echo ( $_GET['log'] == 'ssl-request' ? 'checked' : '' ); ?>>SSL Request Log</label>
						</div>

						<hr />

						<strong>Keybindings</strong>

						<p class="clearfix">
							<div class="pull-left"><kbd><?php echo $ctrlkey; ?>+F</kbd></div>
							<div class="pull-right">Search log</div>
						</p>
						<p class="clearfix">
							<div class="pull-left"><kbd><?php echo $ctrlkey; ?>+G</kbd></div>
							<div class="pull-right">Find next</div>
						</p>
						<p class="clearfix">
							<div class="pull-left"><kbd>Shift+<?php echo $ctrlkey; ?>+G</kbd></div>
							<div class="pull-right">Find previous</div>
						</p>
						<p class="clearfix">
							<div class="pull-left"><kbd>Alt+F</kbd></div>
							<div class="pull-right">Persistent Search</div>
						</p>
						<p class="clearfix">
							<div class="pull-left"><kbd>Alt+G</kbd></div>
							<div class="pull-right">Go To Line</div>
						</p>

						<div class="clearfix"></div>

						<hr />

						<a name="clear" class="btn btn-danger clear-log">Clear Log File</a>
					</form>
				</div>
			</div>

			<div class="col-md-9">
				<textarea id="log-viewer" readonly="readonly"><?php echo $data; ?></textarea>
			</div>
		</div>
	</div>
<?php
// Require more things
require_once( '../../htdocs/footer.php' );