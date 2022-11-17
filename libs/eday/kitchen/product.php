<?php
/* class product for e-Day admin
 * started at august 27th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
use eday\site;
class product{
  const version='1.1.0';
  public function all($p=1,$l=9){
    global $title,$products,$error,$row,$next,$limit;
    $s=(int)$p>1?((int)$p-1)*$l:0;
    $products=site::product(null,$err,$s,(int)$l);
    $error=$err;
    $row=site::productRow($err);
    $error=!$error?$err:$error;
    if($row==0){
      $error=false;
      $products=[];
    }
    $next=false;$limit=$l;
    if($row&&$row>$p*$l){$next=$p+1;}
    $title='All Products';
    admin::html('header');
    admin::html('menu');
    admin::html('product-all');
    admin::html('footer');
    return true;
  }
  public function add(){
    global $title;
    $title='New Product';
    admin::html('header');
    admin::html('menu');
    admin::html('product-add');
    admin::html('footer');
    return true;
  }
  public function edit($id=false){
    global $title,$product,$error;
    if(!preg_match('/^\d+$/',$id)){
      $error='Invalid product ID.';
    }else{
      $product=site::product((int)$id,$err);
      $error=$err;
    }
    $title='Edit Product';
    admin::html('header');
    admin::html('menu');
    admin::html('product-edit');
    admin::html('footer');
    return true;
  }
  public function ajax(){
    $valid=['deleteProduct','addProduct','saveProduct'];
    if(!isset($_POST['request'])
      ||!in_array($_POST['request'],$valid)
      ||!method_exists($this,$_POST['request'])){
      return self::error('Invalid request.');
    }return @\call_user_func_array([__CLASS__,$_POST['request']],[]);
  }
  private static function saveProduct(){
    if(!isset($_POST['id'],$_POST['name'],$_POST['price'],$_POST['discount'],$_POST['currency'],$_POST['ribbon'],$_POST['order_to'],$_POST['description'])){
      return self::error('Require product ID, name, price, discount, currency, ribbon, order_to and description.');
    }
    if(!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid product ID.');
    }
    $file=isset($_FILES['picture'])?(object)$_FILES['picture']:false;
    $filename=isset($_POST['picture'])?$_POST['picture']:'';
    if($file){
      if($file->error){return self::error('Invalid file.');}
      $dir=EDAY_INDEX_DIR.'files/upload/';
      $new=$dir.preg_replace('/[^a-z0-9\.-]+/i','-',strtolower($file->name));
      if(!is_dir($dir)){@mkdir($dir,0755,true);}
      @rename($file->tmp_name,$new);
      @chmod($new,0644);
      $filename='files/upload/'.basename($new);
    }
    $db=site::db();
    $upd=$db->query('update products '.http_build_query([
      'name'=>'string('.$_POST['name'].')',
      'price'=>'int('.$_POST['price'].')',
      'discount'=>'int('.$_POST['discount'].')',
      'currency'=>'string('.$_POST['currency'].')',
      'ribbon'=>'string('.$_POST['ribbon'].')',
      'order_to'=>'string('.$_POST['order_to'].')',
      'description'=>'string:'.$_POST['description'].'',
      'picture'=>'string('.$filename.')',
    ]).' where id='.$_POST['id']);
    if(!$upd||$db->error){
      return self::error($db->error?$db->error:'Failed to update data product.');
    }return self::result();
  }
  private static function addProduct(){
    if(!isset($_POST['name'],$_POST['price'],$_POST['discount'],$_POST['currency'],$_POST['ribbon'],$_POST['order_to'],$_POST['description'])){
      return self::error('Require name, price, discount, currency, ribbon, order_to and description.');
    }
    $file=isset($_FILES['picture'])?(object)$_FILES['picture']:false;
    $filename='';
    if($file){
      if($file->error){return self::error('Invalid file.');}
      $dir=EDAY_INDEX_DIR.'files/upload/';
      $new=$dir.preg_replace('/[^a-z0-9\.-]+/i','-',strtolower($file->name));
      if(!is_dir($dir)){@mkdir($dir,0755,true);}
      @rename($file->tmp_name,$new);
      @chmod($new,0644);
      $filename='files/upload/'.basename($new);
    }
    $db=site::db();
    $ins=$db->query('insert into products '.http_build_query([
      'name'=>'string('.$_POST['name'].')',
      'price'=>'int('.$_POST['price'].')',
      'discount'=>'int('.$_POST['discount'].')',
      'currency'=>'string('.$_POST['currency'].')',
      'ribbon'=>'string('.$_POST['ribbon'].')',
      'order_to'=>'string('.$_POST['order_to'].')',
      'description'=>'string:'.$_POST['description'].'',
      'picture'=>'string('.$filename.')',
    ]));
    if(!$ins||$db->error){
      return self::error($db->error?$db->error:'Failed to insert data product.');
    }
    $last=$db->query('real lastid');
    if($last&&!$db->error){
      $upd=$db->query('update tags tid=int('.$last.') where tid=1000000000 and type="product"');
    }return self::result();
  }
  private static function deleteProduct(){
    if(!isset($_POST['id'])||!preg_match('/^\d+$/',$_POST['id'])){
      return self::error('Invalid product ID.');
    }
    $db=site::db();
    if(!$db->query('delete from products where id='.$_POST['id'])){
      return self::error('Failed to delete a product.');
    }$db->query('delete from tags where tid='.$_POST['id']);
    return self::result();
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
