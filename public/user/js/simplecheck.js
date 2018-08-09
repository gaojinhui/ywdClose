function $Obj(lsObj){if(typeof(lsObj)=="object")return lsObj;var loObj = document.getElementById(lsObj);if(loObj==null && document.getElementsByName(lsObj)!=null){$.zd.alert('',"请指定" + lsObj + "的ID，并通过ID检查对象!");}else return loObj;}
function $Objs(lsObj){if(typeof(lsObj)=="object")return lsObj;return document.getElementsByName(lsObj);}
function isNullStr(lsValue){
	if(lsValue==null || lsValue=="") return true;
	else return false;
}
function trim(lsValue){
	if(lsValue==null || lsValue=="") return "";
	return lsValue.replace(/(^\s*)|(\s*$)/g, "");
}
function checkRadioEmpty(lsObj,lsLabel){
	var loObjs = document.getElementsByName(lsObj);
	if(loObjs==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	if(typeof(loObjs.length)=="undefined"){if(!loObjs.checked){$.zd.alert('',"请选择"+lsLabel+"！",function(){focusObj(loObjs);});return false;}}
	var allNoCheck = true;
	for(var i=0,n=loObjs.length;i<n;i++){if(loObjs[i].checked){allNoCheck=false;break;}}
	if(allNoCheck){$.zd.alert('',"请选择"+lsLabel+"！",function(){focusObj(loObjs[0]);});return false;}
	return true;
}
function checkEmpty(lsObj,lsLabel,lsTipTitle){
	var title = lsTipTitle || "请输入";
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(lsValue==""){$.zd.alert('',title+lsLabel+"！",function(){focusObj(lsObj);});return false;}
	return true;
}

function checkEmptyMO(lsObj,lsLabel,lsTipTitle){
	var title = lsTipTitle || "请输入";
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(lsValue==""){$.zd.alert('',title+lsLabel+"！",function(){focusObj(lsObj);},'','','mobile');return false;}
	return true;
}

function getSelectedOption(lsObj){	
  	var loSelected = $Obj(lsObj);
  	if(loSelected!=null){
  		var loOptions = loSelected.options;
  		var iSelected = loSelected.selectedIndex;
  		var loOption = loOptions[iSelected];
  		return loOption;
  	}else{
  		return null;
  	}
}
function checkEmptyObj(lsObj,lsLabel){
	var loObj = lsObj;
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(lsValue==""){$.zd.alert('',"请输入"+lsLabel+"！",function(){focusObj(lsObj);});return false;}
	return true;
}
function checkInt(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(isNaN(lsValue)){$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;}
	if(!isNumberNum(lsValue,0)){$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;}
	return true;	
}
function checkNumber(lsObj,lsLabel,dotNum){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(isNaN(lsValue)){$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;}
	if(typeof(dotNum)!="undefined"){if(!isNumberNum(lsValue,dotNum)){$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;}}
	return true;	
}

//身份证校验 数字+字母格式，最多支持18位
function checkIDENTITY_NUM(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	var reg=/^[a-zA-Z0-9]{15,18}$/;
	if(!reg.test(lsValue)){
		$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;
	}else{ 
		return  true;
	} 	
}



function getInt(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return -1;}
	var lsValue = trim(loObj.value);
	if(isNaN(lsValue)){return -1;}
	return parseInt(lsValue,10);
}
function focusObj(lsObj){
	try{if(typeof(lsObj)=="object"){
		lsObj.focus();
		}else{
			var loObj=document.getElementById(lsObj);
			if(loObj!=null)loObj.focus();
		}
	}catch(ee){}		
}
function checkLength(lsObj,lsLabel,maxLen){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = loObj.value;
	if(isNullStr(lsValue)) return true;
	if(lsValue.length>maxLen){$.zd.alert('',"请确保"+lsLabel+"的内容长度不超过"+maxLen+"个字符！",function(){focusObj(lsObj);});return false;}
	return true;
}

