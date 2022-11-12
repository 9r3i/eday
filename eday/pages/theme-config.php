<div class="index">
<?php
global $config,$error,$namespace;
if(!is_array($config)||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfThemeConfig;
}
?>
<table width="100%" class="form-table"><tbody>
<?php
foreach($config as $type=>$data){
?><tr><td class="form-key"></td><td><strong>[<?=$type?>]</strong></td></tr>
<?php
  foreach($data as $key=>$value){
?>
  <tr><td class="form-key"><?=$key?></td><td>
    <input type="text" theme="config" class="input" name="<?=$key?>" data-parent="<?=$type?>" placeholder="Insert <?=$key?>" value="<?=htmlspecialchars($value)?>" />
  </td></tr>
<?php
  }
}
?>
  <tr><td class="form-key"></td><td style="padding:20px 10px;">
    <a href="javascript:themeConfigSave()" class="submit submit-blue"><i class="fa fa-save"></i> Save Config</a>
  </td></tr>
</tbody></table>
<?php
//echo '<pre>'.print_r($config,true).'</pre>';
?>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/theme.js')?>"></script>
<script type="text/javascript">
var THEME_ACTION_URL=SITE_URL+'?admin=theme/ajax';
var THEME_NAMESPACE='<?=$namespace?>';
</script>
<?php
endOfThemeConfig:


