$.prototype.val = function (base) {
    return function (setVal) {
        if (setVal != null && this[0]) {
            this.each(function () {
                this.value = setVal;
            });
            return this;
        }
        else {
            var thisVal = this.length > 0 ? this[0].value : "";
            if (this.attr("txtholder")) {

                if (thisVal == this.attr("txtholder")) {
                    return "";
                }
                else {
                    return thisVal;
                }
            }
            else {
                return thisVal;
            }
        }

    }
}($.prototype.val);

(function () {

    WT = {
        SubmitForm: function (formId) {
            //txtholder属性控件未输入值的修改为空值(否则txtholder值会提交到后台)
            $("[txtholder]").each(function () {
                if ($(this)[0].value == $(this).attr("txtholder")) {
                    $(this).val("");
                }
            });

            $("#" + formId).submit();
        },
        //表单验证
        CheckForm: function (config) {

            var ValidateType = {
                required: "required",//必填
                number: "number",//正数（可小数，不能0）
                negativenum: "negativenum",//负数（可小数）
                allnumber: "allnumber",//数字（可正负可小数）
                positivedec: "positivedec",//正小数
                negativedec: "negativedec",//负小数
                decimal: "decimal",//小数(正负)
                integer: "int",//整数(正负)
                plusint: "plusint",//正整数
                minusint: "minusint",//负整数
                email: "email",//邮箱
                phone: "phone",//电话
                mobile: "mobile"//手机
            },
            RegValidate = function (validateType, checkTxt) {
                var regExp = "";
                switch (validateType) {
                    case ValidateType.number:
                        regExp = /^([1-9]\d*\.?\d*)$|(0\.\d*[1-9])$/;
                        break;
                    case ValidateType.negativenum:
                        regExp = /^-([1-9]\d*|0)(\.\d+)?$/;
                        break;
                    case ValidateType.allnumber:
                        regExp = /^-?([1-9]\d*|0)(\.\d+)?$/;
                        break;
                    case ValidateType.positivedec:
                        regExp = /^([1-9]\d*.\d*|0.\d*[1-9]\d*)$/;
                        break;
                    case ValidateType.negativedec:
                        regExp = /^-([1-9]\d*.\d*|0.\d*[1-9]\d*)$/;
                        break;
                    case ValidateType.decimal:
                        regExp = /^-?([1-9]\d*.\d*|0.\d*[1-9]\d*)$/;
                        break;
                    case ValidateType.integer:
                        regExp = /^-?[1-9]\d*$/;
                        break;
                    case ValidateType.plusint:
                        regExp = /^[1-9]\d*$/;
                        break;
                    case ValidateType.minusint:
                        regExp = /^-[1-9]\d*$/
                        break;
                    case ValidateType.email:
                        regExp = /^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/;
                        break;
                    case ValidateType.phone:
                        regExp = /^[0-9-()（）]{7,18}$/;
                        break;
                    case ValidateType.mobile:
                        regExp = /^0?(13|14|15|17|18)[0-9]{9}$/;
                        break;
                }
                return regExp.test(checkTxt);
            },
            FailedOp = function (el) {
                el.css("border", "solid 1px red");
            },
            SuccessOp = function (el) {
                //TODO:验证成功复原状态
                el.removeAttr("style");
            },
            submitEl = (config && config.gp) ? $("[validate-submit=" + config.gp + "]") : $("[validate-submit]"),
            CheckSingle = function (el) {

                var elVal = $.trim(el.val()),
                    elHolder = $.trim(el.attr("txtholder")),
                    types = [],
                    validateRes = true;

                $(el[0].attributes).each(function () {
                    if (this.name.indexOf("validate") > -1 && this.name.indexOf("form") == -1) {
                        types = this.value.split(' ');
                        return false;
                    }
                });

                if ($.inArray(ValidateType.required, types) > -1 && (elVal == "" || elVal == elHolder)) {
                    validateRes = false;
                }
                else if (elVal != "") {
                    for (var i = 0; i < types.length; i++) {
                        if (types[i] != ValidateType.required) {
                            validateRes = RegValidate(types[i], elVal);
                            if (!validateRes)
                                break;
                        }
                    }
                }
                if (!validateRes)
                    FailedOp(el);
                else
                    SuccessOp(el);
                return validateRes;
            },
            CheckGroup = function () {

                var firstFailedEl, group = $(this).attr("validate-submit");
                $("[validate" + (group != "" ? "-" + group : "") + "]").each(function () {
                    if (!CheckSingle($(this)) && !firstFailedEl)
                        firstFailedEl = this;

                });
                if (firstFailedEl) {
                    firstFailedEl.focus();
                    return false;
                }
                else if (config && config.cb) {
                    eval(config.cb);
                }

            };

            submitEl.each(function () {

                var groupEl = $("[validate" + (config && config.gp ? "-" + config.gp : "") + "]");

                if (typeof (blurEv) != "undefined") groupEl.unbind("blur", blurEv);
                groupEl.bind("blur", blurEv = function () {
                    CheckSingle($(this));
                });

                $(this).unbind("click").click(CheckGroup);

            });

        },

        //自定义select控件：select控件添加isreadonly='0'属性，生成可输入的文本框。
        CustomSelect: {
            CreateCustomEl: function (oldSelectEl) {
                
                oldSelectEl.removeClass("no-to-custom");
                var options = oldSelectEl.children(), selectTxt = oldSelectEl.children("option:selected").text();
                var style = (oldSelectEl.attr("style") ? " style='" + oldSelectEl.attr("style") + "' " : "");
                var cusSelBox = "<div class='cus_sel_box_comm " + (oldSelectEl.attr("class") ? oldSelectEl.attr("class") : "").replace("holder_txt", "") + "' " + style + "></div>";
                oldSelectEl.before(cusSelBox);
                var holderTxt = selectTxt == "" && oldSelectEl.attr("txtholder") ? ("txtholder='" + oldSelectEl.attr("txtholder") + "'") : "",
                   isReadonly = oldSelectEl.attr("isreadonly") == "0" ? "" : "readonly='readonly' unselectable='on'",
                    inputName = "txt_" + oldSelectEl.attr("name");
                var cusSelectTxt = "<input name='" + inputName + "' value='" + selectTxt + "' class='cus_sel_txt_comm' " + holderTxt + " " + isReadonly + " />",
                    cusSelectOption = "<ul class='cus_sel_option' style='display:none;'>";
                options.each(function () {
                    var thisTxt = $(this).text()
                    if (thisTxt == selectTxt)
                        cusSelectOption += "<li class='cus_sel_selected' style='display:none'>" + thisTxt + "</li>";
                    else
                        cusSelectOption += "<li>" + thisTxt + "</li>";
                });
                cusSelectOption += "</ul>";
                oldSelectEl.prev().append(cusSelectTxt + cusSelectOption);

                var txtShowBox = oldSelectEl.prev().children("input"),
                    listItem = oldSelectEl.prev().find("li"),
                    hideDelay;

                txtShowBox.click(function () {
                    if ($(this).next().is(":hidden"))
                        $(this).next().show();
                    else
                        $(this).next().hide();
                });

                txtShowBox.bind("mouseenter mouseleave", function (e) {
                    clearTimeout(hideDelay);
                    if (e.type == "mouseleave") {
                        hideDelay = setTimeout(function () {
                            txtShowBox.next().hide();
                        }, 300);
                    }

                });

                listItem.parent().bind("mouseenter mouseleave", function (e) {
                    if (e.type == "mouseenter") {
                        clearTimeout(hideDelay);
                    }
                    else {
                        hideDelay = setTimeout(function () {
                            txtShowBox.next().hide();
                        }, 200);
                    }
                });

                listItem.mouseenter(function () {
                    $(this).addClass("cus_sel_option_hover");
                });

                listItem.mouseout(function () {
                    $(this).removeClass("cus_sel_option_hover");
                });

                listItem.click(function () {
                    var thisItem = $(this);
                    txtShowBox.val(thisItem.text()).blur();
                    txtShowBox.parent().next().children().removeAttr("selected");
                    txtShowBox.parent().next().children().each(function () {
                        if ($(this).text() == thisItem.text())
                            $(this).attr("selected", "selected").parent().change();
                    });

                    listItem.parent().hide();
                    if (!thisItem.hasClass("cus_sel_selected")) {
                        thisItem.parent().find(".cus_sel_selected").removeClass("cus_sel_selected");
                        thisItem.addClass("cus_sel_selected");
                    }
                });

                oldSelectEl.css("display", "none");
            },
            Init: function () {
                $("select").each(function () {
                    if (!$(this).hasClass("no-to-custom")) {
                        WT.CustomSelect.CreateCustomEl($(this));
                    }
                });

            }


        },

        //弹出层操作功能 config:{mainBody:jqueryobj,isClose:bool,closeEl:jqueryobj,showScroll:bool,drag:bool,}
        //.wt_pop_close 关闭元素类名  .wt_pop_drag 拖动元素类名
        PopupWindow: function (config) {

            var mainBody = config.mainBody, isDrag = false, startPosLeft, startPosTop, startMouseX, startMouseY,
            SetCenter = function () {
                var pageWidth = $(window).width(),
                    pageHeight = $(window).height(),
                    mainBodyWidth = mainBody.width(),
                    mainBodyHeight = mainBody.height(),
                    scrTop = $(document).scrollTop();
                var leftVal = (pageWidth - mainBodyWidth) < 0 ? 0 : ((pageWidth - mainBodyWidth) / 2), topVal = ((pageHeight - mainBodyHeight) < 0 ? 0 : (((pageHeight - mainBodyHeight) / 2))) + scrTop;
                $(".wt_pop_cover,.wt_pop_cover iframe").width(pageWidth).height($(document).height());
                mainBody.css("left", leftVal + "px").css("top", topVal + "px");
            },
            DragWindow = function () {
                mainBody.find(".wt_pop_drag").unbind("mousedown mouseup mousemove mouseout").bind("mousedown mouseup mousemove mouseout", function (e) {
                    switch (e.type) {
                        case "mousedown":
                            if (e.target.nodeName.toLocaleLowerCase() != "input") {
                                startMouseX = e.pageX,
                                startMouseY = e.pageY,
                                startPosLeft = mainBody.offset().left,
                                startPosTop = mainBody.offset().top
                                if (e.type == "touchstart") {
                                    startMouseX = e.originalEvent.targetTouches[0].pageX,
                                    startMouseY = e.originalEvent.targetTouches[0].pageY
                                }
                                isDrag = true;
                                mainBody.css("cursor", "move");
                            }
                            break;
                        case "mouseup":
                            isDrag = false;
                            mainBody.css("cursor", "default");
                            break;
                        case "mousemove":
                            if (isDrag) {
                                var x = e.pageX, y = e.pageY;
                                if (e.type == "touchmove") {
                                    x = e.originalEvent.targetTouches[0].pageX,
                                    y = e.originalEvent.targetTouches[0].pageY
                                }
                                mainBody.css("left", x - (startMouseX - startPosLeft)).css("top", y - (startMouseY - startPosTop));
                            }
                            break;
                    }
                });

            },
            CloseWindow = function () {
                mainBody.prev(".wt_pop_cover").remove(), mainBody.hide();
                $(window).unbind("resize", SetCenter);
            }

            if (config.isClose) {
                CloseWindow();
            }
            else {
                config.showScroll = config.showScroll == null ? true : config.showScroll;
                config.drag = config.drag == null ? true : config.drag;
                var popCover = "<div class='wt_pop_cover'><iframe frameborder='0'></iframe></div>";
                mainBody.before(popCover);
                SetCenter();
                mainBody.css("z-index", "999999").css("position", "absolute").show();
                $(window).bind("resize", SetCenter);
                $(".wt_pop_close").unbind("click").click(CloseWindow);
                if (config.drag) {
                    DragWindow();
                }
            }


        },
        /*
        文本框元素加上<input type='text' txtholder="提示文本内容"/> 注意：使用此属性提交表单前要清空下文本框value（未输入值的），否则后台会接收到txtholder值。可以使用本库SubmitForm提交表单       
        */
        TxtHodler: function () {
            $("[txtholder]").each(function () {
                var thisEl = $(this);
                var holderTxt = thisEl.attr("txtholder");
                if (thisEl[0].value == "") {
                    thisEl.addClass("holder_txt").val(holderTxt);
                }
                thisEl.focus(function () {
                    if ($(this)[0].value == $(this).attr("txtholder")) {
                        $(this).removeClass("holder_txt");
                        $(this).val("");
                    }
                });
                thisEl.blur(function () {
                    if ($(this)[0].value == "") {
                        $(this).addClass("holder_txt").val($(this).attr("txtholder"));
                    }
                    else if ($(this)[0].value != $(this).attr("txtholder")) {
                        $(this).removeClass("holder_txt");
                    }
                });
            });
        },

        /*
        config: {panel:JQObj,cb:"function({pi})",ps:8,pi:1,dc:100,skip:true,first:false,sc:10}
        参数说明：
                 panel：放分页控件的父容器，
                 cb：分页调用方法（{pi}代表页码参数）
                 ps：pagesize 每页数据条数（非必须，默认10）
                 pi：pageindex 当前页码（非必须，默认1）
                 dc：datacount 数据总数
                 skip：是否显示跳转控件（非必须，默认显示，当最大页数大于页码按钮数时才显示）
                 first:是否显示第一页最后一页按钮(非必须,默认不显示)
                 sc：showcount 显示页码按钮数（非必须，默认10）
        */
        Pager: function (config) {
            var pageSize = !config.ps ? 10 : config.ps,
               dataCount = !config.dc ? 0 : config.dc,
               pageCount = Math.ceil(dataCount / pageSize),
               pageIndex = !config.pi ? 1 : (config.pi > pageCount ? 1 : config.pi),
           showPageCount = !config.sc ? 10 : config.sc;

            var pagerHtml = "";
            pagerHtml += "<table class='tb-pager'>";
            pagerHtml += "<tr>";
            pagerHtml += "<td align='center'>";
            if (config.first) {
                pagerHtml += "<a class='" + (pageIndex == 1 ? "page-first-curr" : "page-first") + "'>第一页<\/a>";
            }
            pagerHtml += "<a class='" + (pageIndex == 1 ? "page-prev-curr" : "page-prev") + "'>上一页<\/a>";

            for (var i = 1; i <= pageCount; i++) {
                if (i == 1 && pageIndex >= showPageCount && pageCount > showPageCount) {
                    pagerHtml += "<span class='page-more'>...<\/span>";
                }
                else if (i > showPageCount && i > pageIndex + 1) {
                    pagerHtml += "<span class='page-more'>...<\/span>";
                    break;
                }
                else if (i > pageIndex - showPageCount + 1) {
                    var aClass = i == pageIndex ? "page-curr" : "page-num";
                    pagerHtml += "<a class='" + aClass + "'>" + i + "<\/a>";
                }
            }
            pagerHtml += "<a class='" + (pageIndex == pageCount ? "page-next-curr" : "page-next") + "'>下一页<\/a>   ";
            if (config.first) {
                pagerHtml += "<a class='" + (pageIndex == pageCount ? "page-last-curr" : "page-last") + "'>最后一页<\/a>";
            }

            pagerHtml += "<span class='page-info'><span class='page-index'>" + pageIndex + "<\/span>/<span class='page-count'>" + pageCount + "<\/span>页<\/span>";

            if (typeof (config.skip) == "undefined" || config.skip) {
                if (pageCount > showPageCount)
                    pagerHtml += "<span class='page-skip'><input type='text'><a>跳转<\/a><\/span>";
            }
            pagerHtml += "<\/td>";
            pagerHtml += "<\/tr>";
            pagerHtml += "<\/table>";
            config.panel.html(pagerHtml);

            if (config.cb) {
                config.panel.find(".page-num").click(function () {
                    eval(config.cb.replace("{pi}", $(this).text()));
                });

                config.panel.find(".page-next").click(function () {
                    eval(config.cb.replace("{pi}", Number(config.panel.find(".page-curr").text()) + 1));
                });

                config.panel.find(".page-prev").click(function () {
                    eval(config.cb.replace("{pi}", Number(config.panel.find(".page-curr").text()) - 1));
                });

                config.panel.find(".page-last").click(function () {
                    eval(config.cb.replace("{pi}", pageCount));
                });

                config.panel.find(".page-first").click(function () {
                    eval(config.cb.replace("{pi}", 1));
                });

                if (config.panel.find(".page-skip").length > 0) {
                    config.panel.find(".page-skip").children("a").click(function () {
                        var pageInput = $(this).prev();

                        if (pageInput.val() == "") {
                            alert("请输入页码！");
                            pageInput.focus();
                            return;
                        }
                        else if (!isNaN(parseInt(pageInput.val())) && parseInt(pageInput.val()) > 0 && parseInt(pageInput.val()) <= pageCount) {
                            eval(config.cb.replace("{pi}", parseInt(pageInput.val())));
                            return;
                        }
                        else {
                            alert("请输入正确的页码！");
                            pageInput.focus();
                            return;
                        }
                    });
                }
            }
        }
    }

    $(function () {

    })
})();