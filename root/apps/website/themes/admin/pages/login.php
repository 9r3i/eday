<?php
/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain',true,401);
  exit('Error: 401 Unauthorized.');
}
/* prepare site */
$site=$this->site;
$menus=$site->menu->top;

/* start html output */
?><!DOCTYPE html><html lang="en-US"><head>
  <meta content="text/html; charset=utf-8" http-equiv="content-type" />
  <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />
  <meta content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" name="viewport" />
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
  <link rel="shortcut icon" href="<?=$site->webPath?>images/logo.png" type="image/png" />
  <?=$this->loadCSS('noscript',true)?>
  <?=$this->loadCSS('font-awesome.min')?>
  <?=$this->loadCSS('sweetalert.min')?>
  <?=$this->loadCSS('admin.min')?>
  <?=$this->loadJS('sweetalert.min')?>
  <?=$this->loadJS('events-1.1.0.min')?>
  <script type="text/javascript">
  var WEBSITE_ADDRESS='<?=EDAY_ADDR?>',
      ADMIN_KEY='<?=$site->adminKey?>';
  </script>
</head><body>
<!-- NOSCRIPT -->
<noscript><div id="noscript"></div></noscript>
<!-- VIEWPORT-TOO-SMALL -->
<div class="viewport-too-small"></div>
<!-- BODY LOGIN -->
<div class="body-login" id="website-login">
<div class="login-header" data-title="Login"></div>
<table class="login-table"><tbody>
<tr><td>Username</td><td><input type="text" name="username" class="login-input" /></td></tr>
<tr><td>Password</td><td><input type="password" name="password" class="login-input" /></td></tr>
<tr><td></td><td><input class="linear" type="submit" value="Login" name="submit" class="login-submit" /></td></tr>
</tbody></table>
<?php
/* prepare site name and year */
$name='9r3i';
$year=date('Y');
$footer="Copyright &copy; {$year}, {$name}, All Right Reserved.";
$menus=$this->site->menu->sidebar;
?>
<div class="login-footer" data-title="<?=$footer?>" title="<?=$name?>"></div>
<div style="clear:both;"></div>
</div>
<div class="pre-load" id="pre-load"></div>
<style type="text/css">
body{
  background-image:url('<?=$site->webPath?>images/wallpaper.jpg');
  background-position:top center;
  background-size:auto auto;
}
</style>
<?=$this->loadJS('admin.min')?>
</body></html>


