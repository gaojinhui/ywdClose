
//全局参数  开始

var vtype = '-1', //-1原始状态，0点击出发点，1点击到达地
    areaInfo = { "province": "", "city": "", "area": "", "address": "", "add_remark": "", "lng": "", "lat": "" },
    bdmap = new BMap.Map("allmap"),
    geoc = new BMap.Geocoder(),
    setUrlTimer = null;

//全局参数  结束 

$(function () {

    Init();

});

//页面初始化方法
function Init() {

    WT.CustomSelect.Init();
    WT.TxtHodler();

    //登录判定 开始
    $.get("/Ashx/LoginForm.ashx?nowdate=" + new Date().getTime() + "&action=checklogin", function (result) {
        if (result != "-1" && result != "") {
            $("#FnoLogin").hide();
            $("#FuserName").html(result.user);
            $("#userName1").val(result.user);
            $("#FhasLogin").show();
        } else {
            $("#FnoLogin").show();
            $("#FhasLogin").hide();
        }
    }, "json");
    //登录判定 结束

    //表单验证 开始
    WT.CheckForm({ cb: "SendSubmit()" });
    WT.CheckForm({ gp: "nolog", cb: "checkFast()" });
    WT.CheckForm({ gp: "check", cb: "sendMsg(0)" });
    WT.CheckForm({ gp: "login", cb: "checkLogin()" });
    //表单验证 结束

    //发货调查 开始
    $(".fah-jy").bind("mouseover mouseout", function (e) {
        $(this).find("span").toggle();
    })

    $(window).scroll(function () {
        var scrTop = $(document).scrollTop();
        $(".fah-jy").css("top", scrTop + "px");
    });
    //发货调查 结束

    //常用信息操作   开始

    $(".top-sender,.top-receiver").click(function () {
        var topType = $(this).hasClass("top-sender") ? "sender" : "receiver";
        GetTopInfo(topType, 1);
        if (!$("#FhasLogin").is(":hidden")) {
            WT.PopupWindow({ mainBody: $(".top-list") });
        }
    });

    $(".fhxx-cyhw").click(function () {

        GetTopInfo('goods', 1);
        if (!$("#FhasLogin").is(":hidden")) {
            WT.PopupWindow({ mainBody: $(".top-list") });
        }

    });

    $("#upVLength,#upVWidth,#upVHeight").blur(function () {

        var thisVal = $.trim($(this).val());
        if (this.id != null && this.id.indexOf("upV") != -1) {
            var upLength = $("#upVLength").val();
            var upWidth = $("#upVWidth").val();
            var upHeight = $("#upVHeight").val();
            if (!isNaN(Number(upLength)) && Number(upLength) > 0 && !isNaN(Number(upWidth)) && Number(upWidth) > 0 && !isNaN(Number(upHeight)) && Number(upHeight) > 0) {
                $("#upGoodsVol").val(Number(upLength) * Number(upWidth) * Number(upHeight) / 1000000);
            }
        }
    });

    //常用信息操作   结束

    $(".nologin-login,.nologin-phone").click(NoLoginOp);

    var froms = $("#hfStartFrom").val();
    if (froms != "") {
        vtype = 0;
        SetAreaInfo((new Function("return " + froms))());
        setUrlTimer = 1;
    }

}

function SetUrlArea() {

    if (vtype == 0) {
        if ($("#hfStartFrom").val() != "") {
            if ($("#hfStartEnd").val() != "") {
                $("#hfStartFrom").val("");
                vtype = "1";
                SetAreaInfo((new Function("return " + $("#hfStartEnd").val()))());

                setUrlTimer = setTimeout(function () { $("#txtfrom").blur(); }, 500);
            }
        }
    }
    else {
        if ($("#hfStartEnd").val() != "") {
            $("#hfStartEnd").val("");

            setTimeout(function () { $("#txtto").blur(); setUrlTimer = null; }, 1000);
        }
    }

}

//退出登录 开始
function outLogin() {
    $.get("/Ashx/LoginForm.ashx?nowdate=" + new Date().getTime() + "&action=LoginOut", function (result) {
        if (result == "1") {
            $("#FnoLogin").show();
            $("#FhasLogin").hide();
        }
    });
}
//退出登陆 结束

function SendSubmit() {

    if (CheckSubmit()) {
        $.get('/WebParts/Ashx/canlogin.ashx', function (msg, status) {
            if (msg == "0") {
                $("#txtsj").val($("[name=sender_mobile]").val());

                WT.PopupWindow({ mainBody: $(".login") });

            }
            else {
                $(".fabu-btn").html("提交中...").css("background", "#ccc").unbind("click");
                WT.SubmitForm("form1");
            }
        }, 'json');
    }
}

//获取常用联系信息   开始

