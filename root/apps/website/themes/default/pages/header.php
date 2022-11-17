<?php
/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain',true,401);
  exit('Error: 401 Unauthorized.');
}
/* use namespace eday site */
use eday\site;
/* prepare site */
$site=$this->site;
$menus=isset($site->menu->top)?$site->menu->top:[];
/**
header('content-type:text/plain');
print_r($menus);
exit;
//*/

/* start html output */
?><!DOCTYPE html><html lang="en-US"><head>
  <meta content="text/html; charset=utf-8" http-equiv="content-type" />
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
  <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport" />
  <?=$this->plugin()->load('header','');?>
  <title><?=$site->title?></title>
  <meta content="<?=$site->description?>" name="description" />
  <meta content="<?=$site->keywords?>" name="keywords" />
  <meta content="<?=$site->robots?>" name="robots" />
  <meta content="<?=$site->author?>" name="author" />
  <meta content="<?=$site->authorURI?>" name="author-uri" />
  <meta content="<?=$site->generator?>" name="generator" />
  <meta content="<?=$site->version?>" name="version" />
  <link rel="canonical" href="<?=$site->canonical?>" type="text/html" />
  <link rel="pingback" href="<?=$site->pingback?>" type="text/html" />
  <link rel="alternate" href="<?=$site->alternate?>" title="RSS Feed" type="application/rss+xml" />
  <link rel="shortcut icon" href="<?=site::url?>favicon.ico" type="image/x-icon" />
  <?=$this->loadCSS('noscript',true)?>
  <?=$this->loadCSS('index.min')?>
  <?=$this->loadJS('events-1.1.0.min')?>
  <script type="text/javascript">
  var WEBSITE_ADDRESS='<?=EDAY_ADDR?>',
      WEBSITE_REQUEST_URI='<?=EDAY_REQUEST_URI?>',
      WEBSITE_DATA=null,
      WEBSITE_LOAD_PAGES=<?=@json_encode(array_values($this->config->load->toArray()))?>;
  </script>
</head><body>
<!-- NOSCRIPT -->
<noscript><div id="noscript"></div></noscript>
<!-- VIEWPORT-TOO-SMALL -->
<div class="viewport-too-small"></div>
<!-- HEADER -->
<div class="header" id="header" data-name="<?=$site->name?>">
<!-- MENU-BUTTON -->
<div class="menu-button" id="menu-button">
  <div class="menu-button-strip"></div>
  <div class="menu-button-strip"></div>
  <div class="menu-button-strip"></div>
</div>
<!-- MENU -->
<div class="menu" id="menu">
  <div class="menu-header" id="menu-header"></div>
<?php foreach($menus as $menu){ ?>
  <a href="<?=site::url.$menu->slug?>" title="<?=$menu->name?>"><div class="menu-each"><?=$menu->name?></div></a>
<?php } ?>
</div>
</div>
<!-- BODY -->
<div class="body" id="website-body">
<div class="body-content" id="website-content">
