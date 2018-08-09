<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:36:"./Template/manage\menu\menulist.html";i:1531410750;}*/ ?>
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
	<blockquote class="layui-elem-quote news_search">
		<div class="layui-inline">
			<a class="layui-btn layui-btn-normal newsAdd_btn" onclick="layui.menuadd()">添加</a>
		</div>
		<button class="layui-btn layui-btn-danger" lay-submit="" lay-filter="delbtn">批量删除</button>
		</div>
		<div class="layui-inline">
			<div class="layui-form-mid layui-word-aux">批量删除数据之后将无法恢复谨慎操作</div>
		</div>
	</blockquote>
	<div class="layui-form news_list">
		<table class="layui-table layui-form" lay-skin="line" >
			<colgroup>
				<col width="5%">
				<col width="1%">
				<col width="10%">
				<col width="15%">
				<col>
				<col width="10%">
				<col width="20%">
				<col width="20%">
			</colgroup>
			<thead>
			<tr>
				<th><input type="checkbox" name="" lay-skin="primary" lay-filter="allChoose"></th>
				<th>排序</th>
				<th>图标</th>
				<th>菜单名称</th>
				<th>链接</th>
				<th>状态</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>
			<?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			<tr>
				<td><input type="checkbox" name="" lay-skin="primary" value="<?php echo $vo['id']; ?>"></td>
				<td><input style="width: 3em;" type="number" v-id="<?php echo $vo['id']; ?>" name="menuid" lay-skin="primary" onchange="layui.numord(this)" value="<?php echo $vo['menuid']; ?>"></td>
				<td>
					<i class="layui-icon" style="font-size: 18px; color: #5FB878;"><?php echo $vo['icon']; ?></i>
				</td>
				<td style="text-align: left;padding-left:<?php echo $vo['level']*20; ?>px">
				<?php $__FOR_START_25193__=0;$__FOR_END_25193__=$vo['level'];for($i=$__FOR_START_25193__;$i < $__FOR_END_25193__;$i+=1){ ?>
					├
				<?php } ?>
				<?php echo $vo['title']; ?>
				</td>
				<td><?php echo $vo['href']; ?></td>
				<td>
					<?php echo $vo['status']==1?'
					<span style="color:#090">使用中</span>
					':'
					<span style="color:#FF5722">已停用</span>
					'; ?>

				</td>
				<td>
				<a class="lay-a" href="javascript:void(0)" onclick="layui.edit('<?php echo $vo['id']; ?>')">编辑</a>
				<span class="text-explode">|</span>
				<a data-field="status" class="lay-a"  data-value="0" href="javascript:void(0)" onclick="layui.switch('<?php echo $vo['id']; ?>','<?php echo $vo['status']; ?>')"><?php echo $vo['status']==1?'禁用':'启用'; ?></a>
				<span class="text-explode">|</span>
				<a  data-field="delete" class="lay-a"  data-action="/admin/menu/del.html" href="javascript:void(0)" onclick="layui.delete('<?php echo $vo['id']; ?>')">删除</a>

				</td>
			</tr>
				<?php if(is_array($vo['children']) || $vo['children'] instanceof \think\Collection || $vo['children'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			<tr>
				<td><input type="checkbox" name="" lay-skin="primary" value="<?php echo $vo['id']; ?>"></td>
				<td><input style="width: 3em;" type="number" v-id="<?php echo $vo['id']; ?>" name="menuid" lay-skin="primary" onchange="layui.numord(this)" value="<?php echo $vo['menuid']; ?>"></td>
				<td>
					<i class="layui-icon" style="font-size: 18px; color: #5FB878;"><?php echo $vo['icon']; ?></i>
				</td>
				<td style="text-align: left;padding-left:<?php echo $vo['level']*40; ?>px">
					<?php $__FOR_START_200__=0;$__FOR_END_200__=$vo['level'];for($i=$__FOR_START_200__;$i < $__FOR_END_200__;$i+=1){ ?>
					├
					<?php } ?>
					<?php echo $vo['title']; ?>
				</td>
				<td><?php echo $vo['href']; ?></td>
				<td>
					<?php echo $vo['status']==1?'
					<span style="color:#090">使用中</span>
					':'
					<span style="color:#FF5722">已停用</span>
					'; ?>

				</td>
				<td>
					<a class="lay-a" href="javascript:void(0)" onclick="layui.edit('<?php echo $vo['id']; ?>')">编辑</a>
					<span class="text-explode">|</span>
					<a data-field="status" class="lay-a"  data-value="0" href="javascript:void(0)" onclick="layui.switch('<?php echo $vo['id']; ?>','<?php echo $vo['status']; ?>')"><?php echo $vo['status']==1?'禁用':'启用'; ?></a>
					<span class="text-explode">|</span>
					<a  data-field="delete" class="lay-a"  data-action="/admin/menu/del.html" href="javascript:void(0)" onclick="layui.delete('<?php echo $vo['id']; ?>')">删除</a>

				</td>
			</tr>
				<?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>

			</tbody>
		</table>

	</div>
	<script type="text/javascript" src="/public/layui/layui.js"></script>
	<script type="text/javascript" src="/public/manage/js/menulist.js"></script>
</body>
</html>