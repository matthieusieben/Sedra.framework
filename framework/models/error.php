<?php

require_once 'log.php';

set_error_handler('__error_handler');
set_exception_handler('__exception_handler');

class FrameworkException extends Exception {
	public function __construct($message, $code = 500) {

		if($message instanceof Exception)
			$message = $message->message;

		parent::__construct($message, $code);
	}
}

function __error_handler($errno, $errstr, $errfile, $errline) {

	load_model('log');
	log_phperror($errno, $errstr, $errfile, $errline);

	switch ($errno) {
	case E_ERROR:
	case E_PARSE:
	case E_USER_ERROR:
	case E_CORE_ERROR:
	case E_COMPILE_ERROR:
		throw new ErrorException($errstr, 500, $errno, $errfile, $errline);
		break;
	case E_STRICT:
	case E_DEPRECATED:
	case E_USER_DEPRECATED:
	case E_WARNING:
	case E_USER_WARNING:
	case E_RECOVERABLE_ERROR:
		if(config('devel')) {
			$error = array(
				'errno' => $errno,
				'errstr' => $errstr,
				'errfile' => $errfile,
				'errline' => $errline
			);
			if(function_exists('dvm')) {
				dvm($error);
			} else if(function_exists('kprintr')) {
				kprintr($error);
			} else {
				var_dump($error);
			}
		}
		break;
	case E_NOTICE:
	default:
		break;
	}
}

function __exception_handler($e) {

	load_model('log');
	log_exception($e);

	# Clear output buffers
	$output_buffer = ob_get_clean_all();

	# Set error status header
	if(!headers_sent()) {
		set_status_header($e instanceof FrameworkException ? $e->getCode() : 500);
		header('Content-Type: text/html; charset=utf-8');
	}

	try {
		if(function_exists('theme'))
			exit(theme('error/exception', array('exception' => $e, 'output_buffer' => $output_buffer)));
		else
			throw $e;
	} catch(FrameworkException $e) {
		fatal($e->getMessage(), NULL, $e->getCode(), $e->getFile(), $e->getLine(), $e->getTrace());
	} catch(Exception $e) {
		fatal($e->getMessage(), NULL, 500, $e->getFile(), $e->getLine(), $e->getTrace());
	}
}

function fatal( $message, $heading = NULL, $status_code = 500, $file = NULL, $line = NULL, $trace = NULL) {
	global $request_folder;

	if (empty($heading)) {
		$heading = t('Error @code', array('@code' => $status_code));
	}

	if (is_numeric($status_code) && !headers_sent()) {
		set_status_header($status_code);
	}

	if (empty($trace)) {
		$trace = debug_backtrace();
	}

	# Clear output buffers
	$output_buffer = ob_get_clean_all();

	# Error message
?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Error <?php echo $status_code; ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<?php if(function_exists('theme_css')): ?>
			<?php echo theme_css('libraries/bootstrap/css/bootstrap.min.css'); ?>
			<?php echo theme_css('libraries/bootstrap/css/bootstrap-responsive.min.css'); ?>
		<?php endif; ?>

		<style>
			body {
				padding-top: 60px;
			}
		</style>
	</head>
	<body class="controller-error path-error-<?php echo $status_code; ?>">
		<div class="container">
			<div class="hero-unit">

				<h1><?php echo $heading; ?></h1>
				<p><?php echo $message; ?></p>

				<?php if (config('devel')): ?>

					<?php if($file || $line): ?>
						<dl>
							<dt><?php echo t('File') ?></dt>
							<dd><code><?php echo $file; ?></code></dd>
							<dt><?php echo t('Line') ?></dt>
							<dd><code><?php echo $line; ?></code></dd>
						</dl>
					<?php endif; ?>

					<h2><?php echo t('Backtrace'); ?></h2>
					<?php if(function_exists('kprintr')) kprintr($trace); else var_dump($trace); ?>

					<h2><?php echo t('Output buffer content'); ?></h2>
					<?php if($output_buffer): ?>
						<pre><?php if(function_exists('kprintr')) kprintr($output_buffer); else var_dump($output_buffer); ?></pre>
					<?php else: ?>
						<p><em><?php echo t('Empty'); ?></em></p>
					<?php endif; ?>

				<?php endif; ?>

			</div>
		</div>
	</body>
</html><?php

	# Stop script execution
	exit();
}
