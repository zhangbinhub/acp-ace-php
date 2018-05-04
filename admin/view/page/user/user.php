<?php
require dirname(__FILE__) . '/../../common/pageHead.php';
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>用户配置</title>
    <?php
    require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php';
    $commonTools = new \service\tools\ToolsClass(0);
    $roles = $commonTools->getDatasBySQL("select r.id,r.name,a.appname from T_role r left join t_application a on a.id=r.appid
                        order by a.sort asc,r.levels asc,r.sort asc");
    ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content no-padding-top">
    <div class="row">
        <div class="col-xs-12">
            <div
                    class="form-horizontal col-xs-12 well no-margin-bottom no-padding-bottom">
                <form id="userconfig_conditionform">
                    <div class="form-group">
                        <label class="col-sm-1 control-label no-padding-right"
                               for="userconfig_query_username">姓名：</label>
                        <div class="col-sm-2">
                            <input type="text" id="userconfig_query_username"
                                   name="userconfig_query_username" class="form-control"
                                   maxlength="40"/>
                        </div>
                        <label class="col-sm-1 control-label no-padding-right"
                               for="userconfig_query_loginno">账号：</label>
                        <div class="col-sm-2">
                            <input type="text" id="userconfig_query_loginno"
                                   name="userconfig_query_loginno" class="form-control"
                                   maxlength="40"/>
                        </div>
                        <label class="col-sm-1 control-label no-padding-right"
                               for="userconfig_query_userlevel">级别：</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control"
                                   id="userconfig_query_userlevel"
                                   name="userconfig_query_userlevel"/>
                        </div>
                        <label class="col-sm-1 control-label no-padding-right"
                               for="userconfig_query_userstatus">状态：</label>
                        <div class="col-sm-2">
                            <select class="form-control"
                                    id="userconfig_query_userstatus"
                                    name="userconfig_query_userstatus">
                                <option value=""></option>
                                <option value="1">启用</option>
                                <option value="0">禁用</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-1 control-label no-padding-right"
                               for="userconfig_query_userdepartmentname">机构：</label>
                        <div class="col-sm-11">
                            <input type="text" class="form-control input-sm width-100"
                                   id="userconfig_query_userdepartmentname"
                                   name="userconfig_query_userdepartmentname"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-1 control-label no-padding-right"
                               for="userconfig_query_userrolenames">角色：</label>
                        <div class="col-sm-11">
                            <select class="tag-input-style width-100" style="height: 37px" multiple="multiple"
                                    id="userconfig_query_userrolenames"
                                    name="userconfig_query_userrolenames" data-placeholder=" ">
                                <?php
                                foreach ($roles as $role) {
                                    echo '<option value="' . $role['id'] . '">' . $role['appname'] . '----' . $role['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xs-12 well no-margin-bottom" style="padding: 5px;">
                <div class="div-grid-button-area-b">
                    <button type="button" class="btn btn-xs btn-primary"
                            id="userconfig_reset_btn">重置查询条件
                    </button>
                    <button type="button" class="btn btn-xs btn-primary"
                            id="userconfig_query_btn">
                        <i class="ace-icon fa fa-search bigger-120"></i> 查询
                    </button>
                </div>
                <div class="div-grid-button-area-p">
                    <button type="button" class="btn btn-xs btn-primary"
                            id="userconfig_add_btn">
                        <i class="ace-icon fa fa-plus bigger-120"></i> 新增
                    </button>
                    <button type="button" class="btn btn-xs btn-primary"
                            id="userconfig_del_btn">
                        <i class="ace-icon fa fa-trash-o bigger-120"></i> 删除
                    </button>
                </div>
            </div>
            <div class="col-xs-12 no-padding">
                <table id="userconfig_grid_table"></table>
                <div id="userconfig_grid_pager"></div>
            </div>
        </div>
    </div>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>