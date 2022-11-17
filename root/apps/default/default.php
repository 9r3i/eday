<?php
/* default
 * ~ default application for e-Day engine
 * authored by 9r3i
 * https://github.com/9r3i
 * started at september 4th 2018
 */

/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain');
  exit('Error: This application requires e-Day engine.');  
}

/* global $site */
global $site;

/* call the class */
$site=require_once('pages/site.php');

/* load header */
$site->load('header');

/* get current */
if(isset($_GET['product_id'])){
  /* load product */
  $site->load('product');
}elseif(isset($_GET['post_id'])){
  /* load post */
  $site->load('post');
}else{
  /* load index */
  $site->load('index');
}

/* load sidebar */
$site->load('sidebar');

/* load footer */
$site->load('footer');

/* calculate generated application */
echo $site->generated();


