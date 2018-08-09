
	$(document).ready(function (){
		getOpenOrCloseTradeTime();
		getNewsList('43','105','106','107');
		getNewsPic('ADPLACE_201507271734380274');
		getbottomcopy();
	});
//底部版权
function getbottomcopy(){
$.ajax({
			url:"sitePagecodeAction!getbottomcopy.do?code=bottomcopy",
			type:"post",
			dataType:"html",
			cache:false,
			async:false,
			data:{},
			success:function(data){
				$("#copyright").html(data+"<div class=\"erweima\"></div>");
			},
			error:function(retMsg){
				try{
					$.zd.alert('','获取菜单失败！');
				}catch(eee){
				}
			}
		});
}

//时间函数定时任务
	var interHandle=setInterval("show_time()",1000);
//	首页倒计时
	function show_time(){
	var dates = new Date();//获取当前时间
	var y = dates.getFullYear();//获取当前年
	var M = dates.getMonth()+1;//获取当前月
	var d = dates.getDate();//获取当前日
	var h = dates.getHours();//获取当前小时
	var m = dates.getMinutes();//获取当前分
	var s = dates.getSeconds(); //获取当前秒
        //获取当前时间戳
	var sysdate = y + "-" + M + "-"+ d +" "+ h +":"+ m +":" + s;   //拼成完整当前时间
	sysdate = sysdate.replace(" ","-").replace(":","-").replace(":","-");//将完整的时间中的空格和：替换成-
	var s_locaTime = sysdate.split("-");//按照-分割成六组数
	var e_locaTime = new Date(parseInt(s_locaTime[0],10),parseInt(s_locaTime[1],10),parseInt(s_locaTime[2],10),parseInt(s_locaTime[3],10),parseInt(s_locaTime[4],10),parseInt(s_locaTime[5],10));
	var locaTime = e_locaTime.getTime();  //获取当前时间戳
	
	//获取开闭市时间
	var s_time=$("#start_time").val();//开市时间
	var e_time=$("#end_time").val();//闭市时间
	var stime = y + "-" + M + "-"+ d +" "+s_time;//开市时间拼接上年月日，以便下面取时间戳
	var etime = y + "-" + M + "-"+ d +" "+e_time;//闭市时间拼接上年月日，以便下面取时间戳
	s_limitdate = stime.replace(" ","-").replace(":","-").replace(":","-");//将开市时间中的空格和：替换成-
	e_limitdate = etime.replace(" ","-").replace(":","-").replace(":","-");//将闭市时间中的空格和：替换成-
	var Array_s_limitdate = s_limitdate.split("-");//按照-分割数组，六组，年月日时分秒，开市
	var Array_e_limitdate = e_limitdate.split("-");//按照-分割数组，六组，年月日时分秒，闭市
	var start_date = new Date(parseInt(Array_s_limitdate[0],10),parseInt(Array_s_limitdate[1],10),parseInt(Array_s_limitdate[2],10),parseInt(Array_s_limitdate[3],10),parseInt(Array_s_limitdate[4],10),parseInt(Array_s_limitdate[5],10));
	var end_date   = new Date(parseInt(Array_e_limitdate[0],10),parseInt(Array_e_limitdate[1],10),parseInt(Array_e_limitdate[2],10),parseInt(Array_e_limitdate[3],10),parseInt(Array_e_limitdate[4],10),parseInt(Array_e_limitdate[5],10));
	var s_limitTime = start_date.getTime(); //获取开市时间戳
	var e_limitTime = end_date.getTime(); //获取闭市时间戳
	var mintime =(locaTime - s_limitTime);//距开市时间的时间差
	var maxtime =(e_limitTime - locaTime);//距闭市时间的时间差
	if(mintime>0){
		if(maxtime >= 0){
			 //计算出相差天数
			var days=Math.floor(maxtime/(24*3600*1000));
			var daystr = days>0?days+"天":"";
			var leave1=maxtime%(24*3600*1000);    //计算天数后剩余的毫秒数
			var hours=Math.floor(leave1/(3600*1000));
			var hourstr = hours>0?hours+"小时":"";
			//计算相差分钟数
			var leave2=leave1%(3600*1000)        //计算小时数后剩余的毫秒数
			var minutes=Math.floor(leave2/(60*1000))
			var minutestr = minutes>0?minutes+"分钟":"";
			//计算相差秒数
			var leave3=leave2%(60*1000)      //计算分钟数后剩余的毫秒数
			var seconds=Math.floor(leave3/1000)
			var secondstr = seconds>0?seconds+"秒":"";
			$("#time").html('距离闭市：<font>'+hours+'&nbsp;小时&nbsp;'+minutes+'&nbsp;分&nbsp;</font>');
			maxtime=maxtime-1000;
		}else{
			clearInterval(interHandle);
			$("#time").html('<font>已闭市</font>');
		}
	}else{
	clearInterval(interHandle);
			$("#time").html('<font>未开市</font>');
	}
		
}
//获取开闭市时间
	function  getOpenOrCloseTradeTime(){
	$.ajax({
				url:rootPath+"/home!getOpenOrCloseTradeTime.do",//提交的url
				method:"post",//提交方式 post get
				cache:false,//是否禁止缓存
				async:false,//是否异步 false同步 true异步
				dataType:"json",//返回数据类型 html json jsonp
				success:function(ret){
				if(ret.status ==0){//获取数据成功
						//控制开闭市的状态
					var start_time = (ret.msg.split("-"))[0]; 
					var end_time = (ret.msg.split("-"))[1];
					var S_hours = start_time.substring(0,2);
					var S_minutes = start_time.substring(2,4);
					var S_seconds = start_time.substring(4,6);
					var E_hours = end_time.substring(0,2);
					var E_minutes = end_time.substring(2,4);
					var E_seconds = end_time.substring(4,6);
					$("#start_time").html(S_hours+':'+S_minutes);
					$("#end_time").html(E_hours+':'+E_minutes);
					$("#start_time").val(S_hours+':'+S_minutes+':'+S_seconds);
					$("#end_time").val(E_hours+':'+E_minutes+':'+E_seconds);
					}else{
						$.zd.alert("提示信息",ret.msg); 
						return false;
					}
				},
				error:function(req,msg){//错误时的处理
					$.zd.alert('',msg);
				}
			});

}
//获取待办事项的数目
	function  getdbsxInfo(){
	$.ajax({
				url:rootPath+"/home!getdbsxInfo.do",//提交的url
				method:"post",//提交方式 post get
				cache:false,//是否禁止缓存
				async:false,//是否异步 false同步 true异步
				dataType:"json",//返回数据类型 html json jsonp
				success:function(ret){
							if(ret.status ==0){//获取数据成功
							$("#dbsxInfo").html('<font>['+ret.msg+']</font>');
							}else{
								$.zd.alert("提示信息",ret.msg); 
								return false;
							}
						},
				error:function(req,msg){//错误时的处理
					$.zd.alert('',msg);
				}
			});

}
	/** 退出系统 */
	function logout(){
		$.zd.confirm("提示信息","确定要退出系统吗？",function(){
		jQuery.ajax({
			url:"login!out.do",
			cache:false,
			datatype:"html",
			success:function(msg){
				//alert("退出系统成功！");
				//top.location = "";
				window.location="home.do";
			},
			error:function(msg){
			}
		});
		});
	}


