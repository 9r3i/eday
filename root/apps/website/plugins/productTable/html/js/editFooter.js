/* editFooter.js */
window.editFooter={
/* initialize editor */
init:function(rdata){
  //alert(JSON.stringify(HTML_CONTENTS.footer));
  let id='content-editor',
  submit=document.getElementById('content-save'),
  con=document.getElementById(id);
  con.textContent=HTML_CONTENTS.footer,
  ed=window.CKEDITOR.replace(id),
  originData=document.getElementById(id);
      ed.on('key',function(e){
        originData.value=e.editor.getData();
      });
      ed.on('blur',function(e){
        originData.value=e.editor.getData();
      });
      ed.on('change',function(e){
        originData.value=e.editor.getData();
      });
      setTimeout(e=>{
        let ckes=document.getElementsByClassName('cke_contents');
        if(ckes){
          for(let el of ckes){
            el.style.height='400px';
          }
        }
      },1500);
  submit.onclick=function(){
    submit.disabled=true;
    submit.innerHTML='<i class="fa fa-pulse fa-spinner"></i> Saving...';
    _productTable.request('saveFooter',function(r){
      submit.disabled=false;
      submit.innerHTML='<i class="fa fa-save"></i> Save';
      //alert(JSON.stringify(r));
      if(r=='OK'){
        return _productTable.success(
          'Footer has been updated.',function(e){
            
          });
      }
      return _productTable.error(JSON.stringify(r));
    },{
      data:originData.value,
    });
  };
},
};


