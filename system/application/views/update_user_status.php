<?php
if( $error != '' )
	echo '<p class="bad">'.$error.'</p>';

if( $update_status != '' )
	echo '<p class="good">'.$update_status.'</p>';

echo validation_errors('<div class="inlineErr">', '</div>');
?>