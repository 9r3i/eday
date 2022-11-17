<?php
/* class option for e-Day admin
 * started at august 27th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
use eday\site;
class option{
  const version='1.1.0';
  public function globals(){
    global $title;
    $title='Globals';
    admin::html('header');
    admin::html('menu');
    admin::html('globals');
    admin::html('footer');
    return true;
  }
  public function settings(){
    global $title,$option;
    $option=site::config();
    $title='Settings';
    admin::html('header');
    admin::html('menu');
    admin::html('settings');
    admin::html('footer');
    return true;
  }
  public function ajax(){
    if(EDAY_ADMIN_TYPE!=='master'&&EDAY_ADMIN_TYPE!=='admin'){
      return self::error('Have no access to this page.');
    }
    $valid=['saveSettings','',''];
    if(!isset($_POST['request'])
      ||!in_array($_POST['request'],$valid)
      ||!method_exists($this,$_POST['request'])){
      return self::error('Invalid request.');
    }return @\call_user_func_array([__CLASS__,$_POST['request']],[]);
  }
  private static function saveSettings(){
    if(!isset($_POST['name'],$_POST['description'],$_POST['keyword'],$_POST['app'])
      ||!isset($_POST['timezone'],$_POST['api'],$_POST['dbapi'])){
      return self::error('Require name, description, keyword, app, timezone, api and dbapi.');
    }
    self::saveConfigINI($_POST);
    $db=site::db();
    $upd=$db->query('update site '.http_build_query([
      'name'=>'string('.$_POST['name'].')',
      'description'=>'string('.$_POST['description'].')',
      'keyword'=>'string('.$_POST['keyword'].')',
      'app'=>'string('.$_POST['app'].')',
      'timezone'=>'string('.$_POST['timezone'].')',
      'api'=>'int('.$_POST['api'].')',
      'dbapi'=>'int('.$_POST['dbapi'].')',
    ]).' where id=1');
    if(!$upd||$db->error){
      return self::error($db->error?$db->error:'Failed to save settings.');
    }return self::result();
  }
  private static function saveConfigINI(array $data){
    if(!isset($data['name'],$data['description'],$data['keyword'],$data['app'])
      ||!isset($data['timezone'],$data['api'],$data['dbapi'])){
      return false;
    }
    $file=EDAY_ROOT.'config.ini';
    $tfile=EDAY_ROOT.'config.ini.tmp';
    if(!is_file($file)||!is_readable($file)){
      return false;
    }@copy($file,$tfile);
    $old=@file($file,FILE_IGNORE_NEW_LINES);
    if(!is_array($old)){return false;}
    $string=['name','description','keyword','app','timezone'];
    $int=['api','dbapi'];
    $res=[];
    $error=false;
    foreach($string as $key){
      if(!isset($data[$key])){
        $error=true;break;
      }$res[]="{$key}=\"{$data[$key]}\"";
    }if($error){return false;}
    foreach($int as $key){
      if(!isset($data[$key])){
        $error=true;break;
      }$res[]="{$key}={$data[$key]}";
    }if($error){return false;}
    $start=false;
    $out=[];$count=0;
    $boundary='; end of site';
    foreach($old as $value){
      if(trim($value)==$boundary){
        $out[]=trim($value);
        $start=false;
      }elseif(trim($value)=='[site]'){
        $out[]=trim($value);
        $start=true;
      }elseif(!$start){
        $out[]=trim($value);
      }else{
        if(isset($res[$count])){
          $out[]=$res[$count];
        }$count++;
      }
    }
    $put=@file_put_contents($file,implode("\r\n",$out));
    if(!$put){@copy($tfile,$file);}
    @unlink($tfile);
    return true;
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
