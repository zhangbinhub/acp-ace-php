(function ($) {

    adminsysparamconfig = {};

    /**
     * 生成应用配置列表
     *
     * @param grid_selectorId
     * @param pager_selectorId
     */
    adminsysparamconfig.generateGrid = function (grid_selectorId, pager_selectorId) {
        var grid_selector = grid_selectorId;
        var pager_selector = pager_selectorId;
        var param = {
            url: G_webrootPath + "/service/sysparam/serviceSysParam",
            editurl: G_webrootPath + "/service/sysparam/serviceSysParam",
            multiselect: true,
            sortname: "confvalue",
            needFilter: true,
            filter: {
                searchOnEnter: null
            },
            colNames: ['编辑', 'id', '参数名称', '参数值', '描述', '状态'],
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
                    name: 'confname',
                    index: 'confname',
                    editable: true,
                    editrules: {
                        required: true
                    },
                    edittype: 'text'
                },
                {
                    name: 'confvalue',
                    index: 'confvalue',
                    editable: true,
                    edittype: 'text'
                },
                {
                    name: 'confdes',
                    index: 'confdes',
                    editable: true,
                    edittype: 'text'
                },
                {
                    name: 'status',
                    index: 'status',
                    align: "center",
                    width: 65,
                    editable: true,
                    fixed: true,
                    edittype: "checkbox",
                    editoptions: {
                        value: "启用:禁用"
                    },
                    formatter: function (cellvalue, options, rowObject) {
                        var temp = "";
                        if (cellvalue == "启用") {
                            temp = "<div class='blue bolder grid-form-field-div align-center width-100'>启用</div>";
                        } else {
                            temp = "<div class='red bolder grid-form-field-div align-center width-100'>禁用</div>";
                        }
                        return temp;
                    },
                    unformat: AUI.grid.format.auiSwitch
                }]
        };

        AUI.grid.generateGrid(grid_selector, pager_selector, param, null, {
            navGrid: {
                del: true,
                search: true,
                refresh: true
            },
            inlineNav: {
                add: true
            }
        });
    };

    $(function () {
        adminsysparamconfig.generateGrid("sysparam-grid-table", "sysparam-grid-pager");
    });
})($);