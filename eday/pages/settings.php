<div class="index">
<?php
if(EDAY_ADMIN_TYPE=='user'){
  echo '<div class="post-error">Error: Have no access to this page.</div>';
  goto endOfSettings;
}
global $option;
$themes=unserialize(EDAY_THEMES);
$theme_option='';$timezone='';$api='';$dbapi='';
$op_api=['Disallow API Access','Allow API Access'];
$op_dbapi=['Disallow Database API Access','Allow Database API Access'];
foreach($themes as $k=>$theme){
  if($theme->namespace=='admin'){continue;}
  $selected=$theme->namespace==$option->theme?'selected="selected"':'';
  $description=$theme->description!==''?' ('.htmlspecialchars($theme->description).')':'';
  $version=$theme->version!==''?' '.$theme->version:'';
  $theme_option.='<option value="'.$theme->namespace.'" '.$selected.'>'
    .htmlspecialchars($theme->name).$version.$description.'</option>';
}
foreach(get::timezones() as $k=>$v){
  $selected=$k==$option->timezone?'selected="selected"':'';
  $timezone.='<option value="'.$k.'" '.$selected.'>'.htmlspecialchars($v).'</option>';
}
foreach($op_api as $k=>$v){
  $selected=$k==$option->api?'selected="selected"':'';
  $api.='<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
}
foreach($op_dbapi as $k=>$v){
  $selected=$k==$option->dbapi?'selected="selected"':'';
  $dbapi.='<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
}
?>
<table width="100%" class="settings-table"><tbody>
  <tr><td class="setting-key">Site Name</td><td>
    <input type="text" class="input" name="name" placeholder="Insert Site Name" value="<?=htmlspecialchars($option->name)?>" />
  </td></tr>
  <tr><td class="setting-key">Site Description</td><td>
    <input type="text" class="input" name="description" placeholder="Insert Site Description" value="<?=htmlspecialchars($option->description)?>" />    
  </td></tr>
  <tr><td class="setting-key">Site Keyword</td><td>
    <input type="text" class="input" name="keyword" placeholder="Insert Site Keyword" value="<?=htmlspecialchars($option->keyword)?>" />    
  </td></tr>
  <tr><td class="setting-key">Site Theme</td><td>
    <select class="select" name="theme"><?=$theme_option?></select>
  </td></tr>
  <tr><td class="setting-key">Timezone</td><td>
    <select class="select" name="timezone"><?=$timezone?></select>
  </td></tr>
  <tr><td class="setting-key">Site API</td><td>
    <select class="select" name="api"><?=$api?></select>
  </td></tr>
  <tr><td class="setting-key">Database API</td><td>
    <select class="select" name="dbapi"><?=$dbapi?></select>
  </td></tr>
  <tr><td class="setting-key"></td><td style="padding:20px 10px;">
    <a href="javascript:settingSubmitSave()" class="submit submit-blue"><i class="fa fa-save"></i> Save Settings</a>
  </td></tr>
</tbody></table>
</div>
<script type="text/javascript" src="<?=admin::themeURL('js/option.js')?>"></script>
<script type="text/javascript">
var OPTION_ACTION_URL=SITE_URL+'?admin=option/ajax';
</script>
<?php endOfSettings: ?>


