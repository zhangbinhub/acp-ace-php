<?php
require dirname(__FILE__) . '/../../common/pageHead.php';
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>用户信息配置</title>
    <?php
    require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php';
    $commonTools = new \service\tools\ToolsClass(0);
    $id = "";
    $userinfo = null;
    if (isset($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
        $userinfo = $commonTools->getDatasBySQL("select u.name,u.loginno,u.levels,u.sort,u.status from T_user u where u.id='" . $id . "'")[0];
    }
    $currUser = admin\service\tools\ToolsClass::getUser();
    $highRoleLevel = \service\user\UserManagerClass::getHighestRoleLevel($currUser->getId());
    if ($highRoleLevel == 0) {
        $highRoleLevel = $highRoleLevel - 1;
    }
    $roles = $commonTools->getDatasBySQL("select r.id,r.name,a.appname,case when r.id in (select ur.roleid from t_user_role_set ur where ur.userid='" . $id . "') then 'selected' else '' end as selected from T_role r inner join t_application a on a.id=r.appid where r.levels>" . $highRoleLevel . " order by a.sort asc,r.levels asc,r.sort asc");
    $userlevel = $currUser->getLevels();
    if ($userlevel < 0) {
        $userlevel = 0;
    }
    echo '<script type="text/javascript">var currLevel=' . $userlevel . ';</script>';
    ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<input class="hidden" id="userconfig_userinfo_id" value="<?php echo $id; ?>"/>
<div class="widget-main">
    <form class="form-horizontal" id="userconfig_userinfo_form">
        <ul class="nav nav-tabs padding-16">
            <li class="active"><a data-toggle="tab"
                                  id="userconfig_userinfo_info_a"
                                  href="#userconfig_userinfo_edit_basic" aria-expanded="true"> <i
                            class="green ace-icon fa fa-pencil-square-o bigger-125"></i> 用户信息
                </a></li>
            <li class=""><a data-toggle="tab"
                            href="#userconfig_userinfo_edit_departments" aria-expanded="false">
                    <i class="blue ace-icon fa fa-users bigger-125"></i> 机构
                </a></li>
        </ul>
        <div class="tab-content div-info-tab-content">
            <div id="userconfig_userinfo_edit_basic" class="tab-pane active">
                <div class="form-group">
                    <div class="form-group col-xs-6 no-padding no-margin">
                        <label class="col-sm-2 control-label no-padding-right"
                               for="userconfig_userinfo_username">姓名：</label>
                        <div class="col-sm-10">
                            <div class="col-sm-6 no-padding-left">
									<span class="block input-icon input-icon-right"> <input
                                                type="text" id="userconfig_userinfo_username"
                                                name="userconfig_userinfo_username" class="form-control"
                                                required placeholder="姓名" maxlength="40"
                                                value="<?php if ($userinfo !== null) echo $userinfo['name']; ?>"/>
									</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-xs-6 no-padding no-margin">
                        <label class="col-sm-2 control-label no-padding-right"
                               for="userconfig_userinfo_loginno">账号：</label>
                        <div class="col-sm-10">
                            <div class="col-sm-6 no-padding-left">
									<span class="block input-icon input-icon-right"> <input
                                                type="text" id="userconfig_userinfo_loginno"
                                                name="userconfig_userinfo_loginno" class="form-control"
                                                required placeholder="账号" maxlength="40"
                                                value="<?php if ($userinfo !== null) echo $userinfo['loginno']; ?>"/>
									</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label no-padding-right"
                           for="userconfig_userinfo_userlevel">级别：</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control"
                               id="userconfig_userinfo_userlevel"
                               name="userconfig_userinfo_userlevel"
                               value="<?php if ($userinfo !== null) echo $userinfo['levels']; else echo $currUser->getLevels() < 0 ? 1 : $currUser->getLevels() + 1; ?>"/>
                        <label class="align-bottom" for="userconfig_userinfo_userlevel">（数字越小级别越高）</label>
                    </div>
                    <label class="col-sm-1 control-label no-padding-right"
                           for="userconfig_userinfo_userstatus">状态：</label>
                    <div class="col-sm-5">
                        <div class="col-sm-6 no-padding-left">
                            <select class="form-control" id="userconfig_userinfo_userstatus"
                                    name="userconfig_userinfo_userstatus">
                                <option <?php if ($userinfo !== null && $userinfo['status'] == '1') echo 'selected'; ?>
                                        value="1"> 启用
                                </option>
                                <option <?php if ($userinfo !== null && $userinfo['status'] == '0') echo 'selected'; ?>
                                        value="0"> 禁用
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label no-padding-right"
                           for="userconfig_userinfo_usersort">序号：</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control"
                               id="userconfig_userinfo_usersort"
                               name="userconfig_userinfo_usersort"
                               value="<?php if ($userinfo !== null) echo $userinfo['sort']; else echo 0; ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-1 control-label no-padding-right"
                           for="userconfig_userinfo_userrolenames">角色：</label>
                    <div class="col-sm-11">
                        <select multiple="multiple"
                                id="userconfig_userinfo_userrolenames"
                                name="userconfig_userinfo_userrolenames" data-placeholder=" "
                                class="tag-input-style width-100" style="height: 37px">
                            <?php
                            foreach ($roles as $role) {
                                echo '<option ' . $role['selected'] . ' value="' . $role['id'] . '">' . $role['appname'] . '----' . $role['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div id="userconfig_userinfo_edit_departments" class="tab-pane"
                 style="height: 328px">
                <ul id="userconfig_userinfo_userdepartmenttree"
                    class="ztree width-100" style="overflow: auto; height: 100%"></ul>
            </div>
        </div>
    </form>
    <div class="form-actions center no-margin">
        <?php
        if ($id !== "") {
            echo '<button type="button" class="btn btn-white btn-info btn-bold btn-round" id="userconfig_userinfo_rpw_btn"><i class="ace-icon fa fa-key bigger-120 blue"></i> 重置密码</button> &nbsp; &nbsp;';
        }
        ?>
        <button type="reset"
                class="btn btn-white btn-warning btn-bold btn-round"
                id="userconfig_userinfo_reset_btn">
            <i class="ace-icon fa fa-undo bigger-120 orange2"></i> 撤销
        </button>
        &nbsp; &nbsp;
        <button type="button"
                class="btn btn-white btn-success btn-bold btn-round"
                id="userconfig_userinfo_save_btn">
            <i class="ace-icon fa fa-floppy-o bigger-120 green"></i> 保存
        </button>
        &nbsp; &nbsp;
        <button type="button"
                class="btn btn-white btn-danger btn-bold btn-round"
                id="userconfig_userinfo_cancle_btn">
            <i class="ace-icon fa fa-times bigger-120 red2"></i> 取消
        </button>
    </div>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>