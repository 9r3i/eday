<?php
/* check the engine */
if(!defined('EDAY')){
  header('content-type:text/plain',true,401);
  exit('Error: 401 Unauthorized.');
}

/* testing script *
header('content-type:text/plain');
//$db=site::db();
//$select=$db->query('select * from users');
//print_r($select);exit;
//print_r(get_defined_constants(true)['user']);exit;
//print_r(site::config());
//$select=$this->db->select('users');
//$select=$this->db->select('menu','name=Admin');
//$select=$this->db->select('posts','url=personal-coaching');
//print_r($select);
print_r($this);
exit;
//*/

/* load html header */
$this->loadPage('header');

/* print content */
echo $this->content;

/* load html footer */
$this->loadPage('footer');


