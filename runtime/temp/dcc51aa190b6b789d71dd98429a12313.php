<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:37:"./Template/manage\power\menulist.html";i:1531410751;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>文章列表--layui后台管理模板</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/public/layui//css/layui.css" media="all" />
	<link rel="stylesheet" href="//at.alicdn.com/t/font_tnyc012u2rlwstt9.css" media="all" />
</head>
<body class="childrenBody">
<form class="layui-form">
	<div class="layui-form news_list">
		<table class="layui-table layui-form" lay-skin="line" >
			<colgroup>
				<col width="5%">
				<col width="5%">
				<col width="20%">
			</colgroup>
			<thead>
			<tr>
				<th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
				<th>图标</th>
				<th>菜单名称</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			<tr>
				<td>
					<?php if($vo['check'] == 'on'): ?>
					<input type="checkbox" name="menu[]" lay-skin="primary" value="<?php echo $vo['id']; ?>" checked>
					<?php else: ?>
					<input type="checkbox" name="menu[]" lay-skin="primary" value="<?php echo $vo['id']; ?>" >
					<?php endif; ?>
				</td>
				<td>
					<i class="layui-icon" style="font-size: 18px; color: #5FB878;"><?php echo $vo['icon']; ?></i>
				</td>
				<td style="text-align: left;padding-left:<?php echo $vo['level']*20; ?>px">
				<?php $__FOR_START_11904__=0;$__FOR_END_11904__=$vo['level'];for($i=$__FOR_START_11904__;$i < $__FOR_END_11904__;$i+=1){ ?>
					├
				<?php } ?>
				<?php echo $vo['title']; ?>
				</td>
			</tr>
				<?php if(is_array($vo['children']) || $vo['children'] instanceof \think\Collection || $vo['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			<tr>
				<td>
					<?php if($vo['check'] == 'on'): ?>
					<input type="checkbox" name="menu[]" lay-skin="primary" value="<?php echo $vo['id']; ?>" checked>
					<?php else: ?>
					<input type="checkbox" name="menu[]" lay-skin="primary" value="<?php echo $vo['id']; ?>" >
					<?php endif; ?>
				</td>
				<td>
					<i class="layui-icon" style="font-size: 18px; color: #5FB878;"><?php echo $vo['icon']; ?></i>
				</td>
				<td style="text-align: left;padding-left:<?php echo $vo['level']*40; ?>px">
					<?php $__FOR_START_28061__=0;$__FOR_END_28061__=$vo['level'];for($i=$__FOR_START_28061__;$i < $__FOR_END_28061__;$i+=1){ ?>
					├
					<?php } ?>
					<?php echo $vo['title']; ?>
				</td>
			</tr>
				<?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
		</table>
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button class="layui-btn" lay-submit="" lay-filter="upCourse">立即提交</button>
			</div>
		</div>
	</div>
	<input	type="hidden" name="groupid" value="<?php echo $groupid; ?>"/>
</form>
	<script type="text/javascript" src="/public/layui/layui.js"></script>
	<script type="text/javascript" src="/public/manage/js/power/menulist.js"></script>
</body>
</html>