<?php
if( $error != '' )
	echo '<p>'.$error.'</p>';

if( count($user_list)>0 ):
	if( $data_format == 'csv' ):
		//CSV ACL
		$file_id = $file_data[0]['file_id'];
		$assigned_users = array();
		foreach( $user_list as $u ):
			$user_name = $u['user_firstname'].' '.$u['user_lastname'];

			foreach( $acl_data as $a ):
				if( $a['user_id'] == $u['user_id'] )
					$assigned_users[] = $user_name;
			endforeach;
		endforeach;
		
		echo implode(', ', $assigned_users);
	else:
		echo '<hr />';
		echo '<div id="update_status"></div>';

		$file_id = $file_data[0]['file_id'];
		foreach( $user_list as $u ):
			$user_name = $u['user_firstname'].' '.$u['user_lastname'];
	
			$chk_data = array(
				'name' => 'user_id[]',
				'id' => 'user_id'.$u['user_id'],
				'rel' => 'user',
				'value' => $u['user_id'],
				'title' => $user_name,
				'checked' => FALSE
			);
	
			foreach( $acl_data as $a ):
				if( $a['user_id'] == $u['user_id'] ):
					$chk_data['checked'] = TRUE;
				endif;
			endforeach;
			
			echo '<p>';
			echo form_checkbox($chk_data).'&nbsp;'.$user_name;
			echo '</p>';
		endforeach;
		
		$btn_data = array(
			'name' => 'btn_update',
			'id' => 'btn_update',
			'value' => 'Assign',
			'type' => 'button',
			'class' => 'btn'
		);
		echo '<p>'.form_submit($btn_data).'</p>';
		
		echo '<input type="hidden" name="file_id" id="file_id" value="'.$file_id.'" />';
?>
<script type="text/javascript">
$(document).ready(function(){
	$('#btn_update').click(function(){

        var csvIDs = [];
        $("input[rel='user']:checked").each(function(){
        	csvIDs.push(this.getAttribute('value'));
        });

    	csvIDs.sort();

		$("#btn_update").attr('disabled', true);
		$("#btn_update").attr('value', 'Updating...');

	    $.ajax({
	    	   type: "POST",
	    	   url: '<?= site_url("files/acl/$file_id/update") ?>',
	    	   data: "user_ids="+csvIDs.join(","),
	    	   dataType: "html",
	    	   error: function(){
	    		   $("#btn_update").attr('disabled', false);
	    		   $("#btn_update").attr('value', 'Assign');
	    	   },
	    	   success: function(msg){
	    		   $("#btn_update").attr('disabled', false);
	    		   $("#btn_update").attr('value', 'Assign');

	    		   $('#update_status').html(msg);
	    	   }
	    });//ajax
	});
});
</script>
<?php
	endif;
else:
	echo 'Sorry! No users in the database.';
endif;
?>