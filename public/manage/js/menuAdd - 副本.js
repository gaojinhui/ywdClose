layui.use(['form','layer','jquery','layedit','laydate'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		laydate = layui.laydate,
		$ = layui.jquery;
    form.verify({
        title: function(value){
            if(value.length < 1){
                return '必须填写';
            }
        }
    });

	form.on('submit(addmenu)',function(){
		if(!$('input').val()){
            layer.msg('信息不完整，无法提交', {icon: 5});
            return false;
		}
        $.post("/manage/index/menuadd_handle",
            {
                title:$("[name='title']").val(),
                status:$("[name='status']:checked").val(),
                parentid:$("[name='parentid']").val(),
                href:$("[name='href']").val(),
                icon:$("[name='icon']").val(),
                id:$("[name='id']").val(),
                type:1,
                body:UE.getEditor('editor').getContent(),
                litpic:$('#litpic')[0].src
            },
            function(data){
                if(data.error_code==0){
                    layer.msg('操作成功', {icon: 6,time:1500},function(){
                        location.reload();
                    });
                }else{
                    layer.msg('操作失败', {icon: 5,time:1500});
                }
            });
	})

	$(".menu-icon").click(function(){
        var index = parent.layer.getFrameIndex(window.name);
        layer.open({
            type: 2,
			title:"选择图标",
            skin: 'layui-layer-rim', //加上边框
            area: ['50%', '80%'], //宽高
            anim:2,
            content:"/manage/index/showicon",
			end:function(){
                var ico = window.localStorage.getItem("ico");
                $(".menu-icon").html(ico);
                $(".layui-focus").val(ico);
                //window.location.reload();
			}
        });
	})


	
})