<?php
/* class file for e-Day admin
 * started at september 1st 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
class file{
  const version='1.1.0';
  public function manager($p=null){
    global $title,$files,$error,$path;
    $error=false;
    $files=self::scanDefault();
    $path=$p===null?'':@base64_decode($p);
    if(!$path&&$p!==null){
      $error='Invalid path.';
      $files=false;
    }elseif($path&&$p!==null){
      $files=self::scan($path);
      if(!is_array($files)){$error='Failed to scan path.';}
    }
    if(EDAY_ADMIN_TYPE!=='master'&&EDAY_ADMIN_TYPE!=='admin'){
      $error='Have no access to this page.';
    }
    $title='File Manager';
    admin::html('header');
    admin::html('menu');
    admin::html('file-manager');
    admin::html('footer');
    return true;
  }
  public function ajax(){
    if(EDAY_ADMIN_TYPE!=='master'&&EDAY_ADMIN_TYPE!=='admin'){
      return self::error('Have no access to this page.');
    }
    $valid=['deleteFile','uploadFile','renameFile'];
    if(!isset($_POST['request'])
      ||!in_array($_POST['request'],$valid)
      ||!method_exists($this,$_POST['request'])){
      return self::error('Invalid request.');
    }return @\call_user_func_array([__CLASS__,$_POST['request']],[]);
  }
  private static function renameFile(){
    if(!isset($_POST['file'],$_POST['nfile'],$_POST['path'])){
      return self::error('Require file, nfile and path.');
    }
    if(preg_match('/^\./',$_POST['file'])
      ||preg_match('/^\./',$_POST['nfile'])
      ||preg_match('/^\./',$_POST['path'])){
      return self::error('Invalid file or nfile or path.');
    }
    $file=EDAY_INDEX_DIR.$_POST['path'].'/'.$_POST['file'];
    $new=EDAY_INDEX_DIR.$_POST['path'].'/'.$_POST['nfile'];
    if(!is_file($file)||preg_match('/^files\/(editors|kitchen)/',$_POST['path'].'/'.$_POST['file'])){
      return self::error('File does not exist.');
    }
    if(is_file($new)||preg_match('/^files\/(editors|kitchen)/',$_POST['path'].'/'.$_POST['nfile'])){
      return self::error('File name has been taken.');
    }
    if(!@rename($file,$new)){
      return self::error('Failed to rename file.');
    }return self::result();
  }
  private static function uploadFile(){
    if(!isset($_FILES['file'],$_POST['path'])){
      return self::error('Require file and path.');
    }
    if(preg_match('/^\./',$_FILES['file']['name'])||preg_match('/^\./',$_POST['path'])){
      return self::error('Invalid file or path.');
    }
    $new=preg_replace('/[^a-z0-9\.-]+/i','-',strtolower($_FILES['file']['name']));
    $file=EDAY_INDEX_DIR.$_POST['path'].'/'.$new;
    if($_FILES['file']['error']){
      return self::error('File is error.');
    }
    if(is_file($file)||preg_match('/^files\/(editors|kitchen)/',$_POST['path'].'/'.$new)){
      return self::error('File does exist.');
    }
    if(!@move_uploaded_file($_FILES['file']['tmp_name'],$file)){
      return self::error('Failed to delete file.');
    }return self::result();
  }
  private static function deleteFile(){
    if(!isset($_POST['file'],$_POST['path'])){
      return self::error('Require file and path.');
    }
    if(preg_match('/^\./',$_POST['file'])||preg_match('/^\./',$_POST['path'])){
      return self::error('Invalid file or path.');
    }$file=EDAY_INDEX_DIR.$_POST['path'].'/'.$_POST['file'];
    if(!is_file($file)||preg_match('/^files\/(editors|kitchen)/',$_POST['path'].'/'.$_POST['file'])){
      return self::error('File does not exist.');
    }
    if(!@unlink($file)){
      return self::error('Failed to delete file.');
    }return self::result();
  }
  private static function scanDefault(){
    return [
      [
        'name'=>'files',
        'path'=>'',
        'type'=>'directory',
        'size'=>@filesize(EDAY_INDEX_DIR.'files'),
        'modified'=>@filemtime(EDAY_INDEX_DIR.'files'),
      ],
      [
        'name'=>'apps',
        'path'=>'',
        'type'=>'directory',
        'size'=>@filesize(EDAY_INDEX_DIR.'apps'),
        'modified'=>@filemtime(EDAY_INDEX_DIR.'apps'),
      ],
    ];
  }
  private static function scan($d=null){
    if(!is_string($d)||!is_dir(EDAY_INDEX_DIR.$d)||preg_match('/^\./',$d)){return false;}
    $d=str_replace('\\','/',$d);
    $d.=substr($d,-1)!='/'?'/':'';
    $s=@array_diff(@scandir(EDAY_INDEX_DIR.$d),['.','..']);
    if(!is_array($s)){return false;}
    @usort($s,function($a,$b){
      $c='/';
      $a=substr($a,0,1)==='_'?$c.substr($a,1):$a;
      $b=substr($b,0,1)==='_'?$c.substr($b,1):$b;
      return strcasecmp($a,$b);
    });$r=[];$u=[];
    if($d=='files/'){$s=array_diff($s,['editors','kitchen']);}
    foreach($s as $f){
      $t=is_file(EDAY_INDEX_DIR.$d.$f)?'file'
        :(is_dir(EDAY_INDEX_DIR.$d.$f)?'directory':'unknown');
      $v=[
        'name'=>$f,
        'path'=>$d,
        'type'=>$t,
        'size'=>@filesize(EDAY_INDEX_DIR.$d.$f),
        'modified'=>@filemtime(EDAY_INDEX_DIR.$d.$f),
      ];
      if($t=='directory'){$u[]=$v;}
      else{$r[]=$v;}
    }return array_merge($u,$r);
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