function GetTopInfo(infoType, pageIndex) {

    var skey = $(".fhfhr-search-key").val();
    if (skey == "") {
        $(".fhfhr-search-key").unbind("input propertychange").on("input propertychange", function () {
            GetTopInfo(infoType, 1);
        });
    }
    var topInfoUrl = "/Ashx/UserTopInfo.ashx?t=" + new Date().getTime() + "&action=get&type=" + infoType + "&p=" + pageIndex + "&skey=" + skey;
    $.get(topInfoUrl, function (infoRes) {
        if (infoRes.status == "-1") {
            var confirmStr = infoType == "sender" ? "需要登录选择常用发货人，是否要转到登录页面？" : "需要登录选择常用收货人，是否要转到登录页面？";
            if (confirm(confirmStr)) {
                document.location.replace("http://www.chinawutong.com/login.html?url=" + document.location.href);
            }
            return;
        }
        else if (infoRes.status == "-2") {
            alert("获取失败，请重试！");
        }
        else if (infoRes.status == "1") {

            var topTitle = "";
            var topInfoHtml = "";
            $(".fhr-lxr,.fhr-lxr1,.fhhwxx1,.fhhwxx,.fhhwxx-tt,.fhr-tt,.fhr-footer").remove();
            $(".fhr-fy").html("");
            if (infoType == "sender" || infoType == "receiver") {

                topInfoHtml += "<div class=\"fhr-tt\" style=\"margin: 0 auto;\">";
                topInfoHtml += "	<ul>";
                topInfoHtml += "		<li>姓&nbsp;名<\/li>";
                topInfoHtml += "		<li>电&nbsp;话<\/li>";
                topInfoHtml += "		<li>地&nbsp;址<\/li>";
                topInfoHtml += "	<\/ul>";
                topInfoHtml += "<\/div>";

                for (var i = 0; i < infoRes.topinfo.length; i++) {
                    var id = infoRes.topinfo[i].Id;
                    var name = infoRes.topinfo[i].Name;
                    var phone = infoRes.topinfo[i].Phone;
                    var sheng = infoRes.topinfo[i].Sheng;
                    var shi = infoRes.topinfo[i].Shi;
                    var xian = infoRes.topinfo[i].Xian;
                    var address = infoRes.topinfo[i].Address;
                    var lng = infoRes.topinfo[i].Lng;
                    var lat = infoRes.topinfo[i].Lat;
                    var isDefault = infoRes.topinfo[i].Default;

                    topInfoHtml += "<div class=\"fhr-lxr1\" style=\"margin: 0 auto; margin-top: 4px;\">";
                    topInfoHtml += "	<ul>";
                    topInfoHtml += "		<li style=\"width:15px;\">";
                    topInfoHtml += "		<input name=\"\" type=\"checkbox\" value=\"\" style=\"display:none\"/><\/li>";
                    topInfoHtml += "		<li style=\"width:90px;\" class=\"font-w\">" + name + "<\/li>";
                    topInfoHtml += "		<li style=\"width:120px;\" class=\"font-w\">" + phone + "<\/li>";
                    topInfoHtml += "		<li style=\"width:360px;\">" + address + "<\/li>";
                    topInfoHtml += "		<li style=\"width:30px;\"><a href=\"javascript:void(0)\" rel=\"nofollow\"><font color=\"red\">修改<\/font><\/a><\/li>";
                    topInfoHtml += "	<\/ul>";
                    if (isDefault == "1")
                        topInfoHtml += "<div class=\"fhr-label\"><font color=\"#FFFFFF\" style=\"margin-left:5px;\">默认地址<\/font><\/div>";
                    topInfoHtml += "<input name='topListSheng' type='hidden' value='" + sheng + "'> <input name='topListShi' type='hidden' value='" + shi + "'> <input name='topListXian' type='hidden' value='" + xian + "'> <input type='hidden' name='topListLng' value='" + lng + "'> <input name='topListLat' type='hidden' value='" + lat + "'><input type='hidden' name='topListId' value='" + id + "'><input type='hidden' name='topListDefault' value='" + isDefault + "'>";
                    topInfoHtml += "<\/div>";
                }
                $(".topinfo_search").after(topInfoHtml);
                if (!isNaN(infoRes.pagecount) && Number(infoRes.pagecount) > 1) {

                    WT.Pager({ panel: $(".fhr-fy"), cb: "GetTopInfo('" + infoType + "', {pi})", ps: 7, pi: pageIndex, dc: infoRes.reccount });
                }
                topTitle = infoType == "sender" ? "常用发货人" : "常用收货人";
                $(".fhxg-wz").eq(0).html(topTitle);
                $(".fhfhr-search-key").attr("txtholder", "请输入姓名、手机号或地址搜索");
                //常用信息操作事件绑定  开始
                $(".fhr-lxr,.fhr-lxr1").click(function (e) {

                    var thisDiv;
                    if (e.target.className != "fhr-lxr1" && e.target.className != "fhr-lxr") {
                        thisDiv = $(e.target).parents(".fhr-lxr1");
                        if (thisDiv.length == 0) {
                            thisDiv = $(e.target).parents(".fhr-lxr");
                        }
                    }

                    else
                        thisDiv = $(e.target);
                    var thisName = thisDiv.find("li").eq(1).text();
                    var thisPhone = thisDiv.find("li").eq(2).text();
                    var thisAddress = thisDiv.find("li").eq(3).text();
                    var thisSheng = thisDiv.find("[name=topListSheng]").val();
                    var thisShi = thisDiv.find("[name=topListShi]").val();
                    var thisXian = thisDiv.find("[name=topListXian]").val();
                    var thisLng = thisDiv.find("[name=topListLng]").val();
                    var thisLat = thisDiv.find("[name=topListLat]").val();
                    var thisId = thisDiv.find("[name=topListId]").val();
                    var thisIsDefault = thisDiv.find("[name=topListDefault]").val();

                    WT.PopupWindow({ mainBody: $(".top-list"), isClose: true });

                    if (e.target.innerHTML == "修改") {

                        $("#fhxgTit").text(infoType == "sender" ? "修改常用发货人" : "修改常用收货人");

                        $("#upTopName").val(thisName);
                        $("#upTopPhone").val(thisPhone);
                        $("#upTopAddress").val(thisAddress).blur();
                        $("#upTopId").val(thisId);
                        $("#upTopSheng").val(thisSheng);
                        $("#upTopShi").val(thisShi);
                        $("#upTopXian").val(thisXian);
                        $("#upTopLng").val(thisLng);
                        $("#upTopLat").val(thisLat);
                        if (thisIsDefault == "1") {
                            $("#upTopDefault").attr("checked", "checked");
                        }
                        else {
                            $("#upTopDefault").removeAttr("checked");
                        }

                        WT.CheckForm({ gp: "top", cb: "TopInfoOP('update', '" + infoType + "')" });

                        $(".fhxg-button2,.fhxg-pic").unbind("click").on("click", function () {
                            WT.PopupWindow({ mainBody: $(".top-edit"), isClose: true });
                            WT.PopupWindow({ mainBody: $(".top-list") });
                        });

                        WT.PopupWindow({ mainBody: $(".top-edit") });

                    }
                    else {

                        if (infoType == "sender") {
                            $("#txtfrom").val(thisAddress);
                            $("[name=sender_name]").val(thisName);
                            $("[name=sender_mobile]").val(thisPhone);
                            $("#startlng").val(thisLng);
                            $("#startlat").val(thisLat);
                            $("#startpro").val(thisSheng);
                            $("#startcity").val(thisShi);
                            $("#starttown").val(thisXian);
                            $("#start_address_remark").val("");

                            $("#txtfAddressRemark").removeClass("holder_txt").val(thisAddress);
                            $("#iconFId").attr("wtmap-data", "{p:'" + thisSheng + "',c:'" + thisShi + "',a:'" + thisXian + "',d:'" + thisAddress + "',dr:'',ln:'" + thisLng + "',la:'" + thisLat + "'}");

                            $("#txtfrom").attr("wtmap-data", "{p:'" + thisSheng + "',c:'" + thisShi + "',a:'" + thisXian + "',d:'" + thisAddress + "',dr:'',ln:'" + thisLng + "',la:'" + thisLat + "'}");

                            $("#txtfrom,[name=sender_name],[name=sender_mobile]").blur();
                        }
                        else {
                            $("#txtto").val(thisAddress);
                            $("[name=receiver_name]").val(thisName);
                            $("[name=receiver_mobile]").val(thisPhone);
                            $("#endlng").val(thisLng);
                            $("#endlat").val(thisLat);
                            $("#endpro").val(thisSheng);
                            $("#endcity").val(thisShi);
                            $("#endtown").val(thisXian);
                            $("#end_address_remark").val("");

                            $("#txttAddressRemark").removeClass("holder_txt").val(thisAddress);

                            $("#iconTId").attr("wtmap-data", "{p:'" + thisSheng + "',c:'" + thisShi + "',a:'" + thisXian + "',d:'" + thisAddress + "',dr:'',ln:'" + thisLng + "',la:'" + thisLat + "'}");
                            $("#txtto").attr("wtmap-data", "{p:'" + thisSheng + "',c:'" + thisShi + "',a:'" + thisXian + "',d:'" + thisAddress + "',dr:'',ln:'" + thisLng + "',la:'" + thisLat + "'}");

                            $("#txtto,[name=receiver_name],[name=receiver_mobile]").blur();
                        }

                        //if ($("#startpro").val() != "" && $("#endpro").val() != "")
                        //    GetCarData(1);
                    }

                });
                $(".fhr-lxr,.fhr-lxr1").bind("mouseover mouseout", function (e) {
                    if (e.type == "mouseover")
                        this.className = "fhr-lxr";
                    else
                        this.className = "fhr-lxr1";
                });
                $("#btnAddTop").unbind("click").bind("click", function () {

                    $("#divUpdateTop").find("input").val("").removeAttr("checked").removeAttr("wtmap-data");
                    $("#upTopAddress").val($("#upTopAddress").attr("txtholder")).addClass("holder_txt");

                    $(".fhxg-button2,.fhxg-pic").unbind("click").on("click", function () {
                        WT.PopupWindow({ mainBody: $(".top-edit"), isClose: true });
                        WT.PopupWindow({ mainBody: $(".top-list") });
                    });

                    WT.CheckForm({ gp: "top", cb: "TopInfoOP('add', '" + infoType + "')" });

                    WT.PopupWindow({ mainBody: $(".top-list"), isClose: true });
                    $("#fhxgTit").text(infoType == "sender" ? "添加常用发货人" : "添加常用收货人");
                    WT.PopupWindow({ mainBody: $(".top-edit") });
                });
                //常用信息操作事件绑定  结束
            }
            else if (infoType == "goods") {
                topInfoHtml += "<div class=\"fhhwxx-tt\" style=\" margin:0 auto;\">";
                topInfoHtml += "	<ul>";
                topInfoHtml += "		<li style=\" width: 170px; \">名称<\/li>";
                topInfoHtml += "		<li style=\" width: 90px; \">单件重量<\/li>";
                topInfoHtml += "		<li style=\" width: 60px; \">长(cm)<\/li>";
                topInfoHtml += "		<li style=\" width: 60px; \">宽(cm)<\/li>";
                topInfoHtml += "		<li style=\" width: 60px; \">高(cm)<\/li>";
                topInfoHtml += "		<li style=\" width: 100px; \">单件体积(m³)<\/li>";
                topInfoHtml += "		<li style=\" width: 50px; \">类型<\/li>";
                topInfoHtml += "	<\/ul>";
                topInfoHtml += "<\/div>";

                for (var i = 0; i < infoRes.topinfo.length; i++) {
                    var id = infoRes.topinfo[i].Id;
                    var name = infoRes.topinfo[i].Name;
                    var weight = infoRes.topinfo[i].Weight;
                    var length = infoRes.topinfo[i].Length;
                    var width = infoRes.topinfo[i].Width;
                    var height = infoRes.topinfo[i].Height;
                    var unit = infoRes.topinfo[i].Unit;
                    var vol = infoRes.topinfo[i].Vol;
                    var type = infoRes.topinfo[i].Type;
                    var isDefault = infoRes.topinfo[i].Default;

                    var weightStr = "无", lengthStr = "无", widthStr = "无", heightStr = "无", volStr = "无";
                    if (!isNaN(Number(weight)) && Number(weight) > 0) {
                        weightStr = unit == "1" ? (Number(weight) / 1000) + "吨" : weight + "公斤";
                    }
                    if (!isNaN(Number(length)) && Number(length) > 0) {
                        lengthStr = length;
                    }
                    if (!isNaN(Number(width)) && Number(width) > 0) {
                        widthStr = width;
                    }
                    if (!isNaN(Number(height)) && Number(height) > 0) {
                        heightStr = height;
                    }
                    if (!isNaN(Number(vol)) && Number(vol) > 0) {
                        volStr = vol;
                    }

                    topInfoHtml += "<div class=\"fhhwxx1\" style=\"margin:0 auto; margin-top:4px;\">";
                    topInfoHtml += "         <ul>";
                    topInfoHtml += "		  <li>";
                    topInfoHtml += "		  <input name=\"cbTopGoods\" type=\"checkbox\" style=\"border: 0px;\"/><\/li>";
                    topInfoHtml += "          <li style=\"width:170px;\">" + name + "<\/li>";
                    topInfoHtml += "          <li style=\"width:90px;\">" + weightStr + "<\/li>";
                    topInfoHtml += "          <li style=\"width:60px;\">" + lengthStr + "<\/li>";
                    topInfoHtml += "          <li style=\"width:60px;\">" + widthStr + "<\/li>";
                    topInfoHtml += "          <li style=\"width:60px;\">" + heightStr + "<\/li>";
                    topInfoHtml += "          <li style=\"width:100px;\">" + volStr + "<\/li>";
                    topInfoHtml += "          <li style=\"width:60px;\">" + (type == "0" ? "规则" : "不规则") + "<\/li>";
                    topInfoHtml += "          <li style=\"width:30px;\"><a href=\"javascript:void(0)\" rel=\"nofollow\"><font color=\"red\">修改<\/font><\/a><\/li>";
                    topInfoHtml += "      <\/ul>";
                    if (isDefault == "1")
                        topInfoHtml += "<div class=\"fhr-label\"><font color=\"#FFFFFF\" style=\"margin-left:5px;\">默认货物<\/font><\/div>";
                    topInfoHtml += "<input type='hidden' name='topListId' value='" + id + "'><input type='hidden' name='topListUnit' value='" + unit + "'><input type='hidden' name='topListDefault' value='" + isDefault + "'>";
                    topInfoHtml += "<\/div>";

                }
                $(".topinfo_search").after(topInfoHtml);
                if (!isNaN(infoRes.pagecount) && Number(infoRes.pagecount) > 1) {

                    WT.Pager({ panel: $(".fhr-fy"), cb: "GetTopInfo('" + infoType + "', {pi})", ps: 7, pi: pageIndex, dc: infoRes.reccount });
                }
                $(".fhxg-wz").eq(0).html("常用货物信息");
                $(".fhfhr-search-key").attr("txtholder", "请输入货物名称搜索");
                $(".fhr-fy").after("<div class=\"fhr-footer\"><a href=\"javascript:void(0)\"><input type=\"button\" class=\"fhr-button1\" style=\"border:0px;\"><\/a><a href=\"javascript:void(0)\"><input type=\"button\" class=\"fhr-button2\" style=\"border:0px;\"><\/a><\/div>");
                $(".fhhwxx1,.fhhwxx").click(function (e) {

                    var thisDiv;
                    if (e.target.className != "fhhwxx1" && e.target.className != "fhhwxx") {
                        thisDiv = $(e.target).parents(".fhhwxx1");
                        if (thisDiv.length == 0) {
                            thisDiv = $(e.target).parents(".fhhwxx");
                        }
                    }
                    else
                        thisDiv = $(e.target);
                    var thisName = thisDiv.find("li").eq(1).text();
                    var thisWeight = thisDiv.find("li").eq(2).text().replace("吨", "").replace("公斤", "");
                    var thisUnit = thisDiv.find("[name=topListUnit]").val();
                    var thisLength = thisDiv.find("li").eq(3).text();
                    var thisWidth = thisDiv.find("li").eq(4).text();
                    var thisHeight = thisDiv.find("li").eq(5).text();
                    var thisVol = thisDiv.find("li").eq(6).text();
                    var thisType = thisDiv.find("li").eq(7).text();
                    var thisId = thisDiv.find("[name=topListId]").val();
                    var thisIsDefault = thisDiv.find("[name=topListDefault]").val();


                    if (e.target.innerHTML == "修改") {

                        $("#hwxgTit").text("修改常用货物信息");
                        $("#dtVolTit").html("单件体积(m³)：");
                        $("#upGoodsId").val(thisId);
                        $("#upGoodsName").val(thisName);
                        if (thisWeight != "无")
                            $("#upGoodsWeight").val(thisWeight);
                        if (thisLength != "无") {
                            $("#upVLength").val(thisLength).blur();
                        }

                        if (thisWidth != "无") {
                            $("#upVWidth").val(thisWidth).blur();
                        }
                        if (thisHeight != "无") {
                            $("#upVHeight").val(thisHeight).blur();
                        }

                        if (thisVol != "无")
                            $("#upGoodsVol").val(thisVol);

                        $("#upGoodsUnit").prev().children("ul").children(":contains('" + (thisUnit == "1" ? "吨" : "公斤") + "')").click();
                        $("#upGoodsType").prev().children("ul").children().eq(thisType == "规则" ? 0 : 1).click();

                        if (thisIsDefault == "1") {
                            $("#upGoodsDefault").attr("checked", "checked");
                        }
                        else {
                            $("#upGoodsDefault").removeAttr("checked");
                        }

                        $(".fhxg-button2,.fhxg-pic").unbind("click").on("click", function () {
                            WT.PopupWindow({ mainBody: $(".top-edit-goods"), isClose: true });
                            $("#upGoodsName,#upGoodsWeight,#upGoodsVol").val("");
                            WT.PopupWindow({ mainBody: $(".top-list") });
                        });

                        WT.PopupWindow({ mainBody: $(".top-list"), isClose: true });
                        WT.PopupWindow({ mainBody: $(".top-edit-goods") });

                        WT.CheckForm({ gp: "topgoods", cb: "TopInfoOP('update', '" + infoType + "')" });
                    }
                    else {
                        var thisCb = thisDiv.find("[name=cbTopGoods]");
                        if (!thisCb.is(":checked"))
                            thisCb.attr("checked", "checked");
                        else
                            thisCb.removeAttr("checked");
                    }

                });
                $(".fhhwxx,.fhhwxx1").mouseover(function () {
                    this.className = "fhhwxx";
                });
                $(".fhhwxx,.fhhwxx1").mouseout(function () {
                    this.className = "fhhwxx1";
                });
                $(".fhr-button1").click(function () {
                    if ($("[name=cbTopGoods]:checked").length > 0) {

                        //$(".tb-goods-list .goods-list").remove();

                        if ($(".tb-goods-list .goods-list").length == 5) {
                            alert("已添加5种货物，不可继续增加！");
                            return;
                        }
                        else {
                            //先删除未填写货物信息行
                            $(".tb-goods-list .goods-list").each(function () {
                                if ($(this).find(".reset_el").eq(0).val() == "") {
                                    var delBtn = $(this).find(".pointer:contains('删除')");
                                    if (delBtn.length > 0) {
                                        delBtn.click();
                                    }
                                    else {
                                        $(this).remove();
                                    }
                                }
                            });

                            $("[name=cbTopGoods]:checked").each(function () {

                                if ($(".tb-goods-list .goods-list").length == 5) {
                                    return;
                                }

                                var thisDiv = $(this).parents(".fhhwxx1,.fhhwxx");
                                var thisName = thisDiv.find("li").eq(1).text();
                                var thisWeight = thisDiv.find("li").eq(2).text().replace("吨", "").replace("公斤", "");
                                var thisLength = thisDiv.find("li").eq(3).text();
                                var thisWidth = thisDiv.find("li").eq(4).text();
                                var thisHeight = thisDiv.find("li").eq(5).text();
                                var thisVol = thisDiv.find("li").eq(6).text();
                                var thisType = thisDiv.find("li").eq(7).text();
                                var thisId = thisDiv.find("[name=topListId]").val();
                                var thisIsDefault = thisDiv.find("[name=topListDefault]").val();
                                var thisUnit = thisDiv.find("[name=topListUnit]").val();

                                AddGoods(thisType == "规则" ? 0 : 1);

                                var addGoodsRow = $(".goods-all-info").prev();
                                var thisInput = addGoodsRow.find(".reset_el");
                                thisInput.eq(0).val(thisName);
                                thisInput.eq(1).val("1");
                                if (thisWeight != "无") {
                                    if (!isNaN(Number(thisWeight) && Number(thisWeight) > 0)) {
                                        thisInput.eq(2).val(thisWeight);
                                        addGoodsRow.find("div.one-unit-select").find("li").eq(thisUnit).click();
                                    }
                                }
                                if (thisVol != "无") {
                                    thisInput.eq(3).val(thisVol);
                                }

                            });

                        }

                    }
                    WT.PopupWindow({ mainBody: $(".top-list"), isClose: true });
                });

                $(".fhr-button2").click(function () {
                    WT.PopupWindow({ mainBody: $(".top-list"), isClose: true });
                });

                $("[name=cbTopGoods]").click(function (e) {
                    e.stopPropagation();
                });

                $("#btnAddTop").unbind("click").bind("click", function () {
                    $("#dtVolTit").html("单件体积(m³)：");
                    $("#divUpdateTop,#divUpdateGoods").find("input").not("[readonly]").val("").blur().removeAttr("checked");


                    $(".fhxg-button2,.fhxg-pic").unbind("click").on("click", function () {
                        WT.PopupWindow({ mainBody: $(".top-edit-goods"), isClose: true });
                        WT.PopupWindow({ mainBody: $(".top-list") });
                    });

                    $("#hwxgTit").text("添加常用货物信息");

                    WT.PopupWindow({ mainBody: $(".top-list"), isClose: true });
                    WT.PopupWindow({ mainBody: $(".top-edit-goods") });

                    WT.CheckForm({ gp: "topgoods", cb: "TopInfoOP('add', '" + infoType + "')" });

                });


            }

            if ($(".fhfhr-search-key").val() == "") {//初次加载显示holder文本
                $(".fhfhr-search-key").blur();
            }
        }
    }, 'json');
}

