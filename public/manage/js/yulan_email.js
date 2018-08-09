var a=1;
var b=200;
layui.use(['form',"table","upload","laytpl"], function(){
    var $ = layui.$, form = layui.form;
    table = layui.table;
    upload = layui.upload;
    laytpl = layui.laytpl;

        function GetQueryString(name)
    {
        var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if(r!=null)return  unescape(r[2]); return null;
    }


    var addNewsArray = [],addNews;
    form.on("submit(addNews)",function(data){
        var index = layer.load(1, {shade: [0.1,'#fff']});
        data.field.type=GetQueryString("type");
        $imgurl = $(".site-demo-upload img").attr("src");
        if(!$imgurl){layer.msg("请选择图片",{time:800});return false;}
        data.field.url=$imgurl;
        $.post("/manage/user_contorller/yulan_edit",data.field,function(data){
           // layer.close(index);
            if(data.error_code==0){
                layer.msg('操作成功', {icon: 6,time:1500},function(){
                    location.reload();
                });
            }else{
                layer.msg('操作失败', {icon: 5,time:1500});
            }
        })
        return false;
    })
})
//数字前置补零
// 增加标准图片
function addstandard(i)
{
    if(a==1){
            a=i;
    }
    a=a+1;
    var html='';
    html="<div class='layui-form-item'  id='videodd"+a+"'>";
    html+="<label class='layui-form-label' style='display: block'>标准图片</label>";
    html+="<div class='site-demo-upload'>";
    html+="<div class='layui-upload-drag' id='test"+a+"'>";
    html+="<i class='layui-icon'></i>";
    html+="<p>点击上传，或将文件拖拽到此处</p>";
    html+="</div>";
    html+="<input class='layui-upload-file' type='file' name='file'>";
    html+="<input type='hidden' value='' name='litpic1[]' id='litpic"+a+"'/>";
    html+="<img id='litpica"+a+"' style='width: 300px; height: 135px; display: none;  margin-top: -60px;' src=''/>";
     html+= "<image src='/public/manage/images/cha.png' style='background-color: #ccc;border-radius: 30px;height: 30px;width: 30px;margin-left: 52px;margin-bottom: -62px;' onclick='delvideo("+a+")'>";
    html+="</div>";
    html+="</div>";
    $('#standard').append(html);
    upload.render({
        elem: '#test'+a
        ,url: '/manage/index/upload',
        size:'6000'
        ,done: function(res){
            if(res.error_code==0){
                layer.msg('上传成功',{time:800},function(){
                    $("#litpica"+a).attr("src",res.data);
                    $("#litpica"+a).show();
                    document.getElementById('litpic'+a).value=res.data;
                });
            }else{
                layer.msg('上传失败',{time:800});
            }
        }
    });
}
 function delvideo(i)
    {
        //根据节点获取当前行
        var o = document.getElementById("videodd"+i);
        o.remove();
    }
// 增加用户图片
function adduserlitpic(j)
{
    if(b==200){
        b=j;
    }
    b=b+1;
    var html='';
    html="<div class='layui-form-item'  id='litpicrrr"+b+"'>";
    html+="<label class='layui-form-label' style='display: block'>用户图片</label>";
    html+="<div class='site-demo-upload'>";
    html+="<div class='layui-upload-drag' id='test"+b+"'>";
    html+="<i class='layui-icon'></i>";
    html+="<p>点击上传，或将文件拖拽到此处</p>";
    html+="</div>";
    html+="<input class='layui-upload-file' type='file' name='file'>";
    html+="<input type='hidden' value='' name='litpic200[]' id='litpic"+b+"'/>";
    html+="<img id='litpicb"+b+"' style='width: 300px; height: 135px; display: none;  margin-top: -60px;' src=''/>";
    html+= "<image src='/public/manage/images/cha.png' style='background-color: #ccc;border-radius: 30px;height: 30px;width: 30px;margin-left: 52px;margin-bottom: -62px;' onclick='del("+b+")'>";
    html+="</div>";
    html+="</div>";
    $('#userlitpic').append(html);
     upload.render({
        elem: '#test'+b
        ,url: '/manage/index/upload',
        size:'6000'
        ,done: function(res){
            if(res.error_code==0){
                layer.msg('上传成功',{time:800},function(){
                    $("#litpicb"+b).attr("src",res.data);
                    $("#litpicb"+b).show();
                    document.getElementById('litpic'+b).value=res.data;
                });
            }else{
                layer.msg('上传失败',{time:800});
            }
        }
    });
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
