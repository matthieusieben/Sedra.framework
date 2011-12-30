<?php Load::helper('html'); ?>
</head>
<?php if (isset($body_class)): ?>
	<body class="<?php echo $body_class; ?>">
<?php else: ?>
	<body>
<?php endif ?>
	<div id="wrapper">
		<div id="header">
			<h1 id="name" class="main-box">
				<?php a('/', 'Sedra'); /* XXX : Site name */ ?>
			</h1>
			<ul id="menu" class="main-box">
				<li><a href="<?php echo Url::make('/') ?>"><?php p('Home'); ?></a></li>
				<li><a href="<?php echo Url::make('/') ?>"><?php p('Home'); ?></a></li>
				<li class="selected"><a href="<?php echo Url::make('/') ?>"><?php p('Home'); ?></a></li>
				<li><a href="<?php echo Url::make('/') ?>"><?php p('Home'); ?></a></li>
			</ul>
			<div class="clear"></div>
		</div>
		<div id="container" class="main-box">
			<?php if ($_messages = message()): ?>
				<ul id="messages">
					<?php foreach ($_messages as $_type => $_list): ?>
						<?php foreach ($_list as $_message): ?>
							<li class="<?php echo $_type ?>"><?php echo nl2br($_message); ?></li>
						<?php endforeach ?>
					<?php endforeach ?>
				</ul>
			<?php endif ?>
			<div id="content">