function TopInfoOP(action, type) {
    var topInfoUrl = "";
    if (type == "sender" || type == "receiver") {
        var name = $("#upTopName").val();
        var phone = $("#upTopPhone").val();
        var address = $("#upTopAddress").val();
        var sheng = $("#upTopSheng").val();
        var shi = $("#upTopShi").val();
        var xian = $("#upTopXian").val();
        var lng = $("#upTopLng").val();
        var lat = $("#upTopLat").val();
        var id = $("#upTopId").val();
        var isdefault = $("#upTopDefault").is(":checked") ? "1" : "0";

        if (name == "") {
            alert("请输入姓名！");
            return;
        }
        else if (phone == "") {
            alert("请输入手机号码！");
            return;
        }
        else if (address == "") {
            alert("请选择地址信息！");
            return;
        }
        else {
            var telReg = !!phone.match(/^(13[0-9]|15[012356789]|17[0678]|18[0-9]|14[57])[0-9]{8}$/);
            if (telReg == false) {
                alert("请输入正确的手机号！");
                return;
            }
        }

        topInfoUrl = "/Ashx/UserTopInfo.ashx?action=" + action + "&type=" + type + "&name=" + name + "&phone=" + phone + "&sheng=" + sheng + "&shi=" + shi + "&xian=" + xian + "&address=" + address + "&lng=" + lng + "&lat=" + lat + "&id=" + id + "&default=" + isdefault;
    }
    else {
        var name = $("#upGoodsName").val();
        var weight = $("#upGoodsWeight").val();
        var unit = $("#upGoodsUnit").val();
        var length = $("#upVLength").val();
        var width = $("#upVWidth").val();
        var height = $("#upVHeight").val();
        var vol = $("#upGoodsVol").val();
        var goodstype = $("#upGoodsType").val();
        var isDefault = $("#upGoodsDefault").is(":checked") ? "1" : "0";
        var id = $("#upGoodsId").val();

        topInfoUrl = "/Ashx/UserTopInfo.ashx?action=" + action + "&type=" + type + "&name=" + name + "&weight=" + weight + "&unit=" + unit + "&length=" + length + "&width=" + width + "&height=" + height + "&vol=" + vol + "&goodstype=" + goodstype + "&id=" + id + "&default=" + isDefault;
    }

    if (topInfoUrl != "") {
        $.get(topInfoUrl, function (retJson) {
            if (retJson.status == "2") {
                alert("添加成功！");
                GetTopInfo(type, 1);
            }
            else if (retJson.status == "3") {
                alert("更新成功！");

                GetTopInfo(type, 1);
            }
            else {
                alert("操作失败！");
            }
            if (type == "goods")
                WT.PopupWindow({ mainBody: $(".top-edit-goods"), isClose: true });
            else
                WT.PopupWindow({ mainBody: $(".top-edit"), isClose: true });
            WT.PopupWindow({ mainBody: $(".top-list") });

        }, "json");
    }
}


