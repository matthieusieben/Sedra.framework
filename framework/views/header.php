	<style>
	@media (min-width: 980px) {
		#page-wrap { padding-top: 66px; }
	}
	</style>
<?php require 'body.php' ?>

	<?php require 'components/navbar.php' ?>

	<div id="page-wrap">

		<div class="container">
			<div class="row" id="main">
				<div class="span12 content">
					<?php require 'components/message.php' ?>

					<?php if(isset($title)): ?>
						<h1><?php echo $title; ?></h1>
					<?php endif; ?>
