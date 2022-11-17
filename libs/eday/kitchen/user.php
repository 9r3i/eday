<?php
/* class user for e-Day admin
 * started at august 27th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
use eday\site;
class user{
  const version='1.1.0';
  public function add(){
    global $title;
    $title='Add User';
    admin::html('header');
    admin::html('menu');
    admin::html('user-add');
    admin::html('footer');
    return true;
  }
  public function edit($id=false){
    global $title,$user,$error;
    if(!preg_match('/^\d+$/',$id)){
      $error='Invalid user ID.';
    }else{
      $user=site::user((int)$id,$err);
      $error=$err;
    }
    $title='Edit User';
    admin::html('header');
    admin::html('menu');
    admin::html('user-edit');
    admin::html('footer');
    return true;
  }
  public function all($p=1,$l=10){
    global $title,$users,$error,$row,$next,$limit;
    $s=(int)$p>1?((int)$p-1)*$l:0;
    $users=site::user(null,$err,$s,(int)$l);
    $error=$err;
    $row=site::userRow($err);
    $error=!$error?$err:$error;
    $next=false;$limit=$l;
    if($row&&$row>$p*$l){$next=$p+1;}
    $title='All Users';
    admin::html('header');
    admin::html('menu');
    admin::html('user-all');
    admin::html('footer');
    return true;
  }
  public function ajax(){
    $valid=['deleteUser','makeAdmin','saveUser','addUser'];
    if(!isset($_POST['request'])
      ||!in_array($_POST['request'],$valid)
      ||!method_exists($this,$_POST['request'])){
      return self::error('Invalid request.');
    }return @\call_user_func_array([__CLASS__,$_POST['request']],[]);
  }
  private static function addUser(){
    if(EDAY_ADMIN_TYPE!=='admin'&&EDAY_ADMIN_TYPE!=='master'){
      return self::error('Have no right to add a user.');
    }
    if(!isset($_POST['username'],$_POST['password'],$_POST['cpassword'])){
      return self::error('Require username, password and cpassword.');
    }
    if(($_POST['password']!==''||$_POST['cpassword']!=='')
      &&$_POST['password']!==$_POST['cpassword']){
      return self::error('Password and confirm password is not equal.');
    }
    if(!preg_match('/^[a-z0-9]+$/',$_POST['username'])){
      return self::error('Invalid username. Must be alpha-numeric.');
    }
    $db=site::db();
    $sel=$db->query('select * from users where username="'.$_POST['username'].'"');
    if($sel&&isset($sel[0])){
      return self::error('Username has been taken.');
    }$db->error=false;
    $ins=$db->query('insert into users '.http_build_query([
      'username'=>'string('.$_POST['username'].')',
      'password'=>'string('.password_hash($_POST['password'],PASSWORD_BCRYPT).')',
      'type'=>'string(user)',
    ]));
    if(!$ins||$db->error){
      return self::error($db->error?$db->error:'Failed to add a user.');
    }return self::result();
  }
  private static function saveUser(){
    if(!isset($_POST['id'])||!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid user ID.');
    }
    if(EDAY_ADMIN_TYPE!=='master'&&EDAY_ADMIN_ID!=$_POST['id']){
      return self::error('Have no right to edit this user.');
    }
    if(!isset($_POST['username'],$_POST['password'],$_POST['cpassword'])){
      return self::error('Require username, password and cpassword.');
    }
    if(($_POST['password']!==''||$_POST['cpassword']!=='')
      &&$_POST['password']!==$_POST['cpassword']){
      return self::error('Password and confirm password is not equal.'
        ."\r\n".'Leave this blank means no change.');
    }
    if(!preg_match('/^[a-z0-9]+$/',$_POST['username'])){
      return self::error('Invalid username. Must be alpha-numeric.');
    }
    $db=site::db();
    $sel=$db->query('select * from users where username="'.$_POST['username'].'"');
    if($sel&&isset($sel[0])&&$sel[0]['id']!=EDAY_ADMIN_ID){
      return self::error('Username has been taken.');
    }$db->error=false;
    $data=['username'=>'string('.$_POST['username'].')'];
    if($_POST['password']!==''){
      $data['password']='string('.password_hash($_POST['password'],PASSWORD_BCRYPT).')';
    }
    $upd=$db->query('update users '.http_build_query($data).' where id='.$_POST['id']);
    if(!$upd||$db->error){
      return self::error($db->error?$db->error:'Failed to save the user update.');
    }return self::result();
  }
  private static function makeAdmin(){
    if(!isset($_POST['id'])||!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid user ID.');
    }
    if(!isset($_POST['isAdmin'])){
      return self::error('Require isAdmin parameter.');
    }
    if(EDAY_ADMIN_TYPE!=='admin'&&EDAY_ADMIN_TYPE!=='master'){
      return self::error('Have no right to edit this user.');
    }
    $db=site::db();
    $sel=$db->query('select * from users where id='.$_POST['id']);
    if($sel&&isset($sel[0])&&$sel[0]['type']=='master'){
      return self::error('Master cannot be an admin.');
    }$db->error=false;
    $upd=$db->query('update users '.http_build_query([
      'type'=>'string('.($_POST['isAdmin']?'user':'admin').')'
    ]).' where id='.$_POST['id']);
    if(!$upd||$db->error){
      return self::error($db->error?$db->error:'Failed to save the user update.');
    }return self::result();
  }
  private static function deleteUser(){
    if(!isset($_POST['id'])||!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid user ID.');
    }
    if(EDAY_ADMIN_TYPE!=='admin'&&EDAY_ADMIN_TYPE!=='master'){
      return self::error('Have no right to delete a user.');
    }
    if(site::userRow()==1){
      return self::error('Cannot delete last user.');
    }
    $db=site::db();
    if(!$db->query('delete from users where id='.$_POST['id'])){
      return self::error('Failed to delete a user.');
    }return self::result();
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