//获取常用联系信息   结束

//未登录下单  开始
function checkFast() {
    //快速发布自动登录
    var address;
    if ($("[name=txt_select-city]"))
    { address = $("[txt_select-city]").val(); }
    if (address == "") {
        address = $("#txtfrom").val();
    }
    $.post("/ashx/fh_login.ashx", { type: "0", code: $("#pnum").val(), tel: $("#txtsj").val(), sendname: $("[name=sender_name]").val(), fhArea: address },
    //$.post("/ashx/fh_login.ashx", { type: "0", code: $("#pnum").val() },
       function (data) {
           if (data == "1") {
               WT.SubmitForm("form1");
           }
           else if (data == "2") {
               alert("验证码错误!");
           }
           else if (data == "0") {
               alert("验证码过期");
           }
           else if (data == "-1") {
               alert("验证码不存在");
           }
       });
}

function NoLoginOp() {
   
    var bool1 = $("#imgage1").attr("src") == "http://img.chinawutong.com/images/login-line.jpg" && $(this).text() == "登录下单";
    var bool2 = $("#imgage1").attr("src") == "http://img.chinawutong.com/images/login-line1.jpg" && $(this).text() == "快速下单";
    if (bool1 || bool2) {
        $("#loginks,#logindl").toggle();
        $(".nologin-phone,.nologin-login").css("color", "#666");

        if ($(this).text() == "快速下单") {
            $(".nologin-phone").css("color", "#085bc2");
            $("#imgage1").attr("src", "http://img.chinawutong.com/images/login-line.jpg");
        }
        else {
            $(".nologin-login").css("color", "#085bc2");
            $("#imgage1").attr("src", "http://img.chinawutong.com/images/login-line1.jpg");
        }
    }
}

