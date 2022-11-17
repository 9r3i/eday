<?php
/* class menu for e-Day admin
 * started at august 28th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
use eday\site;
use eday\base;
class menu{
  const version='1.1.0';
  public function all(){
    global $title,$menus,$error;
    $db=site::db();
    $sel=$db->query('SELECT * FROM menus');
    $parsed=base::parseMenuChildren($sel);
    $menus=base::parseMenu($parsed);
    $error=false;
    if($db->error){
      $error=$db->error;
      $count=$db->query('SELECT count(id) as row FROM menus');
      if($count&&isset($count[0]['row'])&&$count[0]['row']==0){
        $error=false;
        $menus=[];
      }
    }
    $title='Menus';
    admin::html('header');
    admin::html('menu');
    admin::html('menu-all');
    admin::html('footer');
    return true;
  }
  public function ajax(){
    $valid=['moveMenu','addMenu','deleteMenu',''];
    if(!isset($_POST['request'])
      ||!in_array($_POST['request'],$valid)
      ||!method_exists($this,$_POST['request'])){
      return self::error('Invalid request.');
    }return @\call_user_func_array([__CLASS__,$_POST['request']],[]);
  }
  private static function deleteMenu(){
    if(!isset($_POST['id'])||!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid menu ID.');
    }
    $db=site::db();
    if(!$db->query('delete from menus where id='.$_POST['id'])){
      return self::error('Failed to delete a menu.');
    }return self::result();
  }
  private static function addMenu(){
    if(!isset($_POST['name'],$_POST['uri'],$_POST['type'],$_POST['parent'])){
      return self::error('Require name, uri, type and parent.');
    }
    if(!preg_match('/^[a-z0-9]+$/',$_POST['type'])){
      return self::error('Invalid menu type.');
    }
    $db=site::db();
    $data=[
      'name'=>'string('.$_POST['name'].')',
      'uri'=>'string('.$_POST['uri'].')',
      'type'=>'string('.$_POST['type'].')',
      'parent'=>'int('.$_POST['parent'].')',
    ];
    $ins=$db->query('insert into menus '.http_build_query($data));
    if(!$ins||$db->error){
      return self::error($db->error?$db->error:'Failed to add a menu.');
    }
    $id=$db->query('real lastid');
    if(!$id||$db->error){
      return self::error($db->error?$db->error:'Failed to get ID.');
    }return self::result(json_encode(['id'=>$id]));
  }
  private static function moveMenu(){
    if(!isset($_POST['id'],$_POST['parent'])){
      return self::error('Require id and parent.');
    }
    if(!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid menu ID.');
    }
    $isType=false;
    if(!preg_match('/^\d+$/',$_POST['parent'])){
      $isType=true;
    }
    $db=site::db();
    $sel=$db->query('select * from menus where '
      .($isType?'type="'.$_POST['parent'].'"':'id='.$_POST['parent']));
    if(!$sel||!isset($sel[0])){
      return self::error('Parent menu does not exist.');
    }$db->error=false;
    $data=['parent'=>'int('.$_POST['parent'].')'];
    if($isType){
      $data=[
        'parent'=>'int(0)',
        'type'=>'string('.$_POST['parent'].')',
      ];
    }
    $upd=$db->query('update menus '.http_build_query($data).' where id='.$_POST['id']);
    if(!$upd||$db->error){
      return self::error($db->error?$db->error:'Failed to move a menu.');
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
