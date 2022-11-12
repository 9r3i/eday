<div class="index">
<?php
global $post,$error;
if($post&&!$error){
?>
<div class="form-row">
  <input type="text" class="input" name="title" placeholder="Insert Title" value="<?=htmlspecialchars($post['title'])?>" />
</div>
<div class="form-row">
  <textarea id="editor" class="textarea" name="content" placeholder="Insert Content">
    <?=htmlspecialchars($post['content'])?>
  </textarea>
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
  <a href="javascript:postSubmitSave()" class="submit submit-blue"><i class="fa fa-save"></i> Save Post</a>
</div>
<?php 
}else{
  echo '<div class="post-error">Error: '.$error.'</div>';
}
?>
</div>
<script type="text/javascript" src="<?=admin::editorPath()?>"></script>
<script type="text/javascript" src="<?=admin::themeURL('js/post.js')?>"></script>
<script type="text/javascript" src="<?=admin::themeURL('js/tag.js')?>"></script>
<script type="text/javascript">
var POST_ACTION_URL=SITE_URL+'?admin=post/ajax';
var POST_ERROR=<?=$error?'"Error: '.$error.'"':'false'?>;
var POST_PICTURE="<?=$post['picture']?>";
if(!POST_ERROR){
  var POST_ID=<?=$post['id']?>;
  loadEditor('editor');
  postPicturePreview('input-picture');
  postPicturePreviewEdit('input-picture');
  tagEventAdd('tag-input','tag-preview',POST_ID,'post');
  tagEventGet('tag-preview',POST_ID,'post');
}
</script>
