<?php
/* class post for e-Day admin
 * started at august 25th 2018
 */
return new post;
class post{
  public function all($p=1,$l=10){
    global $title,$posts,$error,$row,$next,$limit;
    $s=(int)$p>1?((int)$p-1)*$l:0;
    $posts=site::post(null,$err,$s,(int)$l);
    $error=$err;
    $row=site::postRow($err);
    $error=!$error?$err:$error;
    if($row==0){
      $error=false;
      $posts=[];
    }
    $next=false;$limit=$l;
    if($row&&$row>$p*$l){$next=$p+1;}
    $title='All Posts';
    admin::html('header');
    admin::html('menu');
    admin::html('post-all');
    admin::html('footer');
    return true;
  }
  public function edit($id=false){
    global $title,$post,$error;
    if(!preg_match('/^\d+$/',$id)){
      $error='Invalid post ID.';
    }else{
      $post=site::post((int)$id,$err);
      $error=$err;
    }
    $title='Edit Post';
    admin::html('header');
    admin::html('menu');
    admin::html('post-edit');
    admin::html('footer');
    return true;
  }
  public function add(){
    global $title;
    $title='New Post';
    admin::html('header');
    admin::html('menu');
    admin::html('post-add');
    admin::html('footer');
    return true;
  }
  public function ajax(){
    $valid=['addPost','deletePost','savePost'];
    if(!isset($_POST['request'])
      ||!in_array($_POST['request'],$valid)
      ||!method_exists($this,$_POST['request'])){
      return self::error('Invalid request.');
    }return @\call_user_func_array([self,$_POST['request']],[]);
  }
  private static function savePost(){
    if(!isset($_POST['id'],$_POST['title'],$_POST['content'])){
      return self::error('Require post ID, title and content.');
    }
    if(!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid post ID.');
    }
    $file=isset($_FILES['picture'])?(object)$_FILES['picture']:false;
    $filename=isset($_POST['picture'])?$_POST['picture']:'';
    if($file){
      if($file->error){return self::error('Invalid file.');}
      $dir=EDAY_INDEX_DIR.'files/upload/';
      $new=$dir.preg_replace('/[^a-z0-9\.-]+/i','-',strtolower($file->name));
      if(!is_dir($dir)){@mkdir($dir,0755,true);}
      @rename($file->tmp_name,$new);
      @chmod($new,0777);
      $filename='files/upload/'.basename($new);
    }
    $db=site::db();
    $upd=$db->query('update posts '.http_build_query([
      'title'=>'string('.$_POST['title'].')',
      'content'=>'string:'.$_POST['content'],
      'picture'=>'string('.$filename.')',
      'datetime'=>'string('.date('Y-m-d H:i:s').')',
    ]).' where id='.$_POST['id']);
    if(!$upd||$db->error){
      return self::error($db->error?$db->error:'Failed to update data post.');
    }return self::result();
  }
  private static function deletePost(){
    if(!isset($_POST['id'])||!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid post ID.');
    }
    $db=site::db();
    if(!$db->query('delete from posts where id='.$_POST['id'])){
      return self::error('Failed to delete a post.');
    }$db->query('delete from tags where tid='.$_POST['id']);
    return self::result();
  }
  private static function addPost(){
    if(!isset($_POST['title'],$_POST['content'])){
      return self::error('Require title and content.');
    }
    $file=isset($_FILES['picture'])?(object)$_FILES['picture']:false;
    $filename='';
    if($file){
      if($file->error){return self::error('Invalid file.');}
      $dir=EDAY_INDEX_DIR.'files/upload/';
      $new=$dir.preg_replace('/[^a-z0-9\.-]+/i','-',strtolower($file->name));
      if(!is_dir($dir)){@mkdir($dir,0755,true);}
      @rename($file->tmp_name,$new);
      @chmod($new,0777);
      $filename='files/upload/'.basename($new);
    }
    $db=site::db();
    $ins=$db->query('insert into posts '.http_build_query([
      'title'=>'string('.$_POST['title'].')',
      'content'=>'string:'.$_POST['content'],
      'picture'=>'string('.$filename.')',
      'datetime'=>'string('.date('Y-m-d H:i:s').')',
    ]));
    if(!$ins||$db->error){
      return self::error($db->error?$db->error:'Failed to insert data post.');
    }
    $last=$db->query('real lastid');
    if($last&&!$db->error){
      $upd=$db->query('update tags tid=int('.$last.') where tid=1000000000 and type="post"');
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
