<?php
use eday\site;
?><!DOCTYPE html><html lang="en-US" dir="ltr"><head>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title>Maintenance Mode - <?=site::config('name')?></title>
  <meta name="description" content="<?=site::config('description')?>" />
  <meta name="keywords" content="<?=site::config('keyword')?>" />
  <meta name="robots" content="follow,index" />
  <meta name="author" content="9r3i" />
  <meta name="uri" content="//github.com/9r3i" />
  <script type="text/javascript">
  var SITE_URL='<?=EDAY_ADDR?>';
  </script>
  <style tyle="text/css" media="screen,print">#noscript:before{content:"Please, activate your browser javascript.";}#noscript{margin:0px;padding:30px;text-align:center;font-size:32px;position:fixed;z-index:9999;top:0px;bottom:0px;left:0px;right:0px;background-color:#eed;}
  *{font-family:Tahoma,Verdana,Arial,consolas,monospace;color:#222;cursor:default;}
  *::selection{background-color:transparent;}
  body{margin:0px;padding:0px;}
  .index{margin:30px auto;text-align:center;}
  </style>
</head><body><div class="viewport-too-small"></div>
<noscript><div id="noscript"></div></noscript>
<div class="body"><div class="index">
<h1>Welcome to <span style="color:#37b;"><?=site::config('name')?></span> website.</h1>
<h1 style="color:#b33;">[Maintenance Mode]</h1>
<hr style="border:1px dotted #bbb;" />
<h2>Right now, this website is being maintained to get better experience to the users.</h2>
<h2>Please, try to visit us again later.</h2>
<h2 style="color:#eed;">9r3i</h2>
</div>
</div>
<div class="footer"></div>
</body></html>
