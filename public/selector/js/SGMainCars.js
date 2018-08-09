$(function () {

    InitMainCars();

})

function InitMainCars() {

    //出发地、到达地处理 开始

    $("#txtfrom,#txtto").blur(function () {

        if ($("#WTMap_Main").length > 0) {
            return;
        }

        if (this.id == "txtfrom") {
            vtype = 0;
        }
        else {
            vtype = 1;
        }

        if ($(this).attr("wtmap-data") != undefined) {
            var elData = (new Function("return  " + $(this).attr("wtmap-data")))();
            geoc.getPoint(elData.p + elData.c + elData.a.replace("市辖区", ""), function (point) {
                if (point) {

                    areaInfo.province = elData.p;
                    areaInfo.city = elData.c;
                    areaInfo.area = elData.a;
                    areaInfo.lng = point.lng;
                    areaInfo.lat = point.lat;
                    SetAreaInfo(areaInfo);
                } else {
                    alert("您选择地址没有解析到结果!");
                }
            }, elData.c);
        }

    });

    BdMapAuto("txtfAddressRemark");
    BdMapAuto("txttAddressRemark");

    //出发地、到达地处理 结束

    //车辆需求联动 开始

    var changeEv;
    $("#choose_len_1,#choose_len,#choose_type_1,#choose_type").change(changeEv = function () {

        var otherEl;
        if (this.id.indexOf("choose_len") > -1)
            otherEl = this.id == "choose_len_1" ? $("#choose_len") : $("#choose_len_1");
        else
            otherEl = this.id == "choose_type_1" ? $("#choose_type") : $("#choose_type_1");

        otherEl.unbind("change");
        otherEl.prev().find("li:contains('" + $(this).find("option:selected").text() + "')").click();
        otherEl.bind("change", changeEv);
        if ($("#startlng").val() != "" && $("#endlng").val() != "") {
            GetCars(1);
        }

    });

    $("#sel_goods_name").change(function () {

        $("#txtGoodsName").val($(this).val());

    });

    //车辆需求联动 结束

    //信息有效期 开始
    $("[name=txtendTime]").click(function () {
        WdatePicker({ dateFmt: 'yyyy-MM-dd', alwaysUseStartDate: true, errDealMode: -1, minDate: '%y-%M-%d', onpicked: function () { this.blur(); } });
    });

    $("#cbLongDate").click(function () {
        var timeEl = $("[name=txtendTime]");
        if ($(this).is(":checked")) {
            timeEl.attr("oldT", timeEl.val()).attr("disabled", "disabled").val("长期");
        }
        else {
            $("[name=txtendTime]").removeAttr("disabled").val(timeEl.attr("oldT"));
        }

    });
    //信息有效期 结束

    //说明字数判断 开始
    $("[name=remark]").bind("keyup blur", function () {

        var max = 30;
        var ol = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
        if (ol == "") {
            $("#infoCount").text("0");
        }
        else {
            if (ol.length > max) {
                $(this).val(ol.substring(0, max));
            }
            var chars = ol.length;
            $("#infoCount").text(chars);
        }

    });
    //说明字数判断 结束

    //切换运输车辆、配货专线选项卡 开始
    $("#carListTitleId,#zcphListTitleId").click(function () {
        $(this).parent().siblings().removeClass("titl").children().removeClass("select");
        $(this).addClass("select");
        $(this).parent().addClass("titl");

        $("#carInfo,#phzxInfo").toggle();

        if (this.id == "zcphListTitleId") {
            $(".choiced-tch").click();
            $("#carInfo").hide();
            $("#phzxInfo").show();
        }
        else {
            $("#phzxInfo").hide();
            $("#carInfo").show();
        }
    });
    //切换运输车辆、配货专线选项卡 结束

    //获取运输车辆
    GetCars(1);

    //获取配货专线
    GetWlLines(1);

    //车辆位置地图关闭 
    $(".close-cc").click(function () {
        WT.PopupWindow({ mainBody: $("#ditutanchu"), isClose: true });
    });

    $("#fh_ygbx,#fh_ygbxdq").click(function () {
        if (this.id == "fh_ygbx") {
            if (!$(this).is(":checked")) {
                $("#fh_ygbxdq").attr({ "checked": false, "disabled": true });
            }
            else {
                $("#fh_ygbxdq").attr({ "disabled": false });
            }
        }
        CalcInsurance();
    });

    $("#invoiceAmt").blur(CalcInsurance);

    $("[name=goods_weight],[name=goods_vol]").blur(autoCar);
    $("[name=goods_unit]").change(autoCar);
}

