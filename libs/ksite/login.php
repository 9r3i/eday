<?php
/* check ksite object and request */
if(!isset($this)||!is_object($this)
  ||!$this instanceof ksite
  ||!isset($_GET[$this->core->adminKey])){
  /* error statement */
  header('Content-Type: text/plain',true,401);
  exit('Error: Unauthorized.');
}

/* check login */
if(isset($_COOKIE['ksite-admin'])
  &&$_COOKIE['ksite-admin']===$this->user->token){
  header('Content-Type: text/plain',true,402);
  exit('Error: User is logged in.');
}
    
/* check request login */
$error=false;
if(isset($_POST['login'],$_POST['username'],$_POST['password'])){
  if($_POST['username']===$this->user->username
    &&password_verify($_POST['password'],$this->user->password)){
    setcookie('ksite-admin',$this->user->token,time()+(3600*24));
    header("Location: {$this->adminPath}");
    exit;
  }$error=true;
}

/* prepare error */
$error=$error?'<div class="login-error" id="login-error">Invalid username or password.</div>':'';

/* html */
?><!DOCTYPE html><html lang="en-US" dir="ltr"><head>
  <!-- http-equiv -->
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title>Login &#8213; ksiteAdmin</title>
  <!-- meta -->
  <meta name="description" content="Login Page" />
  <meta name="keywords" content="login" />
  <meta name="robots" content="no index, no follow" />
  <meta name="author" content="<?=$this->site->author?>" />
  <meta name="uri" content="<?=$this->site->uri?>" />
  <!-- style sheet -->
  <link rel="stylesheet" href="ksite/css/login.css" type="text/css" media="screen,print" />
  <!-- favicon -->
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head><body><div class="login">
<div class="login-header"></div>
<div class="login-body"><form action="<?=$this->adminPath?>" method="post">
  <?=$error?>
  <div class="login-input"><input type="text" name="username" placeholder="Username" /></div>
  <div class="login-input"><input type="password" name="password" placeholder="Password" /></div>
  <div class="login-input"><input type="submit" name="login" value="Login" /></div>
</form></div>
<div class="login-footer"></div>
<script type="text/javascript">
var loginError=document.getElementById('login-error');
if(loginError){setTimeout(function(){
  loginError.style.display='none';
},3000);}
</script>
</div></body></html>
