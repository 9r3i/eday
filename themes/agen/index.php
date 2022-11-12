<?php
/* agen theme
 * ~ theme for e-day website
 * authored by 9r3i
 * https://github.com/9r3i
 * started at july 18th 2018
 */

/* load theme pre-load */
require_once('before.php');
//header('content-type:text/plain');print_r($init);exit;

?><!DOCTYPE html><html><head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="Shortcut Icon" href="themes/agen/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="themes/agen/css/v-css.css" type="text/css" media="all" />
  <link rel="stylesheet" href="themes/agen/css/style.css" type="text/css" media="all" />
  <link rel="stylesheet" href="themes/agen/css/other.css" type="text/css" media="all" />
  <link rel="stylesheet" href="themes/agen/css/settings.css" type="text/css" media="screen">
  <title><?=$site->title?></title>
  <meta name="description" content="<?=$site->description?>" />
  <meta name="keywords" content="<?=$site->keyword?>" />
  <meta property="og:image" content="<?=site::themeURL('icons/cleanjelly-152.png')?>" />
  <script type="text/javascript">var templateDirectory="test/themes/agen";var popup_act="0";</script>
  <link rel="apple-touch-icon" sizes="57x57" href="<?=site::themeURL('icons/cleanjelly-57.png')?>" />
  <link rel="apple-touch-icon" sizes="60x60" href="<?=site::themeURL('icons/cleanjelly-60.png')?>" />
  <link rel="apple-touch-icon" sizes="72x72" href="<?=site::themeURL('icons/cleanjelly-72.png')?>" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?=site::themeURL('icons/cleanjelly-76.png')?>" />
  <link rel="apple-touch-icon" sizes="114x114" href="<?=site::themeURL('icons/cleanjelly-114.png')?>" />
  <link rel="apple-touch-icon" sizes="120x120" href="<?=site::themeURL('icons/cleanjelly-120.png')?>" />
  <link rel="apple-touch-icon" sizes="144x144" href="<?=site::themeURL('icons/cleanjelly-144.png')?>" />
  <link rel="apple-touch-icon" sizes="152x152" href="<?=site::themeURL('icons/cleanjelly-152.png')?>" />
  <link rel="apple-touch-icon" sizes="180x180" href="<?=site::themeURL('icons/cleanjelly-180.png')?>" />
  <link rel="icon" type="image/png" sizes="192x192" href="<?=site::themeURL('icons/cleanjelly-192.png')?>" />
  <link rel="icon" type="image/png" sizes="96x96" href="<?=site::themeURL('icons/cleanjelly-96.png')?>" />
  <link rel="icon" type="image/png" sizes="16x16" href="<?=site::themeURL('icons/cleanjelly-16.png')?>" />
  <link rel="icon" type="image/png" sizes="32x32" href="<?=site::themeURL('icons/cleanjelly-32.png')?>" />
<body>
<div class="agen-wrap">



<div class="agen-header">
  <div class="container">
    <div class="row">
       <div class="col-md-5 col-sm-5">
        <div class="header-logo"><a href="#/" title="<?=$site->name?>">
          <img src="files/logo.png" alt="<?=$site->name?>">
        </a></div>
      </div>
      <div class="visible-xs clearfix"></div>
      <div class="col-md-4 col-sm-3">
        <div class="top-kontak">
          <span class="glyphicon glyphicon-time"></span> <?=$site->contact->time?><br />
          <span class="glyphicon glyphicon-earphone"></span> <?=$site->contact->earphone?><br />
          <span class="glyphicon glyphicon-phone"></span> <?=$site->contact->phone?><br />
          <i class="icon-whatsapp"></i> <?=$site->contact->whatsapp?><br />
          <!-- <i class="icon-bbm"></i> <?=$site->contact->bbm?><br /> -->
          <!-- <span class="glyphicon glyphicon-envelope"></span> <?=$site->contact->email?> -->
        </div>
      </div>
      <div class="col-md-3 col-sm-4">
        <div class="cart">
          <a href="#/keranjang" title="keranjang belanja">
            <i class="icon-basket"></i> (<span id="tampilJumlah">0</span> pcs)
          </a>
          <div class="cart-down" style="display: none;">
            <div id="virtacart">
              <input name="virtacartToken" value="bea187098505818ffe32cc5256f78ef6" type="hidden" />
              <table class="table">
                <thead><tr><th>Gambar</th><th>Barang</th><th>jml</th><th>Berat</th><th>Total</th></tr></thead>
                <tbody>
                  <tr><td id="kosong" colspan="5">keranjang belanja anda kosong</td></tr>
                </tbody>
                <tfoot><tr><th></th><th></th><th><span id="xtotalBarang">0</span><input name="totalBarang" id="totalBarang" value="0" type="hidden"></th><th><span id="xtotalBerat">0,00</span><input name="totalBerat" id="totalBerat" value="0.00" type="hidden"></th><th><span id="xtotalHarga">Rp 0</span><input name="totalHarga" id="totalHarga" value="0" type="hidden"></th></tr></tfoot>
              </table>
              <div id="virtacart-betul">barang telah di tambahkan ke keranjang</div>
              <div id="virtacart-salah">tentukan pilihan</div>
            </div>
            <div class="btn-cart">
              <a class="btn pull-right" href="#/keranjang">Selesai Belanja </a>
            </div>
          </div>
          <span class="vtr-search-icon"><i class="icon-search pull-right"></i></span>
          <div class="search-box">
            <form action="#" class="search-form" method="get">
              <input required="" class="search-text" name="s" placeholder="cari produk" type="text">
              <button class="search-button" type="submit"><i class="icon-search"></i></button>
            </form>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>