function CheckSubmit() {

    if (!$("#divNeedZc").is(":hidden")) {
        if ($("#choose_len").val() == "车长") {
            alert("请选择车长！");
            return false;
        }
        if ($("#choose_type").val() == "选择车辆类型") {
            alert("请选择车辆类型！");
            return false;
        }
    }

    if ($("#fh_ygbx").is(":checked")) {
        if ($("#invoiceAmt").val() == "") {
            alert("请输入货物价值！");
            $("#invoiceAmt").focus();
            return false;
        }
        else if (isNaN(Number($("#invoiceAmt").val()))) {
            alert("请输入正确的货物价值！");
            $("#invoiceAmt").focus();
            return false;
        }
    }

    return true;

}

//获取运输车辆 开始
var getCarAjax, getLineAjax;
function GetCars(pid) {
    if (getCarAjax)
        getCarAjax.abort();
    var carFrom = $("#startpro").val() + "-" + $("#startcity").val() + "-" + $("#starttown").val(),
          carTo = $("#endpro").val() + '-' + $("#endcity").val() + '-' + $("#endtown").val(),
         carLen = $("#choose_len").val().replace("车长", "").replace("米以上", "").replace("米", ""),
        carType = $("#choose_type").val().replace("选择车辆类型", "");
    var isall = $("#startlng").val() == "" || $("#endlng").val() == "" ? "1" : "0";
    $("#carListDiv").html("<div style='text-align: center;padding: 10px 0;'>获取运输车辆信息...<br><img src='http://img.chinawutong.com/Images/loading.gif'></div>");

    if (setUrlTimer) {//链接有地址参数及选择特定车源或专线发货防止多次请求承运列表
        return;
    }

    var url = '/Ashx/fh_getZC.ashx?action=car&isall=' + isall + '&p=' + pid + '&f=' + escape(carFrom) + '&t=' + escape(carTo) + '&carLen=' + carLen + '&carType=' + escape(carType) + '&r=' + new Date().getTime();
    var hdSelectCy = $("#hdSelectCy");

    if (hdSelectCy.length > 0 && hdSelectCy.val() != "" && hdSelectCy.val() != "0")
        url = url + "&sid=" + hdSelectCy.val();
    getCarAjax = $.get(url, function (msg, status) {
        if (msg.status == "0") {//成功
            var allCarInfo = "";
            $(msg.carLine).each(function (index, item) {
                var carPosType = "", carPosIco = "";
                var carPro = item.csheng, carCity = item.cshi, carArea = item.cxian, carLng = item.lng, carLat = item.lat;
                if (carLng != "" && carLat != "") {
                    var geoc = new BMap.Geocoder();
                    geoc.getLocation(new BMap.Point(carLng, carLat), function (rs) {
                        var addComp = rs.addressComponents;
                        var oPro = carPro.substr(0, 2);
                        var oCity = carCity.substr(0, 2);
                        var nPro = addComp.province.substr(0, 2);
                        var nCity = addComp.city.substr(0, 2);
                        if (addComp.city.indexOf("直辖县级行政单位") != -1) {
                            if (nPro == "海南") {
                                if (callBackDada.address_city.indexOf("市") != -1)
                                    nCity = "海南直辖市";
                                else
                                    nCity = "海南直辖县";
                            }
                            else
                                nCity = addComp.district.substr(0, 2);
                        }
                        if (nPro != oPro || nCity != oCity) {
                            carPosType = " <strong class='fa5'>回程车</strong>丨<span id='addinfo_" + index + "' style='display:none'>" + addComp.province + addComp.city + addComp.district + "</span> ";
                        }
                        else {
                            carPosType = " <strong class='fa5'>本地车</strong>丨<span id='addinfo_" + index + "' style='display:none'>" + addComp.province + addComp.city + addComp.district + "</span> ";
                        }
                        $(".car_pos_type").eq(index).html(carPosType);
                    });

                }

                var carlen = ((item.carLength == "0" || item.carLength == "") ? "不详" : (item.carLength + "米")),
               carzaizhong = ((item.carZai == "0" || item.carZai == "") ? "不详" : (item.carZai + "吨"));
                carPosIco = carLng != "" ? "<a title='点击查看车辆位置' style='cursor: pointer;' onclick=\"ShowCarPos('" + carLng + "','" + carLat + "','" + item.carNum + "','" + item.carType + "','" + carlen + "','" + carzaizhong + "','" + item.cheZhu + "','addinfo_" + index + "','" + item.csheng + "','" + item.cshi + "','" + item.cxian + "')\"> <img src='http://www.chinawutong.com/images/clyxBtn_1.png'></a>" : "";

                var carTem = $("#car-data-temp").html();
                var imgPath = "http://img2.chinawutong.com",
                  oldBigImg = "<img src='/images/smche_b.jpg' width='200'>",
                oldSmallImg = "<img src='/images/smche.jpg' width='25' height='19'>",
                     bigImg = item.chePic != "" ? "<img src='" + imgPath + item.chePic + "' width='200'>" : oldBigImg,
                   smallImg = item.chePic != "" ? "<img src='" + imgPath +  item.chePic + "' width='25' height='19'>" : oldSmallImg;

                //{lineId}|{carId}|{custId}

                carTem = carTem.replace("{smallPic}", smallImg).replace("{bigPic}", bigImg);
                carTem = carTem.replace("{carNum}", item.carNum).replace("{carType}", item.carType);
                carTem = carTem.replace("{carLen}", carlen).replace("{carLoad}", carzaizhong);
                carTem = carTem.replace(/{carOwner}/g, item.cheZhu);
                carTem = carTem.replace("{price}", item.carPrice);
                carTem = carTem.replace("{carPosIco}", carPosIco);
                carTem = carTem.replace("{lineId}", item.id);
                carTem = carTem.replace("{carId}", item.cid);
                carTem = carTem.replace("{custId}", item.custId);

                allCarInfo += carTem;
            });

            $("#carListDiv").html(allCarInfo + "<div class='car-pager'></div>");

            WT.Pager({ panel: $(".car-pager"), cb: "GetCars({pi})", ps: 8, pi: pid, dc: msg.totalCount });

            $("#carListDiv .choice-tch").click(function () {

                $("[name=select_car]").removeAttr("checked");

                if ($(this).hasClass("choice-tch")) {
                    $(".choiced-tch").removeClass("choiced-tch").addClass("choice-tch");
                    $(this).removeClass("choice-tch").addClass("choiced-tch");
                    $(this).children().attr("checked", "checked");
                    $("#divNeedZc").hide();
                }
                else {
                    $(this).removeClass("choiced-tch").addClass("choice-tch");
                    $("#divNeedZc").show();
                }
            });

            if ($("#hdSelectCy").val() != "") {
                if ($("[name=select_car]").eq(0).val().split("|")[0] == $("#hdSelectCy").val()) {
                    $("#carListDiv .choice-tch").eq(0).click();
                }
            }

        } else {
            $("#carListDiv").html("<span class='noinfo'>没有查询到符合条件的车辆，您可以尝试选择整车配货专线！</span>");
        }
    }, 'json');
}
//获取运输车辆 结束

