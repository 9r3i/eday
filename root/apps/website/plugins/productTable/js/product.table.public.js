/* product.table.public.js */
;function productTablePublic(){
/* version */
this.version='1.0.0';
/* external variables */
this.WEBSITE_ADDRESS=typeof WEBSITE_ADDRESS==='string'
  ?WEBSITE_ADDRESS:window.location.origin+'/';
this.WEBSITE_TITLE=typeof WEBSITE_TITLE==='string'
  ?WEBSITE_TITLE:'WEBSITE';
this.PRODUCT_URL=typeof PRODUCT_URL==='string'?PRODUCT_URL:'';
this.PRODUCT_DATA=typeof PRODUCT_DATA==='object'
  &&PRODUCT_DATA!==null?PRODUCT_DATA:{};
this.HTML_DATA=typeof HTML_DATA==='object'
  &&HTML_DATA!==null?HTML_DATA:{};
this.AJAX_QUERY=typeof AJAX_QUERY==='object'
  &&AJAX_QUERY!==null?AJAX_QUERY:{key:'key',value:'value'};
/* global variables */
window._productTablePublic=this;
/* internal variables */
this.content=document.getElementById('website-body');
this.query=null;
this.isSingle=false;
this.resizeData=null;
/* initialize */
this.init=function(){
  //this.cartData(false);
  /* remove all elements */
  this.clearElement(this.content);
  /* ger query */
  this.query=this.parseStr(window.location.search.substring(1));
  this.isSingle=window.location.pathname=='/'+this.PRODUCT_URL
                &&this.query.hasOwnProperty('id');
  /* initialize anchors */
  this.initAnchors();
  /* initialize buyer */
  this.buyerInit();
  /* WINDOW_EVENTS */
  if(typeof WINDOW_EVENTS==='object'&&WINDOW_EVENTS!==null){
    /* add on resize event */
    WINDOW_EVENTS.onresize.push(function(e){
      return _productTablePublic.onresize(e);
    });
    /* popstate */
    WINDOW_EVENTS.onpopstate.push(function(e){
      return _productTablePublic.popstate(e);
    });
    /* execute all window events */
    WINDOW_EVENTS.execAll();
  }
  /* start single view */
  if(this.query.hasOwnProperty('page')){
    return this.pageLoad(window.location.search);
  }
  /* start single view */
  if(this.isSingle){
    return this.singleStart();
  }
  /* start bulk view */
  var tag=this.query.hasOwnProperty('tag')?this.query.tag:null,
  store=this.query.hasOwnProperty('store')?this.query.store:null;
  return this.bulkStart(tag,store);
};
/* popstate  */
this.popstate=function(e){
  this.query=this.parseStr(window.location.search.substring(1));
  if((history.state&&history.state.type=='single')
    ||(window.location.pathname=='/'+this.PRODUCT_URL
    &&this.query.hasOwnProperty('id'))){
    return this.singleStart();
  }
  if((history.state&&history.state.type=='category')
    ||(window.location.pathname=='/'+this.PRODUCT_URL
    &&this.query.hasOwnProperty('tag'))){
    return this.bulkStart(this.query.tag);
  }
  if((history.state&&history.state.type=='store')
    ||(window.location.pathname=='/'+this.PRODUCT_URL
    &&this.query.hasOwnProperty('store'))){
    return this.bulkStart(null,this.query.store);
  }
  if((history.state&&history.state.type=='page')
    ||(window.location.pathname=='/'+this.PRODUCT_URL
    &&this.query.hasOwnProperty('page'))){
    return this.pageLoad(window.location.search);
  }
  if(window.location.pathname=='/'){
    return this.bulkStart();
  }
};
/* anchors */
this.initAnchors=function(){
  var _this=this,
  an=document.querySelectorAll('a[href]'),
  i=an.length;
  while(i--){
    an[i].onclick=function(e){
      e.preventDefault();
      _this.menuHide();
      _this.buyerContentClose();
      if(this.href===_this.WEBSITE_ADDRESS){
        window.history.pushState({},'',this.href);
        return _this.bulkStart();
      }else if(this.href.match(/\?admin$/)){
        return _this.externalPage(this.href,this.innerText);
      }else if(this.href
        .substr(_this.WEBSITE_ADDRESS.length)
        .match(/^apps\//)){
        return _this.externalPage(this.href,this.innerText);
      }else if(this.target=='_blank'){
        return window.open(this.href,'_blank');
      }else if(this.href.match(/\?page=[a-zA-Z0-9\-]+$/)){
        return _this.pageLoad(this.href);
      }
      return window.location.assign(this.href);
    };
  }
};
/* menu hide */
this.menuHide=function(){
  /* get elements */
  var menu=document.getElementById('menu');
  var shade=document.getElementById('menu-shadow');
  var isOverflow=menu.classList.contains('menu-overflow');
  if(!shade||!menu||!isOverflow){return false;}
  /* get elements */
  document.body.classList.remove('dont-scroll');
  menu.classList.remove('menu-overflow-show');
  shade.parentElement.removeChild(shade);
  return true;
};
/* on resize */
this.onresize=function(e){
  this.query=this.parseStr(window.location.search.substring(1));
  this.isSingle=window.location.pathname=='/'+this.PRODUCT_URL
                &&this.query.hasOwnProperty('id');
  if(this.isSingle||!this.resizeData){return false;}
  return this.bulkData(this.resizeData);
};


/* page content */
this.pageContent=function(url,content){
  var _this=this,
  doc=(new DOMParser).parseFromString(content,'text/html'),
  body=doc.getElementById('website-content'),
  title=doc.querySelector('.post-title'),
  nbody=this.buildElement('div',null,{
    'class':'pt-page',
  });
  this.buyerContentClose();
  if(!doc||!body){
    return this.error('Error: Failed to load content.');
  }
  window.history.replaceState({
    type:'page',
    data:content,
    title:title?title.innerText:'',
  },'',url);
  if(title){
    this.pageTitle(title.innerText);
  }
  nbody.innerHTML=body.innerHTML;
  this.content.innerHTML='';
  this.content.appendChild(nbody);
};
/* page load */
this.pageLoad=function(url){
  var _this=this,
  m=url.match(/\?page=([a-zA-Z0-9\-]+)$/);
  if(history.state&&history.state.type=='page'
    &&history.state.hasOwnProperty('data')){
    return this.pageContent(url,history.state.data);
  }
  window.history.pushState({
    type:'page',
    data:'',
  },'',url);
  if(!m||!m[1]){
    return _this.error('Error: Invalid page URL.');
  }
  this.loader(true,'Loading...');
  return this.getContent(m[1]+'.html',function(r){
    _this.loader(false);
    return _this.pageContent(url,r);
  },function(e){
    _this.loader(false);
    return _this.error('Error: Failed to load page.');
  });
};
/* page title */
this.pageTitle=function(title,html){
  var tel=document.querySelector('title');
  if(!tel){return false;}
  if(html){
    tel.innerHTML=title;
  }else{
    tel.innerText=title;
  }return true;
};



/* buyer */
this.buyerBody=null;
this.buyerUser=null;
/* initialize buyer */
this.buyerInit=function(){
  this.buyerUser=this.buyerData();
  var buyerContent=this.buildElement('div',null,{
    'class':'buyer-content',
  }),
  header=this.buyerHeader(),
  closeButton=this.buildElement('button',null,{
    'class':'buyer-movable-footer-button',
    'id':'buyer-movable-footer-button',
  },[
    this.buildElement('i',null,{
      'class':'fa fa-close',
    }),
    this.buildElement('span','Close'),
  ]),
  movableFooter=this.buildElement('div',null,{
    'class':'buyer-movable-footer',
  },[closeButton]),
  buyerButton=this.buildElement('button',null,{
    'class':'buyer-movable-button',
    'id':'buyer-movable-button',
  },[
    this.buildElement('i',null,{
      'class':'fa fa-shopping-cart',
    }),
    this.buildElement('span','Shopping Cart'),
  ]),
  movable=this.buildElement('div',null,{
    'class':'buyer-movable-content',
    'id':'buyer-movable-content',
  },[header,buyerContent,movableFooter]),
  footer=document.getElementById('website-footer');
  this.buyerBody=buyerContent;
  buyerButton.movableContent=movable;
  buyerButton.movableButton=buyerButton;
  buyerButton.movableClose=closeButton;
  buyerButton.onclick=function(){
    var cn='buyer-movable-content-show',
    ccn='buyer-movable-footer-button-up',
    cnn='buyer-movable-button-up';
    if(this.movableContent.classList.contains(cn)){
      this.movableContent.classList.remove(cn);
      this.movableButton.classList.remove(cnn);
      this.movableClose.classList.remove(ccn);
    }else{
      this.movableContent.classList.add(cn);
      this.movableButton.classList.add(cnn);
      this.movableClose.classList.add(ccn);
    }
  };
  closeButton.movableContent=movable;
  closeButton.movableButton=buyerButton;
  closeButton.movableClose=closeButton;
  closeButton.onclick=function(){
    var cn='buyer-movable-content-show',
    ccn='buyer-movable-footer-button-up',
    cnn='buyer-movable-button-up';
    if(this.movableContent.classList.contains(cn)){
      this.movableContent.classList.remove(cn);
      this.movableButton.classList.remove(cnn);
      this.movableClose.classList.remove(ccn);
    }else{
      this.movableContent.classList.add(cn);
      this.movableButton.classList.add(cnn);
      this.movableClose.classList.add(ccn);
    }
  };
  if(footer){
    this.clearElement(footer);
    footer.appendChild(movable);
    footer.appendChild(buyerButton);
  }
  this.buyerContent('cart');
};
/* close movable content */
this.buyerContentClose=function(){
  var idn='buyer-movable-content',
  idnn='buyer-movable-button',
  idcn='buyer-movable-footer-button',
  eln=document.getElementById(idn),
  elnn=document.getElementById(idnn),
  elcn=document.getElementById(idcn),
  rateDialog=document.getElementById('rate-dialog'),
  cn='buyer-movable-content-show',
  cnn='buyer-movable-button-up',
  ccn='buyer-movable-footer-button-up',
  res=0;
  if(eln&&eln.classList.contains(cn)){
    eln.classList.remove(cn);
    res++;
  }
  if(elnn&&elnn.classList.contains(cnn)){
    elnn.classList.remove(cnn);
    res++;
  }
  if(elcn&&elcn.classList.contains(ccn)){
    elcn.classList.remove(ccn);
    res++;
  }
  if(rateDialog){
    rateDialog.parentElement.removeChild(rateDialog);
  }
  return res;
};
/* buyer update */
this.buyerContent=function(k,d){
  var el=null,pl=null;
  if(k==='cart'){
    pl=this.cartContent(d);
    el=pl.body;
  }else if(k==='login'){
    pl=this.buyerLoginForm(d);
    el=pl.body;
  }else if(k==='register'){
    pl=this.buyerRegisterForm(d);
    el=pl.body;
  }else if(k==='edit'){
    pl=this.buyerProfileEditForm(d);
    el=pl.body;
  }else if(k==='profile'){
    pl=this.buyerProfile(d);
    el=pl.body;
  }else if(k==='orders'){
    pl=this.buyerOrders(d);
    el=pl.body;
  }else if(k==='checkout'){
    pl=this.orderCheckout(d);
    el=pl.body;
  }
  if(!el||typeof el.appendChild!=='function'){
    return false;
  }
  this.clearElement(this.buyerBody);
  this.buyerBody.appendChild(el);
  return true;
};
/* buyer register form content */
this.buyerRegisterForm=function(){
  var build=this.buildElement,
  form={
    name:build('input',null,{
      'class':'form-input',
      type:'text',
      name:'name',
      placeholder:'Full Name',
    }),
    phone:build('input',null,{
      'class':'form-input',
      type:'number',
      name:'phone',
      placeholder:'Example: 081234567890',
    }),
    password:build('input',null,{
      'class':'form-input',
      type:'password',
      name:'password',
      placeholder:'Password for Login',
    }),
    address:build('textarea',null,{
      'class':'form-textarea',
      type:'textarea',
      name:'address',
      placeholder:'Recipient Address',
    }),
    button:build('button',null,{
      'class':'form-button',
    },[
      build('i',null,{
        'class':'fa fa-send'
      }),
      build('span','Register'),
    ]),
    title:build('div','Registration Form',{
      'class':'form-title'
    }),
    tologin:build('a','Login Now'),
    reged:build('span','I have account. '),
  },
  _this=this;
  form.table=build('table',null,{
      'class':'form-table',
      cellpadding:'0',
      cellspacing:'5px',
      border:'0',
    },[
      build('tbody',null,{},[
        build('tr',null,{},[
          build('td','Name'),
          build('td',null,{},[form.name]),
        ]),
        build('tr',null,{},[
          build('td','Phone'),
          build('td',null,{},[form.phone]),
        ]),
        build('tr',null,{},[
          build('td','Password'),
          build('td',null,{},[form.password]),
        ]),
        build('tr',null,{},[
          build('td','Address'),
          build('td',null,{},[form.address]),
        ]),
        build('tr',null,{},[
          build('td',''),
          build('td',null,{},[form.button]),
        ]),
        build('tr',null,{},[
          build('td',null,{
            colspan:'2',
          },[form.reged,form.tologin]),
        ]),
      ])
  ]);
  form.body=build('div',null,{
      'class':'form-body',
  },[form.title,form.table]);
  /* onclick event */
  form.tologin.onclick=function(a){
    return _this.buyerContent('login');
  };
  form.button.onclick=function(a){
    var data={
      name:form.name.value,
      phone:form.phone.value,
      password:form.password.value,
      address:form.address.value,
    };
    _this.loader(true);
    _this.request('buyerRegister',function(r){
      _this.loader(false);
      if(typeof r==='object'&&r!==null
        &&r.hasOwnProperty('data')){
        _this.buyerData(r.data);
        _this.buyerUser=r.data;
        _this.buyerInit();
        return _this.success('Register success.');
      }return _this.error(JSON.stringify(r));
    },data);
  };
  /* return the form */
  return form;
};
/* buyer login form content */
this.buyerLoginForm=function(){
  var build=this.buildElement,
  form={
    phone:build('input',null,{
      'class':'form-input',
      type:'number',
      name:'phone',
      placeholder:'Example: 081234567890',
    }),
    password:build('input',null,{
      'class':'form-input',
      type:'password',
      name:'password',
      placeholder:'Password for Login',
    }),
    button:build('button',null,{
      'class':'form-button',
    },[
      build('i',null,{
        'class':'fa fa-sign-in'
      }),
      build('span','Login'),
    ]),
    title:build('div','Login Form',{
      'class':'form-title'
    }),
    toreg:build('a','Register Now'),
    noreg:build('span','I don\'t have account. '),
  },
  _this=this;
  form.table=build('table',null,{
      'class':'form-table',
      cellpadding:'0',
      cellspacing:'5px',
      border:'0',
    },[
      build('tbody',null,{},[
        build('tr',null,{},[
          build('td','Phone'),
          build('td',null,{},[form.phone]),
        ]),
        build('tr',null,{},[
          build('td','Password'),
          build('td',null,{},[form.password]),
        ]),
        build('tr',null,{},[
          build('td',''),
          build('td',null,{},[form.button]),
        ]),
        build('tr',null,{},[
          build('td',null,{
            colspan:'2',
          },[form.noreg,form.toreg]),
        ]),
      ])
  ]);
  form.body=build('div',null,{
      'class':'form-body',
  },[form.title,form.table]);
  /* onclick event */
  form.toreg.onclick=function(a){
    return _this.buyerContent('register');
  };
  form.button.onclick=function(a){
    var data={
      phone:form.phone.value,
      password:form.password.value,
    };
    _this.loader(true);
    _this.request('buyerLogin',function(r){
      _this.loader(false);
      if(typeof r==='object'&&r!==null
        &&r.hasOwnProperty('data')){
        _this.buyerData(r.data);
        _this.buyerUser=r.data;
        _this.buyerInit();
        return _this.success('Logged in.');
      }return _this.error(JSON.stringify(r));
    },data);
  };
  /* return the form */
  return form;
};
/* buyer profile edit form content */
this.buyerProfileEditForm=function(){
  var build=this.buildElement,
  user=this.buyerUser,
  form={
    name:build('input',null,{
      'class':'form-input',
      type:'text',
      name:'name',
      placeholder:'Full Name',
    }),
    phone:build('input',null,{
      'class':'form-input',
      type:'number',
      name:'phone',
      placeholder:'Example: 081234567890',
    }),
    password:build('input',null,{
      'class':'form-input',
      type:'password',
      name:'password',
      placeholder:'Blank means no change',
      autocomplete:'off',
    }),
    address:build('textarea',null,{
      'class':'form-textarea',
      type:'textarea',
      name:'address',
      placeholder:'Recipient Address',
    }),
    button:build('button',null,{
      'class':'form-button',
    },[
      build('i',null,{
        'class':'fa fa-save'
      }),
      build('span','Save'),
    ]),
    title:build('div','Profile Edit Form',{
      'class':'form-title'
    }),
  },
  _this=this;
  form.table=build('table',null,{
      'class':'form-table',
      cellpadding:'0',
      cellspacing:'5px',
      border:'0',
    },[
      build('tbody',null,{},[
        build('tr',null,{},[
          build('td','Name'),
          build('td',null,{},[form.name]),
        ]),
        build('tr',null,{},[
          build('td','Phone'),
          build('td',null,{},[form.phone]),
        ]),
        build('tr',null,{},[
          build('td','Password'),
          build('td',null,{},[form.password]),
        ]),
        build('tr',null,{},[
          build('td','Address'),
          build('td',null,{},[form.address]),
        ]),
        build('tr',null,{},[
          build('td',''),
          build('td',null,{},[form.button]),
        ]),
      ])
  ]);
  form.body=build('div',null,{
      'class':'form-body',
  },[form.title,form.table]);
  form.name.value=user.name;
  form.phone.value=user.phone;
  form.password.value='';
  form.address.value=user.address;
  /* onclick event */
  form.button.onclick=function(a){
    var data={
      aid:user.aid,
      name:form.name.value,
      phone:form.phone.value,
      password:form.password.value,
      address:form.address.value,
    };
    _this.loader(true);
    _this.request('buyerUpdate',function(r){
      _this.loader(false);
      if(typeof r==='object'&&r!==null
        &&r.hasOwnProperty('data')){
        _this.buyerData(r.data);
        _this.buyerUser=r.data;
        _this.buyerInit();
        return _this.success('Updated success.');
      }
      return _this.parseAlert(r);
      //return _this.error(JSON.stringify(r));
    },data);
  };
  /* return the form */
  return form;
};
/* buyer profile content */
this.buyerProfile=function(){
  var build=this.buildElement,
  user=this.buyerUser,
  form={
    button:build('button',null,{
      'class':'form-button',
    },[
      build('i',null,{
        'class':'fa fa-edit'
      }),
      build('span','Edit Profile'),
    ]),
    title:build('div','My Profile',{
      'class':'form-title'
    }),
  },
  _this=this;
  form.table=build('table',null,{
      'class':'form-table',
      cellpadding:'0',
      cellspacing:'5px',
      border:'0',
    },[
      build('tbody',null,{},[
        build('tr',null,{},[
          build('td','Name'),
          build('td',user.name),
        ]),
        build('tr',null,{},[
          build('td','Phone'),
          build('td',user.phone),
        ]),
        build('tr',null,{},[
          build('td','Address'),
          build('td',user.address),
        ]),
        build('tr',null,{},[
          build('td',''),
          build('td',null,{},[form.button]),
        ]),
      ])
  ]);
  form.body=build('div',null,{
      'class':'form-body',
  },[form.title,form.table]);
  /* onclick event */
  form.button.onclick=function(a){
    return _this.buyerContent('edit');
  };
  /* return the form */
  return form;
};
/* buyer placed orders */
this.buyerOrders=function(d){
  var _this=this,
  build=this.buildElement;
  this.loader(true);
  this.request('buyerOrders',function(r){
    _this.loader(false);
    if(typeof r==='object'&&r!==null
      &&r.hasOwnProperty('data')){
      var pl=_this.buyerOrdersContent(r.data),
      el=pl.body;
      if(!el||typeof el.appendChild!=='function'){
        alert(JSON.stringify(el));
        return false;
      }
      _this.clearElement(_this.buyerBody);
      _this.buyerBody.appendChild(el);
      return true;
    }return _this.error(JSON.stringify(r));
  },{
    buyer_id:this.buyerUser.aid,
    buyer_name:this.buyerUser.name,
    buyer_phone:this.buyerUser.phone,
  });
  return false;
};
/* buyer placed orders content */
this.buyerOrdersContent=function(d){
  var _this=this,
  build=this.buildElement,
  warnText='The phone number and address are permanent '
    +'after placing the orders. '
    +'Then seller will text you before deliver the package.',
  dt={},
  currency='',
  grandTotal=0,
  od={
    body:build('div',null,{
      'class':'order-body',
    }),
    title:build('div','My Orders',{
      'class':'order-title',
    }),
    warn:build('div',null,{
      'class':'order-warning',
    },[
      build('i',null,{
        'class':'fa fa-exclamation-circle'
      }),
      build('span',warnText),
    ]),
    detail:build('div',null,{
      'class':'order-detail',
    }),
    paymentMethod:build('div',null,{
      'class':'order-payment-method',
    },[
      build('div'),
    ]),
    grandTotal:build('span',null,{
      'class':'order-grand-total'
    }),
    footerTotal:build('div',null,{
      'class':'order-footer-total',
    },[
      build('span','Total: '),
    ]),
    footer:build('div',null,{
      'class':'order-footer',
    }),
  };
  od.title.appendTo(od.body);
  od.warn.appendTo(od.body);
  od.detail.appendTo(od.body);
  od.paymentMethod.appendTo(od.body);
  od.footer.appendTo(od.body);
  od.grandTotal.appendTo(od.footerTotal);
  od.footerTotal.appendTo(od.footer);
  if(d.length<1){
    this.error('Error: You haven\'t ordered yet.');
    return od;
  }
  for(var i=0;i<d.length;i++){
    var p=d[i],
    bak=p.seller_id+'.'+p.buyer_address;
    if(!dt.hasOwnProperty(bak)){
      dt[bak]={
        sellerName:p.seller_name,
        seller_id:p.seller_id,
        totalItem:0,
        totalPrice:0,
        currency:p.currency,
        detail:[],
        buyer_name:p.buyer_name,
        buyer_phone:p.buyer_phone,
        buyer_address:p.buyer_address,
      };
      currency=p.currency;
    }
    dt[bak].totalItem+=parseInt(p.item_quantity,10);
    dt[bak].totalPrice+=
      parseInt(p.item_price,10)*parseInt(p.item_quantity,10);
    dt[bak].detail.push(p);
    grandTotal+=
      parseInt(p.item_price,10)*parseInt(p.item_quantity,10);
  }
  od.grandTotal.innerText=currency
    +this.parseDigit(parseInt(grandTotal));
  
  for(var u in dt){
    var p=dt[u],
    elp=build('div',null,{
      'class':'order-detail-products',
    }),
    elt=build('div','Subtotal: '
      +p.currency+this.parseDigit(p.totalPrice)
      +' ('+p.totalItem+' item'
        +(p.totalItem>1?'s':'')+')'
      +'',{
      'class':'order-detail-subtotal'
    }),
    /**/
    address=build('div',null,{
      'class':'order-address'
    },[
      build('i',null,{
        'class':'fa fa-map-marker'
      }),
      build('div',null,{
        'class':'order-address-name'
      },[
        build('strong',p.buyer_name),
        build('span',p.buyer_phone),
      ]),
      build('div',p.buyer_address,{
        'class':'order-address-address'
      }),
    ]),
    //*/
    el=build('div',null,{
      'class':'order-detail-each',
    },[
      address,
      build('div',p.sellerName,{
        'class':'order-detail-store',
        'data-seller_id':p.seller_id,
      }),
      elp,
      elt,
    ]);
    for(var i of p.detail){
      var pim=build('img',null,{
        src:i.item_picture,
      }),
      pid=build('div',null,{
        'class':'order-product-data',
      },[
        build('div',i.item_name),
        build('div',i.currency+this.parseDigit(i.item_price)),
        build('div','Qty: '+i.item_quantity+' item'+(i.item_quantity>1?'s':'')),
      ]),
      pi=build('div',null,{
        'class':'order-product-each'
      },[
        build('table',null,{
          'class':'order-product-table',
          cellpadding:'0px',
          cellspacing:'5px',
          border:'0px',
        },[
          build('tbody',null,{},[
            build('tr',null,{},[
              build('td',null,{},[pim]),
              build('td',null,{},[pid]),
            ])
          ]),
        ]),
      ]);
      pi.appendTo(elp);
    }
    el.appendTo(od.detail);
  }
  return od;
};
/* buyer header */
this.buyerHeader=function(){
  var _this=this,
  name=this.buyerUser.name||'Not Login',
  build=this.buildElement,
  uname=build('div',name,{
    'class':'buyer-user-name',
  }),
  cart=build('button','Cart'),
  orders=build('button','Orders'),
  profile=build('button','Profile'),
  logout=build('button','Logout'),
  login=build('button','Login'),
  table=build('table',null,{
    'class':'buyer-header-table',
    cellpadding:'0px',
    cellspacing:'0px',
    border:'0px',
  },[
    build('tbody',null,{},[
      build('tr',null,{},[
        build('td',null,{},[uname]),
        build('td',null,{},[cart]),
        build('td',null,{},[this.buyerUser.aid?orders:this.textNode('')]),
        build('td',null,{},[this.buyerUser.aid?profile:this.textNode('')]),
        build('td',null,{},[this.buyerUser.aid?logout:login]),
      ]),
    ]),
  ]),
  head=build('div',null,{
    'class':'buyer-header',
  },[table]);
  cart.onclick=function(){
    return _this.buyerContent('cart');
  };
  orders.onclick=function(){
    return _this.buyerContent('orders');
  };
  profile.onclick=function(){
    return _this.buyerContent('profile');
  };
  logout.onclick=function(){
    return _this.confirm('Logout',
      'Do you want to logout?',function(yes){
      if(!yes){return false;}
      _this.buyerData(false);
      _this.buyerUser=null;
      return _this.buyerInit();
    });
  };
  login.onclick=function(){
    return _this.buyerContent('login');
  };
  return head;
};
/* buyer data */
this.buyerData=function(v){
  var k='website-buyer',
  s=null,r=false,d=null;
  if(v===false){
    localStorage.removeItem(k);
    return true;
  }else if(v){
    s=JSON.stringify(v);
    localStorage.setItem(k,s);
    return true;
  }
  d=localStorage.getItem(k);
  try{r=JSON.parse(d);}catch(e){}
  return r?r:[];
};
/* cart content */
this.cartContent=function(){
  var d=this.cartData(),
  len=d.length,
  ts=len>1?'s':'',
  tr=null,
  _this=this,
  ls={},
  build=this.buildElement,
  r={
    body:build('div',null,{
      'class':'cart-body',
    },[
      build('div','My Cart ('+len+' item'+ts+')',{
        'class':'cart-title'
      }),
    ]),
    tbody:build('tbody'),
    table:build('table',null,{
      'class':'cart-table',
      cellpadding:'0',
      cellspacing:'5px',
      border:'0',
    },[]),
    rtbody:build('tbody'),
    rtable:build('table',null,{
      'class':'cart-table-bottom',
      cellpadding:'0',
      cellspacing:'5px',
      border:'0',
    },[]),
    list:{},
    selectedItems:{},
    totalPrice:0,
    totalItem:0,
    currency:'',
  };
  r.tbody.appendTo(r.table);
  r.table.appendTo(r.body);
  for(var i of d){
    if(!r.list.hasOwnProperty(i.seller_id)){
      r.list[i.seller_id]={};
    }
    if(i.selected||r.selectedItems.hasOwnProperty(i.aid)){
      if(r.selectedItems.hasOwnProperty(i.aid)){
        r.selectedItems[i.aid].quantity+=1;
      }else{
        r.selectedItems[i.aid]={
          quantity:1,
          price:parseInt(i.price,10),
          name:i.name,
          picture:i.pictures[0],
          seller_id:i.seller_id,
          product:i,
        };
        r.currency=i.currency;
      }
      r.totalItem+=1;
      r.totalPrice+=parseInt(i.price,10);
    }
    if(r.list[i.seller_id].hasOwnProperty(i.aid)){
      var qty=r.list[i.seller_id][i.aid].itemCount+1;
      r.list[i.seller_id][i.aid].itemCount=qty;
      r.list[i.seller_id][i.aid].tr.dataset.itemCount=qty;
      r.list[i.seller_id][i.aid].quantity
        .innerText='Qty: '+qty+' item'+(qty>1?'s':'');
      continue;
    }
    r.list[i.seller_id][i.aid]=this.cartRow(i);
    r.list[i.seller_id][i.aid].tr.appendTo(r.table);
  }
  var checkout=build('button',null,{
    'class':'cart-checkout',
  },[
    build('i',null,{
      'class':'fa fa-shopping-cart'
    }),
    build('span','Check Out ('+r.totalItem+')')
  ]),
  totalPrice=build('div',r.currency+this.parseDigit(r.totalPrice),{
    'class':'cart-total-price',
  }),
  isCheckAll=len==r.totalItem&&len>0;
  checkall=build('button',null,{
    'class':'cart-checkall',
  },[
    build('i',null,{
      'class':'fa fa-'+(isCheckAll?'check-':'')+'circle',
    }),
  ]);
  tr=build('tr',null,{},[
    build('td',null,{},[
      checkall,
      this.textNode('All'),
    ]),
    build('td',null,{},[
      this.textNode('Total: '),
      totalPrice,
    ]),
    build('td',null,{},[checkout]),
  ]);
  tr.appendTo(r.rtbody);
  r.rtbody.appendTo(r.rtable);
  r.rtable.appendTo(r.body);
  checkout.selectedItems=r.selectedItems;
  checkout.totalPrice=r.totalPrice;
  checkout.totalItem=r.totalItem;
  checkout.onclick=function(){
    if(this.totalItem<1){
      return _this.error('Error: No selected item.');
    }
    var _checkout=this;
    return _this.confirm('Check Out',
    'Do you wish to checkout?',function(yes){
      if(!yes){return false;}
      
      return _this.cartCheckout(_checkout);
    });
  };
  checkall.isCheckAll=isCheckAll;
  checkall.onclick=function(e){
    var cs=_this.cartData();
    if(!cs){return false;}
    for(var u in cs){
      cs[u].selected=this.isCheckAll?false:true;
    }
    _this.cartData(cs);
    _this.buyerContent('cart');
  };
  return r;
};
/* cart row for table */
this.cartRow=function(p){
  var row={
    data:p,
    itemCount:1,
    seller_id:p.seller_id,
  },
  _this=this,
  build=this.buildElement;
  row.check=build('button',null,{
    'class':'cart-check',
  },[
    build('i',null,{
      'class':'fa fa-'+(p.selected?'check-':'')+'circle',
    }),
  ]);
  row.image=build('img',null,{
    src:p.pictures[0],
  });
  row.name=build('div',p.name,{
    'class':'cart-p-name',
  });
  row.price=build('div',p.currency+this.parseDigit(p.price),{
    'class':'cart-p-price',
    'data-price':(p.price).toString(),
  });
  row.quantity=build('div','Qty: '+row.itemCount
      +' item'+(row.itemCount>1?'s':''),{
    'class':'cart-p-quantity',
  });
  row.seller=build('div',null,{
    'class':'cart-p-seller',
  },[
    build('span',p.sellerName),
  ]);
  row.remove=build('button',null,{
    'class':'cart-remove',
  },[
    build('i',null,{
      'class':'fa fa-trash'
    }),
  ]);
  row.tr=build('tr',null,{},[
    build('td',null,{},[row.check]),
    build('td',null,{},[row.image]),
    build('td',null,{},[
      row.name,
      row.price,
      row.quantity,
      row.seller,
    ]),
    build('td',null,{},[row.remove]),
  ]);
  row.tr.dataset.itemCount=row.itemCount;
  row.check.dataset.aid=p.aid;
  row.check.onclick=function(){
    var cs=_this.cartSelect(this.dataset.aid);
    if(!cs){return false;}
    cs[0].selected=cs[0].selected?false:true;
    _this.cartUpdate(this.dataset.aid,cs[0]);
    _this.buyerContent('cart');
  };
  row.remove.product=p;
  row.remove.onclick=function(){
    this.quantity=row.itemCount;
    return _this.removefromcart(this);
  };
  row.image.dataset.url=this.PRODUCT_URL+'?id='+p.url;
  row.image.dataset.data=JSON.stringify(p);
  row.image.onclick=function(){
      var data=false,
      plr=_productTablePublic.preloader();
      if(this.dataset.url==plr.idn){
        return false;
      }
      try{
        data=JSON.parse(this.dataset.data);
      }catch(e){
        data=false;
      }
      if(data){
        window.history.pushState({
          type:'single',
          data:data,
        },data.name,this.dataset.url);
        return _productTablePublic.singleStart();
      }return window.location.assign(this.dataset.url);
  };
  return row;
};
/* cart checkout */
this.cartCheckout=function(d){
  if(!this.buyerUser||!this.buyerUser.aid){
    return this.buyerContent('register');
  }
  return this.buyerContent('checkout',d);
};
/* order checkout content */
this.orderCheckout=function(d){
  var _this=this,
  build=this.buildElement,
  warnText='To avoid failed delivery, please ensure the ADDRESS '
    +'you have provided is CORRECT. Insert your home address '
    +'for estimated delivery on Saturday, Sunday or '
    +'National Holidays.',
  dt={},
  data={
    buyer_id:this.buyerUser.aid,
    buyer_name:this.buyerUser.name,
    buyer_phone:this.buyerUser.phone,
    buyer_address:this.buyerUser.address,
    total:d.totalPrice,
    orders:{},
    currency:'',
  },
  currency='',
  od={
    body:build('div',null,{
      'class':'order-body',
    }),
    title:build('div','Check Out',{
      'class':'order-title',
    }),
    warn:build('div',null,{
      'class':'order-warning',
    },[
      build('i',null,{
        'class':'fa fa-exclamation-circle'
      }),
      build('span',warnText),
    ]),
    address:build('div',null,{
      'class':'order-address'
    },[
      build('i',null,{
        'class':'fa fa-map-marker'
      }),
      build('div',null,{
        'class':'order-address-name'
      },[
        build('strong',this.buyerUser.name),
        build('span',this.buyerUser.phone),
      ]),
      build('div',this.buyerUser.address,{
        'class':'order-address-address'
      }),
    ]),
    detail:build('div',null,{
      'class':'order-detail',
    }),
    paymentMethod:build('div',null,{
      'class':'order-payment-method',
    },[
      build('div'),
    ]),
    button:build('button',null,{
      'class':'order-button'
    },[
      build('i',null,{
        'class':'fa fa-send'
      }),
      this.textNode('Place Order'),
    ]),
    grandTotal:build('span',null,{
      'class':'order-grand-total'
    }),
    footerTotal:build('div',null,{
      'class':'order-footer-total',
    },[
      build('span','Total: '),
    ]),
    footer:build('div',null,{
      'class':'order-footer',
    }),
  };
  od.title.appendTo(od.body);
  od.warn.appendTo(od.body);
  od.address.appendTo(od.body);
  od.detail.appendTo(od.body);
  od.paymentMethod.appendTo(od.body);
  od.footer.appendTo(od.body);
  od.grandTotal.appendTo(od.footerTotal);
  od.footerTotal.appendTo(od.footer);
  od.button.appendTo(od.footer);
  
  for(var i in d.selectedItems){
    var p=d.selectedItems[i];
    if(!dt.hasOwnProperty(p.seller_id)){
      dt[p.seller_id]={
        sellerName:p.product.sellerName,
        seller_id:p.seller_id,
        totalItem:0,
        totalPrice:0,
        currency:p.product.currency,
        detail:[],
      };
      currency=p.product.currency;
    }
    if(!data.orders.hasOwnProperty(p.seller_id)){
      data.orders[p.seller_id]={
        seller_id:p.seller_id,
        seller_name:p.product.sellerName,
        totalItem:0,
        totalPrice:0,
        currency:p.product.currency,
        items:[],
        products:[],
      };
      data.currency=p.product.currency;
    }
    dt[p.seller_id].totalItem+=parseInt(p.quantity,10);
    dt[p.seller_id].totalPrice+=
      parseInt(p.product.price,10)*parseInt(p.quantity,10);
    dt[p.seller_id].detail.push(p);
    
    data.orders[p.seller_id].totalItem+=parseInt(p.quantity,10);
    data.orders[p.seller_id].totalPrice+=
      parseInt(p.product.price,10)*parseInt(p.quantity,10);
    data.orders[p.seller_id].items.push({
      aid:p.product.aid,
      name:p.name,
      price:parseInt(p.price,10),
      quantity:parseInt(p.quantity,10),
    });
    //data.orders[p.seller_id].products.push(p);
  }
  od.grandTotal.innerText=currency+this.parseDigit(parseInt(d.totalPrice));
  
  for(var u in dt){
    var p=dt[u],
    elp=build('div',null,{
      'class':'order-detail-products',
    }),
    elt=build('div','Subtotal: '
      +p.currency+this.parseDigit(p.totalPrice)
      +' ('+p.totalItem+' item'
        +(p.totalItem>1?'s':'')+')'
      +'',{
      'class':'order-detail-subtotal'
    }),
    el=build('div',null,{
      'class':'order-detail-each',
    },[
      build('div',p.sellerName,{
        'class':'order-detail-store',
        'data-seller_id':p.seller_id,
      }),
      elp,
      elt,
    ]);
    for(var i of p.detail){
      var pim=build('img',null,{
        src:i.picture,
      }),
      pid=build('div',null,{
        'class':'order-product-data',
      },[
        build('div',i.name),
        build('div',p.currency+this.parseDigit(i.price)),
        build('div','Qty: '+i.quantity+' item'+(i.quantity>1?'s':'')),
      ]),
      pi=build('div',null,{
        'class':'order-product-each'
      },[
        build('table',null,{
          'class':'order-product-table',
          cellpadding:'0px',
          cellspacing:'5px',
          border:'0px',
        },[
          build('tbody',null,{},[
            build('tr',null,{},[
              build('td',null,{},[pim]),
              build('td',null,{},[pid]),
            ])
          ]),
        ]),
      ]);
      pi.appendTo(elp);
    }
    el.appendTo(od.detail);
  }
  
  od.button.data=data;
  od.button.onclick=function(e){
    var orderData=this.data;
    _this.confirm('Send the Order',
      'Are you sure want to place the order?',function(yes){
      if(!yes){return false;}
      _this.loader(true);
      _this.request('orderPlace',function(r){
        _this.loader(false);
        if(typeof r==='object'&&r!==null
          &&r.hasOwnProperty('data')){
          for(var i in r.data){
            _this.cartDelete(r.data[i].item_id);
          }return _this.buyerOrders();
        }return _this.error(JSON.stringify(r));
      },orderData);
    });
  };
  return od;
};
/* */


/* addtocart */
this.addtocart=function(b){
  var p=b.product,
  d=this.cartSelect(p.aid),
  s=document.getElementById('pts-store-'+p.seller_id),
  _this=this;
  if(d.length>=p.stock){
    return this.error('Error: Out of stock.');
  }
  if(s){p.sellerName=s.dataset.name;}
  p.selected=false;
  this.confirm('Add to Cart',
    'Add this item to your cart?',
    function(r){
      if(!r){return false;}
      _this.cartInsert(p);
      _this.buyerContent('cart');
  });
};
/* removefromcart */
this.removefromcart=function(b){
  cb=typeof cb==='function'?cb:function(){};
  var p=b.product,
  _this=this;
  this.confirm('Remove from Cart',
    'Remove this item from your cart?',
    function(r){
      if(!r){return false;}
      _this.cartDelete(p.aid);
      _this.buyerContent('cart');
  });
};


/* footer */
this.footerContent=function(){
  let home_footer=document.createElement('div');
  home_footer.classList.add('home-footer');
  home_footer.innerHTML=this.HTML_DATA.home_footer;
  return home_footer;
};
/* home slider */
this.homeSlider=function(r){
  let leftPictures=[],
  rightPictures=[],
  leftData={},
  rightData={},
  left=document.getElementById('home-slider-left'),
  right=document.getElementById('home-slider-right'),
  tmp=0;
  for(let i in r){
    let p=this.convertProduct(r[i]);
    if(p.categories.indexOf(left.dataset.id)>=0){
      p.catid=left.dataset.id;
      p.section='left';
      leftPictures.push(p.pictures[0]);
      leftData[p.pictures[0]]=p;
    }
    if(p.categories.indexOf(right.dataset.id)>=0){
      p.catid=right.dataset.id;
      p.section='right';
      rightPictures.push(p.pictures[0]);
      rightData[p.pictures[0]]=p;
    }
  }
  leftPictures.reverse();
  rightPictures.reverse();
  let sliderLeft=this.slider(leftPictures,'slider-left'),
  sliderRight=this.slider(rightPictures,'slider-right');
  sliderLeft.element.id='home-slider-left-data';
  sliderRight.element.id='home-slider-right-data';
  left.appendChild(sliderLeft.element);
  right.appendChild(sliderRight.element);
  sliderLeft.init();
  setTimeout(()=>{
    sliderRight.init();
  },200);
  let imgLink=function(path,p,img){
      if(!img.parentElement.parentElement
        .classList.contains('slider')){
        return;
      }
      let px=document.createElement('div'),
      id=path+'-'+p.section,
      opx=document.getElementById(id);
      px.id=id;
      px.innerText=p.name;
      px.dataset.data=JSON.stringify(p);
      px.dataset.url=_productTablePublic.PRODUCT_URL+'?id='+p.url;
      px.classList.add('home-slider-data-name');
      px.onclick=function(){
        let data=false;
        try{
          data=JSON.parse(this.dataset.data);
        }catch(e){
          data=false;
        }
        if(data){
          window.history.pushState({
            type:'single',
            data:data,
          },data.name,this.dataset.url);
          return _productTablePublic.singleStart();
        }return window.location.assign(this.dataset.url);
      };
      img.parentElement.appendChild(px);
  };
  setTimeout(()=>{
    let sl=document.getElementsByClassName('slider-left'),
    sr=document.getElementsByClassName('slider-right'),
    slx=sl[0].children,
    srx=sr[0].children;
    for(let i=0;i<slx.length;i++){
      if(slx[i].children[0].tagName=='IMG'){
        let ipath=slx[i].children[0].src,
        rpath=ipath.replace(/^https?:\/\/[^\/]+\//,''),
        rdata=leftData[rpath];
        imgLink(rpath,rdata,slx[i].children[0]);
      }
    }
    for(let i=0;i<srx.length;i++){
      if(srx[i].children[0].tagName=='IMG'){
        let ipath=srx[i].children[0].src,
        rpath=ipath.replace(/^https?:\/\/[^\/]+\//,''),
        rdata=rightData[rpath];
        imgLink(rpath,rdata,srx[i].children[0]);
      }
    }
  },500);
  return true;
};

/* bulk start */
this.bulkStart=function(tag,store){
  this.content.innerHTML='';
  var _this=this,
  data=tag?{tag:tag}:store?{store:store}:{},
  method=tag?'publicCategories':'publicProducts';
  method=store?'publicStores':method;
  this.pageTitle(this.WEBSITE_TITLE);
  if(history.state&&history.state.hasOwnProperty('data')
    &&history.state.type=='category'){
    data.tag=history.state.data.id;
    this.pageTitle(history.state.data.name);
  }
  if(history.state&&history.state.hasOwnProperty('data')
    &&history.state.type=='store'){
    data.store=history.state.data.id;
    this.pageTitle(history.state.data.name);
  }
  this.buyerContentClose();
  this.bulkData();
  this.request(method,function(r){
    if(typeof r==='object'&&r!==null){
      return _this.bulkData(r);
    }return _this.error(JSON.stringify(r));
  },data);
};
/* bulk data */
this.bulkData=function(r){
  /* set default */
  var def={
    length:Math.floor(this.content.offsetWidth/200)*3,
    data:[]
  },
  _this=this;
  for(var i=0;i<def.length;i++){
    def.data.push(this.preloadColumn());
  }
  /* check input data array */
  r=typeof r==='object'&&r!==null&&Array.isArray(r.data)?r:def;
  this.resizeData=r;
  /* convert content */
  var content=this.bulkContent(r.data);
  /* add to content */
  this.clearElement(this.content);
  this.content.appendChild(content.cover);
  this.content.appendChild(content.pad);
  /* store tag */
  if(r.hasOwnProperty('seller')){
    var store=this.storeTag(r.seller.seller_id,r.seller);
    store.classList.add('pts-store-fixed');
    content.cover.style.marginTop='72px';
    this.content.insertBefore(store,content.cover);
    this.content.scrollTo({
        top:0,
        left:0,
        behavior:'smooth',
    });
  }
  if(r.hasOwnProperty('category')
    &&r.category.hasOwnProperty('aid')){
    var cat=this.headerTag(r.category.aid,r.category);
    cat.classList.add('pts-store-fixed');
    content.cover.style.marginTop='72px';
    this.content.insertBefore(cat,content.cover);
    this.content.scrollTo({
        top:0,
        left:0,
        behavior:'smooth',
    });
  }
  if(r.hasOwnProperty('type')&&r.type=='products'){
    let home_header=document.createElement('div');
    home_header.classList.add('home-header');
    home_header.innerHTML=this.HTML_DATA.home_header;
    this.content.insertBefore(home_header,content.cover);
    this.categoryClickInit();
    this.homeSlider(r.data);
  }
  /* footer content */
  let home_footer=this.footerContent();
  this.content.appendChild(home_footer);
  /* bulk rate */
  var prt=document.querySelectorAll('.prodrate');
  if(prt&&prt.length){
    setTimeout(e=>{
      return _this.bulkRate(prt);
    },500);
  }
};
/* bulk content */
this.bulkContent=function(rdata){
  /* prepare data */
  var data=Array.isArray(rdata)?rdata:[],
  i=data.length,
  build=this.buildElement,
  clong=Math.floor(window.innerWidth/200),
  clen=clong>2?Math.min(clong,5):2,
  tds=[],
  tr=build('tr'),
  tbody=build('tbody',null,{},[tr]),
  table=build('table',null,{
    'class':'prodtable',
    border:'0',
    cellpadding:'0',
    cellspacing:'0',
  },[tbody]),
  pad=build('div',null,{
    'class':'prodpad'
  }),
  cover=build('div',null,{
    'class':'prodcover'
  },[table]),
  cpx=[],
  state=[],
  plr=this.preloader(),
  temp=false;
  /* column */
  for(var u=0;u<clen;u++){
    var td=build('td',null,{'class':'prodtd'+clong});
    tds.push(td);
    td.appendTo(tr);
  }
  /* parse the loop */
  while(i--){
    var p=this.convertProduct(data[i]),
    px=this.bulkEach(p);
    px.image.classList.add('prodimage'+clong);
    //cpx.push(px);
    px.each.appendTo(tds[i%clen]);
    if(px.each.dataset.url!=plr.idn){
      state.push({
        url:px.each.dataset.url,
        data:p,
        title:p.name,
      });
    }
  }
  /* return cover and pad */
  return {
    cover:cover,
    pad:pad,
    cpx:cpx,
    state:state,
  };
};
/* bulk each */
this.bulkEach=function(p){
  var xp=p,
    build=this.buildElement,
    px={
      each:build('div',null,{'class':'prodeach'}),
      image:build('div',null,{'class':'prodimage'}),
      title:build('div',null,{'class':'prodtitle'}),
      name:build('span',null,{'class':'prodname'}),
      price:build('div',null,{'class':'prodprice'}),
      aprice:build('span',null,{'class':'prodafter'}),
      currency:build('span',null,{'class':'prodcurrency'}),
      bprice:build('span',null,{'class':'prodbefore'}),
      discount:build('span',null,{'class':'proddiscount'}),
      addon:build('div',null,{'class':'prodaddon'}),
      rating:build('div',null,{'class':'prodrating'}),
      star:build('span',null,{'class':'prodstar'}),
      rate:build('span',null,{
        'class':'prodrate',
        'id':'prodrate-'+p.aid,
        'data-pid':p.aid+'',
      }),
      sold:build('span',null,{'class':'prodsold'}),
      istar:build('i',null,{'class':'fa fa-star prodorange'}),
      menu:build('div',null,{'class':'prodmenu'}),
      imenu:build('i',null,{'class':'fa fa-ellipsis-v'}),
      location:build('div',null,{'class':'prodlocation'}),
      option:build('div',null,{'class':'prodoption'}),
    },
    img=new Image;
    /* image */
    img.src=p.pictures[0];
    px.image.appendChild(img);
    px.image.appendTo(px.each);
    /* name */
    px.name.innerText=p.name;
    px.name.appendTo(px.title);
    px.title.appendTo(px.each);
    /* price */
    px.currency.innerText=p.currency;
    px.aprice.innerText=p.prices;
    px.currency.appendTo(px.price);
    px.aprice.appendTo(px.price);
    if(p.bprice&&parseInt(p.bprice,10)>0){
      px.bprice.innerText=p.currency+p.bprices;
      px.discount.innerText=this.parseDiscount(p.price,p.bprice);
      px.bprice.appendTo(px.price);
      px.discount.appendTo(px.price);
    }
    px.price.appendTo(px.each);
    /* addon */
    px.addon.appendTo(px.each);
    /* rating */
    px.imenu.appendTo(px.menu);
    px.istar.appendTo(px.star);
    px.rate.innerText='...';
    px.sold.innerText=p.sold+' sold';
    px.star.appendTo(px.rating);
    px.rate.appendTo(px.rating);
    px.sold.appendTo(px.rating);
    px.menu.appendTo(px.rating);
    px.rating.appendTo(px.each);
    /* add to column */
    px.each.dataset.url=this.PRODUCT_URL+'?id='+p.url;
    px.each.dataset.data=JSON.stringify(p);
    px.each.onclick=function(){
      var data=false,
      plr=_productTablePublic.preloader();
      if(this.dataset.url==plr.idn){
        return false;
      }
      try{
        data=JSON.parse(this.dataset.data);
      }catch(e){
        data=false;
      }
      if(data){
        window.history.pushState({
          type:'single',
          data:data,
        },data.name,this.dataset.url);
        return _productTablePublic.singleStart();
      }return window.location.assign(this.dataset.url);
    };
  /* preload */
  var prl=['name','price','sold'],
  preload=this.preloader(),
  wwidth=Math.min(window.innerWidth,1000),
  clong=Math.floor(wwidth/200),
  clen=clong>2?Math.min(clong,5):2,
  cwidth=(wwidth/clen)-50;
  if(p.name==preload.idn){
    for(var i in prl){
      preload=this.preloader().override(px[prl[i]]);
      preload.outer.style.width=cwidth+'px';
    }
    preload=this.preloader().override(px.image);
    cwidth=(wwidth/clen)-15;
    preload.outer.style.width=cwidth+'px';
    preload.outer.style.height=cwidth+'px';
    px.each.removeChild(px.rating);
    px.onclick=null;
  }
  return px;
};
/* bulk rate */
this.bulkRate=function(prt,i){
  i=i?i:0;
  if(!prt||!prt[i]){
    return false;
  }
  var _this=this;
  this.request('publicRate',function(r){
    var prate=document.getElementById('prodrate-'+r.product_id);
    if(!prate){return;}
    prate.innerText=''+r.rate;
    i++;
    return _this.bulkRate(prt,i);
  },{pid:prt[i].dataset.pid});
};

/* single start */
this.singleStart=function(){
  var content=null,
  _this=this;
  this.buyerContentClose();
  this.content.innerHTML='';
  if(history.state&&history.state.hasOwnProperty('data')){
    content=this.singleContent(history.state.data);
  }else if(this.PRODUCT_DATA.hasOwnProperty('description')){
    content=this.singleContent(this.PRODUCT_DATA);
  }
  this.content.scrollTo({
        top:0,
        left:0,
        behavior:'smooth',
  });
  if(content){
    content.body.appendTo(this.content);
    _this.pageTitle(content.title);
    /* footer content */
    let home_footer=this.footerContent();
    this.content.appendChild(home_footer);
    return content.slider.init();
  }
  content=this.singleContent();
  content.body.appendTo(this.content);
  /* footer content */
  let home_footer=this.footerContent();
  this.content.appendChild(home_footer);
  /* request */
  this.request('publicProduct',function(r){
    if(typeof r==='object'&&r!==null
      &&r.hasOwnProperty('data')){
      content=_this.singleContent(r.data);
      _this.pageTitle(content.title);
      _this.content.innerHTML='';
      content.body.appendTo(_this.content);
      let home_footer=_this.footerContent();
      _this.content.appendChild(home_footer);
      return content.slider.init();
    }return _this.error(JSON.stringify(r));
  },{id:this.query.id});
};
/* single content */
this.singleContent=function(r){
  r=typeof r==='object'&&r!==null
    ?r:this.preloadColumn();
  /* preparing product */
  var prod=r.hasOwnProperty('name')?r:this.convertProduct(r),
  pts=this.singleBasic(),
  _this=this,
  slider=this.slider(prod.pictures),
  priceText=document.createTextNode(prod.prices),
  catdata=this.categoryData(),
  temp=false;
  /* pictures */
  pts.pictures.appendChild(slider.element);
  pts.slider=slider;
  /* title name */
  pts.name.innerText=prod.name;
  /* price and discount */
  pts.currency.innerText=prod.currency;
  pts.aprice.appendChild(priceText);
  if(prod.bprice&&parseInt(prod.bprice,10)>0){
    pts.bprice.innerText=prod.currency+prod.bprices;
    pts.bprice.appendTo(pts.price);
    pts.discount.innerText=this.parseDiscount(prod.price,prod.bprice);
    pts.discount.appendTo(pts.price);
  }
  /* button */
  pts.button=this.buildElement('button',null,{
    'class':'pts-button',
  },[
    this.buildElement('i',null,{
      'class':'fa fa-cart-plus'
    }),
    this.buildElement('span','Add to Cart'),
  ]);
  pts.button.product=prod;
  pts.button.onclick=function(e){
    return _this.addtocart(this);
  };
  pts.button.appendTo(pts.price);
  if(prod.stock<1){
    pts.button.disabled=true;
    pts.button.classList.add('pts-button-out');
  }
  /* rating table */
  pts.ratingTable.appendTo(pts.rating);
  pts.ratingBody.appendTo(pts.ratingTable);
  pts.ratingRow.appendTo(pts.ratingBody);
  pts.rates.appendTo(pts.ratingRow);
  pts.sold.appendTo(pts.ratingRow);
  pts.stock.appendTo(pts.ratingRow);
  pts.sold.innerText=prod.sold+' sold';
  pts.stock.innerText=prod.stock+' item'
    +(prod.stock>1?'s':'')+'\nin stock';
  pts.stock.id='pts-stock-'+prod.aid;
  /* rating */
  this.request('publicRating',function(r){
    var rating=_this.singleRate(r.point,r.length);
    rating.element.appendTo(pts.rates);
    rating.element.dataset.aid=prod.aid;
    rating.element.onclick=function(e){
      return _this.rateDialog(this.dataset.aid);
    };
  },{pid:prod.aid});
  /* description */
  pts.description.innerHTML=prod.description.replace(/\n/g,'<br />');
  /* spec */
  pts.specTitle.innerText='Specification';
  pts.specTitle.appendTo(pts.spec);
  /* spec table */
  pts.tbody=this.buildElement('tbody',null,{
    'class':'pts-tbody'
  });
  pts.table=this.buildElement('table',null,{
    'class':'pts-table',
    'cellpadding':'0',
    'cellspacing':'5',
    'border':'0'
  },[pts.tbody]);
  pts.table.appendTo(pts.spec);
  for(var i in prod.spec){
    this.specRow(i,prod.spec[i]).appendTo(pts.tbody);
  }
  /* store name */
  pts.store=this.storeTag(prod.seller_id);
  pts.store.appendTo(pts.body);
  /* category */
  for(var i=0;i<prod.categories.length;i++){
    var pcid=prod.categories[i];
    if(catdata.hasOwnProperty(pcid)){
      var cat=this.categoryTag(pcid,catdata[pcid]);
      cat.appendTo(pts.category);
      continue;
    }
    this.request('publicCategory',function(r){
      if(typeof r==='object'&&r!==null
        &&r.hasOwnProperty('data')){
          var cat=_this.categoryTag(r.data.aid,r.data.name);
          cat.appendTo(pts.category);
          catdata[r.data.aid]=r.data.name;
          _this.categoryData(catdata);
        }
    },{id:prod.categories[i]});
  }
  /* preload stuff */
  pts.preload=false;
  var preload=this.preloader();
  if(prod.name==preload.idn){
    pts.preload=true;
    preload=this.preloader().override(pts.name);
    preload.outer.style.width='20%';
    preload=this.preloader().override(pts.price);
    preload.outer.style.width='60%';
    preload=this.preloader().override(pts.rating);
    preload.outer.style.width='40%';
    preload=this.preloader().override(pts.description);
    preload.outer.style.width='80%';
    preload.outer.style.height='40px';
    preload=this.preloader().override(pts.pictures);
    preload.outer.style.height='640px';
    pts.body.removeChild(pts.spec);
  }
  /* return the pts */
  pts.title=prod.name;
  return pts;
};
/* single basic */
this.singleBasic=function(){
  var px={
    body:this.singleElement('pts-body'),
    pictures:this.singleElement('pts-pictures'),
    title:this.singleElement('pts-title'),
    name:this.singleElement('pts-name','span'),
    badge:this.singleElement('pts-badge'),
    price:this.singleElement('pts-price'),
    currency:this.singleElement('pts-currency','span'),
    aprice:this.singleElement('pts-aprice','span'),
    bprice:this.singleElement('pts-bprice','span'),
    discount:this.singleElement('pts-discount','span'),
    addon:this.singleElement('pts-addon'),
    social:this.singleElement('pts-social'),
    rating:this.singleElement('pts-rating'),
    ratingTable:this.singleElement('pts-rating-table','table'),
    ratingBody:this.singleElement('pts-rating-tbody','tbody'),
    ratingRow:this.singleElement('pts-rating-row','tr'),
    star:this.singleElement('pts-star','span'),
    rate:this.singleElement('pts-rate','span'),
    rates:this.singleElement('pts-rates','td'),
    sold:this.singleElement('pts-sold','td'),
    stock:this.singleElement('pts-stock','td'),
    menu:this.singleElement('pts-menu'),
    location:this.singleElement('pts-location'),
    option:this.singleElement('pts-option'),
    description:this.singleElement('pts-description'),
    specTitle:this.singleElement('pts-spec-title'),
    spec:this.singleElement('pts-spec'),
    category:this.singleElement('pts-category'),
    store:this.singleElement('pts-store'),
  };
  px.body.appendChild(px.pictures);
  px.body.appendChild(px.price);
  px.body.appendChild(px.title);
  px.body.appendChild(px.addon);
  px.body.appendChild(px.rating);
  px.body.appendChild(px.social);
  px.body.appendChild(px.description);
  px.body.appendChild(px.spec);
  px.body.appendChild(px.category);
  px.price.appendChild(px.currency);
  px.price.appendChild(px.aprice);
  px.title.appendChild(px.name);
  return px;
};
/* single element */
this.singleElement=function(cn,tn){
  return this.buildElement(tn||'div',null,{
    'class':cn||''
  });
};
/* single star */
this.singleStar=function(x=0){
  var s=['-o','-half-o',''],
  sx=s.hasOwnProperty(x)?s[x]:s[0];
  return this.buildElement('i',null,{
    'class':'fa fa-star'+sx+' pts-star-orange',
  });
};
/* calculate rating */
this.singleRate=function(point,length,max=5){
  var rating={
    length:length,
    point:point,
    total:0,
    stars:0,
    value:0,
    per:0,
    max:max,
    tags:[],
    element:null,
    ws:length>1?'s':'',
  };
  rating.total=rating.length*rating.max;
  rating.per=rating.total?rating.point/rating.total:0;
  rating.value=Math.floor(rating.per*(rating.max*10));
  rating.stars=rating.value/10;
  for(var i=1;i<=rating.max;i++){
    var rate=0;
    if(rating.stars>=i){
      rate=2;
    }else if(i-rating.stars<0.7){
      rate=1;
    }
    rating.tags.push(this.singleStar(rate));
  }
  rating.element=this.buildElement(
    'span',rating.stars+'/'+rating.max+' ',{
      'class':'pts-rate'
    },[
      this.buildElement('span',null,{
        'class':'pts-star'
      },rating.tags),
      this.buildElement('span','('+length+' review'+rating.ws+')',{
        'class':'pts-review'  
      }),
    ]);
  return rating;
};

/* cart select */
this.cartSelect=function(k){
  var d=this.cartData(),
  n=[];
  for(var i of d){
    if(i.aid==k){
      n.push(i);
    }
  }return n;
};
/* cart update */
this.cartUpdate=function(k,v){
  var d=this.cartData(),
  n=[];
  for(var i of d){
    n.push(i.aid==k?v:i);
  }
  if(n.length!=d.length){
    return false;
  }
  this.cartData(n);
  return n;
};
/* cart delete */
this.cartDelete=function(k){
  var d=this.cartData(),
  n=[];
  for(var i of d){
    if(i.aid!=k){
      n.push(i);
    }
  }
  if(n.length==d.length){
    return false;
  }
  this.cartData(n);
  return n;
};
/* cart imsert */
this.cartInsert=function(v){
  var d=this.cartData();
  d.push(v);
  this.cartData(d);
  return d;
};
/* cart data */
this.cartData=function(v){
  var k='website-cart',
  s=null,r=false,d=null;
  if(v===false){
    localStorage.removeItem(k);
    return true;
  }else if(v){
    s=JSON.stringify(v);
    localStorage.setItem(k,s);
    return true;
  }
  d=localStorage.getItem(k);
  try{r=JSON.parse(d);}catch(e){}
  return r?r:[];
};

/* rate dialog */
this.rateDialog=function(pid){
  if(!this.buyerUser||!this.buyerUser.aid){
    return false;
  }
  var build=this.buildElement,
  _this=this,
  olddialog=document.getElementById('rate-dialog'),
  dialog=build('div',null,{
    'class':'rate-dialog',
    'id':'rate-dialog',
  });
  if(olddialog){
    olddialog.parentElement.removeChild(olddialog);
  }
  for(var i=1;i<=5;i++){
    var star=build('i',null,{
      'class':'fa fa-star-o pts-star-orange',
      'id':'rate-star-'+i,
    });
    star.dataset.value=i+'';
    star.dataset.pid=pid+'';
    star.onclick=function(e){
      var dp=this.parentElement,
      _this=_productTablePublic;
      if(!_this.buyerUser||!_this.buyerUser.aid){
        if(dp){dp.parentElement.removeChild(dp);}
        return false;
      }
      for(var u=1;u<=parseInt(this.dataset.value);u++){
        var sr=document.getElementById('rate-star-'+u);
        if(sr){
          sr.classList.replace('fa-star-o','fa-star');
        }
      }
      _this.loader(true);
      _this.request('rateSend',function(r){
        _this.loader(false);
        if(dp){dp.parentElement.removeChild(dp);}
        if(r!='OK'){return _this.error(r);}
        return _this.success(r);
      },{
        pid:this.dataset.pid,
        bid:_this.buyerUser.aid,
        rate:this.dataset.value,
      });
    };
    star.appendTo(dialog);
  }
  dialog.appendTo(document.body);
};
/* header tag -- category bulk list */
this.headerTag=function(cat_id,data){
  var pts={
    store:this.buildElement('div',null,{
      'class':'pts-store',
      'id':'pts-store-'+cat_id,
      'data-name':'',
    }),
  },
  _this=this;
  pts.store.location=this.buildElement('div','Loading...');
  pts.store.strong=this.buildElement('strong',null,{
    'data-cat_id':cat_id,
    'data-name':data.name,
  });
  pts.store.name=this.buildElement('div',null,{},[pts.store.strong]);
  pts.store.image=this.buildElement('img',null,{
    width:'50px',
    height:'50px',
    alt:data.name,
  });
  pts.store.image.onerror=function(e){
    this.src=_this.HTML_DATA.white;
  };
  pts.store.td2=this.buildElement('td',null,{},[
    pts.store.name,pts.store.location
  ]);
  pts.store.td1=this.buildElement('td',null,{},[pts.store.image]);
  pts.store.tr=this.buildElement('tr',null,{},[
    pts.store.td1,pts.store.td2
  ]);
  pts.store.tbody=this.buildElement('tbody',null,{},[pts.store.tr]);
  pts.store.table=this.buildElement('table',null,{
    'class':'pts-store-table',
    cellspacing:'5px',
    cellpadding:'0',
    border:'0',
  },[pts.store.tbody]);
  pts.store.image.style.display='none';
  pts.store.table.appendTo(pts.store);
  pts.store.strong.onclick=function(){
    var url=_this.PRODUCT_URL+'?tag='+this.dataset.cat_id;
    window.history.pushState({
      type:'category',
      data:{
        id:this.dataset.cat_id,
        name:this.dataset.name,
      }
    },this.dataset.name,url);
    _this.pageTitle(this.dataset.name);
    return _this.bulkStart(this.dataset.cat_id,null);
  };
  if(data){
    pts.store.image.src=_this.HTML_DATA.tag;
    pts.store.image.alt=data.name;
    pts.store.image.style.display='block';
    pts.store.strong.innerText=data.name;
    pts.store.strong.dataset.name=data.name;
    pts.store.location.innerText='Products of '+data.name;
    return pts.store;
  }
  return pts.store;
};
/* store tag */
this.storeTag=function(seller_id,data){
  var pts={
    store:this.buildElement('div',null,{
      'class':'pts-store',
      'id':'pts-store-'+seller_id,
      'data-name':'',
    }),
  },
  _this=this;
  pts.store.location=this.buildElement('div','Loading...');
  pts.store.strong=this.buildElement('strong',null,{
    'data-seller_id':seller_id,
    'data-name':'',
  });
  pts.store.name=this.buildElement('div',null,{},[pts.store.strong]);
  pts.store.image=this.buildElement('img',null,{
    width:'50px',
    height:'50px',
    alt:'',
  });
  pts.store.image.onerror=function(e){
    this.src=_this.HTML_DATA.white;
  };
  pts.store.td2=this.buildElement('td',null,{},[
    pts.store.name,pts.store.location
  ]);
  pts.store.td1=this.buildElement('td',null,{},[pts.store.image]);
  pts.store.tr=this.buildElement('tr',null,{},[
    pts.store.td1,pts.store.td2
  ]);
  pts.store.tbody=this.buildElement('tbody',null,{},[pts.store.tr]);
  pts.store.table=this.buildElement('table',null,{
    'class':'pts-store-table',
    cellspacing:'5px',
    cellpadding:'0',
    border:'0',
  },[pts.store.tbody]);
  pts.store.image.style.display='none';
  pts.store.table.appendTo(pts.store);
  pts.store.strong.onclick=function(){
    var url=_this.PRODUCT_URL+'?store='+this.dataset.seller_id;
    window.history.pushState({
      type:'store',
      data:{
        id:this.dataset.seller_id,
        name:this.dataset.name,
      }
    },this.dataset.name,url);
    _this.pageTitle(this.dataset.name);
    return _this.bulkStart(null,this.dataset.seller_id);
  };
  if(data){
    var images=this.parseJSON(data.picture);
    pts.store.image.src=images[0];
    pts.store.image.alt=data.name;
    pts.store.image.style.display='block';
    pts.store.strong.innerText=data.name;
    pts.store.strong.dataset.name=data.name;
    pts.store.location.innerText=data.location;
    return pts.store;
  }
  this.request('publicStore',function(r){
    if(typeof r==='object'&&r!==null
      &&r.hasOwnProperty('data')){
      var images=_this.parseJSON(r.data.picture);
      pts.store.image.src=images[0];
      pts.store.image.alt=r.data.name;
      pts.store.image.style.display='block';
      pts.store.strong.innerText=r.data.name;
      pts.store.strong.dataset.name=r.data.name;
      pts.store.location.innerText=r.data.location;
      pts.store.dataset.name=r.data.name;
      return true;
    }
    pts.store.style.display='none';
  },{seller_id:seller_id});
  return pts.store;
};
/* preload */
this.preloader=function(){
  var idn='<...preload>',
  inner=this.buildElement('div',null,{
    'class':'preload-inner'
  }),
  outer=this.buildElement('div',null,{
    'class':'preload-outer',
  },[inner]);
  return {
    idn:idn,
    inner:inner,
    outer:outer,
    clear:this.clearElement,
    override:function(el){
      this.clear(el);
      el.appendChild(this.outer);
      return this;
    },
  };
};
/* request */
this.request=function(mt,cb,dt){
  cb=typeof cb==='function'?cb:function(){};
  dt=typeof dt==='object'&&dt!==null?dt:{};
  if(typeof mt!=='string'){return cb(false);}
  var url=this.WEBSITE_ADDRESS
    +'?'+this.AJAX_QUERY.key
    +'='+this.AJAX_QUERY.value;
  dt.method=mt;
  return this.stream(url,cb,cb,dt);
};
/* preload column */
this.preloadColumn=function(){
  var c=this.originColumn(),d={};
  for(var i in c){
    d[i]='<...preload>';
  }return d;
};
/* origin column */
this.originColumn=function(){
  return {
    aid:0,
    title:'',
    content:'',
    price:0,
    place:0,
    host:'',
    start:0,
    stock:'',
    picture:'[]',
    keywords:'[]',
    description:'{}',
    datetime:'',
    url:'',
    author:'',
    trainer:'',
    end:'{}',
  };
};
/* conversion */
this.convertProduct=function(p){
  var res={
    aid:p.aid,
    name:p.title,
    description:p.content,
    price:p.price,
    bprice:p.place,
    currency:p.host,
    sold:p.start,
    stock:p.stock,
    pictures:this.parseJSON(p.picture),
    categories:this.parseJSON(p.keywords),
    spec:this.parseJSON(p.description),
    datetime:p.datetime,
    url:p.url,
    seller_id:p.author,
    badge:p.trainer,
    other:this.parseJSON(p.end),
    prices:this.parseDigit(p.price),
    bprices:this.parseDigit(p.place),
  };
  return res;
};
/* initialize category click */
this.categoryClickInit=function(){
  let _this=this,
  sps=document.getElementsByClassName('category-click');
  if(!sps){return;}
  for(let sp of sps){
    sp.onclick=_this.categoryClickExec;
  }
};
/* execute category click */
this.categoryClickExec=function(e){
  let _this=_productTablePublic,
  url=_this.PRODUCT_URL+'?tag='+this.dataset.id;
    window.history.pushState({
      type:'category',
      data:{
        id:this.dataset.id,
        name:this.dataset.name,
      }
    },this.dataset.name,url);
    _this.pageTitle(this.dataset.name);
    return _this.bulkStart(this.dataset.id);
}
/* category tag */
this.categoryTag=function(id,val){
  var build=this.buildElement,
  _this=this,
  sp=build('div',null,{
    'class':'category-tag-public',
    'data-id':id,
    'data-name':val,
  },[
    build('i',null,{
      'class':'fa fa-tag'
    }),
    build('span',val),
  ]);
  sp.onclick=function(e){
    var url=_this.PRODUCT_URL+'?tag='+this.dataset.id;
    window.history.pushState({
      type:'category',
      data:{
        id:this.dataset.id,
        name:this.dataset.name,
      }
    },this.dataset.name,url);
    _this.pageTitle(this.dataset.name);
    return _this.bulkStart(this.dataset.id);
  };
  return sp;
};
/* category data */
this.categoryData=function(v){
  var k='website-category',
  s=null,r=false,d=null;
  if(v===false){
    localStorage.removeItem(k);
    return true;
  }else if(v){
    s=JSON.stringify(v);
    localStorage.setItem(k,s);
    return true;
  }
  d=localStorage.getItem(k);
  try{r=JSON.parse(d);}catch(e){}
  return r?r:{};
};
/* spec row public */
this.specRow=function(key,value){
  var build=this.buildElement,
  tdk=build('td',key),
  tdv=build('td',value);
  tr=build('tr',null,{
    'class':'spec-row-pub',
    'data-key':key,
    'data-value':value,
  },[tdk,tdv]);
  return tr;
};
/* slider -- require: tiny-slider.js */
this.slider=function(images,cname='slider'){
  images=Array.isArray(images)?images:[];
  let imgs=[],
  children=[],
  div=document.createElement('div');
  for(var i in images){
    var di=document.createElement('div');
    var img=document.createElement('img');
    img.src=''+images[i];
    di.appendChild(img);
    div.appendChild(di);
    imgs.push(img);
    children.push(di);
  }
  div.classList.add('slider');
  if(cname!='slider'){
    div.classList.add(cname);
  }
  return {init:function(){return tns({
    container:'.'+cname,
    items:1,
    slideBy:"page",
    mouseDrag:false,
    swipeAngle:false,
    controls:false,
    nav:false,
    speed: 400,
    startIndex:0,
    rewind:false,
    center:false,
    autoWidth:false,
    loop:true,
    autoplay:true,
    autoplayHoverPause:false,
    autoplayTimeout:2500,
    autoplayText:[
      "▶",
      "❚❚",
    ],
    autoplayButton:false,
    autoplayButtonOutput:false,
    autoplayResetOnVisibility:false,
  })},
    element:div,
    images:imgs,
    children:children,
  };
};

/* ------- OTHER STAND-ALONE ------- */
/* parse alert */
this.parseAlert=function(){
  var r=[];
  for(let i of arguments){
    r.push(i);
  }
  return alert(JSON.stringify(r));
};
/* parse json */
this.parseJSON=function(str){
  var res=false;
  try{res=JSON.parse(str);}catch(e){
    return false;
  }return res;
};
/* parse digit */
this.parseDigit=function(d){
  var s=d.toString(),
  r=[],i=s.length,t=0;
  while(i>0){
    r.push(s.substring(i-3,i));
    i-=3;
  }
  return r.reverse().join('.');
};
/* parse discount */
this.parseDiscount=function(a,b){
  return (Math.ceil(a/b*100)-100)+'%';
};

/* ------- EXTERNAL PAGE ------- */
/* external page -- loaded inside iframe */
this.externalPage=function(url,title){
  var id='website-frame';
  var frame=document.querySelector('iframe#'+id);
  var fc=document.querySelector('#'+id+'-close');
  var fh=document.querySelector('#'+id+'-head');
  if(frame){frame.parentElement.removeChild(frame);}
  if(fh){fh.parentElement.removeChild(fh);}
  if(fc){fc.parentElement.removeChild(fc);}
  if(typeof url!=='string'){return false;}
  /* frame title */
  title=typeof title==='string'?title:'Untitled';
  /* frame element */
  frame=document.createElement('iframe');
  frame.id=id;
  frame.classList.add(id);
  frame.src=url;
  frame.onload=function(){
    _productTablePublic.loader(false);
  };
  frame.onerror=function(){
    _productTablePublic.loader(false);
  };
  document.body.appendChild(frame);
  _productTablePublic.loader(true);
  /* head element */
  fh=document.createElement('div');
  fh.classList.add(id+'-head');
  fh.id=id+'-head';
  fh.dataset.title=title;
  document.body.appendChild(fh);
  /* close element */
  fc=document.createElement('div');
  fc.classList.add(id+'-close');
  fc.id=id+'-close';
  fc.title='Close';
  document.body.appendChild(fc);
  /* donr scroll body */
  if(!document.body.classList.contains('dont-scroll')){
    document.body.classList.add('dont-scroll');
  }
  /* prepare title */
  var dt=document.querySelector('title');
  if(dt){
    var baseTitle=dt.innerText;
    fc.dataset.title=baseTitle;
    dt.innerText=title;
  }
  /* set global variable as true */
  window.EXTERNAL_OPEN=true;
  /* click event */
  fc.onclick=function(e){
    document.body.classList.remove('dont-scroll');
    this.parentElement.removeChild(this);
    if(frame){frame.parentElement.removeChild(frame);}
    if(fh){fh.parentElement.removeChild(fh);}
    if(dt){dt.innerText=this.dataset.title;}
    /* set global variable as false */
    window.EXTERNAL_OPEN=false;
    return true;
  };return true;
};
/* close external page -- stand-alone */
this.externalPageClose=function(){
  var id='website-frame';
  var frame=document.querySelector('iframe#'+id);
  var fc=document.querySelector('#'+id+'-close');
  var fh=document.querySelector('#'+id+'-head');
  var dt=document.querySelector('title');
  document.body.classList.remove('dont-scroll');
  /* set global variable as false */
  window.EXTERNAL_OPEN=false;
  if(frame){frame.parentElement.removeChild(frame);}
  if(fh){fh.parentElement.removeChild(fh);}
  if(fc){
    if(dt){dt.innerText=fc.dataset.title;}
    fc.parentElement.removeChild(fc);
  }return true;
};

/* ------- LOADERS ------- */
/* fake head loader -- for local data */
this.fakeHeadLoader=function(cb,value){
  cb=typeof cb==='function'?cb:function(){};
  value=value?parseInt(value):0;
  value=Math.max(Math.min(value,100),0);
  if(value>=100){
    _productTablePublic.headLoader(false);
    return cb(true);
  }_productTablePublic.headLoader(value);
  return setTimeout(function(){
    value+=3;
    return _productTablePublic.fakeHeadLoader(cb,value);
  },10);
};

/* ------- STAND-ALONE METHODS ------- */
/* head loader -- stand-alone */
this.headLoader=function(value){
  /* set loader id */
  var id='website-head-loader';
  /* get element */
  var hl=document.getElementById(id);
  var bar=document.getElementById(id+'-bar');
  /* check value */
  if(typeof value!=='number'){
    if(hl){hl.parentElement.removeChild(hl);}
    return false;
  }
  /* set minimum and maximum value */
  value=Math.max(Math.min(value,100),0);
  /* check hl */
  if(!hl){
    /* create new element */
    var hl=document.createElement('div');
    var bar=document.createElement('div');
    hl.classList.add('website-head-loader');
    bar.classList.add('website-head-loader-bar');
    hl.id=id;
    bar.id=id+'-bar';
  }
  /* set value */
  bar.style.width=value+'%';
  /* append child */
  hl.appendChild(bar);
  document.body.appendChild(hl);
};
/* loader -- stand-alone */
this.loader=function(open,text){
  var id='website-loader';
  var ld=document.getElementById(id);
  if(!open){
    if(ld){ld.parentElement.removeChild(ld);}
    return false;
  }text=typeof text==='string'?text:'Loading...';
  if(!ld){
    var ld=document.createElement('div');
    ld.classList.add(id);
    ld.id=id;
  }ld.dataset.text=text;
  document.body.appendChild(ld);
  return true;
};
/* create text node */
this.textNode=function(t){
  return document.createTextNode(t);
};
/* build element */
this.buildElement=function(tag,text,attr,children,html,content){
  var div=document.createElement(typeof tag==='string'?tag:'div');
  div.appendTo=function(el){
    if(typeof el.appendChild==='function'){
      el.appendChild(this);
      return true;
    }return false;
  };
  if(typeof text==='string'){
    div.innerText=text;
  }
  if(typeof attr==='object'&&attr!==null){
    for(var i in attr){
      div.setAttribute(i,attr[i]);
    }
  }
  if(Array.isArray(children)){
    for(var i=0;i<children.length;i++){
      div.appendChild(children[i]);
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
/* clear element field */
this.clearElement=function(el){
  if(!el||!el.childNodes){return false;}
  var i=el.childNodes.length;
  while(i--){
    el.removeChild(el.childNodes[i]);
  }return true;
};
/* parse string -- url query */
this.parseStr=function(t){
  if(typeof t!=='string'){return false;}
  var s=t.split('&');
  var r={},c={};
  for(var i=0;i<s.length;i++){
    if(!s[i]||s[i]==''){continue;}
    var p=s[i].split('=');
    var k=decodeURIComponent(p[0]);
    if(k.match(/\[(.*)?\]$/g)){
      var l=k.replace(/\[(.*)?\]$/g,'');
      var w=k.replace(/^.*\[(.*)?\]$/g,"$1");
      c[l]=c[l]?c[l]:0;
      if(w==''){w=c[l];c[l]+=1;}
      if(!r[l]){r[l]={};}
      r[l][w]=decodeURIComponent(p[1]);
      continue;
    }r[k]=p[1]?decodeURIComponent(p[1]):'';
  }return r;
};
/* disabled */
this.disabled=function(el,pos){
  if(!Array.isArray(el)){return false;}
  pos=typeof pos==='boolean'?pos:true;
  for(var i=0;i<el.length;i++){
    if(el[i].nodeName==='INPUT'){
      el[i].disabled=pos;
    }
  }return true;
};
/* get content
 * @parameters:
 *   url = string of url
 *   cb  = function of success callback
 *   er  = function of error callback
 *   txt = bool of text output; default: true
 */
this.getContent=function(url,cb,er,txt){
  cb=typeof cb==='function'?cb:function(){};
  er=typeof er==='function'?er:function(){};
  txt=txt===false?false:true;
  var xhr=new XMLHttpRequest();
  xhr.open('GET',url,true);
  xhr.send();
  xhr.onreadystatechange=function(e){
    if(xhr.readyState==4){
      if(xhr.status==200){
        var text=xhr.responseText?xhr.responseText:' ';
        if(txt){return cb(text);}
        var res=false;
        try{res=JSON.parse(text);}catch(e){}
        return cb(res?res:text);
      }else if(xhr.status==0){
        return er('Error: No internet connection.');
      }return er('Error: '+xhr.status+' - '+xhr.statusText+'.');
    }else if(xhr.readyState<4){
      return false;
    }return er('Error: '+xhr.status+' - '+xhr.statusText+'.');
  };return true;
};
/* stream
 * @require: this.uniform
 * @parameters:
 *   url = string of url
 *   cb  = function of success callback of response code 200
 *   er  = function of error callback
 *   dt  = object of data form
 *   hd  = object of headers
 *   ul  = function of upload progress
 *   dl  = function of download progress
 *   mt  = string of method
 *   ud4 = function of under-four ready-state
 * @return: void
 */
this.stream=function(url,cb,er,dt,hd,ul,dl,mt,ud4){
  /* prepare callbacks */
  cb=typeof cb==='function'?cb:function(){};
  er=typeof er==='function'?er:function(){};
  ul=typeof ul==='function'?ul:function(){};
  dl=typeof dl==='function'?dl:function(){};
  ud4=typeof ud4==='function'?ud4:function(){};
  /* prepare xhr --> xmlhttp */
  var xmlhttp=false;
  if(window.XMLHttpRequest){
    xmlhttp=new XMLHttpRequest();
  }else{
    /* older browser xhr */
    var xhf=[
      function(){return new ActiveXObject("Msxml2.XMLHTTP");},
      function(){return new ActiveXObject("Msxml3.XMLHTTP");},
      function(){return new ActiveXObject("Microsoft.XMLHTTP");}
    ];
    for(var i=0;i<xhf.length;i++){try{xmlhttp=xhf[i]();}catch(e){continue;}break;}
  }
  /* check xhr */
  if(!xmlhttp){return er('Error: Failed to build XML http request.');}
  /* set method */
  var mts=['GET','POST','PUT','OPTIONS','HEAD','DELETE'];
  mt=typeof mt==='string'&&mts.indexOf(mt)>=0?mt
    :typeof dt==='object'&&dt!==null?'POST':'GET';
  /* open xhr connection */
  xmlhttp.open(mt,url,true);
  /* build urlencoded form data */
  if(typeof dt==='object'&&dt!==null
    &&!dt.hasOwnProperty('append')
    &&typeof dt.append!=='function'){
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    dt=this.buildQuery(dt);
  }
  /* set headers */
  if(typeof hd=='object'&&hd!=null){
    for(var i in hd){xmlhttp.setRequestHeader(i,hd[i]);}
  }
  /* xhr ready state change */
  xmlhttp.onreadystatechange=function(e){
    if(xmlhttp.readyState===4&&xmlhttp.status===200
      &&typeof xmlhttp.responseText==='string'){
      try{var res=JSON.parse(xmlhttp.responseText);}
      catch(e){var res=xmlhttp.responseText;}
      return cb(res);
    }else if(xmlhttp.readyState===4){
      if(xmlhttp.status===0){return er('Error: No internet connection.');}
      return er('Error: '+xmlhttp.status+' '+xmlhttp.statusText);
    }else if(xmlhttp.readyState<4){
      return ud4('Mobile::stream--> '+xmlhttp.readyState+' '+xmlhttp.status+' '+xmlhttp.statusText);
    }return er('Error: '+xmlhttp.status+' '+xmlhttp.statusText);
  };
  /* set callback for upload and download */
  xmlhttp.upload.onprogress=ul;
  xmlhttp.addEventListener("progress",dl,false);
  /* send XHR */
  xmlhttp.send(dt);
};
/* build query stream data form */
this.buildQuery=function(data,key){
  var ret=[],dkey=null;
  for(var d in data){
    dkey=key?key+'['+encodeURIComponent(d)+']'
        :encodeURIComponent(d);
    if(typeof data[d]=='object'&&data[d]!==null){
      ret.push(this.buildQuery(data[d],dkey));
    }else{
      ret.push(dkey+"="+encodeURIComponent(data[d]));
    }
  }return ret.join("&");
};
/* uniform -- build urlencoded form data */
this.uniform=function(dt){
  var ret=[];
  for(var d in dt){
    if(Array.isArray(dt[d])||(typeof dt[d]=='object'&&dt[d]!==null)){
      ret.push(this.uniform(dt[d]));
    }else{ret.push(encodeURIComponent(d)+"="+encodeURIComponent(dt[d]));}
  }return ret.join("&");
};
/* set cookie */
this.setCookie=function(cname,cvalue,exdays,domain,path){
  exdays=exdays?parseInt(exdays):1;
  var d=new Date();
  d.setTime(d.getTime()+(exdays*24*60*60*1000));
  var expires=";expires="+d.toGMTString();
  var domain=domain?";domain="+domain:'';
  var path=path?";path="+path:'';
  /* BlackBerry browser version 5.0 doesn't support document.cookie */
  document.cookie=cname+"="+cvalue+expires+domain+path;
  return true;
};
/* get cookie */
this.getCookie=function(cname){
  var name=cname+"=",r=false;
  var ca=document.cookie.split(';');
  for(var i=0;i<ca.length;i++){
    var c=ca[i].trim();
    if(c.indexOf(name)==0){
      r=c.substring(name.length,c.length);
      break;
    }
  }return r;
};
/* default alert - sweet */
this.alert=function(title,text,type,callback){
  title=typeof title==='string'?title:'';
  text=typeof text==='string'?text:'';
  type=typeof type==='string'?type:'';
  return typeof swal==='function'?swal({
      title:title,
      text:text,
      type:type
    },callback):alert(title+'\r\n\r\n'+text);
};
/* default error */
this.error=function(text,callback){
  text=typeof text==='string'?text:text.toString();
  return typeof swal==='function'?swal({
      title:"Error",
      text:text,
      type:"error"
    },callback):alert(text);
};
/* default success */
this.success=function(text,callback){
  text=typeof text==='string'?text:text.toString();
  return typeof swal==='function'?swal({
      title:"Success",
      text:text,
      type:"success"
    },callback):alert(text);
};
/* default confirm */
this.confirm=function(title,text,callback){
  if(typeof title!=='string'
    ||typeof text!=='string'
    ||typeof callback!=='function'){return;}
  if(typeof swal!=='function'){
    var c=confirm(title+'\r\n\r\n'+text);
    return callback(c);
  }
  return swal({
    title:title,text:text,type:"warning",
    showCancelButton:true,
    confirmButtonColor:"#DD6B55",
    cancelButtonColor:"#DDFFBB",
    confirmButtonText:"Yes",
    cancelButtonText:"No",
    closeOnConfirm:true
  },callback);
};
/* initialize */
return this.init();
};
