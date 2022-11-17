<?php
/* header for default application
 * started at september 4th 2018
 */

/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain');
  exit('Error: This application requires e-Day engine.');  
}

/* use namespace */
use eday\site;

/* global site */
global $site;

?><!DOCTYPE html><html lang="en-US" dir="ltr"><head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title><?=$site->title?></title>
  <meta name="description" content="<?=$site->description?>" />
  <meta name="keywords" content="<?=$site->keyword?>" />
  <meta name="robots" content="follow,index" />
  <meta name="author" content="9r3i" />
  <meta name="uri" content="//github.com/9r3i" />
  <meta property="og:image" content="<?=site::appURL('images/cart-152.png')?>" />
  <script type="text/javascript" src="<?=site::appURL('js/header-1.5.1.min.js')?>"></script>
  <script type="text/javascript" src="<?=site::appURL('js/sweetalert.min.js')?>"></script>
  <script type="text/javascript" src="<?=site::appURL('js/default.js')?>"></script>
  <script type="text/javascript">
  var SITE_URL='<?=EDAY_ADDR?>';
  </script>
  <link href="<?=site::appURL('css/default.css')?>?v=1.0.0" rel="stylesheet" type="text/css" media="screen,print" />
  <link href="<?=site::appURL('css/font-awesome.min.css')?>?v=4.3.0" rel="stylesheet" type="text/css" media="screen,print" />
  <link href="<?=site::appURL('css/sweetalert.min.css')?>?v=1.0.0" rel="stylesheet" type="text/css" media="screen,print" />
  <link href="<?=site::appURL('images/cart.ico')?>" type="image/x-icon" rel="shortcut icon" />
  <style tyle="text/css" media="screen,print">#noscript:before{content:"Please, activate your browser javascript.";}#noscript{margin:0px;padding:30px;text-align:center;font-size:32px;position:fixed;z-index:9999;top:0px;bottom:0px;left:0px;right:0px;background-color:#eed;}</style>
  <link rel="apple-touch-icon" sizes="57x57" href="<?=site::appURL('images/cart-57.png')?>" />
  <link rel="apple-touch-icon" sizes="60x60" href="<?=site::appURL('images/cart-60.png')?>" />
  <link rel="apple-touch-icon" sizes="72x72" href="<?=site::appURL('images/cart-72.png')?>" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?=site::appURL('images/cart-76.png')?>" />
  <link rel="apple-touch-icon" sizes="114x114" href="<?=site::appURL('images/cart-114.png')?>" />
  <link rel="apple-touch-icon" sizes="120x120" href="<?=site::appURL('images/cart-120.png')?>" />
  <link rel="apple-touch-icon" sizes="144x144" href="<?=site::appURL('images/cart-144.png')?>" />
  <link rel="apple-touch-icon" sizes="152x152" href="<?=site::appURL('images/cart-152.png')?>" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?=site::appURL('images/cart-180.png')?>" />
  <link rel="icon" type="image/png" sizes="192x192" href="<?=site::appURL('images/cart-192.png')?>" />
  <link rel="icon" type="image/png" sizes="96x96" href="<?=site::appURL('images/cart-96.png')?>" />
  <link rel="icon" type="image/png" sizes="16x16" href="<?=site::appURL('images/cart-16.png')?>" />
  <link rel="icon" type="image/png" sizes="32x32" href="<?=site::appURL('images/cart-32.png')?>" />
</head><body><div class="viewport-too-small"></div>
<noscript><div id="noscript"></div></noscript>
<div class="header"><?=$site->title?></div>
<div class="body"><div class="index">

