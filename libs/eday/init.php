<?php
/* e-day initial -- renamed from engine
 * ~ e-commerce cms
 * authored by 9r3i
 * https://github.com/9r3i
 * started at august 24th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday
 */
namespace eday;
use eday\eday;
use eday\site;
class init{
  const version='1.1.0';
  public $engine=null;
  public function __construct(string $dir){
    /* prepare initial directory */
    $dir=str_replace('\\','/',$dir);
    $dir.=substr($dir,-1)!='/'?'/':'';
    /* initialized check */
    if(!defined('EDAY_INITIALIZED')){
      /* define root directory */
      defined('EDAY_ROOT') or define('EDAY_ROOT',$dir.'/eday/');
      /* define index directory */
      defined('EDAY_INDEX_DIR') or define('EDAY_INDEX_DIR',$dir);
      /* prevent direct access */
      defined('EDAY') or eday::error('Failed to access directory.',403);
      /* if site is offline */
      EDAY or eday::error('Site is not active.',404);
      /* initialize eday engine */
      eday::initialize() or eday::error(eday::$error);
    }
    /* prepare the engine */
    $this->engine=site::engine();
    /* return the object */
    return $this;
  }
}


