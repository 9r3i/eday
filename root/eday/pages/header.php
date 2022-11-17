<?php
/* header for admin app
 * started at august 25th 2018
 */

/* use namespace eday */
use eday\admin;
use eday\site;

/* global title */
global $title;

?><!DOCTYPE html><html lang="en-US" dir="ltr"><head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title><?=$title.(admin::isLogin()?' &#8213; Admin':'')?> &#8213; e-Day</title>
  <meta name="description" content="Admin &#8213; e-Day" />
  <meta name="keywords" content="admin, e-Day" />
  <meta name="robots" content="nofollow,noindex" />
  <meta name="author" content="9r3i" />
  <meta name="uri" content="//github.com/9r3i" />
  <meta property="og:image" content="<?=admin::appURL('images/cart-152.png')?>" />
  <script type="text/javascript" src="<?=admin::appURL('js/header-1.5.1.min.js')?>"></script>
  <script type="text/javascript" src="<?=admin::appURL('js/sweetalert.min.js')?>"></script>
<?php if(admin::isLogin()){ ?>
  <script type="text/javascript" src="<?=admin::appURL('js/admin.js')?>"></script>
<?php } ?>
  <script type="text/javascript">
  var SITE_URL='<?=EDAY_ADDR?>';
  var SITE_ADMIN_KEY='<?=site::adminKey()?>';
<?php if(admin::isLogin()){ ?>
  var ADMIN_EDITOR='<?=admin::config('editor')?>';
<?php foreach(site::defined() as $k=>$v){
if(!preg_match('/^[a-z0-9_]+$/i',$k)){continue;}
$v=preg_match('/^\d+$/',$v)?(int)$v:$v;
$v=is_bool($v)?(bool)$v:$v;
$nv=json_encode($k=='EDAY_APPS'||$k=='EDAY_CONFIG'?unserialize($v):$v);
echo '  var '.$k.'='.$nv.';'."\r\n";
}} ?>
  </script>
  <link href="<?=admin::appURL('css/kitchen.css')?>?v=1.0.0" rel="stylesheet" type="text/css" media="screen,print" />
  <link href="<?=admin::appURL('css/font-awesome.min.css')?>?v=4.3.0" rel="stylesheet" type="text/css" media="screen,print" />
  <link href="<?=admin::appURL('css/sweetalert.min.css')?>?v=1.0.0" rel="stylesheet" type="text/css" media="screen,print" />
  <link href="<?=admin::appURL('images/cart.ico')?>" type="image/x-icon" rel="shortcut icon" />
  <style tyle="text/css" media="screen,print">#noscript:before{content:"Please, activate your browser javascript.";}#noscript{margin:0px;padding:30px;text-align:center;font-size:32px;position:fixed;z-index:9999;top:0px;bottom:0px;left:0px;right:0px;background-color:#eed;}</style>
  <link rel="apple-touch-icon" sizes="57x57" href="<?=admin::appURL('images/cart-57.png')?>" />
  <link rel="apple-touch-icon" sizes="60x60" href="<?=admin::appURL('images/cart-60.png')?>" />
  <link rel="apple-touch-icon" sizes="72x72" href="<?=admin::appURL('images/cart-72.png')?>" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?=admin::appURL('images/cart-76.png')?>" />
  <link rel="apple-touch-icon" sizes="114x114" href="<?=admin::appURL('images/cart-114.png')?>" />
  <link rel="apple-touch-icon" sizes="120x120" href="<?=admin::appURL('images/cart-120.png')?>" />
  <link rel="apple-touch-icon" sizes="144x144" href="<?=admin::appURL('images/cart-144.png')?>" />
  <link rel="apple-touch-icon" sizes="152x152" href="<?=admin::appURL('images/cart-152.png')?>" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?=admin::appURL('images/cart-180.png')?>" />
  <link rel="icon" type="image/png" sizes="192x192" href="<?=admin::appURL('images/cart-192.png')?>" />
  <link rel="icon" type="image/png" sizes="96x96" href="<?=admin::appURL('images/cart-96.png')?>" />
  <link rel="icon" type="image/png" sizes="16x16" href="<?=admin::appURL('images/cart-16.png')?>" />
  <link rel="icon" type="image/png" sizes="32x32" href="<?=admin::appURL('images/cart-32.png')?>" />
</head><body><div class="viewport-too-small"></div>
<noscript><div id="noscript"></div></noscript>
<?php if(admin::isLogin()){ ?>
<div class="header"><?=$title?></div>
<?php } ?>