//获取验证码
function sendMsg(type) {
    var time = 60;
    var uName = $("[name=txtfaPer]").val();
    $.post("/ashx/GetPhoneCode1.ashx?SmsType=" + (type == 1 ? "voice" : "0") + "&name=" + escape(uName) + "&fun=" + type, { PhoneNum: $("#txtsj").val() },
       function (data) {
           if (data == "4") {

               if (type == "1") {
                   $("#div_msg1").hide();
                   $("#div_msg2").hide();
                   $("#div_msg3").show();
               }
               else if (type == "0") {
                   $("#btncode").attr("class", "phone1");
                   $("#btncode").attr({ "disabled": "disabled" });
                   $("#div_msg1").show();
                   $("#div_msg2").hide();
                   $("#div_msg3").hide();
                   var tval = setInterval(function () {
                       if (time == 0) {
                           if (type == "0") {
                               $("#div_msg1").hide();
                               //$("#div_msg2").show();
                               $("#div_msg3").hide();
                           }
                           $("#btncode").removeAttr("disabled");
                           $("#btncode").attr("class", "phone");
                           clearInterval(tval);
                           time = 60;
                       }
                       else {
                           time = time - 1;
                           $("#labmino").html(time);
                       }
                   }, 1000);
               }
           }
           else if (data == "-1") {
               alert("不能重复发送短信,请1分钟后尝试！");
           }
           else if (data == "-2") {
               alert("匿名发货太频繁，请登录或明天再试！");
           }
           else {
               alert("验证码发送失败，请联系客服！\r\n物通客服电话：400-010-5656");
           }
       });
}

