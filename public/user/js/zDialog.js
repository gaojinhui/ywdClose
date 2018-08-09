function getRootPath(){   
 var curWwwPath=window.document.location.href; //获取当前网址，如： http://localhost:8083/uimcardprj/share/meun.jsp        
  var pathName=window.document.location.pathname;     //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp   
  var pos=curWwwPath.indexOf(pathName);    
  var localhostPaht=curWwwPath.substring(0,pos);  //获取主机地址，如： http://localhost:8083        
  var projectName=pathName.substring(0,pathName.substr(1).indexOf('/')+1);    //获取带"/"的项目名，如：/uimcardprj    
  return(localhostPaht+projectName);
}
var IMAGESPATH = rootPath+'/plugins/core/stand/js/dialog/images/';//图片路径配置
//var IMAGESPATH = 'images/'; //图片路径配置
//var IMAGESPATH = 'http://www.5-studio.com/wp-content/uploads/2009/11/'; //图片路径配置
var HideScrollbar=false;//弹出Dialog时是否隐藏滚动条
/*************************一些公用方法和属性****************************/
var agt =   window.navigator.userAgent;
var isIE = agt.toLowerCase().indexOf("msie") != -1;
var isGecko = agt.toLowerCase().indexOf("gecko") != -1;
var ieVer = isIE ? parseInt(agt.split(";")[1].replace(/(^\s*)|(\s*$)/g,"").split(" ")[1]) : 0;
var isIE8 = !!window.XDomainRequest && !!document.documentMode;
var isIE7 = ieVer==7 && !isIE8;
var ielt7 = isIE && ieVer<7;

if(isIE)
	try{ document.execCommand('BackgroundImageCache',false,true); }catch(e){}

var $id = function (id) {
    return typeof id == "string" ? document.getElementById(id) : id;
};
//if (!window.$) var $ = $id;
function stopEvent(evt){//阻止一切事件执行,包括浏览器默认的事件
	evt = window.event||evt;
	if(!evt){
		return;
	}
	if(isGecko){
		evt.preventDefault();
		evt.stopPropagation();
	}
	evt.cancelBubble = true
	evt.returnValue = false;
}

Array.prototype.remove = function (s, dust) { //如果dust为ture，则返回被删除的元素
    if (dust) {
        var dustArr = [];
        for (var i = 0; i < this.length; i++) {
            if (s == this[i]) {
                dustArr.push(this.splice(i, 1)[0]);
            }
        }
        return dustArr;
    }
    for (var i = 0; i < this.length; i++) {
        if (s == this[i]) {
            this.splice(i, 1);
        }
    }
    return this;
}
if(!isIE&&HTMLElement){
	if(!HTMLElement.prototype.attachEvent){
		window.attachEvent = document.attachEvent = HTMLElement.prototype.attachEvent = function(evtName,func){
			evtName = evtName.substring(2);
			this.addEventListener(evtName,func,false);
		}
		window.detachEvent = document.detachEvent = HTMLElement.prototype.detachEvent = function(evtName,func){
			evtName = evtName.substring(2);
			this.removeEventListener(evtName,func,false);
		}
	}
}
var $topWindow = function () {
    var parentWin = window;
    while (parentWin != parentWin.parent) {
        if (parentWin.parent.document.getElementsByTagName("FRAMESET").length > 0) break;
        parentWin = parentWin.parent;
    }
    return parentWin;
};
var $bodyDimensions = function (win) {
    win = win || window;
    var doc = win.document;
    var cw = doc.compatMode == "BackCompat" ? doc.body.clientWidth : doc.documentElement.clientWidth;
    var ch = doc.compatMode == "BackCompat" ? doc.body.clientHeight : doc.documentElement.clientHeight;
    var sl = Math.max(doc.documentElement.scrollLeft, doc.body.scrollLeft);
    var st = Math.max(doc.documentElement.scrollTop, doc.body.scrollTop); //考虑滚动的情况
    var sw = Math.max(doc.documentElement.scrollWidth, doc.body.scrollWidth);
    var sh = Math.max(doc.documentElement.scrollHeight, doc.body.scrollHeight); //考虑滚动的情况
	if(sh<ch)
		sh=ch; //IE下在页面内容很少时存在scrollHeight<clientHeight的情况
    return {
        "clientWidth": cw,
        "clientHeight": ch,
        "scrollLeft": sl,
        "scrollTop": st,
        "scrollWidth": sw,
        "scrollHeight": sh
    }
}

