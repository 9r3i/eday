<?php
/* class option for e-Day admin
 * started at august 27th 2018
 */
return new option;
class option{
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
    }return @\call_user_func_array([self,$_POST['request']],[]);
  }
  private static function saveSettings(){
    if(!isset($_POST['name'],$_POST['description'],$_POST['keyword'],$_POST['theme'],$_POST['timezone'],$_POST['api'],$_POST['dbapi'])){
      return self::error('Require name, description, keyword, theme, timezone, api and dbapi.');
    }
    $db=site::db();
    $upd=$db->query('update site '.http_build_query([
      'name'=>'string('.$_POST['name'].')',
      'description'=>'string('.$_POST['description'].')',
      'keyword'=>'string('.$_POST['keyword'].')',
      'theme'=>'string('.$_POST['theme'].')',
      'timezone'=>'string('.$_POST['timezone'].')',
      'api'=>'int('.$_POST['api'].')',
      'dbapi'=>'int('.$_POST['dbapi'].')',
    ]).' where id=1');
    if(!$upd||$db->error){
      return self::error($db->error?$db->error:'Failed to save settings.');
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