function checkLogin() {
    var obj = $(".login-btn");
    var un = $("#txtUserName").val();
    var pd = $("#txtPwd").val();
    obj.html("正在登录....");
    $.post("/ashx/fh_login.ashx", { u: un, p: pd, type: "1" },
   function (data) {
       if (data == "-1") {
           $("#errorMsg2").html("不能登录!");
           $("#txtPwd").val("");
           obj.html("登录下单");
       }
       else if (data == "0") {
           $("#errorMsg2").html("用户名不存在!");
           $("#txtPwd").val("");
           obj.html("登录下单");
       }
       else if (data == "1") {
           WT.SubmitForm("form1");
       }
       else if (data == "-2") {
           $("#errorMsg2").html("用户名和密码不对应!");
           $("#txtPwd").val("");
           obj.html("登录下单");
       }
       else if (data == "-3") {
           $("#errorMsg2").html("登录次数过多，请2小时后再登录!");
           $("#txtPwd").val("");
           obj.html("登录下单");
       }
   });
}
//未登录下单  结束
var acFrom, acTo;
function BdMapAuto(objid) {
    var areaObj = $("#" + objid);
    var locationCity = "郑州市";
    if ($(".city-cut-name").length > 0) {
        locationCity = $(".city-cut-name").text();
    }
    else {
        if (objid == "txtfAddressRemark") {
            locationCity = $("#startpro").val();
        }
        else {
            locationCity = $("#endpro").val();
        }
    }

    if (objid == "txtfAddressRemark")
        acFrom = new BMap.Autocomplete({
            "input": objid,
            "location": locationCity != "" ? locationCity : "郑州市"
        });
    else
        acTo = new BMap.Autocomplete({
            "input": objid,
            "location": locationCity != "" ? locationCity : "郑州市"
        });
    var ac = objid == "txtfAddressRemark" ? acFrom : acTo;
    if (areaObj.val() != "" && areaObj.val() != areaObj.attr("txtholder")) {
        ac.setInputValue(areaObj.val());
        areaObj.removeClass("holder_txt");
    }
    else
        ac.setInputValue(areaObj.attr("txtholder"));

    ac.addEventListener("onconfirm", function (e, target) {
        var nowEl = $(document.activeElement);
        nowEl.attr("haveset", "1");
        setTimeout(function () { nowEl.blur() }, 500);
    });

    areaObj.blur(areaEv = function () {
        $(this).removeAttr("haveset");
        vtype = (this.id == "txtfrom" || this.id == "txtfAddressRemark" ? 0 : 1);
        if (this.id == "txtfrom" && $("#startpro").val() != "") {
            $("#startdetail,#startremark,#startlng,#startlat,#startpro,#startcity,#starttown").val("");
            $(this).removeAttr("wtmap-data");
        }
        else if (this.id == "txtto" && $("#endpro").val() != "") {
            $("#enddetail,#endremark,#endlng,#endlat,#endpro,#endcity,#endtown").val("");
            $(this).removeAttr("wtmap-data");
        }

        var areaKey = $(this).val();
        if (areaKey != "") {

            GetAreaInfo(areaKey, SetAreaInfo, $(this));
        }

    });
}

