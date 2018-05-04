/** ztree start */
(function ($) {

    var tree = {};

    /**
     * 生成树
     *
     * @param treeObj
     * @param treeSetting
     * @param treeNodes
     */
    tree.initTree = function (treeObj, treeSetting, treeNodes) {
        $.fn.zTree.init($(treeObj), treeSetting, treeNodes);
    };

    /**
     * 获取树对象
     *
     * @param id
     */
    tree.getTreeObj = function (id) {
        return $.fn.zTree.getZTreeObj(id);
    };

    /**
     * 打开树形选择对话框
     * @param param 树形参数
     * @param callBackFunc 回调函数 function(rtn); rtn=(Array)[{name:"",id:""},{name:"",id:""},...]
     * @description url返回的数据是节点数组，每个节点可识别的参数有：
     *                  id：         id
     *                  name：       名称
     *                  pId：        父id，根节点为0
     *                  iconSkin：   图标皮肤名称
     *                  isParent：   是否是父节点
     *                  open：       是否打开
     *                  nocheck：    是否不能被选中
     */
    tree.showTree = function (param, callBackFunc) {
        param = $.extend({
            title: "选择",//选择对话框标题
            isCheckBox: false, // 是否是多选树（默认是单选）
            selectroot: true, // 是否可以选择根节点
            getid: true, // 返回的选择结果中是否需要包含ID
            getAllPath: true, // 返回的选择结果是否需要全路径
            initUrl: undefined,//初始化树节点时的连接地址,post请求
            initParam: {},//初始化树节点请求需要传的post参数
            isSynch: true,// 是否使用同步加载模式
            isShowSearch: true,// 是否显示搜索区域（isSynch为true时有效）
            datas: "",// 初始化时选中的节点id（isSynch为true时有效）
            selectall: false, // 是否可以全选（isSynch为true时有效）
            url: undefined,// 展开树节点时调用的请求地址,post参数id（isSynch为false时有效）
            asyncParam: []// 异步请求提交的额外参数
        }, param);
        var height = 505;
        if (!param.isSynch || !param.isShowSearch) {
            height = 460;
        }
        AUI.dialog.inDialog(400, height, param.title, {
            innerUrl: "/view/plugins/tree/treeView",
            params: param
        }, undefined, callBackFunc);
    };

    /**
     * 获取选中节点的id数组
     * @param id
     * @param idKey 默认“id”
     * @returns {Array}
     */
    tree.getCheckedNodeIds = function (id, idKey) {
        idKey = idKey || "id";
        var nodes = AUI.tree.getTreeObj(id).getCheckedNodes(true);
        var ids = [];
        for (var i = 0; i < nodes.length; i++) {
            ids.push(nodes[i][idKey]);
        }
        return ids;
    };

    tree.nodeAttrFilter = function (nodes, includeKeyArray) {
        if (!includeKeyArray) {
            return nodes;
        }
        var result = [];
        for (var i = 0; i < nodes.length; i++) {
            var node = {};
            for (var j = 0; j < includeKeyArray.length; j++) {
                node[includeKeyArray[j]] = nodes[i][includeKeyArray[j]];
            }
            result.push(node);
        }
        return result;
    };

    AUI.tree = tree;
})($);
/** ztree end */
