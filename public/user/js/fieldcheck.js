//////////////////////////////////////////////////////
//													//
//		project name字段验证       					//
//		by lhf 2012-01-20		   				    //
//		file name :fieldcheck.js					//
//		description:         					    //
//													//
//////////////////////////////////////////////////////

//判断当前字符串中的所有字符在指定的字符串中
function IsRightStr(lsStr1,lsStr2){
	if(lsStr1==null || lsStr2==null) return 0;
	var IsRight=1;
	var ch;
	for(i=0;i < lsStr1.length;i++){
		ch=lsStr1.charAt(i);
		if(lsStr2.indexOf(ch)==-1){
			IsRight=0;
			break;
		}
	}
	return IsRight;
}
//判断是否为正数字
function IsJustNum(Str){
	if(IsRightStr(Str,"0123456789.+")==0) return 0;
	if(Str.indexOf("-")>0) return 0;
	if(Str.indexOf("+")>0) return 0;
	if(Str.charAt(Str.length-1)==".") return 0;
	if(Str.lastIndexOf(".")!=Str.indexOf(".")) return 0;
	return 1;
}
//判断是否为数字范围
function IsNumRange(Str){
	if(IsRightStr(Str,"0123456789.-+")==0) return 0;
	var cTemp = Str.charAt(Str.length-1);
	if(cTemp<'0' || cTemp>'9') return 0;
	if(Str.lastIndexOf("-")!=Str.indexOf("-")) return 0;
	return 1;
}
//判断是否为整数范围
function IsIntNumRange(Str){
	if(IsRightStr(Str,"0123456789-+")==0) return 0;
	var cTemp = Str.charAt(Str.length-1);
	if(cTemp<'0' || cTemp>'9') return 0;
	if(Str.lastIndexOf("-")!=Str.indexOf("-")) return 0;
	return 1;
}
//判断是否为正整数范围
function IsJustIntNumRange(Str){
	if(IsRightStr(Str,"0123456789")==0) return 0;
	var cTemp = Str.charAt(Str.length-1);
	if(cTemp<'0' || cTemp>'9') return 0;
	if(Str.lastIndexOf("-")!=Str.indexOf("-")) return 0;
	return 1;
}
//检查是否为正实数
function isNumber(strInteger){
	var newPar=/^[0-9]+(.[0-9]{1,2})?$/;
	return newPar.test(strInteger); 
}
/**
*/
function getValue(obj){
	var tmp="";
	tmp=obj;
	if(tmp==null){
		return "$";
	}
	if(tmp==undefined){
		return "$";
	}
	if(tmp==""){
		return "$";
	}
	if(toTrim(tmp)==""){
		return "$";
	}
	return tmp;
}
//判断是否为数字、字母、下滑线
function IsNumDownLineChars(str){
	var reg=/[^A-Za-z0-9_]/g 
	if (reg.test(str)){
		return (false);
	}else{
		return(true);
	}
}
//去左空格
function LTrim(str){
	return str.replace(/^\s*/g,"");
} 
//去右空格
function RTrim(str){
	return str.replace(/\s*$/g,"");
}
//ȥ��去首尾空格
function toTrim(str){
	return str;//RTrim(LTrim(str));//str.replace(/^\s*|\s*$/g,"");
}
/**
* 判断是否为正实数
* obj 判断对象，str：对象描述，langes：语言类型（默认空，为中文）
*/
function checkJustNumber(obj,str,num,langes){
	if(getValue(num)=="$"){
		if(obj.value=="$"){
	    	$.zd.alert('',"输入错误，"+str+"必须输入正数！",function(){
				obj.value="";
				obj.focus();
	    	});
			return false;
		}
		if(getValue(obj.value)=="$"){
		}else if(IsJustNum(obj.value)==0){
			$.zd.alert('',"输入错误，"+str+"必须输入正数！",function(){
				obj.focus();
			});
			return false;
		}
	}else{
		if(obj.value=="$"){
	    	$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
				obj.value="";
				obj.focus();
	    	});
			return false;
		}
		if(getValue(obj.value)=="$"){
		}else if(!isNumberNum(obj.value,num)){
			$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
				obj.focus();
			});
			return false;
		}
	}
}
/**
* 检查是否为正实数
* strInteger数字字符串，num位数
*/
function isNumberNum(strInteger,num){
	var newPar="";
	if(parseInt(num)==1){
		newPar=/^[0-9]+(.[0-9]{1,1})?$/;	
	}else if(parseInt(num)==2){
		newPar=/^[0-9]+(.[0-9]{1,2})?$/;
	}else if(parseInt(num)==3){
		newPar=/^[0-9]+(.[0-9]{1,3})?$/;
	}else if(parseInt(num)==4){
		newPar=/^[0-9]+(.[0-9]{1,4})?$/;
	}else if(parseInt(num)==5){
		newPar=/^[0-9]+(.[0-9]{1,5})?$/;
	}else if(parseInt(num)==6){
		newPar=/^[0-9]+(.[0-9]{1,6})?$/;
	}else if(parseInt(num)==7){
		newPar=/^[0-9]+(.[0-9]{1,7})?$/;
	}else if(parseInt(num)==8){
		newPar=/^[0-9]+(.[0-9]{1,8})?$/;
	}else{
		newPar=/^[0-9]+(.[0-9]{1,2})?$/;
	}
	return newPar.test(strInteger); 
}
//判断是否为两位小数点的实数数字
function checkFloat(obj){
	if(obj.value.lastIndexOf("-")!=obj.value.indexOf("-")) {
		$.zd.alert('','输入错误，必须输入正数，且小数点后不能超过2位！',function(){
			obj.value="";
			obj.focus();
		});
		return false;
	}else if(obj.value.lastIndexOf("-")>0){
		$.zd.alert('','输入错误，必须输入正数，且小数点后不能超过2位！',function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("+")!=obj.value.indexOf("+")){
		$.zd.alert('','输入错误，必须输入正数，且小数点后不能超过2位！',function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}else if(obj.value.lastIndexOf("+")>0){
		$.zd.alert('','输入错误，必须输入正数，且小数点后不能超过2位！',function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value=="$"){
		$.zd.alert('','输入错误，必须输入正数，且小数点后不能超过2位！',function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(getValue(obj.value)=="$"){
	}else if(!isNumber(obj.value)){
		$.zd.alert('','输入错误，必须输入正数，且小数点后不能超过2位！',function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
}
//判断是否为两位小数点的实数数字
function checkRealNumFloat(obj,str){
	if(obj.value.lastIndexOf("-")!=obj.value.indexOf("-")) {
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过2位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("-")>0) {
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过2位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("+")!=obj.value.indexOf("+")){
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过2位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value=="$"){
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过2位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(getValue(obj.value)=="$"){
	}else {
		var number=obj.value;
		if(number.substr(0,1)=="+"||number.substr(0,1)=="-"){
			number=number.substr(1);
		}
		if(!isNumber(number)){
			$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过2位！",function(){
			obj.value="";
			obj.focus();
			});
			return false;
		}
	}
}
function checkAbcNumber(obj,str){
	if(getValue(obj.value)=="$"){
	}else {
		if(!IsNumAndAbcChars(obj.value)){
			$.zd.alert('',"输入错误，"+str+"只能输入数字或字母！",function(){
			obj.value="";
			obj.focus();
			});
			return false;
		}
	}
}
/**
*判断是否为数字、字母
*/
function IsNumAndAbcChars(str){
	var reg=/[^A-Za-z0-9]/g;
	if (reg.test(str)){
		return (false);
	}else{
		return(true);
	}
}
//判断是否正实数，小数点位数为num 
function checkPlusFloat(obj,str,num){
	var errInfo=0;
	if(obj.value=='0') {
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且不能等于0！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("-")>=0) {
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("+")>0) {
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value=="$"){
		$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(getValue(obj.value)=="$"){
	}else {
		var number=obj.value;
		if(number.substr(0,1)=="+"||number.substr(0,1)=="-"){
			number=number.substr(1);
		}
		if(!isNumberNum(number,num)){
			$.zd.alert('',"输入错误，"+str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
			obj.value="";
			obj.focus();
			});
			return false;
		}else{
			obj.value=number;
		}
	}
}
//判断是否为整数
function checkIntegerInfo(obj,str){
	if(obj.value=="$"){
		$.zd.alert('',"输入错误，"+str+"必须输入整数！",function(){
		//obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("+")>0){
		$.zd.alert('',"输入错误，"+str+"必须输入整数！",function(){
		//obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("-")>0){
		$.zd.alert('',"输入错误，"+str+"必须输入整数！",function(){
		//obj.value="";
		obj.focus();
		});
		return false;
	}
	if(getValue(obj.value)=="$"){
	}else if(IsIntNumRange(obj.value)!=1){
		$.zd.alert('',"输入错误，"+str+"必须输入整数！",function(){
		//obj.value="";
		obj.focus();
		});
		return false;
	}
}
//判断是否为正整数
function checkJustIntegerInfo(obj,str){
	if(obj.value=="$"){
	    $.zd.alert('',"输入错误，"+str+"必须输入正整数！",function(){
		obj.value="";
		obj.focus();
	    });
		return false;
	}
	if(obj.value.lastIndexOf("+")!=obj.value.indexOf("+")){
		$.zd.alert('',"输入错误，"+str+"必须输入正整数！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(getValue(obj.value)=="$"){
	}else{
		var number=obj.value;
		if(number.substr(0,1)=="+"){
			number=number.substr(1);
		}
	 	if(IsJustIntNumRange(number)!=1){
			$.zd.alert('',"输入错误，"+str+"必须输入正整数！",function(){
			//obj.value="";
			obj.focus();
			});
			return false;
		}else{
			obj.value=number;
		}
	}
}
//判断是否为正整数
function checkJustIntegerNum(obj,str,num){
	if(obj.value=="$"){
	    $.zd.alert('',"输入错误，"+str+"必须输入"+num+"位正整数！",function(){
		obj.value="";
		obj.focus();
	    });
		return false;
	}
	if(obj.value.lastIndexOf("+")!=obj.value.indexOf("+")){
		$.zd.alert('',"输入错误，"+str+"必须输入"+num+"位正整数！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(getValue(obj.value)=="$"){
	}else{
		var number=obj.value;
		if(number.substr(0,1)=="+"){
			number=number.substr(1);
		}
	 	if(IsJustIntNumRange(number)!=1){
			$.zd.alert('',"输入错误，"+str+"必须输入"+num+"位正整数！",function(){
			//obj.value="";
			obj.focus();
			});
			return false;
		}else{
			obj.value=number;
			if(number.length!=4){
				$.zd.alert('',"输入错误，"+str+"必须输入"+num+"位正整数！",function(){
				obj.focus();
				});
				return false;
			}
		}
	}
}
function checkNumber(obj){
	if(obj.value=="$"){
		$.zd.alert('','输入错误，必须输入数字！',function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(getValue(obj.value)=="$"){
	//obj.value="";
	}else if(IsNumRange(obj.value)!=1){
		$.zd.alert('','输入错误，必须输入数字！',function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
}
/**
* 设置所有的按钮状态：置灰
* button、submit、reset
*/
function setAllButtonAsh() {
	var obj;
	obj = document.getElementsByTagName("INPUT");
	var i;
	for (i = obj.length - 1; i >= 0; i--) {
		if(obj[i].type=="button"||obj[i].type=="reset"||obj[i].type=="submit"){
			obj[i].disabled="disabled";
		}
	}
}
/**
* 判断日期格式YYYY-MM
* obj 判断对象，str提示信息
*/
function checkYYYYMM(obj,str){
	if(getValue(obj.value)=="$"){
	}
	if(!(obj.value.match(/^\s+$/))
    	&&obj.value!=''
        &&!(obj.value.match(/^(\d{4})(-)((0[1-9])|(1[0-2]))$/))
        &&!(obj.value.match(/^(\d{4})((0[1-9])|(1[0-2]))$/))){
        $.zd.alert('',"输入错误，"+str+"格式必须是YYYY-MM",function(){
        obj.focus();
        });
		return false;
	}else if (obj.value.match(/^(\d{4})((0[1-9])|(1[0-2]))$/)){
		obj.value=obj.value.substr(0,4)+'-'+obj.value.substr(4,2);
	}
}
/**
* 选中对象值
* obj 对象
*/
function choiceAllValue(obj){
	obj.select();
}
//全选 选中多选框
function checkAll(elementName) {
	var a = document.getElementsByName(elementName);
	for (var i = 0; i < a.length; i++) {
		if(a[i].disabled){
		}else{
			if (!a[i].checked) {
            	a[i].checked = true;
			}
		}
	}
}
//全不选 选中多选框
function cancelAll(elementName) {
	var a = document.getElementsByName(elementName);
	for (var i = 0; i < a.length; i++) {
		if (a[i].checked) {
			a[i].checked = false;
		}
	}
}
//验证“值”是否为“NULL”
function ifNullValue(objValue){
	if(objValue==null){
		return true;
	}else if(objValue==undefined){
		return true;
	}else if(objValue==""){
		return true;
	}else if(objValue=="null"){
		return true;
	}else if(toTrim(objValue)==""){
		return true;
	}
	return false;
}
//验证“值”是否为“NULL”
function ifNullObjName(objName){
	if(objName==null){
		return true;
	}else if(objName==undefined){
		return true;
	}
	return false;
}
//获取字符串
function getObjValue(objValue){
	if(objValue==null){
		return "";
	}else if(objValue==undefined){
		return "";
	}else if(objValue==""){
		return "";
	}else if(objValue=="null"){
		return "";
	}
	return objValue;
}
//验证“对象”是否为“NULL”
function ifNullObj(objName){
	if(document.getElementById(objName)==null){
		return true;
	}else{
		return false;
	}
}
//验证“字段”对应值是否为“NULL”
function ifNullObjValue(objName){
	if(ifNullObj(objName)){
		return true;
	}else{
		if(ifNullValue(document.getElementById(objName).value)){
			return true;
		}else{
			return false;
		}
	}
}
/**
* 获取对象
* objName对象名
*/
function getObject(objName){
	return document.getElementById(objName);
}
/**
* 获取数据
* objName对象名
*/
function getObjectValue(objName){
	if(ifNullObj(objName)){//为NULL
		return "";
	}else{
		return document.getElementById(objName).value;
	}
}
/**
* 获取下拉框显示数据
* objName对象名
*/
function getSelectObjectValue(objName){
	if(ifNullObj(objName)){//为NULL
		return "";
	}else{
		if(getObject(objName).selectedIndex<0){
			return "";
		}else{
			return getObject(objName)[getObject(objName).selectedIndex].innerText;
		}
	}
}
/**
* 设置焦点
* objName对象名
*/
function setObjectFocus(objName){
	if(ifNullObj(objName)){//为NULL
	}else{
		document.getElementById(objName).focus();
	}
}
/**
* 设置输入框数据
* objName对象名
*/
function setObjectValue(objName,objValue){
	if(ifNullObj(objName)){//为NULL
	}else if(objValue!="0"&&ifNullValue(objValue)){
		document.getElementById(objName).value="";
	}else{
		document.getElementById(objName).value=objValue;
	}
}
/**
* 设置数据
* objName对象名
*/
function setObjectInnerValue(objName,objValue){
	if(ifNullObj(objName)){//为NULL
	}else if(ifNullValue(objValue)){
		document.getElementById(objName).innerHTML="";
	}else{
		document.getElementById(objName).innerHTML=objValue;
	}
}
/**
* 获取数据
* objName对象名
*/
function getObjectInnerValue(objName,objValue){
	if(ifNullObj(objName)){//为NULL
		return "";
	}else{
		return document.getElementById(objName).innerHTML;
	}
}
/**
* 显示数据
* objName对象名
*/
function showObjectDisplay(objName){
	if(ifNullObj(objName)){//为NULL
	}else{
		document.getElementById(objName).style.display="block";
	}
}
/**
* 隐藏数据
* objName对象名
*/
function hiddenObjectDisplay(objName){
	if(ifNullObj(objName)){//为NULL
	}else{
		document.getElementById(objName).style.display="none";
	}
}
/**
* 设置按钮置灰
* objName对象名
*/
function setButtonDisabled(objName){
	if(ifNullObj(objName)){//为NULL
	}else{
		document.getElementById(objName).disabled="disabled";
	}
}
/**
* 设置按钮 不置灰
* objName对象名
*/
function setButtonAbled(objName){
	if(ifNullObj(objName)){//为NULL
	}else{
		document.getElementById(objName).disabled="";
	}
}
function checkBooleanInfo(obj,str){
	if(ifNullValue(obj.value)){
	}else if(obj.value!="0"&&obj.value!="1"){
		$.zd.alert('',"输入错误，"+str+"只能输入1或0！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
}
/* 定义字符串类 功能类似与 java 中的 StringBuffer 类 */
function StringBuffer(){
	this._strings_ = new Array();
}
/*增加字符串*/
StringBuffer.prototype.append = function(str){            
	this._strings_.push(str);
};
/*返回所增加的字符串*/
StringBuffer.prototype.toString = function(){
	return this._strings_.join("");
};