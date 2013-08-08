<?php global $request_base; ?>
<!DOCTYPE html>
<html lang="<?php echo $lang ?>">
<head>
	<meta charset="utf-8">
	<title><?php echo $title ?></title>

	<style type="text/css">
		body {
			background: #f1f1f1;
			font-size: 13px;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			color: #333;
			color: rgba(0, 0, 0, 0.5);
			padding: 0;
			margin: 0;
		}
		a {
			color: #428bca;
			text-decoration: none;
		}
		a:hover {
			color: #326a9c;
			text-decoration: underline;
		}
		header,
		#content {
			font-size: 16px;
			text-align: center;
			text-shadow: 1px 1px white;
		}
		h1 {
			font-family: Georgia, serif;
			font-style: italic;
			font-size: 3em;
			margin: 2em 0 0.5em;
			color: #333;
			color: rgba(0, 0, 0, 0.75);
		}
		p {
			margin: 1em 0;
		}
		footer {
			color: #333;
		}
		h2 {
			border-top: 1px solid #ccc;
			border-bottom: 1px solid #ccc;
			padding: 0.5em;
			background: white;
			margin: 5em 0 0.5em;
			text-align: center;
		}
		#debug-info {
			margin: 0 auto;
			padding: 0 5px;
			max-width: 950px;
		}
		dt {
			font-weight: bold;
			display: block;
		}
		dt, dd {
			line-height: 1.5em;
		}
		dd {
			margin-left: 1em;
		}
		pre {
			display: block;
			padding: 1em;
			margin: 0 0 1em;
			line-height: 1.5em;
			word-break: break-all;
			word-wrap: break-word;
			white-space: pre;
			white-space: pre-wrap;
			color: #333;
		}
		code {
			padding: 0.2em;
			color: #c00;
			white-space: nowrap;
		}
		code, pre {
			font-family: Monaco,Menlo,Consolas,"Courier New",monospace;
			background-color: #f5f5f5;
			border: 1px solid #ccc;
			border: 1px solid rgba(0, 0, 0, 0.15);
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
		}
	</style>
</head>
<body>

	<header>
		<h1><?php echo t('Oops...') ?></h1>
	</header>

	<div id="content">
		<p><?php echo $message ?></p>
		<p><?php echo t('Go back to !link.', array('!link' => '<a href="'.$request_base.'">'.config('site.name', t('the home page')).'</a>')) ?></p>
	</div>

	<?php if (config('devel')): ?>
		<footer>

			<h2><?php echo t('Debug info') ?></h2>

			<div id="debug-info">

				<dl>
					<dt><?php echo t('Error') ?></dt>
					<dd><code><?php echo @$error ?></code></dd>
					<dt><?php echo t('File') ?></dt>
					<dd><code><?php echo @$file ?></code></dd>
					<dt><?php echo t('Line') ?></dt>
					<dd><code><?php echo @$line ?></code></dd>
				</dl>

				<h3><?php echo t('Output buffer content') ?></h3>
				<?php if($output_buffer): ?>
					<pre><?php echo check_plain($output_buffer) ?></pre>
				<?php else: ?>
					<p><em><?php echo t('Empty') ?></em></p>
				<?php endif ?>

				<h3><?php echo t('Backtrace') ?></h3>
				<?php foreach ($trace as $num => $call): ?>
					<?php var_dump($call) ?>
				<?php endforeach ?>

			</div>

		</footer>
	<?php endif ?>

</body>
</html>