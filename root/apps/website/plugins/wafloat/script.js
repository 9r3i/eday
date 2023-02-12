
(function(){
  let wab=document.getElementById('wafloat');
  if(!wab){return;}
  wab.onclick=function(){
    let cl='wafloat-wide',
    text=this.dataset.text,
    hc=this.classList.contains(cl);
    if(window.location.search.match(/^\?id=/)){
      text=window.location.href+'\n\n'+text+'\n';
    }
    if(hc){
      let url=this.dataset.url+'&text='+encodeURIComponent(text);
      this.classList.remove(cl);
      window.open(url,'_blank');
    }else{
      this.classList.add(cl);
    }
  };
})();