layui.use(['form','laytpl'], function(){
    var form = layui.form;
    layer = layui.layer;
    laytpl = layui.laytpl;
    $ = layui.$;

    $(".newMenu dl dt").click(function(){
        $(".newMenu dd").hide(500);
        if($(this).hasClass("bgcolor")){
            $(this).siblings().hide(500);
            $(this).removeClass("bgcolor");
        }else{

            $(".newMenu dt").removeClass("bgcolor");
            $(this).addClass("bgcolor");
            $(this).siblings().show(500);
        }
    })

    $(".newMenu dl dd a").click(function(){
        var index = layer.load({shade: [0.1,'#fff'],time:false});
        function closeindex(){
            layer.close(index);
        }
        setTimeout(closeindex(),3000)
        $(".newMenu dl dd a").css("color","#000000");
        $(this).css("color","red");
    })

})