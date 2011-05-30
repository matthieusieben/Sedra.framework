</head>
<?php if (isset($body_class)): ?>
	<body class="<?php echo $body_class; ?>">
<?php else: ?>
	<body>
<?php endif ?>
	<div id="wrapper">
		<div id="header">
			<ul id="menu">
				<li><a href="<?php echo Url::make('/') ?>"><?php p('Home'); ?></a></li>
			</ul>
			<?php if ($_messages = message()): ?>
				<ul id="messages">
					<?php foreach ($_messages as $_type => $_list): ?>
						<?php foreach ($_list as $_message): ?>
							<li class="<?php echo $_type ?>"><?php echo $_message ?></li>
						<?php endforeach ?>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
			<div class="clear"></div>
		</div>
		<div id="container">
			<div id="content">
