/* product.js */
var W,D,CKEDITOR,PRODUCT_ACTION_URL,PRODUCT_PICTURE_FILE=null;




/* submit save the product */
function productSubmitSave(){
  var name=qs('input[name="name"]').value;
  var currency=qs('input[name="currency"]').value;
  var price=qs('input[name="price"]').value;
  var discount=qs('input[name="discount"]').value;
  var order_to=qs('input[name="order_to"]').value;
  var description=editorGetValue();
  var ribbon=qs('select[name="ribbon"]').value;
  var picture=typeof PRODUCT_PICTURE_FILE==='object'
    &&PRODUCT_PICTURE_FILE!==null
    ?PRODUCT_PICTURE_FILE:null;
  var picture_url=typeof PRODUCT_PICTURE_FILE==='string'?PRODUCT_PICTURE_FILE:'';
  var unform=false;
  var data={
    id:PRODUCT_ID,
    name:name,
    currency:currency,
    price:price,
    discount:discount,
    order_to:order_to,
    description:description,
    ribbon:ribbon,
    picture:picture_url,
    request:'saveProduct',
  };
  if(picture){
    var FD=new FormData();
    for(var i in data){FD.append(i,data[i]);}
    FD.append('picture',picture);
    unform=true;
    data=FD;
  }
  adminLoader();
  W.post(PRODUCT_ACTION_URL,function(r){
    adminLoader(false);
    if(r.match(/^error/ig)){return error(r);}
    else if(r=='OK'){
      return success(r,function(y){
        //W.location.assign('?'+SITE_ADMIN_KEY+'=product/all');
      });
    }
    console.log(r);
    return salert('Something is going wrong.');
  },data,unform,null,null,null,function(e){
    adminLoader(false);
    return error(e);
  });
}

/* picture in edit product */
function productPicturePreviewEdit(id){
  var picture=gebi(id);
  var preview=gebi('preview');
  if(!preview||!picture||PRODUCT_PICTURE==''){return false;}
      PRODUCT_PICTURE_FILE=PRODUCT_PICTURE;
      preview.style.display='block';
      picture.style.display='none';
      preview.innerHTML='<img src="'+PRODUCT_PICTURE+'" />';
      preview.firstChild.onclick=function(e){
        return confirmation('Delete this picture?','',function(yes){
          if(!yes){return false;}
          picture.style.display='block';
          preview.innerHTML='';
          preview.style.display='none';
          PRODUCT_PICTURE_FILE=null;
        });
      };
}

/* submit to add a product */
function productSubmitAdd(){
  var name=qs('input[name="name"]').value;
  var currency=qs('input[name="currency"]').value;
  var price=qs('input[name="price"]').value;
  var discount=qs('input[name="discount"]').value;
  var order_to=qs('input[name="order_to"]').value;
  var description=editorGetValue();
  var ribbon=qs('select[name="ribbon"]').value;
  var picture=PRODUCT_PICTURE_FILE;
  var unform=false;
  var data={
    name:name,
    currency:currency,
    price:price,
    discount:discount,
    order_to:order_to,
    description:description,
    ribbon:ribbon,
    request:'addProduct',
  };
  if(picture){
    var FD=new FormData();
    for(var i in data){FD.append(i,data[i]);}
    FD.append('picture',picture);
    unform=true;
    data=FD;
  }
  adminLoader();
  W.post(PRODUCT_ACTION_URL,function(r){
    adminLoader(false);
    if(r.match(/^error/ig)){return error(r);}
    else if(r=='OK'){
      return success(r,function(y){
        W.location.assign('?'+SITE_ADMIN_KEY+'=product/all');
      });
    }
    console.log(r);
    return salert('Something is going wrong.');
  },data,unform,null,null,null,function(e){
    adminLoader(false);
    return error(e);
  });
}

/* delete a product by id */
function productDeleteID(id){
  if(!id||!id.toString().match(/^\d+$/)){return error('Invalid ID.');}
  return confirmation('Delete this product?','',function(yes){
    if(!yes){return false;}
    adminLoader();
    var data={request:'deleteProduct',id:id};
    W.post(PRODUCT_ACTION_URL,function(r){
      adminLoader(false);
      if(r.match(/^error/ig)){return error(r);}
      else if(r=='OK'){
        return success(r,function(y){
          W.location.assign('?'+SITE_ADMIN_KEY+'=product/all');
        });
      }
      console.log(r);
      return salert('Something is going wrong.');
    },data,false,null,null,null,function(e){
      adminLoader(false);
      return error(e);
    });
  });
}

/* picture preview */
function productPicturePreview(id){
  var picture=gebi(id);
  var preview=gebi('preview');
  if(!preview||!picture){return false;}
  preview.style.display='none';
  PRODUCT_PICTURE_FILE=null;
  picture.addEventListener('change',function(e){
    PRODUCT_PICTURE_FILE=null;
    preview.style.display='block';
    var file=this.files[0];
    if(!file.type.match(/^image\//g)){
      preview.innerHTML='<span style="color:red;">Error: File is not image. '
        +'<em>(jpg/jpeg/png/gif)</em>.</span>';
      return false;
    }
    if(file.size>Math.pow(1024,2)*2){
      preview.innerHTML='<span style="color:red;">Error: File size is too large. '
        +'<em>(Max. 2 MB)</em>.</span>';
      return false;
    }
    var FR=new FileReader();
    FR.onloadend=function(e){
      picture.style.display='none';
      preview.innerHTML='<img src="'+e.target.result+'" />';
      PRODUCT_PICTURE_FILE=file;
      preview.firstChild.onclick=function(e){
        return confirmation('Delete this picture?','',function(yes){
          if(!yes){return false;}
          picture.style.display='block';
          preview.innerHTML='';
          preview.style.display='none';
          PRODUCT_PICTURE_FILE=null;
        });
      };
    };FR.readAsDataURL(file);
  },false);
}


