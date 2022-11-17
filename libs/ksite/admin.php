<?php
/* check ksite object and request */
if(!isset($this)||!is_object($this)
  ||!$this instanceof ksite
  ||!isset($_GET[$this->core->adminKey])){
  /* error statement */
  header('Content-Type: text/plain',true,401);
  exit('Error: Unauthorized.');
}

/* check login */
if(!isset($_COOKIE['ksite-admin'])
  ||$_COOKIE['ksite-admin']!==$this->user->token){
  return @require_once($this->dir.'login.php');
}

/* prepare request */
$request=$_GET[$this->core->adminKey];
$request=$request==''?'dashboard':$request;

/* cover request -- execute */
$this->coverRequest($request);

/* prepare menu and pages */
$pages=[
  'dashboard'=>'Dashboard',
  'posts'=>'All Posts',
  'logout'=>'Logout',
  'setting'=>'Setting',
  'visitor'=>'Visitor',
  'new'=>'New Post',
  'edit'=>'Edit Post',
  'delete'=>'Delete Post',
  'generate'=>'Generate',
  'generateExec'=>'Generate Execution',
  'upload'=>'Upload',
];
$menus=[
  'dashboard'=>'Dashboard',
  'new'=>'New Post',
  'posts'=>'All Posts',
  'generate'=>'Generate',
  'upload'=>'Upload',
  'visitor'=>'Visitor',
  'setting'=>'Setting',
  'logout'=>'Logout',
];

/* prepare title and file content */
$title=array_key_exists($request,$pages)
  ?$pages[$request]:'404';

/* html */
?><!DOCTYPE html><html lang="en-US" dir="ltr"><head>
  <!-- http-equiv -->
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title><?=$title?> &#8213; ksiteAdmin</title>
  <!-- meta -->
  <meta name="description" content="Admin Page" />
  <meta name="keywords" content="admin" />
  <meta name="robots" content="no index, no follow" />
  <meta name="author" content="<?=$this->site->author?>" />
  <meta name="uri" content="<?=$this->site->uri?>" />
  <!-- style sheet -->
  <link rel="stylesheet" href="ksite/css/admin.css" type="text/css" media="screen,print" />
  <!-- noscript css -->
  <style type="text/css" media="screen,print">/* noscript */#noscript:before{content:"Please activate your browser javascript";}#noscript{position:fixed;top:0px;left:0px;bottom:0px;right:0px;z-index:999;background-color:#fff;font-size:large;color:#777;text-align:center;padding:20px 10px;margin:0px;cursor:default;font-family:consolas,monospace;}</style>
  <!-- favicon -->
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <!-- script header -->
  <script type="text/javascript" src="ksite/js/events-1.1.0.min.js"></script>
  <script type="text/javascript">
  var ksite=<?=$this->getJSON(false)?>;
  var pages=<?=json_encode($pages)?>;
  var upload_max_filesize='<?=ini_get('upload_max_filesize')?>';
  </script>
</head><body><noscript><div id="noscript"></div></noscript>
<div class="admin-menu" id="menu">
<div class="admin-menu-header"></div>
<?php foreach($menus as $href=>$name){
  echo '<a href="'.$this->info->base.'?'.$this->core->adminKey.'='.$href.'" title="'.$name.'">'
    .'<div class="admin-menu-each">'.$name.'</div></a>';
} ?>
<div class="admin-menu-footer"></div>
</div>
<div class="admin-body">
<div class="admin-title" id="title"><?=$title?></div>
<div class="admin-content" id="content">
<?=$this->loadPage($request)?>
</div>
</div>
<div class="admin-footer" data-title="9r3i\ksite:admin"><div class="admin-menu-button" id="menu-button"></div><div>
<script type="text/javascript" src="ksite/js/admin.js?v=<?=$this->date?>"></script>
</body></html>


