<div class="index product-index">
<?php
global $products,$error,$row,$next,$limit;
if($error||!is_array($products)){
  echo '<div class="product-error">Error: '.$error.'</div>';
  $products=[];
  goto endOfProductAll;
}
echo '<div class="post-row">Total products: '.$row.'</div>';
foreach($products as $product){
  $product_image='<img src="'.$product['picture'].'" alt="" />';
  $product_ribbon=$product['ribbon']=='inden'?'<div class="product-row-ribbon product-row-new">New</div>'
    :($product['ribbon']=='best'?'<div class="product-row-ribbon product-row-best">Best Seller</div>':'');
  echo '<div class="product-row">'.$product_ribbon
    .'<div class="product-row-image">'.$product_image.'</div>'
    .'<div class="product-row-name">'.$product['name'].'</div>'
    .'<div class="product-row-detail">Price: '.base::price($product['price'])
      .' '.$product['currency'].'.<br />'
      .'Discount: '.base::price($product['discount']).' '.$product['currency']
      .'</div>'
    .'<div class="product-row-option">'
    .'<a href="'.site::url.'?product_id='.$product['id'].'" target="_blank" class="submit-blue">'
      .'<i class="fa fa-share-square-o"></i> View</a> '
    .'<a href="'.site::url.'?admin=product/edit/'.$product['id'].'" class="submit-green">'
      .'<i class="fa fa-edit"></i> Edit</a> '
    .'<a href="javascript:productDeleteID('.$product['id'].')" class="submit-red">'
      .'<i class="fa fa-trash"></i> Delete</a> '
    .'</div>'
    .'</div>';
}
if($next){
  echo '<div class="post-row" style="text-align:center;padding:20px;">'
    .'<a href="'.site::url.'?admin=product/all/'.$next.'/'.$limit.'" class="submit submit-blue">Next Posts</a>'
    .'</div>';
}
endOfProductAll:
?>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/product.js')?>"></script>
<script type="text/javascript">
var PRODUCT_ACTION_URL=SITE_URL+'?admin=product/ajax';
</script>


