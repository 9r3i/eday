<?php
/* e-day engine
 * ~ e-commerce cms
 * authored by 9r3i
 * https://github.com/9r3i
 * started at august 24th 2018
 */

/* initialized check */
if(defined('EDAY_INITIALIZED')){goto engine;}

/* define root directory */
defined('EDAY_ROOT') or define('EDAY_ROOT',str_replace('\\','/',__DIR__).'/');

/* define index directory */
defined('EDAY_INDEX_DIR') or define('EDAY_INDEX_DIR',str_replace('\\','/',dirname(__DIR__)).'/');

/* prepare auto-load */
spl_autoload_register(function($c){
  $f=EDAY_ROOT.'classes/'.$c.'.php';
  if(!is_file($f)){
    header('content-type:text/plain');
    exit('Error: Failed to load "'.$c.'".');
  }require_once($f);
});

/* prevent direct access */
defined('EDAY') or eday::error('Failed to access directory.',403);

/* if site is offline */
EDAY or eday::error('Site is not active.',404);

/* initialize eday engine */
eday::initialize() or eday::error(eday::$error);

/* return the engine */
engine:
return site::engine();
