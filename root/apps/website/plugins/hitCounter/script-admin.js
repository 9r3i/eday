/* script-admin.js */
var MSERVER_LOG;new hitCounterAdmin(MSERVER_LOG);
;function hitCounterAdmin(data){
/* version */
this.version='1.0.0';
this.MSERVER_STATISTIC=null;
var _hitCounterAdmin=this;
/* initial */
this.init=function(data){
  /* parse data */
  this.MSERVER_STATISTIC=this.mserverLogParseStatistic(data);
  /* print out the statistic */
  return this.printStatistic(null,this.MSERVER_STATISTIC.total);
};
/* print statistic object */
this.printStatistic=function(level,total){
  /* check data statistic */
  if(!this.MSERVER_STATISTIC){return false;}
  /* check element */
  var stat=document.getElementById('dashboard-statistic'+(level?'-'+level:''));
  if(!this.MSERVER_STATISTIC||!stat){return false;}
  /* prepare statistic level */
  var dlevel=typeof level==='string'?level.split(/:/):[];
  /* prepare data statistic */
  var data=this.MSERVER_STATISTIC;
  for(var i=0;i<dlevel.length;i++){
    if(data.hasOwnProperty(dlevel[i])){
      data=data[dlevel[i]];
    }
  }
  /* sort keys */
  var keys=[],ndata={};
  for(var k in data){
    if(k==='name'||k==='total'){continue;}
    keys.push('10'+k);
  }keys.sort();
  /* print out result */
  var ul=document.createElement('ul');
  for(var i=0;i<keys.length;i++){
    var k=keys[i].replace(/^10/,'');
    if(k==='name'||k==='total'){continue;}
    var percentT=total?data[k].total/total*100:0;
    var percent=total?Math.floor(data[k].total/total*100):0;
    var div=document.createElement('div');
    var li=document.createElement('li');
    var lid=document.createElement('div');
    var an=document.createElement('a');
    var id='dashboard-statistic-'+(level?[level,k]:[k]).join(':');
    lid.classList.add('statistic-percent');
    lid.style.width=percentT+'%';
    an.dataset.level=(level?[level,k]:[k]).join(':');
    an.dataset.id=id;
    an.dataset.total=data[k].total;
    an.innerText=data[k].name+' '+k+' --> '+data[k].total+' --> '+percent+'%';
    an.onclick=function(){
      if(this.dataset.loaded){
        this.dataset.loaded='';
        var el=document.getElementById(this.dataset.id);
        if(el){el.innerHTML='';}
        return false;
      }this.dataset.loaded='true';
      return _hitCounterAdmin.printStatistic(this.dataset.level,this.dataset.total);
    };
    div.id=id;
    li.appendChild(lid);
    li.appendChild(an);
    li.appendChild(div);
    ul.appendChild(li);
  }
  /* append to stat */
  stat.appendChild(ul);
  /* return as true */
  return true;
};
/* parse mserver.log into statistic object */
this.mserverLogParseStatistic=function(r){
  /* check input type of r */
  r=typeof r==='string'?r:'';
  /* split string into array */
  var logs=r.split(/\r\n|\r|\n/);
  var i=logs.length,res={total:0,name:'root'};
  /* prepare mserver.log pattern */
  var ptrn=/^\[(\d{4}\-\d{2}\-\d{2}\s\d{2}:\d{2}:\d{2})\]\s(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}):(\d{1,5})\s([A-Z]+)\s\[(\d{3})\]\s(\/.*)$/i;
  /* start parsing each row into an object */
  while(i--){
    /* check row using pattern */
    var row=logs[i].trim().match(ptrn);
    if(!row){continue;}
    /* get date and hour */
    var dh=row[1].split(/[^\d]/);
    /* prepare access object */
    var pa={
      time:row[1],
      ip:row[2],
      port:row[3],
      method:row[4],
      code:row[5],
      uri:row[6],
      year:dh[0],
      month:dh[1],
      date:dh[2],
      hour:dh[3],
      minute:dh[4],
      second:dh[5],
    };
    /* push object year */
    res.total++;
    if(!res.hasOwnProperty(pa.year)){
      res[pa.year]={total:0,name:'year'};
    }res[pa.year].total++;
    /* push object month */
    if(!res[pa.year].hasOwnProperty(pa.month)){
      res[pa.year][pa.month]={total:0,name:'month'};
    }res[pa.year][pa.month].total++;
    /* push object date */
    if(!res[pa.year][pa.month].hasOwnProperty(pa.date)){
      res[pa.year][pa.month][pa.date]={total:0,name:'date'};
    }res[pa.year][pa.month][pa.date].total++;
    /* push object hour */
    if(!res[pa.year][pa.month][pa.date].hasOwnProperty(pa.hour)){
      res[pa.year][pa.month][pa.date][pa.hour]={total:0,name:'hour'};
    }res[pa.year][pa.month][pa.date][pa.hour].total++;
    /* push object minute */
    if(!res[pa.year][pa.month][pa.date][pa.hour].hasOwnProperty(pa.minute)){
      res[pa.year][pa.month][pa.date][pa.hour][pa.minute]={total:0,name:'minute'};
    }res[pa.year][pa.month][pa.date][pa.hour][pa.minute].total++;
  }return res;
};
/* return this initialize */
return this.init(data);
};


