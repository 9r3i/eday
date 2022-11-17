<?php
/* webHelper
 * ~ helper for website app --> e-Day Framework
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 6th 2019
 * @requires:
 *   - dataObject
 *   - site
 */
use eday\eday;
use eday\site;
class webHelper{
  const version='1.0.1';
  /* parse site menu */
  public static function parseSiteMenu(array $menus){
    $data=[];
    foreach($menus as $menu){
      $type=$menu['type'];
      if(!isset($data[$type])){
        $data[$type]=[];
      }
      $key=sprintf('%09d%09d',$menu['order'],$menu['aid']);
      $data[$type][$key]=new dataObject([
        'id'=>$menu['aid'],
        'name'=>$menu['name'],
        'slug'=>$menu['slug'],
        'order'=>$menu['order'],
      ]);
      ksort($data[$type]);
    }return new dataObject($data);
  }
  /* generate site data */
  public static function siteData(array $data){
    $def=self::siteDefaultData()->toArray();
    $ndata=[];
    foreach($def as $key=>$value){
      $ndata[$key]=isset($data[$key])?$data[$key]:$value;
    }return new dataObject($ndata);
  }
  /* get site default data */
  public static function siteDefaultData(){
    $def=self::siteDefaultDataArray();
    $iniFile=EDAY_APP_DIR.site::config('app').'/config.ini';
    $ini=@parse_ini_file($iniFile,true);
    $ini=is_array($ini)&&isset($ini['website'])
      ?$ini['website']:[];
    $res=[];
    foreach($def as $key=>$value){
      $res[$key]=isset($ini[$key])?$ini[$key]:$value;
    }return new dataObject($res);
  }
  /* get post default data */
  public static function postDefaultData(){
    $ini=@parse_ini_string(self::postDefaultDataString());
    $ini=is_array($ini)?$ini:[];
    $ini['length']=count($ini);
    return new dataObject($ini);
  }
  /* site default data string */
  public static function siteDefaultDataArray(){
    return [
      'name'=>'Unnamed Website',
      'title'=>'Untitled Website',
      'description'=>'Website description',
      'keywords'=>'Website keywords',
      'robots'=>'indexes, followes',
      'author'=>'9r3i',
      'authorURI'=>'https://github.com/9r3i',
      'generator'=>'e-Day',
      'version'=>eday::version,
      'canonical'=>site::url,
      'pingback'=>site::url,
      'alternate'=>site::url.'feed.xml',
    ];
  }
  /* post default data string */
  public static function postDefaultDataString(){
    return <<<EOD
aid=40404
title=Error: 404 Not Found
content=Error: 404 Not Found
status=publish
type=page
access=public
template=standard
description=
keywords=
trainer=
picture=
price=
stock=
place=
host=
start=
end=
author=9r3i
url=404
datetime=2019-11-06 13:43:48
time=1407810399
cid=10000116
EOD;
  }
/* ------- TABLE KEYS: posts -------
----- type -----
page     =26 --> .html
article  =37 --> .html
training =43 --> .html
post     =11 --> .html
*/
}

