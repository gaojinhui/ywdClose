layui.use(['element','form','jquery','layer'], function(){
    var  $ = layui.jquery, form = layui.form,
        layer=layui.layer;
        element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块
    //监听导航点击
    element.on('nav(demo)', function(elem){
        //console.log(elem)
        layer.msg(elem.text());
    });
    form.on('checkbox(allChoose)', function(data){
        var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
        child.each(function(index, item){
            item.checked = data.elem.checked;
        });
        form.render('checkbox');
    });

    form.on("submit(upCourse)",function(data){
        var index = layer.load(1, {shade: [0.1,'#fff']});
        $.post("/manage/admin_contorller/grouppower",data.field,function(data){
            if(data.error_code==0){
                layer.msg('操作成功', {icon: 6,time:1500},function(){
                    
                     layer.close(index);
                });
            }else{
                layer.msg('操作失败', {icon: 5,time:1500});
            }
        })
        return false;
    });

    })
