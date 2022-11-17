/* edit.js */
var edit={
/* save edited post */
save:function(data,opts){
  /* check data */
  if(typeof data!=='object'||data===null){
    return _admin.error('Invalid data object.');
  }
  /* check options */
  if(typeof opts!=='object'||opts===null
    ||!opts.hasOwnProperty('editElType')
    ||!opts.hasOwnProperty('selPic')
    ||!opts.hasOwnProperty('selDate')
    ||!opts.hasOwnProperty('delBut')
    ||!opts.hasOwnProperty('submit')){
    return _admin.error('Invalid options object.');
  }
  /* set options as variables */
  var editElType=opts.editElType;
  var selPic=opts.selPic;
  var selDate=opts.selDate;
  var delBut=opts.delBut;
  var submit=opts.submit;
  var _this=this;
  submit.value='Saving...';
  _admin.loader(true,'Saving...');
  /* send the request */
  return _admin.request('savePost',function(r){
    _admin.loader(false);
    /* enabled all inputable elements
     * and show all extension editor
     */
    _admin.disabled(editElType,false);
    _admin.disabled([submit,delBut],false);
    _this.editorShow();
    for(var ki in selDate){
      selDate[ki].show();
    }selPic.show();
    submit.value='Save';
    /* check error */
    if(r!=='OK'){return _admin.error(r);}
    return _admin.success(r);
  },function(e){
    _admin.loader(false);
    /* enabled all inputable elements
     * and show all extension editor
     */
    _admin.disabled(editElType,false);
    _admin.disabled([submit,delBut],false);
    _this.editorShow();
    for(var ki in selDate){
      selDate[ki].show();
    }selPic.show();
    submit.value='Save';
    return _admin.error(e);
  },data);
},
/* editor show */
editorShow:function(){
  return window.tinymce.activeEditor.show();
},
/* editor hide */
editorHide:function(){
  return window.tinymce.activeEditor.hide();
},
/* editor get value */
editorGetValue:function(){
  return window.tinymce.activeEditor.getContent();
},
/* load editor */
editorLoad:function(id,loaded){
  loaded=loaded?parseInt(loaded):0;
  var _this=this;
  if(loaded>9){return false;}
  if(typeof window.tinymce==='object'
    &&window.tinymce!==null
    &&loaded>0){
    window.tinymce.init({selector:'#'+id});
    return _this.editorTinymceAbout();
  }loaded++;
  return setTimeout(function(e){
    return _this.editorLoad(id,loaded);
  },200);
},
/* remove branding link on tinymce */
editorTinymceAbout:function(loaded){
  loaded=loaded?parseInt(loaded):0;
  if(loaded>99){return false;}
  var test=document.querySelector('span.mce-branding');
  if(test){
    test.style.display='none';
    var tos=document.querySelector('.mce-tinymce');
    if(tos){tos.style.removeProperty('width');}
    return true;
  }loaded++;
  var _this=this;
  return setTimeout(function(){
    return _this.editorTinymceAbout(loaded);
  },50);
},
/* initialize editor */
init:function(){
  /* prepare variables */
  window.tinymce=null;
  /* prepare variables */
  var inputs="url,title,content,picture".split(',');
  var selects="type,status,access,template".split(',');
  var allKeys="description,keywords,trainer,price,place,start,end,host,stock".split(',');
  var editData=_admin.PAGE.data;
  /* get elements */
  var editError=false,editSelect={},editElType={};
  /* element inputs */
  for(var i=0;i<inputs.length;i++){
    var tag=inputs[i]=='content'?'textarea':'input';
    var el=document.querySelector(tag+'[name="'+inputs[i]+'"]');
    if(!el){editError='Element for "'+inputs[i]+'" is not detected.';break;}
    editElType[inputs[i]]=el;
  }if(editError){return _admin.error('Error: '+editError);}
  /* element selects */
  for(var i=0;i<selects.length;i++){
    var el=document.querySelector('select[name="'+selects[i]+'"]');
    if(!el){editError='Element for "'+selects[i]+'" is not detected.';break;}
    editSelect[selects[i]]=el;
    editElType[selects[i]]=el;
  }if(editError){return _admin.error('Error: '+editError);}
  /* element for all keys */
  for(var i=0;i<allKeys.length;i++){
    var el=document.querySelector('input[name="'+allKeys[i]+'"]');
    if(!el){editError='Element for "'+allKeys[i]+'" is not detected.';break;}
    editElType[allKeys[i]]=el;
  }if(editError){return _admin.error('Error: '+editError);}
  /* get submit element */
  var submit=document.querySelector('input[name="submit"]');
  if(!submit){return _admin.error('Error: Element "submit" is not detected.');}
  /* get delete element */
  var delBut=document.querySelector('input[name="delete"]');
  if(!delBut){return _admin.error('Error: Element "delete" is not detected.');}
  /* switch element */
  var switchBut=document.querySelector('input[name="switch"]');
  if(!switchBut){return _admin.error('Error: Element "switch" is not detected.');}
  /* prepare visibilities */
  var cel=document.querySelectorAll('tr[data-'+editData.type+']');
  if(cel){for(var i=0;i<cel.length;i++){
    cel[i].style.display='table-row';
  }}
  /* select [type] change events */
  editSelect.type.onchange=function(){
    var trs=document.getElementsByTagName('tr');
    var type=this.value;
    for(var i=0;i<trs.length;i++){
      if(trs[i].dataset.hasOwnProperty(type)
        ||trs[i].dataset.hasOwnProperty('all')){
        trs[i].style.display='table-row';
      }else{
        trs[i].style.removeProperty('display');
      }
    }
  };
  /* set default values */
  for(var k in editElType){
    if(editData.hasOwnProperty(k)){
      if(k=='picture'){
        editElType[k].dataset.value=editData[k];
      }else{
        editElType[k].value=editData[k];
      }
    }
  }
  /* add date selector */
  var selDate={
    start:new dateSelect('input[name="start"]'),
    end:new dateSelect('input[name="end"]'),
  };
  /* prepare picture */
  var selPic=pictureSelect('input[name="picture"]');
  /* prepare content editor */
  var editorID='editor-content';
  editElType.content.id=editorID;
  this.editorLoad(editorID);
  /* prepare this object */
  var _this=this;
  /* submit as save */
  submit.onclick=function(e){
    /* disabled all inputable elements
     * and hide all extension editor
     */
    _admin.disabled(editElType,true);
    _admin.disabled([submit,delBut],true);
    _this.editorHide();
    for(var ki in selDate){
      selDate[ki].hide();
    }selPic.hide();
    /* prepare object options */
    var opts={
      editElType:editElType,
      selPic:selPic,
      selDate:selDate,
      delBut:delBut,
      submit:submit,
    };
    /* prepare object data to send */
    var data={
      aid:editData.aid,
    };
    /* prepare object data from element value */
    for(var k in editElType){
      if(k=='picture'){
        data[k]=editElType[k].dataset.value;
      }else if(k=='content'){
        data[k]=_this.editorGetValue();
      }else{
        data[k]=editElType[k].value;
      }
    }
    /* send the save request */
    return _this.save(data,opts);
  };
  /* delete post */
  delBut.onclick=function(e){
    return _admin.confirm('Delete Post',
      'Delete this post?',function(yes){
      if(!yes){return false;}
      _admin.loader(true,'Deleting...');
      return _admin.request('deletePost',function(r){
        _admin.loader(false);
        if(r!='OK'){
          return _admin.error(r);
        }_admin.success(r);
        return _admin.go('posts/posts');
      },function(e){
        _admin.loader(false);
        return _admin.error(e);
      },{id:_admin.PAGE.data.aid});
    });
  };
  /* switch event */
  switchBut.onclick=function(e){
    this.blur();
    if(this.dataset.type=='rich'){
      this.dataset.type='plain';
      this.value='Rich Text';
      return edit.editorHide();
    }else{
      this.dataset.type='rich';
      this.value='Plain Text';
      return edit.editorShow();
    }
  };
  /* return as this object */
  return this;
}};edit.init();


