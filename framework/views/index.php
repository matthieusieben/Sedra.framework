<?php require 'views/head.php' ?>
<?php require 'views/body.php' ?>
<?php require 'views/header.php' ?>

<?php if(@$__data['view'] === 'index') unset($__data['view']); ?>
<?php echo theme('html', $__data) ?>

<?php require 'views/footer.php' ?>
<?php require 'views/foot.php' ?>