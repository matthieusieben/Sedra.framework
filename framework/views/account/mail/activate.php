<?php global $request_server; ?>
<?php echo $account->name ?>,

A new account using this email has been created at <?php echo $request_server ?>.

You may now activate it by clicking on this link or copying and pasting it in your browser:

<?php echo $activate_url ?>

Attention: The username is case sensitive.