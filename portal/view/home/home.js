(function ($) {

    portalhomepageobj = {};

    portalhomepageobj.onlineUserOptions = null;
    portalhomepageobj.onlineUserChart = null;

    /**
     * 初始化图表
     */
    portalhomepageobj.initOnlineUserCharts = function () {
        portal_tools_obj.doAjax(G_webrootPath + "/service/home/serviceHome", {
            cmd: "getOnlineUserInfo"
        }, function (result) {
            if (result.errmsg) {
                AUI.chart.distroyChart(portalhomepageobj.onlineUserChart);
                AUI.dialog.alert(result.errmsg, null, 3);
            } else if (result.info) {
                portalhomepageobj.createChart(result.info);
            }
        }, "POST", false, "json", true, function (obj, message, exception) {
            AUI.chart.distroyChart(portalhomepageobj.onlineUserChart);
            AUI.dialog.alert(message, null, 3);
        });
    };

    /**
     * 生成图表
     *
     * @param info
     */
    portalhomepageobj.createChart = function (info) {
        if (info.isall) {
            $("#homepage_chart_onlineuser").parent().parent().parent().parent().removeClass("col-sm-6");
            $("#homepage_chart_onlineuser").parent().parent().parent().parent().addClass("col-xs-12");
            portalhomepageobj.onlineUserOptions = {
                title: {
                    text: '用户数统计',
                    subtext: '用户总数：' + info.total
                },
                tooltip: {
                    show: true,
                    trigger: 'item'
                },
                legend: {
                    data: ['用户数']
                },
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: {
                            show: true
                        }
                    }
                },
                calculable: true,
                xAxis: [{
                    type: 'category',
                    data: info.appnames
                }],
                yAxis: [{
                    type: 'value'
                }],
                series: [{
                    name: '用户数',
                    type: 'bar',
                    itemStyle: {
                        normal: { // 系列级个性化，横向渐变填充
                            barBorderRadius: [10, 10, 0, 0],
                            color: (function () {
                                return 'rgba(30,144,255,0.8)';
                            })(),
                            label: {
                                show: true,
                                textStyle: {
                                    fontSize: '20',
                                    fontFamily: '微软雅黑',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        emphasis: {
                            barBorderRadius: [10, 10, 0, 0]
                        }
                    },
                    data: info.usercounts
                }]
            };
        } else {
            $("#homepage_chart_onlineuser").parent().parent().parent().parent().removeClass("col-xs-12");
            $("#homepage_chart_onlineuser").parent().parent().parent().parent().addClass("col-sm-6");
            portalhomepageobj.onlineUserOptions = {
                tooltip: {
                    formatter: "{a} <br/>{b} : {c} 人"
                },
                toolbox: {
                    show: true,
                    feature: {
                        saveAsImage: {
                            show: true
                        }
                    }
                },
                series: [{
                    title: {
                        show: true,
                        offsetCenter: [0, '-40%'], // x, y，单位px
                        textStyle: { // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                            fontWeight: 'bolder'
                        }
                    },
                    max: info.total,
                    name: '用户数',
                    type: 'gauge',
                    splitNumber: info.splitNumber, // 分割段数，默认为5
                    axisTick: { // 坐标轴小标记
                        splitNumber: info.axisTick.splitNumber // 每份split细分多少段
                    },
                    detail: {
                        formatter: '{value} 人',
                        textStyle: { // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                            color: 'auto',
                            fontWeight: 'bolder'
                        }
                    },
                    data: [{
                        value: info.usercounts,
                        name: '用户数'
                    }]
                }]
            };
        }
        if (portalhomepageobj.onlineUserChart === null) {
            portalhomepageobj.onlineUserChart = AUI.chart.initChart("homepage_chart_onlineuser", portalhomepageobj.onlineUserOptions);
        } else {
            AUI.chart.refresh(portalhomepageobj.onlineUserChart, portalhomepageobj.onlineUserOptions);
        }
    };

    /**
     * 显示统计界面
     */
    portalhomepageobj.showOnlineUserStatistical = function () {
        portalhomepageobj.initOnlineUserCharts();
        $("#homepage_charts_reload,#homepage_charts_fullscreen").click(function () {
            portalhomepageobj.initOnlineUserCharts();
        });
        $("#homepage_charts_collapse").click(function () {
            setTimeout(function () {
                resizeChart();
            }, 0);
        });
    };

    var resizeChart = function () {
        if (portalhomepageobj.onlineUserChart !== null) {
            AUI.chart.resize(portalhomepageobj.onlineUserChart);
        }
    };

    $(function () {
        portalhomepageobj.showOnlineUserStatistical();
        $(window).unbind("resize", resizeChart);
        $(window).bind("resize", resizeChart);
    });
})($);