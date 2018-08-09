var menuids=["suckertree1"] //Enter id(s) of SuckerTree UL menus, separated by commas
$(document).ready(function(){//页面加载完后执行的JS
	buildsubmenus();//处理顶部菜单的动态效果
});

//处理顶部菜单的动态效果
function buildsubmenus(){
	try{
		for (var i=0; i<menuids.length; i++){
			var ultags=document.getElementById(menuids[i]).getElementsByTagName("ul")
			for (var t=0; t<ultags.length; t++){
				//ultags[t].parentNode.getElementsByTagName("a")[0].className=""
				ultags[t].parentNode.onmouseover=function(){
					this.getElementsByTagName("ul")[0].style.display="block"
				}
				ultags[t].parentNode.onmouseout=function(){
					this.getElementsByTagName("ul")[0].style.display="none"
				}
			}
		}
	}catch(eee){
	}
}
//处理切换
function setTab(name,cursel,n){
	for(i=1;i<=n;i++){
		var menu=document.getElementById(name+i);/* two1 */
		var con=document.getElementById("con_"+name+"_"+i);/* con_two_1 */
		menu.className=i==cursel?"hover":"";/*三目运算  等号优先*/
		con.style.display=i==cursel?"block":"none";
	}
}
//TAB行的样式处理
function tabRowChange(o,a,b,c,d){
	var t=document.getElementById(o).getElementsByTagName("tr");
	for(var i=0;i<t.length;i++){
		t[i].style.backgroundColor=(t[i].sectionRowIndex%2==0)?a:b;
		t[i].onclick=function(){
			if(this.x!="1"){
				this.x="1";//本来打算直接用背景色判断，FF获取到的背景是RGB值，不好判断
				this.style.backgroundColor=d;
			}else{
				this.x="0";
				this.style.backgroundColor=(this.sectionRowIndex%2==0)?a:b;
			}
		}
		t[i].onmouseover=function(){
			if(this.x!="1")this.style.backgroundColor=c;
		}
		t[i].onmouseout=function(){
			if(this.x!="1")this.style.backgroundColor=(this.sectionRowIndex%2==0)?a:b;
		}
	}
}
/*
* 跳转页面
*/
function gotoPage(flag,pageInput,pageCount){
	var pageIdx = $("#"+pageInput).val();
	if(pageIdx==""){$.zd.alert('',"请输入页码！",function(){$("#"+pageInput).focus();});return;}
	if(isNaN(pageIdx)){$.zd.alert('',"页码输入不正确！",function(){$("#"+pageInput).focus();});return;}
	if(pageIdx<1){$.zd.alert('',"页码输入范围错误，不能小于1！",function(){$("#"+pageInput).focus();});return;}
	if(pageCount>0){
		if(pageIdx>pageCount){$.zd.alert('',"页码输入范围错误，超出总页数"+pageCount+"！",function(){$("#"+pageInput).focus();});return;}
	}else{
		if(pageIdx>pageCount){$.zd.alert('',"页码输入范围错误！",function(){$("#"+pageInput).focus();});return;}
	}
	try{
		turnPage(flag,pageIdx);
	}catch(eee){
		$.zd.alert('',"页面跳转失败："+eee.description);
	}
}
/*
* 跳转页面
*/
function gotoPageBid(flag,pageInput,pageCount){
	var pageIdx = $("#"+pageInput).val();
	if(pageIdx==""){$.zd.alert('',"请输入页码！",function(){$("#"+pageInput).focus();});return;}
	if(isNaN(pageIdx)){$.zd.alert('',"页码输入不正确！",function(){$("#"+pageInput).focus();});return;}
	if(pageIdx<1){$.zd.alert('',"页码输入范围错误，不能小于1！",function(){$("#"+pageInput).focus();});return;}
	if(pageCount>0){
		if(pageIdx>pageCount){$.zd.alert('',"页码输入范围错误，超出总页数"+pageCount+"！",function(){$("#"+pageInput).focus();});return;}
	}else{
		if(pageIdx>pageCount){$.zd.alert('',"页码输入范围错误！",function(){$("#"+pageInput).focus();});return;}
	}
	try{
		turnPageBid(flag,pageIdx);
	}catch(eee){
		$.zd.alert('',"页面跳转失败："+eee.description);
	}
}
/*
* 打开弹出窗口
* url 打开地址
* title 显示标题
* width 窗口宽度
* height 窗口高度
*/
function openShowDlg(url,title,width,height){
	var loPopDiv = new YLPopDiv();
	
	//页面通过定义popdlg变量获得弹出窗的对象
	if(typeof(popdlg)!="undefined"){
		popdlg = loPopDiv;
	}
	
	//处理客户端权限处理增加isDlg参数
	if(url.indexOf("?")!=-1){
		url=url+"&isDlg=Y"
	}else{
		url=url+"?isDlg=Y"
	}
	loPopDiv.url = url;
	loPopDiv.title = title;
	loPopDiv.width = width;
	if(typeof(height)!="undefined")
		loPopDiv.height = height ;
	else
		loPopDiv.height = 500;
	loPopDiv.left = ($(document.body).width()-width)/2 + "px";
	loPopDiv.paddingTop = document.documentElement.scrollTop + "px";
	//loPopDiv.showCloseButton = false;//不显示关闭按钮
	loPopDiv.buttons = eval("[]");
	loPopDiv.popDiv(rootPath);
}
/*
* 打开弹出窗口
* url 打开地址
* title 显示标题
* width 窗口宽度
* height 窗口高度
*/
function openShowDlgNoCloseButton(url,title,width,height){
	var loPopDiv = new YLPopDiv();
	//处理客户端权限处理增加isDlg参数
	if(url.indexOf("?")!=-1){
		url=url+"&isDlg=Y"
	}else{
		url=url+"?isDlg=Y"
	}
	loPopDiv.url = url;
	loPopDiv.title = title;
	loPopDiv.width = width;
	if(typeof(height)!="undefined")
		loPopDiv.height = height;
	else
		loPopDiv.height = 500;
	loPopDiv.left = ($(document.body).width()-width)/2 + "px";
	loPopDiv.paddingTop = document.documentElement.scrollTop + "px";
	loPopDiv.showCloseButton = false;//不显示关闭按钮
	loPopDiv.buttons = eval("[]");
	loPopDiv.popDiv(rootPath);
	return loPopDiv;
}
/**
* 打开无边框，标题，背景 弹出层
* url	地址
*/
function openShowDlgNoTitle(url,width,height){
	var loPopDiv = new YLPopDiv();
	//处理客户端权限处理增加isDlg参数
	if(url.indexOf("?")!=-1){
		url=url+"&isDlg=Y"
	}else{
		url=url+"?isDlg=Y"
	}
	//loPopDiv.url = url;
	//通过异步获取内容
	$.ajax({
		url:url,
		async:false,
		success:function(data){
			loPopDiv.content = data;
			loPopDiv.url="";
		}
	});	
	loPopDiv.width = width;
	loPopDiv.showTitle=false;
	if(typeof(height)!="undefined")
		loPopDiv.height = height;
	else
		loPopDiv.height = 500;
	loPopDiv.left = ($(document.body).width()-width)/2 + "px";
	loPopDiv.paddingTop = document.documentElement.scrollTop+document.body.scrollTop+ "px";
	loPopDiv.showCloseButton = false;//不显示关闭按钮
	loPopDiv.buttons = eval("[]");
	loPopDiv.popDiv(rootPath);
}

