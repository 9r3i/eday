<?php
/* webiste
 * ~ new type of website
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 5th 2019
 */

/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain');
  exit('Error: This application requires e-Day engine.');  
}

/* autoload */
spl_autoload_register(function($cn){
  $dir=__DIR__;
  $cf="{$dir}/classes/{$cn}.php";
  if(class_exists($cn,false)
    ||!is_file($cf)){
    return false;
  }require_once($cf);
});

/* define Ldb2x direcotry */
defined('LDB2X_CLI_DIR') or define('LDB2X_CLI_DIR',__DIR__.'/database');

/* initialize the website */
new website;


