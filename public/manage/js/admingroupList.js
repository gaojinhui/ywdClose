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
        ,url:'/manage/admin_contorller/getadmingrouplist?type='+GetQueryString('type')
        ,cellMinWidth:80
        ,cols: [[
            {type:'checkbox'},
            {field:'id', width:80, title: 'ID', sort: true}
            ,{field:'title', title: '分组名称',width:180}
                     // ,{field:'images', title: '图片',  minWidth: 100,  unresize: true}
            ,{field:'lock',  title: '操作', width:200, toolbar: '#barDemo'}
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
       if(obj.event === 'del'){
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
              title: '编辑'
              ,content: "<input type='hidden' id='id' value='"+data.id+"'/><input type='text' id='titleName' placeholder='请输入分组名称 ' value='"+data.title+"' style='    height: 34px;padding-left: 10px;border: 1px solid #aaa;border-radius: 5px;'>",
              yes: function(index, layero){
                    $.post('/manage/admin_contorller/powerid', {id:$('#id').val(),name:$('#titleName').val()}, function(data){
                            layer.close(index);
                            if(data.error_code==0){
                                layer.msg('操作成功', {icon: 6,time:1500},function(){
                                    location.reload();
                                });
                            }else{
                                layer.msg('操作失败', {icon: 5,time:1500});
                            }
                    })
              }
            });  
        } else if(obj.event === 'power'){
           layer.open({
                 type: 2,
                title:"权限编辑",
                skin: 'layui-layer-demo', //样式类名
                anim: 2,
                area: ['100%', '95%'],
                shadeClose: true, //开启遮罩关闭
                maxmin: true,
                content: "/manage/admin_contorller/powermenu?id="+data.id
            });
        }
    });

   active = {
        addData: function(){
            layer.open({
              title: '添加分组'
              ,content: "<input type='text' id='titleName' placeholder='请输入分组名称 ' style='    height: 34px;padding-left: 10px;border: 1px solid #aaa;border-radius: 5px;'>",
              yes: function(index, layero){
                    $.post('/manage/admin_contorller/powerid', {name:$('#titleName').val()}, function(data){
                            layer.close(index);
                            if(data.error_code==0){
                                layer.msg('操作成功', {icon: 6,time:1500},function(){
                                    location.reload();
                                });
                            }else{
                                layer.msg('操作失败', {icon: 5,time:1500});
                            }
                    })
              }
            });  
        }
    };

    $('.demoTable .layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });








})