//获取配货专线 开始
function GetWlLines(pid) {
    if (getLineAjax)
        getLineAjax.abort();
    var zcphFrom = $("#startpro").val() + '-' + $("#startcity").val() + '-' + $("#starttown").val();
    var zcphTo = $("#endpro").val() + '-' + $("#endcity").val() + '-' + $("#endtown").val();
    var isall = $("#startpro").val() == "" || $("#endpro").val() == "" ? "1" : "0";
    $("#zcphListDiv").html("<tr><td style='text-align: center;padding: 10px 0;'>获取配货专线信息...<br><img src='http://img.chinawutong.com/Images/loading.gif'></td></tr>");

    if (setUrlTimer) {//链接有地址参数及选择特定专线发货防止多次请求承运列表
        return;
    }

    var url = '/Ashx/fh_getZC.ashx?action=zcph&isall=' + isall + '&af=' + $("#allFrom").val() + '&at=' + $("#allTo").val() + '&p=' + pid + '&f=' + escape(zcphFrom) + '&t=' + escape(zcphTo) + '&r=' + new Date().getTime();
    var hdSelectCy = $("#hdSelectCy");

    if (hdSelectCy.length > 0 && hdSelectCy.val() != "" && hdSelectCy.val() != "0")
        url = url + "&sid=" + hdSelectCy.val();
    getLineAjax = $.get(url, function (msg, status) {
        if (msg.status == "0") {//成功调取数据
            if (isall == "1") {
                $("#allFrom").val(msg.fIndex);
                $("#allTo").val(msg.tIndex);
            }
            var allLineHtml = "";
            $(msg.zcList).each(function (index, item) {
                var whideStyle = item.oprice == "面议" || item.hjt == "" ? "style='display:none'" : "";
                var lhideStyle = item.olightPrice == "面议" || item.ljt == "" ? "style='display:none'" : "";
                var isZhengzhou = false;
                if ($("#startcity").val() == "郑州市" && $("#starttown").val() == "市辖区" && $("#startlng").val() != "") {
                    isZhengzhou = true;
                }

                var wMorePrice = item.oprice == "面议" || item.hjt == "" ? "" : "<a href='javascript:void(0)' ><span class='j-blue'>阶梯价格</span></a>" + item.hjt;
                var lMorePrice = item.olightPrice == "面议" || item.ljt == "" ? "" : "<a href='javascript:void(0)' ><span class='j-blue'>阶梯价格</span></a>" + item.ljt;
                var services = "<img " + ((isZhengzhou && item.partner == 2) ? "" : " style='display:none;' ") + "  title='免费上门接货' src='http://www.chinawutong.com/images/mf-j-icon.png'>&nbsp;" + (item.send == "1" ? "<img title='支持送货上门' src='http://www.chinawutong.com/images/s-icon.png'>&nbsp;" : "") + (item.ds == "1" ? "<img title='支持代收货款' src='http://www.chinawutong.com/images/d-icon.png'>&nbsp;" : "") + (item.bjrate != "" && item.bjrate != "0" ? "<img src='http://www.chinawutong.com/images/b-icon.png' title='支持保价'>" : "");

                var lineDataTemp = $("#line-data-temp").children().html();

                lineDataTemp = lineDataTemp.replace(/{id}/g, index);
                lineDataTemp = lineDataTemp.replace("{lineId}", item.id);
                lineDataTemp = lineDataTemp.replace("{custId}", item.custid);
                lineDataTemp = lineDataTemp.replace("{comName}", item.Add_Companyname.replace("有限公司", "").replace("有限责任公司", ""));
                lineDataTemp = lineDataTemp.replace("{custId}", item.custid);
                lineDataTemp = lineDataTemp.replace("{wxtIco}", (item.wxt == "1" ? "<img src='http://img.chinawutong.com/images/wxthome.gif'>" : ""));
                lineDataTemp = lineDataTemp.replace("{partnerIco}", (item.partner != "2" ? "" : "<img class='partner_ico' title='中国物通网推荐承运商，在线下单，免费上门接货' src='http://www.chinawutong.com/images/min-tjcys.png'>"));
                lineDataTemp = lineDataTemp.replace("{weightPrice}", item.oprice);
                lineDataTemp = lineDataTemp.replace("{weightMore}", wMorePrice);
                lineDataTemp = lineDataTemp.replace("{lightPrice}", item.olightPrice);
                lineDataTemp = lineDataTemp.replace("{lightMore}", lMorePrice);
                lineDataTemp = lineDataTemp.replace("{tranTime}", item.tranTime);
                lineDataTemp = lineDataTemp.replace("{services}", services);

                allLineHtml += lineDataTemp;
            });

            WT.Pager({ panel: $(".line-pager"), cb: "GetWlLines({pi})", ps: 8, pi: pid, dc: msg.totalCount });

            $("#zcphListDiv").html(allLineHtml);

            //单选效果
            $("[name=ra_zczx]").click(function () {
                if ($(this).is(":checked")) {
                    $("[name=ra_zczx]").attr("checked", false);
                    $(this).attr("checked", true);
                    $(".choiced-tch").click();
                    //$("#ra_zccar").val("0|0|0-0|0");
                }
            });

            $(".j-blue").bind("mouseover mouseout", function (e) {
                if (e.type == "mouseover")
                    $(this).parent().next().show();
                if (e.type == "mouseout")
                    $(this).parent().next().hide();
            });

            if ($("#hdSelectCy").val() != "") {
                if ($("[name=ra_zczx]").eq(0).val().split("|")[0] == $("#hdSelectCy").val()) {

                    $("#zcphListTitleId").click();
                    $("[name=ra_zczx]").eq(0).click();
                }
            }

        } else {
            $("#zcphListDiv").html("<tr><td colspan='7'><span class='noinfo'>没有查询到符合条件的配货专线，您可以尝试选择运输车辆！</span></td></tr>");
        }

    }, 'json');
}
//获取配货专线 结束

