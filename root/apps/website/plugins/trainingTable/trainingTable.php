<?php
use \eday\site;
class trainingTable{
  const version='2.0.1';
  /* add training register to admin menu */
  public function adminMenu(array $menu){
    /* menu add --> format menu --> path, name, icon, level */
    return array_merge([['plugin/trainingTable','Training Register','',8]],$menu);
  }
  /* training register in admin page */
  public function adminPage(){
    /* globalize website */
    global $website;
    $ldb=$website->db();
    /* check database table */
    $table='daftar_training';
    if(!in_array($table,$ldb->show_tables())){
      $ldb->create_table($table);
    }
    $data=$ldb->select($table);
    $i=count($data);
    $content='';
    while($i--){
      $content.='<div class="training-register-each">'.$data[$i]['content'].'</div>';
    }
    $content.='<style type="text/css">'
      .@file_get_contents(WEBSITE_PLUGINS_DIRECTORY.'trainingTable/style.css')
      .'</style>';
    return $content;
  }
  /* generate action of training table -- registration submit */
  public function action(){
    /* check query get */
    if(!isset($_GET['training-table-submit-form'])){
      return false;
    }
    /* set default header */
    header('content-type:application/json;charset=utf-8;');
    /* set default output */
    $result=[
      'status'=>'error',
      'message'=>'require post method',
    ];
    /* check form */
    if(!isset($_POST['message'],$_POST['title'],$_POST['trainer'])
      ||!isset($_POST['date'],$_POST['mobile'],$_POST['name'],$_POST['email'])){
      $result['message']='invalid registration form';
      exit(@json_encode($result));
    }
    /* prepare email content */
    $headers = 'From: '.$_POST['name'].' <'.$_POST['email'].'>'."\r\n";
    $headers .= 'MIME-Version: 1.0'."\r\n";
    $headers .= 'Content-type: text/html; charset=utf-8'."\r\n";
    $subject = 'Training Registration from Website';
    $to = 'dewiindrayani65@gmail.com';
    $message = '<div><hr /></div>';
    $message .= '<div>Training: '.$_POST['title'].'</div>';
    $message .= '<div>Tanggal: '.$_POST['date'].'</div>';
    $message .= '<div>Trainer: '.$_POST['trainer'].'</div>';
    $message .= '<div>Sent at: '.date('d-m-Y H:i').'</div>';
    $message .= '<div><hr /></div>';
    $message .= '<div>Nama: '.$_POST['name'].'</div>';
    $message .= '<div>Email: '.$_POST['email'].'</div>';
    $message .= '<div>HP: '.$_POST['mobile'].'</div>';
    $message .= '<div>Pesan: '.nl2br($_POST['message']).'</div>';
    $message .= '<div><hr /></div>';
    $message .= '<div>Powered by <a href="https://github.com/9r3i">9r3i</a></div>';
    /* globalize website */
    global $website;
    $ldb=$website->db();
    /* check database table */
    $table='daftar_training';
    if(!in_array($table,$ldb->show_tables())){
      $ldb->create_table($table);
    }
    /* insert data */
    $ldb->insert($table,[
      'from_name'=>$_POST['name'],
      'from_email'=>$_POST['email'],
      'content'=>$message
    ]);
    /* send the email */
    if(@mail($to,$subject,$message,$headers)){
      $result=[
        'status'=>'OK',
        'message'=>'message sent',
      ];
    }else{
      $result=[
        'status'=>'error',
        'message'=>'cannot sent the message',
      ];
    }exit(@json_encode($result));
  }
  /* generate content of training table */
  public function content($content){
    $ptrn='/@\[training_table\]/';
    if(!preg_match($ptrn,$content)){return $content;}
    return preg_replace_callback($ptrn,function($akur){
      /* get databse LDB */
      global $website;
      $ldb=$website->db();
      $select=$ldb->select('posts','type=training&access=public&status=publish');
      /* initailize result, training and json */
      $result=[];$training=[];$json=[];
      /* table header */
      $result[]='<div class="training-table-parent-element">';
      $result[]='<table class="training-table" cellspacing="0">';
      $result[]='<thead><tr class="training-table-head">'
        .'<th class="training-table-head-title">Topic</th>'
        .'<th class="training-table-head-date">Tanggal</th>'
        .'<th class="training-table-head-price">Investasi</th>'
        .'<th class="training-table-head-trainer">Pembicara</th>'
        .'<th class="training-table-head-place">Tempat</th>'
        .'<th class="training-table-head-reg">Formulir</th></tr></thead><tbody>';
      /* parse and order by training time and aid */
      foreach($select as $sel){
        $training_time=$this->getTime($sel['start']);
        $sel['training_time_new']=$training_time;
        $training[$training_time.'-'.$sel['aid']]=$sel;
      }ksort($training);
      /* add search input */
      $result[]='<tr class="training-table-ganjil"><td>'
        .'<input type="text" id="training_table_input_title" '
          .'class="training-table-input" placeholder="Topic" />'
        .'</td><td>'
        .'<input type="text" id="training_table_input_date" '
          .'class="training-table-input" placeholder="Date" />'
        .'</td><td>'
        .'<input type="text" id="training_table_input_price" '
          .'class="training-table-input" placeholder="Price" />'
        .'</td><td>'
        .'<input type="text" id="training_table_input_trainer" '
          .'class="training-table-input" placeholder="Trainer" />'
        .'</td><td>'
        .'<input type="text" id="training_table_input_place" '
          .'class="training-table-input" placeholder="Place" />'
        .'</td><td></td></tr>';
      /* prepare json while pushing table */
      foreach($training as $time=>$train){
        /* get held time */
        $until=$this->getTime($train['end']);
        if($until<time()){continue;}
        $json[$time]=$train;
        $json[$time]['training_start']=$this->toTanggal($train['start']);
        $json[$time]['training_end']=$this->toTanggal($train['end']);
        /* prepare tanggal */
        $tanggal=$this->toTanggal($train['start']);
        if($train['start']!=$train['end']){
          $tanggal.='<br />sampai<br />'.$this->toTanggal($train['end']);
        }
        /* push result */
        $result[] = '<tr id="tt_'.$time.'">'
          .'<td class="training-table-title">'
            .'<a href="'.site::url.$train['url'].'.html" title="'.$train['title'].'">'
            .$train['title'].'</a></td>'
          .'<td class="training-table-date">'.$tanggal.'</td>'
          .'<td class="training-table-price">'.$train['price'].'</td>'
          .'<td class="training-table-trainer">'.$train['trainer'].'</td>'
          .'<td class="training-table-place">'.$train['place'].'</td>'
          .'<td class="training-table-reg">'
            .'<button class="training-table-reg-button" title="Click to register" data-title="'
            .htmlspecialchars($train['title']).'" data-date="'
            .$this->toTanggal($train['start']).'" data-trainer="'
            .htmlspecialchars($train['trainer']).'" '
            .'onclick="training_table_reg(this);">Daftar</button></td>'
          .'</tr>';
      }$result[]='</tbody></table></div>';
      /* add script and style */
      $result[]='<link rel="stylesheet" href="'.WEBSITE_PLUGINS_PATH
        .'trainingTable/style.css?v=2.0.0" type="text/css" />';
      $result[]='<script language="javascript" type="text/javascript">'
        .'var TRAINING_TABLE_POSTS = '.json_encode($json).';</script>';
      $result[]='<script language="javascript" src="'.WEBSITE_PLUGINS_PATH
        .'trainingTable/training.js" type="text/javascript"></script>';
      /* return result as string */
      return implode($result);
    },$content);
  }
  /* date to tanggal (indonesia) */
  public function toTanggal($string){
    $days=['Minggu','Senin','Selasa','Rabu','Kamis','Jum\'at','Sabtu'];
    $months=[
      '','Januari','Februari','Maret','April',
      'Mei','Juni','Juli','Agustus','September',
      'Oktober','November','Desember'
    ];
    $time=$this->getTime($string);
    $hari=$days[date('w',$time)];
    $tanggal=date('j',$time);
    $bulan=$months[date('n',$time)];
    $tahun=date('Y',$time);
    return "$hari, $tanggal $bulan $tahun";
  }
  /* get time from string */
  public function getTime($str=''){
    return @strtotime((string)$str);
  }
}


