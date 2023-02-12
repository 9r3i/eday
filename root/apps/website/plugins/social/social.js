/**
 * social.js
 * ~ an eday website plugin for sharing to social media
 * started at november 14th 2022
 * @requires ForceWebsite (for like only), QRCode
 */
;function social(param){
this.param=Array.isArray(param)?param:[];
this.init=function(plug){
  if(!window.location.search.match(/^\?id=/)){
    return;
  }
  const _social=this;
  this.onContentReady(r=>{
    var pr=r.parentNode,
    shr=this.buildElement('div',null,{
      id:'sharer',
    }),
    lkr=this.buildElement('div',null,{
      'class':'like',
      id:'like',
    },[
      this.buildElement('span','Loading...',{
        'class':'like-loading'
      })
    ]),
    qrc=this.buildElement('div',null,{
      id:'qrcode',
    }),
    scl=this.buildElement('div',null,{
      'class':'social',
      id:'social',
    },[]);
    if(this.param.indexOf('sharer')>=0
      ||this.param.indexOf('like')>=0
      ||this.param.indexOf('qrcode')>=0){
      pr.insertBefore(scl,r);
    }
    if(this.param.indexOf('qrcode')>=0){
      qrc.appendTo(scl);
      _social.qrcode.init();
    }
    if(this.param.indexOf('sharer')>=0){
      shr.appendTo(scl);
      _social.sharer.init();
    }
    if(this.param.indexOf('like')>=0){
      lkr.appendTo(scl);
      _social.like.init(r.dataset.id);
    }
  });
};
/* on content ready -- helper for plugins */
this.onContentReady=function(cb,i){
  cb=typeof cb==='function'?cb:function(){};
  i=i?parseInt(i,10):0;
  let c=document.getElementById('social-plugin'),
  _social=this;
  if((c&&!/Loading\.{3}/.test(c.innerHTML))||i>500){
    return cb(c&&!/Loading\.{3}/.test(c.innerHTML)?c:false,i);
  }i++;
  setTimeout(e=>{
    _social.onContentReady(cb,i);
  },10);
};
/* build element */
this.buildElement=function(tag,text,attr,children,html,content){
  let div=document.createElement(typeof tag==='string'?tag:'div');
  div.appendTo=function(el){
    if(typeof el==='object'&&el!==null
      &&typeof el.appendChild==='function'){
      el.appendChild(this);
      return true;
    }return false;
  };
  div.remove=function(){
    if(!this.parentNode
      ||typeof this.parentNode.removeChild!=='function'){
      return;
    }this.parentNode.removeChild(this);
  };
  if(typeof text==='string'){
    div.innerText=text;
  }
  if(typeof attr==='object'&&attr!==null){
    for(let i in attr){
      div.setAttribute(i,attr[i]);
    }
  }
  if(Array.isArray(children)){
    for(let i=0;i<children.length;i++){
      if(typeof children[i]==='object'
        &&children[i]!==null
        &&typeof children[i].appendChild==='function'){
        div.appendChild(children[i]);
      }
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
/* qrcode object -- requires: QRCode */
this.qrcode={
  version:'1.0.0',
  init:function(){
    window.SocialQR=this;
    var _qrcode=this,
    PS=document.getElementById('qrcode');
    if(!PS){return;}
    PS.title='This post to QRCode';
    PS.innerHTML='<span class="qrcode-image"></span>'
      +'<span class="qrcode-label">QRCode</span>';
    PS.onclick=function(e){
      _qrcode.dialog(function(d){
        d.innerHTML='<div id="qrcode-result"></div>';
        var res=new QRCode("qrcode-result",{
          text:window.location.href,
          width:200,
          height:200,
          colorDark:"#000000",
          colorLight:"#ffffff",
          correctLevel:QRCode.CorrectLevel.H
        });
      });
    };
  },
  dialog:function(c){
    var _qrcode=this,
    r=document.getElementById('qrcode-dialog');
    if(r){r.parentElement.removeChild(r);}
    var d=document.createElement('div');
    d.id="qrcode-dialog";
    d.innerHTML='<div id="qrcode-dialog-bg"></div><div id="qrcode-dialog-content"></div>';
    document.body.appendChild(d);
    var bg=document.getElementById('qrcode-dialog-bg');
    if(bg){
      bg.onclick=function(e){_qrcode.dialog_close();};
      bg.oncontextmenu=function(e){_qrcode.dialog_close();};
    }
    var r=document.getElementById('qrcode-dialog-content');
    if(c){c(r);}
  },
  dialog_close:function(){
    var r=document.getElementById('qrcode-dialog');
    if(r){r.parentElement.removeChild(r);}
  }
};
/* like object */
this.like={
  version:'2.2.0',
  init:function(id){
    var _like=this,
    is_liked=localStorage.getItem('like-'+id)?true:false,
    likes=is_liked?1:0,
    el=document.getElementById('like');
    if(!el){return false;}
    ForceWebsite.fetch('like.get',r=>{
      likes=r;
      el.innerHTML='<div class="like'+(is_liked?' liked':'')+'" '
          +'title="Like'+(is_liked?'d':'')+' this post" '
          +'id="like-button" data-id="'+id+'">'
        +'<div class="like-image"></div>'
        +'<div class="like-label">Like'+(is_liked?'d':'')+'</div>'
        +'</div>'
        +'<div class="like-count" title="'+likes+'">'
        +'<div class="before"></div>'
        +'<div class="after">'+likes+'</div>'
        +'</div>';
      var dlike=document.getElementById('like-button');
      if(!dlike){return;}
      dlike.onclick=function(e){
        var id=this.dataset.id;
        if(localStorage.getItem('like-'+id)){return false;}
        return _like.likePost(id);
      };
    },{id:id});
  },
  likePost:function(id){
    var lf=document.getElementById('like-form');
    if(lf){lf.parentElement.removeChild(lf);}
    var lc=document.querySelector('div[class="like"]');
    if(lc){
      lc.classList.add('liked');
      lc.title='Liked';
    }
    var ll=document.querySelector('div[class="like-label"]');
    if(ll){
      ll.innerText='Liked';
    }
    var lco=document.querySelector('div[class="like-count"]');
    if(lco){
      var count=parseInt(lco.title,10);
      lco.title=count+1;
      lco.children[1].innerText=count+1;
    }
    ForceWebsite.fetch('like.put',function(r){
      if(typeof r==='string'&&r=='OK'){
        localStorage.setItem('like-'+id,'true');
        return true;
      }
      lco.title=count;
      lco.children[1].innerText=count;
      ll.innerText='Like';
      lc.title='Like';
      lc.classList.remove('liked');
    },{
      id:id,
    });
  },
};
/* sharer object */
this.sharer={
  version:'1.3.0',
  init:function(){
    window.SocialSharer=this;
    var _sharer=this,
    PS=document.getElementById('sharer');
    if(!PS){return;}
    PS.title='Share this post';
    PS.innerHTML='<span class="sharer-image"></span>'
      +'<span class="sharer-label">Share</span>';
    var url=encodeURIComponent(document.location.href),
    title=encodeURIComponent(document.getElementsByTagName('title')[0].innerHTML),
    desc='',
    media=encodeURIComponent(document.location.protocol+'//'+document.location.host+'/files/images/luthfie-logo.png');
    PS.onclick=function(e){
      _sharer.dialog(function(d){
        d.innerHTML='<div class="sharer-header">Share to</div>';
        d.innerHTML+='<a href="javascript:SocialSharer.open(\'facebook\')" title="Share to Facebook">'
          +'<div class="sharer-each sharer-facebook">Facebook</div></a>';
        d.innerHTML+='<a href="javascript:SocialSharer.open(\'twitter\')" title="Share to Twitter">'
          +'<div class="sharer-each sharer-twitter">Twitter</div></a>';
        d.innerHTML+='<a href="javascript:SocialSharer.open(\'whatsapp\')" title="Share to Whatsapp">'
          +'<div class="sharer-each sharer-whatsapp">Whatsapp</div></a>';
        d.innerHTML+='<a href="javascript:SocialSharer.open(\'linkedin\')" title="Share to LinkedIn">'
          +'<div class="sharer-each sharer-linkedin">LinkedIn</div></a>';
        d.innerHTML+='<a href="javascript:SocialSharer.open(\'telegram\')" title="Share to Telegram">'
          +'<div class="sharer-each sharer-telegram">Telegram</div></a>';
        d.innerHTML+='<a href="javascript:SocialSharer.open(\'pinterest\')" title="Share to Pinterest">'
          +'<div class="sharer-each sharer-pinterest">Pinterest</div></a>';
        d.innerHTML+='<a href="javascript:SocialSharer.open(\'tumblr\')" title="Share to Tumblr">'
          +'<div class="sharer-each sharer-tumblr">Tumblr</div></a>';
      });
    };
  },
  open:function(l){
    var url=encodeURIComponent(document.location.href),
    title=encodeURIComponent(document.getElementsByTagName('title')[0].innerHTML),
    desc=encodeURIComponent(document.querySelector('meta[name="description"]').content),
    media=encodeURIComponent(document.location.protocol+'//'+document.location.host+'/files/images/luthfie-logo.png'),
    link={
      whatsapp:!window.hasOwnProperty('ontouchstart')
        ?'https://api.whatsapp.com/send?text='+title+'%20~%20'+url
        :'whatsapp://send?text='+title+'%20~%20'+url,
      facebook:'http://www.facebook.com/share.php?v=4&u='+url+'&t='+title,
      twitter:'http://twitter.com/share?text='+title+'&url='+url+'&via=',
      telegram:'https://telegram.me/share/url?url='+url,
      gplus:'https://plus.google.com/share?url='+url,
      linkedin:'http://www.linkedin.com/shareArticle?mini=true&url='+url+'&title='+title+'&summary='+desc,
      pinterest:'http://pinterest.com/pin/create/button/?url='+url+'&media='+media+'&description='+title,
      tumblr:'http://tumblr.com/widgets/share/tool?canonicalUrl='+url+'&title='+title+'&caption='+desc,
    };
    this.dialog_close();
    if(link[l]){
      window.open(link[l],'_blank');
    }
  },
  dialog:function(c){
    var _sharer=this,
    r=document.getElementById('sharer-dialog');
    if(r){r.parentElement.removeChild(r);}
    var d=document.createElement('div');
    d.id="sharer-dialog";
    d.innerHTML='<div id="sharer-dialog-bg"></div><div id="sharer-dialog-content"></div>';
    document.body.appendChild(d);
    var bg=document.getElementById('sharer-dialog-bg');
    if(bg){
      bg.onclick=function(e){_sharer.dialog_close();};
      bg.oncontextmenu=function(e){_sharer.dialog_close();};
    }
    var r=document.getElementById('sharer-dialog-content');
    if(c){c(r);}
  },
  dialog_close:function(){
    var r=document.getElementById('sharer-dialog');
    if(r){r.parentElement.removeChild(r);}
  }
};
};


/* implements social */
(function(){
  setInterval(()=>{
    return socialSharerInitialize();
  },1000);
})();

/* initialize */
function socialSharerInitialize(){
if(!window.location.search.match(/^\?id=/)){
  return;
}
const addon=document.getElementsByClassName('pts-social');
if(!addon||addon.length<1){
  return;
}
/* prepare social options and element */
const socialOption=[],
socialElOld=document.getElementById('social-plugin'),
socialEl=document.createElement('div');
socialEl.id='social-plugin';
if(socialElOld){
  return;
}
if(SOCIAL_OPTION.sharer){
  socialOption.push('sharer');
}
if(SOCIAL_OPTION.qrcode){
  socialOption.push('qrcode');
}
/* append to first addon */
addon[0].appendChild(socialEl);
/* execute social sharer */
const socialSharer=new social(socialOption);
return socialSharer.init();
}