//使用百度搜索输入地址，获取搜索到的第一个地址并转为物通网地址格式
function GetAreaInfo(areaKey, callFun, JQObj) {
    var addressFull = areaKey;
    function getAreaDetail() {
        if (local.getResults().getPoi(0)) {
            var pp = local.getResults().getPoi(0).point;
            geoc.getLocation(pp, function (rs) {
                if (JQObj && !JQObj.attr("haveset")) {
                    JQObj.attr("haveset", "1");
                    var addComp = rs.addressComponents;
                    var areaJson = { "province": addComp.province, "city": addComp.city, "area": addComp.district, "address": addressFull, "add_remark": "", "lng": rs.point.lng, "lat": rs.point.lat };
                    areaJson = GetWTArea(areaJson);
                    if (callFun)
                        callFun(areaJson);
                }
            });
        }
        else if (areaKey && areaKey.length > 1 && JQObj && !JQObj.attr("haveset")) {
            areaKey = areaKey.substr(0, areaKey.length - 1);
            local.search(areaKey);
        }
    }
    var local = new BMap.LocalSearch(bdmap, {
        onSearchComplete: getAreaDetail
    });
    local.search(areaKey);
}

//将省市县转换为物通网省市县格式
function GetWTArea(areaJson) {
    areaJson.province = areaJson.province.substr(0, 2),
    areaJson.city = areaJson.city.substr(0, 2),
    areaJson.area = areaJson.area.substr(0, 2);
    $.ajax({
        type: "POST",
        dataType: "json",
        async: false,
        url: "/Ashx/SendGoodsHandler.ashx?t=" + new Date().getTime(),
        data: { "action": "getarea", "pro": areaJson.province, "city": areaJson.city, "area": areaJson.area },
        success: function (retJson) {
            if (retJson.status == 1) {
                areaJson.province = retJson.pro;
                areaJson.city = retJson.city;
                areaJson.area = retJson.area;
            }
        }
    });
    return areaJson;
}

function GetDistance() {
    var distance = "0";
    var startCity = $("#startcity").val(),
         startLng = $("#startlng").val(),
         startLat = $("#startlat").val(),
          endCity = $("#endcity").val(),
           endLng = $("#endlng").val(),
           endLat = $("#endlat").val();
    if (startLng != "" && startLat != "" && endLng != "" && endLat != "") {
        var url = "/ashx/SendGoodsHandler.ashx?action=distance&startLng=" + startLng + "&startLat=" + startLat + "&endLng=" + endLng + "&endLat=" + endLat + "&startCity=" + startCity + "&endCity=" + endCity;
        $.ajax({
            type: "get",
            url: url,
            async: false,
            success: function (res) {
                distance = res;
                $("#distance").val(res);
            }
        });
    }
    return distance;
}

function SetHiddenArea(address) {

    if (vtype == 0) {
        $("#startpro").val(address.province);
        $("#startcity").val(address.city);
        $("#starttown").val(address.area);
        $("#startdetail").val(address.address);
        $("#startremark").val(address.add_remark);
        $("#startlng").val(address.lng);
        $("#startlat").val(address.lat);
        if (acFrom)
            acFrom.setLocation(address.area != "市辖区" ? address.area : address.city);
    }
    else if (vtype == 1) {
        $("#endpro").val(address.province);
        $("#endcity").val(address.city);
        $("#endtown").val(address.area);
        $("#enddetail").val(address.address);
        $("#endremark").val(address.add_remark);
        $("#endlng").val(address.lng);
        $("#endlat").val(address.lat);
        if (acTo)
            acTo.setLocation(address.area != "市辖区" ? address.area : address.city);
    }
    else if (vtype == 2) {
        $("#upTopLng").val(address.lng);
        $("#upTopLat").val(address.lat);
        $("#upTopSheng").val(address.province);
        $("#upTopShi").val(address.city);
        $("#upTopXian").val(address.area);
    }
}

