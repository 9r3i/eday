<div class="index">
<div class="form-row">
  <input type="text" class="input" name="title" placeholder="Insert Title" />
</div>
<div class="form-row">
  <textarea id="editor" class="textarea" name="content" placeholder="Insert Content"></textarea>
</div>
<div class="form-row">
  <input type="file" class="input" name="picture" id="input-picture" />
  <div id="preview" class="preview"></div>
</div>
<div class="form-row">
  <div id="tag-preview" class="tag-preview"></div>
  <input type="text" class="input" name="tag" id="tag-input" placeholder="Insert Tags" />
</div>
<div class="form-row" style="margin:20px 5px;text-align:center;">
  <a href="javascript:postSubmitAdd()" class="submit submit-blue"><i class="fa fa-save"></i> Save Post</a>
</div>
</div>
<script type="text/javascript" src="<?=admin::editorPath()?>"></script>
<script type="text/javascript" src="<?=admin::themeURL('js/post.js')?>"></script>
<script type="text/javascript" src="<?=admin::themeURL('js/tag.js')?>"></script>
<script type="text/javascript">
var POST_ACTION_URL=SITE_URL+'?admin=post/ajax';
loadEditor('editor');
postPicturePreview('input-picture');
tagEventAdd('tag-input','tag-preview','1000000000','post');
tagEventGet('tag-preview','1000000000','post');
</script>


