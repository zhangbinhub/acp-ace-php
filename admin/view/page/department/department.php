<?php
require dirname(__FILE__) . '/../../common/pageHead.php';
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>机构配置</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <!-- 内容 start -->
    <div class="row">
        <div class="col-sm-4">
            <div class="widget-box" style="height: 470px">
                <div class="widget-header">
                    <h4 class="smaller">机构</h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <ul id="departmentconfig_departmenttree" class="ztree width-100"
                            style="overflow: auto; height: 429px"></ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-8 hidden" id="departmentconfig_departmentinfo_div">
            <div class="widget-box">
                <div class="widget-header">
                    <h4 class="smaller" id="departmentconfig_departmentinfo_title">详细信息</h4>
                </div>
                <div class="widget-body">
                    <div
                            class="widget-main no-padding-left no-padding-right no-padding-bottom">
                        <form class="form-horizontal"
                              id="departmentconfig_departmentinfo_form">
                            <ul class="nav nav-tabs padding-16">
                                <li class="active"><a data-toggle="tab"
                                                      href="#departmentconfig_departmentinfo_edit_basic"
                                                      aria-expanded="true"> <i
                                                class="green ace-icon fa fa-pencil-square-o bigger-125"></i>
                                        机构信息
                                    </a></li>
                                <li><a data-toggle="tab"
                                       href="#departmentconfig_departmentinfo_edit_users"
                                       aria-expanded="false"> <i
                                                class="blue ace-icon fa fa-user-plus bigger-125"></i> 关联用户
                                    </a></li>
                            </ul>
                            <div class="tab-content">
                                <div id="departmentconfig_departmentinfo_edit_basic"
                                     class="tab-pane active">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding-right"
                                               for="departmentconfig_department_name">机构名称：</label>
                                        <div class="col-sm-10">
                                            <div class="col-sm-6 no-padding-left">
													<span class="block input-icon input-icon-right"> <input
                                                                id="departmentconfig_department_name"
                                                                name="departmentconfig_department_name" type="text"
                                                                class="input-xxlarge width-100" required
                                                                placeholder="机构名称" maxlength="40"/></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding-right"
                                               for="departmentconfig_department_code">机构编码：</label>
                                        <div class="col-sm-10">
                                            <div class="col-sm-6 no-padding-left">
													<span class="block input-icon input-icon-right"> <input
                                                                id="departmentconfig_department_code"
                                                                name="departmentconfig_department_code" type="text"
                                                                class="input-xxlarge width-100"
                                                                placeholder="机构编码" maxlength="40"/></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label no-padding-right"
                                               for="departmentconfig_department_sort">排序序号：</label>
                                        <div class="col-sm-10">
                                            <div class="col-sm-6 no-padding-left">
                                                <input type="text" class="form-control width-100"
                                                       id="departmentconfig_department_sort"
                                                       name="departmentconfig_department_sort"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="departmentconfig_departmentinfo_edit_users"
                                     class="tab-pane">
                                    <select multiple="multiple" size="10"
                                            name="departmentconfig_department_user_list"
                                            id="departmentconfig_department_user_list">
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions center no-margin">
                                <button type="button" class="btn btn-white btn-info btn-bold"
                                        id="departmentconfig_department_save_btn">
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