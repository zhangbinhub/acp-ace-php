<?php
require dirname(__FILE__) . '/../common/pageHead.php';
$user = admin\service\tools\ToolsClass::getUser();
$roleconfig = service\user\UserManagerClass::validatePermissions($GLOBALS['application']->getId(), $user->getId(), 'roleconfig');
if(!$roleconfig){
    header("HTTP/1.1 403 no permissions!");
    header("status: 403 no permissions!");
    echo ("<span style=\"color:red\">no permissions!</span>");
    exit();
}
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>角色配置</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <!-- 内容 start -->
    <div class="row">
        <div class="col-sm-4">
            <div class="widget-box" style="height: 470px">
                <div class="widget-header">
                    <h4 class="smaller">角色组</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <ul id="roleconfig_roletree" class="ztree width-100"
                            style="overflow: auto; height: 428px"></ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8 hidden" id="roleconfig_roleinfo_div">
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="smaller" id="roleconfig_roleinfo_title">详细信息</h4>
                </div>
                <div class="widget-body">
                    <div
                        class="widget-main no-padding-left no-padding-right no-padding-bottom">
                        <form class="form-horizontal" id="roleconfig_roleinfo_form">
                            <ul class="nav nav-tabs padding-16">
                                <li class="active"><a data-toggle="tab"
                                                      href="#roleconfig_roleinfo_edit_basic" aria-expanded="true"> <i
                                            class="green ace-icon fa fa-pencil-square-o bigger-125"></i>
                                        角色信息
                                    </a></li>
                                <li class=""><a data-toggle="tab"
                                                href="#roleconfig_roleinfo_edit_users" aria-expanded="false">
                                        <i class="blue ace-icon fa fa-user-plus bigger-125"></i> 关联用户
                                    </a></li>
                                <li class=""><a data-toggle="tab"
                                                href="#roleconfig_roleinfo_edit_menu" aria-expanded="false"> <i
                                            class="pink ace-icon fa fa-list bigger-125"></i> 菜单权限
                                    </a></li>
                                <li class=""><a data-toggle="tab"
                                                href="#roleconfig_roleinfo_edit_func" aria-expanded="false"> <i
                                            class="blue ace-icon fa fa-sliders bigger-125"></i> 功能权限
                                    </a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="roleconfig_roleinfo_edit_basic"
                                     class="tab-pane active">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding-right"
                                               for="roleconfig_roleinfo_role_name">角色名称：</label>
                                        <div class="col-sm-10">
                                            <div class="col-sm-6 no-padding-left">
													<span class="block input-icon input-icon-right"> <input
                                                            id="roleconfig_roleinfo_role_name"
                                                            name="roleconfig_roleinfo_role_name" type="text"
                                                            class="form-control width-100" required
                                                            placeholder="角色名称" maxlength="40"/></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding-right"
                                               for="roleconfig_roleinfo_role_level">角色级别：</label>
                                        <div class="col-sm-10">
                                            <div class="col-sm-6 no-padding-left">
                                                <input type="text" class="form-control width-100"
                                                       id="roleconfig_roleinfo_role_level"
                                                       name="roleconfig_roleinfo_role_level"/>
                                                <label class="align-bottom" for="roleconfig_roleinfo_role_level">（数字越小级别越高）</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding-right"
                                               for="roleconfig_roleinfo_role_sort">排序序号：</label>
                                        <div class="col-sm-10">
                                            <div class="col-sm-6 no-padding-left">
                                                <input type="text" class="form-control width-100"
                                                       id="roleconfig_roleinfo_role_sort"
                                                       name="roleconfig_roleinfo_role_sort"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="roleconfig_roleinfo_edit_users" class="tab-pane">
                                    <select multiple="multiple" size="10"
                                            name="roleconfig_roleinfo_user_list"
                                            id="roleconfig_roleinfo_user_list">
                                    </select>
                                </div>
                                <div id="roleconfig_roleinfo_edit_menu" class="tab-pane"
                                     style="height: 350px">
                                    <ul id="roleconfig_roleinfo_menutree" class="ztree width-100"
                                        style="overflow: auto; height: 100%"></ul>
                                </div>
                                <div id="roleconfig_roleinfo_edit_func" class="tab-pane"
                                     style="height: 350px">
                                    <ul id="roleconfig_roleinfo_functree" class="ztree width-100"
                                        style="overflow: auto; height: 100%"></ul>
                                </div>
                            </div>
                            <div class="form-actions center no-margin">
                                <button type="button" class="btn btn-white btn-info btn-bold"
                                        id="roleconfig_roleinfo_save_btn">
                                    <i class="ace-icon fa fa-floppy-o bigger-120 blue"></i> 保存
                                </button>
                            </div>
                        </form>
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