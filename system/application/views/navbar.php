<div id="navbar">
<?php
$admin_links = array(
						'Home' => 'home',
						'Manage Users' => 'users',
						'My Account' => site_url().'/profile',
						'Logout' => 'auth/logout'
					);

$user_links = array(
						'Home' => 'home',
						'My Account' => site_url().'/profile',
						'Logout' => 'auth/logout'
					);

$cbx_links = array();
$cbx_links['My Account'] = 'doxentralcbx';

$nav_links = $user_links;
if( $user_data['user_type'] == 1 )
	$nav_links = $admin_links;

foreach($nav_links as $t => $l) {
	if( $l=='home'):
		$_link = array('class' => ($l==$this->uri->segment(1)||$this->uri->segment(1)=='') ? 'link-active' : 'link');
	else:
		$_link = array('class' => $l==$this->uri->segment(1) ? 'link-active' : 'link');
	endif;

	if( isset($cbx_links[$t]) )
		$_link['rel'] = $cbx_links[$t];

	echo anchor($l, $t, $_link).'&nbsp;&nbsp&nbsp;&nbsp&nbsp';
}
?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	//ColorBox Assignment for My Account
	$("a[rel='doxentralcbx']").colorbox({title: 'My Account', width: '400px'});
});
</script>

<h1><?php echo 'Welcome <span>'.$user_data['user_firstname'].'!</span>'; ?></h1>