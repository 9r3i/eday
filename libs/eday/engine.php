<?php
/* class engine for e-day
 * started at august 24th 2018
 * continue at november 28th 2019 -- version 1.1.0 -- being library namespace eday
 */
namespace eday;
#[AllowDynamicProperties]
class engine{
  const version='1.1.0';
  public $info=null;
  public $start=null;
  public function __construct($info=null,$start=null){
    $this->info=$info;
    $this->start=$start;
  }
  public function __call($method=null,$args=[]){
    if(!isset($this->$method)){return false;}
    return @\call_user_func_array($this->$method,$args);
  }
}
