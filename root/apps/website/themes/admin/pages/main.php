<?php
/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain',true,401);
  exit('Error: 401 Unauthorized.');
}
/* use namespace */
use eday\site;
/* prepare site */
$site=$this->site;
$menus=$site->menu->top;

/* testing script *
header('content-type:text/plain');
$db=site::db();
//$select=$db->query('select * from users');
//print_r($select);exit;
//print_r(get_defined_constants(true)['user']);exit;
//print_r(site::config());
//$select=$this->db->select('users');
//$select=$this->db->select('menu');
//$select=$this->db->select('posts','url=personal-coaching');
//print_r($select);
print_r($this->site);
exit;
//*/

/* start html output */
?><!DOCTYPE html><html lang="en-US"><head>
  <meta content="text/html; charset=utf-8" http-equiv="content-type" />
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
  <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport" />
  <title data-title="<?=$site->title?>"><?=$site->title?></title>
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
  <link rel="shortcut icon" href="<?=$site->webPath?>images/logo.png" type="image/png" />
  <?=$this->loadCSS('noscript',true)?>
  <?=$this->loadCSS('font-awesome.min')?>
  <?=$this->loadCSS('sweetalert.min')?>
  <?=$this->loadCSS('admin.min')?>
  <?=$this->loadJS('sweetalert.min')?>
  <?=$this->loadJS('events-1.1.0.min')?>
  <?=$this->loadJS('dateSelect.min')?>
  <?=$this->loadJS('pictureSelect.min')?>
  <script type="text/javascript">
  var WEBSITE_ADDRESS='<?=EDAY_ADDR?>',
      ADMIN_KEY='<?=$site->adminKey?>',
      ADMIN_PATH='<?=$site->webPath?>';
  </script>
</head><body>
<!-- NOSCRIPT -->
<noscript><div id="noscript"></div></noscript>
<!-- VIEWPORT-TOO-SMALL -->
<div class="viewport-too-small"></div>
<!-- HEADER -->
<div class="header" id="header" data-name="<?=$site->title?>">
<!-- MENU-BUTTON -->
<div class="menu-button" id="menu-button">
  <div class="menu-button-strip"></div>
  <div class="menu-button-strip"></div>
  <div class="menu-button-strip"></div>
</div>
<!-- MENU -->
<div class="menu" id="menu">
  <div class="menu-header" id="menu-header"></div>
<?php foreach($menus as $menu){
  $externalMenu=$menu->external?' menu-external':'';
  echo '<a href="'.site::url.$menu->slug.'" data-title="'.$menu->name.'">'
    .'<div class="menu-each">'
    .'<i class="fa fa-'.$menu->icon.$externalMenu.'"></i>'
    .'<div class="menu-text">'.$menu->name.'</div>'
    .'</div></a>';
} ?>
</div>
</div>
<!-- BODY -->
<div class="body" id="website-body">
<div class="body-content" id="website-content">

</div>
<div style="clear:both;"></div>
</div>
<?php
/* prepare site name and year */
$name='9r3i';
$year=date('Y');
$footer="Copyright &copy; {$year}, {$name}, All Right Reserved.";
$menus=$this->site->menu->sidebar;
?>
<div class="pre-load" id="pre-load"></div>
<?=$this->loadJS('admin.min')?>
</body></html>


