/* admin.js */
var W,D,ADMIN_EDITOR,
  ADMIN_MCE_ABOUT=false,
  ADMIN_CKE_ABOUT=false,
  CKEDITOR,
  NICEDIT,
  NOEDITOR=null;


/* get editor value */
function editorGetValue(){
  if(ADMIN_EDITOR==='alpha'&&window.hasOwnProperty('ALPHA')){
    return ALPHA.on?ALPHA.getValue():false;
  }
  else if(ADMIN_EDITOR==='ckeditor'&&window.hasOwnProperty('CKEDITOR')){
    return CKEDITOR.instances.editor.getData();
  }
  else if(ADMIN_EDITOR==='tinymce'&&window.hasOwnProperty('tinymce')){
    return tinymce.activeEditor.getContent();
  }
  else if(ADMIN_EDITOR==='nicedit'&&window.hasOwnProperty('nicEditor')){
    return NICEDIT.getContent();
  }
  else{
    console.log('Error: Editor is not found.','Editor namespace: '+ADMIN_EDITOR);
    return NOEDITOR?NOEDITOR.value:false;
  }
}

/* load editor - [require: files/editors/<editor_namespace>] */
function loadEditor(id){
  /* [require: alpha] */
  if(ADMIN_EDITOR==='alpha'&&window.hasOwnProperty('ALPHA')){
    return ALPHA.on?ALPHA.editor(id):false;
  }
  /* [require: ckeditor] */
  else if(ADMIN_EDITOR==='ckeditor'&&window.hasOwnProperty('CKEDITOR')){
    CKEDITOR.config.height=150;
    CKEDITOR.config.width='auto';
    CKEDITOR.replace(id);
    return removeCKEditorAboutButton();
  }
  /* [require: tinymce] */
  else if(ADMIN_EDITOR==='tinymce'&&window.hasOwnProperty('tinymce')){
    tinymce.init({selector:'#'+id});
    return removeTinymceAboutButton();
  }
  /* [require: nicedit] */
  else if(ADMIN_EDITOR==='nicedit'&&window.hasOwnProperty('nicEditor')){
    new nicEditor({fullPanel:true}).panelInstance(id);
    window.NICEDIT=new nicEditors.findEditor(id);
    return true;
  }
  /* [if editor is not loaded or not found] */
  else{
    NOEDITOR=gebi(id);
    console.log('Error: Editor is not found.','Editor namespace: '+ADMIN_EDITOR);
    return NOEDITOR?true:false;
  }
}

/* load editor script - [unstable] */
function loadEditorScript(){
  var path=EDAY_EDITOR_PATH+ADMIN_EDITOR+'/'+ADMIN_EDITOR+'.js';
  var el=qs('script[src="'+path+'"]');
  return !el?load_script(path):false;
}

/* remove about bottom on CKEDITOR */
function removeCKEditorAboutButton(){
  if(ADMIN_CKE_ABOUT||ADMIN_EDITOR!=='ckeditor'){return false;}
  setTimeout(function(){
    var test=qs('span.cke_button__about_icon');
    if(!test){return removeCKEditorAboutButton();}
    test.parentElement.style.display='none';
    ADMIN_CKE_ABOUT=true;
  },100);
}

/* remove branding link on tinymce */
function removeTinymceAboutButton(){
  if(ADMIN_MCE_ABOUT||ADMIN_EDITOR!=='tinymce'){return false;}
  setTimeout(function(){
    var test=qs('span.mce-branding');
    if(!test){return removeTinymceAboutButton();}
    test.style.display='none';
    ADMIN_MCE_ABOUT=true;
  },100);
}

/* logout */
function adminLogout(){
  return confirmation('Logout?','',function(yes){
    if(!yes){return false;}
    W.location.assign('?admin=log/out');
  });
}

/* loader */
function adminLoader(t){
  var id='admin-loader';
  var el=gebi(id);
  if(el){el.parentElement.removeChild(el);}
  if(t===false){return false;}
  el=ce('div');
  el.id=id;
  el.classList.add(id);
  var text=ce('div');
  text.classList.add(id+'-text');
  text.classList.add('blink');
  el.appendChild(text);
  D.body.appendChild(el);
}


