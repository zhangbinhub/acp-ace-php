/** charts start */
(function ($) {

    var chart = {};

    /**
     * 生成图表
     *
     * @param areaid
     *            图表区域id
     * @param options
     *            图表配置参数
     * @param theme
     *            主题
     * @return myChart
     */
    chart.initChart = function (areaid, options, theme) {
        var option = $.extend({}, options);
        theme = theme || 'dark';
        var myChart = echarts.init(document.getElementById(areaid), theme);
        myChart.setOption(option);
        return myChart;
    };

    /**
     * 刷新图标
     * @param myChart
     * @param options
     */
    chart.refresh = function (myChart, options) {
        myChart.setOption(options);
    };

    /**
     * 清空并释放图表
     *
     * @param myChart
     */
    chart.distroyChart = function (myChart) {
        if (myChart && !myChart.isDisposed()) {
            myChart.clear();
            myChart.dispose();
        }
    };

    chart.resize = function (myChart) {
        if (myChart && !myChart.isDisposed()) {
            myChart.resize();
        }
    };

    AUI.chart = chart;
})($);
/** charts end */
