layui.config({
	base : "js/"
}).use(['form','layer'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		$ = layui.jquery;
	//video背景
	$(window).resize(function(){
		if($(".video-player").width() > $(window).width()){
			$(".video-player").css({"height":$(window).height(),"width":"auto","left":-($(".video-player").width()-$(window).width())/2});
		}else{
			$(".video-player").css({"width":$(window).width(),"height":"auto","left":-($(".video-player").width()-$(window).width())/2});
		}
	}).resize();
	
	//登录按钮事件
	form.on("submit(login)",function(data){
		var index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        if(!data.field.username){layer.msg('对不起，您的用户名不能为空！！！！', {time: 1500, icon:5,anim:1,shade: 0.4,});return false}
        if(!data.field.userpwd){layer.msg('对不起，您的密码不能为空！！！！', {time: 1500, icon:5,anim: 1,shade: 0.4,});return false}
        // if(!data.field.captcha){layer.msg('对不起，您的验证码不能为空', {time: 1500, icon:5,anim: 1,shade: 0.4,});return false}
       /* $.ajax({
        	url:"login/login",
        	data:{'field':data.field},
        	type:'post',
        	dataType:'json',
        	success:function(e){
        		 layer.close(index);
	            if(e.error_code==0){
	                layer.msg("登录成功，正在跳转.....", {time: 1500, icon:6,shade: 0.4},function(){
	                    window.location.href="/manage/index/index";
	                });
	            }else{
	                $(".code img").attr("src",'/captcha.html?x='+Math.random());
	                layer.msg(e.msg, {time: 1500, icon:5,anim:1,shade: 0.4});
	            }
	    }
        })*/
        $.post("login/login",data.field,function(e){
            layer.close(index);
            if(e.error_code==0){
                layer.msg("登录成功，正在跳转.....", {time: 1500, icon:6,shade: 0.4},function(){
                    window.location.href="/manage/index/index";
                });
            }else{
                $(".code img").attr("src",'/captcha.html?x='+Math.random());
                layer.msg(e.msg, {time: 1500, icon:5,anim:1,shade: 0.4});
                }
        })
        return false;
	})
})
