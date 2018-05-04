(function ($) {

    applicationconfig = {};

    applicationconfig.myChart = null;

    /**
     * 生成应用配置列表
     *
     * @param grid_selectorId
     * @param pager_selectorId
     */
    applicationconfig.generateAppGrid = function (grid_selectorId, pager_selectorId) {
        var grid_selector = grid_selectorId;
        var pager_selector = pager_selectorId;
        var param = {
            url: G_webrootPath + "/service/application/serviceApp",
            editurl: G_webrootPath + "/service/application/serviceApp",
            multiselect: true,
            sortname: "sort",
            needFilter: true,
            filter: {
                searchOnEnter: null
            },
            colNames: ['编辑', 'id', '类型', '应用根路径', '应用名称', '数据源', '语言', '版权所有者', '开始年份', '结束年份', '版本', '默认应用', '序号', '操作'],
            colModel: [{
                name: 'myac',
                index: '',
                width: 70,
                fixed: true,
                sortable: false,
                search: false,
                formatter: 'actions'
            },
                {
                    name: 'id',
                    index: 'id',
                    hidden: true
                },
                {
                    name: 'type',
                    index: 'type',
                    hidden: true
                },
                {
                    name: 'webroot',
                    index: 'webroot',
                    editable: true,
                    fixed: true,
                    width: 100,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'appname',
                    index: 'appname',
                    editable: true,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'dbno',
                    index: 'dbno',
                    formatter: 'select',
                    edittype: 'select',
                    editoptions: {
                        value: dbresourceStr
                    },
                    editable: true,
                    editrules: {
                        required: true
                    }
                },
                {
                    name: 'language',
                    index: 'language',
                    editable: true,
                    fixed: true,
                    width: 80,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'copyright_owner',
                    index: 'copyright_owner',
                    editable: true,
                    fixed: true,
                    width: 100,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'copyright_begin',
                    index: 'copyright_begin',
                    align: "center",
                    editable: true,
                    fixed: true,
                    width: 70,
                    editrules: {
                        required: true,
                        number: true
                    },
                    editoptions: {
                        size: "4",
                        maxlength: "4"
                    },
                    unformat: AUI.grid.format.pickYear
                },
                {
                    name: 'copyright_end',
                    index: 'copyright_end',
                    align: "center",
                    editable: true,
                    fixed: true,
                    width: 70,
                    editrules: {
                        size: "4",
                        maxlength: "4",
                        number: true
                    },
                    editoptions: {
                        size: "4",
                        maxlength: "4"
                    },
                    unformat: AUI.grid.format.pickYear
                },
                {
                    name: 'version',
                    index: 'version',
                    editable: true,
                    fixed: true,
                    width: 70,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'defaultapp',
                    index: 'defaultapp',
                    align: "center",
                    width: 65,
                    editable: true,
                    fixed: true,
                    edittype: "checkbox",
                    editoptions: {
                        value: "是:否"
                    },
                    formatter: function (cellvalue, options, rowObject) {
                        var temp = "";
                        if (cellvalue == "是") {
                            temp = "<div class='blue bolder grid-form-field-div align-center width-100'>是</div>";
                        } else {
                            temp = "<div class='red bolder grid-form-field-div align-center width-100'>否</div>";
                        }
                        return temp;
                    },
                    unformat: AUI.grid.format.auiSwitch
                },
                {
                    name: 'sort',
                    index: 'sort',
                    editable: true,
                    fixed: true,
                    width: 60,
                    editrules: {
                        required: true,
                        number: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'config',
                    index: 'config',
                    align: 'center',
                    classes: 'jqgrid config-btn row-td',
                    sortable: false,
                    search: false,
                    fixed: true,
                    editable: false,
                    width: 40,
                    formatter: function (cellvalue, options, rowObject) {
                        return "<div class=\"inline pos-rel dropup\">"
                            + "<button type=\"button\" class=\"btn btn-minier btn-primary dropdown-toggle\" data-toggle=\"dropdown\" data-position=\"auto\" aria-expanded=\"false\"><i class=\"ace-icon fa fa-cog icon-only bigger-110\"></i></button>"
                            + "<ul class=\"dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close\">"
                            + "<li><a href=\"javascript:void(0);\" class=\"tooltip-success\" data-rel=\"tooltip\" onclick=\"applicationconfig.showStatistical('"
                            + rowObject.id
                            + "','"
                            + rowObject.appname
                            + "')\" title=\"统计\" data-original-title=\"View\"><span class=\"green\"><i class=\"ace-icon fa fa-bar-chart-o\"></i></span></a></li>"
                            + "<li><a href=\"javascript:void(0);\" class=\"tooltip-info\" data-rel=\"tooltip\" onclick=\"applicationconfig.showInfoConfig('"
                            + rowObject.id
                            + "','"
                            + rowObject.appname
                            + "')\" title=\"相关信息\" data-original-title=\"Edit\"><span class=\"blue\"><i class=\"ace-icon fa fa-pencil-square-o bigger-120\"></i></span></a></li>"
                            + "</ul></div>";
                    }
                }]
        };

        // var buttons = [{
        //     type: "sep"
        // }, {
        //     type: "button",
        //     buttonicon: "ace-icon fa fa-file-excel-o green",
        //     title: "导出Excel",
        //     onClickButton: function () {
        //         applicationconfig.exportToFile(0);
        //     }
        // }, {
        //     type: "button",
        //     buttonicon: "ace-icon fa fa-file-pdf-o red",
        //     title: "导出PDF",
        //     onClickButton: function () {
        //         applicationconfig.exportToFile(1);
        //     }
        // }];

        AUI.grid.generateGrid(
            grid_selector,
            pager_selector,
            param,
            null,
            {
                navGrid: {
                    del: true,
                    search: true,
                    refresh: true
                },
                inlineNav: {
                    add: true
                }
            },
            null,
            function (form) {
                form.find("input[name='copyright_begin'],input[name='copyright_end']")
                    .datepicker({
                        minViewMode: "years",
                        format: "yyyy",
                        language: "cn",
                        autoclose: true
                    });
                form.find('input[type=checkbox]').addClass('ace ace-switch ace-switch-6').after('<span class="lbl"></span>');
            });
    };

    /**
     * 导出文件
     *
     * @param flag
     *            0-Excel，1-PDF
     */
    // applicationconfig.exportToFile = function (flag) {
    //     var name = "";
    //     if (flag == 0) {
    //         name = "application/appExcel";
    //     } else if (flag == 1) {
    //         name = "application/appPDF";
    //     }
    //     admin_tools_obj.doAjaxToServer(name, null, function (data) {
    //         if (data.errmsg) {
    //             AUI.dialog.alert(data.errmsg, null, 3);
    //         } else {
    //             admin_tools_obj.doDownloadFromBack(data.data, true, false);
    //         }
    //     });
    // };

    /**
     * 生成信息配置列表
     *
     * @param grid_selectorId
     * @param pager_selectorId
     */
    applicationconfig.generateInfoGrid = function (grid_selectorId, pager_selectorId) {
        var grid_selector = grid_selectorId;
        var pager_selector = pager_selectorId;
        var param = {
            multiselect: true,
            sortname: "isenabled",
            needFilter: true,
            filter: {
                searchOnEnter: null
            },
            caption: "附加信息配置",
            height: 150,
            colNames: ['编辑', 'id', '信息名称', '信息内容', '可用'],
            colModel: [
                {
                    name: 'myac',
                    index: '',
                    width: 70,
                    fixed: true,
                    sortable: false,
                    search: false,
                    formatter: 'actions'
                },
                {
                    name: 'id',
                    index: 'id',
                    hidden: true
                },
                {
                    name: 'info_name',
                    index: 'info_name',
                    editable: true,
                    width: 300,
                    fixed: true,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'info_value',
                    index: 'info_value',
                    editable: true,
                    editrules: {
                        required: true
                    },
                    editoptions: {
                        rows: "2"
                    },
                    edittype: 'textarea'
                },
                {
                    name: 'isenabled',
                    index: 'isenabled',
                    align: "center",
                    width: 60,
                    fixed: true,
                    editable: true,
                    edittype: "checkbox",
                    editoptions: {
                        value: "是:否"
                    },
                    formatter: function (cellvalue, options, rowObject) {
                        var temp = "";
                        if (cellvalue == "是") {
                            temp = "<div class='blue bolder grid-form-field-div align-center width-100'>是</div>";
                        } else {
                            temp = "<div class='red bolder grid-form-field-div align-center width-100'>否</div>";
                        }
                        return temp;
                    },
                    unformat: AUI.grid.format.auiSwitch
                }]
        };
        AUI.grid.generateGrid(grid_selector, pager_selector, param, null, {
            navGrid: {
                add: true,
                del: true,
                search: true,
                refresh: true
            }
        }, null, function (form) {
            form.find('input[type=checkbox]').addClass('ace ace-switch ace-switch-6').after('<span class="lbl"></span>');
        });
    };

    /**
     * 生成链接配置列表
     *
     * @param grid_selectorId
     * @param pager_selectorId
     */
    applicationconfig.generateLinkGrid = function (grid_selectorId, pager_selectorId) {
        var grid_selector = grid_selectorId;
        var pager_selector = pager_selectorId;
        var param = {
            multiselect: true,
            sortname: "isenabled",
            needFilter: true,
            filter: {
                searchOnEnter: null
            },
            caption: "外部链接配置",
            height: 150,
            colNames: ['编辑', 'id', '链接类型', '链接名称', '链接地址', '链接图标地址', '可用'],
            colModel: [
                {
                    name: 'myac',
                    index: '',
                    width: 70,
                    fixed: true,
                    sortable: false,
                    search: false,
                    formatter: 'actions'
                },
                {
                    name: 'id',
                    index: 'id',
                    hidden: true
                },
                {
                    name: 'link_type',
                    index: 'link_type',
                    editable: true,
                    fixed: true,
                    width: 100,
                    editrules: {
                        required: true
                    },
                    formatter: "select",
                    edittype: 'select',
                    stype: "select",
                    editoptions: {
                        value: '0:文字;1:图片'
                    }
                },
                {
                    name: 'link_name',
                    index: 'link_name',
                    editable: true,
                    fixed: true,
                    width: 100,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'link_url',
                    index: 'link_url',
                    editable: true,
                    editrules: {
                        required: true,
                        url: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'link_image_url',
                    index: 'link_image_url',
                    editable: true,
                    edittype: 'textarea'
                },
                {
                    name: 'isenabled',
                    index: 'isenabled',
                    align: "center",
                    width: 60,
                    fixed: true,
                    editable: true,
                    edittype: "checkbox",
                    editoptions: {
                        value: "是:否"
                    },
                    formatter: function (cellvalue, options, rowObject) {
                        var temp = "";
                        if (cellvalue == "是") {
                            temp = "<div class='blue bolder grid-form-field-div align-center width-100'>是</div>";
                        } else {
                            temp = "<div class='red bolder grid-form-field-div align-center width-100'>否</div>";
                        }
                        return temp;
                    },
                    unformat: AUI.grid.format.auiSwitch
                }]
        };
        AUI.grid.generateGrid(grid_selector, pager_selector, param, null, {
            navGrid: {
                add: true,
                del: true,
                search: true,
                refresh: true
            }
        }, null, function (form) {
            form.find('input[type=checkbox]').addClass('ace ace-switch ace-switch-6').after('<span class="lbl"></span>');
        });
    };

    /**
     * 显示相关信息配置界面
     *
     * @param appid
     * @param appname
     */
    applicationconfig.showInfoConfig = function (appid, appname) {
        $("#application_appconfig_page,#application_infoconfig_page,#application_statistical_page").addClass("hidden");
        $("#application_infoconfig_page").removeClass("hidden");
        admin_tools_obj.appendBreadcrumb("相关信息配置");
        AUI.grid.refreshGrid("application-info-grid-table", true, {
            url: G_webrootPath + "/service/application/serviceAppInfo",
            editurl: G_webrootPath + "/service/application/serviceAppInfo?appid=" + appid,
            postData: {
                appid: appid
            }
        });
        AUI.grid.refreshGrid("application-link-grid-table", true, {
            url: G_webrootPath + "/service/application/serviceAppLink",
            editurl: G_webrootPath + "/service/application/serviceAppLink?appid=" + appid,
            postData: {
                appid: appid
            }
        });
        AUI.grid.resizeGrid();
        $("#application_info_title").html(' <i class="ace-icon fa fa-angle-double-right"></i> ' + appname);
    };

    /**
     * 显示统计界面
     *
     * @param appid
     * @param appname
     */
    applicationconfig.showStatistical = function (appid, appname) {
        /**
         * 初始化图表
         *
         * @param appid
         */
        function initCharts(appid) {
            AUI.showProcess(undefined, $("#application_charts_info_logininfo"));
            admin_tools_obj.doAjax(
                G_webrootPath + "/service/application/serviceApp",
                {
                    oper: "getLoginInfo",
                    appid: appid
                },
                function (result) {
                    AUI.closeProcess($("#application_charts_info_logininfo"));
                    if (result.errmsg) {
                        AUI.chart.distroyChart(applicationconfig.myChart);
                        AUI.dialog.alert(result.errmsg, null, 3);
                    } else {
                        var pointdata = [];
                        for (var i = 0; i < result.length; i++) {
                            pointdata.push(parseInt(result[i][1]));
                        }
                        var xAxisdata = [];
                        for (var i = 0; i < result.length; i++) {
                            xAxisdata.push(result[i][0]);
                        }
                        var options = {
                            title: {
                                text: '用户登录次数统计'
                            },
                            tooltip: {
                                trigger: 'axis'
                            },
                            dataZoom: {
                                show: true,
                                start: 70
                            },
                            legend: {
                                data: ['登录次数']
                            },
                            toolbox: {
                                show: true,
                                feature: {
                                    saveAsImage: {
                                        show: true
                                    }
                                }
                            },
                            xAxis: [{
                                type: 'category',
                                boundaryGap: false,
                                splitNumber: 10,
                                data: xAxisdata
                            }],
                            yAxis: [{
                                type: 'value',
                                splitNumber: 10
                            }],
                            grid: {
                                y2: 80
                            },
                            series: [{
                                name: '登录次数',
                                type: 'line',
                                showAllSymbol: true,
                                smooth: true,
                                itemStyle: {
                                    normal: {
                                        color: "#A2CD5A",
                                        areaStyle: {
                                            type: 'default'
                                        }
                                    }
                                },
                                data: pointdata
                            }]
                        };
                        applicationconfig.myChart = AUI.chart.initChart("application_charts_info_logininfo", options);
                    }
                }, "POST", false, "json", true, function (obj, message, exception) {
                    AUI.closeProcess($("#application_charts_info_logininfo"));
                    AUI.chart.distroyChart(applicationconfig.myChart);
                    AUI.dialog.alert(message, null, 3);
                });
        }

        $("#application_appconfig_page,#application_infoconfig_page,#application_statistical_page").addClass("hidden");
        $("#application_statistical_page").removeClass("hidden");
        admin_tools_obj.appendBreadcrumb("统计信息");
        $("#application_charts_title").html('<i class="ace-icon fa fa-angle-double-right"></i>' + appname);
        initCharts(appid);
        $("#application_charts_reload,#application_charts_fullscreen").click(
            function () {
                initCharts(appid);
            });
        $("#application_charts_collapse").click(function () {
            setTimeout(function () {
                resizeChart();
            }, 0);
        });
    };

    /**
     * 返回上一界面
     */
    applicationconfig.doBack = function () {
        admin_tools_obj.doBackBreadcrumb();
        $("#application_appconfig_page,#application_infoconfig_page,#application_statistical_page").addClass("hidden");
        $("#application_appconfig_page").removeClass("hidden");
        AUI.grid.resizeGrid();
    };

    var resizeChart = function () {
        if (applicationconfig.myChart != null) {
            AUI.chart.resize(applicationconfig.myChart);
        }
    };

    $(function () {
        applicationconfig.generateAppGrid("application-grid-table", "application-grid-pager");
        applicationconfig.generateInfoGrid("application-info-grid-table", "application-info-grid-pager");
        applicationconfig.generateLinkGrid("application-link-grid-table", "application-link-grid-pager");
        $("#application_infoconfig_page,#application_statistical_page").find(".page-header>span.arrowed:eq(0)").click(function () {
            applicationconfig.doBack();
        });
        $(window).unbind("resize", resizeChart);
        $(window).bind("resize", resizeChart);
    });
})($);