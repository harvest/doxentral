<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo isset($page_title) ? 'doxentral' : $page_title; ?></title>

<?php
$main_css_link = array(
          'href' => 'css/main.css',
          'rel' => 'stylesheet',
          'type' => 'text/css',
          'media' => 'screen'
);
echo link_tag($main_css_link);
?>

<script type="text/javascript" src="<?php echo base_url().'js/jquery-1.4.min.js'; ?>"></script>

</head>
<body>