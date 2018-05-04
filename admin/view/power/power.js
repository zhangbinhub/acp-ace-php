(function ($) {

    powerconfig = {};

    powerconfig.currNodeId = "";
    powerconfig.currNodeAppId = "";
    powerconfig.currNodeName = "";
    powerconfig.currModuleNodeId = "";
    powerconfig.currModuleNodeAppId = "";
    powerconfig.currModuleNodeName = "";

    /**
     * 鼠标移上节点时显示自定义控件（新增按钮）
     *
     * @param treeId
     * @param treeNode
     */
    powerconfig.addHoverDom = function (treeId, treeNode) {
        var sObj = $("#" + treeNode.tId + "_span");
        if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0)
            return;
        var addStr = "<span class='ace-icon fa fa-plus-circle green bigger-110' id='addBtn_" + treeNode.tId + "' title='新增'></span>";
        sObj.after(addStr);
        var btn = $("#addBtn_" + treeNode.tId);
        if (btn) {
            btn.bind("click", function () {
                powerconfig.addNode(treeId, treeNode);
                return false;
            });
        }
        btn.css("margin-left", "5px");
        $("#" + treeNode.tId + "_remove").removeClass("button").removeClass("remove").addClass("ace-icon fa fa-trash-o red bigger-110").css("margin-left", "5px");
    };

    /**
     * 鼠标移上节点时显示自定义控件（新增按钮）
     *
     * @param treeId
     * @param treeNode
     */
    powerconfig.addHoverDomModule = function (treeId, treeNode) {
        var sObj = $("#" + treeNode.tId + "_span");
        if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0)
            return;
        var addStr = "<span class='ace-icon fa fa-plus-circle green bigger-110' id='addBtn_" + treeNode.tId + "' title='新增' onfocus='this.blur();'></span>";
        sObj.after(addStr);
        var btn = $("#addBtn_" + treeNode.tId);
        if (btn) {
            btn.bind("click", function () {
                powerconfig.addNodeModule(treeId, treeNode);
                return false;
            });
        }
        btn.css("margin-left", "5px");
        $("#" + treeNode.tId + "_remove").removeClass("button").removeClass("remove").addClass("ace-icon fa fa-trash-o red bigger-110").css("margin-left", "5px");
    };

    /**
     * 鼠标移开节点时隐藏自定义控件（新增按钮）
     *
     * @param treeId
     * @param treeNode
     */
    powerconfig.removeHoverDom = function (treeId, treeNode) {
        $("#addBtn_" + treeNode.tId).unbind().remove();
    };

    /**
     * 是否显示删除按钮
     *
     * @param treeId
     * @param treeNode
     * @returns
     */
    powerconfig.showRemoveBtn = function (treeId, treeNode) {
        return !(treeNode.pid == "0" || treeNode.type == 0);
    };

    /**
     * 判断节点是否可以删除
     *
     * @param treeId
     * @param treeNode
     * @returns
     */
    powerconfig.beforeRemove = function (treeId, treeNode) {
        if (treeNode.isParent) {
            AUI.dialog.alert("含有下级菜单，不能删除！", null, 3);
            return false;
        }
        AUI.dialog.confirm("确定删除 " + treeNode.name + "？", function (result) {
            if (result) {
                var zTree = AUI.tree.getTreeObj(treeId);
                admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceMenu",
                    {
                        cmd: "del",
                        id: treeNode.id
                    }, function (data) {
                        if (data.errmsg) {
                            AUI.dialog.alert(data.errmsg, function () {
                                zTree.selectNode(treeNode);
                                powerconfig.zTreeOnClick(null, treeId, treeNode, null);
                            }, 3);
                        } else {
                            zTree.removeNode(treeNode);
                            $("#powerconfig_menuconfig_menuinfo").addClass("hidden");
                        }
                    });
            }
        });
        return false;
    };

    /**
     * 判断节点是否可以删除
     *
     * @param treeId
     * @param treeNode
     * @returns
     */
    powerconfig.beforeRemoveModule = function (treeId, treeNode) {
        if (treeNode.isParent) {
            AUI.dialog.alert("含有下级模块，不能删除！", null, 3);
            return false;
        }
        AUI.dialog.confirm("确定删除 " + treeNode.name + "？", function (result) {
            if (result) {
                var zTree = AUI.tree.getTreeObj(treeId);
                admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceFunc",
                    {
                        cmd: "delModule",
                        id: treeNode.id
                    }, function (data) {
                        if (data.errmsg) {
                            AUI.dialog.alert(data.errmsg, function () {
                                zTree.selectNode(treeNode);
                                powerconfig.zTreeOnClickModule(null, treeId, treeNode, null);
                            }, 3);
                        } else {
                            zTree.removeNode(treeNode);
                            $("#powerconfig_funcconfig_moduleinfo").addClass("hidden");
                        }
                    });
            }
        });
        return false;
    };

    /**
     * 拖拽之前调用的函数
     *
     * @param treeId
     * @param treeNodes
     * @returns {Boolean}
     */
    powerconfig.beforeDrag = function (treeId, treeNodes) {
        return treeNodes[0].pid != 0;
    };

    /**
     * 拖拽操作结束之前调用的函数
     *
     * @param treeId
     * @param treeNodes
     * @param targetNode
     * @param moveType
     * @param isCopy
     * @returns {Boolean}
     */
    powerconfig.beforeDrop = function (treeId, treeNodes, targetNode, moveType, isCopy) {
        if (targetNode) {
            return !(targetNode.pid == 0 && (moveType == "prev" || moveType == "next"));
        }
        return false;
    };

    /**
     * 结束拖拽时调用的函数
     *
     * @param event
     * @param treeId
     * @param treeNodes
     * @param targetNode
     * @param moveType
     * @param isCopy
     */
    powerconfig.onDrop = function (event, treeId, treeNodes, targetNode, moveType, isCopy) {
        admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceMenu", {
            cmd: "saveAndResort",
            id: treeNodes[0].id,
            targetid: targetNode.id,
            appid: targetNode.appid,
            moveType: moveType
        }, function (data) {
            var zTree = AUI.tree.getTreeObj(treeId);
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, function () {
                    powerconfig.generateMenuTree();
                }, 3);
            } else {
                zTree.selectNode(treeNodes[0]);
                powerconfig.zTreeOnClick(null, treeId, treeNodes[0], null);
            }
        }, "POST", false);
    };

    /**
     * 新增节点时调用的函数
     *
     * @param treeId
     * @param treeNode
     */
    powerconfig.addNode = function (treeId, treeNode) {
        var parentid = treeNode.id;
        var appid = treeNode.appid;
        admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceMenu", {
            cmd: "add",
            appid: appid,
            parentid: parentid
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var zTree = AUI.tree.getTreeObj(treeId);
                zTree.addNodes(treeNode, data);
                var node = zTree.getNodeByParam("id", data.id, treeNode);
                zTree.selectNode(node);
                powerconfig.zTreeOnClick(null, treeId, node, null);
            }
        });
    };

    /**
     * 新增节点时调用的函数
     *
     * @param treeId
     * @param treeNode
     */
    powerconfig.addNodeModule = function (treeId, treeNode) {
        var parentid = treeNode.id;
        var appid = treeNode.appid;
        admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceFunc", {
            cmd: "addModule",
            appid: appid,
            parentid: parentid
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var zTree = AUI.tree.getTreeObj(treeId);
                zTree.addNodes(treeNode, data);
                var node = zTree.getNodeByParam("id", data.id, treeNode);
                zTree.selectNode(node);
                powerconfig.zTreeOnClickModule(null, treeId, node, null);
            }
        });
    };

    /**
     * 获取父节点名称
     *
     * @param treeid
     * @param treeNode
     * @returns {String}
     */
    powerconfig.getParentName = function (treeid, treeNode) {
        if (treeNode.pid != "0") {
            var parentNode = treeNode.getParentNode();
            return powerconfig.getParentName(treeid, parentNode) + "|#|" + parentNode.name;
        } else {
            return "";
        }
    };

    /**
     * 点击事件
     *
     * @param event
     * @param treeId
     * @param treeNode
     * @param clickFlag
     */
    powerconfig.zTreeOnClick = function (event, treeId, treeNode, clickFlag) {
        powerconfig.currNodeId = treeNode.id;
        powerconfig.currNodeAppId = treeNode.appid;
        powerconfig.currNodeName = powerconfig.getParentName(treeId, treeNode) + "|#|" + treeNode.name;
        powerconfig.currNodeName = powerconfig.currNodeName.substring(3).replace(/\|#\|/ig, " <i class=\"ace-icon fa fa-angle-double-right\"></i> ");
        if (treeNode.pid != "0") {
            powerconfig.initInfoPage();
        } else {
            $("#powerconfig_menuconfig_menuinfo").addClass("hidden");
        }
    };

    /**
     * 点击事件
     *
     * @param event
     * @param treeId
     * @param treeNode
     * @param clickFlag
     */
    powerconfig.zTreeOnClickModule = function (event, treeId, treeNode, clickFlag) {
        powerconfig.currModuleNodeId = treeNode.id;
        powerconfig.currModuleNodeAppId = treeNode.appid;
        powerconfig.currModuleNodeName = powerconfig.getParentName(treeId, treeNode) + "|#|" + treeNode.name;
        powerconfig.currModuleNodeName = powerconfig.currModuleNodeName.substring(3).replace(/\|#\|/ig, " <i class=\"ace-icon fa fa-angle-double-right\"></i> ");
        if (treeNode.pid != "0") {
            powerconfig.initModuleInfoPage();
        } else {
            $("#powerconfig_funcconfig_moduleinfo").addClass("hidden");
        }
    };

    /**
     * 生成预览图标
     */
    powerconfig.generateIconPreview = function () {
        var classname = $("#powerconfig_menuconfig_menuinfo_icon_class").val();
        var color = $("#powerconfig_menuconfig_menuinfo_icon_color").val();
        if ($.trim(classname) != "") {
            $("#powerconfig_menuconfig_menuinfo_icon_preview").attr("class", "bigger-120 ace-icon fa " + classname).css({"color": color});
        } else {
            $("#powerconfig_menuconfig_menuinfo_icon_preview").attr("class", "");
        }
    };

    /**
     * 初始化详细信息数据
     */
    powerconfig.initInfoPage = function () {
        $("#powerconfig_menuconfig_menuinfo_save_btn").hide();
        $("#powerconfig_menuconfig_menuinfo_save_btn").prop("disabled", true);
        $("#powerconfig_menuconfig_menuinfo").removeClass("hidden");
        admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceMenu",
            {
                cmd: "searchInfo",
                menuid: powerconfig.currNodeId,
                appid: powerconfig.currNodeAppId
            },
            function (data) {
                if (data.errmsg) {
                    AUI.dialog.alert(data.errmsg, null, 3);
                } else {
                    $("#powerconfig_menuconfig_menuinfo_title").html('详细信息 <small>' + powerconfig.currNodeName + '</small>');
                    AUI.element.refreshColorpicker("powerconfig_menuconfig_menuinfo_icon_color", data.icon_color);
                    $("#powerconfig_menuconfig_menuinfo_menu_name").val(data.menu_name);
                    $("#powerconfig_menuconfig_menuinfo_menu_model").val(data.menu_model);
                    $("#powerconfig_menuconfig_menuinfo_menu_opentype").val(data.menu_opentype);
                    $("#powerconfig_menuconfig_menuinfo_dialog_w").val(data.dialog_w);
                    $("#powerconfig_menuconfig_menuinfo_dialog_h").val(data.dialog_h);
                    powerconfig.showSize(parseInt(data.menu_opentype));
                    $("#powerconfig_menuconfig_menuinfo_page_url").val(data.page_url);
                    AUI.element.setSpinnerValue("powerconfig_menuconfig_menuinfo_menu_sort", data.menu_sort);
                    if (data.menu_status == 1) {
                        $("#powerconfig_menuconfig_menuinfo_menu_status").prop("checked", true);
                    } else {
                        $("#powerconfig_menuconfig_menuinfo_menu_status").prop("checked", false);
                    }
                    AUI.element.setChosenSelected("powerconfig_menuconfig_menuinfo_icon_class", data.icon_class);
                    powerconfig.generateIconPreview();
                    AUI.element.updateSelectItem("powerconfig_menuconfig_menuinfo_rolelist", data.roles, data.select_roles);
                    AUI.element.refreshDuallist("powerconfig_menuconfig_menuinfo_rolelist");
                    $("#powerconfig_menuconfig_menuinfo_save_btn").show();
                    $("#powerconfig_menuconfig_menuinfo_save_btn").prop("disabled", false);
                    $("#powerconfig_menuconfig_menuinfo").focusInView({
                        top: 50
                    });
                }
            }, "POST", false);
    };

    /**
     * 初始化详细信息数据
     */
    powerconfig.initModuleInfoPage = function () {
        $("#powerconfig_funcconfig_moduleinfo_save_btn").hide();
        $("#powerconfig_funcconfig_moduleinfo_save_btn").prop("disabled", true);
        $("#powerconfig_funcconfig_moduleinfo").removeClass("hidden");
        admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceFunc",
            {
                cmd: "searchModuleInfo",
                moduleid: powerconfig.currModuleNodeId,
                appid: powerconfig.currModuleNodeAppId
            },
            function (data) {
                if (data.errmsg) {
                    AUI.dialog.alert(data.errmsg, null, 3);
                } else {
                    $("#powerconfig_funcconfig_moduleinfo_title").html('详细信息 <small>' + powerconfig.currModuleNodeName + '</small>');
                    $("#powerconfig_funcconfig_moduleinfo_module_name").val(data.module_name);
                    $("#powerconfig_funcconfig_moduleinfo_module_code").val(data.module_code);
                    AUI.element.updateSelectItem("powerconfig_funcconfig_moduleinfo_rolelist", data.roles, data.select_roles);
                    AUI.element.refreshDuallist("powerconfig_funcconfig_moduleinfo_rolelist");
                    AUI.grid.refreshGrid("powerconfig_funcconfig_moduleinfo_funcinfo_grid_table", true,
                        {
                            url: G_webrootPath + "/service/power/serviceFunc",
                            editurl: G_webrootPath + "/service/power/serviceFunc?moduleid=" + powerconfig.currModuleNodeId + "&appid=" + powerconfig.currModuleNodeAppId,
                            postData: {
                                cmd: "searchFunc",
                                moduleid: powerconfig.currModuleNodeId,
                                appid: powerconfig.currModuleNodeAppId
                            }
                        });
                    $("#powerconfig_funcconfig_moduleinfo_save_btn").show();
                    $("#powerconfig_funcconfig_moduleinfo_save_btn").prop("disabled", false);
                    $("#powerconfig_funcconfig_moduleinfo").focusInView({
                        top: 50
                    });
                }
            }, "POST", false);
    };

    /**
     * 功能权限配置
     *
     * @param funcid
     * @param funcname
     */
    powerconfig.showFuncPower = function (funcid, appid, funcname) {
        AUI.dialog.inDialog(800, 554, funcname + "角色权限",
            {
                innerUrl: G_webrootPath
                + "/view/power/funcpower?funcid=" + funcid
                + "&appid=" + appid
            }, null, function (data) {
                if (data) {
                    AUI.grid.refreshGrid("powerconfig_funcconfig_moduleinfo_funcinfo_grid_table");
                }
            });
    };

    /**
     * 显示对话框尺寸配置项
     *
     * @param opentype
     */
    powerconfig.showSize = function (opentype) {
        if (opentype == 2 || opentype == 3) {
            $("#powerconfig_menuconfig_menuinfo_dialog_size").removeClass("hidden");
        } else {
            $("#powerconfig_menuconfig_menuinfo_dialog_size").addClass("hidden");
        }
    };

    /**
     * 生成详细信息页面组件
     */
    powerconfig.generateInfoPage = function () {
        AUI.element.updateSelectItem("powerconfig_menuconfig_menuinfo_icon_class", Global_IconClasses, "", true, true);
        AUI.element.initChosen("#powerconfig_menuconfig_menuinfo_icon_class");
        $("#powerconfig_menuconfig_menuinfo_icon_class").next().find("ul.chosen-results").css("max-height", "180px");
        $("#powerconfig_menuconfig_a,#powerconfig_menuconfig_menuinfo_info_a").click(function () {
            AUI.element.refreshChosen("powerconfig_menuconfig_menuinfo_icon_class");
        });
        AUI.element.initColorpicker("powerconfig_menuconfig_menuinfo_icon_color");
        $("#powerconfig_menuconfig_menuinfo_icon_class").on("change", function () {
            powerconfig.generateIconPreview();
        });
        $("#powerconfig_menuconfig_menuinfo_icon_color").on("blur", function () {
            powerconfig.generateIconPreview();
        });
        AUI.element.initSpinner("powerconfig_menuconfig_menuinfo_menu_sort");
        $("#powerconfig_menuconfig_menuinfo_menu_model").change(function () {
            if ($(this).val() == "1") {
                $("#powerconfig_menuconfig_menuinfo_menu_opentype").val("4");
            } else {
                $("#powerconfig_menuconfig_menuinfo_menu_opentype").val("0");
            }
        });
        $("#powerconfig_menuconfig_menuinfo_menu_opentype").change(function () {
            powerconfig.showSize(parseInt($(this).val()));
        });
        $("#powerconfig_menuconfig_menuinfo_dialog_w,#powerconfig_menuconfig_menuinfo_dialog_h").blur(function () {
            var value = $(this).val();
            if (isNaN(Number(value))) {
                $(this).val("0");
            }
        });
        AUI.element.initDuallist("powerconfig_menuconfig_menuinfo_rolelist",
            {
                selectorMinimalHeight: 270,
                selectedListLabel: '<div class="col-sm-12 well well-sm center">已授权角色</div>',
                nonSelectedListLabel: '<div class="col-sm-12 well well-sm center">备选角色</div>'
            });
        $("#powerconfig_menuconfig_menuinfo_save_btn").unbind("click").click(function () {
            $("#powerconfig_menuconfig_menuinfo_dialog_w,#powerconfig_menuconfig_menuinfo_dialog_h").blur();
            if ($("#powerconfig_menuconfig_menuinfo_menu_model").val() == "1") {
                var opentype = $("#powerconfig_menuconfig_menuinfo_menu_opentype").val();
                if (opentype == "0" || opentype == "2") {
                    AUI.dialog.alert("外部链接不允许使用“内嵌模式（div）”或“对话框模式（div）”", null, 2);
                    $("#powerconfig_menuconfig_menuinfo_menu_opentype").val("5");
                    return;
                }
            }
            admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceMenu",
                {
                    cmd: "saveInfo",
                    menuid: powerconfig.currNodeId,
                    icon_class: $("#powerconfig_menuconfig_menuinfo_icon_class").val(),
                    icon_color: $("#powerconfig_menuconfig_menuinfo_icon_color").val(),
                    sort: $("#powerconfig_menuconfig_menuinfo_menu_sort").val(),
                    menu_name: $("#powerconfig_menuconfig_menuinfo_menu_name").val(),
                    menu_model: $("#powerconfig_menuconfig_menuinfo_menu_model").val(),
                    menu_opentype: $("#powerconfig_menuconfig_menuinfo_menu_opentype").val(),
                    dialog_w: $("#powerconfig_menuconfig_menuinfo_dialog_w").val() == "" ? 0 : $("#powerconfig_menuconfig_menuinfo_dialog_w").val(),
                    dialog_h: $("#powerconfig_menuconfig_menuinfo_dialog_h").val() == "" ? 0 : $("#powerconfig_menuconfig_menuinfo_dialog_h").val(),
                    page_url: $("#powerconfig_menuconfig_menuinfo_page_url").val(),
                    status: $("#powerconfig_menuconfig_menuinfo_menu_status").is(":checked") ? 1 : 0,
                    select_roles: $("#powerconfig_menuconfig_menuinfo_rolelist").val()
                },
                function (data) {
                    if (data.errmsg) {
                        AUI.dialog.alert(data.errmsg, null, 3);
                    } else {
                        AUI.dialog.alert("保存成功！", function () {
                            $("#powerconfig_menuconfig_menuinfo_save_btn").hide();
                            $("#powerconfig_menuconfig_menuinfo_save_btn").prop("disabled", true);
                            powerconfig.generateMenuTree(function (treeId) {
                                var zTree = AUI.tree.getTreeObj(treeId);
                                var node = zTree.getNodeByParam("id", powerconfig.currNodeId);
                                zTree.selectNode(node);
                                powerconfig.zTreeOnClick(null, treeId, node, null);
                            });
                        }, 1);
                    }
                });
        });
    };

    /**
     * 生成详细信息页面组件
     */
    powerconfig.generateModuleInfoPage = function () {
        powerconfig.generateFuncGrid("powerconfig_funcconfig_moduleinfo_funcinfo_grid_table", "powerconfig_funcconfig_moduleinfo_funcinfo_grid_pager");
        $("#powerconfig_funcconfig_a,#powerconfig_funcconfig_moduleinfo_funcinfo_a").click(function () {
            setTimeout(function () {
                AUI.grid.resizeGrid();
            }, 0);
        });
        AUI.element.initDuallist("powerconfig_funcconfig_moduleinfo_rolelist",
            {
                selectorMinimalHeight: 270,
                selectedListLabel: '<div class="col-sm-12 well well-sm center">已授权角色</div>',
                nonSelectedListLabel: '<div class="col-sm-12 well well-sm center">备选角色</div>'
            });
        AUI.element.initValidate("powerconfig_funcconfig_moduleinfo_form",
            {
                powerconfig_funcconfig_moduleinfo_module_name: "请输入模块名称",
                powerconfig_funcconfig_moduleinfo_module_code: {
                    required: "请输入模块编码",
                    remote: "此编码已存在"
                }
            }, {
                powerconfig_funcconfig_moduleinfo_module_code: {
                    remote: {
                        url: G_webrootPath + "/service/power/serviceFunc",
                        type: "POST",
                        data: {
                            cmd: "validationMcode",
                            module_id: function () {
                                return powerconfig.currModuleNodeId;
                            },
                            module_code: function () {
                                return $("#powerconfig_funcconfig_moduleinfo_module_code").val();
                            }
                        }
                    }
                }
            });
        $("#powerconfig_funcconfig_moduleinfo_save_btn").click(function () {
            if (AUI.element.doValidate("powerconfig_funcconfig_moduleinfo_form")) {
                admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceFunc",
                    {
                        cmd: "saveModuleInfo",
                        module_id: function () {
                            return powerconfig.currModuleNodeId;
                        },
                        module_name: $("#powerconfig_funcconfig_moduleinfo_module_name").val(),
                        module_code: $("#powerconfig_funcconfig_moduleinfo_module_code").val(),
                        select_roles: $("#powerconfig_funcconfig_moduleinfo_rolelist").val()
                    },
                    function (data) {
                        if (data.errmsg) {
                            AUI.dialog.alert(data.errmsg, null, 3);
                        } else {
                            AUI.dialog.alert("保存成功！", function () {
                                $("#powerconfig_funcconfig_moduleinfo_save_btn").hide();
                                $("#powerconfig_funcconfig_moduleinfo_save_btn").prop("disabled", true);
                                powerconfig.generateModuleTree(function (treeId) {
                                    var zTree = AUI.tree.getTreeObj(treeId);
                                    var node = zTree.getNodeByParam("id", powerconfig.currModuleNodeId);
                                    zTree.selectNode(node);
                                    powerconfig.zTreeOnClickModule(null, treeId, node, null);
                                });
                            }, 1);
                        }
                    });
            } else {
                AUI.dialog.alert("请先完善模块信息！", null, 2);
            }
        });
    };

    /**
     * 生成功能列表
     *
     * @param grid_selectorId
     * @param pager_selectorId
     */
    powerconfig.generateFuncGrid = function (grid_selectorId, pager_selectorId) {
        var grid_selector = grid_selectorId;
        var pager_selector = pager_selectorId;
        var param = {
            height: 200,
            multiselect: true,
            sortname: "code",
            colNames: ['编辑', 'id', 'applicationid', '名称', '编码', '记录日志', '功能权限'],
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
                    name: 'applicationid',
                    index: 'applicationid',
                    hidden: true
                },
                {
                    name: 'name',
                    index: 'name',
                    editable: true,
                    sortable: false,
                    editrules: {
                        required: true
                    }
                },
                {
                    name: 'code',
                    index: 'code',
                    editable: true,
                    sortable: true,
                    editrules: {
                        required: true
                    }
                },
                {
                    name: 'islog',
                    index: 'islog',
                    align: "center",
                    width: 65,
                    editable: true,
                    sortable: false,
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
                    name: 'config',
                    index: 'config',
                    align: 'center',
                    classes: 'jqgrid config-btn row-td',
                    sortable: false,
                    search: false,
                    fixed: true,
                    editable: false,
                    width: 80,
                    formatter: function (cellvalue, options, rowObject) {
                        return "<button type=\"button\" class=\"btn btn-minier btn-primary\" onclick=\"powerconfig.showFuncPower('"
                            + rowObject.id
                            + "','"
                            + rowObject.applicationid
                            + "','"
                            + rowObject.name
                            + "')\"><i class=\"ace-icon fa fa-key icon-only bigger-110\"></i> 权限</button>";
                    }
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
        }, null, function (form) {
            form.find('input[type=checkbox]').addClass('ace ace-switch ace-switch-6').after('<span class="lbl"></span>');
        });
    };

    /**
     * 生成菜单树
     *
     * @param callBackFun
     */
    powerconfig.generateMenuTree = function (callBackFun) {
        var setting = {
            view: {
                addHoverDom: powerconfig.addHoverDom,
                removeHoverDom: powerconfig.removeHoverDom,
                selectedMulti: false
            },
            edit: {
                drag: {
                    autoExpandTrigger: true,
                    isCopy: false,
                    prev: true,
                    inner: true,
                    next: true
                },
                enable: true,
                editNameSelectAll: true,
                removeTitle: "删除",
                renameTitle: "重命名",
                showRemoveBtn: powerconfig.showRemoveBtn,
                showRenameBtn: false
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pid",
                    rootPId: "0"
                }
            },
            callback: {
                beforeRemove: powerconfig.beforeRemove,
                beforeDrag: powerconfig.beforeDrag,
                beforeDrop: powerconfig.beforeDrop,
                beforeDragOpen: true,
                onDrop: powerconfig.onDrop,
                onClick: powerconfig.zTreeOnClick
            }
        };
        admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceMenu", {
            cmd: "search"
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var menutree = $("#powerconfig_menuconfig_menutree");
                AUI.tree.initTree(menutree, setting, data.tree);
                if (typeof (callBackFun) == "function") {
                    callBackFun("powerconfig_menuconfig_menutree");
                }
            }
        }, "POST", false);
    };

    /**
     * 生成模块树
     *
     * @param callBackFun
     */
    powerconfig.generateModuleTree = function (callBackFun) {
        var setting = {
            view: {
                addHoverDom: powerconfig.addHoverDomModule,
                removeHoverDom: powerconfig.removeHoverDom,
                selectedMulti: false
            },
            edit: {
                drag: {
                    isCopy: false,
                    isMove: false,
                    prev: false,
                    inner: false,
                    next: false
                },
                enable: true,
                editNameSelectAll: true,
                removeTitle: "删除",
                renameTitle: "重命名",
                showRemoveBtn: powerconfig.showRemoveBtn,
                showRenameBtn: false
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pid",
                    rootPId: "0"
                }
            },
            callback: {
                beforeRemove: powerconfig.beforeRemoveModule,
                onClick: powerconfig.zTreeOnClickModule
            }
        };
        admin_tools_obj.doAjax(G_webrootPath + "/service/power/serviceFunc", {
            cmd: "searchModule"
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var menutree = $("#powerconfig_funcconfig_moduletree");
                AUI.tree.initTree(menutree, setting, data.tree);
                if (typeof (callBackFun) == "function") {
                    callBackFun("powerconfig_funcconfig_moduletree");
                }
            }
        }, "POST", false);
    };

    $(function () {
        powerconfig.generateMenuTree(function () {
            powerconfig.generateInfoPage();
            powerconfig.generateModuleTree(function () {
                powerconfig.generateModuleInfoPage();
            });
        });
    });
})($);