//显示车辆当前位置地图  开始
function ShowCarPos(lng, lat, carnum, cartype, carlen, carzaizhong, carchezhu, addinfoid, pro, city, area) {
    var map, marker, point, infoWindow;
    WT.PopupWindow({ mainBody: $("#ditutanchu") });
    map = new BMap.Map("allmap12"); //弹出层的map
    map.enableScrollWheelZoom();
    point = new BMap.Point(lng, lat);
    marker = new BMap.Marker(point, { icon: new BMap.Icon("http://www.chinawutong.com/images/huoche_marker.gif", new BMap.Size(48, 48), { imageOffset: new BMap.Size(0, 0) }) });
    map.centerAndZoom(point, 11);
    map.addOverlay(marker);
    var opts = {
        width: 350,     // 信息窗口宽度
        height: 120,     // 信息窗口高度
        enableAutoPan: true, //自动平移
        title: "", // 信息窗口标题
        enableMessage: false//设置允许信息窗发送短息
    }
    infoWindow = new BMap.InfoWindow("<div style='height:22px;line-height:22px;color:#333;padding-top:5px;'><li style='float:left;width:130px;'>车牌号：<span>" + carnum + "</span></li><li style='float:left;width:110px;'>车型：<span>" + cartype + "</span></li><li style='float:left;width:100px;'><span>车长：" + carlen + "</span></li><li style='float:left;width:130px;'>载重：<span>" + carzaizhong + "</span></li><li style='float:left;'><span>发布者：" + carchezhu + "</span></li><div style='float:left;height:22px;line-height:22px;color:#333;'><p style='font-size:12px;color:#333;'>常驻地：" + pro + city + area + "</p></div><div style='clear:both;'></div><div style='float:left;height:22px;line-height:22px;color:#333;'><p style='font-size:12px;color:#333;'>当前位置：" + $("#" + addinfoid).text() + "</p></div><div style='float:left;height:22px;line-height:22px;'><p style='font-size:12px;color:#666;'>(此位置为模糊位置，待车主确认后，可实时查看车辆精确位置。)</p></div></div>", opts);  // 创建信息窗口对象     

    map.openInfoWindow(infoWindow, point); //开启信息窗口
    marker.addEventListener("click", function () {
        map.openInfoWindow(infoWindow, point); //开启信息窗口

    });
}
//显示车辆当前位置地图  结束

