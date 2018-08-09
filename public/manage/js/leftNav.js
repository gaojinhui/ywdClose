function navBar(strData){
	var data;
	if(typeof(strData) == "string"){
		var data = JSON.parse(strData); //部分用户解析出来的是字符串，转换一下
	}else{
		data = strData;
	}
	var ulHtml = '<ul class="layui-nav layui-nav-tree">';
	for(var i=0;i<data.length;i++){//根据数组的长度循环取出来里面内容
		if(data[i].spread){
			ulHtml += '<li class="layui-nav-item layui-nav-itemed">';
		}else{
			ulHtml += '<li class="layui-nav-item">';
		}
		if(data[i].children != undefined && data[i].children.length > 0){//假如有二级菜单并且二级菜单至少为一个的时候
			ulHtml += '<a href="javascript:;" lay-direction="2">';
			if(data[i].icon != undefined && data[i].icon != ''){//判断菜单前面是否添加了icon图标
				if(data[i].icon.indexOf("icon-") != -1){
					ulHtml += '<i class="iconfont '+data[i].icon+'" data-icon="'+data[i].icon+'"></i>';
				}else{
					ulHtml += '<i class="layui-icon" data-icon="'+data[i].icon+'">'+data[i].icon+'</i>';
				}
			}
			ulHtml += '<cite>'+data[i].title+'</cite>';//菜单标题
			ulHtml += '<span class="layui-nav-more"></span>';
			ulHtml += '</a>';
			ulHtml += '<dl class="layui-nav-child">';
			for(var j=0;j<data[i].children.length;j++){//循环取出来菜单的二级子菜单
				if(data[i].children[j].children.length > 0){//如果存在三级菜单

					if(data[i].children[j].target == "_blank"){
						ulHtml += '<dd><a href="javascript:;" data-url="'+data[i].children[j].href+'" target="'+data[i].children[j].target+'">';
					}else{
						ulHtml += '<dd><a href="javascript:;" data-url="'+data[i].children[j].href+'">';
					}
					if(data[i].children[j].icon != undefined && data[i].children[j].icon != ''){
						if(data[i].children[j].icon.indexOf("icon-") != -1){
							ulHtml += '<i class="iconfont '+data[i].children[j].icon+'" data-icon="'+data[i].children[j].icon+'"></i>';
						}else{
							ulHtml += '<i class="layui-icon" data-icon="'+data[i].children[j].icon+'">'+data[i].children[j].icon+'</i>';
						}
					}
					ulHtml += '<cite>'+data[i].children[j].title+'</cite>';
					ulHtml += '<span class="layui-nav-more"></span>';
					ulHtml += '</a>';
					// 三级子菜单
					ulHtml += '<dl class="layui-nav-child">';
					for(var k=0;k<data[i].children[j].children.length;k++){
						if(data[i].children[j].children[k].target == "_blank"){//判断打开方式
							ulHtml += '<dd><a href="javascript:;" data-url="'+data[i].children[j].children[k].href+'" target="'+data[i].children[j].children[k].target+'">';
						}else{
							ulHtml += '<dd><a href="javascript:;" data-url="'+data[i].children[j].children[k].href+'">';
						}
						if(data[i].children[j].children[k].icon != undefined && data[i].children[j].children[k].icon != ''){//判断后台有没有设置icon图标
							if(data[i].children[j].children[k].icon.indexOf("icon-") != -1){
								ulHtml += '<i class="iconfont '+data[i].children[j].children[k].icon+'" data-icon="'+data[i].children[j].children[k].icon+'"></i>';
							}else{
								ulHtml += '<i class="layui-icon" data-icon="'+data[i].children[j].children[k].icon+'">'+data[i].children[j].children[k].icon+'</i>';
							}
						}
						ulHtml += '<cite>'+data[i].children[j].children[k].title+'</cite></a></dd>';
					}
					ulHtml += '</dl>';
					ulHtml += '</dd>';
				}else{//当三级菜单不存在
					if(data[i].children[j].target == "_blank"){
						ulHtml += '<dd><a href="javascript:;" data-url="'+data[i].children[j].href+'" target="'+data[i].children[j].target+'">';
					}else{
						ulHtml += '<dd><a href="javascript:;" data-url="'+data[i].children[j].href+'">';
					}
					if(data[i].children[j].icon != undefined && data[i].children[j].icon != ''){
						if(data[i].children[j].icon.indexOf("icon-") != -1){
							ulHtml += '<i class="iconfont '+data[i].children[j].icon+'" data-icon="'+data[i].children[j].icon+'"></i>';
						}else{
							ulHtml += '<i class="layui-icon" data-icon="'+data[i].children[j].icon+'">'+data[i].children[j].icon+'</i>';
						}
					}
					ulHtml += '<cite>'+data[i].children[j].title+'</cite></a>';
					ulHtml += '</dd>';
				}
			}
			ulHtml += "</dl>";
		}else{
			if(data[i].target == "_blank"){
				ulHtml += '<a href="javascript:;" data-url="'+data[i].href+'" target="'+data[i].target+'">';
			}else{
				ulHtml += '<a href="javascript:;" data-url="'+data[i].href+'">';
			}
			if(data[i].icon != undefined && data[i].icon != ''){
				if(data[i].icon.indexOf("icon-") != -1){
					ulHtml += '<i class="iconfont '+data[i].icon+'" data-icon="'+data[i].icon+'"></i>';
				}else{
					ulHtml += '<i class="layui-icon" data-icon="'+data[i].icon+'">'+data[i].icon+'</i>';
				}
			}
			ulHtml += '<cite>'+data[i].title+'</cite></a>';
		}
		ulHtml += '</li>';
	}
	ulHtml += '</ul>';
	return ulHtml;
}
