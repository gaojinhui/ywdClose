layui.use(['form', 'layedit',"upload",'jquery','layer'], function(){
    var form = layui.form;
        layer = layui.layer;
        layedit = layui.layedit;
        upload = layui.upload;
    	$ = layui.jquery;
 	form.on("submit(upCourse)",function(data){
        var index = layer.load(1, {shade: [0.1,'#fff']});
       $.post("/manage/user_contorller/update_xunlian",data.field,function(data){
            layer.close(index);
            if(data.error_code==0){
                layer.msg('操作成功', {icon: 6,time:1500},function(){
                    location.reload();
                });
            }else{
                layer.msg('操作失败', {icon: 5,time:1500});
            }
        })
 		return false;
 	})
    //选择日期查看用户训练计划
    var id=$('#id').val();
    var openid=$('#openid').val();
    form.on('select(test)', function(data){
      window.location.href='/manage/user_contorller/xunlian?id='+id+'&value='+data.value+'&openid='+openid;
    });
})
