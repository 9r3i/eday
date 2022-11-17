<?php
class webContact{
  const version='2.0.0';
  /* add inbox to admin menu */
  public function adminMenu(array $menu){
    /* menu add --> format menu --> path, name, icon, level */
    return array_merge([['plugin/webContact','Inbox','envelope',8]],$menu);
  }
  /* admin page */
  public function adminPage(){
    /* global website */
    global $website;
    /* get website database */
    $db=$website->db();
    /* prepare css link */
    $cssLink='<link href="'.WEBSITE_PLUGINS_PATH.'webContact/files/style.css" '
      .'type="text/css" rel="stylesheet" />';
    /* prepare data table */
    $table='web_contact';
    if(!in_array($table,$db->show_tables())){
      $db->create_table($table);
    }$select=$db->select($table);
    /* prepare content */
    $content='<div class="web-contact-review">';
    $next = (isset($_GET['next']))?$_GET['next']:0;
    foreach(array_reverse($select) as $sel){
      $content.='<div class="web-contact-review-each">'
        .'<div class="web-contact-review-content">'.$sel['content'].'</div>'
        .'<div class="web-contact-review-time">Sent on '
          .date('l, F jS, Y - H:i:s',$sel['time'])
          .'</div>'
        . '</div>';
    }$content.='</div>';
    /* return the content */
    return $content.$cssLink;
  }
  /* execute action of web contact */
  public function action(){
    /* check web contact send request */
    if(!isset($_POST['web-contact-send'])
      ||!isset($_POST['from_email'],$_POST['from_name'],$_POST['message'])
      ||!isset($_POST['sapaan'],$_POST['hp'],$_POST['cc'])){
      return false;
    }
    /* globalize $website and $post */
    global $website,$post;
    /* get uri option page and target */
    $uri=$this->getOption('page');
    $target=$this->getOption('target');
    /* check uri and compare with site request uri */
    if(!is_string($uri)
      ||$post->url.'.html'!=$uri){
      return false;
    }
    /* call website database */
    $ldb=$website->db();
    /* prepare table */
    $table='web_contact';
    if(!in_array($table,$ldb->show_tables())){
      $ldb->create_table($table);
    }
    /* set data as $_POST */
    $data=$_POST;
    /* prepare email header */
    $headers='From: '.$data['from_name'].' <'.$data['from_email'].'>'."\r\n";
    $headers.='MIME-Version: 1.0'."\r\n";
    $headers.='Content-type: text/html; charset=utf-8'."\r\n";
    /* Check the Cc mail */
    if(isset($data['cc'])){
      $headers.='Cc: '.$data['from_name'].' <'.$data['from_email'].'>'."\r\n";
    }
    /* prepare target mail sending */
    $to=is_string($target)?$target:'mail@'.$_SERVER['SERVER_NAME'];
    /* prepare mail subject */
    $subject=$this->getOption('subject');
    $subject=is_string($subject)?$subject:'Email from '.$_SERVER['SERVER_NAME'];
    /* prepare default email message */
    $message='<!DOCTYPE html><html lang="en-US"><head>'
      .'<meta content="text/html; charset=utf-8" http-equiv="content-type" />'
      .'<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible" />'
      .'<meta content="width=device-width, initial-scale=1" name="viewport" />'
      .'<title>'.$subject.' &#8213; Web Contact</title>'
      .'<meta content="9r3i" name="author" />'
      .'<meta content="https://github.com/9r3i" name="author-uri" />'
      .'<meta content="e-Day::webContact" name="generator" />'
      .'<style type="text/css">body{color:#333;font-family:Tahoma,Segoe UI,Arial;}</style>'
      .'</head><body><div style="color:#333;font-family:Tahoma,Segoe UI,Arial;">';
    /* prepare message format */
    $format=$this->getOption('format');
    $message_format=$this->messageFormat($format,$data);
    $message.=$message_format;
    /* append message footer */
    $message.='<p></p><p></p><hr />'
      .'<p>Sent on '.date('F, jS Y - H:i:s').' &middot; Website Contact</p>'
      .'<p>e-Day Framework &#8213; Powered by '
      .'<a href="https://github.com/9r3i" target="_blank" title="9r3i">9r3i</a></p>';
    /* closing message */
    $message.='</div></body></html>';
    /* insert data into database */
    $ldb->insert($table,[
      'from_name'=>$data['from_name'],
      'from_email'=>$data['from_email'],
      'content'=>$message_format
    ]);
    /* get output option */
    $output=$this->getOption('output');
    $output=is_string($output)?$output:'json';
    /* send the mail */
    if(@mail($to,$subject,$message,$headers)){
      $status=[
        'status'=>'OK',
        'message'=>'Message has been sent',
        'data'=>[
          'to'=>$to,
          'subject'=>$subject,
          'header'=>$headers,
          'content'=>$message,
          'data_request'=>$data,
        ]
      ];
      if($output=='json'){
        header('content-type: application/json');
        exit(@json_encode($status));
      }return $status;
    }
    /* error if not sent */
    $error=[
      'status'=>'error',
      'message'=>'Error: Failed to send the message.'
    ];
    if($output=='json'){
      header('content-type: application/json');
      exit(@json_encode($error));
    }return $error;
  }
  /* generate content of web contact -- replace entire post content */
  public function content($content){
    /* get uri option form */
    $uri=$this->getOption('form');
    /* check uri */
    if(!is_string($uri)){return $content;}
    /* compare with site request uri */
    global $post;
    if($post->url.'.html'!=$uri){return $content;}
    /* get form content */
    $image=$this->fileURL('contact.png');
    $form=@file_get_contents($this->filePath('form.html'));
    $form=str_replace('__WEB_CONTACT_IMAGE__',$image,$form);
    /* get uri option page */
    $actionURI=$this->getOption('page');
    /* prepare another requirement */
    $cssFile=$this->fileURL('style.css');
    $jsFile=$this->fileURL('script.js');
    $css='<link rel="stylesheet" href="'.$cssFile.'?v=2.0.0" type="text/css" media="print,screen" />';
    $js='<script type="text/javascript" src="'.$jsFile.'"></script>';
    $jsInline="<script type=\"\">var WEB_CONTACT_ACTION_URI='{$actionURI}';</script>";
    /* return replacement content */
    return "{$form} {$jsInline} {$css} {$js}";
  }
  /* message format */
  public function messageFormat(string $format='',array $data=[]){
    $ptrn='/(data\[([a-z0-9_-]+)\])/i';
    preg_match_all($ptrn,$format,$akur);
    $ndata=[];$rdata=[];
    foreach($akur[2] as $k=>$key){
      if(isset($data[$key])){
        $rdata[$k]=$akur[0][$k];
        $ndata[$k]=$data[$key];
      }
    }return str_replace($rdata,$ndata,$format);
  }
  /* get options */
  public function getOption($key=null){
    $file=$this->filePath('options.ini');
    $ini=@parse_ini_file($file);
    $ini=is_array($ini)?$ini:[];
    if(is_string($key)){
      return isset($ini[$key])?$ini[$key]:false;
    }return $ini;
  }
  /* get file content */
  public function filePath(string $file){
    $basePath=WEBSITE_PLUGINS_DIRECTORY.'webContact/files/';
    return $basePath.$file;
  }
  /* get file url of the plugin */
  public function fileURL(string $file){
    $baseURL=WEBSITE_PLUGINS_PATH.'webContact/files/';
    return $baseURL.$file;
  }
}


