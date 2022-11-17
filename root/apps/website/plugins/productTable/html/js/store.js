/* store.js */
window.store={
/* title or name */
nameInput:function(){
  var title=document.querySelector('.title'),
  inputTitle=document.querySelector('input[name="name"]');
  inputTitle.dataset.name='';
  inputTitle.onkeyup=function(e){
    title.innerText='My Store: '
                   +(this.value==''?'[ERROR_EMPTY]':this.value);
    this.dataset.name=this.value;
  };
  return inputTitle;
},
/* headwr */
header:function(rdata){
  rdata.ndata={};
  var temp=false;
  try{
    temp=JSON.parse(rdata.data.picture);
  }catch(e){}
  rdata.ndata.images=temp?temp:[];
  return rdata;
},
/* initialize editor */
init:function(rdata){
  //alert(JSON.stringify(rdata));
  var ipicture=_productTable.pictureInput(
      'input[name="picture"]',
      rdata.ndata.images
    ), // pictures
  iname=this.nameInput(), // name
  iphone=document.querySelector('input[name="phone"]'),
  ilocation=document.querySelector('input[name="location"]'),
  iaddress=document.querySelector('textarea[name="address"]'),
  iabout=document.querySelector('textarea[name="about"]'),
  submit=document.querySelector('button[name="submit"]');
  submit.onclick=function(e){
    var data={
      aid:rdata.data.aid,
      seller_id:rdata.data.seller_id,
      name:iname.value,
      phone:iphone.value,
      location:ilocation.value,
      picture:JSON.stringify([ipicture.dataArray.pop()]),
      address:iaddress.value,
      about:iabout.value,
    };
    if(true){
      //return alert(JSON.stringify(data));
    }
    submit.disabled=true;
    submit.innerHTML='<i class="fa fa-pulse fa-spinner"></i> Saving...';
    _productTable.request('storeUpdate',function(r){
      submit.disabled=false;
      submit.innerHTML='<i class="fa fa-save"></i> Save';
      //alert(JSON.stringify(r));
      if(typeof r==='object'&&r!==null){
        return _productTable.success(
          'Store data has been saved.',function(e){
            
          });
      }
      return _productTable.error(JSON.stringify(r));
    },data);
  };
}
};


