<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>
<?php require 'views/header.php' ?>

<?php if(isset($__data['view']) && $__data['view'] !== 'index'): ?>
	<?php echo theme($__data) ?>
<?php elseif(isset($__data['content'])): ?>
	<?php echo $__data['content'] ?>
<?php endif ?>

<?php require 'views/footer.php' ?>
<?php require 'views/foot.php' ?>