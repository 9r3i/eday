<div class="index">
<?php
/* use namespace eday */
use eday\admin;
global $user,$error;
if($error){
  echo '<div class="post-error">Error: '.$error.'</div>';
  goto endOfUserEdit;
}elseif(EDAY_ADMIN_TYPE!=='master'&&EDAY_ADMIN_ID!==$user['id']){
  echo '<div class="post-error">Error: Have no right to edit a user.</div>';
  goto endOfUserEdit;
}
?>
<table width="100%" class="form-table"><tbody>
  <tr><td class="form-key">Username</td><td>
    <input type="text" class="input" name="username" placeholder="Insert Username" value="<?=htmlspecialchars($user['username'])?>" />
    <input type="hidden" name="id" value="<?=($user['id'])?>" />
  </td></tr>
  <tr><td class="form-key">New Password</td><td>
    <input type="password" class="input" name="password" placeholder="Insert New Password" />    
  </td></tr>
  <tr><td class="form-key">Confirm Password</td><td>
    <input type="password" class="input" name="cpassword" placeholder="Insert Confirm Password" />    
  </td></tr>
  <tr><td class="form-key"></td><td style="padding:20px 10px;">
    <a href="javascript:userSubmitSave()" class="submit submit-blue"><i class="fa fa-save"></i> Save User</a>
  </td></tr>
</tbody></table>
</div>
<script type="text/javascript" src="<?=admin::appURL('js/user.js')?>"></script>
<script type="text/javascript">
var USER_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=user/ajax';
var USER_ID=EDAY_ADMIN_ID;
var USER_IS_USER=EDAY_ADMIN_TYPE=='user'?true:false;
</script>
<?php endOfUserEdit: ?>


