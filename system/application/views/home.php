<?php
if( $_type == '' ):
?>
<script type="text/javascript">
function deleteFile(_file_id, _file_name)
{
	if( confirm('Are you sure to delete file: '+_file_name+' ?') ) {
		$("#file_"+_file_id).fadeTo(400, 0.75);

		//Ajax and pull the ACL
	    $.ajax({
	    	   type: "POST",
	    	   url: "<?php echo site_url().'/files/delete/' ?>"+_file_id,
	    	   dataType: 'json',
	    	   error: function(){
	    		   alert('Error: '+'File deletion failed!');
	    		   $("#file_"+_file_id).fadeTo(500, 0);
	    	   },
	    	   success: function(oJ){
		    	   if( oJ.head ) {
			    	   if( oJ.head == 1 ) {
			    		   $("#file_"+_file_id).fadeOut(500, function(){
			    			   $("#file_"+_file_id).remove();
				    		});
			    	   } else {
			    		   alert(oJ.msg);
			    		   $("#file_"+_file_id).fadeTo(500, 0);
			    	   }
		    	   } else {
			    	   alert(oJ);
			    	   $("#file_"+_file_id).fadeTo(500, 0);
	    		   }
	    	   }
	    });//ajax
	}

	return false;
}
$(document).ready(function(){
	//ColorBox Assignment
	$("a[rel='acl']").each(function(){
		$(this).click(function() {
			$.fn.colorbox({
				href: <?php echo '"'.site_url().'/files/acl/"+this.title+"/view/"'; ?>, 
				slideshow: false,
				title: 'Aceess Control List', 
				open: true,
				width: '400px',
				height: '300px'
			});
			return false;
		});
	});
});

<?php
	$sess_cookie_data = str_replace('"', '\"', $this->session->get_cookie_data());
?>
//swfupload
var swfu;

		window.onload = function () {
			swfu = new SWFUpload({
				// Backend settings
				upload_url: "<?=base_url()?>index.php/files/upload",

				post_params: {"<?php echo $this->config->item('sess_cookie_name'); ?>" :"<?php echo $sess_cookie_data; ?>"},
				file_post_name: "user_file",

				// Flash file settings
				file_size_limit : 0,
				file_types : "*.*",			// or you could use something like: "*.doc;*.wpd;*.pdf",
				file_types_description : "All Files",
				file_upload_limit : 0,
				file_queue_limit : 1,

				// Event handler settings
				swfupload_loaded_handler : swfUploadLoaded,
				
				file_dialog_start_handler: fileDialogStart,
				file_queued_handler : fileQueued,
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				
				//upload_start_handler : uploadStart,	// I could do some client/JavaScript validation here, but I don't need to.
				swfupload_preload_handler : preLoad,
				swfupload_load_failed_handler : loadFailed,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Button Settings
				button_image_url : "<?=base_url()?>images/XPButtonUploadText_61x22.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 61,
				button_height: 22,
				
				// Flash Settings
				flash_url : "<?=base_url()?>js/swfupload/swfupload.swf",
				flash9_url : "<?=base_url()?>js/swfupload/swfupload_fp9.swf",

				custom_settings : {
					progress_target : "fsUploadProgress",
					upload_successful : false
				},
				
				// Debug settings
				debug: false
			});
		};
</script>

<div id="twoColBlock">
	<div id="files">
