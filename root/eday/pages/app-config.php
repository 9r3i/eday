<div class="index">
<?php
/* use namespace eday */
use eday\admin;
global $config,$error,$namespace;
if(!is_array($config)||$error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfAppConfig;
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
    <input type="text" app="config" class="input" name="<?=$key?>" data-parent="<?=$type?>" placeholder="Insert <?=$key?>" value="<?=htmlspecialchars($value)?>" />
  </td></tr>
<?php
  }
}
?>
  <tr><td class="form-key"></td><td style="padding:20px 10px;">
    <a href="javascript:appConfigSave()" class="submit submit-blue"><i class="fa fa-save"></i> Save Config</a>
  </td></tr>
</tbody></table>
<?php
//echo '<pre>'.print_r($config,true).'</pre>';
?>
</div>
<script type="text/javascript" src="<?=admin::appURL('js/app.js')?>"></script>
<script type="text/javascript">
var APP_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=app/ajax';
var APP_NAMESPACE='<?=$namespace?>';
</script>
<?php
endOfAppConfig:


