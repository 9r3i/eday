/* dateSelect.js */
;function dateSelect(select,options){
/* the version */
this.version='1.0.0';
/* prepare element from select */
this.element=document.querySelector(select);
if(!this.element){return false;}
/* prepare options */
this.options=typeof options==='object'
  &&options!==null?options:{};
/* unique id */
this.uniqueID='date-select-'
  +Math.ceil(Math.random()*(new Date).getTime());
/* selector element */
this.parent=null;
this.selector={};
this.dataset={
  uid:this.uniqueID,
};
/* get result */
this.result=function(){
  var res=[];
  for(var k in this.selector){
    res.push(this.selector[k].value);
  }return res.join('-');
};
/* hide */
this.hide=function(){
  this.element.style.display='block';
  for(var k in this.selector){
    this.selector[k].style.display='none';
  }return true;
};
/* show */
this.show=function(){
  this.element.style.display='none';
  for(var k in this.selector){
    this.selector[k].style.display='block';
  }return true;
};
/* initial */
this.init=function(){
  /* hide element */
  this.element.style.display='none';
  /* get parent element */
  this.parent=this.element.parentElement;
  /* create new element */
  var year=document.createElement('select');
  var month=document.createElement('select');
  var date=document.createElement('select');
  /* add ids and uid */
  year.id=this.uniqueID+'-year';
  month.id=this.uniqueID+'-month';
  date.id=this.uniqueID+'-date';
  year.dataset.uid=this.uniqueID;
  month.dataset.uid=this.uniqueID;
  date.dataset.uid=this.uniqueID;
  this.element.dataset.dateSelect=this.uniqueID;
  /* get element value */
  var dtime=new Date(this.element.value);
  if(dtime.toString()==="Invalid Date"){
    dtime=new Date;
  }
  year.dataset.value=dtime.getFullYear();
  month.dataset.value=dtime.getMonth();
  date.dataset.value=dtime.getDate();
  /* restyled */
  year.style.width='auto';
  month.style.width='auto';
  date.style.width='auto';
  /* return to prepare */
  this.selector={
    year:year,
    month:month,
    date:date,
  };
  /* return to prepare */
  return this.prepare();
};
/* prepare selectors */
this.prepare=function(){
  /* prepare year */
  for(var i=1970;i<=2030;i++){
    var op=document.createElement('option');
    op.value=i;
    op.innerText=i;
    if(i==this.selector.year.dataset.value){
      op.setAttribute('selected','selected');
      op.selected=true;
    }this.selector.year.appendChild(op);
  }
  /* prepare month */
  var bulan=[
    'January','February','March','April',
    'May','June','July','August',
    'September','October','November','December'  
  ];
  for(var i=0;i<bulan.length;i++){
    var op=document.createElement('option');
    op.value=(i<9?'0':'').toString()+(i+1).toString();
    op.innerText=bulan[i];
    if(i==this.selector.month.dataset.value){
      op.setAttribute('selected','selected');
      op.selected=true;
    }this.selector.month.appendChild(op);
  }
  /* prepare date */
  for(var i=1;i<=31;i++){
    var op=document.createElement('option');
    op.value=(i<10?'0':'').toString()+(i).toString();
    op.innerText=i;
    if(i==this.selector.date.dataset.value){
      op.setAttribute('selected','selected');
      op.selected=true;
    }this.selector.date.appendChild(op);
  }
  /* appending to parent */
  this.parent.insertBefore(this.selector.date,this.element);
  this.parent.insertBefore(this.selector.month,this.element);
  this.parent.insertBefore(this.selector.year,this.element);
  /* month onchange for dates */
  this.selector.date.onchange=this.change;
  this.selector.month.onchange=this.change;
  this.selector.year.onchange=this.change;
  /* turn on change */
  this.change();
  /* return as this object */
  return this;
};
/* on date change */
this.change=function(e){
  /* get unique id */
  var uid=this.dataset.uid;
  /* prepare elements */
  var ny=document.getElementById(uid+'-year');
  var nm=document.getElementById(uid+'-month');
  var nd=document.getElementById(uid+'-date');
  /* prepare the main element */
  var el=document.querySelector('[data-date-select="'+uid+'"]');
  /* prepare months of 30 date and default end */
  var tn=['04','06','09','11'],end=31;
  /* check for february -- kabisat */
  if(nm.value=='02'){
    end=parseInt(ny.value)%4>0?28:29;
  }else if(tn.indexOf(nm.value)>=0){
    end=30;
  }
  /* set default date value and remove all options */
  var def=parseInt(nd.value);
  nd.innerHTML='';
  /* start write new option element */
  for(var i=1;i<=end;i++){
    var op=document.createElement('option');
    op.value=(i<10?'0':'').toString()+(i).toString();
    op.innerText=i;
    if(i==def){
      op.setAttribute('selected','selected');
      op.selected=true;
    }nd.appendChild(op);
  }
  /* update main element */
  el.value=[
    ny.value,
    nm.value,
    nd.value
  ].join('-');
  /* return as true */
  return true;
};
/* return this initailizer */
return this.init();
};


