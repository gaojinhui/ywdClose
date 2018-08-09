<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:27:"./Template/manage\main.html";i:1533119382;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>首页--layui后台管理模板</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/public/layui/css/layui.css" media="all" />
	<link rel="stylesheet" href="//at.alicdn.com/t/font_tnyc012u2rlwstt9.css" media="all" />
	<link rel="stylesheet" href="/public/manage/css/main.css" media="all" />
</head>
<body class="childrenBody">
	<!-- <div class="panel_box row">
		<div class="panel col">
			<a href="javascript:;" data-url="page/message/message.html">
				<div class="panel_icon">
					<i class="layui-icon" data-icon="&#xe63a;">&#xe63a;</i>
				</div>
				<div class="panel_word newMessage">
					<span></span>
					<cite>新消息</cite>
				</div>
			</a>
		</div>
		<div class="panel col">
			<a href="javascript:;" data-url="add_user">
				<div class="panel_icon" style="background-color:#FF5722;">
					<i class="iconfont icon-dongtaifensishu" data-icon="icon-dongtaifensishu"></i>
				</div>
				<div class="panel_word userAll">
					<span></span>
					<cite>增加管理人员</cite>
				</div>
			</a>
		</div>
		<div class="panel col">
			<a href="javascript:;" data-url="/manage/user_contorller/get_alluser">
				<div class="panel_icon" style="background-color:#009688;">
					<i class="layui-icon" data-icon="&#xe613;">&#xe613;</i>
				</div>
				<div class="panel_word userAll">
					<span></span>
					<cite>管理人员列表</cite>
				</div>
			</a>
		</div>
	</div> -->
	<!-- <div class="panel_box row">
		<div class="panel col">
			<a href="javascript:;" data-url="/manage/news_contorller/shownews">
				<div class="panel_icon" style="background-color:#2F4056;">
					<i class="layui-icon" data-icon="&#xe613;">&#xe613;</i>
				</div>
				<div class="panel_word userAll">
					<span></span>
					<cite>文章列表</cite>
				</div>
			</a>
		</div>
		<div class="panel col">
			<a href="javascript:;" data-url="/manage/course_contorller/getCourselist">
				<div class="panel_icon" style="background-color:#009688;">
					<i class="layui-icon" data-icon="&#xe613;">&#xe613;</i>
				</div>
				<div class="panel_word userAll">
					<span></span>
					<cite>课程列表</cite>
				</div>
			</a>
		</div> -->
		<!-- <div class="panel col">
			<a href="javascript:;" data-url="/manage/review_contorller/getReviewlist">
				<div class="panel_icon" style="background-color:#009688;">
					<i class="layui-icon" data-icon="&#xe613;">&#xe613;</i>
				</div>
				<div class="panel_word userAll">
					<span></span>
					<cite>评论审核列表</cite>
				</div>
			</a>
		</div>
		<div class="panel col">
			<a href="javascript:;" data-url="/manage/news_contorller/shownews?newsStatus=2">
				<div class="panel_icon" style="background-color:#F7B824;">
					<i class="iconfont icon-wenben" data-icon="icon-wenben"></i>
				</div>
				<div class="panel_word waitNews">
					<span></span>
					<cite>待审核文章</cite>
				</div>
			</a>
		</div> -->
		<!-- <div class="panel col">
			<a href="javascript:;" data-url="/manage/video_contorller/getVideolist?state=1">
				<div class="panel_icon" style="background-color:#F7B824;">
					<i class="iconfont icon-wenben" data-icon="icon-wenben"></i>
				</div>
				<div class="panel_word waitNews">
					<span></span>
					<cite>课程审核</cite>
				</div>
			</a>
		</div>
	</div> -->




	<div class="row">
		<div class="col">
			<blockquote class="layui-elem-quote title">系统基本参数</blockquote>
			<table class="layui-table">
				<colgroup>
					<col width="150">
					<col>
				</colgroup>
				<tbody>
				<!-- <tr>
					<td style="color: red">注册会员数(家)</td>
					<td style="font-weight:bold" onclick='layui.update("member",this)' ><?php echo $result['member']; ?></td>

				</tr>
				<tr>
					<td style="color: red">累计交易量(吨)</td>
					<td style="font-weight:bold" onclick="layui.update('trading',this)"><?php echo $result['trading']; ?></td>
				</tr>
				<tr>
					<td style="color: red">累计结算额（亿元）</td>
					<td style="font-weight:bold" onclick="layui.update('statement',this)"><?php echo $result['statement']; ?></td>
				</tr> -->
				<?php if(is_array($sysinfo) || $sysinfo instanceof \think\Collection || $sysinfo instanceof \think\Paginator): $i = 0; $__LIST__ = $sysinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?>
				<tr>
					<td><?php echo $key; ?></td>
					<td><?php echo $info; ?></td>
				</tr>
				<?php endforeach; endif; else: echo "" ;endif; ?>




				</tbody>
			</table>
		</div>
	</div>

	<script type="text/javascript" src="/public/layui/layui.js"></script>
	<script src="/public/layui/layui.all.js"></script>
	<script type="text/javascript" src="/public/manage/js/main.js"></script>
</body>
</html>