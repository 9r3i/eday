<?php
/* class tag for e-Day admin
 * started at august 29th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
use eday\site;
class tag{
  const version='1.1.0';
  public function ajax(){
    $valid=['addTag','deleteTag','getTags'];
    if(!isset($_POST['request'])
      ||!in_array($_POST['request'],$valid)
      ||!method_exists($this,$_POST['request'])){
      return self::error('Invalid request.');
    }return @\call_user_func_array([__CLASS__,$_POST['request']],[]);
  }
  private static function getTags(){
    if(!isset($_POST['tid'],$_POST['type'])){
      return self::error('Require TID and tag type.');
    }
    if(!preg_match('/^\d+$/',$_POST['tid'])){
      return self::error('Invalid TID.');
    }
    if(!preg_match('/^[a-z0-9]+$/',$_POST['type'])){
      return self::error('Invalid tag type.');
    }
    $db=site::db();
    $sel=$db->query('select * from tags where tid='.$_POST['tid'].' and type="'.$_POST['type'].'"');
    if(!$sel||$db->error){
      return self::error($db->error?$db->error:'Failed to get tags.');
    }return self::result(json_encode($sel));
  }
  private static function deleteTag(){
    if(!isset($_POST['id'])||!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid tag ID.');
    }
    $db=site::db();
    $del=$db->query('delete from tags where id='.$_POST['id']);
    if(!$del||$db->error){
      return self::error($db->error?$db->error:'Failed to delete a tag.');
    }return self::result();
  }
  private static function addTag(){
    if(!isset($_POST['tid'],$_POST['name'],$_POST['type'])){
      return self::error('Require TID, tag name and tag type.');
    }
    if(!preg_match('/^\d+$/',$_POST['tid'])){
      return self::error('Invalid TID.');
    }
    if(!preg_match('/^[a-z0-9\-\s]+$/i',$_POST['name'])){
      return self::error('Invalid tag name.');
    }
    if(!preg_match('/^[a-z0-9]+$/',$_POST['type'])){
      return self::error('Invalid tag type.');
    }
    $name=trim($_POST['name']);
    $tag=preg_replace('/[^a-z0-9-]+/','-',strtolower($name));
    $db=site::db();
    $ins=$db->query('insert into tags '.http_build_query([
      'tag'=>'string('.$tag.')',
      'name'=>'string('.$name.')',
      'tid'=>'int('.$_POST['tid'].')',
      'type'=>'string('.$_POST['type'].')',
    ]));
    if(!$ins||$db->error){
      return self::error($db->error?$db->error:'Failed to add a tag.');
    }
    $id=$db->query('real lastid');
    if(!$id||$db->error){
      return self::error($db->error?$db->error:'Failed to get ID.');
    }return self::result(json_encode(['id'=>$id]));
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