function SetAreaInfo(address) {
    if (vtype == 0) {

        $("#txtfrom").removeClass("holder_txt").val(address.province + "-" + address.city + "-" + address.area);
        $("#txtfrom,#txtfAddressRemark").attr("wtmap-data", "{p:'" + address.province + "',c:'" + address.city + "',a:'" + address.area + "',d:'" + address.address + "',dr:'',ln:'" + address.lng + "',la:'" + address.lat + "'}");
    }
    else if (vtype == 1) {

        $("#txtto").removeClass("holder_txt").val(address.province + "-" + address.city + "-" + address.area);
        $("#txtto,#txttAddressRemark").attr("wtmap-data", "{p:'" + address.province + "',c:'" + address.city + "',a:'" + address.area + "',d:'" + address.address + "',dr:'',ln:'" + address.lng + "',la:'" + address.lat + "'}");
    }

    SetHiddenArea(address);

    if (vtype == 0 || vtype == 1) {
        var distance = Number(GetDistance());
        if (distance > 0)
            $("#licheng").show().children("span").text((distance >= 1 ? distance : 1));
        else
            $("#licheng").hide()

        if ($("#startlng").val() !== "" && $("#endlng").val() !== "") {
            GetCars(1);
            GetWlLines(1);
            CalcInsurance();
        }
    }

    SetUrlArea();
}

