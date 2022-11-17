/* Name     : Training Table
 * Author   : Luthfie
 * Email    : Luthfie@y7mail.com
 * Filename : training.js
 * cloned from training table Plugin for Dixie CMS
 * started at november 7th 2019 -- cloned
 */
var TRAINING_TABLE_POSTS;
var inputers={
  title:'title',
  date:'training_start',
  price:'price',
  trainer:'trainer',
  place:'place',
};
/* inputers for search */
for(var inputer in inputers){
  training_table_search(inputer,inputers[inputer]);
}
/* register dialog */
function training_table_reg(el){
  training_table_remove_dialog();
  var title = el.getAttribute('data-title');
  var trainer = el.getAttribute('data-trainer');
  var date = el.getAttribute('data-date');
  var D = document, W = window;
  var dialog_bg = D.createElement('div');
  dialog_bg.setAttribute('class','training-table-dialog-background');
  dialog_bg.innerHTML='<div class="training-table-dialog-closer" '
    +'onclick="training_table_remove_dialog()" title="Close"></div>';
  var dialog_c = D.createElement('div');
  dialog_c.setAttribute('class','training-table-dialog-center');
  dialog_c.setAttribute('id','tt_dialog_center');
  dialog_c.innerHTML = '<div class="training-table-dialog-content">'
    +'<h2 style="text-align:center;">Formulir Pendaftaran</h2>'
    +'<div class="tt-dialog-line"><strong>'+title+'</strong></div>'
    +'<div class="tt-dialog-line"><strong>'+date+'</strong></div>'
    +'<div class="tt-dialog-line">Trainer: <strong>'+trainer+'</strong></div>'
    +'<div class="tt-dialog-line">Nama Lengkap: '
      +'<input class="tt-dialog-input" id="tt_dialog_name" placeholder="Nama Lengkap" /></div>'
    +'<div class="tt-dialog-line">Alamat Email: '
      +'<input class="tt-dialog-input" id="tt_dialog_email" placeholder="youremail@domain.com" /></div>'
    +'<div class="tt-dialog-line">Nomor HP: '
      +'<input class="tt-dialog-input" id="tt_dialog_mobile" placeholder="+62 812-3456-7890" /></div>'
    +'<div class="tt-dialog-line">Pesan: '
      +'<textarea class="tt-dialog-text" id="tt_dialog_text"></textarea></div>'
    +'<div class="tt-dialog-line" style="text-align:right;">'
      +'<button class="training-table-submit-button" id="tt_dialog_submit">Kirim</button>'
      +'<button class="training-table-close-button" onclick="training_table_remove_dialog()">Tutup</button>'
      +'</div>'
    +'<a href="https://github.com/9r3i" title="9r3i" target="_blank">'
      +'<div class="tt-dialog-copyright" id="tt_dialog_copyright"></div></a>'
    +'</div>';
  D.body.appendChild(dialog_bg);
  D.body.appendChild(dialog_c);
  setTimeout(function(){
    dialog_c.classList.add('training-table-dialog-center-show');
  },10);
  D.getElementById('tt_dialog_submit').onclick = function(){
    var name = D.getElementById('tt_dialog_name');
    var email = D.getElementById('tt_dialog_email');
    var mobile = D.getElementById('tt_dialog_mobile');
    if(name.value==''){
      name.style.borderColor = "red";
      alert('Isi nama lengkap anda dengan benar');
      return;
    }else{name.style.borderColor = "#bbb";}
    if(email.value==''||!email.value.match(/^[a-z]{1}[a-z0-9_\.-]+@[a-z0-9-]+\.[a-z]{2,4}([a-z]{2,4})?$/i)){
      email.style.borderColor = "red";
      alert('Isi email anda dengan benar, sesuai format');
      return;
    }else{
      email.style.borderColor = "#bbb";
    }
    if(mobile.value==''||!mobile.value.match(/^\+?[\d- ]{7,20}$/i)){
      alert('Isi nomor HP anda dengan benar, sesuai format\nAtau seperti ini: 0812-3456-7890');
      mobile.style.borderColor = "red";return;
    }else{
      mobile.style.borderColor = "#bbb";
    }
    this.disabled = true;
    this.innerHTML = 'Mengirim...';
    var text = D.getElementById('tt_dialog_text');
    var message = text.value;
    text.disabled = true;
    name.disabled = true;
    email.disabled = true;
    mobile.disabled = true;
    return training_table_post('?training-table-submit-form',function(res){
      if(res.status=='OK'||res.status=='error'){
        alert(res.message);
      }else{
        alert('unknown error');
      }training_table_remove_dialog();
    },{
      title:title,
      trainer:trainer,
      date:date,
      message:message,
      name:name.value,
      email:email.value,
      mobile:mobile.value
    },null,null,null,null,function(e){
      alert(e);
      training_table_remove_dialog();
    });
  }
}
/* remove dialog */
function training_table_remove_dialog(){
  var D = document, W = window;
  var bg = D.getElementsByClassName('training-table-dialog-background');
  if(bg){
    var i = bg.length;
    while(i--){bg[i].parentElement.removeChild(bg[i]);}
  }
  var c = D.getElementById('tt_dialog_center');
  if(c){c.parentElement.removeChild(c);}
}
/* search inputers */
function training_table_search(name,indexKey){
  var input=document.getElementById('training_table_input_'+name);
  if(!input){return false;}
  input.onkeyup=function(e){
    var value=new RegExp(this.value,'i');
    var result=[];
    for(var i in TRAINING_TABLE_POSTS){
      var key=TRAINING_TABLE_POSTS[i].training_time_new;
      var choosen=document.querySelector('tr#tt_'+i);
      if(!TRAINING_TABLE_POSTS[i][indexKey].match(value)){
        result.push(i);
        choosen.setAttribute('style','display:none;');
      }else{
        choosen.removeAttribute('style');
      }
    }
  };
}
/* this function is cloned from header.js --> post function */
function training_table_post(url,callback,data,unform,upload,download,header,error){
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








