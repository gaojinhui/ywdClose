var a=1;
var b=200;
layui.use(['form','layer','jquery','layedit','laydate'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		laypage = layui.laypage,
		layedit = layui.layedit,
		laydate = layui.laydate,
		$ = layui.jquery;
	form.on('submit(addreport)',function(data){
                    data.field.openid=$('#openid').val();
                    data.field.uid=$('#uid').val();
                    $.post("/manage/user_contorller/add_report",data.field,function(data){
                        console.log(data);
                                    if(data.error_code==0){
                                       layer.msg('上传成功',{time:800});
                                    }else{
                                        layer.msg('上传失败', {time:1500});
                                    }
                    });
                    return false;
	})
            $(".menu-icon").click(function(){
                            var openid=$('#openid').val();
                            var index = parent.layer.getFrameIndex(window.name);
                            layer.open({
                                    type: 2,
                                    title:"选择图标",
                                    skin: 'layui-layer-rim', //加上边框
                                    area: ['50%', '80%'], //宽高
                                    anim:2,
                                    content:"/manage/user_contorller/litpic?openid="+openid+"&type=1",
                            end:function(){
                                    var ico = window.localStorage.getItem("ico");
                                    if(ico !=null){
                                            $("#litpica1").attr('src',ico);
                                            $("#litpica1").show();
                                            $("#litpic1").val(ico);
                                    }
                            }
                    });
	})
            $(".menu-litpic").click(function(){
                                    var openid=$('#openid').val();
                                    var index = parent.layer.getFrameIndex(window.name);
                                    layer.open({
                                            type: 2,
                                            title:"选择图标",
                                            skin: 'layui-layer-rim', //加上边框
                                            area: ['50%', '80%'], //宽高
                                            anim:2,
                                            content:"/manage/user_contorller/litpic?openid="+openid+"&type=2",
                                    end:function(){
                                            var ico = window.localStorage.getItem("ico");
                                            if(ico !=null){
                                                    $("#litpicb200").attr('src',ico);
                                                    $("#litpicb200").show();
                                                    $("#litpic200").val(ico);
                                            }
                                    }
                            });
            })  	
})
// 增加标准图片
function addstandard()
{
    a=a+1;
    var html='';
    html="<div class='layui-form-item'  id='videodd"+a+"'>";
    html+="<label class='layui-form-label' style='display: block'>标准图片</label>";
    html+="<div class='site-demo-upload'>";
    html+="<div class='layui-upload-drag' id='test"+a+"'>";
    html+="<i class='layui-icon menu-icon"+a+"'></i>";
    html+="<p>点击上传，或将文件拖拽到此处</p>";
    html+="</div>";
    html+="<input class='layui-upload-file' type='file' name='file'>";
    html+="<input type='hidden' value='' name='litpic1[]' id='litpic"+a+"'/>";
    html+="<img id='litpica"+a+"' style='width: 300px; height: 135px; display: none;  margin-top: -60px;' src=''/>";
     html+= "<image src='/public/manage/images/cha.png' style='background-color: #ccc;border-radius: 30px;height: 30px;width: 30px;margin-left: 52px;margin-bottom: -62px;' onclick='delvideo("+a+")'>";
    html+="</div>";
    html+="</div>";
    $('#standard').append(html);
    $(".menu-icon"+a).click(function(){
                            var openid=$('#openid').val();
                            var index = parent.layer.getFrameIndex(window.name);
                            layer.open({
                                    type: 2,
                                    title:"选择图标",
                                    skin: 'layui-layer-rim', //加上边框
                                    area: ['50%', '80%'], //宽高
                                    anim:2,
                                    content:"/manage/user_contorller/litpic?openid="+openid+"&type=1",
                            end:function(){
                                    var ico = window.localStorage.getItem("ico");
                                    $("#litpica"+a).attr('src',ico);
                                    $("#litpica"+a).show();
                                    $("#litpic"+a).val(ico);
                            }
                    });
    })  
}
 function delvideo(i)
    {
        //根据节点获取当前行
        var o = document.getElementById("videodd"+i);
        o.remove();
    }
    // 增加用户图片
function adduserlitpic()
{
    b=b+1;
    var html='';
    html="<div class='layui-form-item'  id='litpicrrr"+b+"'>";
    html+="<label class='layui-form-label' style='display: block'>用户图片</label>";
    html+="<div class='site-demo-upload'>";
    html+="<div class='layui-upload-drag' id='test"+b+"'>";
    html+="<i class='layui-icon menu-litpic"+b+"'></i>";
    html+="<p>点击上传，或将文件拖拽到此处</p>";
    html+="</div>";
    html+="<input class='layui-upload-file' type='file' name='file'>";
    html+="<input type='hidden' value='' name='litpic200[]' id='litpic"+b+"'/>";
    html+="<img id='litpicb"+b+"' style='width: 300px; height: 135px; display: none;  margin-top: -60px;' src=''/>";
    html+= "<image src='/public/manage/images/cha.png' style='background-color: #ccc;border-radius: 30px;height: 30px;width: 30px;margin-left: 52px;margin-bottom: -62px;' onclick='del("+b+")'>";
    html+="</div>";
    html+="</div>";
    $('#userlitpic').append(html);
    $(".menu-litpic"+b).click(function(){
                            var openid=$('#openid').val();
                            var index = parent.layer.getFrameIndex(window.name);
                            layer.open({
                                    type: 2,
                                    title:"选择图标",
                                    skin: 'layui-layer-rim', //加上边框
                                    area: ['50%', '80%'], //宽高
                                    anim:2,
                                    content:"/manage/user_contorller/litpic?openid="+openid+"&type=2",
                            end:function(){
                                    var ico = window.localStorage.getItem("ico");
                                    $("#litpicb"+b).attr('src',ico);
                                    $("#litpicb"+b).show();
                                    $("#litpic"+b).val(ico);
                            }
                    });
    })  
}
function del(b)
    {
    //根据节点获取当前行
    var o = document.getElementById("litpicrrr"+b);
    o.remove();
    //var rowIndex = o.parentNode.parentNode.rowIndex;
    //删除一行
    //info.deleteRow(rowIndex-1);
    }