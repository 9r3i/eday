<?php
/* use namespace eday */
use eday\admin;
?><div class="index">
<table width="100%" class="form-table"><tbody>
  <tr><td class="form-key">Product Name</td><td>
    <input type="text" class="input" name="name" placeholder="Insert Product Name" />
  </td></tr>
  <tr><td colspan="2">
    <textarea class="textarea" name="description" id="editor" placeholder="Insert Product Description"></textarea>
  </td></tr>
  <tr><td class="form-key">Product Image</td><td>
    <input type="file" class="input" name="picture" id="input-picture" />
    <div id="preview" class="preview"></div>
  </td></tr>
  <tr><td class="form-key">Price</td><td>
    <input type="text" class="input" name="price" placeholder="Price" />
  </td></tr>
  <tr><td class="form-key">Currency</td><td>
    <input type="text" class="input" name="currency" placeholder="Currency" />
  </td></tr>
  <tr><td class="form-key">Discount</td><td>
    <input type="text" class="input" name="discount" placeholder="Discount" />
  </td></tr>
  <tr><td class="form-key">Ribbon</td><td>
    <select class="select" name="ribbon">
      <option value="">[No Ribbon]</option>
      <option value="inden" selected="selected">New</option>
      <option value="best">Best Seller</option>
    </select>
  </td></tr>
  <tr><td class="form-key">Order Info</td><td>
    <input type="text" class="input" name="order_to" placeholder="Insert Order Info" />
  </td></tr>
  <tr><td class="form-key">Product Tags</td><td>
    <div id="tag-preview" class="tag-preview"></div>
    <input type="text" class="input" name="tag" id="tag-input" placeholder="Insert Tags" />
  </td></tr>
  <tr><td class="form-key"></td><td style="padding:20px 10px;">
    <a href="javascript:productSubmitAdd()" class="submit submit-blue"><i class="fa fa-save"></i> Save Product</a>
  </td></tr>
</tbody></table>
</div>
<script type="text/javascript" src="<?=admin::editorPath()?>"></script>
<script type="text/javascript" src="<?=admin::appURL('js/product.js')?>"></script>
<script type="text/javascript" src="<?=admin::appURL('js/tag.js')?>"></script>
<script type="text/javascript">
var PRODUCT_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=product/ajax';
productPicturePreview('input-picture');
loadEditor('editor');
tagEventAdd('tag-input','tag-preview','1000000000','product');
tagEventGet('tag-preview','1000000000','product');
</script>


