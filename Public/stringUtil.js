/**   
* 一些常用的javascript函数(方法)   
*   
* 为便于使用，均书写成String对象的方法   
* 把他保存为.js文件，可方便的扩展字符串对象的功能   
*   
* 方法名 功 能   
* ----------- --------------------------------   
* Trim 删除首位空格   
* Occurs 统计指定字符出现的次数   
* isDigit 检查是否由数字组成   
* isAlpha 检查是否由数字字母和下划线组成   
* isNumber 检查是否为数   
* lenb 返回字节数   
* isInChinese 检查是否包含汉字   
* isEmail 简单的email检查   
* isDate 简单的日期检查，成功返回日期对象   
* isInList 检查是否有列表中的字符字符   
* isInList 检查是否有列表中的字符字符   
* isEmpty 是否为空
* getByteLength 获取字节长度
* isInteger 是否整数
* isFloat 是否浮点数
* isDate 是否日期
* isPhone 是否电话号码
*　isIdCardNo 身份证
* isMobilePhone 手机号码
* isPostcode 邮编
*　isEnglish 纯字母
*/

String.prototype.key=function(){  
  return "lee";  
}

String.prototype.trim=function(){  
  return this.replace(/(^\s*)|(\s*$)/g, "");  
}

String.prototype.isEmpty=function(){
  if(this==null || this.trim()=='') return true;
  return false;
}

String.prototype.getByteLength=function(){
  var n = this.length;
  var len = this.length;
  for ( var i=0; i<len; i++ ){
	if ( this.charCodeAt(i) <0 || this.charCodeAt(i) >255 ){
      ++n;
	}
  }
  return n;
}

empty = function(str){
	if(str==undefined) return true;
	if(str=='undefined') return true;
	if(str==null) return true;
	if(str.trim()=='') return true;
	return false;
}

String.prototype.isInteger=function(){
  if(!this.isEmpty() && checkExp(/^\d*$/g,this)) return true;
  return false;
}

String.prototype.isFloat=function(){
  if(!this.isEmpty() && checkExp(/^[+-]?\d+(\.\d+)?$/g,this)) return true;
  return false;
}

String.prototype.isDate=function(){
  var r = this.trim().match(/^(\d{1,4})(-)(\d{1,2})\2(\d{1,2})$/);
  if(r==null)return false; var d = new Date(r[1], r[3]-1, r[4]);
  return (d.getFullYear()==r[1]&&(d.getMonth()+1)==r[3]&&d.getDate()==r[4]);
}

String.prototype.isPhone=function(){
	if (this.trim().search(/^([0]?\d{2,3})?[-]?\d{5,8}([-]\d+)?$/)==-1)
	{
		return false;
	}else{
		return true;
	}
}

String.prototype.isEmail=function(){
	if (this.trim().search(/^[a-zA-Z_0-9]+@[a-zA-Z_0-9.]+$/)==-1)
	{
		return false;
	}else{
		return true;
	}
}

String.prototype.isIdCardNo=function(){
	if (this.trim().search(/^((\d{15})|(\d{17}[0-9xX]))$/)==-1)
	{
		return false;
	}else{
		return true;
	}
}

String.prototype.isCertNo=function(){
	if (this.trim().search(/^[0-9]*$/)==-1)
	{
		return false;
	}else{
		return true;
	}
}

String.prototype.isMobilePhone=function(){
	if (this.trim().search(/^((13|15|18|14|17|)\d{9})$/)==-1)
	{
		return false;
	}else{
		return true;
	}
}

String.prototype.isPostcode=function(){
	if (this.trim().search(/^([1-9]\d{5})$/)==-1)
	{
		return false;
	}else{
		return true;
	}
}

String.prototype.isAccountNo=function(){
	if (this.trim().search(/^[0-9]*$/)==-1)
	{
		return false;
	}else{
		return true;
	}
}

String.prototype.isEnglish=function(){
  if(!this.isEmpty() && (this.getByteLength() > ele.value.length)) return true;
  return false;
}


//正则表达式检测
function checkExp(reg,s){
  return reg.test(s);
}

/*** 统计指定字符出现的次数 ***/    
String.prototype.Occurs = function(ch) {    
// var re = eval("/[^"+ch+"]/g");    
// return this.replace(re, "").length;    
return this.split(ch).length-1;    
}   
  
/*** 检查是否由数字组成 ***/    
String.prototype.isDigit = function() {    
var s = this.Trim();    
return (s.replace(/\d/g, "").length == 0);    
}   
  
/*** 检查是否由数字字母和下划线组成 ***/    
String.prototype.isAlpha = function() {    
return (this.replace(/\w/g, "").length == 0);    
}    
/*** 检查是否为数 ***/    
String.prototype.isNumber = function() {    
var s = this.Trim();    
return (s.search(/^[+-]?[0-9.]*$/) >= 0);    
}   
  
/*** 返回字节数 ***/    
String.prototype.lenb = function() {    
return this.replace(/[^\x00-\xff]/g,"**").length;    
}   
  
/*** 检查是否包含汉字 ***/    
String.prototype.isInChinese = function() {    
return (this.length != this.replace(/[^\x00-\xff]/g,"**").length);    
}   
  
/*** 简单的email检查 ***/    
String.prototype.isEmail = function() {    
　var strr;    
var mail = this;    
　var re = /(\w+@\w+\.\w+)(\.{0,1}\w*)(\.{0,1}\w*)/i;    
　re.exec(mail);    
　if(RegExp.$3!="" && RegExp.$3!="." && RegExp.$2!=".")    
strr = RegExp.$1+RegExp.$2+RegExp.$3;    
　else    
　　if(RegExp.$2!="" && RegExp.$2!=".")    
strr = RegExp.$1+RegExp.$2;    
　　else    
　strr = RegExp.$1;    
　return (strr==mail);    
}   
  
/*** 简单的日期检查，成功返回日期对象 ***/    
String.prototype.isDate = function() {    
var p;    
var re1 = /(\d{4})[年./-](\d{1,2})[月./-](\d{1,2})[日]?$/;    
var re2 = /(\d{1,2})[月./-](\d{1,2})[日./-](\d{2})[年]?$/;    
var re3 = /(\d{1,2})[月./-](\d{1,2})[日./-](\d{4})[年]?$/;    
if(re1.test(this)) {    
p = re1.exec(this);    
return new Date(p[1],p[2],p[3]);    
}    
if(re2.test(this)) {    
p = re2.exec(this);    
return new Date(p[3],p[1],p[2]);    
}    
if(re3.test(this)) {    
p = re3.exec(this);    
return new Date(p[3],p[1],p[2]);    
}    
return false;    
}    
/*** 检查是否有列表中的字符字符 ***/    
String.prototype.isInList = function(list) {    
var re = eval("/["+list+"]/");    
return re.test(this);    
}   