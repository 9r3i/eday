/* footer.js for e-Day - require: sweetalert.js */
var W,D;

/* default alert - sweet */
function salert(t,d,p,cb){
  t=typeof t==='string'?t:'';
  d=typeof d==='string'?d:'';
  p=typeof p==='string'?p:'';
  return typeof swal==='function'?swal({title:t,text:d,type:p},cb):alert(t+'\r\n\r\n'+d);
}
/* default error */
function error(e,cb){
  e=typeof e==='string'?e:e.toString();
  return typeof swal==='function'?swal({title:"Error",text:e,type:"error"},cb):alert(e);
}
/* default error */
function success(e,cb){
  e=typeof e==='string'?e:e.toString();
  return typeof swal==='function'?swal({title:"Success",text:e,type:"success"},cb):alert(e);
}
/* default confirm */
function confirmation(title,text,callback){
  if(typeof title!=='string'
    ||typeof text!=='string'
    ||typeof callback!=='function'){return;}
  if(typeof swal!=='function'){
    var c=confirm(title+'\r\n\r\n'+text);
    return callback(c);
  }
  swal({
    title:title,text:text,type:"warning",
    showCancelButton:true,
    confirmButtonColor:"#DD6B55",
    cancelButtonColor:"#DDFFBB",
    confirmButtonText:"Yes",
    cancelButtonText:"No",
    closeOnConfirm:true
  },callback);
}


