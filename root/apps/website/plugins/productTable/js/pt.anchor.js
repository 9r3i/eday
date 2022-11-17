function ptanchor(){

/* initialize all anchors */
this.init=function(){
  var an=document.querySelectorAll('a[href]'),
  i=an.length,
  _this=this;
  while(i--){
    an[i].onclick=function(e){
      return _this.exec(e,this);
    };
  }return this;
};
this.exec=function(e,a){
  if(a.href==WEBSITE_ADDRESS){
    return window.location.assign(a.href);
  }
  e.preventDefault();
  
  
  return false;
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
return this.init();
}