var fadeEffect = function(element, start, end, speed, callback){//透明度渐变：start:开始透明度 0-100；end:结束透明度 0-100；speed:速度1-100
	if(!element.effect)
		element.effect = {fade:0, move:0, size:0};
	clearInterval(element.effect.fade);
	var speed=speed||20;
	element.effect.fade = setInterval(function(){
		start = start < end ? Math.min(start + speed, end) : Math.max(start - speed, end);
		element.style.opacity =  start / 100;
		element.style.filter = "alpha(opacity=" + start + ")";
		if(start == end){
			clearInterval(element.effect.fade);
			if(callback)
				callback.call(element);
		}
	}, 20);
};

/*************************弹出框类实现****************************/
var topWin = $topWindow();
var topDoc = topWin.document;
var Dialog = function () {
    /****以下属性以大写开始，可以在调用show()方法前设置值****/
    this.ID = null;
    this.Width = null;
    this.Height = null;
    this.URL = null;
	this.OnLoad=null;
    this.InnerHtml = ""
    this.InvokeElementId = ""
    this.Top = "50%";
    this.Left = "50%";
    this.Title = "　";
    this.OKEvent = null; //点击确定后调用的方法
    this.CancelEvent = null; //点击取消及关闭后调用的方法
    this.ShowButtonRow = false;
    this.MessageIcon = "window.gif";
    this.MessageTitle = "";
    this.Message = "";
    this.ShowMessageRow = false;
    this.Modal = true;
    this.Drag = true;
    this.AutoClose = null;
    this.ShowCloseButton = true;
	this.Animator = true;
	this.MsgForESC = "";
    this.CancelButtonText = "取 消";
    this.OkButtonText = "确 定";
    /****以下属性以小写开始，不要自行改变****/
    this.dialogDiv = null;
	this.bgDiv=null;
    this.opener = null;
    this.innerFrame = null;
    this.innerWin = null;
    this.innerDoc = null;
    this.zindex = 900;
    this.cancelButton = null;
    this.okButton = null;

    if (arguments.length > 0 && typeof(arguments[0]) == "string") { //兼容旧写法
        this.ID = arguments[0];
    } else if (arguments.length > 0 && typeof(arguments[0]) == "object") {
        Dialog.setOptions(this, arguments[0])
    }
	if(!this.ID)
        this.ID = topWin.Dialog._Array.length + "";

};
Dialog._Array = [];
Dialog.bgDiv = null;
Dialog.setOptions = function (obj, optionsObj) {
    if (!optionsObj) return;
    for (var optionName in optionsObj) {
        obj[optionName] = optionsObj[optionName];
    }
};
Dialog.attachBehaviors = function () {
	document.attachEvent("onkeydown", Dialog.onKeyDown);
	window.attachEvent('onresize', Dialog.resetPosition);
	if(!HideScrollbar&&ielt7)
		window.attachEvent("onscroll", Dialog.resetPosition);
};
Dialog.prototype.attachBehaviors = function () {
	var self = this;
    if (this.Drag && topWin.Drag){//注册拖拽方法
		var dragHandle=topWin.$id("_Draghandle_" + this.ID),dragBody=topWin.$id("_DialogDiv_" + this.ID);
		topWin.Drag.init(dragHandle, dragBody);
		dragBody.onDragStart = function (left,top,mouseX,mouseY) {
			if (!isIE && self.URL) { //非ie浏览器下在拖拽时用一个层遮住iframe，以免光标移入iframe失去拖拽响应
				topWin.$id("_Covering_" + self.ID).style.display = ""
			}
		}
		dragBody.onDragEnd = function(left,top,mouseX,mouseY){
			if (!isIE && self.URL) {
				topWin.$id("_Covering_" + self.ID).style.display = "none"
			}
			var bd = $bodyDimensions(topWin);
			if(left<0)
				this.style.left='0px';
			if(left+this.clientWidth>bd.clientWidth)
				this.style.left=bd.clientWidth-this.clientWidth+'px';
			if(ielt7){
				if(top<bd.scrollTop)
					this.style.top=bd.scrollTop+'px';
				if(top+33>bd.scrollTop+bd.clientHeight)
					this.style.top=bd.scrollTop+bd.clientHeight-33+'px';
			}else{
				if(top<0)
					this.style.top='0px';
				if(top+33>bd.clientHeight)
					this.style.top=bd.clientHeight-33+'px';
			}
		}
	}
};
Dialog.prototype.displacePath = function () {
	if(this.URL.indexOf("https")>=0){
		if (this.URL.substr(0, 8) == "https://" || this.URL.substr(0, 1) == "/" || this.URL.substr(0, 11) == "javascript:") {
	        return this.URL;
	    } else {
	        var thisPath = this.URL;
	        var locationPath = window.location.href;
	        locationPath = locationPath.substring(0, locationPath.lastIndexOf('/'));
	        while (thisPath.indexOf('../') >= 0) {
	            thisPath = thisPath.substring(3);
	            locationPath = locationPath.substring(0, locationPath.lastIndexOf('/'));
	        }
	        return locationPath + '/' + thisPath;
	    }
	}else{
		if (this.URL.substr(0, 7) == "http://" || this.URL.substr(0, 1) == "/" || this.URL.substr(0, 11) == "javascript:") {
	        return this.URL;
	    } else {
	        var thisPath = this.URL;
	        var locationPath = window.location.href;
	        locationPath = locationPath.substring(0, locationPath.lastIndexOf('/'));
	        while (thisPath.indexOf('../') >= 0) {
	            thisPath = thisPath.substring(3);
	            locationPath = locationPath.substring(0, locationPath.lastIndexOf('/'));
	        }
	        return locationPath + '/' + thisPath;
	    }
	}
};
Dialog.prototype.setPosition = function () {
    var bd = $bodyDimensions(topWin);
    var thisTop = this.Top,
        thisLeft = this.Left,
		thisdialogDiv=this.getDialogDiv();
    if (typeof this.Top == "string" && this.Top.substring(this.Top.length - 1, this.Top.length) == "%") {
        var percentT = this.Top.substring(0, this.Top.length - 1) * 0.01;
			thisTop =ielt7?bd.clientHeight * percentT - thisdialogDiv.scrollHeight * percentT + bd.scrollTop:bd.clientHeight * percentT - thisdialogDiv.scrollHeight * percentT;
    }
    if (typeof this.Left == "string" && this.Left.substring(this.Left.length - 1, this.Left.length) == "%") {
        var percentL = this.Left.substring(0, this.Left.length - 1) * 0.01;
        thisLeft = ielt7?bd.clientWidth * percentL - thisdialogDiv.scrollWidth * percentL + bd.scrollLeft:bd.clientWidth * percentL - thisdialogDiv.scrollWidth * percentL;
    }
    thisdialogDiv.style.top = Math.round(thisTop) + "px";
    thisdialogDiv.style.left = Math.round(thisLeft) + "px";
};
Dialog.setBgDivSize = function () {
    var bd = $bodyDimensions(topWin);
	if(Dialog.bgDiv){
			if(ielt7){
				Dialog.bgDiv.style.height = bd.clientHeight + "px";
				Dialog.bgDiv.style.top=bd.scrollTop + "px";
				Dialog.bgDiv.childNodes[0].style.display = "none";//让div重渲染,修正IE6下尺寸bug
				Dialog.bgDiv.childNodes[0].style.display = "";
			}else{
				Dialog.bgDiv.style.height = bd.scrollHeight + "px";
			}
		}
};
Dialog.resetPosition = function () {
    Dialog.setBgDivSize();
    for (var i = 0, len = topWin.Dialog._Array.length; i < len; i++) {
        topWin.Dialog._Array[i].setPosition();
    }
};
Dialog.prototype.create = function () {
    var bd = $bodyDimensions(topWin);
    if (typeof(this.OKEvent)== "function") this.ShowButtonRow = true;
    if (!this.Width) this.Width = Math.round(bd.clientWidth * 4 / 10);
    if (!this.Height) this.Height = Math.round(this.Width / 2);
    if (this.MessageTitle || this.Message) this.ShowMessageRow = true;
    var DialogDivWidth = this.Width + 13 + 13;
    var DialogDivHeight = this.Height + 33 + 13 + (this.ShowButtonRow ? 40 : 0) + (this.ShowMessageRow ? 50 : 0);

    if (DialogDivWidth > bd.clientWidth) this.Width = Math.round(bd.clientWidth - 26);
    if (DialogDivHeight > bd.clientHeight) this.Height = Math.round(bd.clientHeight - 46 - (this.ShowButtonRow ? 40 : 0) - (this.ShowMessageRow ? 50 : 0));

    var html = '\
  <table id="_DialogTable_@thisID@" width="' + (this.Width + 26) + '" cellspacing="0" cellpadding="0" border="0" onselectstart="return false;" style="-moz-user-select: -moz-none; font-size:12px; line-height:1.4;border-collapse: collapse;">\
    <tr id="_Draghandle_@thisID@" style="border-bottom:1px solid #CACACA;' + (this.Drag ? "cursor: move;" : "") + '">\
      <td width="13" height="33" style="background:#F5FAFE; border-top:5px solid #CACACA;border-left:5px solid #CACACA" ></td>\
      <td height="33" style="background:#F5FAFE; border-top:5px solid #CACACA"><div style="padding: 0px 0 0 4px; float: left; font-weight: bold; color:#333;"><span id="_Title_@thisID@">' + this.Title + '</span></div>\
        <div onclick="Dialog.getInstance(\'@thisID@\').cancelButton.onclick.apply(Dialog.getInstance(\'@thisID@\').cancelButton,[]);" onmouseout="this.style.backgroundImage=\'url(@IMAGESPATH@dialog_closebtn.gif)\'" onmouseover="this.style.backgroundImage=\'url(@IMAGESPATH@dialog_closebtn_over.gif)\'" style="margin: 4px 0 0;*margin-top: 5px; position: relative;top:auto; cursor: pointer; float: right; height: 17px; width: 28px; background: url(@IMAGESPATH@dialog_closebtn.gif) 0 0;' + (ielt7 ? "margin-top: 3px;" : "") + (this.ShowCloseButton ? "" : "display:none;") + '"></div></td>\
      <td width="13" height="33" style="background:#F5FAFE; border-top:5px solid #CACACA;border-right:5px solid #CACACA"></td>\
    </tr>\
    <tr valign="top">\
      <td width="13" style=" background:#fff; border-left:5px solid #CACACA"></td>\
      <td align="center"><table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">\
          <tr id="_MessageRow_@thisID@" style="' + (this.ShowMessageRow ? "" : "display:none") + '">\
            <td valign="top" height="50"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#eaece9 url(@IMAGESPATH@dialog_bg.jpg) no-repeat scroll right top;" id="_MessageTable_@thisID@">\
                <tr>\
                  <td width="50" height="50" align="center"><img width="32" height="32" src="@IMAGESPATH@' + this.MessageIcon + '" id="_MessageIcon_@thisID@"/></td>\
                  <td align="left" style="line-height: 16px;"><div id="_MessageTitle_@thisID@" style="font-weight:bold">' + this.MessageTitle + '</div>\
                    <div id="_Message_@thisID@">' + this.Message + '</div></td>\
                </tr>\
              </table></td>\
          </tr>\
          <tr>\
            <td valign="top" align="center" style="background:#FFFFFF;"><div id="_Container_@thisID@" style="position: relative; width: ' + this.Width + 'px; height: ' + this.Height + 'px;">\
                <div style="position: absolute; height: 100%; width: 100%; display: none; background-color:#fff; opacity: 0.5;" id="_Covering_@thisID@">&nbsp;</div>\
	' + (function (obj) {
        if (obj.InnerHtml) return obj.InnerHtml;
        if (obj.URL) return '<iframe width="100%" height="100%" frameborder="0" style="border:none 0;" id="_DialogFrame_' + obj.ID + '" src="' + obj.displacePath() + '"></iframe>';
        return "";
    })(this) + '\
              </div></td>\
          </tr>\
          <tr id="_ButtonRow_@thisID@" style="' + (this.ShowButtonRow ? "" : "display:none") + '">\
            <td height="36"><div id="_DialogButtons_@thisID@" style="border-top: 1px solid #DADEE5; padding: 8px 20px; text-align: center; background-color:#f6f6f6;">\
                <input type="button" style="background:#e9e9e9; border:1px solid #a4a4a4;  font-size:12px; color:#000000; width:65px; height:28px; line-height:22px; cursor:pointer;" value="' + this.OkButtonText + '" id="_ButtonOK_@thisID@"/>\
                <input type="button" style="background:#e9e9e9; border:1px solid #a4a4a4;  font-size:12px; color:#000000; width:65px; height:28px; line-height:22px; cursor:pointer;" value="' + this.CancelButtonText + '" onclick="Dialog.getInstance(\'@thisID@\').close();" id="_ButtonCancel_@thisID@"/>\
              </div></td>\
          </tr>\
        </table></td>\
      <td width="13" style=" background:#fff; border-right:5px solid #cacaca"></td>\
    </tr>\
    <tr>\
      <td width="13" height="13" style="background:#fff; border-left:5px solid #cacaca;border-bottom:5px solid #cacaca;"></td>\
      <td  style="background:#fff;border-bottom:5px solid #cacaca;"></td>\
      <td width="13" height="13"  style="background:#fff; border-right:5px solid #cacaca;border-bottom:5px solid #cacaca;"><a onfocus=\'$id("_forTab_@thisID@").focus();\' href="#;"></a></td>\
    </tr>\
  </table>\
</div>\
';
	html=html.replace(/@IMAGESPATH@/gm,IMAGESPATH).replace(/@thisID@/gm,this.ID)
    var div = topWin.$id("_DialogDiv_" + this.ID);
    if (!div) {
        div = topDoc.createElement("div");
        div.id = "_DialogDiv_" + this.ID;
        topDoc.getElementsByTagName("BODY")[0].appendChild(div);
    }
    div.style.position =ielt7?"absolute":"fixed";
    div.style.left = "-9999999px";
    div.style.top = "-9999999px";
    div.innerHTML = html;
    if (this.InvokeElementId) {
        var element = $id(this.InvokeElementId);
        element.style.position = "";
        element.style.display = "";
        if (isIE) {
            var fragment = topDoc.createElement("div");
            fragment.innerHTML = element.outerHTML;
            element.outerHTML = "";
            topWin.$id("_Covering_" + this.ID).parentNode.appendChild(fragment)
        } else {
            topWin.$id("_Covering_" + this.ID).parentNode.appendChild(element)
        }
    }
    this.opener = window;
    if (this.URL) {
        if (topWin.$id("_DialogFrame_" + this.ID)) {
            this.innerFrame = topWin.$id("_DialogFrame_" + this.ID);
        };
        var self = this;
        this.innerFrameOnload = function () {
            try {
				self.innerWin = self.innerFrame.contentWindow;
				self.innerWin.ownerDialog = self;
                self.innerDoc = self.innerWin.document;
                if (self.Title=='　' && self.innerDoc && self.innerDoc.title) {
                    if (self.innerDoc.title) topWin.$id("_Title_" + self.ID).innerHTML = self.innerDoc.title;
                };
            } catch(e) {
                if (window.console && window.console.log) console.log("可能存在访问限制，不能获取到浮动窗口中的文档对象。");
            }
            if (typeof(self.OnLoad)== "function")self.OnLoad();
        };
        if (!isGecko) {
            this.innerFrame.attachEvent("onreadystatechange", function(){//在ie下可以给iframe绑定onreadystatechange事件
				if((/loaded|complete/).test(self.innerFrame.readyState)){
					self.innerFrameOnload();
				}
			});
            //this.innerFrame.attachEvent("onload", self.innerFrameOnload);
        } else {
			this.innerFrame.contentWindow.addEventListener("load", function(){self.innerFrameOnload()}, false);//在firefox下iframe仅能够绑定onload事件
            //this.innerFrame.onload = self.innerFrameOnload;
        };
    };
    topWin.$id("_DialogDiv_" + this.ID).dialogId = this.ID;
    topWin.$id("_DialogDiv_" + this.ID).dialogInstance = this;
    this.attachBehaviors();
    this.okButton = topWin.$id("_ButtonOK_" + this.ID);
    this.cancelButton = topWin.$id("_ButtonCancel_" + this.ID);
	div=null;
};
Dialog.prototype.setSize = function (w, h) {
    if (w && +w > 20) {
        this.Width = +w;
        topWin.$id("_DialogTable_" + this.ID).width = this.Width + 26;
        topWin.$id("_Container_" + this.ID).style.width = this.Width + "px";
    }
    if (h && +h > 10) {
        this.Height = +h;
        topWin.$id("_Container_" + this.ID).style.height = this.Height + "px";
    }
    this.setPosition();
};
Dialog.prototype.show = function () {
    this.create();
    var bgdiv = Dialog.getBgdiv(),
		thisdialogDiv=this.getDialogDiv();
    this.zindex = thisdialogDiv.style.zIndex = Dialog.bgDiv.style.zIndex + 1;
    if (topWin.Dialog._Array.length > 0) {
        this.zindex = thisdialogDiv.style.zIndex = topWin.Dialog._Array[topWin.Dialog._Array.length - 1].zindex + 2;
    } else {
        bgdiv.style.display = "none";
    	if(HideScrollbar){
	        var topWinBody = topDoc.getElementsByTagName(topDoc.compatMode == "BackCompat" ? "BODY" : "HTML")[0];
	        topWinBody.styleOverflow = topWinBody.style.overflow;
	        topWinBody.style.overflow = "hidden";
        }
    }
    topWin.Dialog._Array.push(this);
    if (this.Modal) {
        bgdiv.style.zIndex = topWin.Dialog._Array[topWin.Dialog._Array.length - 1].zindex - 1;
        Dialog.setBgDivSize();
		if(bgdiv.style.display == "none"){
			if(this.Animator){
				var bgMask=topWin.$id("_DialogBGMask");
				bgMask.style.opacity = 0;
				bgMask.style.filter = "alpha(opacity=0)";
        		bgdiv.style.display = "";
				fadeEffect(bgMask,0,40,ielt7?20:10);
				bgMask=null;
			}else{
        		bgdiv.style.display = "";
			}
		}
    }
    this.setPosition();
    if (this.CancelEvent) {
        this.cancelButton.onclick = this.CancelEvent;
        if(this.ShowButtonRow)this.cancelButton.focus();
    }
    if (this.OKEvent) {
        this.okButton.onclick = this.OKEvent;
        if(this.ShowButtonRow)this.okButton.focus();
    }
    if (this.AutoClose && this.AutoClose > 0) this.autoClose();
    this.opened = true;
	bgdiv=null;
};
Dialog.prototype.close = function () {
	var thisdialogDiv=this.getDialogDiv();
    if (this == topWin.Dialog._Array[topWin.Dialog._Array.length - 1]) {
        var isTopDialog = topWin.Dialog._Array.pop();
    } else {
        topWin.Dialog._Array.remove(this)
    }
    if (this.InvokeElementId) {
        var innerElement = topWin.$id(this.InvokeElementId);
        innerElement.style.display = "none";
        if (isIE) {
            //ie下不能跨窗口拷贝元素，只能跨窗口拷贝html代码
            var fragment = document.createElement("div");
            fragment.innerHTML = innerElement.outerHTML;
            innerElement.outerHTML = "";
            document.getElementsByTagName("BODY")[0].appendChild(fragment)
        } else {
            document.getElementsByTagName("BODY")[0].appendChild(innerElement)
        }

    }
    if (topWin.Dialog._Array.length > 0) {
        if (this.Modal && isTopDialog) Dialog.bgDiv.style.zIndex = topWin.Dialog._Array[topWin.Dialog._Array.length - 1].zindex - 1;
    } else {
        Dialog.bgDiv.style.zIndex = "900";
        Dialog.bgDiv.style.display = "none";
        if(HideScrollbar){
	        var topWinBody = topDoc.getElementsByTagName(topDoc.compatMode == "BackCompat" ? "BODY" : "HTML")[0];
	        if(topWinBody.styleOverflow != undefined)
	        	topWinBody.style.overflow = topWinBody.styleOverflow;
        }
    }
    this.opener.focus();
    if (isIE) {
		/*****释放引用，以便浏览器回收内存**/
		thisdialogDiv.dialogInstance=null;
		this.innerFrame=null;
		this.bgDiv=null;
		if (this.CancelEvent){this.cancelButton.onclick = null;};
		if (this.OKEvent){this.okButton.onclick = null;};
		topWin.$id("_DialogDiv_" + this.ID).onDragStart=null;
		topWin.$id("_DialogDiv_" + this.ID).onDragEnd=null;
		topWin.$id("_Draghandle_" + this.ID).onmousedown=null;
		topWin.$id("_Draghandle_" + this.ID).root=null;
		/**end释放引用**/
        thisdialogDiv.outerHTML = "";
		CollectGarbage();
    } else {
        var RycDiv = topWin.$id("_RycDiv");
        if (!RycDiv) {
            RycDiv = topDoc.createElement("div");
            RycDiv.id = "_RycDiv";
        }
        RycDiv.appendChild(thisdialogDiv);
        RycDiv.innerHTML = "";
		RycDiv=null;
    }
	thisdialogDiv=null;
    this.closed = true;
};
Dialog.prototype.autoClose = function () {
    if (this.closed) {
        clearTimeout(this._closeTimeoutId);
        return;
    }
    this.AutoClose -= 1;
    topWin.$id("_Title_" + this.ID).innerHTML = this.AutoClose + " 秒后自动关闭";
    if (this.AutoClose <= 0) {
        this.close();
    } else {
        var self = this;
        this._closeTimeoutId = setTimeout(function () {
            self.autoClose();
        },
        1000);
    }
};
Dialog.getInstance = function (id) {
    var dialogDiv = topWin.$id("_DialogDiv_" + id);
    if (!dialogDiv) alert("没有取到对应ID的弹出框页面对象");
	try{
    	return dialogDiv.dialogInstance;
	}finally{
		dialogDiv = null;
	}
};
Dialog.prototype.addButton = function (id, txt, func) {
    topWin.$id("_ButtonRow_" + this.ID).style.display = "";
    this.ShowButtonRow = true;
    var button = topDoc.createElement("input");
    button.id = "_Button_" + this.ID + "_" + id;
    button.type = "button";
    button.style.cssText = "margin-right:5px";
    button.value = txt;
    button.onclick = func;
    var input0 = topWin.$id("_DialogButtons_" + this.ID).getElementsByTagName("INPUT")[0];
    input0.parentNode.insertBefore(button, input0);
    return button;
};
Dialog.prototype.removeButton = function (btn) {
    var input0 = topWin.$id("_DialogButtons_" + this.ID).getElementsByTagName("INPUT")[0];
    input0.parentNode.removeChild(btn);
};
Dialog.getBgdiv = function () {
    if (Dialog.bgDiv) return Dialog.bgDiv;
    var bgdiv = topWin.$id("_DialogBGDiv");
    if (!bgdiv) {
        bgdiv = topDoc.createElement("div");
        bgdiv.id = "_DialogBGDiv";
        bgdiv.style.cssText = "position:absolute;left:0px;top:0px;width:100%;height:100%;z-index:999";
        var bgIframeBox = '<div style="position:relative;width:100%;height:100%;">';
		var bgIframeMask = '<div id="_DialogBGMask" style="position:absolute;background-color:#333;opacity:0.2;filter:alpha(opacity=40);width:100%;height:100%;"></div>';
		var bgIframe = ielt7?'<iframe src="about:blank" style="filter:alpha(opacity=0);" width="100%" height="100%"></iframe>':'';
		bgdiv.innerHTML=bgIframeBox+bgIframeMask+bgIframe+'</div>';
        topDoc.getElementsByTagName("BODY")[0].appendChild(bgdiv);
        if (ielt7) {
            var bgIframeDoc = bgdiv.getElementsByTagName("IFRAME")[0].contentWindow.document;
            bgIframeDoc.open();
            bgIframeDoc.write("<body style='background-color:#333' oncontextmenu='return false;'></body>");
            bgIframeDoc.close();
			bgIframeDoc=null;
        }
    }
    Dialog.bgDiv = bgdiv;
	bgdiv=null;
    return Dialog.bgDiv;
};
Dialog.prototype.getDialogDiv = function () {
	var dialogDiv=topWin.$id("_DialogDiv_" + this.ID)
	if(!dialogDiv)alert("获取弹出层页面对象出错！");
	try{
		return dialogDiv;
	}finally{
		dialogDiv = null;
	}
};
Dialog.onKeyDown = function (evt) {
	var evt=window.event||evt;
    if ((evt.shiftKey && evt.keyCode == 9)
		 ||evt.keyCode == 8) { //shift键及tab键,或backspace键
        if (topWin.Dialog._Array.length > 0) {
			var target = evt.srcElement||evt.target;
			if(target.tagName!='INPUT'&&target.tagName!='TEXTAREA'){//如果在不在输入状态中
				stopEvent(evt);
				return false;
			}
        }
    }
    if (evt.keyCode == 27) { //ESC键
        Dialog.close();
    }
};
Dialog.close = function (id) {
    if (topWin.Dialog._Array.length > 0) {
        var diag = topWin.Dialog._Array[topWin.Dialog._Array.length - 1];
        if(diag.MsgForESC){
			Dialog.confirm(diag.MsgForESC,function(){diag.cancelButton.onclick.apply(diag.cancelButton, []);})
        }else{
        	diag.cancelButton.onclick.apply(diag.cancelButton, []);
        }
    }
};
Dialog.alert = function (msg, func, w, h) {
    var w = w || 300,
        h = h || 110;
    var diag = new Dialog({
        Width: w,
        Height: h
    });
    diag.ShowButtonRow = true;
    diag.Title = "系统提示";
    diag.CancelEvent = function () {
        diag.close();
        if (func) func();
    };
    diag.InnerHtml = '<table height="100%" border="0" align="center" cellpadding="10" cellspacing="0">\
		<tr><td align="right"><img id="Icon_' + this.ID + '" src="' + IMAGESPATH + 'icon_alert.gif" width="34" height="34" align="absmiddle"></td>\
			<td align="left" id="Message_' + this.ID + '" style="font-size:9pt">' + msg + '</td></tr>\
	</table>';
    diag.show();
    diag.okButton.parentNode.style.textAlign = "center";
    diag.okButton.style.display = "none";
    diag.cancelButton.value = diag.OkButtonText;
    diag.cancelButton.focus();
};
Dialog.confirm = function (msg, funcOK, funcCal, w, h) {
    var w = w || 300,
        h = h || 110;
    var diag = new Dialog({
        Width: w,
        Height: h
    });
    diag.ShowButtonRow = true;
    diag.Title = "信息确认";
    diag.CancelEvent = function () {
        diag.close();
        if (funcCal) funcCal();
    };
    diag.OKEvent = function () {
        diag.close();
        if (funcOK) funcOK();
    };
    diag.InnerHtml = '<table height="100%" border="0" align="center" cellpadding="10" cellspacing="0">\
		<tr><td align="right"><img id="Icon_' + this.ID + '" src="' + IMAGESPATH + 'icon_query.gif" width="34" height="34" align="absmiddle"></td>\
			<td align="left" id="Message_' + this.ID + '" style="font-size:9pt">' + msg + '</td></tr>\
	</table>';
    diag.show();
    diag.okButton.parentNode.style.textAlign = "center";
    diag.okButton.focus();
};
Dialog.open = function (arg) {
    var diag = new Dialog(arg);
    diag.show();
    return diag;
};
window.attachEvent("onload", Dialog.attachBehaviors);
