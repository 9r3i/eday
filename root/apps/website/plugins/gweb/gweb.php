<?php
class gweb{
  function header(string $head){
    $ini=WEBSITE_PLUGINS_DIRECTORY.'gweb/about.ini';
    $data=@parse_ini_file($ini,true);
    $head.='<meta name="google-site-verification" content="'
      .$data['option']['code'].'" />';
    return $head;
  }
}