<div class="index">
<?php
global $token;
$timezone='';
foreach(get::timezones() as $k=>$v){
  $selected=$k=='Asia/Jakarta'?'selected="selected"':'';
  $timezone.='<option value="'.$k.'" '.$selected.'>'.htmlspecialchars($v).'</option>';
}
?>
<table width="100%" class="settings-table"><tbody>
  <tr><td class="setting-key"></td><td><strong>User Setup</strong></td></tr>
  <tr><td class="setting-key">Username</td><td>
    <input type="text" class="input" name="username" placeholder="Insert Username" />
  </td></tr>
  <tr><td class="setting-key">New Password</td><td>
    <input type="password" class="input" name="password" placeholder="Insert New Password" />
  </td></tr>
  <tr><td class="setting-key">Confirm Password</td><td>
    <input type="password" class="input" name="cpassword" placeholder="Insert Confirm Password" />
  </td></tr>
  <tr><td class="setting-key"></td><td><strong>Site Setup</strong></td></tr>
  <tr><td class="setting-key">Site Name</td><td>
    <input type="text" class="input" name="name" placeholder="Insert Site Name" value="" />
  </td></tr>
  <tr><td class="setting-key">Site Description</td><td>
    <input type="text" class="input" name="description" placeholder="Insert Site Description" value="" />
  </td></tr>
  <tr><td class="setting-key">Site Keyword</td><td>
    <input type="text" class="input" name="keyword" placeholder="Insert Site Keywords" value="" />
  </td></tr>
  <tr><td class="setting-key">Timezone</td><td>
    <select class="select" name="timezone"><?=$timezone?></select>
  </td></tr>
  <tr><td class="setting-key"></td><td style="padding:20px 10px;">
    <a href="javascript:void(0)" class="submit submit-blue" id="save-button"><i class="fa fa-save"></i> Save</a>
  </td></tr>
</tbody></table>
</div>
<script type="text/javascript">
var setup_token='<?=$token?>';
var save_button=gebi('save-button');
if(save_button){save_button.addEventListener('click',function(e){
  save_button.disabled=true;
  save_button.innerHTML='<i class="fa fa-pulse fa-spinner"></i> Saving...';
  W.post(SITE_URL+'?admin=log/save/setup',function(r){
    save_button.innerHTML='<i class="fa fa-save"></i> Save';
    if(r.toString().match(/^error/ig)){
      save_button.disabled=false;
      return error(r.toString());
    }else if(r=='OK'){
      return success('Saved.',function(y){
        W.location.assign(SITE_URL+'?admin=log/in');
      });
    }console.log(r);
    save_button.disabled=false;
    return error('Some thing was going wrong.');
  },{
    username:qs('input[name="username"]').value,
    password:qs('input[name="password"]').value,
    cpassword:qs('input[name="cpassword"]').value,
    name:qs('input[name="name"]').value,
    description:qs('input[name="description"]').value,
    keyword:qs('input[name="keyword"]').value,
    timezone:qs('select[name="timezone"]').value,
    token:setup_token,
  },null,null,null,null,function(e){
    save_button.disabled=false;
    save_button.innerHTML='<i class="fa fa-save"></i> Save';
    return error(e);
  });
},false);}
</script>
<script type="text/javascript" src="<?=admin::themeURL('js/footer.js')?>" async="true"></script>
</body></html>


