<?php
use \eday\site;
class productTable extends webPlugin{
  const version='2.0.1';
  protected $db;
  protected $table;
  protected $ajaxError=false;
  protected $cookie;
  protected $user;
  protected $methods;
  protected $post;
  protected $ajaxQuery;
  function __construct(){
    /* extends from webPlugin to get additional methods:
     * - path    = string of directory path
     * - url     = string of url path
     * - option  = mixed of option value; <plugin_name>/options.ini
     *             --> per section; default: config
     * - website = object of global website
     * - post    = object of global data post
     */
    parent::__construct(__CLASS__);
    /* set database */
    $this->db=$this->website()->db();
    /* set post */
    $this->post=$this->post();
    /* set cookie */
    $this->cookie=new dataObject($_COOKIE);
    /* register ajax methods and minimum user level */
    $this->methods=[
      'products'=>2,
      'new'=>2,
      'edit'=>2,
    ];
    /* check database table */
    $this->table='product_table';
    if(!in_array($this->table,$this->db->show_tables())){
      $this->db->create_table($this->table);
    }
    /* ajax query */
    $this->ajaxQuery=new dataObject([
      'key'=>'EdayProductTable',
      'value'=>'ajax',
    ]);
  }
  /* add product register to admin menu */
  public function adminMenu(array $menu){
    /* menu add --> format menu --> path, name, icon, level */
    return array_merge([
      [
        'plugin/productTable/products', // admin path
        'My Products', // name and title
        'shopping-cart', // icon
        2, // level 2 = member
      ],
      [
        'plugin/productTable/orders', // admin path
        'My Orders', // name and title
        'shopping-cart', // icon
        2, // level 2 = member
      ],
      [
        'plugin/productTable/store', // admin path
        'My Store', // name and title
        'shopping-cart', // icon
        2, // level 2 = member
      ],
    ],$menu);
  }
  /* product register in admin page */
  public function adminPage($path=''){
    /* parse arguments */
    $req=$this->parseArgs($path);
    /* load ajax content */
    if($req->page=='ajax'){
      if(true){
        @require_once($this->path('eday.product.table.php'));
        return new EdayProductTable($this->db);
      }
      /* old ajax return */
      if(!array_key_exists($req->method,$this->methods)
        ||USER_LEVEL<$this->methods[$req->method]){
        return $this->errorResult('Unauthorized.');
      }return $this->ajax($req->method,$_POST);
    }
    /* load html content */
    $htmlHeader=$this->loadFile('html/_header.html');
    $htmlFooter=$this->loadFile('html/_footer.html');
    $htmlContents=[
      'products'=>$this->loadFile('html/products.html'),
      'newProduct'=>$this->loadFile('html/newProduct.html'),
      'editProduct'=>$this->loadFile('html/editProduct.html'),
      'orders'=>$this->loadFile('html/orders.html'),
      'store'=>$this->loadFile('html/store.html'),
    ];
    $temp='';
    foreach($this->faList() as $fa){
      $temp.='<div><i class="fa fa-'.$fa.'"></i> '.$fa.'</div>';
    }
    $htmlContents['fa']=$temp;
    //return $this->printOut([]);
    /* webAdmin token */
    $token=isset($_COOKIE['webAdmin'])?$_COOKIE['webAdmin']:null;
    /* return all output */
    return ''
      .$htmlHeader
      .$this->loadPathCSS('css/font-awesome.min.css')
      .$this->loadPathCSS('css/sweetalert.min.css')
      .$this->loadPathCSS('css/tiny-slider.min.css')
      .$this->loadPathCSS('css/admin.css')
      .$this->loadPathCSS('css/product.table.css')
      .$this->loadPathJS('js/sweetalert.min.js')
      .$this->loadPathJS('js/tiny-slider.min.js')
      .$this->loadPathJS('js/events-1.1.0.min.js')
      .$this->loadPathJS('js/dateSelect.min.js')
      .$this->loadPathJS('js/pictureSelect.min.js')
      .$this->loadPathJS('js/product.table.js')
      .$this->loadPathJS('html/js/store.js')
      .$this->loadPathJS('html/js/orders.js')
      .$this->loadPathJS('html/js/products.js')
      .$this->loadPathJS('html/js/newProduct.js')
      .$this->loadPathJS('html/js/editProduct.js')
      .'<script type="text/javascript">'
        .'const WEBSITE_ADDRESS="'.EDAY_ADDR.'";'
        .'const ADMIN_KEY="'.$this->website()->site()->adminKey.'";'
        .'const ADMIN_PATH="'.$this->website()->site()->webPath.'";'
        .'const ADMIN_TOKEN='.json_encode($token).';'
        .'const HTML_CONTENTS='.json_encode($htmlContents).';'
        .'const PRODUCT_URL="'.$this->option('product_url').'";'
        .'const REQUEST_PAGE="'.$req->page.'";'
        .'new productTable;'
        .';</script>'
      .$htmlFooter
      .'';
  }
  /* action */
  public function action(){
    if(isset($_GET['EdayProductTable'])
      &&$_GET[$this->ajaxQuery->key]==$this->ajaxQuery->value){
      @require_once($this->path('eday.product.table.php'));
      return new EdayProductTable($this->db);
    }
    if($this->post->type=='product'){
      header('Location: '.$this->option('product_url')
        .'?id='.$this->post->url);
      exit;
    }
    if(EDAY_PATH=='home.html'
      &&(!isset($_GET['id'])
        &&!isset($_GET['tag'])
        &&!isset($_GET['store'])
        &&!isset($_GET['page'])
        )){
      header('Location: '.EDAY_ADDR);
      exit;
    }
    global $isProductTable,$post;
    $isProductTable=false;
    if($this->post->url.'.html'==$this->option('product_url')
      ||EDAY_PATH==$this->option('product_url')){
      $isProductTable=true;
      $this->isProductTable=true;
      if(isset($_GET['id'])){
        $where='access=public&status=publish&type=product'
          .'&template=standard&url='.$_GET['id'];
        $select=$this->db->select('posts',$where);
        if(is_array($select)&&isset($select[0])){
          $post=new dataObject($select[0]);
          $this->post=$post;
        }
      }elseif(isset($_GET['page'])){
        $where='access=public&status=publish&type=page'
          .'&template=standard&url='.$_GET['page'];
        $select=$this->db->select('posts',$where);
        if(is_array($select)&&isset($select[0])){
          $post=new dataObject($select[0]);
          $this->post=$post;
        }
      }
    }
    
    /**
    $where='access=public&status=publish&type=product'
      .'&template=standard';
    //$where='access=public&status=publish&author=admin';
    $dx=implode('',explode('.',microtime(true)));
    $bx=base_convert($dx,10,36);
    $token='webAdmin-lBBoeqZ9OV7UmOxs2FwIQQ';
    // http://127.0.0.1:9301/product.html?product_id=1234567&test=testing
    exit($this->printOut([
      'product_url'=>$this->option('product_url'),
      'EDAY_PATH'=>EDAY_PATH,
      'post_url'=>$this->post->url,
      //$this->post(),
      //@json_encode($this->post->toArray()),
      //$this->db->update('posts','aid=14',['author'=>'admin']),
      'db_error'=>$this->db->error,
      //$this->db->select('review_table',$where),
      //'posts'=>$this->db->select('posts',$where),
      //$this->db->delete_table('category_table'),
      'tables'=>$this->db->show_tables(),
      //$this->db->delete('category_table','parent=0'),
      //'options'=>$this->db->select('options'),
      //$this->db->select('users'),
      //$this->db->select('logs','token='.$token),
      //'order_delete'=>$this->db->delete('order_table','buyer_id=1'),
      'order_select'=>$this->db->select('order_table'),
      'db'=>$this->db,
    ]));
    //*/
  }
  /* generate content of product table */
  public function content($content){
    global $isProductTable;
    if($isProductTable){
      $content='';
    }
    return $content;
  }
  /* header */
  public function header($header){
    global $isProductTable;
    if($isProductTable){
      $html=[
        'single'=>$this->loadFile('html/public.product.html'),
        'bulk'=>$this->loadFile('html/public.products.html'),
        'preload'=>'<div class="preload-outer">'
                  .'<div class="preload-inner"></div></div>',
        'white'=>$this->loadFile('images/white.1.txt'),
      ];
      $header.=''
      .$this->loadPathCSS('css/font-awesome.min.css')
      .$this->loadPathCSS('css/sweetalert.min.css')
      .$this->loadPathCSS('css/tiny-slider.min.css')
      .$this->loadPathCSS('css/product.table.css')
      .$this->loadPathJS('js/sweetalert.min.js')
      .$this->loadPathJS('js/tiny-slider.min.js')
      .$this->loadPathJS('js/product.table.public.js')
      .'<script type="text/javascript">'
        .'const PRODUCT_URL="'.$this->option('product_url').'";'
        .'const PRODUCT_DATA='.@json_encode($this->post->toArray()).';'
        .'const HTML_DATA='.@json_encode($html).';'
        .'const AJAX_QUERY='.@json_encode($this->ajaxQuery).';'
        .'</script>'
      .'';
    }
    return $header;
  }
  /* footer */
  public function footer($footer){
    global $isProductTable;
    if($isProductTable){
      $footer.=''
      .'<style type="text/css">'
      .'#website-content{display:none;}'
      .'</style>'
      .'<script type="text/javascript">'
       .'setTimeout(()=>{new productTablePublic;},500);'
        .'</script>'
      .'';
    }
    return $footer;
  }
  /* exec page */
  protected function executePage(string $path){
    /* parse arguments */
    $req=$this->parseArgs($path);
    /* check page file */
    $file=$this->path('page/'.$req->page.'.php');
    if(!is_file($file)){
      return 'Error: Page is not found.';
    }
    /* load page file */
    @require_once($file);
    /* call the class */
    $pcname="productTablePage\\".$req->page;
    $pobj=new $pcname;
    /* check method */
    if(!method_exists($pobj,$req->method)
      ||!(new ReflectionMethod($pobj,$req->method))->isPublic()){
      return 'Error: Invalid method.';
    }
    /* execute requested method */
    $pexec=\call_user_func_array([$pobj,$req->method],[$req->args]);
    /* return the result */
    return $pexec;
  }
  /* ajax */
  protected function ajax(string $method,array $data=[]){
    $out=@\call_user_func_array([$this,$method],[new dataObject($data)]);
    if(!$out){
      return $this->errorResult($this->ajaxError);
    }return $this->result($out);
  }
  /* error set to ajaxError - for ajax */
  protected function error($s=null){
    $this->ajaxError=$s;
    return false;
  }
  /* result - for ajax */
  protected function result($s=null){
    $s=is_string($s)?$s:@json_encode($s);
    header('Content-Type: text/plain');
    header('Content-Length: '.strlen($s));
    exit($s);
  }
  /* error result - for ajax */
  protected function errorResult($s=null){
    $s=is_string($s)?$s:'Unknown.';
    return $this->result('Error: '.$s);
  }
  /* load file content */
  protected function loadFile(string $file):string{
    $get=@file_get_contents($this->path($file));
    return $get?$get:'';
  }
  /* load css by url path */
  protected function loadPathCSS(string $file):string{
    return '<link rel="stylesheet" href="'
      .$this->url($file).'" type="text/css" />';
  }
  /* load js by url path */
  protected function loadPathJS(string $file):string{
    return '<script src="'.$this->url($file)
      .'" type="text/javascript"></script>';
  }
  /* parse argument path */
  protected function parseArgs(string $path){
    $ex=explode('/',$path);
    return new dataObject([
      'page'=>isset($ex[1])?$ex[1]:'',
      'method'=>isset($ex[2])?$ex[2]:'',
      'args'=>array_slice($ex,3),
    ]);
  }
  /* new slug */
  private function newSlug():string{
    $bx=1600110939;
    $cx=microtime(true)-$bx;
    $dx=implode('',explode('.',$cx));
    $ex=mt_rand(pow(10,strlen($dx)-1),$dx);
    $xx=base_convert($ex,10,36);
    return strrev($xx);
  }
  /* default columns for tables *//*
  rating (?)
  reviews (array) --> review_id
  vouchers (array) --> voucher_id
  */
  /* print out the arguments */
  protected function printOut($args=[]){
    return '<pre>'.print_r([
      '$_args'=>$args,
      '$_GET'=>$_GET,
      '$_POST'=>$_POST,
      '$_SERVER'=>$_SERVER,
      '$_COOKIE'=>$_COOKIE,
      'CONSTANTS'=>get_defined_constants(true)['user'],
    ],true).'</pre>';
  }
  /* font-awesome list */
  protected function faList(){
    return ["medium","subway","train","viacoin","bed","hotel","user-times","user-plus",
      "server","whatsapp","pinterest-p","facebook-official","neuter","mars-stroke-h",
      "mars-stroke-v","mars-stroke","venus-mars","mars-double","venus-double",
      "transgender-alt","transgender","mercury","mars","venus","heartbeat",
      "street-view","motorcycle","user-secret","ship","diamond","cart-arrow-down",
      "cart-plus","skyatlas","simplybuilt","shirtsinbulk","sellsy","leanpub","forumbee",
      "dashcube","connectdevelop","buysellads","meanpath","ils","sheqel","shekel","cc",
      "angellist","ioxhost","bus","bicycle","toggle-on","toggle-off","lastfm-square",
      "lastfm","line-chart","pie-chart","area-chart","birthday-cake","paint-brush",
      "eyedropper","at","copyright","trash","bell-slash-o","bell-slash","cc-stripe",
      "cc-paypal","cc-amex","cc-discover","cc-mastercard","cc-visa","google-wallet",
      "paypal","calculator","wifi","newspaper-o","yelp","twitch","slideshare",
      "plug","binoculars","tty","futbol-o","soccer-ball-o","bomb","share-alt-square",
      "share-alt","sliders","paragraph","header","circle-thin","genderless","history",
      "paper-plane-o","send-o","paper-plane","send","weixin","wechat","qq","tencent-weibo",
      "hacker-news","git","git-square","empire","ge","rebel","ra","circle-o-notch",
      "life-ring","support","life-saver","life-buoy","life-bouy","jsfiddle","codepen",
      "vine","file-code-o","file-video-o","file-movie-o","file-audio-o","file-sound-o",
      "file-archive-o","file-zip-o","file-image-o","file-picture-o","file-photo-o",
      "file-powerpoint-o","file-excel-o","file-word-o","file-pdf-o","database","soundcloud",
      "deviantart","spotify","tree","taxi","cab","car","automobile","recycle","steam-square",
      "steam","behance-square","behance","cubes","cube","spoon","paw","child","building","fax",
      "language","joomla","drupal","pied-piper-alt","pied-piper","digg","delicious",
      "stumbleupon","stumbleupon-circle","reddit-square","reddit","google","yahoo",
      "graduation-cap","mortar-board","university","bank","institution","openid","wordpress",
      "envelope-square","slack","space-shuttle","plus-square-o","try","turkish-lira",
      "vimeo-square","wheelchair","dot-circle-o","caret-square-o-left","toggle-left",
      "arrow-circle-o-left","arrow-circle-o-right","stack-exchange","pagelines",
      "renren","weibo","vk","bug","archive","moon-o","sun-o","gratipay","gittip",
      "male","female","trello","foursquare","skype","dribbble","linux","android",
      "windows","apple","long-arrow-right","long-arrow-left","long-arrow-up",
      "long-arrow-down","tumblr-square","tumblr","bitbucket-square","bitbucket","adn",
      "flickr","instagram","stack-overflow","dropbox","youtube-play","xing-square","xing",
      "youtube","youtube-square","thumbs-down","thumbs-up","sort-numeric-desc",
      "sort-numeric-asc","sort-amount-desc","sort-amount-asc","sort-alpha-desc",
      "sort-alpha-asc","file-text","file","btc","bitcoin","krw","won","rub","rouble",
      "ruble","jpy","yen","rmb","cny","inr","rupee","usd","dollar","gbp","eur","euro",
      "caret-square-o-right","toggle-right","caret-square-o-up","toggle-up",
      "caret-square-o-down","toggle-down","compass","share-square","external-link-square",
      "pencil-square","check-square","level-down","level-up","minus-square-o","minus-square",
      "ticket","play-circle","rss-square","ellipsis-v","ellipsis-h","bullseye","unlock-alt",
      "anchor","css3","html5","chevron-circle-down","chevron-circle-up","chevron-circle-right",
      "chevron-circle-left","maxcdn","rocket","fire-extinguisher","calendar-o","shield",
      "microphone-slash","microphone","puzzle-piece","eraser","subscript","superscript",
      "exclamation","info","question","chain-broken","unlink","code-fork","crop",
      "location-arrow","star-half-o","star-half-full","star-half-empty","reply-all",
      "mail-reply-all","code","terminal","flag-checkered","flag-o","keyboard-o","gamepad",
      "meh-o","frown-o","smile-o","folder-open-o","folder-o","github-alt","reply","mail-reply",
      "circle","spinner","quote-right","quote-left","circle-o","mobile","mobile-phone",
      "tablet","laptop","desktop","angle-down","angle-up","angle-right","angle-left",
      "angle-double-down","angle-double-up","angle-double-right","angle-double-left",
      "plus-square","h-square","beer","fighter-jet","medkit","ambulance","hospital-o",
      "building-o","file-text-o","cutlery","coffee","bell-o","suitcase","stethoscope",
      "user-md","cloud-upload","cloud-download","exchange","lightbulb-o","clipboard",
      "paste","umbrella","sitemap","bolt","flash","comments-o","comment-o",
      "tachometer","dashboard","gavel","legal","undo","rotate-left","linkedin",
      "envelope","sort-asc","sort-up","sort-desc","sort-down","sort","unsorted",
      "columns","caret-right","caret-left","caret-up","caret-down","money","google-plus",
      "google-plus-square","pinterest-square","pinterest","truck","magic","table",
      "underline","strikethrough","list-ol","list-ul","bars","reorder","navicon","square",
      "floppy-o","save","paperclip","files-o","copy","scissors","cut","flask","cloud","link",
      "chain","users","group","arrows-alt","briefcase","filter","tasks","wrench","globe",
      "arrow-circle-down","arrow-circle-up","arrow-circle-right","arrow-circle-left",
      "hand-o-down","hand-o-up","hand-o-left","hand-o-right","certificate","bell","bullhorn",
      "hdd-o","rss","credit-card","unlock","github","facebook","facebook-f","twitter",
      "phone-square","bookmark-o","square-o","phone","lemon-o","upload","github-square",
      "trophy","sign-in","external-link","thumb-tack","linkedin-square","sign-out","heart-o",
      "star-half","thumbs-o-down","thumbs-o-up","comments","cogs","gears","key","camera-retro",
      "facebook-square","twitter-square","bar-chart","bar-chart-o","arrows-h","arrows-v",
      "folder-open","folder","shopping-cart","retweet","chevron-down","chevron-up","magnet",
      "comment","random","calendar","plane","exclamation-triangle","warning","eye-slash","eye",
      "fire","leaf","gift","exclamation-circle","asterisk","minus","plus","compress","expand",
      "share","mail-forward","arrow-down","arrow-up","arrow-right","arrow-left","ban",
      "check-circle-o","times-circle-o","crosshairs","info-circle","question-circle",
      "check-circle","times-circle","minus-circle","plus-circle","chevron-right",
      "chevron-left","eject","step-forward","fast-forward","forward","stop","pause","play",
      "backward","fast-backward","step-backward","arrows","check-square-o","share-square-o",
      "pencil-square-o","edit","tint","adjust","map-marker","pencil","picture-o","image",
      "photo","video-camera","indent","outdent","dedent","list","align-justify",
      "align-right","align-center","align-left","text-width","text-height","italic","bold",
      "font","camera","print","bookmark","book","tags","tag","barcode","qrcode","volume-up",
      "volume-down","volume-off","headphones","flag","lock","list-alt","refresh","repeat",
      "rotate-right","play-circle-o","inbox","arrow-circle-o-up","arrow-circle-o-down",
      "download","road","clock-o","file-o","home","trash-o","cog","gear","signal",
      "power-off","search-minus","search-plus","times","close","remove","check","th-list",
      "th","th-large","film","user","star-o","star","heart","envelope-o","search",
      "music","glass"
    ];
  }
}