<?php 
endif; 
	
	if( $_type == '' || $_type == 'files' ):
		//Owned files for Admins
		if( $user_data['user_type'] == 1 ):
			echo '<h2>Your files</h2>';
			foreach($own_files as $f):
				echo '<div id="file_'.$f->file_id.'">';

				$f_status = '<span class="%s">%s</span>';
				if( $f->file_status == -1 ):
					$f_status = sprintf($f_status, 'red_bg', 'Invalid');
				elseif( $f->file_status == 1 ):
					$f_status = sprintf($f_status, 'green_bg', 'Active');
				elseif( $f->file_status == 2 ):
					$f_status = sprintf($f_status, 'red_bg', 'Deleted');
				else:
					$f_status = sprintf($f_status, 'gold_bg', 'New');
				endif;

				$d_link = site_url().'/files/download/'.$f->file_id;
				$d_anchor = anchor($d_link, 'Download');
				$del_link = array(
					'onclick' => "deleteFile('$f->file_id', '$f->file_title');return false;" 
					);

				echo '<p class="caption">#'.$f->file_id.'&nbsp;<em>'.$f->file_title.'</em>&nbsp;'.$f_status.'&nbsp;<br />'.$d_anchor;
				echo '&nbsp;|&nbsp'.anchor('javascript://', 'Assign', array('rel' => 'acl', 'id' => 'cbx'.$f->file_id, 'title' => $f->file_id));
				echo '&nbsp;|&nbsp;'.anchor('javascript://', 'Delete', $del_link);
				echo '<br /><span class="desc">'.$f->file_desc.'</span>';
				echo '</p>';
				/*
				if( $f->file_status == 1 ):
					echo anchor('#', '>>', array('rel' => 'assign_info', 'title' => $f->file_id.'|'.$f->file_title));
					echo '<div id="assignInfo'.$f->file_id.'" class="greyBox">Loading...</div>';
				endif;
				*/

				echo '</div>';
			endforeach;
		else:
			echo '<h2>Your files (owned)</h2>';
			foreach($own_files as $f):
				$d_link = site_url().'/files//download/'.$f->file_id;
				$d_anchor = anchor($d_link, 'Download');
				echo '<p class="caption">#'.$f->file_id.'&nbsp;<em>'.$f->file_title.'</em>&nbsp;'.$d_anchor;
				echo '<br /><span class="desc">'.$f->file_desc.'</span>';
				echo '</p>';
			endforeach;
			
			echo '<h2>Your files (assigned)</h2>';
			foreach($assigned_files as $f):
				$d_link = site_url().'/files//download/'.$f->file_id;
				$d_anchor = anchor($d_link, 'Download');
				echo '<p class="caption">#'.$f->file_id.'&nbsp;<em>'.$f->file_title.'</em>&nbsp;'.$d_anchor;
				echo '<br /><span class="desc">'.$f->file_desc.'</span>';
				echo '</p>';
			endforeach;
		endif;
	endif;	//$_type == '' || $_type == 'files'
	
if( $_type == '' ):
?>
	</div>

	<div id="quickLinks">
	<h4>Quick Upload</h4>
	<?php
		$upload_path = $this->config->item('upload_path', 'settings');
		if( !is_writable($upload_path) )
			echo '<span class="inlineErr">Error: Upload path is not Writable: '.$upload_path.'</span>'; 
	?>
	<div id="quickUploadStatus"></div>
	<form action="#" method="post" enctype="multipart/form-data" name="frmQuickUpload">
	  <p>
			Name Your File:<br />
			<input type="text" name="file_title" id="file_title" class="input" size="40" maxlength="128" />
	  </p>
	  <p style="vertical-align:top;">
			Description:<br />
			<textarea name="file_desc" id="file_desc" cols="40" rows="5" class="input"></textarea>
	  </p>
	  <p style="vertical-align:top;">
			<input type="text" id="txtFileName" disabled="true" style="border: solid 1px; background-color: #FFFFFF;width: 100px;" />
			<span id="spanButtonPlaceholder"></span>
	  </p>
	<p style="vertical-align:top;">
		<div class="flash" id="fsUploadProgress"></div>
	</p>
	  <p style="vertical-align:top;">
	    <input type="button" name="btnSubmit" id="btnSubmit" class="btn" value="Submit" />
	    <input type="hidden" name="file_id" id="file_id" value="" />
	  </p>
	</form>

</div>

<script type="text/javascript">
$(document).ready(function(){
	uploadDone = function() {
		//Ajax and pull the ACL
	    $.ajax({
	    	   type: "POST",
	    	   url: "<?php echo site_url().'/files/update/' ?>"+$("#file_id").val(),
	    	   dataType: 'html',
	    	   data: {file_title: $("#file_title").val(), file_desc: $("#file_desc").val()},
	    	   error: function(){
	    		   $("#file_id").val('');
	    		   alert('Error: '+'File updation failed!');
	    	   },
	    	   success: function(oJ){
		    	   swfu.setButtonDisabled(false);
		    	   $("#file_title").val('')
		    	   $("#file_desc").val('');
		    	   $("#txtFileName").val('');
		    	   $("#file_id").val('');

		    	   $("#quickUploadStatus").html("File has been uploaded successfully.");
		    	   
					//Ajax and pull the Files...
				    $.ajax({
				    	   type: "GET",
				    	   url: "<?php echo site_url().'/home/index/files' ?>",
				    	   dataType: 'html',
				    	   error: function(){
				    		   $("#files").html('Error! Click Home to refresh.');
				    	   },
				    	   success: function(fHTML){
				    		   $("#files").html(fHTML);
				    	   }
				    });//ajax
	    	   }
	    });//ajax
	}
});
</script>
<?php
endif;//$_type == ''
?>