//保险计算 开始

function CalcInsurance() {
    var flat = $('#startlat').val(),
        flng = $('#startlng').val(),
        tlat = $('#endlat').val(),
        tlng = $('#endlng').val(),
   mainRisks = $('#fh_ygbxdq').val(),
  invoiceAmt = $('#invoiceAmt').val();
    if ($("#fh_ygbx").is(":checked") && flng != "" && tlng != "" && invoiceAmt != "") {
        $.ajax({
            async: true,
            type: "post",
            url: "/bx/InterfaceLayer/postProductPlanTrial.ashx",
            data: { "flat": flat, "flng": flng, "tlat": tlat, "tlng": tlng, "mainRisks": mainRisks, source: "fh", "invoiceAmt": invoiceAmt, "planId": "550" },
            success: function (result) {
                if (result.ret == "0") {
                    $("#orderprice").text(result.data + "元");
                    $("#orderpriceval").val(result.data);

                } else if (result.ret == "1") {
                    $("#orderprice").text(result.msg);
                }
                $(".bx-price").show();
            },
            dataType: "json"
        });
    }
    else {
        $(".bx-price").hide();
    }
}

//保险计算 结束

//根据货物重量体积匹配车长、货物类型（轻重货） 开始

function autoCar() {
    var wList = [3000, 4000, 10000, 25000, 25000, 40000, 40000, 40000],
        vList = [8, 14, 40, 60, 65, 100, 150, 180];
    var wUnit = $("[name=goods_unit]").val(),
       weight = $("[name=goods_weight]").val(),
          vol = $("[name=goods_vol]").val();
    if (vol == "" || weight == "")
        return;

    var wNum = Number(weight);
    wNum = (wUnit == "1" ? wNum * 1000 : wNum);
    var vNum = Number(vol);
    var wIndex = 0, vIndex = 0;
    if (!isNaN(wNum)) {
        for (var i = 0; i < wList.length; i++) {
            if (wNum <= wList[i] || i == wList.length - 1) {
                wIndex = i;
                break;
            }
        }
    }
    if (!isNaN(vNum)) {
        for (var i = 0; i < vList.length; i++) {
            if (vNum <= vList[i] || i == vList.length - 1) {
                vIndex = i;
                break;
            }
        }
    }
    var selectIndex = (wIndex > vIndex ? wIndex : vIndex) + 1;
    $("#choose_len").prev().find("li").eq(selectIndex).click();

    //货物类型联动  1吨大于3方为轻货  小于3方为重货
    if (vNum / wNum * 1000 <= 3) {
        $("[name=goods_type]").prev().find("li").eq(1).click();
    }
    else {
        $("[name=goods_type]").prev().find("li").eq(0).click();
    }

}

//根据货物重量体积匹配车长、货物类型（轻重货） 结束