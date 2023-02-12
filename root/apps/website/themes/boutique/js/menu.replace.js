/* menu.replace.js */
function MenuReplace(){
this.version='1.0.0';
/* big variable */
this.MENU_WIDTH=null;
/* initialize menu */
this.init=function(){
  /* get elements */
  var button=document.getElementById('menu-button'),
  menu=document.getElementById('menu'),
  _menu=this;
  if(!button||!menu){return false;}
  /* get menu width */
  if(!this.MENU_WIDTH){
    this.MENU_WIDTH=menu.offsetWidth+360;
  }
  /* button click event */
  button.onclick=this.menuToggle;
  /* add on resize event */
  WINDOW_EVENTS.onresize.push(function(){
    _menu.menuOnResize();
  });
  /* re-calibrate menu on resize */
  return this.menuOnResize();
};
/* menu hide */
this.menuHide=function(){
  /* get elements */
  var menu=document.getElementById('menu');
  var shade=document.getElementById('menu-shadow');
  var isOverflow=menu.classList.contains('menu-overflow');
  if(!shade||!menu||!isOverflow){return false;}
  /* get elements */
  document.body.classList.remove('dont-scroll');
  menu.classList.remove('menu-overflow-show');
  shade.parentElement.removeChild(shade);
  return true;
};
/* menu toggle */
this.menuToggle=function(){
  /* get elements */
  var button=document.getElementById('menu-button');
  var menu=document.getElementById('menu');
  var shade=document.getElementById('menu-shadow');
  var isOverflow=menu.classList.contains('menu-overflow');
  if(!button||!menu||!isOverflow){return false;}
  /* check for overflow */
  if(menu.classList.contains('menu-overflow-show')){
    document.body.classList.remove('dont-scroll');
    menu.classList.remove('menu-overflow-show');
    if(shade){
      shade.parentElement.removeChild(shade);
    }return true;
  }menu.classList.add('menu-overflow-show');
  /* prepare for shade */
  var shade=document.createElement('div');
  shade.classList.add('menu-overflow-shadow');
  shade.id='menu-shadow';
  shade.onclick=function(e){
    menu.classList.remove('menu-overflow-show');
    document.body.classList.remove('dont-scroll');
    this.parentElement.removeChild(this);
  };
  document.body.appendChild(shade);
  document.body.classList.add('dont-scroll');
  return true;
};
/* re-calibrate menu on resize */
this.menuOnResize=function(){
  /* check menu hide */
  if(!this.MENU_WIDTH){return false;}
  /* get elements */
  var button=document.getElementById('menu-button');
  var menu=document.getElementById('menu');
  var header=document.getElementById('menu-header');
  var shade=document.getElementById('menu-shadow');
  var frame=document.getElementById('website-frame');
  if(!button||!menu||!header){return false;}
  /* re-calibrate position */
  if(this.MENU_WIDTH>window.innerWidth){
    button.classList.add('menu-button-show');
    menu.classList.add('menu-overflow');
    header.classList.add('menu-header-show');
  }else{
    button.classList.remove('menu-button-show');
    menu.classList.remove('menu-overflow');
    menu.classList.remove('menu-overflow-show');
    header.classList.remove('menu-header-show');
    if(shade){
      shade.parentElement.removeChild(shade);
    }
    if(!frame){
      document.body.classList.remove('dont-scroll');
    }
  }return true;
};
}
(new MenuReplace).init();
