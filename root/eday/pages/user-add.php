<div class="index">
<?php
if(EDAY_ADMIN_TYPE=='user'){
  echo '<div class="post-error">Error: Have no access to this page.</div>';
  goto endOfUserAdd;
}
/* use namespace eday */
use eday\admin;
?>
<table width="100%" class="form-table"><tbody>
  <tr><td class="form-key">Username</td><td>
    <input type="text" class="input" name="username" placeholder="Insert Username" />
  </td></tr>
  <tr><td class="form-key">New Password</td><td>
    <input type="password" class="input" name="password" placeholder="Insert New Password" />    
  </td></tr>
  <tr><td class="form-key">Confirm Password</td><td>
    <input type="password" class="input" name="cpassword" placeholder="Insert Confirm Password" />    
  </td></tr>
  <tr><td class="form-key"></td><td style="padding:20px 10px;">
    <a href="javascript:userSubmitAdd()" class="submit submit-blue"><i class="fa fa-save"></i> Save User</a>
  </td></tr>
</tbody></table>
</div>
<script type="text/javascript" src="<?=admin::appURL('js/user.js')?>"></script>
<script type="text/javascript">
var USER_ACTION_URL=SITE_URL+'?'+SITE_ADMIN_KEY+'=user/ajax';
</script>
<?php endOfUserAdd: ?>


