(function ($) {

    var grid_selectorId = "userconfig_grid_table";
    var pager_selectorId = "userconfig_grid_pager";

    adminuserconfig = {};

    /**
     * 生成用户列表
     *
     * @param grid_selectorId
     * @param pager_selectorId
     */
    adminuserconfig.generateGrid = function (grid_selectorId, pager_selectorId) {
        var grid_selector = grid_selectorId;
        var pager_selector = pager_selectorId;
        var param = {
            url: G_webrootPath + "/service/page/user/serviceUser",
            postData: {
                search_username: function () {
                    return $("#userconfig_query_username").val();
                },
                search_loginno: function () {
                    return $("#userconfig_query_loginno").val();
                },
                search_level: function () {
                    return $("#userconfig_query_userlevel").val();
                },
                search_status: function () {
                    return $("#userconfig_query_userstatus").val();
                },
                search_departmentname: function () {
                    return $("#userconfig_query_userdepartmentname").val();
                },
                search_roleids: function () {
                    var rolenames = $("#userconfig_query_userrolenames").val();
                    if (!rolenames) {
                        return "";
                    } else {
                        return rolenames;
                    }
                }
            },
            multiselect: true,
            height: 290,
            sortname: "levels,sort,status",
            colNames: ['编辑', 'id', '姓名', '登录账号', '角色组', '所属机构', '级别', '序号', '状态'],
            colModel: [
                {
                    name: 'myac',
                    index: '',
                    width: 70,
                    fixed: true,
                    sortable: false,
                    align: 'center',
                    formatter: function (cellvalue, options, rowObject) {
                        return "<div style=\"margin-left:8px;\">"
                            + "<div title=\"编辑所选记录\" style=\"float:left;cursor:pointer;\" class=\"ui-pg-div ui-inline-edit\" "
                            + "onclick=\"adminuserconfig.editRecord('"
                            + rowObject.id
                            + "','"
                            + rowObject.name
                            + "',"
                            + rowObject.levels
                            + ");\" "
                            + "onmouseover=\"$(this).addClass('ui-state-hover');\" "
                            + "onmouseout=\"$(this).removeClass('ui-state-hover')\">"
                            + "<span class=\"ui-icon ui-icon-pencil\"></span></div>"
                            + "<div title=\"删除所选记录\" style=\"float:left;margin-left:5px;\" class=\"ui-pg-div ui-inline-del\" "
                            + "onclick=\"adminuserconfig.delRecord('"
                            + rowObject.id
                            + "','"
                            + rowObject.name
                            + "',"
                            + rowObject.levels
                            + ");\" "
                            + "onmouseover=\"$(this).addClass('ui-state-hover');\" "
                            + "onmouseout=\"$(this).removeClass('ui-state-hover');\">"
                            + "<span class=\"ui-icon ui-icon-trash\"></span></div><div>";
                    }
                },
                {
                    name: 'id',
                    index: 'id',
                    hidden: true
                },
                {
                    name: 'name',
                    index: 'name',
                    fixed: true,
                    width: 80
                },
                {
                    name: 'loginno',
                    index: 'loginno',
                    fixed: true,
                    width: 100
                },
                {
                    name: 'rolenams',
                    index: 'rolenams'
                },
                {
                    name: 'departmentnames',
                    index: 'departmentnames'
                },
                {
                    name: 'levels',
                    index: 'levels',
                    align: 'center',
                    fixed: true,
                    width: 60
                },
                {
                    name: 'sort',
                    index: 'sort',
                    align: 'center',
                    fixed: true,
                    width: 60
                },
                {
                    name: 'status',
                    index: 'status',
                    align: 'center',
                    fixed: true,
                    width: 60,
                    formatter: function (cellvalue, options, rowObject) {
                        var temp = "";
                        if (cellvalue == "1") {
                            temp = "<div class='blue bolder grid-form-field-div align-center width-100'>启用</div>";
                        } else {
                            temp = "<div class='red bolder grid-form-field-div align-center width-100'>禁用</div>";
                        }
                        return temp;
                    }
                }]
        };
        AUI.grid.generateGrid(grid_selector, pager_selector, param);
    };

    /**
     * 绑定事件
     */
    adminuserconfig.initEvent = function () {
        $("#userconfig_query_userlevel").blur(function () {
            var value = $(this).val();
            if (isNaN(Number(value))) {
                $(this).val("");
            }
        });
        $("#userconfig_reset_btn").click(function () {
            $("#userconfig_conditionform")[0].reset();
            AUI.element.refreshChosen("userconfig_query_userrolenames");
        });
        $("#userconfig_query_btn").click(function () {
            AUI.grid.refreshGrid(grid_selectorId, true);
        });
        $("#userconfig_del_btn").click(function () {
            adminuserconfig.delRecords();
        });
        $("#userconfig_add_btn").click(function () {
            adminuserconfig.addRecord();
        });
    };

    /**
     * 新增
     */
    adminuserconfig.addRecord = function () {
        AUI.dialog.inDialog(800, 553, "用户详细信息", {
            innerUrl: G_webrootPath + "/view/page/user/userinfo"
        }, null, function (rtn) {
            if (rtn) {
                AUI.grid.refreshGrid(grid_selectorId);
            }
        });
    };

    /**
     * 编辑
     */
    adminuserconfig.editRecord = function (id, name, level) {
        if (parseInt(level) > G_curr_user.level) {
            AUI.dialog.inDialog(800, 550, "用户详细信息", {
                innerUrl: G_webrootPath + "/view/page/user/userinfo?id=" + id
            }, null, function (rtn) {
                if (rtn) {
                    AUI.grid.refreshGrid(grid_selectorId);
                }
            });
        } else {
            AUI.dialog.alert("更高级别用户【" + name + "】不能编辑！", null, 3);
        }
    };

    /**
     * 单个删除
     */
    adminuserconfig.delRecord = function (id, name, level) {
        if (level > G_curr_user.level) {
            AUI.dialog.confirm("确定删除用户【" + name + "】？", function (data) {
                if (data) {
                    var user = {};
                    user.id = id;
                    user.name = name;
                    user.level = level;
                    var userlist = [];
                    userlist.push(user);
                    adminuserconfig.doDelRecord(userlist);
                }
            });
        } else {
            AUI.dialog.alert("更高级别用户【" + name + "】不允许删除！", null, 3);
        }
    };

    /**
     * 批量删除
     */
    adminuserconfig.delRecords = function () {
        var userlist = [];
        var ids = AUI.grid.getSelectedIDs(grid_selectorId);
        if (ids.length > 0) {
            for (var i = 0; i < ids.length; i++) {
                var id = ids[i];
                var rowData = AUI.grid.getRowData(grid_selectorId, id);
                if (parseInt(rowData.levels) > G_curr_user.level) {
                    var user = {};
                    user.id = rowData.id;
                    user.name = rowData.name;
                    user.level = parseInt(rowData.levels);
                    userlist.push(user);
                } else {
                    AUI.dialog.alert("更高级别用户【" + rowData.name + "】不允许删除！", null, 3);
                    return;
                }
            }
            AUI.dialog.confirm("确定删除所选用户？", function (data) {
                if (data) {
                    adminuserconfig.doDelRecord(userlist);
                }
            });
        } else {
            AUI.dialog.alert("请选择需要删除的数据！", null, 3);
        }
    };

    /**
     * 执行删除
     *
     * @param userlist
     */
    adminuserconfig.doDelRecord = function (userlist) {
        if (userlist.length > 0) {
            admin_tools_obj.doAjax(G_webrootPath + "/service/page/user/serviceUser",
                {
                    oper: "del",
                    users: JSON.stringify(userlist)
                }, function (data) {
                    if (data.errmsg) {
                        AUI.dialog.alert(data.errmsg, null, 3);
                    } else {
                        AUI.dialog.alert(data.result, function () {
                            $("#userconfig_query_btn").click();
                        }, 1);
                    }
                });
        }
    };

    $(function () {
        AUI.element.initChosen("#userconfig_query_userrolenames");
        adminuserconfig.generateGrid(grid_selectorId, pager_selectorId);
        adminuserconfig.initEvent();
    });
})($);