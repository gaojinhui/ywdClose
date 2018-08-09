$(".newMenu dl dd").click(function(){
    $('dl dd').children().removeClass("leftbg");
    $(this).children().addClass("leftbg");
})