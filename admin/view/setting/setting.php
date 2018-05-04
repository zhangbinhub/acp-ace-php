<?php
require dirname(__FILE__) . '/../common/commonHead.php';
$skin_colorpicker = "no-skin";
$settings_navbar = $settings_sidebar = $settings_breadcrumbs = $settings_add_container = $settings_hover = $settings_compact = $settings_highlight = $settings_use_tabs = 0;
$user = admin\service\tools\ToolsClass::getUser();
$app = service\tools\ToolsClass::getApplicationInfo(admin\config\AdminConfig::getInstance()['webroot']);
$result = array();
if ($user != null) {
    $userid = $user->getId();
    $appid = $app->getId();
    $commonTools = new service\tools\ToolsClass(0);
    $searchResult = $commonTools->getDatasBySQL("select * from T_User_Configuration where userid='$userid' and appid='$appid'");
    if (isset($_POST['cmd']) && $_POST['cmd'] === 'save') {
        $skin_colorpicker = $_POST['skin_colorpicker'];
        if (isset($_POST['settings_navbar'])) {
            $settings_navbar = $_POST['settings_navbar'] === 'true' ? 1 : 0;
        }
        if (isset($_POST['settings_sidebar'])) {
            $settings_sidebar = $_POST['settings_sidebar'] === 'true' ? 1 : 0;
        }
        if (isset($_POST['settings_breadcrumbs'])) {
            $settings_breadcrumbs = $_POST['settings_breadcrumbs'] === 'true' ? 1 : 0;
        }
        if (isset($_POST['settings_add_container'])) {
            $settings_add_container = $_POST['settings_add_container'] === 'true' ? 1 : 0;
        }
        if (isset($_POST['settings_hover'])) {
            $settings_hover = $_POST['settings_hover'] === 'true' ? 1 : 0;
        }
        if (isset($_POST['settings_compact'])) {
            $settings_compact = $_POST['settings_compact'] === 'true' ? 1 : 0;
        }
        if (isset($_POST['settings_highlight'])) {
            $settings_highlight = $_POST['settings_highlight'] === 'true' ? 1 : 0;
        }
        if (isset($_POST['settings_use_tabs'])) {
            $settings_use_tabs = $_POST['settings_use_tabs'] === 'true' ? 1 : 0;
        }
        $update_sql = "";
        if (count($searchResult) > 0) {
            $update_sql = "update T_User_Configuration set skin_colorpicker='$skin_colorpicker',
            settings_navbar=$settings_navbar,settings_sidebar=$settings_sidebar,settings_breadcrumbs=$settings_breadcrumbs,
            settings_add_container=$settings_add_container,settings_hover=$settings_hover,settings_compact=$settings_compact,
            settings_highlight=$settings_highlight,settings_use_tabs=$settings_use_tabs where id='" . $searchResult[0]['id'] . "'";
        } else {
            $update_sql = "insert into T_User_Configuration(id,userid,appid,skin_colorpicker,settings_navbar,
                settings_sidebar,settings_breadcrumbs,settings_add_container,settings_hover,settings_compact,settings_highlight,settings_use_tabs)
                values('" . service\tools\common\UUIDClass::getUUID() . "','$userid','$appid','$skin_colorpicker',$settings_navbar,$settings_sidebar,
            $settings_breadcrumbs,$settings_add_container,$settings_hover,$settings_compact,$settings_highlight,$settings_use_tabs)";
        }
        $result['result'] = $commonTools->doExecuteSQL($update_sql);
        echo json_encode($result);
        die();
    } else {
        if (count($searchResult) > 0) {
            $row = $searchResult[0];
            $skin_colorpicker = $row['skin_colorpicker'];
            $settings_navbar = (int)$row['settings_navbar'];
            $settings_sidebar = (int)$row['settings_sidebar'];
            $settings_breadcrumbs = (int)$row['settings_breadcrumbs'];
            $settings_add_container = (int)$row['settings_add_container'];
            $settings_hover = (int)$row['settings_hover'];
            $settings_compact = (int)$row['settings_compact'];
            $settings_highlight = (int)$row['settings_highlight'];
            $settings_use_tabs = (int)$row['settings_use_tabs'];
        }
    }
}
require dirname(__FILE__) . '/../common/viewHead.php';
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title>个性设置</title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="page-body page-content no-padding-top">
<form method="post" class="form-horizontal" id="settings_form"
      action="<?php echo htmlspecialchars($_SERVER['REDIRECT_URL'] . '?_dialogid=' . $_GET['_dialogid']) ?>">
    <div class="page-header">
        <div class="ace-settings-item">
            <div class="pull-left">
                <select id="settings_skin_colorpicker"
                        name="settings_skin_colorpicker" class="hide">
                    <option <?php if ($skin_colorpicker == 'no-skin') echo 'selected' ?>
                        data-skin="no-skin" value="#438EB9">#438EB9
                    </option>
                    <option <?php if ($skin_colorpicker == 'skin-1') echo 'selected' ?>
                        data-skin="skin-1" value="#222A2D">#222A2D
                    </option>
                    <option <?php if ($skin_colorpicker == 'skin-2') echo 'selected' ?>
                        data-skin="skin-2" value="#C6487E">#C6487E
                    </option>
                    <option <?php if ($skin_colorpicker == 'skin-3') echo 'selected' ?>
                        data-skin="skin-3" value="#D0D0D0">#D0D0D0
                    </option>
                </select>
            </div>
            <span>&nbsp; 选择皮肤</span>
        </div>
    </div>
    <div class="form-group no-margin-right">
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_navbar"> 固定导航栏 </label>
            <div class="col-xs-5">
                <label> <input name="settings_navbar" id="settings_navbar"
                        <?php if ($settings_navbar === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_sidebar"> 固定侧边栏高度 </label>
            <div class="col-xs-5">
                <label> <input name="settings_sidebar" id="settings_sidebar"
                        <?php if ($settings_sidebar === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group no-margin-right">
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_breadcrumbs"> 固定主页面顶部面板 </label>
            <div class="col-xs-5">
                <label> <input name="settings_breadcrumbs"
                               id="settings_breadcrumbs"
                        <?php if ($settings_breadcrumbs === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_hover"> 悬停显示子菜单 </label>
            <div class="col-xs-5">
                <label> <input name="settings_hover" id="settings_hover"
                        <?php if ($settings_hover === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group no-margin-right">
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_compact"> 紧凑型侧边栏 </label>
            <div class="col-xs-5">
                <label> <input name="settings_compact" id="settings_compact"
                        <?php if ($settings_compact === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_highlight"> 侧边栏高亮风格 </label>
            <div class="col-xs-5">
                <label> <input name="settings_highlight" id="settings_highlight"
                        <?php if ($settings_highlight === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group no-margin-right">
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_add_container"> 页面窄边框 </label>
            <div class="col-xs-5">
                <label> <input name="settings_add_container"
                               id="settings_add_container"
                        <?php if ($settings_add_container === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
        <div class="col-xsm-6">
            <label
                class="col-xs-7 control-label no-padding-right no-padding-top align-right"
                for="settings_use_tabs"> 启用选项卡样式 </label>
            <div class="col-xs-5">
                <label> <input name="settings_use_tabs" id="settings_use_tabs"
                        <?php if ($settings_use_tabs === 1) echo "checked" ?>
                               class="ace ace-switch ace-switch-6" type="checkbox"/> <span
                        class="lbl"></span>
                </label>
            </div>
        </div>
    </div>
    <div class="form-group no-margin-right no-margin-left">
        <hr class="hr-2"/>
    </div>
    <div class="form-group no-margin-right center">
        <button type="button" class="btn btn-white btn-info btn-bold"
                id="savesetting_btn">
            <i class="ace-icon fa fa-floppy-o bigger-120 blue"></i> 保存
        </button>
    </div>
</form>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php'; ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>