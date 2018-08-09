<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:34:"./Template/manage\login\login.html";i:1531410750;}*/ ?>
<!DOCTYPE HTML>
<html>
<head>
	<title>管理员登录</title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
	<script src="/public/manage/js/skel.min.js"></script>
	<script src="/public/manage/js/init.js"></script>
	<link rel="stylesheet" href="/public/layui/css/layui.css" media="all" />
	<link rel="stylesheet" href="/public/manage/css/login.css" media="all" />
	<noscript>
		<link rel="stylesheet" href="css/skel.css" />
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/style-wide.css" />
		<link rel="stylesheet" href="css/style-noscript.css" />
	</noscript>
	<!--[if lte IE 9]><link rel="stylesheet" href="css/ie/v9.css" /><![endif]-->
	<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->

</head>
<body class="loading">
<div id="wrapper">
	<div id="bg"></div>
	<div id="overlay"></div>
	<div id="main">

		<!-- Header -->
		<header id="header">
			<div class="login">
				<h1>管理登录</h1>
				<form class="layui-form">
					<div class="layui-form-item">
						<input class="layui-input" name="username" placeholder="用户名" lay-verify="required" type="text" autocomplete="off">
					</div>
					<div class="layui-form-item">
						<input class="layui-input" name="userpwd" placeholder="密码" lay-verify="required" type="password" autocomplete="off">
					</div>
					<!--  -->
					<button class="layui-btn login_btn" lay-submit="" lay-filter="login">登录</button>
				</form>
			</div>
		</header>

		<!-- Footer -->
		<footer id="footer">
			<span class="copyright">&copy; Untitled. Design.</span>
		</footer>

	</div>
</div>
<script type="text/javascript" src="/public/layui//layui.js"></script>
<script type="text/javascript" src="/public/manage/js/login.js"></script>
</body>
</html>