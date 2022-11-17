/* orders.js */
window.orders={
data:null,
/* initailize */
init:function(data){
  //alert(JSON.stringify(data));
  this.data=data.data;
  var od=this.content(data.data),
  el=document.getElementById('orders-list');
  od.body.appendTo(el);
  return od;
},
/* delete orders */
delete:function(ids){
  var _this=this;
  return _this.confirm('Delete',
    'Do you delete this order?'
      +'\nMake sure that the items has delivered.'
      +'',function(yes){
    if(!yes){return false;}
    _productTable.loader(true,'Deleting...');
    return _productTable.request('deleteOrders',function(r){
      _productTable.loader(false);
      if(r!=='OK'){return _productTable.error(r);}
      var elid=ids.join('-'),
      pas=document.getElementById(elid),
      gtot=document.getElementById('grand-total');
      if(gtot){
        var gval=parseInt(gtot.dataset.total,10),
        currency=gtot.dataset.currency;
        if(pas){
          gval-=parseInt(pas.dataset.total,10);
        }
        gtot.innerText=currency+_this.parseDigit(gval);
        gtot.dataset.total=gval+'';
      }
      if(pas){pas.parentElement.removeChild(pas);}
      var tit=document.querySelector('.title');
      if(tit){
        var cl=tit.innerText.match(/\((\d+)\)/),
        ll=parseInt(cl[1],10)-1;
        tit.innerText='My Products ('+ll+')';
      }
      return _productTable.success(r);
    },{
      ids:JSON.stringify(ids),
    });
  });
},
/* buyers placed orders content */
content:function(d){
  var _this=this,
  build=this.buildElement,
  dt={},
  currency='',
  grandTotal=0,
  od={
    body:build('div',null,{
      'class':'order-body',
    }),
    detail:build('div',null,{
      'class':'order-detail',
    }),
    paymentMethod:build('div',null,{
      'class':'order-payment-method',
    },[
      build('div'),
    ]),
    grandTotal:build('span',null,{
      'class':'order-grand-total',
      id:'grand-total',
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
  od.detail.appendTo(od.body);
  od.paymentMethod.appendTo(od.body);
  od.footer.appendTo(od.body);
  od.grandTotal.appendTo(od.footerTotal);
  od.footerTotal.appendTo(od.footer);
  if(d.length<1){
    od=build('div','You don\'t have an order yet.');
    return od;
  }
  for(var i=0;i<d.length;i++){
    var p=d[i],
    bak=p.buyer_address;
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
  od.grandTotal.dataset.total=grandTotal+'';
  od.grandTotal.dataset.currency=currency;
  for(var u in dt){
    var p=dt[u],
    ids=[],
    _this=this,
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
    button=build('button',null,{
      'class':'order-button'
    },[
      build('i',null,{
        'class':'fa fa-trash'
      }),
      this.textNode('Delete'),
    ]),
    elb=build('div',null,{
      'class':'order-detail-subtotal'
    },[button]),
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
      elb,
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
      ids.push(i.order_id);
    }
    el.appendTo(od.detail);
    el.id=ids.join('-');
    el.dataset.total=p.totalPrice+'';
    button.dataset.ids=JSON.stringify(ids);
    button.onclick=function(e){
      return _this.delete(JSON.parse(this.dataset.ids));
    };
  }
  return od;
},
/* parse digit */
parseDigit:function(d){
  var s=d.toString(),
  r=[],i=s.length,t=0;
  while(i>0){
    r.push(s.substring(i-3,i));
    i-=3;
  }
  return r.reverse().join('.');
},
/* create text node */
textNode:function(t){
  return document.createTextNode(t);
},
/* default confirm */
confirm:function(title,text,callback){
  return _productTable.confirm(title,text,callback);
},
/* build element */
buildElement:function(tag,text,attr,children,html,content){
  return _productTable.buildElement(tag,text,attr,children,html,content);
}
};

