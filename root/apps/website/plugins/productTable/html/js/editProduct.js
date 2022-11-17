/* editProduct.js */
window.editProduct={
/* save edited post */
save:function(data,opts){
  /* check data */
  if(typeof data!=='object'||data===null){
    return _productTable.error('Invalid data object.');
  }
  /* check options */
  if(typeof opts!=='object'||opts===null
    ||!opts.hasOwnProperty('editElType')
    ||!opts.hasOwnProperty('selPic')
    ||!opts.hasOwnProperty('selDate')
    ||!opts.hasOwnProperty('submit')){
    return _productTable.error('Invalid options object.');
  }
  /* set options as variables */
  var editElType=opts.editElType;
  var selPic=opts.selPic;
  var selDate=opts.selDate;
  var submit=opts.submit;
  var _this=this;
  submit.value='Saving...';
  _productTable.loader(true,'Saving...');
  /* send the request */
  return _productTable.request('savePost',function(r){
    _productTable.loader(false);
    /* enabled all inputable elements
     * and show all extension editor
     */
    _productTable.disabled(editElType,false);
    _productTable.disabled([submit],false);
    _this.editorShow();
    for(var ki in selDate){
      selDate[ki].show();
    }selPic.show();
    submit.value='Save';
    /* check error */
    if(r!=='OK'){return _productTable.error(r);}
    return _productTable.success(r,function(){
      return _productTable.go('posts/posts');
    });
  },function(e){
    _productTable.loader(false);
    /* enabled all inputable elements
     * and show all extension editor
     */
    _productTable.disabled(editElType,false);
    _productTable.disabled([submit],false);
    _this.editorShow();
    for(var ki in selDate){
      selDate[ki].show();
    }selPic.show();
    submit.value='Save';
    return _productTable.error(e);
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
/* title or name */
nameInput:function(){
  var title=document.querySelector('.title'),
  inputTitle=document.querySelector('input[name="title"]');
  inputTitle.dataset.name='';
  inputTitle.onkeyup=function(e){
    title.innerText=this.value==''?'[ERROR_EMPTY]':this.value;
    this.dataset.name=this.value;
  };
  return inputTitle;
},
/* headwr */
header:function(rdata){
  rdata.data.contentReal=rdata.data.content;
  rdata.ndata={};
  rdata.ndata.images=JSON.parse(rdata.data.picture);
  rdata.ndata.categories=JSON.parse(rdata.data.keywords);
  rdata.ndata.spec=JSON.parse(rdata.data.description);
  
  return rdata;
},
/* initialize editor */
init:function(rdata){
  //alert(JSON.stringify(rdata));
  var ipicture=_productTable.pictureInput(
      'input[name="picture"]',
      rdata.ndata.images
    ), // pictures
  ititle=this.nameInput(), // name
  icontent=document.querySelector('textarea[name="content"]'), // description
  ihost=document.querySelector('input[name="host"]'), // currency
  iprice=document.querySelector('input[name="price"]'), // prive
  iplace=document.querySelector('input[name="place"]'), // bprice
  istock=document.querySelector('input[name="stock"]'), // stock
  ikeywords=_productTable.categoryInput(
      'input[name="keywords"]',
      rdata.ndata.categories
    ), // categories
  idesc=_productTable.spec(
      'input[name="spec-key"]',
      'input[name="spec-value"]',
      'div.spec-variables',
      rdata.ndata.spec
    ); // specification
  submit=document.querySelector('button[name="submit"]');
  submit.onclick=function(e){
    var data={
      aid:rdata.data.aid,
      title:ititle.value,
      picture:ipicture.dataset.pictures,
      content:icontent.value,
      host:ihost.value,
      price:iprice.value,
      place:iplace.value,
      stock:istock.value,
      keywords:ikeywords.getValue(),
      description:idesc.getValue(),
    };
    if(true){
      //return alert(JSON.stringify(data));
    }
    submit.disabled=true;
    submit.innerHTML='<i class="fa fa-pulse fa-spinner"></i> Saving...';
    _productTable.request('updateProduct',function(r){
      submit.disabled=false;
      submit.innerHTML='<i class="fa fa-save"></i> Save';
      //alert(JSON.stringify(r));
      if(typeof r==='object'&&r!==null){
        return _productTable.success(
          'Data has been updated.',function(e){
            
          });
      }
      return _productTable.error(JSON.stringify(r));
    },data);
  };
}
};


