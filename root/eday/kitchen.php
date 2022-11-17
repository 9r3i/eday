<?php
/* admin app
 * ~ application for e-day admin
 * authored by 9r3i
 * https://github.com/9r3i
 * started at august 25th 2018
 */

/* check eday initialized */
if(!defined('EDAY_INITIALIZED')){
  header('content-type:text/plain',true,401);
  exit('Error: 401 Unauthorized.');
}

/* use namespace eday\admin */
use eday\eday;
use eday\admin;

/* check admin request path */
defined('EDAY_ADMIN_PATH') or eday::error('Invalid admin request path.');

/* start admin privilege */
$admin=new admin;

/* start execute the request */
if(!$admin->start()||$admin->error){
  $error=$admin->error?$admin->error:'Failed to execute admin page.';
  /* check dashboard file */
  if($admin->error_level>=16){
    if(admin::isLogin()){
      if(!is_file(__DIR__.'/pages/dashboard.php')
        ||$admin->isLoaded('dashboard')){
        eday::error($error);
      }
      /* redirect request to dashboard */
      header('location: '.site::url.'?'.site::adminKey().'=dashboard/home');
      exit;
    }admin::redirect('log/in');
  }eday::error($error);
}
