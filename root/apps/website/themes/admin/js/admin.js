/* admin.js */
new admin;
/* admin class */
;function admin(){
/* admin version */
this.version='1.1.0';
/* big variable */
this.MENU_WIDTH=null;
this.USER=null;
this.PAGE=null;
/* set this object */
var _admin=this;
/* array of event on anchor click */
this.onclick=[];
/* initialize admin as constructor */
this.init=function(){
  /* define global _admin to this object */
  window._admin=this;
  /* initial history pop state */
  this.initPopState();
  /* initialize user data */
  this.USER=this.logData();
  /* initialize login page if there is login form (user is not login) */
  this.loginInit();
  /* initial admin body */
  this.initBody();
  /* add on resize event */
  WINDOW_EVENTS.onresize.push(this.initBody);
  /* initial menu */
  this.initMenu();
  /* set menu left as movable */
  this.menuMovableLeft('menu');
  /* initial all anchors */
  this.initAnchors();
  /* execute all window events */
  WINDOW_EVENTS.execAll();
  /*  */
  /*  */
  /*  */
};
/* initialize website body */
this.initBody=function(){
  /* get elements */
  var webBody=document.getElementById('website-body');
  var webContent=document.getElementById('website-content');
  if(!webBody||!webContent){return false;}
  /* re-calibrate body minimum height */
  var minHeight=window.innerHeight-67;
  webBody.style.minHeight=minHeight+'px';
};
/* ajax request */
this.request=function(method,callback,errorResult,data){
  var url=WEBSITE_ADDRESS+'?'+ADMIN_KEY+'=ajax/'+method;
  return _admin.stream(url,callback,errorResult,data);
};
/* go to admin page alias of getPageData */
this.go=function(path){
  return this.getPageData(path);
};


/* ------- ANCHORS, POPSTATE and PUT DATA ------- */
/* initialize pop state of history */
this.initPopState=function(){
  /* add history pop state */
  WINDOW_EVENTS.onpopstate.push(function(e){
    /* check current history state */
    if(!window.history.state){
      /* prepare gets query */
      var gets=_admin.parsePageQuery(location.search);
      /* return get page data */
      return _admin.getPageData(gets[ADMIN_KEY]);
    }
    /* put data */
    return _admin.putPageData(window.history.state,true);
  });
  /* prepare gets query */
  var gets=_admin.parsePageQuery(location.search);
  /* return get page data */
  return _admin.getPageData(gets[ADMIN_KEY]);
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
  _admin.menuHide();
  /* check WEBSITE_ADDRESS */
  if(!window.hasOwnProperty('WEBSITE_ADDRESS')
    ||!this.href){return false;}
  /* prepare query and href */
  var query='?website-ajax-request=true';
  var href=this.getAttribute('href').replace(WEBSITE_ADDRESS,'');
  /* external link */
  if(href.match(/^http/i)||this.target=='_blank'){
    return window.open(this.href,'_blank');
  }
  /* get web content element */
  var webContent=document.getElementById('website-content');
  if(!webContent){
    return window.location.assign(href);
  }
  /* prepare on click event */
  if(Array.isArray(_admin.onclick)){
    for(var i=0;i<_admin.onclick.length;i++){
      if(typeof _admin.onclick[i]==='function'){
        _admin.onclick[i]();
      }
    }
  }
  /* prepare gets query */
  var gets=_admin.parsePageQuery(href);
  /* none admin page */
  if(!gets.hasOwnProperty(ADMIN_KEY)
    ||gets[ADMIN_KEY].indexOf('plugin/')===0){
    return _admin.externalPage(href,this.innerText);
  }
  /* return get page data */
  return _admin.getPageData(gets[ADMIN_KEY]);
};
/* get page data */
this.getPageData=function(path){
  /* logout */
  if(path==='logout'){
    return _admin.logout();
  }
  /* prepare path, query and url */
  path=typeof path==='string'
    &&path!=''?path:'dashboard';
  var query='?'+ADMIN_KEY+'=page/'+path;
  var url=WEBSITE_ADDRESS+query;
  _admin.path=path;
  /* get web content element */
  var webContent=document.getElementById('website-content');
  if(!webContent){return false;}
  /* scroll to top */
  document.scrollingElement.scroll(0,0);
  /* clone webContent */
  var clone=webContent.innerHTML;
  /* create loader */
  webContent.innerText='Loading...';
  /* create loader */
  _admin.loader(true);
  /* start get content */
  return _admin.getContent(url,function(r){
    _admin.loader(false);
    if(typeof r!=='object'){
      webContent.innerHTML=clone;
      _admin.initAnchors();
      var errText=r.match(/^error/i)?r
        :'Error: Failed to load content.';
      return _admin.error(errText);
    }
    /* re-initial anchors */
    var notSave=location.search===query
      &&window.history.state?true:false;
    return _admin.putPageData(r,notSave);
  },function(r){
    _admin.loader(false);
    webContent.innerHTML=clone;
    _admin.initAnchors();
    return _admin.error('Error: Failed to load content.');
  },false);
};
/* put page data */
this.putPageData=function(page,notSave){
  /* check page object */
  if(typeof page!=='object'
    ||page===null
    ||!page.hasOwnProperty('url')
    ||!page.hasOwnProperty('title')
    ||!page.hasOwnProperty('content')){
    return false;
  }
  /* prepare admin page data */
  _admin.PAGE=page;
  /* close external page */
  _admin.externalPageClose();
  /* get webContent */
  var webContent=document.getElementById('website-content');
  if(!webContent){return false;}
  /* fill out web content */
  var data=page.hasOwnProperty('data')
    &&typeof page.data==='object'
    &&page.data!==null?page.data:{};
  var content=_admin.fillPageData(page.content,data);
  /* parse page content */
  var doc=(new DOMParser).parseFromString(content,'text/html');
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
  if(title){title.innerText=page.title+' - webAdmin';}
  /* history push state */
  if(!notSave){
    var pageSize=JSON.stringify(page).length;
    if(pageSize<524288){
      window.history.pushState(page,page.title,page.url);
    }
  }
  /* re-initial anchors */
  return _admin.initAnchors();
};
/* parse page query search */
this.parsePageQuery=function(str){
  str=typeof str==='string'?str:'';
  /* prepare gets query */
  var hrefs=str.split('?');
  var query=hrefs.hasOwnProperty(1)?hrefs[1]:'';
  return _admin.parseStr(query);
};
/* fill page data */
this.fillPageData=function(content,data){
  if(typeof content!=='string'){return content;}
  data=typeof data==='object'&&data!==null?data:{};
  return content.replace(/{{[a-z0-9\._]+}}/ig,function(m){
    var n=m.substr(2,m.length-4),
        s=n.split('.'),
        r=false,
        t=false,
        u=false;
    for(var i=0;i<s.length;i++){
      if(typeof r==='object'
        &&r.hasOwnProperty(s[i])){
        r=r[s[i]];
      }else if(r===false&&_admin.hasOwnProperty(s[i])){
        r=_admin[s[i]];
      }else if(typeof t==='object'
        &&t.hasOwnProperty(s[i])){
        t=t[s[i]];
      }else if(t===false&&data.hasOwnProperty(s[i])){
        t=data[s[i]];
      }else if(typeof u==='object'
        &&u.hasOwnProperty(s[i])){
        u=u[s[i]];
      }else if(u===false&&window.hasOwnProperty(s[i])){
        u=window[s[i]];
      }
    }
    if(r!==_admin&&typeof r!=='object'&&r!==false){
      return r;
    }else if(t!==data&&typeof t!=='object'&&t!==false){
      return t;
    }else if(u!==window&&typeof u!=='object'&&u!==false){
      return u;
    }return m;
  });
};


/* ------- EXTERNAL PAGE ------- */
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
    _admin.loader(false);
  };
  frame.onerror=function(){
    _admin.loader(false);
  };
  document.body.appendChild(frame);
  _admin.loader(true);
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
  /* set global variable as true */
  window.EXTERNAL_OPEN=true;
  /* click event */
  fc.onclick=function(e){
    document.body.classList.remove('dont-scroll');
    this.parentElement.removeChild(this);
    if(frame){frame.parentElement.removeChild(frame);}
    if(fh){fh.parentElement.removeChild(fh);}
    if(dt){dt.innerText=this.dataset.title;}
    /* set global variable as false */
    window.EXTERNAL_OPEN=false;
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
  /* set global variable as false */
  window.EXTERNAL_OPEN=false;
  if(frame){frame.parentElement.removeChild(frame);}
  if(fh){fh.parentElement.removeChild(fh);}
  if(fc){
    if(dt){dt.innerText=fc.dataset.title;}
    fc.parentElement.removeChild(fc);
  }return true;
};


