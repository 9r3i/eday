<?php
/* check ksite object and request */
if(!isset($this)||!is_object($this)
  ||!$this instanceof ksite){
  /* error statement */
  header('Content-Type: text/plain',true,401);
  exit('Error: Unauthorized.');
}

/* ----- testing script ----- *
header('content-type:text/plain');
print_r($this);
exit;
//*/

/* prepare get */
$get=new ksiteData($_GET);

/* prepare default data */
$data=(object)[
  'id'=>0,
  'title'=>'404 Not Found',
  'content'=>'Error: 404 Not Found.',
  'time'=>time(),
  'description'=>'Error: 404 Not Found',
  'canonical'=>'',
  'pingback'=>'',
  'alternate'=>$this->info->base.'rss.xml',
];

/* prepare data object */
$raw=json_encode($data);
$list=$this->getData('list');
$likes=$this->getData('likes');
$tags=$this->getData('tags');

/* prepare user */
$user=$this->user->toArray();
unset($user['password']);
unset($user['username']);
unset($user['token']);
$user=json_encode($user);

/* prepare data */
if(isset($get->error)){
  /* nothing change */
}elseif(isset($get->id)){
  $rawTemp=$this->getData('id/'.$get->id);
  $parsed=is_string($rawTemp)?@json_decode($rawTemp):false;
  if(is_object($parsed)){
    $raw=$rawTemp;
    foreach($parsed as $k=>$v){
      $data->{$k}=$v;
    }
    $data->canonical=$this->info->base.'?id='.$get->id;
    $data->pingback=$this->info->base.'?id='.$get->id;
  }
}elseif(isset($get->home)||$get->length()==0){
  $rawTemp=$this->getData('home');
  $parsed=is_string($rawTemp)?@json_decode($rawTemp):false;
  if(is_object($parsed)){
    $raw=$rawTemp;
    foreach($parsed as $k=>$v){
      $data->{$k}=$v;
    }
    $data->canonical=$this->info->base;
    $data->pingback=$this->info->base;
  }
}elseif(isset($get->author)){
  $rawTemp=$this->getData('author');
  $parsed=is_string($rawTemp)?@json_decode($rawTemp):false;
  if(is_object($parsed)){
    $raw=$rawTemp;
    foreach($parsed as $k=>$v){
      $data->{$k}=$v;
    }
    $data->canonical=$this->info->base.'?author='.$get->author;
    $data->pingback=$this->info->base.'?author='.$get->author;
  }
}elseif(isset($get->search)){
  $rawTemp=$this->blogSearch($get->search);
  $parsed=is_string($rawTemp)?@json_decode($rawTemp):false;
  if(is_object($parsed)){
    $raw=$rawTemp;
    foreach($parsed as $k=>$v){
      $data->{$k}=$v;
    }
    $data->canonical=$this->info->base.'?search='.$get->search;
    $data->pingback=$this->info->base.'?search='.$get->search;
  }
}elseif(isset($get->tag)){
  $rawTemp=$this->getData('tag/'.$get->tag);
  $parsed=is_string($rawTemp)?@json_decode($rawTemp):false;
  if(is_object($parsed)){
    $raw=$rawTemp;
    foreach($parsed as $k=>$v){
      $data->{$k}=$v;
    }
    $data->canonical=$this->info->base.'?tag='.$get->tag;
    $data->pingback=$this->info->base.'?tag='.$get->tag;
  }
}

/*  */
/* use minimize */
$hosts=['localhost','127.0.0.1','0.0.0.0','192.168.43.42','192.168.43.1'];
$useMin=in_array($this->info->host,$hosts)?'':'.min';

/* prepare title */
$title=$data->title?$data->title.' &#8213; ':'';

