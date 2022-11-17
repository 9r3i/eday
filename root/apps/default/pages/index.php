<?php
/* footer for default application
 * started at september 4th 2018
 */

/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain');
  exit('Error: This application requires e-Day engine.');  
}

/* use namespace */
use eday\get;
use eday\base;
use eday\admin;
use eday\site;

/* global site */
global $site;

/* get products */
$limit=9;
$products=get::products($limit);

?>
<div class="products">
<?php foreach($products as $product){
  $product_image='<img src="'.$product['picture'].'" alt="" />';
  $product_ribbon=$product['ribbon']=='inden'
    ?'<div class="product-row-ribbon product-row-new">New</div>'
    :($product['ribbon']=='best'
      ?'<div class="product-row-ribbon product-row-best">Best Seller</div>':'');
  $percent=false;
  if($product['discount']){
    $discount=intval($product['price'])-intval($product['discount']);
    $percent=floor(intval($product['discount'])/intval($product['price'])*100);
    $percent='<div class="product-row-percent" title="Discount '.$percent.'%">'.$percent.'%</p></div>';
  }
  $price=$product['currency'].' '.base::price($product['price']);
  if($percent){
    $price=$product['currency'].' '.base::price($discount).' ';
    $price.='<span class="product-row-discount">'.base::price($product['price']).'</span>';
  }
  $edit=admin::isLogin()?'<a href="'.site::url.'?admin=product/edit/'.$product['id']
    .'" class="product-button-green"><i class="fa fa-edit"></i></a>':'';
?>
<div class="product-row"><?=$product_ribbon?><?=$percent?>
  <div class="product-row-image"><?=$product_image?></div>
  <div class="product-row-name"><?=$product['name']?></div>
  <div class="product-row-detail"><?=$price?></div>
  <div class="product-row-option"><a href="<?=$product['url']?>" class="product-button-blue"><i class="fa fa-share-square-o"></i></a> <?=$edit?></div>
</div>
<?php } ?>
</div>
<?php

/* testing script *
echo '<pre>'.site::ruri.'</pre>';
echo '<pre>';
print_r(site::defined());
echo '</pre>';
//*/



/* testing script */
//header('content-type:text/plain');print_r($site);exit;
