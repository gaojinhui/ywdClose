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
        ,url:'/manage/review_contorller/getReview'
        ,cellMinWidth:80
        ,cols: [[
           {field:'evaluate', title: '评论内容'},
           {field:'createTime',title: '添加日期',templet: '#timestamp',sort: true}
          
        ]]
         ,id: 'testReload'
        ,page: true
    });
    laytpl.toDateString = function(nS){
        return new Date(parseInt(nS) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');
    };

   

    $('.demoTable .layui-btn').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

})
