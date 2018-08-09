<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:37:"./Template/manage\user\emailuser.html";i:1532486832;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>图片总数--layui后台管理模板</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/public/layui/css/layui.css" media="all" />
	<style>
		.laytable-cell-1-lock{
			width: 268px  !important;
		}
		.laytable-cell-1-id{
			width: 60px !important;
		}
		.laytable-cell-1-nickName{
			width: 151px !important;
		}
		.layui-table{
			width:100% !important;
		}
	</style>
</head>
<body class="childrenBody" style="margin-top: 1em">
<div class="demoTable">
   用户名搜索：
    <div class="layui-inline">
        <input class="layui-input" name="keyword" id="demoReload" autocomplete="off">
    </div>
    <button class="layui-btn" data-type="reload" id="sousuo">搜索</button>
    <!-- <button class="layui-btn" data-type="addData">添加</button> -->
</div>
<!-- <div class="layui-btn-group demoTable" >
	<button class="layui-btn" data-type="addData">添加</button>
	<button class="layui-btn" data-type="getCheckData">获取选中行数据</button>
	<button class="layui-btn" data-type="getCheckLength">获取选中数目</button>
	<button class="layui-btn" data-type="isAll">验证是否全选</button>
</div> -->
		<table class="layui-hide" id="test" lay-filter="demo"></table>
		<input type="hidden" value="<?php echo $id; ?>" id="personid" />
		<input type="hidden" value="<?php echo $type; ?>" id="type" />
		<script type="text/html" id="switchTpl">
			<input type="checkbox" name="sex" value="{{d.status}}" bid="{{d.uid}}" lay-skin="switch" lay-text="正常|禁用" lay-filter="sexDemo" {{ d.status == 1 ? 'checked' : '' }}>
		</script>

		<script type="text/html" id="images">
			<img src="{{d.pic}}" onclick='layui.bigimg(this)'/>;
		</script>
		<script type="text/html" id="timestamp">
			{{laytpl.toDateString(d.time)}}
		</script>

		<script type="text/html" id="barDemo">
			<a class="layui-btn layui-btn-xs" lay-event="show">编辑</a>
			{{#  if(<?php echo $type; ?> ==1){ }}
				<a class="layui-btn layui-btn-quan layui-btn-xs" lay-event="addreport">添加报表</a>
				<a class="layui-btn layui-btn-quan layui-btn-xs" lay-event="reportlist">查看报表</a>
			{{#  } else { }}

			{{#  } }}
		</script>

	<script type="text/javascript" src="/public/layui/layui.js"></script>
	<script type="text/javascript" src="/public/manage/js/emailuser.js"></script>

</body>
</html>