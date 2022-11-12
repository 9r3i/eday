<?php
/* before for agen theme
 * ~ theme for website
 * authored by 9r3i
 * https://github.com/9r3i
 * started at july 18th 2018
 */

/* check path */
defined('EDAY_THEME_PATH') or die('Invalid request theme.');

/* initialize site information */
$siteINI=(object)@parse_ini_file('config.ini',true);
$init=get::info();

//header('content-type:text/plain');print_r($init);exit;

/* create object site */
$site=(object)[
  'start'=>microtime(true),
  /* core info */
  'name'=>$init->name,
  'description'=>$init->description,
  'keyword'=>$init->keyword,
  'title'=>$init->name.' - '.$init->description,
  'fanpage'=>$siteINI->site['fanpage'],
  'footer'=>$siteINI->site['footer'],
  /* constants */
  'contact'=>(object)$siteINI->contact,
  'accounts'=>$siteINI->accounts,
  'externals'=>$siteINI->externals,
  /* sliders */
  'slider'=>array_values($siteINI->slider),
  'slider_home'=>agenGetSliderHome(9),
  /* automatic */
  'current'=>site::ruri,
  'post'=>false,
  'product'=>false,
  'tag'=>false,
  'error'=>false,
  /* generated */
  'menu'=>get::menus('top'),
  'total_visitor'=>get::visitors(),
  'products'=>get::products(9),
  'blogs'=>get::posts(2),
  'tags'=>get::tags(),
  /* others */
  'downloads'=>[
    ['#/buku-panduan.pdf','Panduan'],
    ['#/katalog.pdf','Katalog'],
  ],
  'temp'=>'',
];

/* check request post */
if(isset($_GET['post_id'])){
  $post=site::post((int)$_GET['post_id']);
  if(is_array($post)){
    $site->post=(object)$post;
    $site->title=$site->post->title.' - '.$site->name;
  }else{
    $site->error='Post is not found';
    $site->title=$site->error.' - '.$site->name;
  }
}

/* check request product */
if(isset($_GET['product_id'])){
  $product=site::product((int)$_GET['product_id']);
  if(is_array($product)){
    $site->product=(object)$product;
    $site->title=$site->product->name.' - '.$site->name;
  }else{
    $site->error='Product is not found';
    $site->title=$site->error.' - '.$site->name;
  }
}

/* check request tag */
if(isset($_GET['tag'])){
  $tags=get::tagData($_GET['tag']);
  if(is_array($tags)){
    $site->tag=$tags;
    $site->title='Tag: '.basename($_GET['tag']).' - '.$site->name;
  }else{
    $site->error='Tag is not found';
    $site->title=$site->error.' - '.$site->name;
  }
}

/* generate blog post */
function agenGeneratePostBlog($post=null){
  if(!is_object($post)){return false;}
  $date='<p><em class="tanggal">'.date('l, F jS Y',strtotime($post->datetime)).'</em></p>';
  $picture=$post->picture?'<img src="'.$post->picture.'" '
    .' style="max-width:150px;max-height:150px;float:right;margin:0px 0px 10px 10px;'
    .'border:1px solid #bbb;padding:2px;" />':'';
  $tags=get::tagTID((int)$post->id,'product');
  $tag='';
  if($tags){
    array_walk($tags,function(&$v){
      $v='<a href="?tag='.$v['type'].'/'.$v['tag'].'" title="'.$v['name'].'">'.$v['name'].'</a>';
    });
    $tag='<div>Tags: '.implode(', ',(array)$tags).'</div>';
  }return $date.$picture.'<div>'.$post->content.'</div>'.$tag.'';
}

/* generate tags post */
function agenGenerateTagPost($tags=null){
  if(!is_array($tags)||!isset($_GET['tag'])){return false;}
  $res=[];$type=dirname($_GET['tag']);
  $limit=9;$count=0;
  foreach($tags as $tag){
    if($count>=$limit){break;}
    $count++;
    if($type=='product'){
      $tag['url']=site::url.'?product_id='.$tag['id'];
      $res[]=agenGenerateProduct((object)$tag);
    }elseif($type=='post'){
      $tag['url']=site::url.'?post_id='.$tag['id'];
      $res[]=agenGeneratePost((object)$tag);
    }
  }$result='';
  if($type=='post'){
    $result='<div class="boxer"><div class="list-blog"><ul>'.implode($res).'</ul></div></div>';
  }elseif($type=='product'){
    $result='<div class="boxer"><div class="row">'.implode($res).'</div></div>';
  }return $result;
}

