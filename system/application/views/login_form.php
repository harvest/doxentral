<div id="login">
<h1>Login</h1>

<?php
	if( isset($err_msg) )
		echo '<h2>'.$err_msg.'</h2>';
		
	echo validation_errors();
?>

<?php echo form_open('auth/login'); ?>

<div class="inputRow">
<div class="secLabel">
<?php echo form_label('Username: ', 'user_name'); ?>
</div>
<div class="secField">
<?php echo form_input('user_name'); ?>
<?php echo $this->validation->user_name_error; ?>
</div>
</div>
<div class="clear"></div>

<div class="inputRow">
<div class="secLabel">
<?php echo form_label('Confirm Password: ', 'user_password'); ?>
</div>
<div class="secField">
<?php echo form_password('user_password'); ?>
<?php echo $this->validation->user_password_error; ?>
</div>
</div>
<div class="clear"></div>


<div class="secLabel">&nbsp;</div>
<div class="secField">
<?php echo form_submit('submit_login', 'Login', 'class="btn"'); ?>
</div>
<div class="clear"></div>

<?php echo form_close(); ?>
</div>