;layui.define("jquery",function(e){
    function posthandle(url,param){
        $.post(url,param,function(data){
            console.log(data);
        })
    }
})

