layui.use(['form', 'layedit', 'table','jquery'], function(){
    var form = layui.form
        ,layer = layui.layer
        ,layedit = layui.layedit
        table = layui.table;
    	$ = layui.jquery;

	//创建一个编辑器

    function GetQueryString(name)
    {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }

 	var editIndex = layedit.build('news_content');
 	var addNewsArray = [],addNews;
 	form.on("submit(editNews)",function(data){
        var index = layer.load(1, {shade: [0.1,'#fff']});
         $imgurl = $(".site-demo-upload img").attr("src");
        data.field.url=$imgurl;
        $.post("/manage/index/news_update_handle",data.field,function(data){
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
