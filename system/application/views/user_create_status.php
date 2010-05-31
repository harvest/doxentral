<?php
	if( $user_id > 0 )
		echo 'New User Created successfully! #'.$user_id;
	else
		echo 'New User Creation failed! #'.$user_id;

	echo '<br />';
	echo validation_errors();
?>