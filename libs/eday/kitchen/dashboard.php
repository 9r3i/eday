<?php
/* class dashboard for e-Day admin
 * started at august 25th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday\kitchen
 */
namespace eday\kitchen;
use eday\admin;
class dashboard{
  const version='1.1.0';
  public function home(){
    global $title;
    $title='Dashboard';
    admin::html('header');
    admin::html('menu');
    admin::html('dashboard');
    admin::html('footer');
    return true;
  }
}