/**
* 打开无边框，标题，背景 弹出层
* url	地址
*/
function openShowDlgNoTitleCenter(url,width,height,left,top){
	var loPopDiv = new YLPopDiv();
	//处理客户端权限处理增加isDlg参数
	if(url.indexOf("?")!=-1){
		url=url+"&isDlg=Y"
	}else{
		url=url+"?isDlg=Y"
	}
	//loPopDiv.url = url;
	//通过异步获取内容
	$.ajax({
		url:url,
		async:false,
		success:function(data){
			loPopDiv.content = data;
			loPopDiv.url="";
		}
	});	
	loPopDiv.width = width;
	loPopDiv.showTitle=false;
	if(typeof(height)!="undefined")
		loPopDiv.height = height;
	else
		loPopDiv.height = 500;
	
	loPopDiv.left = ($(window).width()-width)/2 + "px";
	loPopDiv.top = ($(window).height()-height)/2+ "px";
	loPopDiv.showCloseButton = false;//不显示关闭按钮
	loPopDiv.buttons = eval("[]");
	loPopDiv.popDiv(rootPath);
}

/*
* 打开弹出窗口
* url 打开地址
* title 显示标题
* width 窗口宽度
* height 窗口高度
*/
function openDoDlg(url,title,width,height,top){
	var loPopDiv = new YLPopDiv();
	//处理客户端权限处理增加isDlg参数
	if(url.indexOf("?")!=-1){
		url=url+"&isDlg=Y"
	}else{
		url=url+"?isDlg=Y"
	}
	loPopDiv.url = url;
	loPopDiv.title = title;
	loPopDiv.width = width;
	if(typeof(height)!="undefined")
		loPopDiv.height = height ;
	else
		loPopDiv.height = 500;
	loPopDiv.left = ($(document.body).width()-width)/2 + "px";
	if(typeof(top)!="undefined"){
		loPopDiv.top = top + "px";
	}
	loPopDiv.paddingTop = document.documentElement.scrollTop+document.body.scrollTop+ "px";
	loPopDiv.popDiv(rootPath);
}
/*
**刘正 2016年8月18日15:29:05 
**增加按钮的控制 sbutton=false 为不显示确定按钮
*/
function openDoDlg(url,title,width,height,top,sbutton){
	var loPopDiv = new YLPopDiv();
	//处理客户端权限处理增加isDlg参数
	if(url.indexOf("?")!=-1){
		url=url+"&isDlg=Y"
	}else{
		url=url+"?isDlg=Y"
	}
	loPopDiv.url = url;
	loPopDiv.title = title;
	loPopDiv.width = width;
	loPopDiv.buttons = sbutton;
	if(typeof(height)!="undefined")
		loPopDiv.height = height ;
	else
		loPopDiv.height = 500;
	loPopDiv.left = ($(document.body).width()-width)/2 + "px";
	if(typeof(top)!="undefined"){
		loPopDiv.top = top + "px";
	}
	loPopDiv.paddingTop = document.documentElement.scrollTop+document.body.scrollTop+ "px";
	loPopDiv.popDiv(rootPath);
}
function closePopDiv(){
	$("div[id*='ylPop']").each(function(){
		$(this).hide();
	});
	if($.browser.msie &&  $.browser.version>=9){//IE9下延迟删除
		setTimeout('$("div[id*=\'ylPop\']").each(function(){$(this).remove();})',500);
	}else{
		$("div[id*='ylPop']").each(function(){
			$(this).remove();
		});
	}
}

