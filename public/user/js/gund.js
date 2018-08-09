function _InitScroll(_S1,_S2,_W,_H,_T){
	return "var marqueesHeight"+_S1+"="+_H+";var stopscroll"+_S1+"=false;var scrollElem"+_S1+"=document.getElementById('"+_S1+"');with(scrollElem"+_S1+"){style.width='768px';style.height='246px';style.overflow='hidden';noWrap=true;}scrollElem"+_S1+".onmouseover=new Function('stopscroll"+_S1+"=true');scrollElem"+_S1+".onmouseout=new Function('stopscroll"+_S1+"=false');var preTop"+_S1+"=0; var currentTop"+_S1+"=0; var stoptime"+_S1+"=0;var leftElem"+_S2+"=document.getElementById('"+_S2+"');scrollElem"+_S1+".appendChild(leftElem"+_S2+".cloneNode(true));setTimeout('init_srolltext"+_S1+"()',"+_T+");function init_srolltext"+_S1+"(){scrollElem"+_S1+".scrollTop=0;setInterval('scrollUp"+_S1+"()',50);}function scrollUp"+_S1+"(){if(stopscroll"+_S1+"){return;}currentTop"+_S1+"+=1;if(currentTop"+_S1+"==(marqueesHeight"+_S1+")) {stoptime"+_S1+"+=1;currentTop"+_S1+"-=1;if(stoptime"+_S1+"=="+_T/50+") {currentTop"+_S1+"=0;stoptime"+_S1+"=0;}}else{preTop"+_S1+"=scrollElem"+_S1+".scrollTop;scrollElem"+_S1+".scrollTop +=1;if(preTop"+_S1+"==scrollElem"+_S1+".scrollTop){scrollElem"+_S1+".scrollTop=0;scrollElem"+_S1+".scrollTop +=0;}}}";
}


$(document).ready(function(){
	$("#yrb").hover(function(){
		$(this).find(".tp01").toggle();
		$(this).find(".tp02").toggle();
		$(this).find("input").toggleClass("input_btn");
	});	
	$(".financial_fw .con dd").hover(function(){
		$(this).find(".con_js").slideToggle("slow");
	});
	$(".floor_c li").hover(function(){
		$(this).find("span").animate({left:'-112px'},"slow");
		},function(){
			$(this).find("span").animate({left:'110px'},"slow");
			
	});
	$(".service_phone").hover(function(){
		$(this).find("span").animate({left:'-142px'},"slow");
		},function(){
			$(this).find("span").animate({left:'140px'},"slow");
			
	});
});

jQuery(document).ready(function($) {
try{
	var f1 = $('.jumbotron[data-slide="1"]').offset().top;
	var fs = $('.navigation').children().size();
	var fss = new Array();
	for (i = 0; i < fs; i++) {
		j = i + 1;
		fss[i] = $('.jumbotron[data-slide="' + j + '"]').offset().top;
	}
	
	
	
	$(window).scroll(function(){
		var currentTOP = $(window).scrollTop();
		if(currentTOP>f1-300){
			$("#skipfloor").show();
		}else{
			$("#skipfloor").hide();
		}
		if (currentTOP <= f1) {
			$('.navigation a').removeClass('sel');
			$('.navigation a[data-slide="1"]').addClass('sel');
			return;
		}else{
			changefl(getFloor(currentTOP));
		}
	});
	
	
	function getFloor(fh){
		if(fs==0||fh<=f1){
			return 1;
		}
		if(fh>=fss[fs-1]){
			return fs;
		}
		for (k=0; k<fs;k++) {
			if(fh>fss[k]&&fh<fss[k+1]){
				return k+1;
			}
		}
	}
	
	function changefl(fno){
		$('.navigation a').removeClass('sel');
		$('.navigation a[data-slide="'+fno+'"]').addClass('sel');
	}
	
}catch(e){
	console.log(e);
}
});

function gotop() {
	$('body,html').animate({
		scrollTop : 0
	}, 800);
}
function gotofloor(thiz) {
	$("a[class='sel']").attr('class', '');
	$(thiz).attr("class", "sel");
	dataslide = $(thiz).attr('data-slide');
	var pos = $('.jumbotron[data-slide="' + dataslide + '"]').offset().top;// 获取该点到头部的距离
	$("html,body").animate({
		scrollTop : pos
	}, 800);
}