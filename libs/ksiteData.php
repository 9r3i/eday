<?php
/* ksiteData
 * ~ convert array to object -- cloned from dataObject 1.1.0
 * ~ all properties are gonna be public
 * authored by 9r3i
 * https://github.com/9r3i
 * started at november 5th 2019
 * cloned at november 19th 2019 -- as version 1.0.0
 * @usage: new ksiteData($array)
 */
class ksiteData{
  const version='1.0.0';
  public function __construct(array $data=[]){
    foreach($data as $key=>$value){
      if(is_string($key)&&!empty($key)){
        $this->{$key}=is_array($value)
          ?new $this($value):$value;
      }
    }
  }
  public function toArray(){
    $data=[];
    foreach($this as $key=>$value){
      $data[$key]=is_object($value)
        &&method_exists($value,'toArray')
        &&is_callable([$value,'toArray'],true)
        ?$value->toArray()
        :$value;
    }return $data;
  }
  public function add(string $key,$value=null){
    $this->{$key}=is_array($value)
      ?new $this($value):$value;
    return true;
  }
  public function length(){
    return count($this->toArray());
  }
}


