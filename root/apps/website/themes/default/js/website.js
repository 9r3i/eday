/* website.js */
new website;
/* website class */
;function website(){
/* website version */
this.version='1.0.0';
/* big variable */
this.MENU_WIDTH=null;
/* set this object */
var _website=this;
/* array of event on anchor click */
this.onclick=[];
/* initialize website as constructor */
this.init=function(){
  /* define global _website to this object */
  window._website=this;
  /* initial website database */
  this.initData();
  /* initial history pop state */
  this.initPopState();
  /*  */
  /*  */
  /*  */
  /* initial website body */
  setTimeout(this.initBody,500);
  /* add to resize event */
  WINDOW_EVENTS.onresize.push(this.initBody);
  /* initial menu */
  this.initMenu();
  /* initial all anchors */
  this.initAnchors();
  /* execute all window events */
  WINDOW_EVENTS.execAll();
  /*  */
};
/* initialize pop state of history */
this.initPopState=function(){
  /* add history pop state */
  WINDOW_EVENTS.onpopstate.push(function(e){
    /* check current history state */
    if(!window.history.state){
      var webContent=document.getElementById('website-content');
      var path=location.pathname.substr(1).replace(/\.html$/,'');
      /* check website data */
      if(typeof WEBSITE_DATA==='object'
        &&WEBSITE_DATA!==null
        &&WEBSITE_DATA.hasOwnProperty(path)
        &&webContent){
        var r=WEBSITE_DATA[path];
        /* re-initial anchors */
        return _website.putPostData(r);
      }return false;
    }
    /* put title */
    return _website.putPostData(window.history.state,true);
  });
};
/* initialize website database -- WEBSITE_DATA */
this.initData=function(update){
  /* prepare load pages */
  if(!window.hasOwnProperty('WEBSITE_LOAD_PAGES')
    ||!Array.isArray(window.WEBSITE_LOAD_PAGES)){
    window.WEBSITE_LOAD_PAGES=[];
  }
  /* get last update */
  var hourUpdate=0.1;
  var rangeUpdate=hourUpdate*3600000;
  var lastUpdate=localStorage.getItem('website-data-update');
  var mustUpdate=!lastUpdate
    ||parseInt(lastUpdate)+rangeUpdate<(new Date).getTime()
    ?true:false;
  /* get local storage */
  var localData=localStorage.getItem('website-data');
  if(localData&&!update){
    /* trying to parse */
    try{
      window.WEBSITE_DATA=JSON.parse(localData);
    }catch(e){
      localStorage.removeItem('website-data');
      return this.initData(true);
    }
    /* check must update */
    return mustUpdate?this.initData(true):true;
  }
  /* prepare url */
  var url=WEBSITE_ADDRESS+'?website-data=full';
  /* start get full data content */
  return this.getContent(url,function(r){
    /* check result */
    if(typeof r!=='object'||r===null){
      return console.log(r);
    }
    /* set global data for result */
    window.WEBSITE_DATA=
      window.WEBSITE_DATA===null
      ?{}:window.WEBSITE_DATA;
    for(var i in r){
      window.WEBSITE_DATA[i]=r[i];
    }
    /* save last update data */
    localStorage.setItem('website-data-update',(new Date).getTime());
    /* save result object to local storage */
    return localStorage.setItem('website-data',JSON.stringify(r));
  },console.log,false);
};
/* initialize all anchors */
this.initAnchors=function(){
  var an=document.querySelectorAll('a[href]');
  var i=an.length;
  while(i--){
    an[i].onclick=this.execAnchor;
  }return true;
};
/* execute an anchor */
this.execAnchor=function(e){
  /* prevent default */
  e.preventDefault();
  /* hide menu */
  _website.menuHide();
  /* check WEBSITE_ADDRESS */
  if(!window.hasOwnProperty('WEBSITE_ADDRESS')
    ||!this.href){return false;}
  /* prepare query and href */
  var query='?website-ajax-request=true';
  var href=this.getAttribute('href').replace(WEBSITE_ADDRESS,'');
  /* external link */
  if(href.match(/^http/i)){
    return window.open(href,'_blank');
  }
  /* get web content element */
  var webContent=document.getElementById('website-content');
  if(!webContent){
    return window.location.assign(href);
  }
  /* prepare on click event */
  if(Array.isArray(_website.onclick)){
    for(var i=0;i<_website.onclick.length;i++){
      if(typeof _website.onclick[i]==='function'){
        _website.onclick[i]();
      }
    }
  }
  /* get slug */
  if(href.match(/^article|training$/)){
    query+='&type='+encodeURIComponent(href);
  }else if(href.match(/\.html$/)){
    query+='&slug='+encodeURIComponent(href.replace(/\.html$/,''));
  }else if(href=='./'||href==''){
    query+='&home=true';
  }else if(href.match(/^apps/)){
    return _website.externalPage(href,this.innerText);
  }else if(href.match(/^\?admin/)){
    var gets=href.split('?');
    var path=gets.hasOwnProperty(1)?'?'+gets[1]:'?admin';
    return _website.externalPage(WEBSITE_ADDRESS+path,this.innerText);
  }else{
    if(this.target=='_blank'){
      return window.open(href,'_blank');
    }return window.location.assign(href);
  }
  /* scroll to top */
  document.scrollingElement.scroll(0,0);
  /* create loader */
  webContent.innerText='Loading...';
  /* clone webContent */
  var clone=webContent.cloneNode();
  /* check website data */
  if(typeof WEBSITE_DATA==='object'
    &&WEBSITE_DATA!==null
    &&WEBSITE_DATA.hasOwnProperty(href.replace(/\.html$/,''))
    &&WEBSITE_LOAD_PAGES.indexOf(href)<0
    &&WEBSITE_DATA[href.replace(/\.html$/,'')].aid!=40404){
    return _website.fakeHeadLoader(function(){
      var r=WEBSITE_DATA[href.replace(/\.html$/,'')];
      /* re-initial anchors */
      return _website.putPostData(r);
    });
  }
  /* create loader */
  _website.loader(true);
  /* start get content */
  _website.getContent(WEBSITE_ADDRESS+query,function(r){
    _website.loader(false);
    if(typeof r!=='object'){
      webContent.innerHTML=clone.innerHTML;
      return alert('Error: Failed to load content.');
    }
    /* push to website database */
    WEBSITE_DATA=typeof WEBSITE_DATA==='object'
      &&WEBSITE_DATA!==null?WEBSITE_DATA:{};
    if(!WEBSITE_DATA.hasOwnProperty(href.replace(/\.html$/,''))
      &&(href.match(/\.html$/)||href=='./'||href=='')
      &&r.type!='bulk'){
      WEBSITE_DATA[href.replace(/\.html$/,'')]=r;
      localStorage.setItem('website-data',JSON.stringify(WEBSITE_DATA));
    }
    /* re-initial anchors */
    return _website.putPostData(r);
  },function(r){
    _website.loader(false);
    webContent.innerHTML=clone.innerHTML;
    return alert('Error: Failed to load content.');
  },false);
  /* return as false */
  return false;
};
/* put post data */
this.putPostData=function(post,state){
  /* check post object */
  if(typeof post!=='object'
    ||post===null
    ||!post.hasOwnProperty('url')
    ||!post.hasOwnProperty('title')
    ||!post.hasOwnProperty('content')){
    return false;
  }
  /* close external page */
  _website.externalPageClose();
  /* get webContent */
  var webContent=document.getElementById('website-content');
  if(!webContent){return false;}
  /* parse post content */
  var doc=(new DOMParser).parseFromString(post.content,'text/html');
  /* get all script elements */
  var scr=doc.querySelectorAll('script'),
        i=scr.length,tempEl=[];
  /* re-calibrate all elements
   * ~ NOTE: i'm doing this because 
   *         the scripts are not running while using history.state
   *         or only put into innerHTML
   *         and this way is for preventing un-running scripts
   */
  while(i--){
    /* create new script element */
    var ns=document.createElement('script');
    ns.async=true;ns.type='text/javascript';
    if(scr[i].src){
      ns.src=scr[i].src;
    }else{
      ns.textContent=scr[i].textContent;
    }tempEl.push(ns);
    /* remove old script elemnet */
    scr[i].parentElement.removeChild(scr[i]);
  }
  /* put content into web content */
  webContent.innerHTML=doc.body.innerHTML;
  /* append all temp script elements */
  for(var i=tempEl.length;i--;i>=0){
    webContent.appendChild(tempEl[i]);
  }
  /* put title */
  var title=document.querySelector('title');
  if(title){title.innerText=post.title;}
  /* history push state */
  if(!state){
    var surfix=post.type=='bulk'?'':'.html';
    window.history.pushState(post,post.title,post.url+surfix);
  }
  /* re-initial anchors */
  return _website.initAnchors();
};
/* external page -- loaded inside iframe */
this.externalPage=function(url,title){
  var id='website-frame';
  var frame=document.querySelector('iframe#'+id);
  var fc=document.querySelector('#'+id+'-close');
  var fh=document.querySelector('#'+id+'-head');
  if(frame){frame.parentElement.removeChild(frame);}
  if(fh){fh.parentElement.removeChild(fh);}
  if(fc){fc.parentElement.removeChild(fc);}
  if(typeof url!=='string'){return false;}
  /* frame title */
  title=typeof title==='string'?title:'Untitled';
  /* frame element */
  frame=document.createElement('iframe');
  frame.id=id;
  frame.classList.add(id);
  frame.src=url;
  frame.onload=function(){
    _website.loader(false);
  };
  frame.onerror=function(){
    _website.loader(false);
  };
  document.body.appendChild(frame);
  _website.loader(true);
  /* head element */
  fh=document.createElement('div');
  fh.classList.add(id+'-head');
  fh.id=id+'-head';
  fh.dataset.title=title;
  document.body.appendChild(fh);
  /* close element */
  fc=document.createElement('div');
  fc.classList.add(id+'-close');
  fc.id=id+'-close';
  fc.title='Close';
  document.body.appendChild(fc);
  /* donr scroll body */
  if(!document.body.classList.contains('dont-scroll')){
    document.body.classList.add('dont-scroll');
  }
  /* prepare title */
  var dt=document.querySelector('title');
  if(dt){
    var baseTitle=dt.innerText;
    fc.dataset.title=baseTitle;
    dt.innerText=title;
  }
  /* click event */
  fc.onclick=function(e){
    document.body.classList.remove('dont-scroll');
    this.parentElement.removeChild(this);
    if(frame){frame.parentElement.removeChild(frame);}
    if(fh){fh.parentElement.removeChild(fh);}
    if(dt){dt.innerText=this.dataset.title;}
    return true;
  };return true;
};
/* close external page */
this.externalPageClose=function(){
  var id='website-frame';
  var frame=document.querySelector('iframe#'+id);
  var fc=document.querySelector('#'+id+'-close');
  var fh=document.querySelector('#'+id+'-head');
  var dt=document.querySelector('title');
  document.body.classList.remove('dont-scroll');
  if(frame){frame.parentElement.removeChild(frame);}
  if(fh){fh.parentElement.removeChild(fh);}
  if(fc){
    if(dt){dt.innerText=fc.dataset.title;}
    fc.parentElement.removeChild(fc);
  }return true;
};
/* fake head loader -- for local data */
this.fakeHeadLoader=function(cb,value){
  cb=typeof cb==='function'?cb:function(){};
  value=value?parseInt(value):0;
  value=Math.max(Math.min(value,100),0);
  if(value>=100){
    _website.headLoader(false);
    return cb(true);
  }_website.headLoader(value);
  return setTimeout(function(){
    value+=3;
    return _website.fakeHeadLoader(cb,value);
  },10);
};
/* head loader */
this.headLoader=function(value){
  /* set loader id */
  var id='website-head-loader';
  /* get element */
  var hl=document.getElementById(id);
  var bar=document.getElementById(id+'-bar');
  /* check value */
  if(typeof value!=='number'){
    if(hl){hl.parentElement.removeChild(hl);}
    return false;
  }
  /* set minimum and maximum value */
  value=Math.max(Math.min(value,100),0);
  /* check hl */
  if(!hl){
    /* create new element */
    var hl=document.createElement('div');
    var bar=document.createElement('div');
    hl.classList.add('website-head-loader');
    bar.classList.add('website-head-loader-bar');
    hl.id=id;
    bar.id=id+'-bar';
  }
  /* set value */
  bar.style.width=value+'%';
  /* append child */
  hl.appendChild(bar);
  document.body.appendChild(hl);
};
/* loader */
this.loader=function(text){
  var id='website-loader';
  var ld=document.getElementById(id);
  if(!text){
    if(ld){ld.parentElement.removeChild(ld);}
    return false;
  }
  if(!ld){
    var ld=document.createElement('div');
    ld.classList.add(id);
    ld.id=id;
  }document.body.appendChild(ld);
  return true;
};
/* initialize website body */
this.initBody=function(){
  /* get elements */
  var webBody=document.getElementById('website-body');
  var webSidebar=document.getElementById('website-sidebar');
  if(!webBody||!webSidebar){return false;}
  /* re-calibrate body minimum height */
  var minHeight=webSidebar.offsetHeight;
  webBody.style.minHeight=minHeight+'px';
};
/* initialize menu */
this.initMenu=function(){
  /* get elements */
  var button=document.getElementById('menu-button');
  var menu=document.getElementById('menu');
  if(!button||!menu){return false;}
  /* get menu width */
  if(!this.MENU_WIDTH){
    this.MENU_WIDTH=menu.offsetWidth+400;
  }
  /* button click event */
  button.onclick=this.menuToggle;
  /* add on resize event */
  WINDOW_EVENTS.onresize.push(this.menuOnResize);
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
  if(!_website.MENU_WIDTH){return false;}
  /* get elements */
  var button=document.getElementById('menu-button');
  var menu=document.getElementById('menu');
  var header=document.getElementById('menu-header');
  var shade=document.getElementById('menu-shadow');
  var frame=document.getElementById('website-frame');
  if(!button||!menu||!header){return false;}
  /* re-calibrate position */
  if(_website.MENU_WIDTH>window.innerWidth){
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
/* get content
 * @parameters:
 *   url = string of url
 *   cb  = function of success callback
 *   er  = function of error callback
 *   txt = bool of text output; default: true
 */
this.getContent=function(url,cb,er,txt){
  cb=typeof cb==='function'?cb:function(){};
  er=typeof er==='function'?er:function(){};
  txt=txt===false?false:true;
  var xhr=new XMLHttpRequest();
  xhr.open('GET',url,true);
  xhr.send();
  xhr.onreadystatechange=function(e){
    if(xhr.readyState==4){
      if(xhr.status==200){
        var text=xhr.responseText?xhr.responseText:' ';
        if(txt){return cb(text);}
        var res=false;
        try{res=JSON.parse(text);}catch(e){}
        return cb(res?res:text);
      }else if(xhr.status==0){
        return er('Error: No internet connection.');
      }return er('Error: '+xhr.status+' - '+xhr.statusText+'.');
    }else if(xhr.readyState<4){
      return false;
    }return er('Error: '+xhr.status+' - '+xhr.statusText+'.');
  };return true;
};
/* return this initial construction */
return this.init();
};