<!-- end of header -->
 



<div class="vtr-menu-wrap">
  <div class="vtr-menu-icon">Menu <i class="icon-th-list pull-right"></i></div>
  <div class="menu-home-container"><ul id="menu-home" class="mobile-menu">
<?php
foreach($site->menu as $menu){
  $currentItem=$site->current==$menu[0]?' current-menu-item':'';
  $childrenItem=isset($menu[2])?' menu-item-has-children':'';
  $submenu='';
  if(isset($menu[2])){foreach($menu[2] as $sub){
    $submenu2='';
    if($sub[2]){foreach($sub[2] as $sub2){
      $submenu2.='<li class="menu-item"><a href="'.$sub2[0].'">'.$sub2[1].'</a></li>';
    }}
    $submenu.='<li class="menu-item"><a href="'.$sub[0].'">'.$sub[1].'</a><ul>'.$submenu2.'</ul></li>';
  }}
  echo '<li class="menu-item'.$currentItem.$childrenItem.'">'
    .'<a href="'.site::url.$menu[0].'">'.$menu[1].'</a>'
    .($childrenItem?'<ul>'.$submenu.'</ul>':'').'</li>';
}
?>
  </ul></div>
</div>
<!-- end of menu-wrap -->



<div class="menu-home-container"><ul id="menu-home-1" class="vtr-menu">
<?php
foreach($site->menu as $menu){
  $currentItem=$site->current==$menu[0]?' current-menu-item':'';
  $childrenItem=isset($menu[2])?' menu-item-has-children':'';
  $submenu='';
  if(isset($menu[2])){foreach($menu[2] as $sub){
    $submenu2='';
    if($sub[2]){foreach($sub[2] as $sub2){
      $submenu2.='<li class="menu-item"><a href="'.$sub2[0].'">'.$sub2[1].'</a></li>';
    }}
    $submenu.='<li class="menu-item"><a href="'.$sub[0].'">'.$sub[1].'</a><ul>'.$submenu2.'</ul></li>';
  }}
  echo '<li class="menu-item'.$currentItem.$childrenItem.'">'
    .'<a href="'.site::url.$menu[0].'">'.$menu[1].'</a>'
    .($childrenItem?'<ul>'.$submenu.'</ul>':'').'</li>';
}
?>
</ul></div>
<!-- end of menu container -->



<?php if($site->current==''): ?>
<div class="agen-slider">
  <div class="bannercontainer">
    <div class="tp-banner">
      <ul>
<?php
$slideTransitions=['boxfade','3dcurtain-vertical','cubic','slotzoom-horizontal'];
foreach($site->slider as $slider){
  echo '<li data-transition="'.$slideTransitions[mt_rand(0,3)].'"'
    .' data-slotamount="4" data-masterspeed="1000">'
    .'<img src="'.$slider.'" alt="" data-bgfit="cover" data-bgposition="left top"'
    .' data-bgrepeat="no-repeat" width="1000" height="400">'
    .'</li>';
}
?>
      </ul>
      <div class="tp-bannertimer tp-bottom"></div>
    </div>
  </div>
</div>
<!-- end of slider -->
<?php endif; ?>



<div class="slider-home">
  <div class="slider-home-wrapper"><ul id="topslider">
