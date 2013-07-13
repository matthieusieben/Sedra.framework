<?php require 'head.php' ?>
<?php require 'body.php' ?>
<?php require 'header.php' ?>

<?php if(isset($__data['view'])): ?>
	<?php echo theme($__data) ?>
<?php elseif(isset($__data['content'])): ?>
	<?php echo $__data['content'] ?>
<?php endif ?>

<?php require 'footer.php' ?>
<?php require 'foot.php' ?>