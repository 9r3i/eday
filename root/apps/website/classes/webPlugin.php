<?php
/* web Plugin
 * ~ website plugin option helper --> class <className> extends webPlugin
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 11th 2019
 */
#[AllowDynamicProperties]
class webPlugin{
  const version='1.0.0';
  protected $pluginNS;
  /* construct */
  public function __construct(string $pns){
    /* set plugin namespace */
    $this->pluginNS=$pns;
  }
  /* get global website */
  public function website(){
    global $website;
    return $website;
  }
  /* get global post */
  public function post(){
    global $post;
    return $post;
  }
  /* get option */
  public function option($key=null,$section='config'){
    $file=$this->path('options.ini');
    if(!is_file($file)){return false;}
    $ini=@parse_ini_file($file,true);
    $ini=is_array($ini)?$ini:[];
    if(is_string($key)){
      return isset($ini[$section][$key])
        ?$ini[$section][$key]:false;
    }return isset($ini[$section])
      ?new dataObject($ini[$section])
      :new dataObject($ini);
  }
  /* get plugin directory content */
  public function path(string $file){
    $basePath=WEBSITE_PLUGINS_DIRECTORY.$this->pluginNS.'/';
    return $basePath.$file;
  }
  /* get plugin url */
  public function url(string $file){
    $baseURL=WEBSITE_PLUGINS_PATH.$this->pluginNS.'/';
    return $baseURL.$file;
  }
}