/* ------- LOADERS ------- */
/* fake head loader -- for local data */
this.fakeHeadLoader=function(cb,value){
  cb=typeof cb==='function'?cb:function(){};
  value=value?parseInt(value):0;
  value=Math.max(Math.min(value,100),0);
  if(value>=100){
    _admin.headLoader(false);
    return cb(true);
  }_admin.headLoader(value);
  return setTimeout(function(){
    value+=3;
    return _admin.fakeHeadLoader(cb,value);
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
this.loader=function(open,text){
  var id='website-loader';
  var ld=document.getElementById(id);
  if(!open){
    if(ld){ld.parentElement.removeChild(ld);}
    return false;
  }text=typeof text==='string'?text:'Loading...';
  if(!ld){
    var ld=document.createElement('div');
    ld.classList.add(id);
    ld.id=id;
  }ld.dataset.text=text;
  document.body.appendChild(ld);
  return true;
};


/* ------- MENU ------- */
/* initialize menu */
this.initMenu=function(){
  /* get elements */
  var button=document.getElementById('menu-button');
  var menu=document.getElementById('menu');
  if(!button||!menu){return false;}
  /* get menu width */
  if(!this.MENU_WIDTH){
    this.MENU_WIDTH=620;
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
  /* hide menu */
  document.body.classList.remove('dont-scroll');
  menu.classList.remove('menu-overflow-show');
  shade.parentElement.removeChild(shade);
  /* remove style left */
  menu.style.removeProperty('left');
  /* return as true */
  return true;
};
/* menu show */
this.menuShow=function(){
  /* get elements */
  var menu=document.getElementById('menu');
  var isOverflow=menu.classList.contains('menu-overflow');
  if(!menu||!isOverflow){return false;}
  /* show menu */
  menu.classList.add('menu-overflow-show');
  /* prepare for shade */
  var shade=document.createElement('div');
  shade.classList.add('menu-overflow-shadow');
  shade.id='menu-shadow';
  shade.onclick=function(e){
    menu.classList.remove('menu-overflow-show');
    document.body.classList.remove('dont-scroll');
    this.parentElement.removeChild(this);
  };
  /* append shade */
  document.body.appendChild(shade);
  document.body.classList.add('dont-scroll');
  /* remove style left */
  menu.style.removeProperty('left');
  /* return as true */
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
  if(!_admin.MENU_WIDTH){return false;}
  /* get elements */
  var button=document.getElementById('menu-button');
  var menu=document.getElementById('menu');
  var header=document.getElementById('menu-header');
  var shade=document.getElementById('menu-shadow');
  var frame=document.getElementById('website-frame');
  if(!button||!menu||!header){return false;}
  /* re-calibrate position */
  if(_admin.MENU_WIDTH>=window.innerWidth){
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
/* movable left menu */
this.menuMovableLeft=function(id){
  //if(!window.hasOwnProperty('ontouchstart')){return false;}
  var el=document.getElementById(id);
  if(!el){return false;}
  WINDOW_EVENTS.ontouchend.push(function(e){
    if(!window.MENU_MOVABLE_LEFT){return false;}
    if(window.EXTERNAL_OPEN){
      window.MENU_MOVABLE_LEFT=false;
      return false;
    }
    var isHide=window.MENU_MOVABLE_LEFT.hide;
    var x=e.changedTouches?e.changedTouches[0].pageX:e.screenX;
    var left=(x-window.MENU_MOVABLE_LEFT.x)+window.MENU_MOVABLE_LEFT.l;
    window.MENU_MOVABLE_LEFT=false;
    if(!isHide){return _admin.menuShow();}
    else if(left<-100){return _admin.menuHide();}
    el.style.left='0px';
  });
  WINDOW_EVENTS.ontouchmove.push(function(e){
    if(!window.MENU_MOVABLE_LEFT){return false;}
    if(window.EXTERNAL_OPEN){window.MENU_MOVABLE_LEFT=false;return false;}
    var x=e.changedTouches?e.changedTouches[0].pageX:e.screenX;
    var left=(x-window.MENU_MOVABLE_LEFT.x)+window.MENU_MOVABLE_LEFT.l;
    if(left>=0&&!window.MENU_MOVABLE_LEFT.hide){
      window.MENU_MOVABLE_LEFT=false;
      return _admin.menuShow();
    }else if(left<-260&&window.MENU_MOVABLE_LEFT.hide){
      window.MENU_MOVABLE_LEFT=false;
      return _admin.menuHide();
    }
    if(left<0){el.style.left=left+'px';}
  });
  WINDOW_EVENTS.ontouchstart.push(function(e){
    if(window.EXTERNAL_OPEN
      ||_admin.MENU_WIDTH<window.innerWidth){
      return false;
    }
    var x=e.changedTouches?e.changedTouches[0].pageX:e.screenX;
    var l=el.offsetLeft;
    if(l===0||x>10){
      if(el.style.left=='0px'&&x>250){
        window.MENU_MOVABLE_LEFT={x:x,l:l,el:el,hide:true};
      }return false;
    }window.MENU_MOVABLE_LEFT={x:x,l:l,el:el,hide:false};
  });
};


/* ------- LOGIN PAGE ------- */
/* initial login page */
this.loginInit=function(){
  /* prepare elements */
  var submit=document.querySelector('input[name="submit"]');
  var username=document.querySelector('input[name="username"]');
  var password=document.querySelector('input[name="password"]');
  if(!submit||!username||!password){return false;}
  /* set events */
  submit.onclick=this.login;
  username.onkeyup=this.login;
  password.onkeyup=this.login;
  return true;
};
/* login submit */
this.login=function(e){
  /* prepare elements */
  var submit=document.querySelector('input[name="submit"]');
  var username=document.querySelector('input[name="username"]');
  var password=document.querySelector('input[name="password"]');
  if(!submit||!username||!password){return false;}
  /* check element */
  if(this===username||this===password){
    if(e.keyCode!==13){return false;}
  }
  if(username.value==''||password.value==''){
    return false;
  }
  /* turn off all elements */
  _admin.disabled([submit,username,password],true);
  submit.value='Cheking...';
  submit.blur();
  username.blur();
  password.blur();
  /* prepare data form */
  var data={
    username:username.value,
    password:password.value
  };
  /* send request */
  return _admin.stream(WEBSITE_ADDRESS+'?'+ADMIN_KEY+'=ajax/login',function(r){
    submit.value='Login';
    if(typeof r==='string'){
      _admin.disabled([submit,username,password],false);
      return _admin.error(r);
    }
    _admin.setCookie('webAdmin',r.token,7*365);
    _admin.setCookie('webAdminData',JSON.stringify(r),7*365);
    return _admin.success('Login success!',function(){
      return window.location.reload();
    });
  },function(e){
    _admin.disabled([submit,username,password],false);
    submit.value='Login';
    return _admin.error(e);
  },data);
};
/* logout */
this.logout=function(){
  return _admin.confirm('Logout','Are you sure?',function(yes){
    if(!yes){return false;}
    _admin.loader(true,'Logging out...');
    return _admin.request('logout',function(r){
      _admin.loader(false);
      if(r!='OK'){return _admin.error(r);}
      _admin.setCookie('webAdmin','',-7);
      _admin.setCookie('webAdminData','',-7);
      return _admin.success('Logout success!',function(){
        return window.location.assign(WEBSITE_ADDRESS+'?'+ADMIN_KEY);
      });
    },function(e){
      _admin.loader(false);
      return _admin.error(e);
    },{token:_admin.USER.token});
  });
};
/* get log data */
this.logData=function(){
  var data=false,raw=_admin.getCookie('webAdminData');
  try{data=JSON.parse(raw);}catch(e){return false;}
  return data;
};


/* ------- STAND-ALONE METHODS ------- */
/* clear element field */
this.clearElement=function(el){
  if(!el.childNodes){return false;}
  var i=el.childNodes.length;
  while(i--){
    el.removeChild(el.childNodes[i]);
  }return true;
};
/* parse string -- url query */
this.parseStr=function(t){
  if(typeof t!=='string'){return false;}
  var s=t.split('&');
  var r={},c={};
  for(var i=0;i<s.length;i++){
    if(!s[i]||s[i]==''){continue;}
    var p=s[i].split('=');
    var k=decodeURIComponent(p[0]);
    if(k.match(/\[(.*)?\]$/g)){
      var l=k.replace(/\[(.*)?\]$/g,'');
      var w=k.replace(/^.*\[(.*)?\]$/g,"$1");
      c[l]=c[l]?c[l]:0;
      if(w==''){w=c[l];c[l]+=1;}
      if(!r[l]){r[l]={};}
      r[l][w]=decodeURIComponent(p[1]);
      continue;
    }r[k]=p[1]?decodeURIComponent(p[1]):'';
  }return r;
};
/* disabled */
this.disabled=function(el,pos){
  if(!Array.isArray(el)){return false;}
  pos=typeof pos==='boolean'?pos:true;
  for(var i=0;i<el.length;i++){
    if(el[i].nodeName==='INPUT'){
      el[i].disabled=pos; 
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
/* stream
 * @require: this.buildQuery
 * @parameters:
 *   url = string of url
 *   cb  = function of success callback of response code 200
 *   er  = function of error callback
 *   dt  = object of data form
 *   hd  = object of headers
 *   ul  = function of upload progress
 *   dl  = function of download progress
 *   mt  = string of method
 *   ud4 = function of under-four ready-state
 * @return: void
 */
this.stream=function(url,cb,er,dt,hd,ul,dl,mt,ud4){
  /* prepare callbacks */
  cb=typeof cb==='function'?cb:function(){};
  er=typeof er==='function'?er:function(){};
  ul=typeof ul==='function'?ul:function(){};
  dl=typeof dl==='function'?dl:function(){};
  ud4=typeof ud4==='function'?ud4:function(){};
  /* prepare xhr --> xmlhttp */
  var xmlhttp=false;
  if(window.XMLHttpRequest){
    xmlhttp=new XMLHttpRequest();
  }else{
    /* older browser xhr */
    var xhf=[
      function(){return new ActiveXObject("Msxml2.XMLHTTP");},
      function(){return new ActiveXObject("Msxml3.XMLHTTP");},
      function(){return new ActiveXObject("Microsoft.XMLHTTP");}
    ];
    for(var i=0;i<xhf.length;i++){try{xmlhttp=xhf[i]();}catch(e){continue;}break;}
  }
  /* check xhr */
  if(!xmlhttp){return er('Error: Failed to build XML http request.');}
  /* set method */
  var mts=['GET','POST','PUT','OPTIONS','HEAD','DELETE'];
  mt=typeof mt==='string'&&mts.indexOf(mt)>=0?mt
    :typeof dt==='object'&&dt!==null?'POST':'GET';
  /* open xhr connection */
  xmlhttp.open(mt,url,true);
  /* build urlencoded form data */
  if(typeof dt==='object'&&dt!==null
    &&!dt.hasOwnProperty('append')
    &&typeof dt.append!=='function'){
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    dt=this.buildQuery(dt);
  }
  /* set headers */
  if(typeof hd=='object'&&hd!=null){
    for(var i in hd){xmlhttp.setRequestHeader(i,hd[i]);}
  }
  /* xhr ready state change */
  xmlhttp.onreadystatechange=function(e){
    if(xmlhttp.readyState===4&&xmlhttp.status===200
      &&typeof xmlhttp.responseText==='string'){
      try{var res=JSON.parse(xmlhttp.responseText);}
      catch(e){var res=xmlhttp.responseText;}
      return cb(res);
    }else if(xmlhttp.readyState===4){
      if(xmlhttp.status===0){return er('Error: No internet connection.');}
      return er('Error: '+xmlhttp.status+' '+xmlhttp.statusText);
    }else if(xmlhttp.readyState<4){
      return ud4('Mobile::stream--> '+xmlhttp.readyState+' '+xmlhttp.status+' '+xmlhttp.statusText);
    }return er('Error: '+xmlhttp.status+' '+xmlhttp.statusText);
  };
  /* set callback for upload and download */
  xmlhttp.upload.onprogress=ul;
  xmlhttp.addEventListener("progress",dl,false);
  /* send XHR */
  xmlhttp.send(dt);
};
/* build query stream data form */
this.buildQuery=function(data,key){
  var ret=[],dkey=null;
  for(var d in data){
    dkey=key?key+'['+encodeURIComponent(d)+']'
        :encodeURIComponent(d);
    if(typeof data[d]=='object'&&data[d]!==null){
      ret.push(this.buildQuery(data[d],dkey));
    }else{
      ret.push(dkey+"="+encodeURIComponent(data[d]));
    }
  }return ret.join("&");
};
/* uniform -- build urlencoded form data */
this.uniform=function(dt){
  var ret=[];
  for(var d in dt){
    if(Array.isArray(dt[d])||(typeof dt[d]=='object'&&dt[d]!==null)){
      ret.push(this.uniform(dt[d]));
    }else{ret.push(encodeURIComponent(d)+"="+encodeURIComponent(dt[d]));}
  }return ret.join("&");
};
/* set cookie */
this.setCookie=function(cname,cvalue,exdays,domain,path){
  exdays=exdays?parseInt(exdays):1;
  var d=new Date();
  d.setTime(d.getTime()+(exdays*24*60*60*1000));
  var expires=";expires="+d.toGMTString();
  var domain=domain?";domain="+domain:'';
  var path=path?";path="+path:'';
  /* BlackBerry browser version 5.0 doesn't support document.cookie */
  document.cookie=cname+"="+cvalue+expires+domain+path;
  return true;
};
/* get cookie */
this.getCookie=function(cname){
  var name=cname+"=",r=false;
  var ca=document.cookie.split(';');
  for(var i=0;i<ca.length;i++){
    var c=ca[i].trim();
    if(c.indexOf(name)==0){
      r=c.substring(name.length,c.length);
      break;
    }
  }return r;
};
/* default alert - sweet */
this.alert=function(title,text,type,callback){
  title=typeof title==='string'?title:'';
  text=typeof text==='string'?text:'';
  type=typeof type==='string'?type:'';
  return typeof swal==='function'?swal({
      title:title,
      text:text,
      type:type
    },callback):alert(title+'\r\n\r\n'+text);
};
/* default error */
this.error=function(text,callback){
  text=typeof text==='string'?text:text.toString();
  return typeof swal==='function'?swal({
      title:"Error",
      text:text,
      type:"error"
    },callback):alert(text);
};
/* default success */
this.success=function(text,callback){
  text=typeof text==='string'?text:text.toString();
  return typeof swal==='function'?swal({
      title:"Success",
      text:text,
      type:"success"
    },callback):alert(text);
};
/* default confirm */
this.confirm=function(title,text,callback){
  if(typeof title!=='string'
    ||typeof text!=='string'
    ||typeof callback!=='function'){return;}
  if(typeof swal!=='function'){
    var c=confirm(title+'\r\n\r\n'+text);
    return callback(c);
  }
  return swal({
    title:title,text:text,type:"warning",
    showCancelButton:true,
    confirmButtonColor:"#DD6B55",
    cancelButtonColor:"#DDFFBB",
    confirmButtonText:"Yes",
    cancelButtonText:"No",
    closeOnConfirm:true
  },callback);
};
/* return this initial construction */
return this.init();
};


