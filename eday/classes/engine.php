<?php
/* class engine for e-day
 * started at august 24th 2018
 */
class engine{
  public $info=null;
  public function __construct($info=null){
    $this->info=$info;
  }
  public function __call($method=null,$args=[]){
    if(!isset($this->$method)){return false;}
    return @\call_user_func_array($this->$method,$args);
  }
}
