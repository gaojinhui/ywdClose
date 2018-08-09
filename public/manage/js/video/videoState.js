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
    table.render({
        elem: '#test'
        ,url:'/manage/video_contorller/getVideo?state=1'
        ,cellMinWidth:80
        ,cols: [[
            {field:'name', title: '课程名称'}
            ,{field:'lecturer', title: '讲师'}
            ,{field:'kname', title: '课程类别'}
            ,{field:'price', title: '价格'}
            ,{field:'rebate', title: '折扣'}
            ,{field:'createTime',title: '添加日期',templet: '#timestamp',sort: true}
            ,{field:'lock',  title: '操作', toolbar: '#barDemo',minWidth: 260,unresize: true}
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
        if(status==1){status=2}else{status=1}
        $.post("/manage/index/update_news_status",{id:obj.value,val:status},function(data){
            if(data.error_code==0){
                layer.msg("修改成功",{time:400,shade:0.1},function(){
                    dom.attr("bid",status);
                });

            }else{
                layer.msg("修改失败");
            }
        })

    });

    table.on('tool(demo)', function(obj){
        var data = obj.data;
        if(obj.event === 'shownews'){
            layer.open({
                type: 2,
                title:"课程预览",
                skin: 'layui-layer-demo', //样式类名
                closeBtn: 1, //不显示关闭按钮
                anim: 2,
                area: ['95%', '95%'],
                shadeClose: true, //开启遮罩关闭
                maxmin: true,
                content: "/manage/video_contorller/getOneVideo?id="+data.id
            });
        } else if(obj.event === 'del'){
            layer.confirm('真的拒绝通过么', function(index){
                $.post("/manage/video_contorller/saveVideoStatue",{id:data.id,val:3},function(data){
                    if(data.error_code==0){
                        layer.msg("成功",{time:400},function(){
                            obj.del();
                            layer.close(index);
                            window.location.reload();
                        });

                    }else{
                        layer.msg("失败",{time:400});
                    }
                })

            });
        }else if(obj.event === 'state'){
            layer.confirm('真的审核通过么', function(index){
                $.post("/manage/video_contorller/saveVideoStatue",{id:data.id,val:2},function(data){
                    if(data.error_code==0){
                        layer.msg("成功",{time:400},function(){
                            obj.state();
                            layer.close(index);
                            window.location.reload();
                        });

                    }else{
                        layer.msg("失败",{time:400});
                    }
                })

            });
        }  else if(obj.event === 'edit'){
            layer.open({
                type: 2,
                title:"课程编辑",
                skin: 'layui-layer-demo', //样式类名
                closeBtn: 1, //不显示关闭按钮
                anim: 2,
                area: ['95%', '100%'],
                shadeClose: true, //开启遮罩关闭
                maxmin: true,
                content: "/manage/video_contorller/updateVideo?id="+data.id,
            });
        }
    });

})
