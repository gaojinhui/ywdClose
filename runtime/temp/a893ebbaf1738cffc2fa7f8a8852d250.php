<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:28:"./Template/manage\index.html";i:1533102412;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>北京鹦鹉岛科技后台管理系统</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta http-equiv="Access-Control-Allow-Origin" content="*">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="icon" href="favicon.ico">
	<link rel="stylesheet" href="/public/layui/css/layui.css" media="all" />
	<link rel="stylesheet" href="//at.alicdn.com/t/font_tnyc012u2rlwstt9.css" media="all" />
	<link rel="stylesheet" href="/public/manage/css/main.css" media="all" />
</head>
<body class="main_body">
	<div class="layui-layout layui-layout-admin">
		<!-- 顶部 -->
		<div class="layui-header header">
			<div class="layui-main">
				<a href="#" class="logo">北京鹦鹉岛科技</a>
				<!-- 显示/隐藏菜单 -->
				<a href="javascript:;" class="iconfont hideMenu icon-menu1"></a>
				<!-- 搜索 -->
				<div class="layui-form component">
			        <select name="modules" lay-verify="required" lay-search="">
						<option value="">直接选择或搜索选择</option>
						<option value="1">layer</option>
						<option value="2">form</option>
						<option value="3">layim</option>
						<option value="4">element</option>
						<option value="5">laytpl</option>
						<option value="6">upload</option>
						<option value="7">laydate</option>
						<option value="8">laypage</option>
						<option value="9">flow</option>
						<option value="10">util</option>
			        </select>
			        <i class="layui-icon">&#xe615;</i>
			    </div>
			    <!-- 天气信息 -->
			    <div class="weather" pc style="width: 200px;">
			    	<div id="tp-weather-widget"></div>
					<script>(function(T,h,i,n,k,P,a,g,e){g=function(){P=h.createElement(i);a=h.getElementsByTagName(i)[0];P.src=k;P.charset="utf-8";P.async=1;a.parentNode.insertBefore(P,a)};T["ThinkPageWeatherWidgetObject"]=n;T[n]||(T[n]=function(){(T[n].q=T[n].q||[]).push(arguments)});T[n].l=+new Date();if(T.attachEvent){T.attachEvent("onload",g)}else{T.addEventListener("load",g,false)}}(window,document,"script","tpwidget","//widget.seniverse.com/widget/chameleon.js"))</script>
					<script>tpwidget("init", {
					    "flavor": "slim",
					    "location": "WX4FBXXFKE4F",
					    "geolocation": "enabled",
					    "language": "zh-chs",
					    "unit": "c",
					    "theme": "chameleon",
					    "container": "tp-weather-widget",
					    "bubble": "disabled",
					    "alarmType": "badge",
					    "color": "#FFFFFF",
					    "uid": "U9EC08A15F",
					    "hash": "039da28f5581f4bcb5c799fb4cdfb673"
					});
					tpwidget("show");</script>
			    </div>
				<!-- <div  class="weather" style="margin-top: 20px;"><i class="iconfont icon-shuaxin1"></i><a href="/" target="_blank" style="color: #FFFfff">网站首页</a> </div> -->
			    <!-- 顶部右侧菜单 -->

			    <ul class="layui-nav top_menu">

			    	<!-- <li class="layui-nav-item showNotice" id="showNotice" pc>
						<a href="javascript:;"><i class="iconfont icon-gonggao"></i><cite>系统公告</cite></a>
					</li> -->
			    	<li class="layui-nav-item" mobile>
			    		<a href="javascript:;" class="mobileAddTab" data-url="page/user/changePwd.html"><i class="iconfont icon-shezhi1" data-icon="icon-shezhi1"></i><cite>设置</cite></a>
			    	</li>
			    	<li class="layui-nav-item" mobile>
			    		<a href="/manage/index/loginout" class="signOut"><i class="iconfont icon-loginout"></i> 退出</a>
			    	</li>
					<li class="layui-nav-item lockcms" pc>
						<a href="javascript:;"><i class="iconfont icon-lock1"></i><cite>锁屏</cite></a>
					</li>
					<li class="layui-nav-item" pc>
						<a href="javascript:;">
							<img src="/public/manage/images/timg.jpg" class="layui-circle" width="35" height="35">
							<cite><?php echo \think\Session::get('manageinfo')['username']; ?></cite>
						</a>
						<dl class="layui-nav-child">
							<!-- <dd><a href="javascript:;" data-url="page/user/userInfo.html"><i class="iconfont icon-zhanghu" data-icon="icon-zhanghu"></i><cite>个人资料</cite></a></dd>
							<dd><a href="javascript:;" data-url="page/user/changePwd.html"><i class="iconfont icon-shezhi1" data-icon="icon-shezhi1"></i><cite>修改密码</cite></a></dd> -->
							<dd><a href="javascript:;" class="changeSkin"><i class="iconfont icon-huanfu"></i><cite>更换皮肤</cite></a></dd>
							<dd><a href="javascript:" class="signOut" onclick="layui.loginout()"><i class="iconfont icon-loginout"></i><cite>退出</cite></a></dd>
						</dl>
					</li>
				</ul>
			</div>
		</div>
		<!-- 左侧导航 -->
		<div class="layui-side layui-bg-black">
			<div class="user-photo">
				<a class="img" title="我的头像" ><img src="/public/manage/images/timg.jpg"></a>
				<p>你好！<span class="userName"><?php echo \think\Session::get('manageinfo')['username']; ?></span>, 欢迎登录</p>
			</div>
			<div class="navBar layui-side-scroll"></div>
		</div>
		<!-- 右侧内容 -->
		<div class="layui-body layui-form">
			<div class="layui-tab marg0" lay-filter="bodyTab" id="top_tabs_box">
				<ul class="layui-tab-title top_tab" id="top_tabs">
					<li class="layui-this" lay-id=""><i class="iconfont icon-computer"></i> <cite>后台首页</cite></li>
				</ul>
				<ul class="layui-nav closeBox">
				  <li class="layui-nav-item">
				    <a href="javascript:;"><i class="iconfont icon-caozuo"></i> 页面操作</a>
				    <dl class="layui-nav-child">
					  <dd><a href="javascript:;" class="refresh refreshThis"><i class="layui-icon">&#x1002;</i> 刷新当前</a></dd>
				      <dd><a href="javascript:;" class="closePageOther"><i class="iconfont icon-prohibit"></i> 关闭其他</a></dd>
				      <dd><a href="javascript:;" class="closePageAll"><i class="iconfont icon-guanbi"></i> 关闭全部</a></dd>
				    </dl>
				  </li>
				</ul>
				<div class="layui-tab-content clildFrame">
					<div class="layui-tab-item layui-show">
						<iframe src="/manage/index/main"></iframe>
					</div>
				</div>
			</div>
		</div>
		<!-- 底部 -->
		<div class="layui-footer footer">
			<p>鹦鹉岛综合后台管理系统</p>
		</div>
	</div>
	
	<!-- 移动导航 -->
	<div class="site-tree-mobile layui-hide"><i class="layui-icon">&#xe602;</i></div>
	<div class="site-mobile-shade"></div>

	<script type="text/javascript" src="/public/layui/layui.all.js"></script>
	<script type="text/javascript" src="/public/manage/js/leftNav.js"></script>
	<script type="text/javascript" src="/public/manage/js/index.js"></script>
</body>
</html>