<script type="text/javascript">
$(document).ready(function(){
	$("a[rel='user_acl']").each(function(){
		$(this).click(function() {
			$.fn.colorbox({
				href: <?php echo '"'.site_url().'/files/user_acl/"+this.title+"/view/"'; ?>, 
				slideshow: false,
				title: 'Aceess Control List', 
				open: true,
				width: '400px',
				height: '300px'
			});
			return false;
		});
	});

	$("a[rel='user_edit']").each(function(){
		$(this).click(function() {
			$.fn.colorbox({
				href: <?php echo '"'.site_url().'/users/edit/"+this.title'; ?>, 
				slideshow: false,
				title: 'Edit User', 
				open: true
			});
			return false;
		});
	});
});

function deleteUser(_user_id, _user_name)
{
	if( confirm('Are you sure to delete user: '+_user_name+' and his/her files?') ) {
		//alert( "<?php echo site_url().'/users/delete/' ?>"+_user_id );
		document.location.href = "<?php echo site_url().'/users/delete/' ?>"+_user_id;
	}

	return false;
}
</script>
<?php
if( is_array($del_status) && count($del_status)>0 ){
	echo '<ul>';
	echo '<li>Files deleted on disk: '.$del_status['file_count'].'</li>';
	echo '<li>File associations deleted: '.$del_status['acl_count'].'</li>';
	echo '<li>Users deleted: '.$del_status['user_count'].'</li>';
	echo '</ul>';
} elseif( trim($del_status) != '' ) {
	echo '<ul>';
	echo '<li>'.$del_status.'</li>';
	echo '</ul>';
}
?>
<div id="twoColBlock">
	<div id="files">
	<h2>Existing Users</h2>
	<?php if( count($users)>0 ): ?>
		<table id="table">
		<tr>
		  <th>Name</th>
		  <th>Username</th>
		  <th>Type</th>
		  <th>Created</th>
		  <th>Status</th>
		  <th>Action</th>
		</tr>
		<tr>
	<?php
		$r = 0;
		foreach($users as $u):
			$r++;
			$user_fullname = $u['user_firstname'].' '.$u['user_lastname'];
			$del_link = array(
							'onclick' => "deleteUser('$u[user_id]', '$user_fullname');return false;" 
						);
			$user_fullname .= '<br /><span class="tinyTag">'.$u['user_email'].'</span>';
			$user_type = $u['user_type']==1 ? 'Admin' : 'User';
			$user_created = unix_to_human(mysql_to_unix($u['user_created']), FALSE, 'us');

			if( $r%2 == 0 ) {
				echo '<tr class="alt">';
				echo '<td>'.$user_fullname.'</td>';
				echo '<td>'.$u['user_name'].'</td>';
				echo '<td>'.$user_type.'</td>';
				echo '<td>'.$user_created.'</td>';
				echo '<td>'.$u['user_status'].'</td>';
				echo '<td>';
				echo anchor('javascript://', 'Edit User', array('rel' => 'user_edit', 'id' => 'cbx'.$u['user_id'], 'title' => $u['user_id']));
				echo '<br/>';
				echo anchor('javascript://', 'View Files', array('rel' => 'user_acl', 'id' => 'cbx'.$u['user_id'], 'title' => $u['user_id']));
				echo '<br/>';
				echo anchor('javascript://', 'Delete', $del_link);
				echo '</td>';
				echo '</tr>';
			} else {
				echo '<tr>';
				echo '<td>'.$user_fullname.'</td>';
				echo '<td>'.$u['user_name'].'</td>';
				echo '<td>'.$user_type.'</td>';
				echo '<td>'.$user_created.'</td>';
				echo '<td>'.$u['user_status'].'</td>';
				echo '<td>';
				echo anchor('javascript://', 'Edit User', array('rel' => 'user_edit', 'id' => 'cbx'.$u['user_id'], 'title' => $u['user_id']));
				echo '<br/>';
				echo anchor('javascript://', 'View Files', array('rel' => 'user_acl', 'id' => 'cbx'.$u['user_id'], 'title' => $u['user_id']));
				echo '<br/>';
				echo anchor('javascript://', 'Delete', $del_link);
				echo '</td>';
				echo '</tr>';
			}
		endforeach;
	?>
	</table>
	<?php endif;?>
	</div>
	
	<?php if($user_data['user_type'] == 1): ?>
	<div id="quickLinks">
	<h4>Add New User</h4>
	<div id="createUserStatus"></div>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#create_user').click(function(){
			$("#create_user").attr('disabled', true);
			$("#create_user").attr('value', 'Creating User...');
			$('#createUserStatus').html('');

		    $.ajax({
		    	   type: "POST",
		    	   url: '<?= site_url("users/create") ?>',
		    	   data: $('#frmCreateUser').serialize(),
		    	   dataType: "html",
		    	   error: function(oHttp, txtStatus, err) {
		    		   $("#create_user").attr('disabled', false);
		    		   $("#create_user").attr('value', 'Create User');

		    		   $('#createUserStatus').html(txtStatus+': '+err.url);
		    	   },
		    	   success: function(msg){
		    		   $("#create_user").attr('disabled', false);
		    		   $("#create_user").attr('value', 'Create User');

		    		   $('#createUserStatus').html(msg);
		    	   }
		    });//ajax
		});
	});
	</script>

	<form action="#" method="post" name="frmCreateUser" id="frmCreateUser">
	  <p>
		Username:<br />
		<input type="text" name="user_name" id="user_name" class="input" size="40" maxlength="64" />
	  </p>
	  <p>
		Password:<br />
	  <input type="password" name="user_password" id="user_password" class="input" size="40" maxlength="16" />
	  </p>
	  <p>
		Firstname:<br />
		<input type="text" name="user_firstname" id="user_firstname" class="input" size="40" maxlength="32" />
	  </p>
	  <p>
		Lastname:<br />
		<input type="text" name="user_lastname" id="user_lastname" class="input" size="40" maxlength="32" />
	  </p>
	  <p>
		E-Mail Address:<br />
		<input type="text" name="user_email" id="user_email" class="input" size="40" maxlength="64" />
	  </p>
	  <p>
		User Type:<br />
	        <select name="user_type" id="user_type">
	          <option value="1">Administrator</option>
	          <option value="2" selected="selected">User</option>
	        </select>
	  </p>
	  <p>
		Check to notify this user by email:<br />
		<input type="checkbox" name="notify_user" id="notify_user" class="input" value="1" />
	  </p>
	  <p style="vertical-align:top;">
	    <input type="button" name="create_user" id="create_user" class="btn" value="Create User" />
	  </p>
	</form>

	</div>
	<?php endif; ?>
</div>

<div class="clear">&nbsp;</div>