<?php
require dirname(__FILE__) . '/../common/pageHead.php';
$user = admin\service\tools\ToolsClass::getUser();
$powerconfig = service\user\UserManagerClass::validatePermissions($GLOBALS['application']->getId(), $user->getId(), 'powerconfig');
if (!$powerconfig) {
    header("HTTP/1.1 403 no permissions!");
    header("status: 403 no permissions!");
    echo("<span style=\"color:red\">no permissions!</span>");
    exit();
}
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>权限配置</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <!-- 内容 start -->
    <div class="row">
        <div class="tabs-left">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab"
                                      id="powerconfig_menuconfig_a" href="#powerconfig_menuconfig"> <i
                                class="pink ace-icon fa fa-list bigger-110"></i> 菜单
                    </a></li>
                <li><a data-toggle="tab" id="powerconfig_funcconfig_a"
                       href="#powerconfig_funcconfig"> <i
                                class="blue ace-icon fa fa-sliders bigger-110"></i> 功能
                    </a></li>
            </ul>
            <div class="tab-content">
                <div id="powerconfig_menuconfig" class="tab-pane in active">
                    <div class="col-sm-4 no-padding-left">
                        <div class="widget-box" style="height: 470px">
                            <div class="widget-header">
                                <h4 class="smaller">菜单结构</h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main no-padding">
                                    <ul id="powerconfig_menuconfig_menutree"
                                        class="ztree width-100" style="overflow: auto;height: 429px"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="powerconfig_menuconfig_menuinfo"
                         class="col-sm-8 no-padding hidden">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="smaller" id="powerconfig_menuconfig_menuinfo_title">详细信息</h4>
                            </div>
                            <div class="widget-body">
                                <div
                                        class="widget-main no-padding-left no-padding-right no-padding-bottom">
                                    <form class="form-horizontal">
                                        <ul class="nav nav-tabs padding-16">
                                            <li class="active"><a data-toggle="tab"
                                                                  href="#powerconfig_menuconfig_menuinfo_edit_basic"
                                                                  id="powerconfig_menuconfig_menuinfo_info_a"
                                                                  aria-expanded="true"> <i
                                                            class="green ace-icon fa fa-pencil-square-o bigger-125"></i>
                                                    信息
                                                </a></li>
                                            <li class=""><a data-toggle="tab"
                                                            href="#powerconfig_menuconfig_menuinfo_edit_param"
                                                            aria-expanded="false"> <i
                                                            class="blue ace-icon fa fa-link bigger-125"></i> 链接
                                                </a></li>
                                            <li class=""><a data-toggle="tab"
                                                            href="#powerconfig_menuconfig_menuinfo_edit_purview"
                                                            aria-expanded="false"> <i
                                                            class="red ace-icon fa fa-key bigger-125"></i> 权限
                                                </a></li>
                                        </ul>
                                        <div class="tab-content overflow-hidden">
                                            <div id="powerconfig_menuconfig_menuinfo_edit_basic"
                                                 class="tab-pane active">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_icon_preview">图标预览：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 control-label align-left no-padding-left">
                                                            <i id="powerconfig_menuconfig_menuinfo_icon_preview"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_icon_class">图标样式：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
                                                            <select class="form-control"
                                                                    id="powerconfig_menuconfig_menuinfo_icon_class"
                                                                    name="powerconfig_menuconfig_menuinfo_icon_class"
                                                                    data-placeholder="选择图标样式">
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_icon_color">图标颜色：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
                                                            <div class="input-group">
                                                                <input id="powerconfig_menuconfig_menuinfo_icon_color"
                                                                       name="powerconfig_menuconfig_menuinfo_icon_color"
                                                                       type="text" class="input-small"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_menu_name">菜单名称：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-12 no-padding-left">
                                                            <input id="powerconfig_menuconfig_menuinfo_menu_name"
                                                                   name="powerconfig_menuconfig_menuinfo_menu_name"
                                                                   type="text" class="input-xxlarge"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_menu_sort">菜单序号：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
                                                            <input type="text" class="input-xxlarge"
                                                                   id="powerconfig_menuconfig_menuinfo_menu_sort"
                                                                   name="powerconfig_menuconfig_menuinfo_menu_sort"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label
                                                            class="col-sm-2 control-label no-padding-right no-padding-top"
                                                            for="powerconfig_menuconfig_menuinfo_menu_status">启用状态：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
                                                            <label> <input
                                                                        id="powerconfig_menuconfig_menuinfo_menu_status"
                                                                        name="powerconfig_menuconfig_menuinfo_menu_status"
                                                                        class="ace ace-switch ace-switch-6"
                                                                        type="checkbox"/>
                                                                <span class="lbl"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="powerconfig_menuconfig_menuinfo_edit_param"
                                                 class="tab-pane">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_menu_model">链接类型：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
                                                            <select id="powerconfig_menuconfig_menuinfo_menu_model"
                                                                    name="powerconfig_menuconfig_menuinfo_menu_model"
                                                                    class="form-control">
                                                                <option value="0" selected>内部连接</option>
                                                                <option value="1">外部连接</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_page_url">链接地址：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-12 no-padding-left">
                                                            <input id="powerconfig_menuconfig_menuinfo_page_url"
                                                                   name="powerconfig_menuconfig_menuinfo_page_url"
                                                                   type="text" class="input-xxlarge"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_menuconfig_menuinfo_menu_opentype">链接模式：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
                                                            <select
                                                                    id="powerconfig_menuconfig_menuinfo_menu_opentype"
                                                                    name="powerconfig_menuconfig_menuinfo_menu_opentype"
                                                                    class="form-control">
                                                                <option value="0" selected>内嵌模式（div）</option>
                                                                <option value="1">内嵌模式（iframe）</option>
                                                                <option value="2">对话框模式（div）</option>
                                                                <option value="3">对话框模式（iframe）</option>
                                                                <option value="4">页面跳转</option>
                                                                <option value="5">新标签页</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-6 hidden"
                                                             id="powerconfig_menuconfig_menuinfo_dialog_size">
                                                            <label class="control-label"
                                                                   for="powerconfig_menuconfig_menuinfo_dialog_w"
                                                                   style="margin-left: 15px">宽：</label> <input
                                                                    id="powerconfig_menuconfig_menuinfo_dialog_w"
                                                                    name="powerconfig_menuconfig_menuinfo_dialog_w"
                                                                    type="text" style="width: 50px"/> <label
                                                                    class="control-label"
                                                                    for="powerconfig_menuconfig_menuinfo_dialog_h"
                                                                    style="margin-left: 15px">高：</label> <input
                                                                    id="powerconfig_menuconfig_menuinfo_dialog_h"
                                                                    name="powerconfig_menuconfig_menuinfo_dialog_h"
                                                                    type="text" style="width: 50px"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="powerconfig_menuconfig_menuinfo_edit_purview"
                                                 class="tab-pane">
                                                <select multiple="multiple" size="10"
                                                        name="powerconfig_menuconfig_menuinfo_rolelist"
                                                        id="powerconfig_menuconfig_menuinfo_rolelist">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-actions center no-margin">
                                            <button type="button"
                                                    class="btn btn-white btn-info btn-bold"
                                                    id="powerconfig_menuconfig_menuinfo_save_btn">
                                                <i class="ace-icon fa fa-floppy-o bigger-120 blue"></i> 保存
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="powerconfig_funcconfig" class="tab-pane">
                    <div class="col-sm-4 no-padding-left">
                        <div class="widget-box" style="height: 470px">
                            <div class="widget-header">
                                <h4 class="smaller">模块结构</h4>
                            </div>
                            <div class="widget-body">
                                <div class="widget-main no-padding">
                                    <ul id="powerconfig_funcconfig_moduletree"
                                        class="ztree width-100" style="overflow: auto; height: 429px"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="powerconfig_funcconfig_moduleinfo"
                         class="col-sm-8 no-padding hidden">
                        <div class="widget-box">
                            <div class="widget-header">
                                <h4 class="smaller"
                                    id="powerconfig_funcconfig_moduleinfo_title">详细信息</h4>
                            </div>
                            <div class="widget-body">
                                <div
                                        class="widget-main no-padding-left no-padding-right no-padding-bottom">
                                    <form class="form-horizontal"
                                          id="powerconfig_funcconfig_moduleinfo_form">
                                        <ul class="nav nav-tabs padding-16">
                                            <li class="active"><a data-toggle="tab"
                                                                  href="#powerconfig_funcconfig_moduleinfo_edit_basic"
                                                                  aria-expanded="true"> <i
                                                            class="green ace-icon fa fa-pencil-square-o bigger-125"></i>
                                                    信息
                                                </a></li>
                                            <li class=""><a data-toggle="tab"
                                                            href="#powerconfig_funcconfig_moduleinfo_edit_purview"
                                                            aria-expanded="false"> <i
                                                            class="red ace-icon fa fa-key bigger-125"></i> 模块权限
                                                </a></li>
                                            <li class=""><a data-toggle="tab"
                                                            id="powerconfig_funcconfig_moduleinfo_funcinfo_a"
                                                            href="#powerconfig_funcconfig_moduleinfo_edit_func"
                                                            aria-expanded="false"> <i
                                                            class="blue ace-icon fa fa-sliders bigger-125"></i> 功能
                                                </a></li>
                                        </ul>
                                        <div class="tab-content overflow-hidden">
                                            <div id="powerconfig_funcconfig_moduleinfo_edit_basic"
                                                 class="tab-pane active">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_funcconfig_moduleinfo_module_name">模块名称：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
																<span class="block input-icon input-icon-right"> <input
                                                                            id="powerconfig_funcconfig_moduleinfo_module_name"
                                                                            name="powerconfig_funcconfig_moduleinfo_module_name"
                                                                            type="text" required placeholder="模块名称"
                                                                            class="input-xxlarge"/></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label no-padding-right"
                                                           for="powerconfig_funcconfig_moduleinfo_module_code">模块编码：</label>
                                                    <div class="col-sm-10">
                                                        <div class="col-sm-6 no-padding-left">
																<span class="block input-icon input-icon-right"> <input
                                                                            id="powerconfig_funcconfig_moduleinfo_module_code"
                                                                            name="powerconfig_funcconfig_moduleinfo_module_code"
                                                                            type="text" required placeholder="模块编码"
                                                                            class="input-xxlarge"/></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="powerconfig_funcconfig_moduleinfo_edit_purview"
                                                 class="tab-pane">
                                                <select multiple="multiple" size="10"
                                                        name="powerconfig_funcconfig_moduleinfo_rolelist"
                                                        id="powerconfig_funcconfig_moduleinfo_rolelist">
                                                </select>
                                            </div>
                                            <div id="powerconfig_funcconfig_moduleinfo_edit_func"
                                                 class="tab-pane">
                                                <div class="col-xs-12 no-padding">
                                                    <table
                                                            id="powerconfig_funcconfig_moduleinfo_funcinfo_grid_table"></table>
                                                    <div
                                                            id="powerconfig_funcconfig_moduleinfo_funcinfo_grid_pager"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions center no-margin">
                                            <button type="button"
                                                    class="btn btn-white btn-info btn-bold"
                                                    id="powerconfig_funcconfig_moduleinfo_save_btn">
                                                <i class="ace-icon fa fa-floppy-o bigger-120 blue"></i> 保存
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 内容 end -->
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>