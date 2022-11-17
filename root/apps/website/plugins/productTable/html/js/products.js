/* products.js */
window.products={
newProduct:function(){
  return _productTable.go('newProduct');
},
/* parse products regard to status, access and type */
parse:function(status,access,type){
  /* prepare types */
  var types={
    status:['publish','draft','trash'],
    access:['public','private'],
    type:['product','post','page','training','article','event'],
  };
  /* prepare arguments */
  status=typeof status==='string'
    &&types.status.indexOf(status)>=0
    ?status:types.status[0];
  access=typeof access==='string'
    &&types.access.indexOf(access)>=0
    ?access:types.access[0];
  type=typeof type==='string'
    &&types.type.indexOf(type)>=0
    ?type:types.type[0];
  /* prepare element option detail */
  var pod=document.getElementById('products-option-detail');
  if(pod){
    pod.innerText='Status: '+status
      +'; Access: '+access
      +'; Type: '+type
      +';';
  }
  /* prepare element */
  var pl=document.querySelector('#products-list');
  /* check element and data */
  if(!pl||!Array.isArray(_productTable.PAGE.data)){
    return false;
  }var data=_productTable.PAGE.data,i=data.length;
  /* clear the field */
  _productTable.clearElement(pl);
  /* parse data */
  while(i--){
    /* check status and access privilege */
    if(data[i].status!=status
      ||data[i].access!=access
      ||data[i].type!=type){
      continue;
    }
    /* parse line */
    var pa=window.products.parseLine(data[i]);
    /* append to products list */
    pl.appendChild(pa);
  }
  /* re-construct all anchors */
  //return _productTable.initAnchors();
},
/* line field */
parseLine:function(post){
  /* check post object */
  if(typeof post!=='object'||post===null){return false;}
  /* create elements */
  var pa=document.createElement('div');
  var ph=document.createElement('div');
  var pb=document.createElement('div');
  var pd=document.createElement('div');
  var view=document.createElement('button');
  var edit=document.createElement('button');
  var del=document.createElement('button');
  var viewIcon=document.createElement('i');
  var editIcon=document.createElement('i');
  var delIcon=document.createElement('i');
  /* add classes */
  pa.classList.add('post-data-each');
  ph.classList.add('post-data-head');
  pb.classList.add('post-data-body');
  pd.classList.add('post-data-detail');
  view.classList.add('button-blue');
  edit.classList.add('button-green');
  del.classList.add('button-red');
  viewIcon.classList.add('fa');
  viewIcon.classList.add('fa-search');
  editIcon.classList.add('fa');
  editIcon.classList.add('fa-edit');
  delIcon.classList.add('fa');
  delIcon.classList.add('fa-trash');
  /* add value */
  pa.id='post-id-'+post.aid;
  ph.innerText=post.title;
  ph.title=post.title;
  pd.innerText=post.datetime+' - '
    +(post.author==_productTable.USER.id?'Yours':post.author);
  view.innerText='View';
  view.title='View this post';
  view.dataset.url=WEBSITE_ADDRESS
    +_productTable.PRODUCT_URL
    +'?id='+post.url;
  edit.innerText='Edit';
  edit.title='Edit this post';
  edit.dataset.id=post.aid;
  del.innerText='Delete';
  del.delete='Delete this post';
  del.dataset.id=post.aid;
  del.dataset.status=post.status;
  /* click event */
  view.onclick=function(e){
    return window.open(this.dataset.url,'_blank');
  };
  edit.onclick=function(e){
    return _productTable.go('editProduct?aid='+this.dataset.id);
  };
  del.onclick=function(e){
    var postID=this.dataset.id;
    var productstatus=this.dataset.status;
    var cTitle='Delete';
    var cText='Delete this post?';
    var cTextLoader='Deleting...';
    var rmethod='deleteProduct';
    return _productTable.confirm(cTitle,cText,function(yes){
      if(!yes){return false;}
      _productTable.loader(true,cTextLoader);
      return _productTable.request(rmethod,function(r){
        _productTable.loader(false);
        if(r!=='OK'){return _productTable.error(r);}
        var pas=document.getElementById('post-id-'+postID);
        if(pas){pas.parentElement.removeChild(pas);}
        var tit=document.querySelector('.title');
        if(tit){
          var cl=tit.innerText.match(/\((\d+)\)/),
          ll=parseInt(cl[1],10)-1;
          tit.innerText='My Products ('+ll+')';
        }
        return _productTable.success(r);
      },{aid:postID});
    });
  };
  /* prepend button elements */
  view.insertBefore(viewIcon,view.firstChild);
  edit.insertBefore(editIcon,edit.firstChild);
  del.insertBefore(delIcon,del.firstChild);
  /* append elements */
  pb.appendChild(view);
  pb.appendChild(edit);
  pb.appendChild(del);
  pa.appendChild(ph);
  pa.appendChild(pd);
  pa.appendChild(pb);
  /* image */
  var img=new Image,
  pics=JSON.parse(post.picture);
  img.src=pics[0];
  img.style.float='right';
  img.style.maxHeight='100px';
  img.style.marginTop='-100px';
  pa.appendChild(img);
  /* return post element */
  return pa;
},
/* update data */
update:function(id,update){
  if(!id.match(/^\d+$/)
    ||!_productTable.PAGE.data
    ||typeof update!=='object'
    ||update===null){return false;}
  var done=false;
  for(var i=0;i<_productTable.PAGE.data.length;i++){
    if(_productTable.PAGE.data[i].aid==id){
      for(var k in update){
        _productTable.PAGE.data[i][k]=update[k];
      }done=true;break;
    }
  }return done;
},
/* initailize */
init:function(data){
  //alert(JSON.stringify(data));
  _productTable.PAGE=data;
  /* prepare elements */
  var status=document.querySelector('select[name="status"]');
  var access=document.querySelector('select[name="access"]');
  var type=document.querySelector('select[name="type"]');
  if(!status||!access||!type){
    return _productTable.error('Error: Some element is not detected.');
  }
  /* pre request */
  var preType=_productTable.path.split('/');
  if(preType.length>=4){
    type.value=preType[3];
    if(preType.length>=5){
      status.value=preType[4];
      if(preType.length>=6){
        access.value=preType[5];
      }
    }
  }
  /* on change event */
  status.onchange=function(e){
    return window.products.parse(status.value,access.value,type.value);
  };
  access.onchange=function(e){
    return window.products.parse(status.value,access.value,type.value);
  };
  type.onchange=function(e){
    return window.products.parse(status.value,access.value,type.value);
  };
  /* disabled */
  status.disabled=true;
  access.disabled=true;
  type.disabled=true;
  /* return parse the post */
  return products.parse(status.value,access.value,type.value);
}
};


