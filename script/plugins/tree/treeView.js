/**
 * Created by zhang on 2016/8/11.
 */
(function ($) {

    treeViewPluginObj = {};

    var params = {
        title: "选择",//选择对话框标题
        isCheckBox: false, // 是否是多选树（默认是单选）
        selectroot: false, // 是否可以选择根节点
        getid: true, // 返回的选择结果中是否需要包含ID
        getAllPath: true, // 返回的选择结果是否需要全路径
        initUrl: undefined,//初始化树节点时的连接地址,post请求
        initParam: {},//初始化树节点请求需要传的post参数
        isSynch: true,// 是否使用同步加载模式
        isShowSearch: true,// 是否显示搜索区域（isSynch为true时有效）
        datas: "",// 初始化时选中的节点id（isSynch为true时有效）
        url: undefined,// 展开树节点时调用的请求地址,post参数id（isSynch为false时有效）
        asyncParam: []// 异步请求提交的额外参数
    };

    var treeid = "_treeView_treeid";

    var treeSetting;

    var zNodes = null;

    /**
     * 初始化配置
     */
    function initTreeParams() {
        params = $.extend(params, AUI.dialog.getParams($("#" + treeid)));
        var check = null;
        var async = {};
        var url = "";
        if (!params.isSynch) {// 异步模式
            if (params.url) {
                url = params.url;
            }
            async = {
                enable: true,
                url: url,
                type: "post",
                dataType: "json",
                autoParam: ["id"],
                otherParam: params.asyncParam,
                dataFilter: dataFilter
            };
        } else {
            if (params.isShowSearch) {
                jQuery("#_treeView_searchNodeArea").show();
            }
        }
        if (params.isCheckBox) {
            check = {
                enable: true,
                chkboxType: {
                    "Y": "p",
                    "N": "ps"
                }
            };
        } else {
            check = {
                enable: true,
                chkStyle: "radio",
                radioType: "all"
            };
        }
        return {
            async: async,
            view: {
                selectedMulti: true
            },
            check: check,
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "pId",
                    rootPId: 0
                }
            },
            callback: {
                onAsyncSuccess: onAsyncSuccess,
                onAsyncError: zTreeOnAsyncError
            }
        };
    }

    /**
     * 初始化树
     */
    treeViewPluginObj.initTree = function () {
        treeSetting = initTreeParams();
        _global_tools_obj.doAjax(params.initUrl, params.initParam, function (data) {
            if (data.errmsg) {
                AUI.dialog.alert(data.errmsg, undefined, 3);
                return;
            }
            zNodes = [];
            if (!params.isSynch) {// 异步模式
                for (var ia = 0; ia < data.length; ia++) {
                    data[ia].pId = 0;
                    data[ia].open = false;
                    if (!params.selectroot) {
                        data[ia].nocheck = true;
                    }
                    data[ia].isParent = true;
                    if (!data[ia].iconSkin) {
                        data[ia].iconSkin = "selecttree";
                    }
                    zNodes.push(data[ia]);
                }
            } else {
                for (var is = 0; is < data.length; is++) {
                    if (!data[is].pId || data[is].pId == null || data[is].pId == "" || data[is].pId == "0" || data[is].pId == 0) {
                        data[is].pId = 0;
                        if (!params.selectroot) {
                            data[is].nocheck = true;
                        }
                    }
                    if (!data[is].iconSkin) {
                        data[is].iconSkin = "selecttree";
                    }
                    zNodes.push(data[is]);
                }
                if (params.datas != "") {// 初始化选中节点
                    var tmpvalues = params.datas.split(",");
                    for (var n = 0; n < tmpvalues.length; n++) {
                        var selectids = getSelectedNodes(tmpvalues[n]);// 选中节点全路径，用"/"分隔
                        if (selectids != "") {
                            var idlist = selectids.split("/");
                            for (var iin = 0; iin < idlist.length; iin++) {
                                for (var jn = 0; jn < zNodes.length; jn++) {
                                    if (zNodes[jn].id == idlist[iin]) {
                                        if (iin < idlist.length - 1) {
                                            zNodes[jn].open = true;
                                        } else {
                                            zNodes[jn].checked = true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            AUI.tree.initTree($("#" + treeid), treeSetting, zNodes);
        }, "POST", false);
    };

    function getSelectedNodes(value, isSearch) {
        if (value != "") {
            var valuelist = value.split("/");
            var selectids = "";// 选中节点全路径id，用"/"分隔
            var lastvalue = "";
            if (valuelist.length > 1) {
                lastvalue = valuelist[valuelist.length - 1];// 获取叶子节点数据
            } else {
                lastvalue = valuelist[0];// 获取叶子节点数据
            }
            for (var jindex = 0; jindex < zNodes.length; jindex++) {
                if ((!isSearch && params.getid && zNodes[jindex].id == lastvalue) || ((isSearch && zNodes[jindex].name.indexOf(lastvalue) > -1) || (!isSearch && zNodes[jindex].name == lastvalue))) {// 找到叶子节点
                    if (selectids != "") {
                        selectids += ",";
                    }
                    if (zNodes[jindex].pId != 0) {
                        if (!params.getid && params.getAllPath && valuelist.length > 1 && !isSearch) {
                            selectids += getParentNodeIdsByAllPath(zNodes[jindex], zNodes, valuelist, valuelist.length - 2);
                        } else {
                            selectids += getParentNodeIds(zNodes[jindex], zNodes);
                        }
                        if (selectids != "") {
                            selectids += "/";
                        }
                    }
                    selectids += zNodes[jindex].id;
                }
            }
            return selectids;
        } else {
            return "";
        }
    }

    function getParentNodeIdsByAllPath(node, zNodes, valuelist, index) {
        for (var i = 0; i < zNodes.length; i++) {
            if (zNodes[i].id == node.pId
                && ((params.getid && zNodes[i].id == valuelist[index]) || zNodes[i].name == valuelist[index])) {
                if (index == 0) {
                    return zNodes[i].id;
                } else {
                    var parentNodeId = getParentNodeIdsByAllPath(zNodes[i], zNodes,
                        valuelist, index - 1);
                    if (parentNodeId != "") {
                        return getParentNodeIdsByAllPath(zNodes[i], zNodes, valuelist, index - 1) + "/" + zNodes[i].id;
                    } else {
                        return "";
                    }
                }
            }
        }
        return "";
    }

    function getParentNodeIds(node, zNodes) {
        for (var i = 0; i < zNodes.length; i++) {
            if (zNodes[i].id == node.pId) {
                if (zNodes[i].pId == 0) {
                    return zNodes[i].id;
                } else {
                    return getParentNodeIds(zNodes[i], zNodes) + "/" + zNodes[i].id;
                }
            }
        }
    }

    function getChildNodeIds(nodeid, zNodes) {
        var result = [];
        for (var i = 0; i < zNodes.length; i++) {
            if (zNodes[i].pId == nodeid) {
                result.push(zNodes[i].id);
                var children = getChildNodeIds(zNodes[i].id, zNodes);
                if (children.length > 0) {
                    result = result.concat(children);
                }
            }
        }
        return result;
    }

    function cancelHalf(treeNode) {
        var zTree = AUI.tree.getTreeObj(treeid);
        treeNode.halfCheck = false;
        zTree.updateNode(treeNode); // 异步加载成功后刷新树节点
    }

    function dataFilter(treeId, parentNode, responseData) {
        if (typeof (responseData) == "string") {
            var str = String(responseData);
            str = str.substring(str.lastIndexOf("["), str.lastIndexOf("]") + 1);
            return $.parseJSON(str);
        } else {
            for (var i = 0; i < responseData.length; i++) {
                if (!responseData[i].iconSkin) {
                    responseData[i].iconSkin = "selecttree";
                }
            }
            return responseData;
        }
    }

    function onAsyncSuccess(event, treeId, treeNode, msg) {
        cancelHalf(treeNode);
    }

    function zTreeOnAsyncError(event, treeId, treeNode, XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
    }

    treeViewPluginObj.searchNodeByKey = function (obj) {
        var keyPressed = 0;
        if (window.event) {
            keyPressed = window.event.keyCode; // IE
        } else {
            keyPressed = window.event.which; // Firefox
        }
        if (keyPressed == 13) {
            treeViewPluginObj.searchNode($(obj).next().children().eq(0));
            return false;
        }
    };

    treeViewPluginObj.searchNode = function (obj) {
        var searchvalue = $(obj).parent().prev().val();
        var selectids = getSelectedNodes(searchvalue, true);
        var treeObj = AUI.tree.getTreeObj(treeid);
        var treeNodes = treeObj.getCheckedNodes(true);
        var tmpdata = [];
        for (var i = 0; i < treeNodes.length; i++) {
            for (var node = treeNodes[i]; node != undefined; node = node.getParentNode()) {
                var tmpnodeChecked = {};
                tmpnodeChecked.id = node.id;
                tmpnodeChecked.name = node.name;
                tmpnodeChecked.pId = node.pId;
                tmpnodeChecked.nocheck = node.nocheck;
                tmpnodeChecked.checked = node.checked;
                tmpnodeChecked.open = node.open;
                tmpnodeChecked.iconSkin = node.iconSkin;
                tmpdata.push(tmpnodeChecked);
            }
        }
        if (selectids != "") {
            var nodepathes = selectids.split(/,/);
            var leafnodes = [];
            var nodelist = [];
            for (var np = 0; np < nodepathes.length; np++) {
                var tmp = nodepathes[np].split(/\//);
                var leafnode = tmp[tmp.length - 1];
                nodelist = nodelist.concat(tmp, getChildNodeIds(leafnode, zNodes));
                leafnodes.push(leafnode);
            }
            for (var jn = 0; jn < zNodes.length; jn++) {
                var id = zNodes[jn].id;
                var isPass = false;
                for (var isp = 0; isp < tmpdata.length; isp++) {
                    if (id == tmpdata[isp].id) {
                        isPass = true;
                        break;
                    }
                }
                if (isPass) {
                    continue;
                }
                var isExit = false;
                for (var nin = 0; nin < nodelist.length; nin++) {
                    if (id == nodelist[nin]) {
                        isExit = true;
                        break;
                    }
                }
                if (isExit) {
                    var tmpnode = _global_tools_obj.objClone(zNodes[jn]);
                    tmpnode.open = true;
                    tmpnode.checked = false;
                    for (var idi = 0; idi < leafnodes.length; idi++) {
                        if (id == leafnodes[idi]) {
                            tmpnode.open = false;
                            break;
                        }
                    }
                    tmpdata.push(tmpnode);
                }
            }
        }
        if (tmpdata.length > 0) {
            AUI.tree.initTree($("#" + treeid), treeSetting, tmpdata);
        } else {
            $("#" + treeid).html("找不到结果");
        }
    };

    treeViewPluginObj.resetTree = function (obj) {
        $(obj).prev().prev().val("");
        AUI.tree.initTree($("#" + treeid), treeSetting, zNodes);
    };

    treeViewPluginObj.cancle = function (obj) {
        AUI.dialog.closeDialog(obj);
    };

    treeViewPluginObj.clearCheck = function () {
        var treeObj = AUI.tree.getTreeObj(treeid);
        var treeNodes = treeObj.getCheckedNodes(true);
        for (var i = 0; i < treeNodes.length; i++) {
            treeObj.checkNode(treeNodes[i], false, true, false);
        }
    };

    treeViewPluginObj.save = function (obj) {
        var results = [];
        var treeObj = AUI.tree.getTreeObj(treeid);
        var treeNodes = treeObj.getCheckedNodes(true);
        var leafNodes = [];
        for (var is = 0; is < treeNodes.length; is++) {
            if (treeNodes[is].children == undefined) {
                leafNodes.push(treeNodes[is]);
            } else {
                var childrenNodes = treeNodes[is].children;
                var flag = 0;
                for (var js = 0; js < childrenNodes.length; js++) {
                    if (childrenNodes[js].checked == true) {
                        flag++;
                        break;
                    }
                }
                if (flag == 0) {
                    leafNodes.push(treeNodes[is]);
                }
            }
        }
        if (params.getAllPath) {
            for (var inn = 0; inn < leafNodes.length; inn++) {
                var node = {};
                var names = "";
                var ids = "";
                for (var pNode = leafNodes[inn]; pNode != undefined; pNode = pNode.getParentNode()) {
                    if (names != "") {
                        names = pNode.name + "/" + names;
                    } else {
                        names = pNode.name;
                    }
                    if (ids != "") {
                        ids = pNode.id + "/" + ids;
                    } else {
                        ids = pNode.id;
                    }
                }
                node.name = names;
                if (params.getid) {
                    node.id = ids;
                }
                results.push(node);
            }
        } else {
            for (var i = 0; i < leafNodes.length; i++) {
                var nodeNomal = {};
                nodeNomal.name = leafNodes[i].name;
                if (params.getid) {
                    nodeNomal.id = leafNodes[i].id;
                }
                results.push(nodeNomal);
            }
        }
        AUI.dialog.closeDialog(obj, results);
    };

    $(function () {
        treeViewPluginObj.initTree();
    });
})($);