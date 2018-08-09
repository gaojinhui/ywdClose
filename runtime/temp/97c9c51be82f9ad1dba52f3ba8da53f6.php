<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:35:"./Template/manage\menu\menuadd.html";i:1531410750;}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>文章添加--layui后台管理模板</title>
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="format-detection" content="telephone=no">
	<link rel="stylesheet" href="/public/layui/css/layui.css" media="all" />
	<script type="text/javascript" charset="utf-8" src="/public/ueditor/ueditor.config.js"></script>
</head>
<style>
	.site-doc-icon li {
		display: inline-block;
		vertical-align: middle;
		width: 127px;
		line-height: 25px;
		padding: 20px 0;
		margin-right: -1px;
		margin-bottom: -1px;
		border: 1px solid #e2e2e2;
		font-size: 14px;
		text-align: center;
		color: #666;
		transition: all .3s;
		-webkit-transition: all .3s;
	}
	.site-doc-icon li .layui-icon {
		display: inline-block;
		font-size: 30px;
	}
</style>
<body class="childrenBody">
	<form class="layui-form" action="javascript:">
		<input type="hidden" name="id" class="layui-input" value="<?php echo (isset($list['id']) && ($list['id'] !== '')?$list['id']:'false'); ?>">
		<input type="hidden" name="type" class="layui-input" value="<?php echo (isset($list['type']) && ($list['type'] !== '')?$list['type']:'false'); ?>">
		<div class="layui-form-item">
			<label class="layui-form-label">栏目类型</label>
			<div class="layui-input-block">
				<select name="menutype" lay-filter="aihao">
					<option value="1">文章</option>
					<option value="2">视频</option>
				</select>
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">菜单名字</label>
			<div class="layui-input-block">
				<input name="title" type="text" class="layui-input newsName" lay-verify="title" placeholder="输入菜单名字" value="<?php echo (isset($list['title']) && ($list['title'] !== '')?$list['title']:''); ?>">
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">默认状态</label>
			<div class="layui-input-block">
				<?php echo (isset($list['status']) && ($list['status'] !== '')?$list['status']:"1")?'
				<input type="radio" name="status" value="1" title="启用" checked>
				<input type="radio" name="status" value="0" title="禁用" >
				':'
				<input type="radio" name="status" value="1" title="启用" >
				<input type="radio" name="status" value="0" title="禁用" checked>
				'; ?>
			</div>
		</div>
		<div class="layui-form-item">
			<label class="layui-form-label">所属菜单</label>
			<div class="layui-input-block">
				<select name="parentid" lay-filter="aihao">
					<option value="<?php echo (isset($list['pid']) && ($list['pid'] !== '')?$list['pid']:''); ?>"><?php echo (isset($list['title']) && ($list['title'] !== '')?$list['title']:''); ?></option>
					<?php if(is_array($menulist) || $menulist instanceof \think\Collection || $menulist instanceof \think\Paginator): $i = 0; $__LIST__ = $menulist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
					<option value="<?php echo $vo['id']; ?>"><?php echo $vo['title']; ?></option>
					<?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</div>
		</div>
		<!-- <div class="layui-form-item">
			<label class="layui-form-label">链接</label>
			<div class="layui-input-block">
				<input type="text" name="href" class="layui-input" lay-verify="titl" placeholder="输入菜单链接"  value="<?php echo (isset($list['href']) && ($list['href'] !== '')?$list['href']:''); ?>">
			</div>
		</div> -->
		<!-- <div class="layui-form-item">
			<label class="layui-form-label">选择图标</label>
			<div class="layui-input-block">
				<i class="layui-icon menu-icon layui-form-mid" style="font-size: 30px; color:#009688"><?php echo (isset($list['icon']) && ($list['icon'] !== '')?$list['icon']:'&#xe64a;'); ?></i>
				<div class="layui-form-mid layui-word-aux">点击图标进行更换</div>
				<input type="hidden" name="icon" class="layui-input layui-focus" value="<?php echo (isset($list['icon']) && ($list['icon'] !== '')?$list['icon']:'&#xe64a;'); ?>">
			</div>
		</div> -->
		<!-- <div class="layui-form-item">
			<label class="layui-form-label" style="display: block">栏目缩略图</label>
			<div class="site-demo-upload">
				<div class="layui-upload-drag" id="test10">
					<i class="layui-icon"></i>
					<p>点击上传，或将文件拖拽到此处</p>
				</div>
				<img id="litpic" style="width: 300px; height: 135px; display: none;  margin-top: -60px;" src=""/>
			</div>
		</div> -->
		<!-- <div class="layui-form-item">
			<label class="layui-form-label">栏目简介</label>
			<div class="layui-input-block">
				<script id="editor" type="text/plain" class="layui-form-item"><?php echo (isset($data['content']) && ($data['content'] !== '')?$data['content']:''); ?></script>
			</div>
		</div> -->
		<div class="layui-form-item">
			<div class="layui-input-block">
				<button class="layui-btn" lay-submit="" lay-filter="addmenu" >立即提交</button>
				<button type="reset" class="layui-btn layui-btn-primary">重置</button>
		    </div>
		</div>
	</form>
	<!-- <div id="icon" style=" display: none">
		<ul class="site-doc-icon">
			<li>
				<i class="layui-icon"></i>
				<div class="name">播放</div>
				<div class="code">&amp;#xe652;</div>
			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">播放暂停</div>
				<div class="code">&amp;#xe651;</div>
			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">音乐</div>
				<div class="code">&amp;#xe6fc;</div>
			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">视频</div>
				<div class="code">&amp;#xe6ed;</div>
			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">语音</div>
				<div class="code">&amp;#xe688;</div>
			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">喇叭</div>
				<div class="code">&amp;#xe645;</div>
			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">对话</div>
				<div class="code">&amp;#xe611;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">设置</div>
				<div class="code">&amp;#xe614;</div>

			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">隐身-im</div>
				<div class="code">&amp;#xe60f;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">搜索</div>
				<div class="code">&amp;#xe615;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">分享</div>
				<div class="code">&amp;#xe641;</div>

			</li>

			<li>
				<i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop">ဂ</i>
				<div class="name">刷新</div>
				<div class="code">&amp;#x1002;</div>
			</li>

			<li>
				<i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop"></i>
				<div class="name">loading</div>
				<div class="code">&amp;#xe63d;</div>
			</li>

			<li>
				<i class="layui-icon layui-anim layui-anim-rotate layui-anim-loop"></i>
				<div class="name">loading</div>
				<div class="code">&amp;#xe63e;</div>
			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">设置</div>
				<div class="code">&amp;#xe620;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">引擎</div>
				<div class="code">&amp;#xe628;</div>
				<div class="fontclass">.yinqing</div>
			</li>
			<li>
				<i class="layui-icon">ဆ</i>
				<div class="name">阅卷错号</div>
				<div class="code">&amp;#x1006;</div>

			</li>
			<li>
				<i class="layui-icon">ဇ</i>
				<div class="name">错-</div>
				<div class="code">&amp;#x1007;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">报表</div>
				<div class="code">&amp;#xe629;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">star</div>
				<div class="code">&amp;#xe600;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">圆点</div>
				<div class="code">&amp;#xe617;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">客服</div>
				<div class="code">&amp;#xe606;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">发布</div>
				<div class="code">&amp;#xe609;</div>
			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">21cake_list</div>
				<div class="code">&amp;#xe60a;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">图表</div>
				<div class="code">&amp;#xe62c;</div>

			</li>
			<li>
				<i class="layui-icon">စ</i>
				<div class="name">正确</div>
				<div class="code">&amp;#x1005;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">换肤2</div>
				<div class="code">&amp;#xe61b;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">在线</div>
				<div class="code">&amp;#xe610;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">右右</div>
				<div class="code">&amp;#xe602;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">左左</div>
				<div class="code">&amp;#xe603;</div>

			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">表格</div>
				<div class="code">&amp;#xe62d;</div>
			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">icon_树</div>
				<div class="code">&amp;#xe62e;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">上传</div>
				<div class="code">&amp;#xe62f;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">添加</div>
				<div class="code">&amp;#xe61f;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">下载</div>
				<div class="code">&amp;#xe601;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">选择模版48</div>
				<div class="code">&amp;#xe630;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">工具</div>
				<div class="code">&amp;#xe631;</div>

			</li>

			<li>
				<i class="layui-icon"></i>
				<div class="name">添加</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">编辑</div>
				<div class="code">&amp;#xe642;</div>
			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">删除</div>
				<div class="code">&amp;#xe640;</div>
			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">向下</div>
				<div class="code">&amp;#xe61a;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">文件</div>
				<div class="code">&amp;#xe621;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">布局</div>
				<div class="code">&amp;#xe632;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">对勾</div>
				<div class="code">&amp;#xe618;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">添加</div>
				<div class="code">&amp;#xe608;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">么么直播－翻页</div>
				<div class="code">&amp;#xe633;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">404</div>
				<div class="code">&amp;#xe61c;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">轮播组图</div>
				<div class="code">&amp;#xe634;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">help</div>
				<div class="code">&amp;#xe607;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">代码1</div>
				<div class="code">&amp;#xe635;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">进水</div>
				<div class="code">&amp;#xe636;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">关于</div>
				<div class="code">&amp;#xe60b;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">向上</div>
				<div class="code">&amp;#xe619;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">日期</div>
				<div class="code">&amp;#xe637;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">文件</div>
				<div class="code">&amp;#xe61d;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">top</div>
				<div class="code">&amp;#xe604;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">好友请求</div>
				<div class="code">&amp;#xe612;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">对</div>
				<div class="code">&amp;#xe605;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">窗口</div>
				<div class="code">&amp;#xe638;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">表情</div>
				<div class="code">&amp;#xe60c;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">正确</div>
				<div class="code">&amp;#xe616;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">我的好友</div>
				<div class="code">&amp;#xe613;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">文件下载</div>
				<div class="code">&amp;#xe61e;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">图片</div>
				<div class="code">&amp;#xe60d;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">链接</div>
				<div class="code">&amp;#xe64c;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">记录</div>
				<div class="code">&amp;#xe60e;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">文件夹</div>
				<div class="code">&amp;#xe622;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">font-strikethrough</div>
				<div class="code">&amp;#xe64f;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">unlink</div>
				<div class="code">&amp;#xe64d;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">编辑_文字</div>
				<div class="code">&amp;#xe639;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">三角</div>
				<div class="code">&amp;#xe623;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">单选框-候选</div>
				<div class="code">&amp;#xe63f;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">单选框-选中</div>
				<div class="code">&amp;#xe643;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">居中对齐</div>
				<div class="code">&amp;#xe647;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">右对齐</div>
				<div class="code">&amp;#xe648;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">左对齐</div>
				<div class="code">&amp;#xe649;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">勾选框（未打勾）</div>
				<div class="code">&amp;#xe626;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">勾选框（已打勾）</div>
				<div class="code">&amp;#xe627;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">加粗</div>
				<div class="code">&amp;#xe62b;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">聊天 对话 IM 沟通</div>
				<div class="code">&amp;#xe63a;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">文件夹_反</div>
				<div class="code">&amp;#xe624;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">手机</div>
				<div class="code">&amp;#xe63b;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">表情</div>
				<div class="code">&amp;#xe650;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">html</div>
				<div class="code">&amp;#xe64b;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">表单</div>
				<div class="code">&amp;#xe63c;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">tab</div>
				<div class="code">&amp;#xe62a;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">emw_代码</div>
				<div class="code">&amp;#xe64e;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">字体-下划线</div>
				<div class="code">&amp;#xe646;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">三角</div>
				<div class="code">&amp;#xe625;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">图片</div>
				<div class="code">&amp;#xe64a;</div>

			</li>
			<li>
				<i class="layui-icon"></i>
				<div class="name">斜体</div>
				<div class="code">&amp;#xe644;</div>

			</li>
		</ul>
	</div> -->
	<script type="text/javascript" src="/public/layui/layui.js"></script>
	<script type="text/javascript" src="/public/manage/js/menuAdd.js"></script>
	<script type="text/javascript" src="/public/manage/js/newsAdd.js"></script>
</body>

</html>
<script>
</script>