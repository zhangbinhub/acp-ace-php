(function ($) {

    admindepartmentconfig = {};

    admindepartmentconfig.currNodeId = "";
    admindepartmentconfig.currNodeName = "";
    admindepartmentconfig.currEditable = "";

    /**
     * 鼠标移上节点时显示自定义控件（新增按钮）
     *
     * @param treeId
     * @param treeNode
     */
    admindepartmentconfig.addHoverDom = function (treeId, treeNode) {
        if (treeNode.editable == "true") {
            var sObj = $("#" + treeNode.tId + "_span");
            if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0)
                return;
            var addStr = "<span class='ace-icon fa fa-plus-circle green bigger-110' id='addBtn_" + treeNode.tId + "' title='新增'></span>";
            sObj.after(addStr);
            var btn = $("#addBtn_" + treeNode.tId);
            if (btn) {
                btn.bind("click", function () {
                    admindepartmentconfig.addNode(treeId, treeNode);
                    return false;
                });
            }
            btn.css("margin-left", "5px");
        }
        $("#" + treeNode.tId + "_remove").removeClass("button").removeClass("remove").addClass("ace-icon fa fa-trash-o red bigger-110").css("margin-left", "5px");
    };

    /**
     * 鼠标移开节点时隐藏自定义控件（新增按钮）
     *
     * @param treeId
     * @param treeNode
     */
    admindepartmentconfig.removeHoverDom = function (treeId, treeNode) {
        $("#addBtn_" + treeNode.tId).unbind().remove();
    };

    /**
     * 是否显示删除按钮
     *
     * @param treeId
     * @param treeNode
     * @returns
     */
    admindepartmentconfig.showRemoveBtn = function (treeId, treeNode) {
        if (treeNode.pid == "0") {
            return false;
        } else {
            return treeNode.editable == "true";
        }
    };

    /**
     * 判断节点是否可以删除
     *
     * @param treeId
     * @param treeNode
     * @returns
     */
    admindepartmentconfig.beforeRemove = function (treeId, treeNode) {
        if (treeNode.isParent) {
            AUI.dialog.alert("含有下级机构，不能删除！", null, 3);
            return false;
        }
        if (treeNode.editable != "true") {
            AUI.dialog.alert("没有权限删除该机构！", null, 3);
            return false;
        }
        AUI.dialog.confirm("确定删除 " + treeNode.name + "？", function (result) {
            if (result) {
                var zTree = AUI.tree.getTreeObj(treeId);
                admin_tools_obj.doAjax(G_webrootPath + "/service/page/department/serviceDepartment", {
                    cmd: "delDepartment",
                    id: treeNode.id
                }, function (data) {
                    if (data.errmsg) {
                        AUI.dialog.alert(data.errmsg, function () {
                            zTree.selectNode(treeNode);
                            admindepartmentconfig.zTreeOnClick(null, treeId, treeNode, null);
                        }, 3);
                    } else {
                        zTree.removeNode(treeNode);
                        $("#departmentconfig_departmentinfo_div").addClass("hidden");
                    }
                }, "POST", false);
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
    admindepartmentconfig.beforeDrag = function (treeId, treeNodes) {
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
    admindepartmentconfig.beforeDrop = function (treeId, treeNodes, targetNode, moveType, isCopy) {
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
    admindepartmentconfig.onDrop = function (event, treeId, treeNodes, targetNode,
                                             moveType, isCopy) {
        admin_tools_obj.doAjax(G_webrootPath + "/service/page/department/serviceDepartment", {
                cmd: "saveAndResort",
                id: treeNodes[0].id,
                targetid: targetNode.id,
                moveType: moveType
            },
            function (data) {
                var zTree = AUI.tree.getTreeObj(treeId);
                if (data.errmsg) {
                    AUI.dialog.alert(data.errmsg, function () {
                        admindepartmentconfig.generateDepartmentTree();
                    }, 3);
                } else {
                    zTree.selectNode(treeNodes[0]);
                    admindepartmentconfig.zTreeOnClick(null, treeId, treeNodes[0], null);
                }
            }, "POST", false);
    };

    /**
     * 新增节点时调用的函数
     *
     * @param treeId
     * @param treeNode
     */
    admindepartmentconfig.addNode = function (treeId, treeNode) {
        admin_tools_obj.doAjax(G_webrootPath + "/service/page/department/serviceDepartment", {
            cmd: "addDepartment",
            currNodeId: treeNode.id,
            currNodeLevel: treeNode.powerlevel
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var zTree = AUI.tree.getTreeObj(treeId);
                zTree.addNodes(treeNode, data);
                var node = zTree.getNodeByParam("id", data.id, treeNode);
                zTree.selectNode(node);
                admindepartmentconfig.zTreeOnClick(null, treeId, node, null);
            }
        }, "POST", false);
    };

    /**
     * 点击事件
     *
     * @param event
     * @param treeId
     * @param treeNode
     * @param clickFlag
     */
    admindepartmentconfig.zTreeOnClick = function (event, treeId, treeNode, clickFlag) {
        admindepartmentconfig.currNodeId = treeNode.id;
        admindepartmentconfig.currNodeName = treeNode.name;
        admindepartmentconfig.currEditable = treeNode.editable;
        if (treeNode.pid != "0") {
            admindepartmentconfig.initInfoPage();
        } else {
            $("#departmentconfig_departmentinfo_div").addClass("hidden");
        }
    };

    /**
     * 初始化详细信息数据
     */
    admindepartmentconfig.initInfoPage = function () {
        $("#departmentconfig_department_name").prop("readonly", true);
        $("#departmentconfig_department_code").prop("readonly", true);
        AUI.element.isEnabledSpinner("departmentconfig_department_sort", false);
        AUI.element.isEnabledDuallistSelected("departmentconfig_department_user_list", false);
        $("#departmentconfig_department_save_btn").hide();
        $("#departmentconfig_department_save_btn").prop("disabled", true);
        $("#departmentconfig_departmentinfo_div").removeClass("hidden");
        admin_tools_obj.doAjax(G_webrootPath + "/service/page/department/serviceDepartment", {
            cmd: "searchInfo",
            departmentid: admindepartmentconfig.currNodeId
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                $("#departmentconfig_departmentinfo_title").html('详细信息 <small> <i class=\"ace-icon fa fa-angle-double-right\"></i> ' + admindepartmentconfig.currNodeName + '</small>');
                $("#departmentconfig_department_name").val(data.department_name);
                $("#departmentconfig_department_code").val(data.department_code);
                AUI.element.setSpinnerValue("departmentconfig_department_sort", data.department_sort);
                AUI.element.setDuallistSelected("departmentconfig_department_user_list", data.select_users);
                if (admindepartmentconfig.currEditable == "true") {
                    $("#departmentconfig_department_name").prop("readonly", false);
                    $("#departmentconfig_department_code").prop("readonly", false);
                    AUI.element.isEnabledSpinner("departmentconfig_department_sort", true);
                    AUI.element.isEnabledDuallistSelected("departmentconfig_department_user_list", true);
                    $("#departmentconfig_department_save_btn").show();
                    $("#departmentconfig_department_save_btn").prop("disabled", false);
                    $("#departmentconfig_departmentinfo_div").focusInView({
                        top: 50
                    });
                }
            }
        }, "POST", false);
    };

    /**
     * 生成详细信息页面组件
     */
    admindepartmentconfig.generateInfoPage = function () {
        AUI.element.initSpinner("departmentconfig_department_sort", {
            min: 0,
            max: 999
        });
        AUI.element.initValidate("departmentconfig_departmentinfo_form", {
            departmentconfig_department_name: "请输入机构名称"
        });
        AUI.element.initDuallist("departmentconfig_department_user_list", {
            selectorMinimalHeight: 270,
            selectedListLabel: '<div class="col-sm-12 well well-sm center">已关联用户</div>',
            nonSelectedListLabel: '<div class="col-sm-12 well well-sm center">备选用户</div>'
        });
        $("#departmentconfig_department_save_btn").unbind("click").click(function () {
            if (admindepartmentconfig.currEditable != "true") {
                AUI.dialog.alert("权限不够，不能编辑该机构！", null, 3);
                return;
            }
            if (AUI.element.doValidate("departmentconfig_departmentinfo_form")) {
                admin_tools_obj.doAjax(G_webrootPath + "/service/page/department/serviceDepartment", {
                    cmd: "saveInfo",
                    departmentid: admindepartmentconfig.currNodeId,
                    department_name: $("#departmentconfig_department_name").val(),
                    department_code: $("#departmentconfig_department_code").val(),
                    department_sort: $("#departmentconfig_department_sort").val(),
                    select_users: $("#departmentconfig_department_user_list").val()
                }, function (data) {
                    if (data.errmsg) {
                        AUI.dialog.alert(data.errmsg, null, 3);
                    } else {
                        AUI.dialog.alert("保存成功！", function () {
                            $("#departmentconfig_department_save_btn").hide();
                            $("#departmentconfig_department_save_btn").prop("disabled", true);
                            admindepartmentconfig.generateDepartmentTree(function (treeId) {
                                var zTree = AUI.tree.getTreeObj(treeId);
                                var node = zTree.getNodeByParam("id", admindepartmentconfig.currNodeId);
                                zTree.selectNode(node);
                                admindepartmentconfig.zTreeOnClick(null, treeId, node, null);
                            });
                        }, 1);
                    }
                });
            }
        });
    };

    /**
     * 生成菜单树
     *
     * @param callBackFun
     */
    admindepartmentconfig.generateDepartmentTree = function (callBackFun) {
        var setting = {
            view: {
                addHoverDom: admindepartmentconfig.addHoverDom,
                removeHoverDom: admindepartmentconfig.removeHoverDom,
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
                showRemoveBtn: admindepartmentconfig.showRemoveBtn,
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
                beforeRemove: admindepartmentconfig.beforeRemove,
                beforeDrag: admindepartmentconfig.beforeDrag,
                beforeDrop: admindepartmentconfig.beforeDrop,
                beforeDragOpen: true,
                onDrop: admindepartmentconfig.onDrop,
                onClick: admindepartmentconfig.zTreeOnClick
            }
        };
        admin_tools_obj.doAjax(G_webrootPath + "/service/page/department/serviceDepartment", {cmd: "searchDepartment"}, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var departmenttree = $("#departmentconfig_departmenttree");
                AUI.tree.initTree(departmenttree, setting, data.tree);
                AUI.element.updateSelectItem("departmentconfig_department_user_list", data.users, "");
                if (typeof (callBackFun) == "function") {
                    callBackFun("departmentconfig_departmenttree");
                }
            }
        }, "POST", false);
    };

    $(function () {
        admindepartmentconfig.generateInfoPage();
        admindepartmentconfig.generateDepartmentTree();
    });
})($);