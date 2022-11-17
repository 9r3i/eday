<?php
/* site for default application
 * started at september 4th 2018
 */

/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain');
  exit('Error: This application requires e-Day engine.');  
}

/* use namespace */
use eday\get;
use eday\site;

/* return direct call */
return new defaultApp;

/* class default app */
class defaultApp{
  const version='1.0.0';
  public $name=null;
  public $description=null;
  public $keyword=null;
  public $title=null;
  public $start=null;
  public $current=null;
  
  function __construct(){
    $init=get::info();
    $this->name=$init->name;
    $this->description=$init->description;
    $this->keyword=$init->keyword;
    $this->title=$init->name.' &#8213; '.$init->description;
    $this->start=microtime(true);
    $this->current=site::ruri;
    
    
  }
  function generated(){
    return '<!-- application generated in '.number_format(microtime(true)-$this->start,4,'.','').' sec -->';
  }
  function load($p){
    $f=EDAY_APP_DIR.'default/pages/'.$p.'.php';
    if(!is_file($f)){return false;}
    return require_once($f);
  }
}
