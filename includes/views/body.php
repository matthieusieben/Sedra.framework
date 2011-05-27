</head>
<?php if (isset($body_class)): ?>
	<body class="<?php echo $body_class; ?>">
<?php else: ?>
	<body>
<?php endif ?>
	<div id="wrapper">
		<div id="header">
			<ul id="menu">
				<li><a href="<?php echo Url::make('/') ?>">Home</a></li>
				<li><a href="<?php echo Url::make('configurator') ?>">Configurator</a></li>
				<li><a href="<?php echo Url::make('contact') ?>">Contact</a></li>
			</ul>
			<div class="clear"></div>
		</div>
		<div id="container">
			<div id="content">
