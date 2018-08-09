layui.use(['form',"table","upload","laytpl"], function(){
    var $ = layui.$, form = layui.form;
    table = layui.table;
    upload = layui.upload;
    laytpl = layui.laytpl;
    //拖拽上传
    upload.render({
        elem: '#test10'
        ,url: '/manage/index/upload',
        size:'6000'
        ,done: function(res){
           if(res.error_code==0){
               layer.msg('上传成功',{time:800},function(){
                   $(".site-demo-upload img").attr("src",res.data);
                   $(".site-demo-upload img").show();

               });
           }else{
               layer.msg('上传失败',{time:800});
           }
        }
    });


//数字前置补零


    //监听单元格编辑
    table.on('edit(demo)', function(obj){
        var value = obj.value //得到修改后的值
            ,data = obj.data //得到所在行所有键值
            ,field = obj.field; //得到字段
        $.post("/manage/index/update_handle",{id:data.id,field:field,val:value},function(res){
            if(res.error_code==0){layer.msg('修改成功',{time:300});}else{layer.msg('修改失败',{time:300});}
        })


    });

    table.render({
        elem: '#test'
        ,url:'/manage/index/get_userlist'
        ,cellMinWidth:80
        ,cols: [[
            {type:'checkbox'},
            {field:'uid', width:80, title: 'ID', sort: true}
            ,{field:'username', title: '用户名', edit: 'text'}
            ,{field:'phone', title: '电话', edit: 'text', sort: true}
            ,{field:'company',  title: '公司'}
            ,{field:'ip',  title: '登录ip'}
            ,{field:'corporation', title: '法人', sort: true, edit: 'text'}
            ,{field:'addtime',  title: '增加日期',sort: true}
            ,{field:'status', title: '状态',  minWidth: 100, templet: '#switchTpl', unresize: true}
            ,{field:'lock',  title: '操作', toolbar: '#barDemo'}
        ]]
        ,page: true
    });

    form.on('switch(sexDemo)', function(obj){
        var status=this.value;
        var bid = $(this).attr("bid");
        if(status==1){status=2}else{status=1}

        $.post("/manage/index/update_userstatus",{id:bid,val:status},function(data){
            if(data.error_code==0){
                layer.msg("修改成功");
            }else{
                layer.msg("修改失败");
            }
        })

    });

    table.on('tool(demo)', function(obj){
        var data = obj.data;
        if(obj.event === 'detail'){
            layer.open({
                type:2,
                skin: 'layui-layer-molv', //加上边框
                area: ['100%','100%'], //宽高
                content: "/manage/index/show_userinfo?id="+data.uid
            });
            //layer.msg('ID：'+ data.uid + ' 的查看操作');
        } else if(obj.event === 'del'){
            layer.confirm('真的删除行么', function(index){
                $.post("/manage/index/delbanner",{id:data.id},function(data){
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
            layer.alert('编辑行：<br>'+ JSON.stringify(data))
        }
    });

    function getdate(nS) {
        return new Date(parseInt(nS) * 1000).toLocaleString().substr(0,17)
    }



    var $ = layui.$, active = {
        getCheckData: function(){ //获取选中数据
            var checkStatus = table.checkStatus('test')
                ,data = checkStatus.data;
            layer.alert(JSON.stringify(data));
        }
        ,getCheckLength: function(){ //获取选中数目
            var checkStatus = table.checkStatus('test')
                ,data = checkStatus.data;
            layer.msg('选中了：'+ data.length + ' 个');
        }
        ,isAll: function(){ //验证是否全选
            var checkStatus = table.checkStatus('test');
            layer.msg(checkStatus.isAll ? '全选': '未全选')
        }
        ,addData: function(){
            layer.open({
                type:2,
                skin: 'layui-layer-molv', //加上边框
                area: ['100%','100%'], //宽高
                content: "/manage/index/showadduser"
            });
        }
    };

    $('.demoTable .layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });


    form.on('submit(formDemo)', function(data){
        $imgurl = $(".site-demo-upload img").attr("src");
        if(!$imgurl){layer.msg("请选择图片",{time:800});return false;}
        data.field.licenceimg=$imgurl;
        $.post("/manage/index/adduser",data.field,function(data){
            if(data.error_code==0){
                layer.msg(data.msg,{icon: 1,time: 1000},function () {
                   // window.location.reload();
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