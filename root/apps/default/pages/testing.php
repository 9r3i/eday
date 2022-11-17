<?php
if(!defined('EDAY')){
  header('content-type:text/plain');
  exit('Error: This application requires e-Day engine.');  
}
if(isset($_GET['testing'])){goto testing;}
?>

<a href="?testing">testing script</a>
<br />
<br />
<a href="?admin">admin</a>




<?php
goto endofscript;
/* testing point */
testing:
/* ----- testing script ----- */
header('content-type:text/plain');

/* database old *
require_once('sdb-1.1.0.php');
$sdb=new sdb('localhost','eday','9r3i','site');
$old=new sdbOld('site_old');

$sel=$sdb->query('select * from products');
$tags=['Sabun','Jelly','Serbaguna','CleanJelly'];
// foreach($sel as $s){
  // foreach($tags as $tag){
    // var_dump($sdb->query('insert into tags '.http_build_query([
      // 'name'=>'string:'.$tag,
      // 'tag'=>'string:'.strtolower($tag),
      // 'tid'=>'string:'.$s['id'],
      // 'type'=>'string:product',
    // ])));
  // }
// }
// print_r($sel);

//print_r($sdb);
//print_r($old);
//*/

/* searching *
$dir=EDAY_ROOT.'classes/';
$pattern='/\b::goto\b/i';
$scan=@array_diff(@scandir($dir),['..','.']);
$scan=is_array($scan)?$scan:[];
$find=[];
foreach($scan as $file){
  if(!is_file($dir.$file)){continue;}
  $get=file_get_contents($dir.$file);
  preg_match_all($pattern,$get,$akur);
  if(!$akur[0]){continue;}
  $find[$file]=$akur[0];
}
print_r($find);
echo "\r\n\r\n------------------------------\r\n\r\n";
//*/

/* variables *
var_dump(PHP_INT_MAX);
print_r(site::defined());
print_r($GLOBALS);
//*/

/* end of  script */
endofscript:


