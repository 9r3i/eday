/* action uri */
var WEB_CONTACT_ACTION_URI;

/* initialize web contact form */
webContactInit();

/* initial */
function webContactInit(){
  /* get elements */
  var sapaan=document.querySelector('select[name="sapaan"]');
  var nama=document.querySelector('input[name="nama"]');
  var from=document.querySelector('input[name="from"]');
  var hp=document.querySelector('input[name="hp"]');
  var message=document.querySelector('textarea[name="message"]');
  var tombol=document.querySelector('input[name="tombol-kirim"]');
  var cc=document.querySelector('input[name="cc"]');
  var logo=document.querySelector('img.web-contact-logo');
  var keterangan=document.querySelector('#keterangan');
  /* check elements */
  if(!sapaan||!nama||!from||!hp||!message
    ||!tombol||!cc||!logo||!keterangan){
    return alert('Error: Failed to prepare message form.');
  }
  /* on focus */
  nama.onfocus=webContactInputFocus;
  from.onfocus=webContactInputFocus;
  hp.onfocus=webContactInputFocus;
  message.onfocus=webContactInputFocus;
  /* on change */
  nama.onchange=webContactInputChange;
  from.onchange=webContactInputChange;
  hp.onchange=webContactInputChange;
  message.onchange=webContactInputChange;
  /* on blur */
  nama.onblur=webContactInputChange;
  from.onblur=webContactInputChange;
  hp.onblur=webContactInputChange;
  message.onblur=webContactInputChange;
  /* click event */
  tombol.onclick=function(){
    return webContactSendMessage(sapaan,nama,from,hp,message,cc,keterangan);
  };
}
/* loader */
function webContactLogoLoading(stop){
  var logo=document.querySelector('img.web-contact-logo');
  if(!logo){return false;}
  if(stop){
    logo.classList.remove('web-contact-logo-loading');
    return true;
  }logo.classList.add('web-contact-logo-loading');
  return true;
}
/* focus */
function webContactInputFocus(e){
  this.style.border='2px solid #9d5';
}
/* change */
function webContactInputChange(e){
  this.style.border='1px solid #ccc';
}
/* error */
function webContactInputError(el){
  el.style.border='2px solid red';
  return false;
}
/* error */
function webContactErrorInfo(el){
  setTimeout(function(){
    el.innerHTML='';
  },2000);
  return false;
}
/* send message */
function webContactSendMessage(sapaan,nama,from,hp,message,cc,keterangan){
  /* check elements */
  if(!sapaan||!nama||!from||!hp||!message||!cc||!keterangan){
    return alert('Error: Imvalid message form.');
  }
  /* prepare regexp */
  var emailReg=/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
  var hpReg=/^\+?[\d\s\-]+$/;
  /* check name */
  if(!/\w/.test(nama.value)){
    keterangan.innerHTML='<span style="color:red;">Mohon isi nama lengkap dengan benar.</span>';
    webContactErrorInfo(keterangan);
    return webContactInputError(nama);
  }
  /* check email */
  if(!emailReg.test(from.value)){
    keterangan.innerHTML='<span style="color:red;">Mohon isi alamant email dengan benar.</span>';
    webContactErrorInfo(keterangan);
    return webContactInputError(from);
  }
  /* check hp */
  if(!hpReg.test(hp.value)){
    keterangan.innerHTML='<span style="color:red;">Mohon isi nomor handphone dengan benar.</span>';
    webContactErrorInfo(keterangan);
    return webContactInputError(hp);
  }
  /* check message */
  if(!/\w+/.test(message.value)){
    keterangan.innerHTML='<span style="color:red;">Mohon isi pesan dengan benar.</span>';
    webContactErrorInfo(keterangan);
    return webContactInputError(message);
  }
  /* put loader */
  var info=document.querySelector('.formulir');
  if(info){info.innerHTML='<h2>Mengirim...</h2>';}
  webContactLogoLoading();
  /* prepare url */
  var url=WEBSITE_ADDRESS+WEB_CONTACT_ACTION_URI;
  /* send message */
  return webContactPost(url,function(hasil){
    webContactLogoLoading(true);
    if(hasil.status=='OK'){
      info.innerHTML='<h2>Pesan terkirim!</h2>';
    }else if(hasil.status=='error'){
      info.innerHTML='<h2>'+hasil.message+'</h2>';
    }else{
      info.innerHTML='<h2>Unknown error!</h2>';
    }
  },{
    sapaan:sapaan.value,
    from_name:nama.value,
    from_email:from.value,
    hp:hp.value,
    message:message.value,
    cc:cc.checked,
    'web-contact-send':true
  },null,null,null,null,function(e){
    info.innerHTML='<h2>'+e+'</h2>';
  });
}
/* this function is cloned from header.js --> post function */
function webContactPost(url,callback,data,unform,upload,download,header,error){
  var xmlhttp=false;
  if(window.XMLHttpRequest){
    xmlhttp=new XMLHttpRequest();
  }else{
    var xhf=[function(){return new ActiveXObject("Msxml2.XMLHTTP");},function(){return new ActiveXObject("Msxml3.XMLHTTP");},function(){return new ActiveXObject("Microsoft.XMLHTTP");}];
    for(var i=0;i<xhf.length;i++){
      try{xmlhttp=xhf[i]();}
      catch(e){continue;}
      break;
    }
  }
  if(!xmlhttp){return;}
  var method=data?'POST':'GET';
  xmlhttp.open(method,url,true);
  this.uniform=function(data){
    var ret=[];
    for(var d in data){
      if(Array.isArray(data[d])||(typeof data[d]=='object'&&data[d]!==null)){
        ret.push(this.uniform(data[d]));
      }
      else{ret.push(encodeURIComponent(d)+"="+encodeURIComponent(data[d]));}
    }
    return ret.join("&");
  };
  if(data&&!unform){
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    data=this.uniform(data);
  }
  if(header&&typeof header=='object'&&header!=null){
    for(var i in header){
      xmlhttp.setRequestHeader(i,header[i]);
    }
  }
  xmlhttp.onreadystatechange=function(){
    var er=false;
    if(callback&&xmlhttp.readyState==4&&xmlhttp.status==200&&xmlhttp.responseText){
      try{var res=JSON.parse(xmlhttp.responseText);}
      catch(e){var res=xmlhttp.responseText;}
      return callback(res);
    }else if(xmlhttp.status==0){
      if(xmlhttp.readyState==4){
        er='error: no internet connection';
        //console.log(er);
      }
    }else if(xmlhttp.readyState<4){
      //console.log('state '+xmlhttp.readyState+' reading...');
    }else{
      er='error: '+xmlhttp.status+' '+xmlhttp.statusText;
      //console.log(er);
      //console.log(xmlhttp);
    }
    if(er){return error?error(er):callback?callback(er):false;}
  };
  if(upload){xmlhttp.upload.onprogress = upload;}
  if(download){xmlhttp.addEventListener("progress",download,false);}
  xmlhttp.send(data);
}