<?php
foreach($site->slider_home as $sliderHome){
  echo '<li style="margin-right: 7px;"><div class="agen-produk2">'
    .'<div class="agen-gambar-center"><div class="agen-gambar">'
    .'<a href="'.$sliderHome[0].'" title="'.$sliderHome[1].'">'
    .'<img class="lazy" src="themes/agen/images/asli.gif" '
      .'data-original="'.$sliderHome[2].'" alt="" width="auto" height="auto">'
    .'</a>'
    .'</div></div></div></li>';
}
?>  
  </ul></div>
  <div class="clearfix"></div>
  <a class="prev" id="topslider_prev" href="#" style="display:block;"><span>prev</span></a>
  <a class="next" id="topslider_next" href="#" style="display:block;"><span>next</span></a> 
</div>
<!-- end of slider-home -->





<!-- long tags -->
<div class="agen-konten">
  <div class="container">
    <div class="row">


      <div class="col-md-7">


        <?php if($site->error): ?>
        <div class="featured-title"><h1>Error</h1></div>
        <div class="post">
          <h1>Error: <?=$site->error?></h1>
        </div>
        <?php endif; ?>


        <?php if(is_object($site->post)&&isset($site->post->id)): ?>
        <div class="featured-title"><h1><?=$site->post->title?></h1></div>
        <div class="post">
          <?=agenGeneratePostBlog($site->post)?>
        </div>
        <?php endif; ?>


        <?php if(is_object($site->product)&&isset($site->product->id)): ?>
        <div class="featured-title"><h1 style="text-align:center;"><?=$site->product->name?></h1></div>
        <div class="post">
          <?=agenGenerateProductPost($site->product)?>
        </div>
        <?php endif; ?>


        <?php if(is_array($site->tag)): ?>
        <div class="featured-title"><h1 style="text-align:center;">
          Tag: <?=basename($_GET['tag'])?></h1></div>
          <?=agenGenerateTagPost($site->tag)?>
        <?php endif; ?>


        <div class="featured-title"><h1>Produk Terbaru</h1></div>
        <div class="boxer">
          <div class="row">	
<?php
$prolimit=$site->current==''?9:3;
$procount=0;
foreach($site->products as $product){
  if($procount>=$prolimit){break;}
  $procount++;
  echo agenGenerateProduct((object)$product);
}
?>

            <div style="clear: both"></div>
            <!--
            <div class="index-pagenavi">
              <span class="pages">Page 1 of 3:</span>
              <strong class="current">1</strong>
              <a href="#/page/2/">2</a>
              <a href="#/page/3/">3</a>
              <a href="#/page/2/"><i class="icon-right-open"></i></a>
            </div>
            -->
          </div>        
        </div>

        <div class="featured-title"><h2>Info Terbaru</h2></div>
        <div class="boxer">
          <div class="list-blog">
            <ul>
<?php
foreach($site->blogs as $blog){
  echo agenGeneratePost((object)$blog);
}
?>
		    </ul>
          </div>
        </div>

      </div>
      <!-- end of col-md-7 -->


      <div class="col-md-5">
        <div class="row">

          <div class="col-md-6 col-sm-6">


<div class="sidebar-menu">
  <div class="sidebar-menu-icon">Kategori <i class="icon-th-list pull-right"></i></div>
  <div class="sidebar-mobile-menu">
	<div class="menu-home-container"><ul id="menu-home-2" class="vtr-sidebar-menu">
<?php
foreach($site->menu as $menu){
  $currentItem=$site->current==$menu[0]?' current-menu-item':'';
  $childrenItem=isset($menu[2])&&!empty($menu[2])?' menu-item-has-children':'';
  $submenu='';
  if(isset($menu[2])&&!empty($menu[2])){foreach($menu[2] as $sub){
    $submenu2='';
    if($sub[2]){foreach($sub[2] as $sub2){
      $submenu2.='<li class="menu-item"><a href="'.$sub2[0].'">'.$sub2[1].'</a></li>';
    }}
    $submenu.='<li class="menu-item"><a href="'.$sub[0].'">'.$sub[1].'</a><ul>'.$submenu2.'</ul></li>';
  }}
  echo '<li class="menu-item'.$currentItem.$childrenItem.'">'
    .'<a href="'.$menu[0].'">'.$menu[1].'</a>'
    .($childrenItem?'<ul>'.$submenu.'</ul>':'').'</li>';
}
?>
    </ul></div>
  </div>  
</div>

<div class="box"><h4>Search</h4>
  <form role="search" method="get" id="searchform" class="searchform" action="#/search"><div>
	<label class="screen-reader-text" for="s">Search for:</label>
	<input name="key" id="s" type="text">
	<input id="searchsubmit" value="Search" type="submit">
  </div></form>
