layui.use(['form','layer','laypage','table','jquery','laytpl'],function(){
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
        ,url:'/manage/course_contorller/getCourse'
        ,cellMinWidth:80
        ,cols: [[
            {field:'name', title: '课程名称'}
            ,{field:'lecturer', title: '讲师'}
            ,{field:'price', title: '价格'}
            ,{field:'rebate', title: '折扣'}
            ,{field:'createTime',title: '添加日期',templet: '#timestamp',sort: true}
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
                    keyword: demoReload.val()
                }
            });      
   });
    laytpl.toDateString = function(nS){
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
    };

    table.on('tool(demo)', function(obj){
        var data = obj.data;
        if(obj.event === 'shownews'){
            layer.open({
                type: 2,
                title:"课程预览",
                skin: 'layui-layer-demo', //样式类名
                closeBtn: 1, //不显示关闭按钮
                anim: 2,
                area: ['100%', '95%'],
                shadeClose: true, //开启遮罩关闭
                maxmin: true,
                content: "/manage/course_contorller/getOneCourse?id="+data.id
            });
        } else if(obj.event === 'del'){
            layer.open({
                type: 2,
                title:"课程编辑",
                skin: 'layui-layer-demo', //样式类名
                closeBtn: 1, //不显示关闭按钮
                anim: 2,
                area: ['100%', '95%'],
                shadeClose: true, //开启遮罩关闭
                maxmin: true,
                content: "/manage/course_contorller/updateCourse?id="+data.id
            });
        }
    });


    $('.demoTable .layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    active = {
        addData: function(){
            layer.open({
                type:2,
                title:"添加文章",
                skin: 'layui-layer-molv', //加上边框
                area: ['100%','100%'], //宽高
                content: "/manage/course_contorller/courseAdd",
                end:function(){
                    window.location.reload();
                }
            });
        }
    };






})
