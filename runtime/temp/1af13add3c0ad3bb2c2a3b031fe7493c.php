<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:38:"./Template/manage\admin\adminList.html";i:1531410750;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>文章列表</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/public/layui/css/layui.css" media="all" />
</head>
<body class="childrenBody" style="margin-top: 1em">
<div class="demoTable">
    <!-- 文章标题搜索：
    <div class="layui-inline">
        <input class="layui-input" name="keyword" id="demoReload" autocomplete="off">
    </div>
    <button class="layui-btn" data-type="reload" id="sousuo">搜索</button> -->
    <button class="layui-btn" data-type="addData">添加</button>
</div>
<table class="layui-hide" id="test" lay-filter="demo" ></table>

<script type="text/html" id="switchTpl">
	<input type="checkbox" name="sex" value="{{d.id}}" bid="{{d.isShow}}" lay-skin="switch" lay-text="显示|隐藏" lay-filter="sexDemo" {{ d.isShow == 1 ? 'checked' : '' }}>
</script>

<script type="text/html" id="images">
	<img src="{{d.pic}}" onclick='layui.bigimg(this)'/>;
</script>
<script type="text/html" id="timestamp">
	{{laytpl.toDateString(d.newsTime)}}
</script>

<script type="text/html" id="barDemo">

</script>

<script type="text/javascript" src="/public/layui/layui.js"></script>
<script type="text/javascript" src="/public/manage/js/adminList.js"></script>

</body>
</html>