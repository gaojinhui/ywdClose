layui.use(['form','layer','laypage','table','jquery','laytpl'], function(){
    var $ = layui.$, form = layui.form;
    table = layui.table;
    laytpl = layui.laytpl;

//数字前置补零
    laytpl.toDateString = function(nS){
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
    };

    //监听单元格编辑
 /*   table.on('edit(demo)', function(obj){
        var value = obj.value //得到修改后的值
            ,data = obj.data //得到所在行所有键值
            ,field = obj.field; //得到字段
        $.post("/manage/user_contorller/saveInfo",{id:data.uid,field:field,val:value},function(res){
            if(res.error_code==0){layer.msg('修改成功',{time:300});}else{layer.msg('修改失败',{time:300});}
        })
    });*/

    table.render({
        elem: '#test'
        ,url:'/manage/Consumer_contorller/getConsumerList'
        ,cellMinWidth:80
        ,cols: [[
            {field:'name', title: '用户名', edit: 'text'},
            {field:'username', title: '用户昵称', edit: 'text'}
            ,{field:'phone', title: '电话', edit: 'text', sort: true}
            ,{field:'reg_time',  title: '登录时间',sort: true}
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
    $('#rebate').on('click', function(){
        $("#layui-btn").css('display','block');
    });
    table.on('tool(demo)', function(obj){
        var data = obj.data;
       if(obj.event === 'del'){
            layer.confirm('真的删除行么', function(index){
                $.post("/manage/Consumer_contorller/delConsumer",{id:data.id},function(data){
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
        } else if(obj.event === 'edit'){
            layer.open({
                type:2,
                skin: 'layui-layer-molv', //加上边框
                area: ['100%','100%'], //宽高
                content: "/manage/Consumer_contorller/getOneConsumer?id="+data.id
            });
        }
    });

    function getdate(nS) {
        return new Date(parseInt(nS) * 1000).toLocaleString().substr(0,17)
    }



    $('.demoTable .layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });
    form.on('submit(submit)', function(data){

        $.post("/manage/Consumer_contorller/updateConsumer",data.field,function(data){
            if(data.error_code==0){
                layer.msg(data.msg,{icon: 1,time: 1000},function () {
                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                    parent.layer.close(index); // 关闭layer
                });
            }else{
                layer.msg(data.msg);
            }
        })
        return  false;
    });

}).define(['jquery', 'layer','form','element'],function(exports){
    $=layui.jquery;
    exports('addbanner', function(){
        layer.open({
            type:2,
            skin: 'layui-layer-molv', //加上边框
            area: ['100%','100%'], //宽高
            content: [link],
            end: function(){
                location.reload();
            }
        });
    });
    exports('edit', function(id){
        layer.open({
            type:2,
            skin: 'layui-layer-molv', //加上边框
            area: ['100%','100%'], //宽高
            content: editlink+"?id="+id,
            end: function(){
                location.reload();
            }
        });
    });

    exports('bigimg', function(e){
        var img =$(e).attr('src');
        console.log(img);
        layer.open({
            type: 1,
            title: false,
            closeBtn: 1,
            area: ['60%','95%'],
            skin: 'layui-layer-nobg', //没有背景色
            shadeClose: true,

            content: '<img src="'+img+'" style="width: 100%; height: auto" />'
        });

    });

});