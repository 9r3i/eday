<div class="menu" id="menu">
<?php
use eday\site;
$menus=[
  ['javascript:void(0)',EDAY_ADMIN_USERNAME,'user-secret','#37b'],
  ['dashboard/home','Dashboard','dashboard'],
  ['post/all','All Posts','file-text-o'],
  ['post/add','New Post','edit',null,true],
  ['product/all','All Products','shopping-cart'],
  ['product/add','New Product','cart-plus','#7b3',true],
  ['menu/all','Menus','th-large'],
];
if(EDAY_ADMIN_TYPE=='admin'||EDAY_ADMIN_TYPE=='master'){
  $menus[]=['file/manager','File Manager','folder'];
  $menus[]=['app/all','Applications','puzzle-piece'];
  $menus[]=['user/all','All Users','user'];
  $menus[]=['option/settings','Settings','gear'];
}else{
  $menus[]=['user/edit/'.EDAY_ADMIN_ID,'My Account','user'];
}
$menus=array_merge($menus,[
  ['javascript:adminLogout()','Logout','sign-out','#b33'],
]);
foreach($menus as $menu){
  $current=$menu[0]==EDAY_ADMIN_PATH?'menu-each-current':'';
  $color=isset($menu[3])?$menu[3]:'#555';
  $padded=isset($menu[4])&&$menu[4]?'menu-each-padded':'';
  $href=preg_match('/^[a-z0-9]+\/[a-z0-9]+(\/[a-z0-9]+)*$/i',$menu[0])
    ?site::url.'?'.EDAY_ADMIN_KEY.'='.$menu[0]:$menu[0];
  echo '<a href="'.$href.'" title="'.$menu[1].'">'
    .'<div class="menu-each '.$padded.' '.$current.'">'
    .'<i class="fa fa-'.$menu[2].'" style="color:'.$color.';"></i>'
    .$menu[1].'</div></a>';
}
?>
<div class="menu-bottom-padded"></div>
</div>
<div class="menu-toggle" id="menu-toggle">
  <span class="menu-toggle-span">Menu Toggle</span>
</div>

<script type="text/javascript">
/* menu toggle */
var menu_toggle=gebi('menu-toggle')
if(menu_toggle){menu_toggle.onclick=function(e){
  var menu=gebi('menu');
  if(!menu){return false;}
  if(menu.style.left=='0px'){
    menu.style.left='-250px';
  }else{
    menu.style.left='0px';
  }
};}

/* menu on resize window */
W.onresize=function(){
  var menu=gebi('menu');
  var mleft=W.innerWidth>700?0:-250;
  if(menu){menu.style.left=mleft+'px';}
};

// adminLeftMenuMovable();

/* make left menu movable by touching-slide to right - [---UNSTABLE---] */
function adminLeftMenuMovable(){
  if(!W.hasOwnProperty('ontouchstart')){return false;}
  W.ontouchend=function(e){
    if(!W.LBM){return false;}
    var isHide=W.LBM.hide;
    var x=e.changedTouches?e.changedTouches[0].pageX:e.screenX;
    var left=(x-W.LBM.x)+W.LBM.l;
    W.LBM=false;
    if(!isHide){W.LBM.el.style.left='0px';}
    else if(left<-100){W.LBM.el.style.left='-250px';}
    W.LBM.el.style.left='0px';
  };
  W.ontouchmove=function(e){
    if(!W.LBM){return false;}
    var x=e.changedTouches?e.changedTouches[0].pageX:e.screenX;
    var left=(x-W.LBM.x)+W.LBM.l;
    if(left>=0&&!W.LBM.hide){
      W.LBM=false;
      W.LBM.el.style.left='0px';
      return true;
    }else if(left<-250&&W.LBM.hide){
      W.LBM.el.style.left='-250px';
      W.LBM=false;
      return true;
    }if(left<0){W.LBM.el.style.left=left+'px';}
  };
  W.ontouchstart=function(e){
    var el=gebi('menu');
    var x=e.changedTouches?e.changedTouches[0].pageX:e.screenX;
    var l=el.offsetLeft;
    if(l===0||x>10){
      if(el.style.left=='0px'&&x>250){
        W.LBM={x:x,l:l,el:el,hide:true};
      }return false;
    }W.LBM={x:x,l:l,el:el,hide:false};
  };
}
</script>