function AddGoods(addTopType) {

    if ($(".tb-goods-list").find(".goods-list").length > 0) {

        var checkRes = CheckGoods($(".goods-all-info").prev());
        if (typeof (checkRes) != "undefined" && !checkRes) {
            return false;
        }

    }

    var goodsNum = $(".irregular-goods,.regular-goods").length;
    var goodsTemplate = $(".goods-data-template").children().html();
    var prefix = addTopType == 1 ? "Irregular" : "Regular";
    var resetOrDel = goodsNum == 0 ? "<input type='button' class='reset_goods pointer' value='重置' />" : "<div class='fhxx-xux'><span class='h30 lh35 pointer'>删除</span></div>";
    goodsTemplate = goodsTemplate.replace("{GoodsTrClass}", addTopType == 0 ? "regular-goods" : "irregular-goods");
    goodsTemplate = goodsTemplate.replace(/{Prefix}/g, prefix).replace(/{Num}/g, goodsNum);
    goodsTemplate = goodsTemplate.replace("{ResetDel}", resetOrDel);
    $(".fhxx-jxzj:visible").remove();
    $(".goods-all-info").before(goodsTemplate);

    //最后一个隐藏添加按钮
    if (goodsNum == 4) {
        $(".fhxx-jxzj:visible").hide();
    }

    var addGoodsTr = $(".goods-all-info").prev();
    //生成自定义select
    addGoodsTr.find("select").each(function (index) {
        if (index == 0) {
            if (addTopType == 1) {
                $(this).children().removeAttr("selected").eq(1).attr("selected", "selected");
            }
            $(this).change(function () {
                var prefix = $(this).val() == "0" ? "Regular" : "Irregular";
                $(this).parents("tr").attr("class", prefix.toLocaleLowerCase() + "-goods goods-list").find("[name]").each(function () {

                    $(this).attr("name", this.name.replace("Irregular", prefix).replace("Regular", prefix));

                });

            });
        }
        WT.CustomSelect.CreateCustomEl($(this));
        if ($(this).hasClass("goods-name-select")) {
            $(this).change(function () {
                $(this).prev().prev().val($(this).find("option:selected").text());
            });
        }
    });

    addGoodsTr.find(".fhxx-zj").click(function () {

        AddGoods();

    });

    //计算货物总信息
    var CalcGoods = function () {
        var allWeight = 0, allVol = 0, allCount = 0, regularWeight = 0, regularVol = 0, irregularCount = 0;
        $(".goods-list").each(function () {

            var selectEl = $(this).find("select"),
                goodsModel = selectEl.eq(0).val(),
                goodsUnit = selectEl.eq(2).val();
            var numInput = count = $(this).find(".reset_el"),
                count = isNaN(Number(numInput.eq(1).val())) ? 0 : Number(numInput.eq(1).val()),
                weight = isNaN(Number(numInput.eq(2).val())) ? 0 : Number(numInput.eq(2).val()),
                vol = isNaN(Number(numInput.eq(3).val())) ? 0 : Number(numInput.eq(3).val());
            weight = goodsUnit == "1" ? weight * 1000 : weight;

            allWeight += weight;
            allVol += vol;
            allCount += count;
            if (goodsModel == "0") {
                regularWeight += weight;
                regularVol += vol;
            }
            else {
                irregularCount += count;
            }
        });

        $(".all_weight").text(allWeight);
        $(".all_vol").text(allVol);
        $(".all_count").text(allCount);
        $("[name=regular_weight]").val(regularWeight);
        $("[name=regular_vol]").val(regularVol);
        $("[name=irregular_count]").val(irregularCount);
        $("[name=all_vol]").val(allVol);
        $("[name=all_weight]").val(allWeight);
        $("[name=all_count]").val(allCount);

        CalcPrice();
    },

    //删除货物信息
    DelGoods = function () {

        var trEl = $(this).parents("tr");
        var addElHtm = $(".goods-all-info").prev().find(".fhxx-zj").html();
        trEl.remove();
        $(".goods-all-info").prev().find(".fhxx-zj").html(addElHtm).children().show();

        $(".irregular-goods,.regular-goods").each(function (i) {

            $(this).find("[name]").each(function () {
                this.name = this.name.replace(/_[0-4]/g, "_" + i);
            });

        });

    },
    //重置货物信息
    RetsetGoods = function () {

        var trEl = $(this).parents("tr");
        trEl.find(".reset_el").val("");

    };

    if (goodsNum > 0) {
        addGoodsTr.find(".fhxx-xux span:contains('删除')").click(DelGoods);
    }
    else {
        addGoodsTr.find(".reset_goods").click(RetsetGoods);
    }

    $(".goods-all-info").prev().find(".reset_el").blur(CalcGoods);

    $(".goods-all-info").prev().find("select").each(function (index) {
        if (index != 1) {
            $(this).change(CalcGoods);
        }
    });
}
//验证货物信息
function CheckGoods(trJqObj) {
    var checkRes = true, resMsg = "";
    var goodsModel = trJqObj.find("select.goods-model").val();
    trJqObj.find(".reset_el").each(function (index) {

        switch (index) {
            case 0:
                if ($(this).val() == "") {
                    resMsg = "请输入货物名称！";
                }
                break;
            case 1:
                if (goodsModel == 1) {
                    if ($(this).val() == "") {
                        resMsg = "请输入货物总件数！";
                    }
                    else if (isNaN(Number($(this).val())) || Number($(this).val()) <= 0) {
                        resMsg = "请输入正确的货物总件数！";
                    }
                }
                else {
                    if ($(this).val() != "" && (isNaN(Number($(this).val())) || Number($(this).val()) <= 0)) {
                        resMsg = "请输入正确的货物总件数！";
                    }
                }
                break;
            case 2:
            case 3:
                if (goodsModel == 1) {
                    if ($(this).val() != "" && (isNaN(Number($(this).val())) || Number($(this).val()) <= 0)) {
                        resMsg = "请输入正确的货物" + (index == 2 ? "总重量" : "总体积") + "！";
                    }
                }
                else {
                    if ($(this).val() == "") {
                        resMsg = "请输入货物" + (index == 2 ? "总重量" : "总体积") + "！";
                    }
                    else if (isNaN(Number($(this).val())) || Number($(this).val()) <= 0) {
                        resMsg = "请输入正确的货物" + (index == 2 ? "总重量" : "总体积") + "！";
                    }
                }
                break;
            default:
                break;
        }

        if (resMsg != "") {
            this.focus();
            return false;
        }

    });

    if (resMsg != "") {

        alert(resMsg);
        checkRes = false;
    }

    return checkRes;
}