/* html */
?><!DOCTYPE html><html lang="en-US" dir="ltr"><head>
  <!-- http-equiv -->
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title><?=$title?><?=$this->site->name;?></title>
  <!-- meta -->
  <meta name="description" content="<?=$data->description?>" />
  <meta name="keywords" content="<?=$this->site->keywords?>" />
  <meta name="robots" content="<?=$this->site->robots?>" />
  <meta name="author" content="<?=$this->site->author?>" />
  <meta name="uri" content="<?=$this->site->uri?>" />
  <!-- style sheet -->
  <link rel="stylesheet" href="ksite/css/blog<?=$useMin?>.css" type="text/css" media="screen,print" />
  <!-- noscript css -->
  <style type="text/css" media="screen,print">/* noscript */#noscript:before{content:"Please activate your browser javascript";}#noscript{position:fixed;top:0px;left:0px;bottom:0px;right:0px;z-index:999;background-color:#fff;font-size:large;color:#777;text-align:center;padding:20px 10px;margin:0px;cursor:default;font-family:consolas,monospace;}</style>
  <!-- opengraph -->
  <meta property="og:image" content="https://luthfie.com/ksite/images/logo/luthfie-logo.png" />
  <!-- favicon -->
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <!-- apple touch icons -->
  <?=$this->appleTouchIcon('ksite/images/logo/luthfie-logo-%s.png')?>
  <!-- canonical, pinpback and alternate link -->
  <link rel="canonical" href="<?=$data->canonical?>" type="text/html" />
  <link rel="pingback" href="<?=$data->pingback?>" type="text/html" />
  <link rel="alternate" href="<?=$data->alternate?>" title="RSS Feed" type="application/rss+xml" />
  <!-- script header -->
  <script type="text/javascript">
  var KSITE_ADDRESS='<?=$this->info->base?>',
      KSITE_DATA_KEY='<?=$this->core->blogData?>',
      KSITE_LIKE_KEY='<?=$this->core->blogLike?>',
      KSITE_SEARCH_KEY='<?=$this->core->blogSearch?>',
      KSITE_SITE_NAME='<?=$this->site->name?>',
      KSITE_USER=<?=is_string($user)?$user:'null'?>,
      KSITE_BLOG_DATA=<?=is_string($raw)?$raw:'null'?>,
      KSITE_BLOG_LIST=<?=is_string($list)?$list:'null'?>,
      KSITE_BLOG_LIKES=<?=is_string($likes)?$likes:'null'?>,
      KSITE_BLOG_TAGS=<?=is_string($tags)?$tags:'null'?>;
  </script>
</head><body>
<!-- noscript tags -->
<noscript><div id="noscript"></div></noscript>
<!-- header -->
<div id="header"><div id="header-title"></div><div id="header-menu"></div><div id="header-search" class="search-button"></div></div>
<!-- list -->
<div id="blog-list" class="blog-list"></div>
<!-- body -->
<div id="body"><div id="body-inner">
<div id="body-inner-left"><div id="body-inner-left-content">
<div class="title" id="title"></div>
<div class="content" id="content"></div>
</div></div>
<div id="body-inner-right">
<div class="sidebar-row">
  <div class="sidebar-title" data-title="Advertisement"></div>
  <div class="sidebar-content"><div class="space-iklan"></div></div>
</div>
<div class="sidebar-row">
  <div class="sidebar-title" data-title="Advertisement"></div>
  <div class="sidebar-content"><div class="space-iklan-isi" data-content="Pastikan anda bebas dari berbagai penyakit, dan temukan penyebabnya sekarang juga.">
  <br /><a href="?id=159">Klik Disini</a></div></div>
</div>
<div class="sidebar-row">
  <div class="sidebar-title" data-title="Recent Posts"></div>
  <div class="sidebar-content"><ul id="recent-posts" class="recent"></ul></div>
</div>
<div class="sidebar-row">
  <div class="sidebar-title" data-title="CPU Speed Test"></div>
  <div class="sidebar-content"><div id="cpu"></div><div class="pre-loader-bubbling"></div></div>
</div>
</div>
</div></div>
<!-- footer -->
<div id="footer"></div>
<!-- last script -->
<script type="text/javascript" src="ksite/js/blog<?=$useMin?>.js?v=<?=$this->date?>"></script>
</body></html>
<?=$this->generatedBy()?>