/* generate product post */
function agenGenerateProductPost($pro=null){
  if(!is_object($pro)){return false;}
  /* best (Best Seller) or inden (New) or false */
  $ribt=['inden'=>'New','best'=>'Best Seller'];
  $ribbon=$pro->ribbon?'<div class="ribbon '.$pro->ribbon.'"><span>'
    .$ribt[$pro->ribbon].'</span></div>':'';
  $percent='';
  $discount=false;
  if($pro->discount){
    $discount=intval($pro->price)-intval($pro->discount);
    $percent='<span class="diskon">('.floor($pro->discount/$pro->price*100).'%)</span>';
  }
  $picture='<div style="text-align:center;">'.($pro->picture?'<img src="'.$pro->picture.'" style="max-width:100%;max-height:300px;border:1px solid #bbb;padding:2px;" />':'').'</div>';
  $price='<div style="text-align:center;">'.$pro->currency.' '
    .number_format($discount?$discount:$pro->price,0)
    .($discount?' <span class="coret">'.number_format($pro->price).'</span> '.$percent:'')
    .'</div>';
  $tags=get::tagTID((int)$pro->id,'product');
  $tag='';
  if($tags){
    array_walk($tags,function(&$v){
      $v='<a href="?tag='.$v['type'].'/'.$v['tag'].'" title="'.$v['name'].'">'.$v['name'].'</a>';
    });
    $tag='<div>Tags: '.implode(', ',(array)$tags).'</div>';
  }return $ribbon.$picture.$price.$pro->description.$tag;
}

/* generate product from object */
function agenGenerateProduct($pro=null){
  if(!is_object($pro)){return false;}
  /* best (Best Seller) or inden (New) or false */
  $ribt=['inden'=>'New','best'=>'Best Seller'];
  $ribbon=$pro->ribbon?'<div class="ribbon '.$pro->ribbon.'"><span>'
    .$ribt[$pro->ribbon].'</span></div>':'';
  $percent='';
  $discount=false;
  if($pro->discount){
    $discount=intval($pro->price)-intval($pro->discount);
    $percent='<div class="diskon"><p class="jumlah color">'
      .floor($pro->discount/$pro->price*100).'%</p></div>';
  }
  return '<div class="col-md-4 col-sm-6 ikibro"><div class="agen-produk">'.$ribbon
    .'<div class="agen-gambar-center"><div class="agen-gambar">'
    .'<a href="'.$pro->url.'" title="'.$pro->name.'">'
    .'<img class="lazy" src="themes/agen/images/asli.gif" '
      .'data-original="'.$pro->picture.'" alt="" width="auto" height="auto">'
    .'<a/>'.$percent
    .'</div></div>'
    .'<div class="agen-title-produk">'
    .'<a href="'.$pro->url.'" title="'.$pro->name.'">'.$pro->name.'</a>'
    .'</div>'
    .'<div class="agen-harga">'.$pro->currency.' '
      .number_format($discount?$discount:$pro->price,0)
      .($discount?' <span class="coret">'.number_format($pro->price).'</span>':'')
    .'</div>'
    .'<div class="tombol">'
    .'<a data-toggle="modal" data-placement="top" rel="tooltip" title="" href="#dialog-'
      .$pro->id.'" class="small beli blue" data-original-title="Beli">'
      .'<span class="glyphicon glyphicon-shopping-cart"></span></a>'
    .'<a href="'.$pro->url.'" data-placement="top" rel="tooltip" title="" '
      .'class="small beli blue" data-original-title="Detail">'
      .'<span class="glyphicon glyphicon-info-sign"></span></a>'
    .'</div>'
    .'<div class="modal fade" id="dialog-'.$pro->id.'" tabindex="-1" role="dialog" '
      .'aria-labelledby="myModalLabel" aria-hidden="true">'
    .'<div class="modal-dialog"><div class="modal-content">'
    .'<div class="modal-header">'
    .'<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'
    .'<h4 class="modal-title"><span class="glyphicon glyphicon-shopping-cart"></span> '
    .$pro->dialog[0].'</h4>'
    .'</div>'
    .'<div class="modal-body">'.$pro->dialog[1].'</div>'
    .'<div class="modal-footer">'
    .'<a class="medium beli blue" href="'.$pro->url.'" title="'.$pro->name.'">'
    .'Detail <span class="glyphicon glyphicon-chevron-right"></span></a>'
    .'</div>'
    .'</div></div>'
    .'</div>'
    .'</div></div>';
}

/* generate post */
function agenGeneratePost($post=null){
  if(!is_object($post)){return false;}
  return '<li>'
    .'<div class="thumb"><a href="'.$post->url.'" title="'.$post->title.'">'
    .'<img class="lazy" src="themes/agen/images/asli.gif" data-original="'.$post->picture
      .'" alt="" width="auto" height="auto">'
    .'</a></div>'
    .'<div class="text"><h4><a href="'.$post->url.'">'.$post->title.'</a></h4>'
    .'<small>'.date('l, F jS Y',$post->time).'</small>'
    .'<p>'.substr(strip_tags($post->content),0,225)
    .(strlen(strip_tags($post->content))>225?' ...':'').'</p>'
    .'</div>'
    .'</li>';
}

/* agen slider_home */
function agenGetSliderHome($limit=9){
  $prod=site::product(null,$error);
  if($error){return [];}
  $x=[];$count=0;
  foreach($prod as $r){
    $count++;
    $ribbon=$r['ribbon']=='inden'?'New':($r['ribbon']=='best'?'Best Seller':'');
    $x[]=[
      site::url.'?product_id='.$r['id'],
      $r['name'],$r['picture']
    ];
    if($count>=$limit){break;}
  }return $x;
}


