/* script.js for hit couter */
hitCounterStart();

/* start hit counter preparing */
function hitCounterStart(c){
  c=c?parseInt(c):0;
  if(window.hasOwnProperty('_website')){
    return hitCounterInit();
  }else if(c>99){
    return false;
  }c++;
  return setTimeout(function(){
    return hitCounterStart(c);
  },50);
}

/* initialize hit counter */
function hitCounterInit(){
  _website.onclick.push(function(e){
    var hcs=document.getElementById('hit_counter_sidebar');
    var hcd=document.getElementsByClassName('hit-counter-digit');
    var hcc=document.getElementsByClassName('hit-counter-content');
    if(!hcs||!hcd||!hcc||!hcs.dataset.total){return false;}
    var total=parseInt(hcs.dataset.total)+1,
      i=hcd.length,o=hcc.length;
    hcs.dataset.total=total;
    while(i--){hcd[i].innerText=total;}
    while(o--){hcc[o].title='Total Hit: '+total;}
    return hitCounterSendTotal();
  });
}

/* hit counter send total count */
function hitCounterSendTotal(){
  var url=WEBSITE_ADDRESS+'?hit-counter-get-total=true';
  return _website.getContent(url,function(r){
    if(r=='OK'){return true;}
    return console.log(r);
  },function(e){
    console.error(e);
  });
}