function checkLengthPlus(lsObj,lsLabel,maxLen,unit){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = loObj.value;
	if(isNullStr(lsValue)) return true;
	if(lsValue.length>maxLen){$.zd.alert('',"请确保"+lsLabel+"的内容长度不超过"+maxLen+"个"+unit+"！",function(){focusObj(lsObj);});return false;}
	return true;
}

function checkBig(lsObj,lsLabel,lsStand){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = loObj.children[0].value;
	if(isNullStr(lsValue)) return true;
	if(lsValue<=lsStand){$.zd.alert('',"请确保"+lsLabel+"的数据大于"+lsStand+"！",function(){focusObj(lsObj);});return false;}
	return true;
}
function checkIn(lsObj,lsLabel,lsRange){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(lsRange===null || lsRange=="") return true;
	var bWrong = false;
	for(var i=0;i<lsValue.length;i++){
		var iChar = lsValue.charAt(i);
		if(lsRange.indexOf(iChar)<0){
			bWrong = true;
			break;
		}
	}
	if(bWrong){$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;}
	return true;	
}
function checkEmail(lsObj,lsLabel,lsRange){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(lsRange===null || lsRange=="") return true;
	var reg=/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
	if(!reg.test(lsValue)){
		$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;
	}else{ 
		return  true;
	} 
}
function checkDate(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	var reg = /^(\d{4})-(\d{2})-(\d{2})$/;
	if(!reg.test(lsValue)&&RegExp.$2<=12&&RegExp.$3<=31){
		$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;
	}else{ 
		return  true;
	} 
}
function clearValue(lsObj,lsDefaultValue){
	var loObj = $Obj(lsObj);
	if(loObj==null){alert("没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(lsDefaultValue==lsValue){
		loObj.value="";
	}
}
function checkYear(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	if(lsValue.length!=4 || isNaN(lsValue)){$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;}
	return true;
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

function checkMobile(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	var reg=/^\d{11}$/;
	if(!reg.test(lsValue)){
		$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;
	}else{ 
		return  true;
	} 
}

function checkMobileMO(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	var reg=/^\d{11}$/;
	if(!reg.test(lsValue)){
		$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);},'','','mobile');return false;
	}else{ 
		return  true;
	} 
}

function checkMobileAndTel(lsObj,lsLabel){
	var loObj = $Obj(lsObj);
	if(loObj==null){$.zd.alert('',"没有找到对象"+lsObj+"组件！");return false;}
	var lsValue = trim(loObj.value);
	var reg=/^\d{11}$/;
	var regTel=/^\d{3,4}([-]?)+\d{7,8}$/;
	if(!reg.test(lsValue)&&!regTel.test(lsValue)){
		$.zd.alert('',"请确保"+lsLabel+"的输入格式正确！",function(){focusObj(lsObj);});return false;
	}else{ 
		return  true;
	} 
}

function checkInt(lsObj, lsLabel){
	if(isNaN(lsObj.value) || lsObj.value.indexOf(".")>=0 || lsObj.value.indexOf("+")>=0 || lsObj.value.indexOf("-")>=0){
		$.zd.alert('',lsLabel+"只能输入数字！",function(){
		focusObj(lsObj);
		});
	}
}
//判断是否正实数，小数点位数为num 
function checkPlusFloat(obj,str,num){
	var errInfo=0;
	if(obj.value.lastIndexOf("-")>=0) {
		$.zd.alert('',str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
		obj.value="";
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("+")>0) {
		alert(str+"必须输入正数，且小数点后不能超过"+num+"位！");
		obj.value="";
		obj.focus();
		return false;
	}
	if(obj.value=="$"){
		$.zd.alert('',str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
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
			$.zd.alert('',str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
			obj.value="";
			obj.focus();
			});
			return false;
		}else{
			obj.value=number;
		}
	}
	return true;
}

