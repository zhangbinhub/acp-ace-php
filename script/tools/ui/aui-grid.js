/** grid ui start */
(function ($) {

    var format = {};

    var Grid = {};

    /**
     * 选择样式
     */
    format.auiSwitch = function (cellvalue, options, cell) {
        $(cell).html($(cell).text());
        setTimeout(function () {
            $(cell).find('input[type=checkbox]').addClass('ace ace-switch ace-switch-6').after('<span class="lbl"></span>');
        }, 0);
    };
    /**
     * 日期选择样式
     */
    format.pickDate = function (cellvalue, options, cell) {
        setTimeout(function () {
            $(cell).find('input[type=text]').datepicker({
                format: "yyyy-mm-dd",
                language: "cn",
                autoclose: true
            });
        }, 0);
    };
    /**
     * 年月选择样式
     */
    format.pickYearMonth = function (cellvalue, options, cell) {
        setTimeout(function () {
            $(cell).find('input[type=text]').datepicker({
                minViewMode: "months",
                format: "yyyy-mm",
                language: "cn",
                autoclose: true
            });
        }, 0);
    };
    /**
     * 年份选择样式
     */
    format.pickYear = function (cellvalue, options, cell) {
        setTimeout(function () {
            $(cell).find('input[type=text]').datepicker({
                minViewMode: "years",
                format: "yyyy",
                language: "cn",
                autoclose: true
            });
        }, 0);
    };
    format.style_edit_form = function (form, edit_form_fun) {
        setTimeout(
            function () {
                if (typeof (edit_form_fun) == "function") {
                    edit_form_fun(form);
                }
                // update buttons classes
                var buttons = form.next().find('.EditButton .fm-button');
                buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();// ui-icon,
                // s-icon
                buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
                buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>');

                buttons = form.next().find('.navButton a');
                buttons.find('.ui-icon').hide();
                buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
                buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
            }, 0);
    };
    format.style_delete_form = function (form) {
        setTimeout(function () {
            var buttons = form.next().find('.EditButton .fm-button');
            buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();// ui-icon, s-icon
            buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
            buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>')
        }, 0);
    };
    format.style_search_filters = function (form) {
        setTimeout(function () {
            form.find('.delete-rule').val('X');
            form.find('.add-rule').addClass('btn btn-xs btn-primary');
            form.find('.add-group').addClass('btn btn-xs btn-success');
            form.find('.delete-group').addClass('btn btn-xs btn-danger');
        }, 0);
    };
    format.style_search_form = function (form) {
        setTimeout(function () {
            var dialog = form.closest('.ui-jqdialog');
            var buttons = dialog.find('.EditTable');
            buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
            buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
            buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
        }, 0);
    };
    // it causes some flicker when reloading or navigating grid
    // it may be possible to have some custom formatter to do this as the grid
    // is being created to prevent this
    // or go back to default browser checkbox styles for the grid
    format.styleCheckbox = function (table) {
        /**
         * $(table).find('input:checkbox').addClass('ace') .wrap('<label />')
         * .after('<span class="lbl align-top" />')
         *
         *
         * $('.ui-jqgrid-labels th[id*="_cb"]:first-child')
         * .find('input.cbox[type=checkbox]').addClass('ace') .wrap('<label
         * />').after('<span class="lbl align-top" />');
         */
    };
    // unlike navButtons icons, action icons in rows seem to be hard-coded
    // you can change them like this in here if you want
    format.updateActionIcons = function (table) {
        /**
         * var replacement = { 'ui-ace-icon fa fa-pencil' : 'ace-icon fa
		 * fa-pencil blue', 'ui-ace-icon fa fa-trash-o' : 'ace-icon fa
		 * fa-trash-o red', 'ui-icon-disk' : 'ace-icon fa fa-check green',
		 * 'ui-icon-cancel' : 'ace-icon fa fa-times red' };
         * $(table).find('.ui-pg-div span.ui-icon').each(function(){ var icon =
		 * $(this); var $class = $.trim(icon.attr('class').replace('ui-icon',
		 * '')); if($class in replacement) icon.attr('class', 'ui-icon
		 * '+replacement[$class]); })
         */
    };
    // replace icons with FontAwesome icons like above
    format.updatePagerIcons = function (table) {
        var replacement = {
            'ui-icon-seek-first': 'ace-icon fa fa-angle-double-left bigger-140',
            'ui-icon-seek-prev': 'ace-icon fa fa-angle-left bigger-140',
            'ui-icon-seek-next': 'ace-icon fa fa-angle-right bigger-140',
            'ui-icon-seek-end': 'ace-icon fa fa-angle-double-right bigger-140'
        };
        $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function () {
            var icon = $(this);
            setTimeout(function () {
                var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
                if ($class in replacement)
                    icon.attr('class', 'ui-icon ' + replacement[$class]);
            }, 0);
        })
    };
    format.enableTooltips = function (table) {
        $('.navtable .ui-pg-button').tooltip({
            container: 'body'
        });
        $(table).find('.ui-pg-div').tooltip({
            container: 'body'
        });
    };

    /**
     * 生成列表
     *
     * @param grid_tableId
     * @param pager_selectorId
     * @param grid_param
     * @param event_param
     * @param nav_param
     * @param gridCompleteFun(table)
     * @param edit_form_fun
     * @param customButton_param
     * @param finallFun(grid_selector)
     */
    Grid.generateGrid = function (grid_tableId, pager_selectorId, grid_param, event_param, nav_param, gridCompleteFun, edit_form_fun, customButton_param, finallFun) {
        setTimeout(
            function () {
                var grid_selector = $('#' + grid_tableId);
                var pager_selector = $('#' + pager_selectorId);
                var param = grid_param || {};
                if (param.autowidth == undefined) {
                    param.autowidth = true;
                }
                if (param.autowidth) {
                    // 绑定改变jqgrid尺寸事件
                    $(window).on('resize.jqGrid', function () {
                        grid_selector.jqGrid('setGridWidth', grid_selector.closest('[class*="col-"]').width());
                    });
                    // 在菜单栏收起或展开时改变尺寸
                    $(document).on('settings.ace.jqGrid', function (ev, event_name, collapsed) {
                        var parent_column = grid_selector.closest('[class*="col-"]');
                        if (event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed') {
                            setTimeout(function () {
                                grid_selector.jqGrid('setGridWidth', parent_column.width());
                            }, 0);
                        }
                    });
                }
                if (param.colModel && param.colModel.length > 0) {
                    for (var col in param.colModel) {
                        if (param.colModel[col].formatter == "actions") {
                            param.colModel[col].formatoptions = $.extend(param.colModel[col].formatoptions,
                                {
                                    keys: true,
                                    afterSave: function () {
                                        grid_selector.jqGrid().trigger("reloadGrid");
                                    },
                                    onSuccess: function (response, postdata) {
                                        if (response.responseText != "true") {
                                            AUI.dialog.alert(response.responseText, null, 3);
                                            return [false, response.responseText];
                                        } else {
                                            grid_selector.jqGrid().trigger("reloadGrid");
                                            return new Array(true);
                                        }
                                    }
                                });
                        }
                    }
                }

                var init_param = {
                    url: param.url || undefined,
                    datatype: param.datatype || "json",
                    mtype: param.mtype || "POST",
                    colNames: param.colNames || undefined,
                    colModel: param.colModel || undefined,
                    pager: pager_selector || undefined,
                    rowNum: param.rowNum || 10,
                    rowList: param.rowList || [10, 20, 30],
                    sortable: param.sortable != undefined ? param.sortable : false,
                    sortname: param.sortname || "",
                    sortorder: param.sortorder || "asc",
                    caption: param.caption || "",
                    altRows: param.altRows != undefined ? param.altRows : true,
                    altclass: param.altclass || undefined,
                    autoencode: param.autoencode != undefined ? param.autoencode : true,
                    autowidth: param.autowidth,
                    cellLayout: param.cellLayout || undefined,
                    cellEdit: param.cellEdit != undefined ? param.cellEdit : false,
                    cellsubmit: param.cellsubmit || "remote",
                    cellurl: param.cellurl || undefined,// 单元格提交的url
                    datastr: param.datastr || "jsonstring",
                    deselectAfterSort: param.deselectAfterSort != undefined ? param.deselectAfterSort : true,
                    direction: param.direction || "ltr",
                    editurl: param.editurl || undefined,
                    emptyrecords: param.emptyrecords || "没有数据",
                    ExpandColClick: param.ExpandColClick != undefined ? param.ExpandColClick : true,
                    ExpandColumn: param.ExpandColumn || undefined,
                    footerrow: param.footerrow != undefined ? param.footerrow : false,
                    forceFit: param.forceFit || undefined,
                    gridview: param.gridview != undefined ? param.gridview : false,
                    height: param.height || 300,
                    hiddengrid: param.hiddengrid != undefined ? param.hiddengrid : false,
                    hidegrid: param.hidegrid != undefined ? param.hidegrid : true,
                    hoverrows: param.hoverrows != undefined ? param.hoverrows : true,
                    jsonReader: param.jsonReader || {
                        root: "rows",
                        page: "page",
                        total: "total",
                        records: "records",
                        repeatitems: true,
                        cell: "cell",
                        id: "id",
                        userdata: "userdata",
                        subgrid: {
                            root: "rows",
                            repeatitems: true,
                            cell: "cell"
                        }
                    },
                    loadonce: param.loadonce != undefined ? param.loadonce : false,
                    loadtext: param.loadtext || "加载中...",
                    loadui: param.loadui || "enable",
                    multikey: param.multikey || undefined,
                    multiboxonly: param.multiboxonly != undefined ? param.multiboxonly : false,
                    multiselect: param.multiselect != undefined ? param.multiselect : false,
                    multiselectWidth: param.multiselectWidth || 20,
                    page: param.page || 1,
                    pagerpos: param.pagerpos || "center",
                    pgbuttons: param.pgbuttons != undefined ? param.pgbuttons : true,
                    pginput: param.pginput != undefined ? param.pginput : true,
                    pgtext: param.pgtext || undefined,
                    prmNames: param.prmNames || {
                        root: "rows",
                        page: "page",
                        total: "total",
                        records: "records",
                        sort: "sidx",
                        order: "sord",
                        search: "_search",
                        nd: "nd",
                        id: "id",
                        editoper: "edit",
                        addoper: "add",
                        deloper: "del",
                        subgridid: "id",
                        totalrows: "totalrows"
                    },
                    postData: param.postData || undefined,
                    recordpos: param.recordpos || "right",
                    recordtext: param.recordtext || undefined,
                    resizeclass: param.resizeclass || undefined,
                    rownumbers: param.rownumbers != undefined ? param.rownumbers : true,
                    rownumWidth: param.rownumWidth || 25,
                    scroll: param.scroll != undefined ? param.scroll : false,
                    scrollOffset: param.scrollOffset || 18,
                    scrollrows: param.scrollrows != undefined ? param.scrollrows : false,
                    shrinkToFit: param.shrinkToFit || undefined,
                    subGrid: param.subGrid != undefined ? param.subGrid : false,
                    subGridModel: param.subGridModel || undefined,
                    subGridOptions: param.subGridOptions || {
                        plusicon: "ace-icon fa fa-plus center bigger-110 blue",
                        minusicon: "ace-icon fa fa-minus center bigger-110 blue",
                        openicon: "ace-icon fa fa-chevron-right center orange",
                        expandOnLoad: false,
                        selectOnExpand: false,
                        reloadOnExpand: true
                    },
                    subGridType: param.subGridType || null,
                    subGridUrl: param.subGridUrl || undefined,
                    subGridWidth: param.subGridWidth || 20,
                    toolbar: param.toolbar || [false, ''],
                    treeGrid: param.treeGrid != undefined ? param.treeGrid : false,
                    treeGridModel: param.treeGridModel || undefined,
                    treeIcons: param.treeIcons || undefined,
                    treeReader: param.treeReader || undefined,
                    tree_root_level: param.tree_root_level || 0,
                    userData: param.userData || undefined,
                    userDataOnFooter: param.userDataOnFooter != undefined ? param.userDataOnFooter : false,
                    viewrecords: param.viewrecords != undefined ? param.viewrecords : true,
                    viewsortcols: param.viewsortcols || undefined,
                    width: param.width || undefined,
                    xmlReader: param.xmlReader || undefined,
                    grouping: param.grouping || undefined,
                    groupingView: param.groupingView || undefined,
                    onInitGrid: param.onInitGrid || undefined,
                    gridComplete: function () {
                        var table = this;
                        setTimeout(function () {
                            format.styleCheckbox(table);
                            format.updateActionIcons(table);
                            format.updatePagerIcons(table);
                            format.enableTooltips(table);
                            if (typeof (gridCompleteFun) == "function") {
                                gridCompleteFun(table);
                            }
                            $(window).trigger('resize.jqGrid');
                        }, 0);
                    },
                    editOptions: {
                        closeAfterEdit: true,
                        recreateForm: true,
                        beforeShowForm: function (e) {
                            var form = $(e[0]);
                            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
                            format.style_edit_form(form, edit_form_fun);
                        },
                        afterSubmit: param.afterEditSubmit || function (response, postdata) {
                            if (response.responseText != "true") {
                                return [false, response.responseText];
                            } else {
                                grid_selector.jqGrid().trigger("reloadGrid");
                                return new Array(true);
                            }
                        }
                    },
                    delOptions: {
                        recreateForm: true,
                        beforeShowForm: function (e) {
                            var form = $(e[0]);
                            if (form.data('styled'))                                return false;
                            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
                            format.style_delete_form(form);
                            form.data('styled', true);
                        },
                        afterSubmit: param.afterDeleteSubmit || function (response, postdata) {
                            if (response.responseText != "true") {
                                return [false, response.responseText];
                            } else {
                                grid_selector.jqGrid().trigger("reloadGrid");
                                return new Array(true);
                            }
                        }
                    }
                };
                for (var item in event_param) {
                    if (item != "gridComplete") {
                        init_param[item] = event_param[item];
                    }
                }
                grid_selector.jqGrid(init_param);
                grid_selector.data("customerParams", param.customerParams || undefined);

                if (param.needFilter) {
                    if (param.filter) {
                        grid_selector.jqGrid('filterToolbar',
                            {
                                stringResult: param.filter.stringResult != undefined ? param.filter.stringResult : true,
                                autosearch: param.filter.autosearch != undefined ? param.filter.autosearch : true,
                                searchOnEnter: param.filter.searchOnEnter != undefined ? param.filter.searchOnEnter : null,
                                beforeSearch: param.filter.beforeSearch || null,
                                afterSearch: param.filter.afterSearch || null,
                                beforeClear: param.filter.beforeClear || null,
                                afterClear: param.filter.afterClear || null,
                                defaultSearch: "cn"
                            });
                    } else {
                        grid_selector.jqGrid('filterToolbar', {
                            stringResult: true,
                            autosearch: true,
                            searchOnEnter: true,
                            beforeSearch: null,
                            afterSearch: null,
                            beforeClear: null,
                            afterClear: null,
                            defaultSearch: "cn"
                        });
                    }
                }

                var navParam = nav_param || {};
                /**
                 * 导航按钮
                 */
                if (navParam.navGrid) {
                    var editOptions = {
                        closeAfterEdit: true,
                        recreateForm: true,
                        beforeShowForm: function (e) {
                            var form = $(e[0]);
                            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
                            format.style_edit_form(form, edit_form_fun);
                        },
                        afterSubmit: navParam.navGrid.afterEditSubmit || function (response, postdata) {
                            if (response.responseText != "true") {
                                return [false, response.responseText];
                            } else {
                                grid_selector.jqGrid().trigger("reloadGrid");
                                return new Array(true);
                            }
                        }
                    };
                    var addOptions = {
                        closeAfterAdd: true,
                        recreateForm: true,
                        viewPagerButtons: false,
                        beforeShowForm: function (e) {
                            var form = $(e[0]);
                            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
                            format.style_edit_form(form, edit_form_fun);
                        },
                        afterSubmit: navParam.navGrid.afterNewSubmit || function (response, postdata) {
                            if (response.responseText != "true") {
                                return [false, response.responseText];
                            } else {
                                grid_selector.jqGrid().trigger("reloadGrid");
                                return new Array(true);
                            }
                        }
                    };
                    var delOptions = {
                        recreateForm: true,
                        beforeShowForm: function (e) {
                            var form = $(e[0]);
                            if (form.data('styled'))
                                return false;
                            form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
                            format.style_delete_form(form);
                            form.data('styled', true);
                        },
                        afterSubmit: navParam.navGrid.afterDeleteSubmit || function (response, postdata) {
                            if (response.responseText != "true") {
                                return [false, response.responseText];
                            } else {
                                grid_selector.jqGrid().trigger("reloadGrid");
                                return new Array(true);
                            }
                        }
                    };
                    var searchOptions = {
                        recreateForm: true,
                        afterShowSearch: function (e) {
                            var form = $(e[0]);
                            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />');
                            format.style_search_form(form);
                        },
                        afterRedraw: function () {
                            format.style_search_filters($(this));
                        },
                        multipleSearch: true,
                        multipleGroup: true,
                        showQuery: true
                    };
                    var viewOptions = {
                        recreateForm: true,
                        beforeShowForm: function (e) {
                            var form = $(e[0]);
                            form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />')
                        }
                    };
                    grid_selector.jqGrid('navGrid', "#" + pager_selectorId, {
                        edit: navParam.navGrid.edit != undefined ? navParam.navGrid.edit : false,
                        editicon: 'ace-icon fa fa-pencil blue',
                        add: navParam.navGrid.add != undefined ? navParam.navGrid.add : false,
                        addicon: 'ace-icon fa fa-plus-circle purple',
                        del: navParam.navGrid.del != undefined ? navParam.navGrid.del : false,
                        delicon: 'ace-icon fa fa-trash-o red',
                        search: navParam.navGrid.search != undefined ? navParam.navGrid.search : false,
                        searchicon: 'ace-icon fa fa-search orange',
                        refresh: navParam.navGrid.refresh != undefined ? navParam.navGrid.refresh : false,
                        refreshicon: 'ace-icon fa fa-refresh green',
                        view: navParam.navGrid.view != undefined ? navParam.navGrid.view : false,
                        viewicon: 'ace-icon fa fa-search-plus grey',
                        alertheight: navParam.navGrid.alertheight
                    }, editOptions, addOptions, delOptions, searchOptions, viewOptions);
                }
                if (navParam.inlineNav) {
                    var addParams = $.extend({
                        useFormatter: true,
                        initdata: {},
                        position: "first",
                        useDefValues: true,
                        addRowParams: {
                            extraparam: {}
                        }
                    }, navParam.inlineNav.addParams);
                    grid_selector.jqGrid('inlineNav', "#" + pager_selectorId, {
                        edit: navParam.inlineNav.edit != undefined ? navParam.inlineNav.edit : false,
                        editicon: 'ace-icon fa fa-pencil blue',
                        add: navParam.inlineNav.add != undefined ? navParam.inlineNav.add : false,
                        addicon: 'ace-icon fa fa-plus-circle purple',
                        save: navParam.inlineNav.save != undefined ? navParam.inlineNav.save : false,
                        savetitle: "提交",
                        saveicon: "ui-icon ui-icon-disk",
                        cancel: navParam.inlineNav.cancel != undefined ? navParam.inlineNav.cancel : false,
                        canceltitle: "取消",
                        cancelicon: "ui-icon ui-icon-cancel",
                        addParams: addParams
                    });
                }

                /**
                 * 自定义按钮
                 */
                if (customButton_param && customButton_param.length > 0) {
                    for (var button in customButton_param) {
                        var type = customButton_param[button].type || "sep";
                        if (type == "sep") {
                            var param = {
                                position: customButton_param[button].position || "last",
                                sepclass: "ui-separator"
                            };
                            grid_selector.navSeparatorAdd('#' + pager_selectorId, param);
                        } else if (type == "button") {
                            var param = {
                                caption: customButton_param[button].caption || "",
                                title: customButton_param[button].title || "自定义按钮",
                                buttonicon: customButton_param[button].buttonicon || "ace-icon fa fa-file blue",
                                onClickButton: customButton_param[button].onClickButton || undefined,
                                position: customButton_param[button].position || "last",
                                cursor: customButton_param[button].cursor || "pointer",
                                id: customButton_param[button].id || undefined
                            }
                            grid_selector.navButtonAdd('#' + pager_selectorId, param);
                        }
                    }
                }

                if (typeof (finallFun) == "function") {
                    finallFun(grid_selector);
                }

                $(document).one('ajaxloadstart.page', function (e) {
                    $(grid_selector).jqGrid('GridUnload');
                    $('.ui-jqdialog').remove();
                });
            }, 0);
    };

    /**
     * 列表添加记录自定义函数
     *
     * @param grid_tableId
     * @param afterSubmit
     */
    Grid.addRecord = function (grid_tableId, afterSubmit) {
        $("#" + grid_tableId).jqGrid('editGridRow', "new", {
            closeAfterAdd: true,
            resize: false,
            afterSubmit: afterSubmit || function (response, postdata) {
                if (response.responseText != "true") {
                    return [false, response.responseText];
                } else {
                    $("#" + grid_tableId).trigger("reloadGrid");
                    return new Array(true);
                }
            }
        });
    };

    /**
     * 列表添加记录自定义函数
     *
     * @param grid_tableId
     */
    Grid.addRecordInline = function (grid_tableId) {
        $("#" + grid_tableId).jqGrid('addRow', {
            useFormatter: true,
            initdata: {},
            position: "first",
            useDefValues: true,
            addRowParams: {
                extraparam: {}
            }
        });
    };

    /**
     * 获取列表选中行的ID
     *
     * @param grid_tableId
     * @returns array
     */
    Grid.getSelectedIDs = function (grid_tableId) {
        return $("#" + grid_tableId).jqGrid('getGridParam', 'selarrrow');
    };

    /**
     * 获取列表数据
     *
     * @param grid_tableId
     * @param id
     *            为空则表示获取全部数据
     * @returns array[Object...]
     */
    Grid.getRowData = function (grid_tableId, id) {
        if (id) {
            return $("#" + grid_tableId).jqGrid('getRowData', id);
        } else {
            return $("#" + grid_tableId).jqGrid('getRowData');
        }
    };

    /**
     * 列表删除记录自定义函数
     *
     * @param grid_tableId
     * @param afterSubmit
     */
    Grid.delRecord = function (grid_tableId, afterSubmit) {
        var ids = Grid.getSelectedIDs(grid_tableId);
        if (ids.length == 0) {
            AUI.dialog.alert("请选择记录", undefined, 0, true);
            return;
        }
        $("#" + grid_tableId).jqGrid('delGridRow', ids, {
            recreateForm: true,
            beforeShowForm: function (e) {
                var form = $(e[0]);
                if (form.data('styled'))
                    return false;
                form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
                format.style_delete_form(form);
                form.data('styled', true);
            },
            afterSubmit: afterSubmit || function (response, postdata) {
                if (response.responseText != "true") {
                    return [false, response.responseText];
                } else {
                    $("#" + grid_tableId).trigger("reloadGrid");
                    return new Array(true);
                }
            }
        });
    };

    /**
     * 刷新列表数据
     *
     * @param grid_tableId
     * @param isReload
     *            是否重新加载回到第一页
     * @param param
     *            重新设置的参数，可选
     */
    Grid.refreshGrid = function (grid_tableId, isReload, param) {
        var $grid = $("#" + grid_tableId);
        if (param) {
            $grid.jqGrid("setGridParam", param);
        }
        if (isReload) {
            $grid.jqGrid("setGridParam", {
                page: 1
            });
        }
        $grid.trigger("reloadGrid");
    };

    /**
     * 重新调整当前页面的表格尺寸
     */
    Grid.resizeGrid = function () {
        $(window).trigger("resize.jqGrid");
    };

    Grid.getGridCustomerParams = function (grid_table_obj) {
        return $(grid_table_obj).data("customerParams");
    };

    Grid.format = format;
    AUI.grid = Grid;
})($);
/** grid ui end */
