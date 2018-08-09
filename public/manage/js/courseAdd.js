layui.use(['form', 'layedit', 'laydate','table',"upload",'jquery'], function(){
    var form = layui.form;
        layer = layui.layer;
        layedit = layui.layedit;
        laydate = layui.laydate;
      //  table = layui.table;
        upload = layui.upload;
    	$ = layui.jquery;
    //拖拽上传
    upload.render({
        elem: '#test10'
        ,url: '/manage/course_contorller/upload',
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
	//创建一个编辑器
    laydate.render({
        elem: '#test17'
        ,calendar: true
    });
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
        }

    });

    function GetQueryString(name)
    {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }

 	var editIndex = layedit.build('news_content');
 	var addNewsArray = [],addNews;
 	form.on("submit(addNews)",function(data){
        var index = layer.load(1, {shade: [0.1,'#fff']});
        $.post("/manage/course_contorller/addCourse",data.field,function(data){
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
