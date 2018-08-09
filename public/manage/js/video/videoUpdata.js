layui.use(['form', 'layedit',"upload",'jquery','layer'], function(){
    var form = layui.form;
        layer = layui.layer;
        layedit = layui.layedit;
        upload = layui.upload;
    	$ = layui.jquery;
    //自定义验证规则
    form.verify({
        price: function(value){
            var pat=/^[1-9]\d{0,5}(?:|\.\d{0,2})$/;
            if(!pat.test(value)){
                return '价格请输入小于100000并且小数点后两位的非负小数';
            }
        }, rebate: function(value){
            var pattern=/^0\.\d{0,2}$/;
            if(!pattern.test(value)){
                return '折扣请输入小于1且小数点后两位的非负小数';
            }
        },image:function(value){
            if(!value){
                return "请选择图片";
            }
        },url:function(value){
            if(!value){
                return "请填写视频地址";
            }
        },Type:function(value){
            if(!value){
                return "请选择分类";
            }
        }
    });
    upload.render({
        elem: '#test10'
        ,url: '/manage/video_contorller/upload',
        size:'6000'
        ,done: function(res){
            if(res.error_code==0){
                layer.msg('上传成功',{time:800},function(){
                    $(".site-demo-upload img").attr("src",res.data);
                    $("#image").attr("value",res.data);
                    $(".site-demo-upload img").show();
                });
            }else{
                layer.msg('上传失败',{time:800});
            }
        }
    });

 	form.on("submit(upCourse)",function(data){
        var index = layer.load(1, {shade: [0.1,'#fff']});
       $.post("/manage/video_contorller/video_update_handle",data.field,function(data){
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
	
})
