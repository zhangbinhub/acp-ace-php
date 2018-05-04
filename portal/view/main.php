<?php
require dirname(__FILE__) . '/common/pageHead.php';
$topmenus = array(); // 顶层菜单
$tools = new portal\service\tools\ToolsClass(0);
$commonTools = new service\tools\ToolsClass(0);
$user = portal\service\tools\ToolsClass::getUser();
$departments = '';
if ($user != null) {
    $userid = $user->getId();
    $topmenus = $commonTools->getDatasBySQL("select distinct m.* from t_menu m left JOIN t_role_menu_set rm on m.id=rm.menuid LEFT JOIN t_user_role_set ur on rm.roleid=ur.roleid INNER JOIN t_role r on r.id=ur.roleid
        where r.appid='" . $GLOBALS['application']->getId() . "' and ur.userid='$userid' and m.parentid='" . $GLOBALS['application']->getId() . "' and m.appid='" . $GLOBALS['application']->getId() . "' and m.status=1 order by m.sort asc");
    $departments = \service\user\UserManagerClass::getDepartments($user->getId(), 1);
}
?>
<html <?php echo $GLOBALS['html_attr'] ?>>
<head>
    <title><?php if ($GLOBALS['application']->getAppname() != null) echo $GLOBALS['application']->getAppname(); ?></title>
    <?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php'; ?>
</head>
<body class="<?php echo $GLOBALS['body_class']; ?>">
<!-- 导航 start-->
<div class="navbar navbar-default<?php if ($GLOBALS['settings_navbar'] == 1) {
    echo " navbar-fixed-top";
} ?>">
    <div class="navbar-container">
        <!-- 小分辨率菜单按钮 start-->
        <button type="button" class="navbar-toggle menu-toggler pull-left" data-target="#main_page_sidebar">
            <span class="sr-only">侧边栏</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <!-- 小分辨率菜单按钮 end-->
        <!-- 系统名称 start-->
        <div class="navbar-header pull-left">
            <a href="javascript:void(0)" class="navbar-brand">
                <small>
                    <i class="fa fa-leaf"></i>
                    <?php if ($GLOBALS['application']->getAppname() != null) echo $GLOBALS['application']->getAppname(); ?>
                </small>
            </a>
        </div>
        <!-- 系统名称 end-->
        <!-- 导航按钮 sart-->
        <div class="navbar-buttons navbar-header pull-right" role="navigation">
            <ul class="nav ace-nav">
                <li style="margin-right: 20px;">
                    <span id="main_now_time" class="white"></span>
                </li>
                <li class="light-blue">
                    <a data-toggle="dropdown" href="javascript:void(0)" class="dropdown-toggle">
                        <img class="nav-user-photo" style="max-height: 40px;" src="
                        <?php
                        if ($user->getPortrait() != null) {
                            echo $user->getPortrait();
                        } else {
                            echo "/assets/avatars/avatar2.png";
                        }
                        ?>" alt="头像"/>
                        <span class="user-info" title="<?php if ($departments !== '') {
                            echo $departments;
                            echo '（' . $user->getName() . '）';
                        } else {
                            echo $user->getName();
                        } ?>">
                            <?php if ($departments !== '') {
                                echo $departments . '<br/>';
                            } ?><?php echo $user->getName(); ?>
						</span>
                        <i class="ace-icon fa fa-caret-down"></i>
                    </a>
                    <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                        <li>
                            <a href="javascript:void(0)" id="main_setting_a">
                                <i class="ace-icon fa fa-cog"></i> 个性设置
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0)" id="main_user_info_a">
                                <i class="ace-icon fa fa-user"></i> 个人资料
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:void(0)" id="main_loginout_a">
                                <i class="ace-icon fa fa-power-off"></i> 退出
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- 导航按钮 end-->
    </div>
