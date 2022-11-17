/* alpha.js, ~ text editor, authored by 9r3i, https://github.com/9r3i, started at august 30th 2018 */
window.ALPHA={
on:true,
version:'1.0.1',
root:null,
tool:null,
preview:null,
element:null,
cursorStart:0,
cursorEnd:0,
selected:null,
getValue:function(){
  return this.generate(this.element.value,true);
},
editor:function(id){
  this.root=document.createElement('div');
  this.tool=document.createElement('div');
  this.preview=document.createElement('div');
  this.element=document.createElement('textarea');
  var el=document.querySelector('textarea#'+id);
  if(!el){this.on=false;return false;}
  if(!this.prepare()){this.on=false;return false;}
  el.parentElement.insertBefore(this.root,el);
  el.style.display='none';
  this.element.name=el.name;
  el.name+='-hidden';
  var raw=el.value.toString().trim();
  this.root.appendChild(this.tool);
  this.root.appendChild(this.preview);
  this.root.appendChild(this.element);
  this.element.spellcheck=false;
  this.element.onkeyup=this.keyup;
  this.element.onmouseup=this.keyup;
  this.element.onblur=this.blur;
  this.element.oncontextmenu=this.absorbEvent;
  raw=this.reverse(raw);
  this.element.value=raw;
  this.preview.innerHTML=this.generate(raw);
  document.fonts.ready.then(function(e){
    ALPHA.scrollMax();
  }).catch();
  this.element.blur();
  return true;
},
prepare:function(){
  if(!this.loadCSS()){this.on=false;return false;}
  this.root.classList.add('alpha-editor');
  this.tool.classList.add('alpha-editor-tool');
  this.preview.classList.add('alpha-editor-preview');
  this.element.classList.add('alpha-editor-textarea');
  this.element.dataset.height=150;
  /* tool buttons */
  this.tool.appendChild(this.bold());
  this.tool.appendChild(this.italic());
  this.tool.appendChild(this.del());
  this.tool.appendChild(this.xup());
  //this.tool.appendChild(this.xdown());
  this.tool.appendChild(this.xcode());
  this.tool.appendChild(this.view());
  return true;
},
keyup:function(e){
  ALPHA.preview.innerHTML=ALPHA.generate(this.value);
  ALPHA.scrollMax();
},
view:function(){
  var view=document.createElement('div');
  view.classList.add('alpha-editor-tool-button');
  view.innerHTML='V';
  view.title='Preview';
  view.onclick=function(e){
    var id='alpha-editor-preview';
    var pre=document.getElementById(id);
    if(pre){
      pre.parentElement.removeChild(pre);
      return false;
    }
    var h=ALPHA.element.dataset.height;
    pre=document.createElement('div');
    pre.id='alpha-editor-preview';
    pre.classList.add('alpha-editor-preview');
    pre.setAttribute('style','position:absolute !important;z-index:3 !important;height:'+(parseInt(h))
      +'px;width:calc(100% - 12px);top:33px !important;');
    pre.innerHTML=ALPHA.getValue();
    ALPHA.root.appendChild(pre);
    return true;
  };return view;
},
xcode:function(){
  var xcode=document.createElement('div');
  xcode.classList.add('alpha-editor-tool-button');
  xcode.innerHTML='&lt;/&gt;';
  xcode.title='Code';
  xcode.onclick=function(e){
    var el=document.getElementById('alpha-cursor-selection');
    if(!ALPHA.selected||!el){return false;}
    var raw=ALPHA.element.value;
    var t=ALPHA.selected[0];
    var p=ALPHA.selected[1];
    var z=ALPHA.selected[2];
    var is=t.match(/^`.*`$/ig);
    var tr=is?t.substr(1,t.length-2):'`'+t+'`';
    var r=raw.substr(0,p)+tr+raw.substr(z);
    ALPHA.element.value=r
    el.parentElement.removeChild(el);
    ALPHA.selected=null;
    ALPHA.element.selectionStart=p;
    ALPHA.element.selectionEnd=z+(is?-2:2);
    ALPHA.preview.innerHTML=ALPHA.generate(r);
  };return xcode;
},
xdown:function(){
  var xdown=document.createElement('div');
  xdown.classList.add('alpha-editor-tool-button');
  xdown.innerHTML='X<sub>n</sub>';
  xdown.title='Submision';
  xdown.onclick=function(e){
    var el=document.getElementById('alpha-cursor-selection');
    if(!ALPHA.selected||!el){return false;}
    var raw=ALPHA.element.value;
    var t=ALPHA.selected[0];
    var p=ALPHA.selected[1];
    var z=ALPHA.selected[2];
    var is=t.match(/^\^.*\^$/ig);
    var tr=is?t.substr(1,t.length-2):'^'+t+'^';
    var r=raw.substr(0,p)+tr+raw.substr(z);
    ALPHA.element.value=r
    el.parentElement.removeChild(el);
    ALPHA.selected=null;
    ALPHA.element.selectionStart=p;
    ALPHA.element.selectionEnd=z+(is?-2:2);
    ALPHA.preview.innerHTML=ALPHA.generate(r);
  };return xdown;
},
xup:function(){
  var xup=document.createElement('div');
  xup.classList.add('alpha-editor-tool-button');
  xup.innerHTML='X<sup>n</sup>';
  xup.title='Super';
  xup.onclick=function(e){
    var el=document.getElementById('alpha-cursor-selection');
    if(!ALPHA.selected||!el){return false;}
    var raw=ALPHA.element.value;
    var t=ALPHA.selected[0];
    var p=ALPHA.selected[1];
    var z=ALPHA.selected[2];
    var is=t.match(/^\^.*\^$/ig);
    var tr=is?t.substr(1,t.length-2):'^'+t+'^';
    var r=raw.substr(0,p)+tr+raw.substr(z);
    ALPHA.element.value=r
    el.parentElement.removeChild(el);
    ALPHA.selected=null;
    ALPHA.element.selectionStart=p;
    ALPHA.element.selectionEnd=z+(is?-2:2);
    ALPHA.preview.innerHTML=ALPHA.generate(r);
  };return xup;
},
del:function(){
  var del=document.createElement('div');
  del.classList.add('alpha-editor-tool-button');
  del.innerHTML='<del>S</del>';
  del.title='Streak';
  del.onclick=function(e){
    var el=document.getElementById('alpha-cursor-selection');
    if(!ALPHA.selected||!el){return false;}
    var raw=ALPHA.element.value;
    var t=ALPHA.selected[0];
    var p=ALPHA.selected[1];
    var z=ALPHA.selected[2];
    var is=t.match(/^~.*~$/ig);
    var tr=is?t.substr(1,t.length-2):'~'+t+'~';
    var r=raw.substr(0,p)+tr+raw.substr(z);
    ALPHA.element.value=r
    el.parentElement.removeChild(el);
    ALPHA.selected=null;
    ALPHA.element.selectionStart=p;
    ALPHA.element.selectionEnd=z+(is?-2:2);
    ALPHA.preview.innerHTML=ALPHA.generate(r);
  };return del;
},
italic:function(){
  var italic=document.createElement('div');
  italic.classList.add('alpha-editor-tool-button');
  italic.innerText='I';
  italic.style.fontStyle='italic';
  italic.title='Italic';
  italic.onclick=function(e){
    var el=document.getElementById('alpha-cursor-selection');
    if(!ALPHA.selected||!el){return false;}
    var raw=ALPHA.element.value;
    var t=ALPHA.selected[0];
    var p=ALPHA.selected[1];
    var z=ALPHA.selected[2];
    var is=t.match(/^_.*_$/ig);
    var tr=is?t.substr(1,t.length-2):'_'+t+'_';
    var r=raw.substr(0,p)+tr+raw.substr(z);
    ALPHA.element.value=r
    el.parentElement.removeChild(el);
    ALPHA.selected=null;
    ALPHA.element.selectionStart=p;
    ALPHA.element.selectionEnd=z+(is?-2:2);
    ALPHA.preview.innerHTML=ALPHA.generate(r);
  };return italic;
},
bold:function(){
  var bold=document.createElement('div');
  bold.classList.add('alpha-editor-tool-button');
  bold.innerText='B';
  bold.title='Bold';
  bold.onclick=function(e){
    var el=document.getElementById('alpha-cursor-selection');
    if(!ALPHA.selected||!el){return false;}
    var raw=ALPHA.element.value;
    var t=ALPHA.selected[0];
    var p=ALPHA.selected[1];
    var z=ALPHA.selected[2];
    var is=t.match(/^\*.*\*$/ig);
    var tr=is?t.substr(1,t.length-2):'*'+t+'*';
    var r=raw.substr(0,p)+tr+raw.substr(z);
    ALPHA.element.value=r
    el.parentElement.removeChild(el);
    ALPHA.selected=null;
    ALPHA.element.selectionStart=p;
    ALPHA.element.selectionEnd=z+(is?-2:2);
    ALPHA.preview.innerHTML=ALPHA.generate(r);
  };return bold;
},
generate:function(r,real){
  var r=r.toString();
  var p=this.element.selectionStart;
  var z=this.element.selectionEnd;
  var l=r.length;
  var cursor='<span class="alpha-cursor-blink"></span>';
  if(!real){if(p!==z){
    this.selected=[r.substr(p,z-p),p,z];
    r=this.leftTag(r.substr(0,p))
      +(p<this.cursorStart?cursor:'')
      +'<span class="alpha-cursor-selection" id="alpha-cursor-selection">'
      +this.leftTag(r.substr(p,z-p))+'</span>'
      +(z>this.cursorEnd?cursor:'')
      +this.leftTag(r.substr(z,l));
  }else{
    r=this.leftTag(r.substr(0,p))+cursor+this.leftTag(r.substr(p,l));
  }}
  this.cursorStart=p;
  this.cursorEnd=z;
  r=r.replace(/\r|\n/ig,function(m){
    return '<br />';
  });
  r=r.replace(/\*[^\*\r\n]+\*/ig,function(m){
    var v=real?m.substr(1,m.length-2):m;
    return '<strong>'+v+'</strong>';
  });
  r=r.replace(/_[^_\r\n]+_/ig,function(m){
    var v=real?m.substr(1,m.length-2):m;
    return '<em>'+v+'</em>';
  });
  r=r.replace(/~[^~\r\n]+~/ig,function(m){
    var v=real?m.substr(1,m.length-2):m;
    return '<del>'+v+'</del>';
  });
  r=r.replace(/`[^`\r\n]+`/ig,function(m){
    var v=real?m.substr(1,m.length-2):m;
    return '<code>'+v+'</code>';
  });
  r=r.replace(/\^[^\^\r\n]+\^/ig,function(m){
    var v=real?m.substr(1,m.length-2):m;
    return '<sup>'+v+'</sup>';
  });
  return r;
},
reverse:function(r){
  var r=r.toString();
  r=r.replace(/&lt;/ig,function(m){
    return '<';
  }).replace(/&amp;/ig,function(m){
    return '&';
  });
  r=r.replace(/(<br>|<br \/>)/ig,function(m){
    return '\n';
  });
  r=r.replace(/<strong>[^<\r\n]+<\/strong>/ig,function(m){
    return '*'+m.substr(8,m.length-17)+'*';
  });
  r=r.replace(/<em>[^<\r\n]+<\/em>/ig,function(m){
    return '_'+m.substr(4,m.length-9)+'_';
  });
  r=r.replace(/<del>[^<\r\n]+<\/del>/ig,function(m){
    return '~'+m.substr(5,m.length-11)+'~';
  });
  r=r.replace(/<code>[^<\r\n]+<\/code>/ig,function(m){
    return '`'+m.substr(6,m.length-13)+'`';
  });
  r=r.replace(/<sup>[^<\r\n]+<\/sup>/ig,function(m){
    return '^'+m.substr(5,m.length-11)+'^';
  });
  return r;
},
scrollMax:function(){
  var max=0;
  if(typeof ALPHA.element.scrollTopMax==='number'){
    max=this.element.scrollTopMax;
  }else{
    var top=this.element.scrollTop;
    this.element.scrollTop=Math.pow(1024,5);
    max=parseInt(this.element.scrollTop);
    this.element.scrollTop=top;
  }
  if(max===0){return false;}
  var h=this.element.dataset.height;
  var nh=parseInt(h)+parseInt(max)+12;
  this.element.dataset.height=nh;
  this.element.style.height=nh+'px';
  this.preview.style.height=nh+'px';
  return true;
},
scroll:function(e){
  ALPHA.preview.scrollTop=this.scrollTop;
},
blur:function(e){
  ALPHA.preview.innerHTML=ALPHA.removeCursor(ALPHA.preview.innerHTML);
},
removeCursor:function(r){
  return r.toString().replace(/<span class="alpha\-cursor\-blink"><\/span>/g,'');
},
absorbEvent:function(event){
  var e=event||window.event;
  e.preventDefault&&e.preventDefault();
  e.stopPropagation&&e.stopPropagation();
  e.cancelBubble=true;
  e.returnValue=false;
  return false;
},
leftTag:function(r,reverse){
  r=r.toString();
  if(reverse){
    return r.replace(/&lt;/ig,function(m){
      return '<';
    }).replace(/&amp;/ig,function(m){
      return '&';
    });
  }
  return r.replace(/&/g,function(){
    return '&amp;';
  }).replace(/</g,function(m){
    return '&lt;';
  });
},
stripTags:function(d){
  return d.toString().replace(/<[^>]+>/ig,'');
},
loadCSS:function(){
  var s=document.getElementsByTagName('script');
  var i=s.length;
  var p='alpha.css';
  while(i--){
    var t=s[i].src.split('?')[0];
    if(!t.match(/alpha\.js$/g)){continue;}
    p=t.replace(/\.js$/g,'.css');
    break;
  }
  var l=document.createElement('link');
  l.rel='stylesheet';
  l.type='text/css';
  l.media='screen,print';
  l.href=p+'?v='+this.version;
  document.head.appendChild(l);
  return true;
},
temp:function(){
  return false;
}};


