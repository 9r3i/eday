<div class="index">
<?php
/* use namespace eday */
use eday\admin;
global $product,$error;
if($error){
  echo '<div class="product-error">Error: '.$error.'</div>';
  goto endOfProductEdit;
}
$selected_no=$product['ribbon']==''?'selected="selected"':'';
$selected_new=$product['ribbon']=='inden'?'selected="selected"':'';
$selected_best=$product['ribbon']=='best'?'selected="selected"':'';
?>
<table width="100%" class="form-table"><tbody>
  <tr><td class="form-key">Product Name</td><td>
    <input type="text" class="input" name="name" placeholder="Insert Product Name" value="<?=htmlspecialchars($product['name'])?>" />
  </td></tr>
  <tr><td class="form-key">Description</td><td>
    <textarea class="textarea" name="description" id="editor" placeholder="Insert Product Description"><?=htmlspecialchars($product['description'])?></textarea>
  </td></tr>
  <tr><td class="form-key">Product Image</td><td>
    <input type="file" class="input" name="picture" id="input-picture" />
    <div id="preview" class="preview"></div>
  </td></tr>
  <tr><td class="form-key">Price</td><td>
    <input type="text" class="input" name="price" placeholder="Price" value="<?=htmlspecialchars($product['price'])?>" />
  </td></tr>
  <tr><td class="form-key">Currency</td><td>
    <input type="text" class="input" name="currency" placeholder="Currency" value="<?=htmlspecialchars($product['currency'])?>" />
  </td></tr>
  <tr><td class="form-key">Discount</td><td>
    <input type="text" class="input" name="discount" placeholder="Discount" value="<?=htmlspecialchars($product['discount'])?>" />
  </td></tr>
  <tr><td class="form-key">Ribbon</td><td>
    <select class="select" name="ribbon">
      <option value="" <?=$selected_no?>>[No Ribbon]</option>
      <option value="inden" <?=$selected_new?>>New</option>
      <option value="best" <?=$selected_best?>>Best Seller</option>
    </select>
  </td></tr>
  <tr><td class="form-key">Order Info</td><td>
    <input type="text" class="input" name="order_to" placeholder="Insert Order Info" value="<?=htmlspecialchars($product['order_to'])?>" />
  </td></tr>
  <tr><td class="form-key">Product Tags</td><td>
    <div id="tag-preview" class="tag-preview"></div>
    <input type="text" class="input" name="tag" id="tag-input" placeholder="Insert Tags" />
  </td></tr>
  <tr><td class="form-key"></td><td style="padding:20px 10px;">
    <a href="javascript:productSubmitSave()" class="submit submit-blue"><i class="fa fa-save"></i> Save Product</a>
  </td></tr>
</tbody></table>
</div>
<script type="text/javascript" src="<?=admin::editorPath()?>"></script>
<script type="text/javascript" src="<?=admin::appURL('js/product.js')?>"></script>
<script type="text/javascript" src="<?=admin::appURL('js/tag.js')?>"></script>
<script type="text/javascript">
var PRODUCT_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=product/ajax';
var PRODUCT_ERROR=<?=$error?'"Error: '.$error.'"':'false'?>;
var PRODUCT_PICTURE="<?=$product['picture']?>";
if(!PRODUCT_ERROR){
  var PRODUCT_ID=<?=$product['id']?>;
  productPicturePreview('input-picture');
  productPicturePreviewEdit('input-picture');
  loadEditor('editor');
  tagEventAdd('tag-input','tag-preview',PRODUCT_ID,'product');
  tagEventGet('tag-preview',PRODUCT_ID,'product');
}
</script>
<?php endOfProductEdit: ?>