/*
* 插入HTML代码，兼容各浏览器
*/
function standInsertHtml(where,el,html){
	if(el==null || typeof(el)=="undefined")return;
	where = where.toLowerCase();
	if(el.insertAdjacentHTML){
		switch(where){
			case "beforebegin" :
				el.insertAdjacentHTML("BeforeBegin",html);
				return el.previousSibling;
			case "afterbegin" :
				el.insertAdjacentHTML("AfterBegin",html);
				return el.firstChild;
			case "beforeend" :
				el.insertAdjacentHTML("BeforeEnd",html);
				return el.lastChild;
			case "afterend" :
				el.insertAdjacentHTML("AfterEnd",html);
				return el.nextSibling;
		}
		throw "Illegal insert point '" + where + "'";
	}
	var range = el.ownerDocument.createRange();
	var frag;
	switch(where){
		case "beforebegin":
			range.setStartBefore(el);
			frag = range.createContextualFragment(html);
			el.parentNode.insertBefore(frag,el);
			return el.previousSibling;
		case "afterbegin" :
			if(el.firstChild){
				range.setStartBefore(el.firstChild);
				frag = range.createContextualFragment(html);
				el.insertBefore(frag,el.firstChild);
				return el.firstChild;
			}else{
				el.innerHTML = html;
				return el.firstChild;
			}
		case "beforeend" :
			if(el.lastChild){
				range.setStartAfter(el.lastChild);
				frag = range.createContextualFragment(html);
				el.appendChild(frag);
				return el.lastChild;
			}else{
				el.innerHTML = html;
				return el.lastChild;
			}
		case "afterend" :
			range.setStartAfter(el);
			frag = range.createContextualFragment(html);
			el.parentNode.insertBefore(frag,el.nextSibling);
			return el.nextSibling;
	}
	throw "Illegal insert point '" + where + "'";
}
/*
*	弹出消息提示框
* 使用例子 showPopMsg("提醒","我的提醒内容，谁dnf拉就水电费水电费快乐就","");
*
* title 消息标题
* content 内容
* url 连接地址
* width 宽度
* height 高度
* callBackFunction 回调方法
*/
function showPopMsg(title,content,url,width,height,callBackFunction){
	var popMsg = new YLPopDiv();
	popMsg.title = title;
	popMsg.content = content;
	popMsg.url = url;
	if(typeof(width)!="undefined"){
		popMsg.width = width;
	}else{
		popMsg.width = "400";
	}
	if(typeof(height)!="undefined"){
		popMsg.height = height;
	}else{
		popMsg.height = "200";
	}
	popMsg.showTime = 0;
	if(typeof(callBackFunction)!="undefined"){
		popMsg.callBackFunction = callBackFunction;
		
	}
	//popMsg.buttons = eval('[{name:"okHvButton",value:"不再提醒",onclick:,target:"this"}]');
	popMsg.okBtnTitle = "不再提醒";
	popMsg.showCloseButton = false;//不显示关闭按钮
	
	popMsg.showMsg(rootPath);
	return popMsg;
}

