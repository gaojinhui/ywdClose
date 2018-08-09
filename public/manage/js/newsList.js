layui.use(['form','layer','jquery','laypage','table','laytpl'],function(){
    var $ = layui.$, form = layui.form;
    table = layui.table;
    upload = layui.upload;
    laytpl = layui.laytpl;

	//加载页面数据
    function GetQueryString(name)
    {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }

    //监听单元格编辑
    table.on('edit(demo)', function(obj){
        var value = obj.value //得到修改后的值
            ,data = obj.data //得到所在行所有键值
            ,field = obj.field; //得到字段
        $.post("/manage/news_contorller/update_news",{id:data.id,field:field,val:value},function(res){
            if(res.error_code==0){layer.msg('修改成功',{time:300});}else{layer.msg('修改失败',{time:300});}
        })


    });

    table.render({
        elem: '#test'
        ,url:'/manage/news_contorller/getnewslist?type='+GetQueryString('type')
        ,cellMinWidth:80
        ,cols: [[
            {type:'checkbox'},
            {field:'id', width:80, title: 'ID', sort: true}
            ,{field:'newsName', title: '标题',}
            ,{field:'newsAuthor', title: '作者', sort: true}
            ,{field:'newsLook',  title: '是否显示',templet: '#switchTpl',}
            ,{field:'newsType', title: '文章类型', sort: true}
            ,{field:'newsTime',  title: '添加日期',templet: '#timestamp',sort: true}
           // ,{field:'images', title: '图片',  minWidth: 100,  unresize: true}
            ,{field:'lock',  title: '操作', toolbar: '#barDemo'}
        ]]
         ,id: 'testReload'
        ,page: true
    });
  $('#sousuo').on('click', function(){
           var type = $(this).data('type');
            var demoReload = $('#demoReload');
            table.reload('testReload', {
                where: {
                    keyword: demoReload.val()
                }
            });      
   });
    laytpl.toDateString = function(nS){
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
    };
    form.on('switch(sexDemo)', function(obj){
        var status=$(this).attr("bid");
        var dom = $(this);
        if(status==1){
            status=2
            var text="真的要隐藏么"
        }else{
           status=1
             var text="真的要展示么"
         }
              layer.confirm(text, function(index){
                $.post("/manage/news_contorller/update_news_status",{id:obj.value,val:status},function(data){
                    if(data.error_code==0){
                        layer.msg("修改成功",{time:400,shade:0.1},function(){
                            dom.attr("bid",status);
                        });

                    }else{
                        layer.msg("修改失败");
                    }
                })
            });
    });

    table.on('tool(demo)', function(obj){
        var data = obj.data;
        if(obj.event === 'shownews'){
            layer.open({
                type: 2,
                title:"文章预览",
                skin: 'layui-layer-demo', //样式类名
                closeBtn: 1, //不显示关闭按钮
                anim: 2,
                area: ['50%', '95%'],
                shadeClose: true, //开启遮罩关闭
                maxmin: true,
                content: "/manage/news_contorller/getnews?id="+data.id
            });
        } else if(obj.event === 'del'){
            layer.confirm('真的删除行么', function(index){
                $.post("/manage/news_contorller/delnews",{id:data.id},function(data){
                    if(data.error_code==0){
                        layer.msg("删除成功",{time:400},function(){
                            obj.del();
                            layer.close(index);
                        });

                    }else{
                        layer.msg("删除失败",{time:400});
                    }
                })

            });
        } else if(obj.event === 'editnews'){
            layer.open({
                type:2,
                skin: 'layui-layer-molv', //加上边框
                area: ['100%','100%'], //宽高
                content: "/manage/news_contorller/news_update?id="+data.id
            });
        }
    });

   active = {
        addData: function(){
            layer.open({
                type:2,
                title:"添加文章",
                skin: 'layui-layer-molv', //加上边框
                area: ['100%','100%'], //宽高
                content: "/manage/news_contorller/addnews?type="+GetQueryString("type"),
                end:function(){
                    window.location.reload();
                }
            });
        }
    };

    $('.demoTable .layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });








})
