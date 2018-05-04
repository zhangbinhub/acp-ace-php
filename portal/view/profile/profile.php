<?php
require dirname(__FILE__) . '/../common/pageHead.php';
$user = portal\service\tools\ToolsClass::getUser();
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>个人资料</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<div class="page-content">
    <!-- 页头 start -->
    <div class="page-header">
        <h1>个人资料</h1>
        <input type="hidden" id="curruser_profile_loginno"
            <?php if ($user->getLoginno() != null) {
                echo 'value="' . $user->getLoginno() . '"';
            } ?> />
    </div>
    <!-- 页头 end -->
    <!-- 内容 start -->
    <div class="user-profile row">
        <form id="curruser_profile_form">
            <div class="col-xs-offset-1 col-xs-10">
                <ul class="nav nav-tabs padding-16">
                    <li class="active"><a data-toggle="tab"
                                          href="#curruser_profile_edit_basic" aria-expanded="true"> <i
                                    class="green ace-icon fa fa-pencil-square-o bigger-125"></i>
                            基本信息
                        </a></li>
                    <li class=""><a data-toggle="tab"
                                    href="#curruser_profile_edit_password" aria-expanded="false"> <i
                                    class="blue ace-icon fa fa-key bigger-125"></i> 密码
                        </a></li>
                </ul>
                <div class="tab-content profile-edit-tab-content">
                    <div id="curruser_profile_edit_basic" class="tab-pane active">
                        <div class="user-profile row">
                            <div class="space-10"></div>
                            <div class="col-xs-12 col-sm-4 center">
                                <div>
										<span class="profile-picture" style="width: 162px"> <img
                                                    id="curruser_profile_avatar"
                                                    class="editable img-responsive editable-click editable-empty"
                                                    alt="头像"
                                                    src="<?php
                                                    if ($user->getPortrait() != null) {
                                                        echo $user->getPortrait();
                                                    } else {
                                                        echo "/assets/avatars/avatar2.png";
                                                    }
                                                    ?>"
                                                    style="display: block;"/>
										</span>
                                    <div class="space-4"></div>
                                    <div
                                            class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                                        <div class="inline position-relative">头像</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-horizontal col-xs-12 col-sm-8">
                                <div class="space-4"></div>
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-3 control-label no-padding-right">登录账号：</label>
                                    <div class="col-sm-9 no-padding-left">
                                        <label class="col-sm-3 control-label no-padding-right align-left"><?php echo $user->getLoginno(); ?></label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <label class="col-sm-3 control-label no-padding-right"
                                           for="curruser_profile_username">姓名：</label>
                                    <div class="col-sm-9 no-padding-left">
                                        <div class="col-sm-6">
												<span class="block input-icon input-icon-right"> <input
                                                            type="text" id="curruser_profile_username"
                                                            name="curruser_profile_username" class="width-100" required
                                                            placeholder="姓名"
                                                        <?php if ($user->getName() != null) {
                                                            echo 'value="' . $user->getName() . '"';
                                                        } ?> />
												</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="curruser_profile_edit_password" class="tab-pane">
                        <div class="form-horizontal">
                            <div class="space-10"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right"
                                       for="curruser_profile_opassword">原密码：</label>
                                <div class="col-sm-9 no-padding-left">
                                    <div class="col-sm-6">
                                        <span class="block input-icon input-icon-right">
                                            <input autocomplete="off"
                                                   onfocus="this.type='password'"
                                                   type="text" id="curruser_profile_opassword"
                                                   name="curruser_profile_opassword" class="width-100"
                                                   placeholder="原密码"/>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-4"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right"
                                       for="curruser_profile_password1">新密码：</label>
                                <div class="col-sm-9 no-padding-left">
                                    <div class="col-sm-6">
                                        <span class="block input-icon input-icon-right">
                                            <input autocomplete="off"
                                                   onfocus="this.type='password'"
                                                   type="text" id="curruser_profile_password1"
                                                   name="curruser_profile_password1" class="width-100"
                                                   placeholder="新密码"/>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-4"></div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label no-padding-right"
                                       for="curruser_profile_password2">确认新密码：</label>
                                <div class="col-sm-9 no-padding-left">
                                    <div class="col-sm-6">
                                        <span class="block input-icon input-icon-right">
                                            <input autocomplete="off"
                                                   onfocus="this.type='password'"
                                                   type="text" id="curruser_profile_password2"
                                                   name="curruser_profile_password2" class="width-100"
                                                   placeholder="确认新密码"/>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix form-actions">
                    <div class="col-xs-12 align-center">
                        <button class="btn btn-info" type="submit">
                            <i class="ace-icon fa fa-check bigger-110"></i> 保存
                        </button>
                        &nbsp; &nbsp;
                        <button class="btn" type="reset">
                            <i class="ace-icon fa fa-undo bigger-110"></i> 重置
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- 内容 end -->
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>