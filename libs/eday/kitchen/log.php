<?php
/* class log for e-Day admin
 * started at august 25th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
use eday\site;
class log{
  const version='1.1.0';
  public function save(){
    if(admin::isLogin()){
      return self::error('Has been logged in.');
    }
    if(!self::isRootUser()){
      return self::error('Site has been setup.');
    }
    if(!isset($_POST['username'],$_POST['password'],$_POST['cpassword'],$_POST['name'],$_POST['description'],$_POST['keyword'],$_POST['timezone'],$_POST['token'])){
      return self::error('Request requirement is not valid.');
    }$token=sha1(self::isRootUser());
    if($_POST['token']!==$token){
      return self::error('Invalid setup token.');
    }
    if(!preg_match('/^[a-z0-9]+$/',$_POST['username'])){
      return self::error('Invalid username.'."\r\n".'Username must be lower case alpha-numeric.');
    }
    if($_POST['username']=='root'){
      return self::error('Invalid username.'."\r\n".'Please, use another username.');
    }
    if(empty($_POST['password'])){
      return self::error('Empty password.');
    }
    if($_POST['password']!==$_POST['cpassword']){
      return self::error('Password and confirm password are not equal.');
    }
    if(!@date_default_timezone_set($_POST['timezone'])){
      return self::error('Invalid timezone.');
    }
    $db=site::db();
    $user='update users username=string('.$_POST['username'].')'
      .'&password=string('.password_hash($_POST['password'],PASSWORD_BCRYPT).') where id=1';
    $site='update site name=string:'.$_POST['name']
      .'&description=string:'.$_POST['description']
      .'&keyword=string:'.$_POST['keyword']
      .'&timezone=string:'.$_POST['timezone']
      .' where id=1';
    $db->query($user);
    if($db->error){return self::error($db->error);}
    $db->query($site);
    if($db->error){return self::error($db->error);}
    return self::result();
  }
  public function setup(){
    if(admin::isLogin()){
      return admin::redirect('dashboard/home');
    }
    if(!self::isRootUser()){
      return admin::redirect('log/in');
    }
    global $title,$token;
    $token=sha1(self::isRootUser());
    $title='Setup';
    admin::html('header');
    admin::html('log-setup');
    return true;
  }
  public function ajax($type=null){
    if(admin::isLogin()){
      return self::error('Has been logged in.');
    }
    $error='Invalid username or password.';
    if(!isset($_POST['username'],$_POST['password'])
      ||!preg_match('/^[a-z0-9]+$/',$_POST['username'])
      ||empty($_POST['username'])
      ||empty($_POST['password'])){
      return self::error($error);
    }
    $db=site::db();
    $sel=$db->query('select * from users where username="'.$_POST['username'].'"');
    if(!$sel||!isset($sel[0])||!password_verify($_POST['password'],$sel[0]['password'])){
      return self::error($error);
    }
    $data=$sel[0];
    $token=admin::token();
    $ins=$db->query('insert into logs username=string('.$data['username'].')&token=string('.$token.')');
    if(!$ins||$db->error){
      $error=$db->error?$db->error:'Failed to login.';
      return self::error($error);
    }
    @setcookie('eday-token',$token,time()+60*60*24*30);
    return self::result();
  }
  public function submit(){
    if(admin::isLogin()){
      return admin::redirect('dashboard/home');
    }
    $error=urlencode('Invalid username or password.');
    if(!isset($_POST['username'],$_POST['password'])
      ||!preg_match('/^[a-z0-9]+$/',$_POST['username'])
      ||empty($_POST['username'])
      ||empty($_POST['password'])){
      return admin::redirect('log/in/error/'.$error);
    }
    $db=site::db();
    $sel=$db->query('select * from users where username="'.$_POST['username'].'"');
    if(!$sel||!isset($sel[0])||!password_verify($_POST['password'],$sel[0]['password'])){
      return admin::redirect('log/in/error/'.$error);
    }
    $data=$sel[0];
    $token=admin::token();
    $ins=$db->query('insert into logs username=string('.$data['username'].')&token=string('.$token.')');
    if(!$ins||$db->error){
      $error=$db->error?$db->error:'Failed to login.';
      return admin::redirect('log/in/error/'.urlencode($error));
    }
    @setcookie('eday-token',$token,time()+60*60*24*30);
    return admin::redirect('dashboard/home');
  }
  public function in($type='',$message=''){
    if(admin::isLogin()){
      return admin::redirect('dashboard/home');
    }
    if(self::isRootUser()){
      return admin::redirect('log/setup');
    }
    global $title,$error;
    $title='Login';
    $error=$type=='error'&&$message?$message:false;
    admin::html('header');
    admin::html('login');
    return true;
  }
  public function out(){
    if(isset($_COOKIE['eday-token'])){
      $db=site::db();
      $del=$db->query('delete from logs where token="'.$_COOKIE['eday-token'].'"');
      @setcookie('eday-token','',time()-10);
    }return admin::redirect('log/in');
  }
  private static function isRootUser(){
    return defined('EDAY_SETUP')&&EDAY_SETUP?false:true;
  }
  private static function error($s=null){
    $s=is_string($s)?$s:'Unknown error.';
    return self::result('Error: '.$s);
  }
  private static function result($s=null){
    $s=is_string($s)?$s:'OK';
    header('Content-Type: text/plain');
    header('Content-Length: '.strlen($s));
    header('HTTP/1.1 200 OK');
    exit($s);
  }
}