/** 日期的加减
	srcDate：源日期
	separator:分隔符
	type：加/减（1：加 0：减）
	days：加/减的天数
	验证通过返回""；不通过返回错误消息
**/
function addOrMinusDate(srcDate, separator, type, days){
	if(!srcDate){
		$.zd.alert('',"日期不能为空");
		return;
	}
	if(!separator){
		separator = "-"; //默认"-"
	}
	if(!type){
		type = "1"; //默认为加
	}
	if(!days){
		return srcDate;
	}
	var srcDates = srcDate.split(separator);
	srcDate = new Date(srcDates[0], srcDates[1]-1, srcDates[2]);
	var date;
	if(type == "1"){
		date = srcDate.getTime()+days*24*60*60*1000;
	}else{
		date = srcDate.getTime()-days*24*60*60*1000;
	}
	var nowDate = new Date();
	nowDate.setTime(date);
	var month = nowDate.getMonth()+1;
	var day = nowDate.getDate();
	return nowDate.getFullYear()+separator+(month<10?("0"+month):month)+separator+(day<10?("0"+day):day);
}

//临时存放，用于日期选择
function selectDate(obj){
	if(!$(".quick_link_dat a[id='"+obj.id+"']").hasClass("active")){
		$(".quick_link_dat a").each(
			function(){
				if($(this).hasClass("active")){
					$(this).removeClass("active");
				}
			}
		);
		$(".quick_link_dat a[id='"+obj.id+"']").addClass("active");
		if(obj.id=="all"){
			$("#BEGINWESTDATE").val("");
			$("#ENDWESTDATE").val(addOrMinusDate($("#INITDATE").val(),"-","0","7"));
		}else if(obj.id=="0"){
			$("#ENDWESTDATE").val($("#INITDATE").val());
			$("#BEGINWESTDATE").val($("#INITDATE").val());
		}else{
			$("#ENDWESTDATE").val($("#INITDATE").val());
			$("#BEGINWESTDATE").val(addOrMinusDate($("#INITDATE").val(),"-","0",obj.id));
		}
	}
}


/**
 * 下一页 调用页面工具类下一页
 * @nextPage
 */
function nextPage() {
	$(".clk-p-a:last").click() ;
}

/**
 * 上一页 调用页面工具类上一页
 * @prePage
 */
function prePage() {
	$(".clk-p-a:first").click();
}

/**
 * 招投标
 * 下一页 调用页面工具类下一页
 * @nextPage
 */
function nextPageBid() {
	$(".clk-bid:last").click() ;
}

/**
 * 招投标
 * 上一页 调用页面工具类上一页
 * @prePage
 */
function prePageBid() {
	$(".clk-bid:first").click();
}

//设置为一个月默认查询条件
function setDateSelectOneMonth(nowDate,START_ADD_DATE,END_ADD_DATE){
	//设置日期 默认
	var befDate= addOrMinusDate(nowDate,"-","0",30);//30天默认
	document.getElementById(START_ADD_DATE).value=befDate;
	document.getElementById(END_ADD_DATE).value=nowDate;

}

//搜索框数字类型校验
function validNumber(obj){
	var value=$(obj).val();
	if(!$.isNumeric(value)&&""!=value){
		value=parseFloat(value);
		if($.isNumeric(value)){
			$(obj).val(value);
		}else{
			$(obj).val("");				
		}
		$(obj).focus();
	} 
}

