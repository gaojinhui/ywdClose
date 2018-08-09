
/*
 *  本插件基于JQUERY
 *  Jquery版本: 1.7.2
 *        Date:2012-06-28
 *      Author:Kingwell
 *      E-mail:jinhua.leng##gmail.com
 *
 *  ---------- 接口说明： ----------
 *
 *  --本插件采用zd命名空间，给Jquery添加静态方法，故调用方法为  $.zd.方法名 参数如下：
 *  --调用方法：
 *  --alert  $.zd.alert(content,title,callBack)
 *  --prompt $.zd.prompt(title,content,callBack)
 *
 *
 *  -- title 标题名称 如果缺省，则为默认标题，如："系统提示"
 *  -- content 内容显示的内容
 *  -- callback 回调函数，单击确定后要执行的内容
 *
 *
 *  --功能：类似系统功能，支持拖动，响应键盘事件，ESC退出，Enter确定，以及回调函数功能。
 *
 *
 */
(function ($) {

	$.zd = {
		title : "提示信息", // 默认标题 可修改
		speed : 400, // 默认速度 可修改
		buttonName : "确认", // 确定按钮默认名称 可修改
		cancel : "取消", // 取消按钮默认名称 可修改
		content : "Content",
		type : "computer",// 默认为电脑 传值可能为手机
		del : function () { //移除遮盖层
			$(".pop1").remove();
			$("#overlayTipInfo").remove();
		},
		//响应ESC键盘退出
		esc : function () { 
			$(document).keyup(function (event) {
				if (event.which == 27) {
					$.zd.del();
				}
			});
		},
		
		//替换特殊字符 异常信息
		filterContent:function(sContent){
			var s=sContent;
			if("undefined" != typeof(prjCode)  && prjCode!="J331_HQGJ_R"){
				if(s.length>50){
					if(s.indexOf("失败")>-1||s.indexOf("异常")>-1||s.indexOf("Exception")>-1||s.indexOf("ORA-")>-1){
						s="操作失败";
					}
				}
			}
			if(s.indexOf("error")>-1){
				s = "因网络问题，您的操作请求出现异常！";
			}
			
			//如果是重庆咖啡的项目，将报盘改为挂盘
			if("undefined" != typeof(prjCode)  && prjCode=="J293_CQKF_L"){
				s =s.replace("报盘","挂盘");
				s =s.replace("报盘","挂盘");//替换两次,防止出现两次提醒
			}
			
			return s; 
		},

		//给出提示信息 不做任何操作 
		alertMsg :function (sTitle, sContent, callBack, sButtonName, sCancel){
			var errorType=0; //默认的错误类型
			if(sContent.indexOf("error")>-1){//网络失败，需要跳转到登录界面，如果是和网站联动，跳转到网站，否则跳转交易登录
				errorType=1;//如果是网络问题，则需要转向登录
			}
			this.move();
			sContent = $.zd.filterContent(sContent);
			
			var title = sTitle || this.title;
			var button = sButtonName || this.buttonName;
			var cancels = sCancel || this.cancel;
			var layer ='<div class="pop1">';
		 		layer +='<div class="title"> ';
		 		layer +='<ul>';
		 		layer +='	<li class="tab">'+title+'</li>  ';
		 		layer +='</ul>';
		 		layer +='<span><a href="javascript:void(0);" id="alert-close">X</a></span>';
		 		layer +='</div>';
		 		layer +='<div class="con">';
		 		layer +='<p class="con">'+sContent+'</p>';
//		 		layer +='<p class="tc">';
//		 		layer +='	<input type="button" id="alert-button" value="'+button+'" class="btnhr30" size=""/>';
		 		//layer +='	<input type="button" value="取消"  class="btnhh30 ml5"size=""/>';
//		 		layer +='</p>';
		 		layer +='</div>   ';
		 		layer +='</div>   ';
			$(layer).fadeIn(this.speed).appendTo("body");
			$("#alert-button").focus();
			$("#alert-close").bind("click", function(){
				$.zd.del();
				if (callBack) {
					if(errorType==0){
						callBack();
					}else{
						$(top).attr("location",URL_WEBSITEJS);
					}
				}
			}); //移除层
			$("#alert-button").bind("click", function () {
				$.zd.del();
				if (callBack) {
					if(errorType==0){
						callBack();
					}else{
						$(top).attr("location",URL_WEBSITEJS);
					}
				}
				
			}); //移除层
//			this.esc();
		},
		
		removeMsg :function(){
			$.zd.del();
		},

		
		
		
		//内容显示功能
		alert : function (sTitle, sContent, callBack, sButtonName, sCancel,stype) {
			var errorType=0; //默认的错误类型
			if(sContent.indexOf("error")>-1){//网络失败，需要跳转到登录界面，如果是和网站联动，跳转到网站，否则跳转交易登录
				errorType=1;//如果是网络问题，则需要转向登录
			}
			this.move();
			//如果有特殊异常字符存在需要替换掉提示内容
			sContent = $.zd.filterContent(sContent);
			var title = sTitle || this.title;
			var button = sButtonName || this.buttonName;
			var cancels = sCancel || this.cancel;
			var type = stype || this.type;
			var layer;
			if(type =="mobile"){
			 	layer ='<div class="pop1" style="left:5px;">';
			}else{
			 	layer ='<div class="pop1">';
			}
			
	 		layer +='<div class="title"> ';
	 		layer +='<ul>';
	 		layer +='	<li class="tab">'+title+'</li>  ';
	 		layer +='</ul>';
	 		layer +='<span><a href="javascript:void(0);" id="alert-close">X</a></span>';
	 		layer +='</div>';
	 		layer +='<div class="con">';
	 		layer +='<p>'+sContent+'</p>';
	 		layer +='<p class="tc">';
	 		layer +='	<input type="button" id="alert-button" value="'+button+'" class="btnhr30" size=""/>';
	 		//layer +='	<input type="button" value="取消"  class="btnhh30 ml5"size=""/>';
	 		layer +='</p>';
	 		layer +='</div>   ';
	 		layer +='</div>   ';
			$(layer).fadeIn(this.speed).appendTo("body");
			
			$("#alert-button").focus();
			$("#alert-close").bind("click", function(){
				$.zd.del();
				if (callBack) {
					if(errorType==0){
						callBack();
					}else{
						$(top).attr("location",URL_WEBSITEJS);
					}
				}
			}); //移除层
			$("#alert-button").bind("click", function () {
				$.zd.del();
				if (callBack) {
					if(errorType==0){
						callBack();
					}else{
						$(top).attr("location",URL_WEBSITEJS);
					}
				}
				
			}); //移除层
			$.zd.esc();
		},
		  
		
		
		//提示 
		/**
		 * sTitle:提示信息 标题
		 * sContent：提示内容
		 * callBack： 确定回调方法
		 * sButtonName：默认 确定
		 * sButtonCancel： 默认取消
		 * callBackCancel： 取消回调方法
		 * 
		 */
		
		confirm : function (sTitle, sContent, callBack, sButtonName, sButtonCancel, callBackCancel) {
			var errorType=0; //默认的错误类型
			if(sContent.indexOf("error")>-1){//网络失败，需要跳转到登录界面，如果是和网站联动，跳转到网站，否则跳转交易登录
				errorType=1;//如果是网络问题，则需要转向登录
			}
			sContent = $.zd.filterContent(sContent);
			this.move(); //拖动
			var title = sTitle || this.title;
			var content = sContent || this.content;
			var button = sButtonName || this.buttonName;
			var cancels = sButtonCancel || this.cancel;
			var layer ='<div class="pop1">';
		 		layer +='<div class="title"> ';
		 		layer +='<ul>';
		 		layer +='	<li class="tab">'+title+'</li>  ';
		 		layer +='</ul>';
		 		layer +='<span><a href="javascript:void(0);" id="alert-close">X</a></span>';
		 		layer +='</div>';
		 		layer +='<div class="con">';
		 		layer +='<p>'+sContent+'</p>';
		 		layer +='<p class="tc">';
		 		layer +='	<input type="button" id="alert-button" value="'+button+'" class="btnhr30" size=""/>';
		 		layer +='	<input type="button" id="alert-cancel" value="'+cancels+'"  class="btnhh30 ml5"size=""/>';
		 		layer +='</p>';
		 		layer +='</div>   ';
		 		layer +='</div>   ';
		 	
			$(layer).fadeIn(this.speed).appendTo("body");
			//this.setting();
			$("#alert-button").focus(); //获得焦点
			$("#alert-close").bind("click", function(){
				$.zd.del();
				if(callBackCancel){
					if(errorType==0){
						callBackCancel();
					}else{
						$(top).attr("location",URL_WEBSITEJS);
					}
					
				}
			}); //移除层
			$("#alert-cancel").bind("click", function(){
				$.zd.del();
				if(callBackCancel){
					if(errorType==0){
						callBackCancel();
					}else{
						$(top).attr("location",URL_WEBSITEJS);
					}
				}
			}); //移除层
			$("#alert-button").bind("click", function () {
				$.zd.del();
				if (callBack) {
					if(errorType==0){
						callBack();
					}else{
						$(top).attr("location",URL_WEBSITEJS);
					}
				};
			}); //移除层
			$.zd.esc();
		},		
		
		
		//框拖动功能
		move : function () {
			this.showYinying();
			$(".tab").mousedown(function (event) {
				var l = parseInt($(".title").css("left")),
				t = parseInt($(".title").css("top"));
				x = event.pageX - l;
				y = event.pageY - t;
				$("body").bind("mousemove", function (event) {
					$(".title").css({
						"left" : (event.pageX - x)
					});
					$(".title").css({
						"top" : (event.pageY - y)
					});
					//$("#alert-container").fadeTo(0,0.9)
				});
			});
			$("body").mouseup(function () {
				$("body").unbind("mousemove");
				//$("#alert-container").fadeTo(0,1)
			});
			
		},
		
	
		//设置背景层与内位置
		setting : function () {
			var bcw = document.documentElement.clientWidth,
		  	    bch = document.documentElement.clientHeight,
			    bsh = document.documentElement.scrollHeight,
			    aw  = $(".title").width() / 2,
			    ah  = $(".title").height() / 2;
			$(".pop1").css("height", bsh);
			$(".title").css({
				"top"  : bch / 2 - ah,
				"left" : bcw / 2 - aw
			});
		},
		
		//设置遮罩层
		showYinying :function (){
			//body 里面添加遮罩 
			if($("body").has("#overlayTipInfo").length==0){
				$("body").append('<div class="none" id="overlayTipInfo"></div>');
			}
			if($("body").has(".pop1").length>0){
				$(".pop1").remove();
			}
			$("#overlayTipInfo").height(document.body.scrollHeight);
			$("#overlayTipInfo").width(document.body.scrollWidth);
			$("#overlayTipInfo").addClass("black_overlay");
		},
		
		//移除遮罩层
		removeYinying :function(){
			$("#overlayTipInfo").remove();
				
		}
	};


})(jQuery);

