<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo trim($page_title) == '' ? 'doxentral' : $page_title; ?></title>

<?php
$main_css_link = array(
          'href' => 'css/main.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media' => 'screen'
);
echo link_tag($main_css_link);

$cbx_css_link = array(
          'href' => 'css/colorbox.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media' => 'screen'
);
echo link_tag($cbx_css_link);
?>
<!--swfupload-->
<link href="<?=base_url()?>css/default.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo base_url().'js/jquery-1.4.min.js'; ?>"></script>
<script type="text/javascript" src="<?php echo base_url().'js/jquery.colorbox-min.js'; ?>"></script>

<script type="text/javascript" src="<?=base_url()?>js/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/swfupload/fileprogress.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/swfupload/handlers.js"></script>

</head>
<body>

<noscript>
<div class="err">
	NOTE: Many features on this Website require Javascript and Cookies. Please see: <a href="http://www.google.com/support/bin/answer.py?answer=23852" class="blue" target="_blank">How to enable JavaScript in your browser</a>.</p>
</div>
</noscript>

<?php
	$img_logo = array(
	          'src' => 'images/doxentral.jpg',
	          'alt' => 'doxentral Logo',
	          'class' => 'logo',
	          'title' => 'doxentral Logo',
	);

	echo img($img_logo);
?>
<div class="clear"></div>

