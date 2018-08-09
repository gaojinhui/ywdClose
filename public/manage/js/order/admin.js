layui.use(['form','layer','laypage','table','jquery','laytpl'], function(){
    var $ = layui.$, form = layui.form;
    table = layui.table;
    laytpl = layui.laytpl;

//数字前置补零
    laytpl.toDateString = function(nS){
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
    };
    laytpl.toState=function(ns){
        if(ns==1){
           return '待支付';
       }else if(ns==2){
           return '已支付';
       }else if(ns==3){
           return '支付失败';
       }else if(ns==4){
            return '取消支付';
       }
    };
    laytpl.toPayment=function(ns){
        if(ns==1){
            return '支付宝支付';
        }else if(ns==2){
            return '支付宝扫码支付';
        }else if(ns==3){
            return '微信支付';
        }else if(ns==4){
            return '微信扫码支付';
        }
    };

    table.render({
        elem: '#test'
        ,url:'/manage/order_contorller/getOrderList'
        ,cellMinWidth:80
        ,cols: [[
            {field:'name', title: '用户名', },
            {field:'ordernum', title: '订单号', sort: true},
            {field:'title', title: '课程名称'},
            {field:'price', title: '价格/元'}
            ,{field:'r_state', title: '订单状态',  sort: true,templet: '#state'}
            ,{field:'r_payment', title: '支付方式',templet: '#payment'}
            ,{field:'r_strtime',  title: '订单时间',sort: true,templet: '#timestamp'}
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
                    keyword: demoReload.val(),
                    state: $("[name='state']").val(),
                    payment:$("[name='payment']").val()
                }
            });      
   });
    $('#rebate').on('click', function(){
        $("#layui-btn").css('display','block');
    });
    table.on('tool(demo)', function(obj){
        var data = obj.data;
     if(obj.event === 'edit'){
            layer.open({
                type:2,
                skin: 'layui-layer-molv', //加上边框
                area: ['50%','100%'], //宽高
                content: "/manage/order_contorller/getOneOrder?id="+data.id
            });
        }
    });

    $('.demoTable .layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
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