</div>

<div class="box"><h4>Recent Posts</h4><ul>
<?php
foreach($site->blogs as $blog){
  $blog=(object)$blog;
  echo '<li><a href="'.$blog->url.'" title="'.$blog->title.'">'.$blog->title.'</a></li>';
}
?>
</ul></div>

<div class="box"><h4>Meta</h4><ul>
  <li><a href="?admin">Log in</a></li>
</ul></div>

<div class="box"><h4>Tags</h4><div class="tagcloud">
<?php
foreach($site->tags as $tag){
  echo '<a href="'.$tag[0].'" title="'.$tag[1].'" style="font-size:'.$tag[2].'px;white-space:pre;">'.$tag[1].'</a> ';
}
?>
</div></div>


          </div>


          <div class="col-md-6 col-sm-6 sidebar">


<div class="box"><h4>Facebook</h4>
  <div class="fanpage">
    <iframe src="//www.facebook.com/plugins/likebox.php?href=<?=$site->fanpage?>&amp;width=183&amp;height=285&amp;colorscheme=light&amp;show_faces=true&amp;border_color=%23FFF&amp;stream=false&amp;header=false" style="border:0;background-color:#FFF;overflow:hidden;width:100%;height:285px;"></iframe>
  </div>
  <div style="clear:both"></div>
</div>

<div class="box"><h4>Rekening Bank</h4>
<?php
foreach($site->accounts as $bank=>$account){
  if(!$account){continue;}
  echo '<div class="'.$bank.'">'.$account.'</div>';
}
?>
  <div style="clear:both"></div>
</div>

<div class="box"><h4>Pengiriman</h4>
  <div class="ekspedisi">
  <!--
    <div class="jne"></div>
    <div class="tiki"></div>
    <div class="pos"></div>
    <div class="wahana"></div>
    <div style="clear: both"></div>
  -->
  </div>
</div>

<div class="box"><h4>Temukan Kami di</h4>
<?php
foreach($site->externals as $web=>$url){
  if(!$url){continue;}
  echo '<a href="'.$url.'" target="_blank" rel="nofollow"><div class="'.$web.'"></div></a>';
}
?>
  <div style="clear: both"></div>
</div>

<div class="box"><h4>Tamu Kunjungan</h4>
  <div class="textwidget">
    <p style="text-align:center;"><?=number_format($site->total_visitor,0)?></p>
  </div>
</div>

<div class="box"><h4>Print &amp; Download</h4>
<?php
foreach($site->downloads as $down){
  echo '<a href="'.$down[0].'" class="button-widget" title="download" target="_blank" rel="nofollow">'
    .'<span class="button-widget-icon"><i class="icon-download"></i></span>'
    .'<span class="button-widget-text">Download</span>'
    .'<span class="button-widget-link">'.$down[1].'</span>'
    .'</a>';
}
?>
</div>

<div class="box"><h4>Tawk</h4>
  <div class="textwidget">
    <p></p>
  </div>
</div>


          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- end of konten -->




<div class="agen-footer"><div class="container"><div class="row"><div class="col-md-12">
  <p><?=$site->footer?></p>
  <p>Copyright &copy; <?=date('Y')?>, <a href="#/"><?=$site->name?></a></p>
  <div class="keatas"><a href="#"><i class="icon-up-circled"></i></a></div>
</div></div></div></div>
<!-- end of footer -->


<script type="text/javascript" src="themes/agen/js/jquery-1.11.3.js" defer="defer"></script>
<script type="text/javascript" src="themes/agen/js/jquery-migrate.js" defer="defer"></script>
<script type="text/javascript" src="themes/agen/js/bootstrap-3.3.5.js" defer="defer"></script>
<script type="text/javascript" src="themes/agen/js/slider-upper.js" defer="defer"></script>
<script type="text/javascript" src="themes/agen/js/theme-punch-tools-1.11.2.js" defer="defer"></script>
<script type="text/javascript" src="themes/agen/js/rev-slider-4.0.6.js" defer="defer"></script>
<script type="text/javascript" src="themes/agen/js/rev-slider-setting.js" defer="defer"></script>
<script type="text/javascript" src="themes/agen/js/virtacart.js" defer="defer"></script>

</div>
<!-- end of agen-wrap -->
</body></html>
<?php
echo '<!-- theme generated in '.number_format(microtime(true)-$site->start,3,'.','').' sec -->';