//判断是否正实数，小数点位数为num 
function checkFloat(lsObj,str,num){
	var obj = $Obj(lsObj);
	var errInfo=0;
	if(obj.value.lastIndexOf("-")>=0) {
		$.zd.alert('',str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
		obj.focus();
		});
		return false;
	}
	if(obj.value.lastIndexOf("+")>0) {
		$.zd.alert('',str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
		obj.focus();
		});
		return false;
	}
	if(obj.value=="$"){
		$.zd.alert('',str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
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
			$.zd.alert('',str+"必须输入正数，且小数点后不能超过"+num+"位！",function(){
			obj.focus();
			});
			return false;
		}else{
			obj.value=number;
		}
	}
	return true;
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

//验证“对象”是否为“NULL”
function ifNullObj(objName){
	if(document.getElementById(objName)==null){
		return true;
	}else{
		return false;
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
	return str.replace(/^\s*|\s*$/g,"");
	//return str;//RTrim(LTrim(str));//str.replace(/^\s*|\s*$/g,"");
}
/*
* 显示或者隐藏对应的对象
*/
function toggle_obj(obj){
	$("#"+obj).toggle();
}
/*
*	校验时间格式是否为HHMMSS
*/
function checkTime(sTime){
	var s=sTime.match(/^[0-2]\d[0-5]\d[0-5]\d$/);
	if(s==null){
		$.zd.alert('','时间格式必须是：HHMMSS');
		return false;
	}
	var hh=sTime.substring(0,2);
	var mm=sTime.substring(2,4);
	var ss=sTime.substring(4,6);
	if(hh>23||mm>59||ss>59){
		$.zd.alert('','时间格式必须是：HHMMSS');
		return false;
	}
	return true;
}
/*
* 打开选择框
*/
function openChooseDlg(url,retoUrl,para,width,height){
	var retResult = null;
	if(url.indexOf("?")>=0){
		retResult = showModalDialog(url+"&dlgHeight="+height+"&r="+Math.random()+"&reto="+encodeURIComponent(retoUrl),para,"center:yes;dialogWidth:"+width+";dialogHeight:"+height+";resizable:yes");
		//retResult = showModelessDialog(url+"&dlgHeight="+height+"&reto="+encodeURIComponent(retoUrl),para,"center:yes;dialogWidth:"+width+";dialogHeight:"+height+";resizable:yes");
	}else{
		retResult = showModalDialog(url+"?dlgHeight="+height+"&r="+Math.random()+"&reto="+encodeURIComponent(retoUrl),para,"center:yes;dialogWidth:"+width+";dialogHeight:"+height+";resizable:yes");
		//retResult = showModelessDialog(url+"?dlgHeight="+height+"&reto="+encodeURIComponent(retoUrl),para,"center:yes;dialogWidth:"+width+";dialogHeight:"+height+";resizable:yes");
	}
	return retResult;
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
//获取选择的值
function getCheckedValue(lsObj){
	var loObj=document.getElementsByName(lsObj);
	if(loObj==null){$.zd.alert('',"无法获取对象"+lsObj+"！");return null;}
	var result;
	if(typeof(loObj.length)=="undefined"){
		if(loObj.checked){result=new Array(1);result[0]=loObj.value;}
		else{result=new Array(0);}
	}else{
		var iCount=0;
		for(var i=0,n=loObj.length;i<n;i++){
			if(loObj[i].checked)iCount++;
		}
		result = new Array(iCount);
		var iIdex=0;
		for(var i=0,n=loObj.length;i<n;i++){
			if(loObj[i].checked){
				result[iIdex++] = loObj[i].value;
			}
		}
	}
	return result;
}
function checkNum(str,des){
	if(str.match(/^\-?\d*\.?\d{0,2}$/)==null)
	{
		$.zd.alert('',des+'小数位不能超过2位！');
		return false;
	}
	else if(str/1>9999999999) 
	{
		$.zd.alert('',des+'超出范围值[9999999999]！');
		return false;
	}
	else
	return true;
}
function checkPrice(str)
{
	if(str.match(/^\-?\d*\.?\d{0,2}$/)==null)
	return false;
	else return true;
}

