layui.use(['carousel', 'form','laytpl'], function(){
    var carousel = layui.carousel
        ,form = layui.form;
    layer = layui.layer;
    laytpl = layui.laytpl;
    $ = layui.$;

    carousel.render({
        elem: '#banner'
        ,width: '100%'
        ,height: '338px'
        ,interval: 4000
    });
    laytpl.toDateString = function(nS){
        return new Date(parseInt(nS) * 1000).toJSON().slice(0,10);
    };
    laytpl.subtxt = function(nS,num){
        num?num:18
        return nS.substring(0,num);
    };
    $.post("/index/index/getnewscon",{type:1,pagesize:10},function(data){
        if(data.error_code==0){
            var getTpl = cggg.innerHTML
                ,view = document.getElementById('cggg_view');
            laytpl(getTpl).render(data, function(html){
                view.innerHTML = html;
            });
        }
    })
    $.post("/index/index/getnewscon",{type:4,pagesize:4},function(data){
        if(data.error_code==0){
            var getTpl = cpdt.innerHTML
                ,view = document.getElementById('cpdt_view');
            laytpl(getTpl).render(data, function(html){
                view.innerHTML = html;
            });
        }
    })
    $.post("/index/index/getnewscon",{type:4,pagesize:4},function(data){
        if(data.error_code==0){
            var getTpl = zdhy.innerHTML
                ,view = document.getElementById('zdhy_view');
            laytpl(getTpl).render(data, function(html){
                view.innerHTML = html;
            });
        }
    })
    $.post("/index/index/getnewscon",{type:4,pagesize:4},function(data){
        if(data.error_code==0){
            var getTpl = jrgz.innerHTML
                ,view = document.getElementById('jrgz_view');
            laytpl(getTpl).render(data, function(html){
                view.innerHTML = html;
            });
        }
    })
    $.post("/index/index/getnewscon",{type:5,pagesize:10},function(data){
        if(data.error_code==0){
            var getTpl = scgg.innerHTML
                ,view = document.getElementById('scgg_view');
            laytpl(getTpl).render(data, function(html){
                view.innerHTML = html;
            });
        }
    })

    form.on('submit(demo1)', function(data){
        if(!data.field.username){layer.msg('对不起，您的用户名不能为空！！！！', {time: 1500, icon:5,anim:1,shade: 0.4,});return false}
        if(!data.field.userpwd){layer.msg('对不起，您的密码不能为空！！！！', {time: 1500, icon:5,anim: 1,shade: 0.4,});return false}
        if(!data.field.captcha){layer.msg('对不起，您的验证码不能为空', {time: 1500, icon:5,anim: 1,shade: 0.4,});return false}
        var index = layer.load(1, {shade: [0.1,'#fff']});
        $.post("index/login/login",data.field,function(e){
            layer.close(index);
            if(e.error_code==0){
                layer.msg(e.msg+"正在跳转...", {time: 1000,shade: 0.1},function(){
                    window.location.href="/user/index/show_iframe";
                });
            }else{
                $(".verification img").attr("src",'/captcha.html?x='+Math.random());
                layer.msg(e.msg, {time: 1500, icon:5,anim:1,shade: 0.1});
                return false
            }
        })
        return false;
    });

});