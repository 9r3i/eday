<?php
/* class dashboard for e-Day admin
 * started at august 25th 2018
 */
return new dashboard;
class dashboard{
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