</div>
<!-- 导航 end-->
<!-- 页面区域 start -->
<div class="main-container<?php if ($GLOBALS['settings_add_container'] == 1) {
    echo " container";
} ?>">
    <!-- 侧边栏 start -->
    <div id="main_page_sidebar" class="sidebar responsive<?php if ($GLOBALS['settings_sidebar'] == 1) {
        echo " sidebar-fixed";
    }
    if ($GLOBALS['settings_compact'] == 1) {
        echo " compact";
    } ?>">
        <!-- 菜单栏 start-->
        <?php echo $tools->generateMenu($topmenus, $GLOBALS['settings_hover'], $GLOBALS['settings_highlight']); ?>
        <!-- 菜单栏 end-->
        <!-- 侧边栏收缩按钮 start -->
        <div class="sidebar-toggle sidebar-collapse">
            <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left"
               data-icon2="ace-icon fa fa-angle-double-right"></i>
        </div>
        <!-- 侧边栏收缩按钮 end -->
    </div>
    <!-- 侧边栏 end -->
    <!-- 主页面 start -->
    <div class="main-content">
        <!-- 主页面内部区域 start -->
        <div class="main-content-inner">
            <?php if ($GLOBALS['settings_use_tabs'] !== 1) { ?>
                <!-- 顶部面板 start -->
                <div class="breadcrumbs<?php if ($GLOBALS['settings_breadcrumbs'] == 1) {
                    echo " breadcrumbs-fixed";
                } ?>">
                    <!-- 页面路径 start -->
                    <ul class="breadcrumb" id="main_breadcrumb">
                        <li class="active">
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="javascript:portal_tools_obj.loadPageInDiv();" class="main_menu">首页</a>
                        </li>
                    </ul>
                    <!-- 页面路径 end -->
                    <!-- 顶部面板 end -->
                </div>
                <!-- 顶部面板 end -->
            <?php } else { ?>
                <!-- 页面内容 start -->
                <div class="breadcrumbs<?php if ($GLOBALS['settings_breadcrumbs'] == 1) {
                    echo " breadcrumbs-fixed";
                } ?>">
                    <!-- 页面路径 start -->
                    <ul id="main_breadcrumb"
                        class="nav nav-tabs padding-12 tab-color-blue background-blue main-tabbable-ul">
                    </ul>
                    <!-- 页面路径 end -->
                    <!-- 顶部面板 end -->
                </div>
            <?php } ?>
            <!-- 页面内容 start -->
            <div id="main_page_content_div" class="page-content main-page-container tab-content main-tabbable-content">
            </div>
            <!-- 页面内容 end -->
        </div>
    </div>
    <!-- 主页面 end -->
    <!-- 页脚 start -->
    <div class="footer">
        <div class="footer-inner">
            <div class="footer-content">
                <span class="bigger-120">
<?php
$foot_str = '';
if ($GLOBALS['application']->getAppname() != null) {
    $foot_str = $foot_str . '<span class="blue bolder">' . $GLOBALS['application']->getAppname();
    if ($GLOBALS['application']->getVersion() != null) {
        $foot_str = $foot_str . '<small>(v' . $GLOBALS['application']->getVersion() . ')</small>' . '</span>';
    } else {
        $foot_str = $foot_str . '</span>';
    }
}
if ($GLOBALS['application']->getCopyrightBegin() != null) {
    $foot_str = $foot_str . ' &copy; ' . $GLOBALS['application']->getCopyrightBegin();
    if ($GLOBALS['application']->getCopyrightEnd() != null) {
        $foot_str = $foot_str . '-' . $GLOBALS['application']->getCopyrightEnd();
    } else {
        $foot_str = $foot_str . '-' . date('Y');
    }
    if ($GLOBALS['application']->getCopyrightOwner() != null) {
        $foot_str = $foot_str . ' ' . $GLOBALS['application']->getCopyrightOwner() . ' 版权所有';
    }
}
echo $foot_str;
?>
                </span>
            </div>
        </div>
    </div>
    <!-- 页脚 end -->
    <!-- 返回顶部按钮 start -->
    <a href="javascript:void(0)" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
    </a>
    <!-- 返回顶部按钮 end -->
</div>
<!-- 页面区域 end -->
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php' ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
<script type="text/javascript">
    var c = 0;
    var Y =<?php echo date('Y')?>, M =<?php echo date('n')?>, D =<?php echo date('j')?>;

    function stime() {
        c++;
        var sec = <?php echo time() - strtotime(date("Y-m-d"))?>+c;
        var H = Math.floor(sec / 3600) % 24;
        var I = Math.floor(sec / 60) % 60;
        var S = sec % 60;
        if (S < 10) S = '0' + S;
        if (I < 10) I = '0' + I;
        if (H < 10) H = '0' + H;
        if (H === '00' && I === '00' && S === '00') D = D + 1; //日进位
        if (M === 2) { //判断是否为二月份******
            if (Y % 4 === 0 && !Y % 100 === 0 || Y % 400 === 0) { //是闰年(二月有28天)
                if (D === 30) {
                    M += 1;
                    D = 1;
                } //月份进位
            }
            else { //非闰年(二月有29天)
                if (D === 29) {
                    M += 1;
                    D = 1;
                } //月份进位
            }
        }
        else { //不是二月份的月份******
            if (M === 4 || M === 6 || M === 9 || M === 11) { //小月(30天)
                if (D === 31) {
                    M += 1;
                    D = 1;
                } //月份进位
            }
            else { //大月(31天)
                if (D === 32) {
                    M += 1;
                    D = 1;
                } //月份进位
            }
        }
        if (M === 13) {
            Y += 1;
            M = 1;
        } //年份进位
        var M_t = M;
        var D_t = D;
        if (M < 10) {
            M_t = "0" + M;
        }
        if (D < 10) {
            D_t = "0" + D;
        }
        $("#main_now_time").text(Y + '-' + M_t + '-' + D_t + ' ' + H + ':' + I + ':' + S);
    }

    $(function () {
        stime();
        setInterval(stime, 1000);
    });
</script>
</body>
</html>