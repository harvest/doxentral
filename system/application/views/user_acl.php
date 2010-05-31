<?php
if( $error != '' )
	echo '<p>'.$error.'</p>';

if( count($file_list)>0 ):
	if( $data_format == 'csv' ):
		//CSV ACL
		$assigned_files = array();
		foreach( $file_list as $f ):
			$assigned_files[] = $f->file_title;
		endforeach;
		
		echo implode(', ', $assigned_files);
	else:
		echo '<hr />';
		echo '<div id="update_status"></div>';

		foreach( $file_list as $f ):
			$chk_data = array(
				'name' => 'file_id[]',
				'id' => 'file_id'.$f->file_id,
				'rel' => 'file',
				'value' => $f->file_id,
				'title' => $f->file_title,
				'checked' => TRUE
			);
	
			echo '<p>';
			echo form_checkbox($chk_data).'&nbsp;'.$f->file_title;
			echo '</p>';
		endforeach;
		
		$btn_data = array(
			'name' => 'btn_update',
			'id' => 'btn_update',
			'value' => 'Update',
			'type' => 'button',
			'class' => 'btn'
		);
		echo '<p>'.form_submit($btn_data).'</p>';
		
		echo '<input type="hidden" name="user_id" id="user_id" value="'.$user_id.'" />';
?>
<script type="text/javascript">
$(document).ready(function(){
	$('#btn_update').click(function(){

        var csvIDs = [];
        $("input[rel='file']:checked").each(function(){
        	csvIDs.push(this.getAttribute('value'));
        });

    	csvIDs.sort();

		$("#btn_update").attr('disabled', true);
		$("#btn_update").attr('value', 'Saving Changes...');

	    $.ajax({
	    	   type: "POST",
	    	   url: '<?= site_url("files/user_acl/$user_id/update") ?>',
	    	   data: "file_ids="+csvIDs.join(","),
	    	   dataType: "html",
	    	   error: function(){
	    		   $("#btn_update").attr('disabled', false);
	    		   $("#btn_update").attr('value', 'Update');
	    	   },
	    	   success: function(msg){
	    		   $("#btn_update").attr('disabled', false);
	    		   $("#btn_update").attr('value', 'Update');

	    		   $('#update_status').html(msg);
	    	   }
	    });//ajax
	});
});
</script>
<?php
	endif;
else:
	echo 'No files are assigned to this user yet.';
endif;
?>