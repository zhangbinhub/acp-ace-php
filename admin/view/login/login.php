<?php
require dirname(__FILE__) . '/../common/commonHead.php';
$username = $password = $yzm = $RememberInfo = "";
$errmsg = "";
if (isset($_POST['cmd']) && $_POST['cmd'] === 'login') {
    // 登录
    if (empty($_POST['username']) || trim($_POST['username']) == "") {
        $errmsg = "用户名不能为空！";
    } else
        if (empty($_POST['pen']) || trim($_POST['pen']) == "") {
            $errmsg = "密码不能为空！";
        } else
            if (empty($_POST['yzm']) || trim($_POST['yzm']) == "") {
                $errmsg = "验证码不能为空！";
            } else {
                $username = trim($_POST['username']);
                $password = trim($_POST['pen']);
                $yzm = trim($_POST['yzm']);
                $login = new admin\service\login\LoginClass();
                $login_result = $login->doLogin($username, $password, $yzm);
                if ($login_result[0] != 0) {
                    $errmsg = $login_result[1];
                } else {
                    if (isset($_POST['RememberInfo']) && $_POST['RememberInfo'] == 'on') {
                        setcookie('username', $username, time() + 604800);
                        setcookie('RememberInfo', $_POST['RememberInfo'], time() + 604800);
                    } else {
                        setcookie('username', '', time() - 604800);
                        setcookie('RememberInfo', '', time() - 604800);
                    }
                    header('Location: ' . $GLOBALS['mainpage_url']);
                    die();
                }
            }
} else
    if (isset($_GET['cmd']) && $_GET['cmd'] === 'loginout') {
        $user = admin\service\tools\ToolsClass::getUser();
        if ($user != null) {
            $login = new admin\service\login\LoginClass();
            $login->doLogout($user->getId());
        }
        header('Location: ' . htmlspecialchars($_SERVER['REDIRECT_URL']));
        die();
    } else {
        if (isset($_COOKIE['username']) && $_COOKIE['username'] != null) {
            $username = $_COOKIE['username'];
        }
        if (isset($_COOKIE['RememberInfo']) && $_COOKIE['RememberInfo'] != null) {
            $RememberInfo = $_COOKIE['RememberInfo'];
        }
    }
require dirname(__FILE__) . '/../common/viewHead.php';
?>
<html <?php echo $GLOBALS['html_attr']; ?>>
<head>
    <title><?php echo $GLOBALS['application']->getAppname() . '-用户登录'; ?></title>
    <?php
    echo '<meta name="description" content="用户登录,' . $GLOBALS['application']->getAppname() . '" />';
    require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/head.php';
    ?>
</head>
<body class="page-body login-layout blur-login">
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <i class="ace-icon fa fa-lock red"></i>
                            <span class="white"><?php echo $GLOBALS['application']->getAppname(); ?></span>
                        </h1>
                        <h4 class="light-blue">
                            &copy; <?php echo $GLOBALS['application']->getCopyrightOwner() . ' <small>(v' . $GLOBALS['application']->getVersion() . ')</small>'; ?></h4>
                    </div>
                    <div class="space-6"></div>
                    <div class="position-relative">
                        <div class="login-box widget-box no-border visible">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <i class="ace-icon fa fa-bookmark green"></i> 请输入登录信息
                                    </h4>
                                    <div class="space-6"></div>
                                    <form id="loginform" method="post"
                                          action="<?php echo htmlspecialchars($_SERVER['REDIRECT_URL']) ?>">
                                        <fieldset form="loginform">
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input id="username" name="username" type="text"
                                                           value="<?php echo $username; ?>" class="form-control"
                                                           placeholder="输入用户名"/>
                                                    <i class="ace-icon fa fa-user"></i>
												</span>
                                            </label>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input id="password" name="password" type="password"
                                                           class="form-control" placeholder="输入密码"/>
                                                    <i class="ace-icon fa fa-lock"></i>
												</span>
                                            </label>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input id="yzm" name="yzm" type="text" class="form-control"
                                                           placeholder="输入验证码"/>
                                                    <i class="ace-icon fa fa-qrcode"></i>
												</span>
                                            </label>
                                            <label class="block clearfix">
                                                <img id="yzm_img" class="yzm-img"
                                                     src="<?php echo $GLOBALS['webroot'] . '/view/common/yzm?' . time() ?>"/>
                                            </label>
                                            <div class="space"></div>
                                            <?php if ($errmsg != "") { ?>
                                                <label class="block clearfix">
                                                    <span class="block red">
                                                        <i class="ace-icon fa fa-times-circle"></i> <?php echo $errmsg; ?>
                                                    </span>
                                                </label>
                                                <div class="space"></div>
                                            <?php } ?>
                                            <div class="clearfix">
                                                <label class="inline">
                                                    <input type="checkbox" id="RememberInfo" name="RememberInfo"
                                                        <?php if ($RememberInfo == 'on') echo 'checked'; ?>
                                                           class="ace acp-checkbox-1"/>
                                                    <span class="lbl"> 记住我的信息</span>
                                                </label>
                                                <button type="button" id="login_btn"
                                                        class="width-35 pull-right btn btn-sm btn-primary">
                                                    <i class="ace-icon fa fa-key"></i>
                                                    <span class="bigger-110">登录</span>
                                                </button>
                                            </div>
                                            <div class="space-4"></div>
                                        </fieldset>
                                    </form>
                                </div>
                                <div class="toolbar clearfix">
                                    <div>
                                        <a href="#" id="forgot_p" data-target="#forgot-box"
                                           class="forgot-password-link">
                                            <i class="ace-icon fa fa-arrow-left"></i> 忘记密码
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="forgot-box" class="forgot-box widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header red lighter bigger">
                                        <i class="ace-icon fa fa-key"></i> 重置密码
                                    </h4>
                                    <div class="space-6"></div>
                                    <p>输入您注册时的邮箱地址</p>
                                    <form>
                                        <fieldset>
                                            <label class="block clearfix">
                                                <span class="block input-icon input-icon-right">
                                                    <input type="email" class="form-control" placeholder="Email"/>
                                                    <i class="ace-icon fa fa-envelope"></i>
												</span>
                                            </label>
                                            <div class="clearfix">
                                                <button type="button" id="getPassword_btn"
                                                        class="width-35 pull-right btn btn-sm btn-danger">
                                                    <i class="ace-icon fa fa-lightbulb-o"></i>
                                                    <span class="bigger-110">发送</span>
                                                </button>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                                <div class="toolbar center">
                                    <a href="#" data-target="#login-box" class="back-to-login-link"> 返回登录
                                        <i class="ace-icon fa fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['webroot'] . '/view/common/foot.php' ?>
<script type="text/javascript" defer
        charset="<?php echo $GLOBALS['charset']; ?>"
        src="<?php echo $_SERVER['REDIRECT_URL'] . '.js?v=1.0.0'; ?>"></script>
</body>
</html>