<?php
	if( isset($error) ):
		echo "0|$error";
	elseif( isset($upload_data) ):
		echo "$file_id|success";
	endif;
?>