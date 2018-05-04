(function ($) {

    adminroleconfig = {};

    adminroleconfig.currNodeId = "";
    adminroleconfig.currNodeAppId = "";
    adminroleconfig.currNodeName = "";
    adminroleconfig.currNodePowerLevel = 0;

    /**
     * 鼠标移上节点时显示自定义控件（新增按钮）
     *
     * @param treeId
     * @param treeNode
     */
    adminroleconfig.addHoverDom = function (treeId, treeNode) {
        if (treeNode.pId == "0") {
            var sObj = $("#" + treeNode.tId + "_span");
            if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0)
                return;
            var addStr = "<span class='ace-icon fa fa-plus-circle green bigger-110' id='addBtn_"
                + treeNode.tId + "' title='新增'></span>";
            sObj.after(addStr);
            var btn = $("#addBtn_" + treeNode.tId);
            if (btn) {
                btn.bind("click", function () {
                    adminroleconfig.addNode(treeId, treeNode);
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
    adminroleconfig.removeHoverDom = function (treeId, treeNode) {
        $("#addBtn_" + treeNode.tId).unbind().remove();
    };

    /**
     * 是否显示删除按钮
     *
     * @param treeId
     * @param treeNode
     * @returns
     */
    adminroleconfig.showRemoveBtn = function (treeId, treeNode) {
        return !(treeNode.pId == "0" || treeNode.powerlevel == 0);
    };

    /**
     * 判断节点是否可以删除
     *
     * @param treeId
     * @param treeNode
     * @returns
     */
    adminroleconfig.beforeRemove = function (treeId, treeNode) {
        if (treeNode.isParent) {
            AUI.dialog.alert("含有下级，不能删除！", null, 3);
            return false;
        }
        AUI.dialog.confirm("确定删除 " + treeNode.name + "？", function (result) {
            if (result) {
                var zTree = AUI.tree.getTreeObj(treeId);
                admin_tools_obj.doAjax(G_webrootPath + "/service/role/serviceRole", {
                    cmd: "delRole",
                    id: treeNode.id
                }, function (data) {
                    if (data.errmsg) {
                        AUI.dialog.alert(data.errmsg, function () {
                            zTree.selectNode(treeNode);
                            adminroleconfig.zTreeOnClick(null, treeId, treeNode,
                                null);
                        }, 3);
                    } else {
                        zTree.removeNode(treeNode);
                        $("#roleconfig_roleinfo_div").addClass("hidden");
                    }
                }, "POST", false);
            }
        });
        return false;
    };

    /**
     * 新增节点时调用的函数
     *
     * @param treeId
     * @param treeNode
     */
    adminroleconfig.addNode = function (treeId, treeNode) {
        admin_tools_obj.doAjax(G_webrootPath + "/service/role/serviceRole", {
            cmd: "addRole",
            appid: treeNode.appid
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var zTree = AUI.tree.getTreeObj(treeId);
                zTree.addNodes(treeNode, data);
                var node = zTree.getNodeByParam("id", data.id, treeNode);
                zTree.selectNode(node);
                adminroleconfig.zTreeOnClick(null, treeId, node, null);
            }
        }, "POST", false);
    };

    /**
     * 获取父节点名称
     *
     * @param treeid
     * @param treeNode
     * @returns {String}
     */
    adminroleconfig.getParentName = function (treeid, treeNode) {
        if (treeNode.pId != "0") {
            var parentNode = treeNode.getParentNode();
            return adminroleconfig.getParentName(treeid, parentNode) + "|#|"
                + parentNode.name;
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
    adminroleconfig.zTreeOnClick = function (event, treeId, treeNode, clickFlag) {
        adminroleconfig.currNodeId = treeNode.id;
        adminroleconfig.currNodeAppId = treeNode.appid;
        adminroleconfig.currNodeName = adminroleconfig.getParentName(treeId, treeNode)
            + "|#|" + treeNode.name;
        adminroleconfig.currNodeName = adminroleconfig.currNodeName.substring(3).replace(
            /\|#\|/ig,
            " <i class=\"ace-icon fa fa-angle-double-right\"></i> ");
        adminroleconfig.currNodePowerLevel = treeNode.powerlevel;
        if (treeNode.pId != "0") {
            adminroleconfig.initInfoPage();
        } else {
            $("#roleconfig_roleinfo_div").addClass("hidden");
        }
    };

    /**
     * 初始化详细信息数据
     */
    adminroleconfig.initInfoPage = function () {
        $("#roleconfig_roleinfo_save_btn").hide();
        $("#roleconfig_roleinfo_save_btn").prop("disabled", true);
        $("#roleconfig_roleinfo_div").removeClass("hidden");
        admin_tools_obj.doAjax(G_webrootPath + "/service/role/serviceRole", {
            cmd: "searchInfo",
            roleid: adminroleconfig.currNodeId,
            appid: adminroleconfig.currNodeAppId
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                $("#roleconfig_roleinfo_title").html('详细信息 <small>' + adminroleconfig.currNodeName + '</small>');
                $("#roleconfig_roleinfo_role_name").val(data.role_name);
                var min = 1;
                var max = 999;
                if (adminroleconfig.currNodePowerLevel == 0) {
                    min = 0;
                    max = 0;
                }
                AUI.element.refreshSpinner("roleconfig_roleinfo_role_level", {
                    min: min,
                    max: max
                });
                AUI.element.setSpinnerValue("roleconfig_roleinfo_role_level", data.role_level);
                AUI.element.setSpinnerValue("roleconfig_roleinfo_role_sort", data.role_sort);
                if (adminroleconfig.currNodePowerLevel == 0) {
                    $("#roleconfig_roleinfo_role_name").prop("disabled", true);
                    AUI.element.isEnabledSpinner("roleconfig_roleinfo_role_level", false);
                    AUI.element.isEnabledSpinner("roleconfig_roleinfo_role_sort", false);
                } else {
                    $("#roleconfig_roleinfo_role_name").prop("disabled", false);
                    AUI.element.isEnabledSpinner("roleconfig_roleinfo_role_level", true);
                    AUI.element.isEnabledSpinner("roleconfig_roleinfo_role_sort", true);
                }
                AUI.element.setDuallistSelected("roleconfig_roleinfo_user_list", data.select_users);
                adminroleconfig.generateMenuTree(data.menutree);
                adminroleconfig.generateFuncTree(data.functree);
                $("#roleconfig_roleinfo_save_btn").show();
                $("#roleconfig_roleinfo_save_btn").prop("disabled", false);
                $("#roleconfig_roleinfo_div").focusInView({
                    top: 50
                });
            }
        }, "POST", false);
    };

    /**
     * 生成详细信息页面组件
     */
    adminroleconfig.generateInfoPage = function () {
        AUI.element.initSpinner("roleconfig_roleinfo_role_level", {
            min: 0,
            max: 999
        });
        AUI.element.initSpinner("roleconfig_roleinfo_role_sort", {
            min: 0,
            max: 999
        });
        AUI.element.initValidate("roleconfig_roleinfo_form", {
            roleconfig_roleinfo_role_name: "请输入角色名称"
        });
        AUI.element.initDuallist("roleconfig_roleinfo_user_list", {
            selectorMinimalHeight: 270,
            selectedListLabel: '<div class="col-sm-12 well well-sm center">已关联用户</div>',
            nonSelectedListLabel: '<div class="col-sm-12 well well-sm center">备选用户</div>'
        });
        $("#roleconfig_roleinfo_save_btn").unbind("click").click(function () {
            if (AUI.element.doValidate("roleconfig_roleinfo_form")) {
                var menus = AUI.tree.getCheckedNodeIds("roleconfig_roleinfo_menutree");
                var modulefuncs = AUI.tree.getTreeObj("roleconfig_roleinfo_functree").getCheckedNodes(true);
                modulefuncs = AUI.tree.nodeAttrFilter(modulefuncs, ["id", "type"]);
                admin_tools_obj.doAjax(G_webrootPath + "/service/role/serviceRole", {
                    cmd: "saveInfo",
                    currNodePowerLevel: adminroleconfig.currNodePowerLevel,
                    roleid: adminroleconfig.currNodeId,
                    role_name: $("#roleconfig_roleinfo_role_name").val(),
                    role_level: $("#roleconfig_roleinfo_role_level").val(),
                    role_sort: $("#roleconfig_roleinfo_role_sort").val(),
                    select_users: $("#roleconfig_roleinfo_user_list").val(),
                    select_menus: menus,
                    select_modulefuncs: modulefuncs
                }, function (data) {
                    if (data.errmsg) {
                        AUI.dialog.alert(data.errmsg, null, 3);
                    } else {
                        AUI.dialog.alert("保存成功！", function () {
                            $("#roleconfig_roleinfo_save_btn").hide();
                            $("#roleconfig_roleinfo_save_btn").prop("disabled", true);
                            adminroleconfig.generateRoleTree(function (treeId) {
                                var zTree = AUI.tree.getTreeObj(treeId);
                                var node = zTree.getNodeByParam("id", adminroleconfig.currNodeId);
                                zTree.selectNode(node);
                                adminroleconfig.zTreeOnClick(null, treeId, node, null);
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
    adminroleconfig.generateRoleTree = function (callBackFun) {
        var setting = {
            view: {
                addHoverDom: adminroleconfig.addHoverDom,
                removeHoverDom: adminroleconfig.removeHoverDom,
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
                showRemoveBtn: adminroleconfig.showRemoveBtn,
                showRenameBtn: false
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pId",
                    rootPId: "0"
                }
            },
            callback: {
                beforeRemove: adminroleconfig.beforeRemove,
                onClick: adminroleconfig.zTreeOnClick
            }
        };
        admin_tools_obj.doAjax(G_webrootPath + "/service/role/serviceRole", {
            cmd: "searchRole"
        }, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, null, 3);
            } else {
                var roletree = $("#roleconfig_roletree");
                AUI.tree.initTree(roletree, setting, data.tree);
                AUI.element.updateSelectItem("roleconfig_roleinfo_user_list", data.users, "");
                if (typeof (callBackFun) == "function") {
                    callBackFun("roleconfig_roletree");
                }
            }
        }, "POST", false);
    };

    /**
     * 初始化菜单树
     *
     * @param menutree
     */
    adminroleconfig.generateMenuTree = function (menutree) {
        var setting = {
            check: {
                enable: true,
                chkStyle: "checkbox"
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pId",
                    rootPId: "0"
                }
            }
        };
        var obj = $("#roleconfig_roleinfo_menutree");
        AUI.tree.initTree(obj, setting, menutree);
    };

    /**
     * 初始化功能树
     *
     * @param functree
     */
    adminroleconfig.generateFuncTree = function (functree) {
        var setting = {
            check: {
                enable: true,
                chkStyle: "checkbox"
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pId",
                    rootPId: "0"
                }
            }
        };
        var obj = $("#roleconfig_roleinfo_functree");
        AUI.tree.initTree(obj, setting, functree);
    };

    $(function () {
        adminroleconfig.generateInfoPage();
        adminroleconfig.generateRoleTree();
    });
})($);