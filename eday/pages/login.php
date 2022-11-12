<?php
global $error;
$error_message=$error?'<div class="login-error" id="error-message">Error: '.$error.'</div>':'';
?>
<div class="login-form">
<div class="login-header"></div>
<?=$error_message?>
<table border="0px" width="100%" cellpadding="0px" cellspacing="0px"><tbody>
<tr><td>Username</td><td>
<input class="input" name="username" type="text" placeholder="Username" />
</td></tr>
<tr><td>Password</td><td>
<input class="input" name="password" type="password" placeholder="Password" />
</td></tr>
<tr><td></td><td style="padding:15px 5px;">
<a href="javascript:void(0)" class="submit submit-blue" id="log-button"><i class="fa fa-sign-in"></i> Login</a>
</td></tr>
</tbody></table>
<div class="login-footer" title="Powered by 9r3i"></div>
</div>
<script type="text/javascript">
/* error message */
W.mess=gebi('error-message');
if(W.mess){
  setTimeout(function(){
    W.mess.parentElement.removeChild(mess);
  },3700);
}
/* --- login submit --- */
var log_button=gebi('log-button');
var log_username=qs('input[name="username"]');
var log_password=qs('input[name="password"]');
if(log_button&&log_username&&log_password){
  log_button.addEventListener('click',function(e){
    return login(log_button,log_username,log_password);
  },false);
  log_username.addEventListener('keyup',function(e){
    if(e.keyCode===13){return login(log_button,log_username,log_password);}
  },false);
  log_password.addEventListener('keyup',function(e){
    if(e.keyCode===13){return login(log_button,log_username,log_password);}
  },false);
}
function login(log_button,log_username,log_password){
  log_button.disabled=true;
  log_button.innerHTML='<i class="fa fa-pulse fa-spinner"></i> Checking...';
  log_button.blur();
  log_username.blur();
  log_password.blur();
  W.post(SITE_URL+'?admin=log/ajax/submit',function(r){
    log_button.innerHTML='<i class="fa fa-sign-in"></i> Login';
    if(r.toString().match(/^error/ig)){
      log_button.disabled=false;
      return error(r.toString());
    }else if(r=='OK'){
      return success('Logged in.',function(y){
        W.location.assign(SITE_URL+'?admin=dashboard/home');
      });
    }console.log(r);
    log_button.disabled=false;
    return error('Some thing was going wrong.');
  },{
    username:log_username.value,
    password:log_password.value
  },null,null,null,null,function(e){
    log_button.disabled=false;
    log_button.innerHTML='<i class="fa fa-sign-in"></i> Login';
    return error(e);
  });
}
/* footer click */
W.power=qs('div.login-footer');
if(W.power){W.power.onclick=function(){
  W.open('https://github.com/9r3i','_blank');
};}
</script>
<script type="text/javascript" src="<?=admin::themeURL('js/footer.js')?>" async="true"></script>
</body></html>