/**
#alert-layer button:focus{border:1px solid #AAA!important; background:#789!important; color:white; outline:none}
#alert-layer{position:absolute;left:0;top:0;width:100%;height:100%;color:#333;line-height:1;z-index:10000; background:rgba(0,0,0,0.1)}
#alert-layer #alert-container{border-radius:3px; padding:10px; width:390px; position:fixed; _position:absolute;}
#alert-layer .alert-top{background:url(images/conner2.png) no-repeat left top; height:10px;}
#alert-layer .alert-bottom{background:url(images/conner2.png) no-repeat left bottom; height:10px;}
#alert-layer #alert-title{font-size:15px; height:30px;line-height:25px;padding:0 10px;position:relative;font-weight:bold;cursor:move;}
#alert-layer #alert-close{background: url(images/close.gif) no-repeat center center; width:25px; height:25px; position:absolute; cursor:pointer; right:2px; top:0px;}
#alert-layer .alert-button{ padding:5px 10px; text-align:right}
#alert-layer #alert-content{background:#F1F1F1; border-top:1px solid #B9B9B9; border-bottom:1px solid #B9B9B9; padding:10px 15px;}
.alert-box{background:url(images/tc_bg.png) repeat-y left top; padding:0 6px}

#alert-layer button{padding:5px; border:1px solid #CCC; margin:auto 5px; border-radius:2px; min-width:60px;}
#alert-layer h1,#alert-layer h2,#alert-layer h3,#alert-layer h4{margin:10px auto; font-size:16px}


#$.zd.alert("提示内容")
#$.zd.alert("提示内容","系统提示")//修改弹出框提示标题
#$.zd.comport("提示内容");
**/