/* product.table */
;function productTable(){
/* version */
this.version='1.0.0';
/* external variables */
this.WEBSITE_ADDRESS=WEBSITE_ADDRESS||window.location.origin+'/';
this.ADMIN_TOKEN=ADMIN_TOKEN||'';
this.ADMIN_KEY=ADMIN_KEY||'admin';
this.ADMIN_PATH=ADMIN_PATH||'';
this.HTML_CONTENTS=HTML_CONTENTS||{};
this.PRODUCT_URL=PRODUCT_URL||'';
this.REQUEST_PAGE=REQUEST_PAGE||'products';
/* internal variables */
this.content=document.getElementById('website-content');
this.path='';
/* global variables */
window._productTable=this;
/* array of event on anchor click */
this.onclick=[];
/* big variable */
this.MENU_WIDTH=null;
this.USER=null;
this.CURRENT=null;
this.PAGE=null;
/* initialize */
this.init=function(){
  /* initialize user data */
  this.USER=this.logData();
  /* initial admin body */
  this.initBody();
  /* get path */
  var query=this.parsePageQuery(window.location.search);
  this.path=query.hasOwnProperty(this.ADMIN_KEY)?query[this.ADMIN_KEY]:'';
  /* add on resize event */
  WINDOW_EVENTS.onresize.push(this.initBody);
  /*  */
  WINDOW_EVENTS.onpopstate.push(function(e){
    return _productTable.getWebContent();
  });
  /* execute all window events */
  WINDOW_EVENTS.execAll();
  /* return web content */
  return this.getWebContent();
};
/* web content */
this.getWebContent=function(){
  var parse=this.parseLocationHash(),
  paths=['products','newProduct','editProduct','store'],
  path=parse.basePath,
  page=paths.indexOf(path)>=0?path:this.REQUEST_PAGE;
  return this.request(page,function(r){
    return _productTable.putContent(r);
  },parse.query);
};
/* get log data */
this.logData=function(){
  var data=false,raw=this.getCookie('webAdminData');
  try{data=JSON.parse(raw);}catch(e){return false;}
  return data;
};
/* initialize website body */
this.initBody=function(){
  /* get elements */
  var webBody=document.getElementById('website-body');
  var webContent=document.getElementById('website-content');
  if(!webBody||!webContent){return false;}
  /* re-calibrate body minimum height */
  var minHeight=window.innerHeight-100;
  webBody.style.minHeight=minHeight+'px';
};
/* request mod */
this.request=function(mt,cb,dt){
  cb=typeof cb==='function'?cb:function(){};
  dt=typeof dt==='object'&&dt!==null?dt:{};
  if(typeof mt!=='string'){return cb(false);}
  var url=this.WEBSITE_ADDRESS+'?EdayProductTable=ajax';
  dt.method=mt;
  dt.webAdminToken=this.ADMIN_TOKEN;
  return this.stream(url,cb,cb,dt);
};
/* ajax request default */
this.requestOld=function(method,callback,errorResult,data){
  var url=WEBSITE_ADDRESS+'?'+ADMIN_KEY+'=ajax/'+method;
  return _admin.stream(url,callback,errorResult,data);
};
/* go to admin page alias of getPageData */
this.go=function(path){
  return window.location.assign('#'+path);
};
/* parse page query search */
this.parsePageQuery=function(str){
  str=typeof str==='string'?str:'';
  /* prepare gets query */
  var hrefs=str.split('?');
  var query=hrefs.hasOwnProperty(1)?hrefs[1]:'';
  return this.parseStr(query);
};
/* fill page data */
this.fillPageData=function(content,data){
  if(typeof content!=='string'){return content;}
  data=typeof data==='object'&&data!==null?data:{};
  return content.replace(/{{[a-z0-9\._]+}}/ig,function(m){
    var n=m.substring(2,m.length-2),
        s=n.split('.'),
        r=false,
        t=false,
        u=false;
    for(var i=0;i<s.length;i++){
      if(typeof r==='object'
        &&r.hasOwnProperty(s[i])){
        r=r[s[i]];
      }else if(r===false&&_productTable.hasOwnProperty(s[i])){
        r=_productTable[s[i]];
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
    if(r!==_productTable&&typeof r!=='object'&&r!==false){
      return r;
    }else if(t!==data&&typeof t!=='object'&&t!==false){
      return t;
    }else if(u!==window&&typeof u!=='object'&&u!==false){
      return u;
    }return m;
  });
};
/* put some content */
this.putContent=function(data){
  data=typeof data==='object'&&data!==null?data:{};
  /* find hash and page */
  var parse=this.parseLocationHash(),
  page=this.HTML_CONTENTS.hasOwnProperty(parse.basePath)
    ?parse.basePath:this.REQUEST_PAGE;
  /* execute the header function if it exists */
  if(typeof window[page]==='object'
    &&window[page]!==null
    &&window[page].hasOwnProperty('header')
    &&typeof window[page].init==='function'){
    data=window[page].header(data);
  }
  /* set the ontent */
  var content=this.fillPageData(this.HTML_CONTENTS[page],data);
  /* parse page content */
  var doc=(new DOMParser).parseFromString(content,'text/html');
  /* get all script elements */
  var scr=doc.querySelectorAll('script'),
        i=scr.length,tempEl=[];
  /* re-calibrate all script elements
   * ~ NOTE: i'm doing this because 
   *         the scripts are not running while using history.state
   *         or only put into innerHTML
   *         and this way is for preventing un-running scripts
   */
  while(i--){
    /* create new script element */
    var ns=document.createElement('script');
    ns.async=true;ns.type='text/javascript';
    ns.textContent=scr[i].textContent;
    tempEl.push(ns);
    /* remove old script elemnet */
    scr[i].parentElement.removeChild(scr[i]);
  }
  /* put the content in web content */
  this.content.innerHTML=content;
  this.content.scrollTo({
    top:0,
    left:0,
    behavior:'smooth',
  });
  /* execute the function if it exists */
  if(typeof window[page]==='object'
    &&window[page]!==null
    &&window[page].hasOwnProperty('init')
    &&typeof window[page].init==='function'){
    return window[page].init(data);
  }
  /* return the content */
  return content;
};
/* parsing hash as uri */
this.parseLocationHash=function(){
  var hash=window.location.hash.substring(1),
  hashes=hash.split('?'),
  path=hashes[0],
  basePath=hashes[0].split('/')[0],
  query=hashes.length>1?this.parseStr(hashes[1]):{};
  return {
    path:path,
    basePath:basePath,
    query:query,
    hash:hash,
  };
};


/* specification */
this.spec=function(sidk,sidv,sidr,data){
  var skey=document.querySelector(sidk),
  svalue=document.querySelector(sidv),
  sres=document.querySelector(sidr),
  _this=this,
  build=this.buildElement,
  tbody=build('tbody'),
  table=build('table',null,{},[tbody]);
  table.appendTo(sres);
  if(typeof data==='object'&&data!==null){
    for(var i in data){
      var tr=_this.specRow(i,data[i]);
      tr.appendTo(tbody);
    }
  }
  skey.onkeyup=function(e){
    if(e.keyCode!==13){return false;}
    var value=svalue.value,
    key=skey.value;
    if(value==''||key==''){return false;}
    skey.value='';
    svalue.value='';
    var tr=_this.specRow(key,value);
    tr.appendTo(tbody);
  };
  svalue.onkeyup=function(e){
    if(e.keyCode!==13){return false;}
    var value=svalue.value,
    key=skey.value;
    if(value==''||key==''){return false;}
    skey.value='';
    svalue.value='';
    var tr=_this.specRow(key,value);
    tr.appendTo(tbody);
  };
  sres.getValue=function(){
    var row=document.getElementsByClassName('spec-row-drop'),
    res={},i=row.length;
    while(i--){
      res[row[i].dataset.key]=row[i].dataset.value;
    }return JSON.stringify(res);
  };
  return sres;
};
/* spec row */
this.specRow=function(key,value){
  var build=this.buildElement,
  tr=document.querySelector('tr[data-key="'+key+'"]');
  if(tr){
    tr.childNodes[1].innerText=value;
    return true;
  }
  var tdk=build('td',key),
  tdv=build('td',value),
  tdi=build('div',null,{
    'class':'spec-tag',
    'data-key':key
  },[
    build('i',null,{
      'class':'fa fa-remove'
    }),
    build('span','')
  ]),
  tdx=build('td',null,{},[tdi]);
  tr=build('tr',null,{
    'class':'spec-row-drop',
    'data-key':key,
    'data-value':value,
  },[
    tdk,tdv,tdx
  ]);
  tdi.onclick=function(e){
    var dkey=this.dataset.key,
    dtr=document.querySelector('tr[data-key="'+dkey+'"]');
    dtr.parentNode.removeChild(dtr);
  };
  return tr;
};
/* category input */
this.categoryInput=function(sid,data){
  var cinput=document.querySelector(sid),
  cparent=cinput.parentNode,
  _this=this;
  if(Array.isArray(data)){
    for(var i in data){
      _this.request('categoryGet',function(r){
        if(typeof r==='object'&&r!==null&&r.aid){
          var sp=_this.categoryTag(r.name,sid);
          sp.dataset.aid=r.aid;
          sp.appendTo(cparent);
        }
      },{aid:data[i]});
    }
  }
  cinput.onkeyup=function(e){
    if(e.keyCode!==13){return false;}
    var val=this.value,
    sp=_this.categoryTag(val,sid);
    this.value='';
    sp.appendTo(cparent);
    _this.request('categoryPut',function(r){
      if(typeof r==='object'&&r!==null&&r.aid){
        sp.dataset.aid=r.aid;
        return true;
      }cparent.removeChild(sp);
    },{
      name:val,
    });
  };
  cinput.getValue=function(){
    var tags=document.getElementsByClassName('category-tag'),
    res=[],i=tags.length;
    while(i--){
      res.push(tags[i].dataset.aid);
    }return JSON.stringify(res);
  };
  return cinput;
};
/* category tag */
this.categoryTag=function(val,sid){
  var build=this.buildElement,
  spi=build('i',null,{
    'class':'fa fa-remove',
    'data-name':val,
    'data-sid':sid,
  }),
  sp=build('div',null,{
    'class':'category-tag',
    'data-name':val,
  },[
    spi,
    build('span',val),
  ]);
  spi.onclick=function(e){
    this.parentNode.parentNode.removeChild(this.parentNode);
  };
  return sp;
};


/* ------- PICTURE ------- */
/* slider -- require: tiny-slider.js */
this.sliderInit=function(id){
  return tns({
    container:id,
    items:1,
    slideBy:"page",
    mouseDrag:false,
    swipeAngle:false,
    controls:false,
    nav:false,
    speed: 400,
    startIndex:0,
    rewind:false,
    center:false,
    autoWidth:false,
    loop:true,
    autoplay:true,
    autoplayHoverPause:false,
    autoplayTimeout:2500,
    autoplayText:[
      "▶",
      "❚❚",
    ],
    autoplayButton:false,
    autoplayButtonOutput:false,
    autoplayResetOnVisibility:false,
  });
};
/* slider -- require: tiny-slider.js */
this.slider=function(images){
  images=Array.isArray(images)?images:[];
  var div=document.createElement('div');
  var imgs=[];
  for(var i in images){
    var di=document.createElement('div');
    var img=document.createElement('img');
    img.src=''+images[i];
    di.appendChild(img);
    div.appendChild(di);
    imgs.push(img);
  }
  div.classList.add('slider');
  return {init:function(){return tns({
    container:'.slider',
    items:1,
    slideBy:"page",
    mouseDrag:false,
    swipeAngle:false,
    controls:false,
    nav:false,
    speed: 400,
    startIndex:0,
    rewind:false,
    center:false,
    autoWidth:false,
    loop:true,
    autoplay:true,
    autoplayHoverPause:false,
    autoplayTimeout:2500,
    autoplayText:[
      "▶",
      "❚❚",
    ],
    autoplayButton:false,
    autoplayButtonOutput:false,
    autoplayResetOnVisibility:false,
  })},element:div,images:imgs};
};
/* picture upload -- string */
this.pictureUpload=function(file,cb){
  return this.request('pictureUpload',cb,{data:file});
};
/* picture convert */
this.pictureConvert=function(file,cb){
  var imgx=new Image,
  iwidth=640,
  iheight=640,
  _this=this;
  imgx.src=URL.createObjectURL(file);
  imgx.onload=function(){
      var ratio=imgx.width>imgx.height
              ?imgx.height/imgx.width
              :imgx.width/imgx.height,
        iw=imgx.width>imgx.height?ratio*imgx.width:imgx.width,
        ih=imgx.height>imgx.width?ratio*imgx.height:imgx.height,
        sx=imgx.width>imgx.height
          ?(imgx.width-imgx.height)/2:0,
        sy=imgx.height>imgx.width
          ?(imgx.height-imgx.width)/2:0;
      var canvas=_this.buildElement('canvas',null,{
        width:iwidth,
        height:iheight,
      }),
      ctx=canvas.getContext('2d');
      ctx.drawImage(imgx,sx,sy,iw,ih,0,0,iwidth,iheight);
      var base64ImageData=canvas.toDataURL();
      return cb(base64ImageData);
  };
};
/* picture input */
this.pictureInput=function(sid,data){
  var picInput=document.querySelector(sid),
  picMask=document.querySelector('.input-mask'),
  picParent=picInput.parentNode,
  picTemp=Array.isArray(data)?data:[],
  _this=this,
  picSlider=document.querySelector('.slider');
  picInput.dataset.pictures=JSON.stringify(picTemp);
  picInput.dataArray=picTemp;
  picSlider.style.height=picSlider.offsetWidth+'px';
  picMask.dataset.text=picMask.innerText;
  if(picTemp.length>0){
    for(var i in picTemp){
      var di=document.createElement('div'),
      img=new Image;
      img.src=picTemp[i];
      di.appendChild(img);
      picSlider.appendChild(di);
    }
    _this.sliderInit('.slider');
  }
  picInput.onchange=function(e){
    var length=this.files.length,
    loaded=0;
    if(length==0){return;}
    _this.clearElement(picSlider);
    picInput.disabled=true;
    picMask.innerText=loaded+'/'+length+' Uploading...';
    for(var i in this.files){
      _this.pictureConvert(this.files[i],file=>{
        _this.pictureUpload(file,r=>{
          loaded++;
          picMask.innerText=loaded+'/'+length+' Uploading...';
          if(typeof r==='object'
            &&r!==null&&r.file){
            var di=document.createElement('div'),
            img=new Image;
            img.src=r.file;
            di.appendChild(img);
            picSlider.appendChild(di);
            picTemp.push(r.file);
          }
          if(loaded==length){
            _this.sliderInit('.slider');
            picInput.dataset.pictures=JSON.stringify(picTemp);
            picInput.dataArray=picTemp;
            picInput.disabled=false;
            picMask.innerText=picMask.dataset.text;
          }
        });
      });
    }
  };
  return picInput;
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
    _productTable.loader(false);
  };
  frame.onerror=function(){
    _productTable.loader(false);
  };
  document.body.appendChild(frame);
  _productTable.loader(true);
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
/* close external page -- stand-alone */
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
    _productTable.headLoader(false);
    return cb(true);
  }_productTable.headLoader(value);
  return setTimeout(function(){
    value+=3;
    return _productTable.fakeHeadLoader(cb,value);
  },10);
};
/* head loader -- stand-alone */
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
/* loader -- stand-alone */
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


/* ------- STAND-ALONE METHODS ------- */
/* build element */
this.buildElement=function(tag,text,attr,children,html,content){
  var div=document.createElement(typeof tag==='string'?tag:'div');
  div.appendTo=function(el){
    if(typeof el.appendChild==='function'){
      el.appendChild(this);
      return true;
    }return false;
  };
  if(typeof text==='string'){
    div.innerText=text;
  }
  if(typeof attr==='object'&&attr!==null){
    for(var i in attr){
      div.setAttribute(i,attr[i]);
    }
  }
  if(Array.isArray(children)){
    for(var i=0;i<children.length;i++){
      div.appendChild(children[i]);
    }
  }
  if(typeof html==='string'){
    div.innerHTML=html;
  }
  if(typeof content==='string'){
    div.textContent=content;
  }
  div.args={
    tag:tag,
    text:text,
    attr:attr,
    children:children,
    html:html,
    content:content,
  };
  return div;
};
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
 * @require: this.uniform
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
    dt=this.uniform(dt);
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
/* initialize */
return this.init();
};
