<?php
class hitCounter extends webPlugin{
  const version='2.0.0';
  public function __construct(){
    /* extends from webPlugin to get additional methods:
     * - path    = string of directory path
     * - url     = string of url path
     * - option  = mixed of option value; <plugin_name>/options.ini
     *             --> per section; default: config
     * - website = object of global website
     * - post    = object of global data post
     */
    parent::__construct(__CLASS__);
  }
  /* add statistic to admin menu */
  public function adminMenu(array $menu){
    /* menu add --> format menu --> path, name, icon, level */
    return array_merge([['plugin/hitCounter','Statistic','line-chart',8]],$menu);
  }
  /* action method */
  public function adminPage(){
    /* globalize website */
    global $website;
    $ldb=$website->db();
    /* check database table */
    $table='hit_counter';
    if(!in_array($table,$ldb->show_tables())){
      $ldb->create_table($table);
    }
    $content='<style type="text/css">'
      .@file_get_contents(WEBSITE_PLUGINS_DIRECTORY.'hitCounter/style-admin.css')
      .'</style>';
    $select=$ldb->select($table,'type=counter');
    $count=(isset($select[0]['total']))?$select[0]['total']:0;
    $count=number_format($count,0,'.',',');;
    $content.='<div class="visitors">Total Hit: '.$count.'</div>';
    /* mserver log */
    $file=INDEX_SERVER.'mserver.log';
    $mserverLog='';
    if(is_file($file)){
      $mserverLog=@file_get_contents($file);
      $mserverLog=is_string($mserverLog)?$mserverLog:'';
    }
    /* statistic */
    $content.='<div id="dashboard-statistic" class="statistic"></div>';
    $content.='<script type="text/javascript">'
      .'var MSERVER_LOG=`'.$mserverLog.'`;'
      .@file_get_contents(WEBSITE_PLUGINS_DIRECTORY.'hitCounter/script-admin.js')
      .'</script>';
    /* return the content */
    return $content;
  }
  /* action method */
  public function action(){
    /* prepare table name */
    $table='hit_counter';
    /* prepare database */
    $db=$this->website()->db();
    /* get tables */
    $tables=$db->show_tables();
    /* set default value */
    $total=0;
    /* check table */
    if(!in_array($table,$tables)){
      /* create new table */
      $db->create_table($table);
      /* insert new table */
      $db->insert($table,[
        'type'=>'counter',
        'total'=>$total,
      ]);
    }else{
      $select=$db->select($table);
      $total=isset($select[0],$select[0]['total'])
        ?$select[0]['total']:$total;
    }
    /* update table */
    $total++;
    $update=$db->update($table,'type=counter',[
      'total'=>$total,
    ]);
    /* check for get total */
    if(isset($_GET['hit-counter-get-total'])){
      exit('OK');
    }
    /* return as true */
    return true;
  }
  /* print output */
  public function sidebar(string $content){
    /* prepare pattern */
    $ptrn='/@\[hit_counter\]/i';
    /* check content */
    if(!preg_match($ptrn,$content)){
      return $content;
    }
    /* replace content */
    return preg_replace_callback($ptrn,function($akur){
      return $this->sidebarReplace($akur);
    },$content);
  }
  /* sidebar replacement */
  protected function sidebarReplace($akur=''){
    /* prepare table name */
    $table='hit_counter';
    /* prepare database */
    $db=$this->website()->db();
    /* set default value */
    $total='Error: Data is not found.';
    /* select counter */
    $select=$db->select($table,'type=counter');
    if(isset($select[0],$select[0]['total'])){
      $total=$select[0]['total'];
    }
    /* prepare style and script */
    $styleContent=@file_get_contents($this->path('style.css'));
    $scriptContent=@file_get_contents($this->path('script.js'));
    /* prepare output */
    $print='<style tyle="text/css">'.$styleContent.'</style>'
      .'<script type="text/javascript">'.$scriptContent.'</script>'
      .'<div id="hit_counter_sidebar" data-total="'.$total.'">'
      .'<div style="clear:both;"></div>'
      .'<div class="hit-counter-content" title="Total Hit: '.$total.'">'
      .'<div class="hit-counter-label">Hit Counter:</div>'
      .'<div class="hit-counter-digit">'.$total.'</div>'
      .'</div><div style="clear:both;"></div></div>';
    /* return the output */
    return $print;
  }
}


