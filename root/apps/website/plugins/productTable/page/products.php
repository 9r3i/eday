<?php
namespace productTablePage;
use \eday\site;
class products extends \webPlugin{
  protected $db;
  function __construct(){
    /* extends from webPlugin to get additional methods:
     * - path    = string of directory path
     * - url     = string of url path
     * - option  = mixed of option value; <plugin_name>/options.ini
     *             --> per section; default: config
     * - website = object of global website
     * - post    = object of global data post
     */
    parent::__construct('productTable');
    /* set database */
    $this->db=$this->website()->db();
  }
  public function testing($args=[]){
    $db=site::db();
    return $this->printOut([
      'site::db'=>$db,
      'site_tables'=>$db->query('show tables'),
      'ldb'=>$this->db,
      'tables'=>$this->db->show_tables(),
      'args'=>$args,
      'website'=>$this->website(),
      'constants'=>get_defined_constants(true)['user'],
      'classes'=>get_declared_classes()
    ]);
  }
  /* print out the arguments */
  public function printOut($args=[]){
    return '<pre>'.print_r([
      '$_args'=>$args,
      '$_GET'=>$_GET,
      '$_POST'=>$_POST,
      '$_SERVER'=>$_SERVER,
      '$_COOKIE'=>$_COOKIE,
    ],true).'</pre>';
  }
}
