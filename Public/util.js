//动态加载外部css
function loadCss(file) {
      var head = $("head").remove("link[role='reload']");
      $("<link/>").attr({ role: 'reload', rel:'stylesheet',href: file, type: 'text/css' }).appendTo(head);
}
//动态加载外部js
function loadJs(file) {
      var head = $("head").remove("script[role='reload']");
      $("<scri" + "pt>" + "</scr" + "ipt>").attr({ role: 'reload', src: file, type: 'text/javascript' }).appendTo(head);
}

function getCRUD(){
var date = new Date();
return date.getYear()+''+date.getMonth()+''+date.getDate()+''+date.getHours()+''+date.getMinutes()+''+date.getSeconds();
}

function mzAlert(msg)
{
  alert(msg);
  return;
}

function jstest2(){
alert('test');
}


function openWindow(sURL,sWinName,iWidth,iHeight,iLeft,iTop){  
  //iWidth,iHeight,iLeft,iTop值大不等于0,方有效
  var width=600;
  var height=300; 
  if(iWidth) width=iWidth; //if(!iWidth):不填,null,'',0 --传进参数
  if(iHeight) height=iHeight;
  //默认按窗口大小居中
  var left=(screen.width-width)/2; 
  var top=(screen.height-height)/2;
  //有值则按其值窗口定位
  if(iLeft) left=iLeft;
  if(iTop) top=iTop;
  var option="toolbar=no,status=yes,scrollbars=yes";
  option+=",width="+width;
  option+=",height="+height;
  option+=",left="+left;
  option+=",top="+top;
  var winName='';
  if(sWinName) winName=sWinName;
  var newWin=window.open(sURL,winName,option); 
  newWin.focus();
  return newWin;
}

function openDialog(sURL,iWidth,iHeight){  
  var option="status:no;center:yes;help:no;border:thin;scroll:yes;";
  option=option+"dialogWidth:"+iWidth+"px;dialogHeight:"+iHeight+"px";
  return window.showModalDialog(sURL,null,option);
}

function focusToEnd(oElement){ 
  var textRange = oElement.createTextRange();
  textRange.moveStart("character", oElement.value.length);
  textRange.collapse(true);
  textRange.select();
}

function isEditRecord(sKey){ 
 //没有选中的记录则返回false,反之返回其值--单选/复选通用
 var oItems=document.getElementsByName(sKey); 
 var n=0; //已选中的记录
 var rtnID="";
 var L=oItems.length;

 for(var i=0;i<L;i++){
   if(oItems[i].checked){
n++;
if(n>1) break;
rtnID=oItems[i].value;
   }
 }

 if(n>1){
   alert('只能选择一条记录!');
   return false;
 }else if(n==1) return rtnID;
  else{
alert('没有选中的记录!');
return false;
  }
}

function isEditRecords(sKey){
  var oItems=document.getElementsByName(sKey);
  var n=0; //已选中的记录
  var L=oItems.length;
  for(var i=0;i<L;i++){
    if(oItems[i].checked) n++;
  }
  if(n==0){
    alert('没有选中记录!');
return false;
  }
  return true;
}

function chooseAll(oKeys,sKey){
  var flag=oKeys.checked;
  var oItems=document.getElementsByName(sKey);
  var L=oItems.length;
  for(var i=0;i<L;i++){
    oItems[i].checked=flag;
  }
}


//若要显示:当前日期加时间(如:2009-06-12 12:00)
function CurentTime()
    {
        var now = new Date();
       
        var year = now.getFullYear(); //年
        var month = now.getMonth() + 1; //月
        var day = now.getDate(); //日
       
        var hh = now.getHours(); //时
        var mm = now.getMinutes(); //分
       
        var clock = year + "-";
       
        if(month < 10)
            clock += "0";
       
        clock += month + "-";
       
        if(day < 10)
            clock += "0";
           
        clock += day + " ";
       
        if(hh < 10)
            clock += "0";
           
        clock += hh + ":";
        if (mm < 10) clock += '0';
        clock += mm;
        return(clock);
    }