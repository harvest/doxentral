<?php
if( $error != '' )
	echo '<p>'.$error.'</p>';

if( $update_status != '' )
	echo '<p>'.$update_status.'</p>';

if( count($user_data)>0 ):
?>
<div id="updateStatus"></div>
<p class="tinyTag">Leave <strong>Current Password</strong> and <strong>New Password</strong> fields blank if you do not want to change your password.</p>
<form action="#" method="post" name="frmUserProfile" id="frmUserProfile">
  <p>
	Username:<br />
	<strong><?php echo $user_data['user_name']; ?></strong>
  </p>
  <p>
	Current Password:<br />
  <input type="password" name="user_password" class="input" size="40" maxlength="16" autocomplete="off" value="" />
  </p>
  <p>
	New Password:<br />
  <input type="password" name="user_password_new" class="input" size="40" maxlength="16" autocomplete="off" value="" />
  </p>
  <p>
	Firstname:<br />
	<input type="text" name="user_firstname" class="input" size="40" maxlength="32" value="<?php echo $user_data['user_firstname']; ?>" />
  </p>
  <p>
	Lastname:<br />
	<input type="text" name="user_lastname" class="input" size="40" maxlength="32" value="<?php echo $user_data['user_lastname']; ?>" />
  </p>
  <p>
	E-Mail Address:<br />
	<input type="text" name="user_email" class="input" size="40" maxlength="64" value="<?php echo $user_data['user_email']; ?>" />
  </p>
  <p>
	User Type:<br />
	<strong>
    <?php
    	echo $user_data['user_type']==1 ? 'Admin' : 'User';
    ?>
    </strong>
  </p>
  <p style="vertical-align:top;float:left;">
    <input type="button" name="update_user" id="update_user" class="btn" value="Apply Changes" />
  </p>
</form>

<script type="text/javascript">
$(document).ready(function(){
	$('#update_user').click(function(){
		$("#update_user").attr('disabled', true);
		$("#update_user").attr('value', 'Processing...');
		$('#updateStatus').html('');

	    $.ajax({
	    	   type: "POST",
	    	   url: '<?= site_url("profile/update") ?>',
	    	   data: $('#frmUserProfile').serialize(),
	    	   dataType: "html",
	    	   error: function(oHttp, txtStatus, err) {
	    		   $("#update_user").attr('disabled', false);
	    		   $("#update_user").attr('value', 'Apply Changes');

	    		   $('#updateStatus').html(txtStatus+': '+err.url);
	    	   },
	    	   success: function(msg){
	    		   $("#update_user").attr('disabled', false);
	    		   $("#update_user").attr('value', 'Apply Changes');

	    		   $('#updateStatus').html(msg);
	    	   }
	    });//ajax
	});
});
</script>
<?php
else:
	echo 'Sorry! Profile data not found. Please try again.';
endif;
?>