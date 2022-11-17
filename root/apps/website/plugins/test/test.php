<?php
/* Plugin HCIClients for Dixie CMS
 * Authored by Luthfie
 * Luthfie@y7mail.com
 * started at unknown
 * continued at november 6th 2019 
 *   -- as cloned to be a class for e-Day --> website app
 */
class test{
  const version='1.1.0';
  const address=EDAY_ADDR.'files/upload/clients/';
  /* manipulate content */
  public function content(string $str){
    /* prepare pattern */
    $ptrn='/@\[HCIClients\]/i';
    /* check content string */
    if(!preg_match('/@\[HCIClients\]/i',$str)){
      return $str;
    }
    /* replace all matched to the pattern */
    return preg_replace_callback($ptrn,function($akur){
      $explore=self::explore('files/upload/clients');
      shuffle($explore);
      $result=['<div class="hci-clients">'];
      foreach($explore as $exp){
        $result[]='<div class="hci-clients-each"><img src="'.self::address.$exp.'" /></div>';
      }$result[]='</div>';
      $result[]='<link rel="stylesheet" type="text/css" href="'
        .WEBSITE_PLUGINS_PATH.'hciClients/style.css?v='.self::version.'" />';
      return implode($result);
    },$str);
  }
  /* explore client files */
  public static function explore(string $dir){
    $dir=str_replace('\\','/',$dir);
    $dir.=substr($dir,-1)!='/'?'/':'';
    $scan=@scandir($dir);
    $scan=is_array($scan)?$scan:[];
    $result=[];
    foreach($scan as $file){
      if(is_file($dir.$file)
        &&preg_match('/\.(jpg|jpeg|png|gif|webp)$/i',$file)){
        $result[]=$file;
      }
    }return $result;
  }
}