/*岁小攀*/

	  var ii=0;
	  var j=0;
	  var k=0;
	  var m=0;
	  var n=0;
	  var intId = null;
	  var intId2 = null;
	  var intId3 = null;
	  var intId4 = null;
	  var intId5 = null;
	  
	  function check(idx){
	  	if(ii<$("#tabRank li").length){
	  	    $("#con_"+ii).addClass("on").siblings().removeClass("on");	
	  	    ii++;
	  	}else{
	  		ii=0;
	  	}
	  }
	  function check2(idx){
	  	if(j<$("#tabRank2 li").length){
	  	    $("#cona_"+j).addClass("on").siblings().removeClass("on");	
	  	    j++;
	  	}else{
	  		j=0;
	  	}
	  }
	  function check3(idx){
	  	if(k<$("#tabRank3 li").length){
	  	    $("#conb_"+k).addClass("on").siblings().removeClass("on");	
	  	    k++;
	  	}else{
	  		k=0;
	  	}
	  }
	  
	  function check4(idx){
	  	if(m<$("#tabRank4 li").length){
	  	    $("#conc_"+m).addClass("on").siblings().removeClass("on");	
	  	    m++;
	  	}else{
	  		m=0;
	  	}
	  }
	  
	    function check5(idx){
	  	if(n<$("#tabRank5 li").length){
	  	    $("#cond_"+n).addClass("on").siblings().removeClass("on");	
	  	    n++;
	  	}else{
	  		n=0;
	  	}
	  }
	  
	  
	  $(document).ready(function(){ 
	  	intId = setInterval(function(){check(1)},2000); 
	  }); 
	  
	   $(document).ready(function(){ 
	  	intId2 = setInterval(function(){check2(1)},2000);  	
	  });
	  
	   $(document).ready(function(){ 
	  	intId3 = setInterval(function(){check3(1)},2000);  	
	  });
	  
	   $(document).ready(function(){ 
	  	intId4 = setInterval(function(){check4(1)},2000);  	
	  });
	  
	   $(document).ready(function(){ 
	  	intId5 = setInterval(function(){check5(1)},2000);  	
	  });
	  
	   $("#tabRank").find("li").live("mouseover",function(){
	  	   clearInterval(intId);
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 
	  	
	  	$("#tabRank").find("li").live("mouseout",function(){
	  	   intId = setInterval(function(){check(1)},1000);  	
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 	
	  	//---------------
	  	$("#tabRank2").find("li").live("mouseover",function(){
	  	   clearInterval(intId2);
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 
	  	
	  	$("#tabRank2").find("li").live("mouseout",function(){
	  	   intId2 = setInterval(function(){check2(1)},1000);  	
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 	  
	  	//-------------------
	  	$("#tabRank3").find("li").live("mouseover",function(){
	  	   clearInterval(intId3);
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 
	  	
	  	$("#tabRank3").find("li").live("mouseout",function(){
	  	   intId3 = setInterval(function(){check3(1)},1000);  	
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 
	  	//-------------------
	  	$("#tabRank4").find("li").live("mouseover",function(){
	  	   clearInterval(intId4);
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 
	  	
	  	$("#tabRank4").find("li").live("mouseout",function(){
	  	   intId4 = setInterval(function(){check4(1)},1000);  	
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 
	  	//-------------------------------
	  	$("#tabRank5").find("li").live("mouseover",function(){
	  	   clearInterval(intId5);
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 
	  	
	  	$("#tabRank5").find("li").live("mouseout",function(){
	  	   intId5 = setInterval(function(){check5(1)},1000);  	
	  	   $(this).addClass("on").siblings().removeClass("on");	
	  	}); 


//获取首页数据
function getNewsList(newsitemid,id,id1,id2){
	$.ajax({
				url:rootPath+"/home!getNewsList.do?day=30",//提交的url
				method:"post",//提交方式 post get
				cache:false,//是否禁止缓存
				async:false,//是否异步 false同步 true异步
				dataType:"html",//返回数据类型 html json jsonp
				data:{"newsitemid":newsitemid,"id":id,"id1":id1,"id2":id2},
				success:function(data){//成功后的处理
				   $("#PaperPulp").html(data);
				},
				error:function(req,msg){//错误时的处理
					$.zd.alert('',msg);
				}
			});
			
}
//获取首页轮播图片
function getNewsPic(id){
	$.ajax({
		url:rootPath+"/home!getNewsPic.do",//提交的url
		method:"post",//提交方式 post get
		cache:false,//是否禁止缓存
		async:false,//是否异步 false同步 true异步
		dataType:"html",//返回数据类型 html json jsonp
		data:{"id":id},
		success:function(data){//成功后的处理
		   $("#CKBJ").html(data);
		},
		error:function(req,msg){//错误时的处理
			$.zd.alert('',msg);
		}
	});
			
}
//获取首页数据实时成交数据
function getPriceListData(warekind_key){ 
	$.ajax({ 
		url:rootPath+"/home!getTradeListData.do",//提交的url
		method:"post",//提交方式 post get
		cache:false,//是否禁止缓存
		async:false,//是否异步 false同步 true异步
		dataType:"html",//返回数据类型 html json jsonp
		data:{"warekind_key":warekind_key},
		success:function(data){//成功后的处理
		     if(warekind_key == 1){  //动力煤
		      $("#CKBJ").html(data);
		     }else if(warekind_key == 2){  //炼焦煤
		      $("#CJZDJ").html(data);
		     }else if(warekind_key == 3){      //化工用煤
		       $("#Platinum_Price").html(data);
		     }else if(warekind_key == 4){      //喷吹用煤
		       $("#Zhishu").html(data);
		     }else if(warekind_key == 5){      //有色金属
		       $("#JYSP_Price").html(data);
		     }
		},
		error:function(req,msg){//错误时的处理
			$.zd.alert('',msg);
		}
	});
	//window.setTimeout(getPriceListData,10000);
}
function sub(){ 
	$.ajax({
				url:rootPath+"/myAttention!myAttentionDataUI.do",//提交的url
				method:"post",//提交方式 post get
				cache:false,//是否禁止缓存
				async:false,//是否异步 false同步 true异步
				dataType:"html",//返回数据类型 html json jsonp
				success:function(data){//成功后的处理
					$("#Zhishu").html(data);
				},
				error:function(req,msg){//错误时的处理
					$.zd.alert('',msg);
				}
			});
}

function guanzhu(gkey){
		$.ajax({
			url:"shoppingCart!addCartInfo.do",
			type:"post",//提交方式 post get
			cache:false,//是否禁止缓存
			async:true,//是否异步 false同步 true异步
			dataType:"json",//返回数据类型 html json jsonp
			data:{"conobjKey":gkey,"cartmaxnum":"5","gnum":"0"},
			success:function(ret){//成功后的处理				
				if(ret.status==0){
					$.zd.alert('',"您不能购买自己发布的商品！");
				}else if(ret.status==1){
				    $.zd.alert('',"该商品已经存在！");
				}else if(ret.status==3){
					$.zd.alert('',"您所添加的商品已满5个，立即进入购物车结算！");
					
				}else if(ret.status==4){
                    $.zd.alert('',"加入购物车成功！");
                    $("#div_gwc_num").html(ret.msg);
                    updateGoodCarPicNum();
				}
			},
			error:function(req,msg){//错误时的处理
			}
		});
	}
	
	function updateGoodCarPicNum(){
		$num=$('.J-shoping-num');
		var num=Number($num.text());
		if(num<=5){
         $num.text(num+1);
		};
	}
	
	function showpic1(){
		$.ajax({
			url:"siteAdvertisementAction!getadvertisement.do?id=ADPLACE_201408080935180784",
			type:"post",
			dataType:"html",
			cache:false,
			async:false,
			data:{},
			success:function(data){
				$("#pic1").html(data);
			},
			error:function(retMsg){
				try{
					$.zd.alert('','获取菜单失败！');
				}catch(eee){
				}
			}
		});
	}
	function showpic2(){
		$.ajax({
			url:"siteAdvertisementAction!getadvertisement.do?id=ADPLACE_201408081043564866",
			type:"post",
			dataType:"html",
			cache:false,
			async:false,
			data:{},
			success:function(data){
				$("#pic2").html(data);
			},
			error:function(retMsg){
				try{
					$.zd.alert('','获取菜单失败！');
				}catch(eee){
				}
			}
		});
	}
	function showpic3(){
		$.ajax({
			url:"siteAdvertisementAction!getadvertisement.do?id=ADPLACE_201408081046276501",
			type:"post",
			dataType:"html",
			cache:false,
			async:false,
			data:{},
			success:function(data){
				$("#pic3").html(data);
			},
			error:function(retMsg){
				try{
					$.zd.alert('','获取菜单失败！');
				}catch(eee){
				}
			}
		});
	}
	
	function showpic4(){
		$.ajax({
			url:"siteAdvertisementAction!getadvertisement.do?id=ADPLACE_201511061024542582",
			type:"post",
			dataType:"html",
			cache:false,
			async:false,
			data:{},
			success:function(data){
				$("#pic4").html(data);
			},
			error:function(retMsg){
				try{
					$.zd.alert('','获取菜单失败！');
				}catch(eee){
				}
			}
		});
	}
	
	function showpic5(){
		$.ajax({
			url:"siteAdvertisementAction!getadvertisement.do?id=ADPLACE_201511111050058678",
			type:"post",
			dataType:"html",
			cache:false,
			async:false,
			data:{},
			success:function(data){
				$("#pic5").html(data);
			},
			error:function(retMsg){
				try{
					$.zd.alert('','获取菜单失败！');
				}catch(eee){
				}
			}
		});
	}
	
		function showLogin(url){
		if(null == url || url == undefined){
			if("<%=visitUrl%>".indexOf("frame/index.jsp")>-1){//现货大厅
				url = "index.do";
			}else if("<%=visitUrl%>".indexOf("transport_index.jsp")>-1){//91物流
				url = "TranHallAction.do";
			}
			else if("<%=visitUrl%>".indexOf("bidHall.jsp")>-1){//招投标大厅
				url = "bid!bidHallUI.do";
			}else{
				url = "myaccount.do";//默认转向会员中心
			}
		}
//	 	var diag = new Dialog({Width:400,Height:250,Modal:true,Title:"登录"});
//	 	diag.URL = "${path}/login!showLogin.do";
//	 	diag.show();
		window.location="login!showLogin.do";
	}
	
function bid(key){
	window.location.href=path+"/view!spotUI.do?key="+key;
	}
	
function toindex(key){
	window.location.href=path+"/view!spotUI.do?key="+key;
	}
	
	
