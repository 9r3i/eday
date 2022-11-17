/* pictureSelect.js */
;function pictureSelect(select,options){
/* the version */
this.version='1.0.0';
/* get element */
this.element=document.querySelector(select);
if(!this.element){return false;}
/* get parent */
this.parent=this.element.parentElement;
/* unique id */
this.uniqueID='picture-select-'
  +Math.ceil(Math.random()*(new Date).getTime());
/* give element an unique id */
this.element.dataset.pictureSelect=this.uniqueID;
/* options */
this.options={
  url:typeof options==='object'
    &&options!==null
    &&options.hasOwnProperty('url')
    ?options.url
    :WEBSITE_ADDRESS+'?'+ADMIN_KEY+'=ajax/upload',
};
/* set as globals */
window[this.uniqueID]=this;
/* this picture element */
this.picture=null;
/* hide */
this.hide=function(){
  this.picture.style.display='none';
  this.element.style.display='block';
  return true;
};
/* show */
this.show=function(){
  this.picture.style.display='block';
  this.element.style.display='none';
  return true;
};
/* initial constructor */
this.init=function(){
  /* create new element */
  this.picture=document.createElement('div');
  /* set attributes */
  this.picture.classList.add('picture-select');
  this.picture.id=this.uniqueID+'-picture';
  this.picture.dataset.uid=this.uniqueID;
  this.element.type='file';
  this.element.accept='image/*';
  this.element.id=this.uniqueID+'-file';
  this.element.dataset.uid=this.uniqueID;
  /* append element into parent */
  this.parent.insertBefore(this.picture,this.element);
  /* on change event */
  this.element.onchange=this.change;
  /* set up picture */
  if(this.element.dataset.value){
    var but=this.removeButton();
    var ptx=this.text(this.basename(this.element.dataset.value));
    var img=new Image;
    img.src=this.element.dataset.value;
    ptx.appendChild(but);
    this.picture.appendChild(ptx);
    this.picture.appendChild(img);
    this.element.style.display='none';
  }
  /* return as this object */
  return this;
};
/* on change event */
this.change=function(e){
  /* get unique id */
  var uid=this.dataset.uid;
  if(!window.hasOwnProperty(uid)){
    return _admin.error('Error: Failed to get unique ID.');
  }
  /* get _this object */
  var _this=window[uid];
  /* check file */
  if(!this.files.length){
    return _admin.error('Error: Failed to get data file.');
  }var file=this.files[0];
  /* check file type */
  if(!file.type.match(/^image\//)){
    return _admin.error('Error: File is not image.');
  }
  /* check file size */
  if(file.size>Math.pow(1024,2)*2){
    return _admin.error('Error: File is too large, max: 2 MB.');
  }
  /* prepare file reader */
  var FR=new FileReader;
  /* on load end event */
  FR.onloadend=function(e){
    /* prepare image */
    var img=new Image;
    img.src=e.target.result;
    img.alt=file.name;
    /* clear field */
    _this.picture.innerHTML='';
    /* insert image */
    _this.picture.appendChild(img);
    img.onload=function(){
      /* upload image */
      if(this.src.match(/^data/)){
        return _this.upload(file);
      }return true;
    };
  };
  /* read as data url */
  FR.readAsDataURL(file);
};
/* build picture remove  button */
this.removeButton=function(){
  var but=document.createElement('button');
  var ifa=document.createElement('i');
  but.classList.add('button');
  but.classList.add('button-red');
  ifa.classList.add('fa');
  ifa.classList.add('fa-trash');
  but.appendChild(ifa);
  but.title='Remove picture';
  var _this=this;
  but.onclick=function(e){
    return _admin.confirm('Remove Picture',
      'Remove this picture?',function(yes){
      if(!yes){return false;}
      _this.element.dataset.value='';
      _this.element.style.display='block';
      _this.picture.innerHTML='';
      return true;
    });
  };return but;
};
/* build picture text */
this.text=function(text){
  text=typeof text==='string'?text:'';
  var ptx=document.createElement('div');
  ptx.classList.add('picture-select-text');
  ptx.dataset.text=text;
  return ptx;
};
/* send upload request */
this.upload=function(file){
  /* prepare loader */
  var ptx=this.text('0% Uploading...');
  var url=this.options.url;
  var dt=new FormData;
  dt.append('file',file);
  ptx.classList.add('picture-select-text-show');
  this.picture.appendChild(ptx);
  var _this=this;
  return _admin.stream(url,function(r){
    if(typeof r!=='object'){
      ptx.dataset.text=r.toString();
      return false;
    }ptx.classList.remove('picture-select-text-show');
    var but=_this.removeButton();
    ptx.dataset.text=_this.basename(r.path);
    ptx.appendChild(but);
    _this.element.dataset.value=r.path;
    _this.element.style.display='none';
    var i=_this.picture.childNodes.length;
    while(i--){
      if(_this.picture.childNodes[i].tagName==='IMG'){
        _this.picture.childNodes[i].src=r.path;
        break;
      }
    }return true;
  },function(e){
    ptx.dataset.text=e;
    return false;
  },dt,null,function(e){
    var pr=Math.floor(e.loaded/e.total*100);
    ptx.dataset.text=pr+'% Downloading...';
  },function(e){
    var pr=Math.floor(e.loaded/e.total*100);
    ptx.dataset.text=pr+'% Uploading...';
  },'POST');
};
/* base name */
this.basename=function(s){
  s=typeof s==='string'?s:'';
  var r=s.match(/([^\/]+)\/?$/);
  return r&&r[1]?r[1]:'';
};
/* return initializer */
return this.init();
};


