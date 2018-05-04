<?php
namespace admin\service\tools;

use service\user\UserManagerClass;

class ToolsClass
{

    public static $LOGIN_YZM_STR = "admin_login_yzm";

    public static $LOGIN_USER_STR = "admin_loginUser";

    private $commonTools = null;

    public function __construct($resourceIndex = -1, $isPersistent = false)
    {
        if ($resourceIndex === -1) {
            if (isset($GLOBALS['app_dbno'])) {
                $resourceIndex = $GLOBALS['app_dbno'];
            } else {
                $resourceIndex = 0;
            }
        }
        $this->commonTools = new \service\tools\ToolsClass($resourceIndex, $isPersistent);
    }

    private function buildOpentype($url, $value)
    {
        $bin = strpos($url, '?');
        if ($bin !== false) {
            $url = $url . '&_opentype=' . $value;
        } else {
            $url = $url . '?_opentype=' . $value;
        }
        return $url;
    }

    /**
     * 生成子菜单
     *
     * @param array $submenus
     * @param int $settings_hover
     * @param int $settings_highlight
     * @return string
     */
    private function generateSubMenus($submenus, $settings_hover = 0, $settings_highlight = 0)
    {
        $result = "";
        $li_class = "";
        if ($settings_hover == 1) {
            $li_class = $li_class . "hover";
        }
        if ($settings_highlight == 1) {
            if ($li_class == "") {
                $li_class = $li_class . "highlight";
            } else {
                $li_class = $li_class . " highlight";
            }
        }
        if (count($submenus) > 0) {
            $result = '<ul class="submenu">';
            foreach ($submenus as $submenu) {
                $id = $submenu['id'];
                $submenus_tmp = $this->commonTools->getDatasBySQL("select distinct m.* from t_menu m where m.parentid='$id' and m.appid='" . $submenu['appid'] . "' and m.status=1 and m.id in
                    (select tm.id from t_menu tm INNER JOIN t_role_menu_set rm on tm.id=rm.menuid INNER JOIN t_user_role_set ur on rm.roleid=ur.roleid INNER JOIN t_role r on r.id=ur.roleid
                    where r.appid='" . $GLOBALS['application']->getId() . "' and ur.userid='" . self::getUser()->getId() . "') order by m.sort asc");
                $dropdown_class = "";
                $dropdown_trag = "";
                $opentype = intval($submenu['opentype']);
                $target = "";
                if (count($submenus_tmp) > 0) {
                    $dropdown_class = " dropdown-toggle";
                    $dropdown_trag = '<b class="arrow fa fa-angle-down"></b>';
                }
                if (empty($submenu['page_url']) || count($submenus_tmp) > 0) {
                    $url = "javascript:void(0);";
                } else {
                    if (intval($submenu['model']) == 0) {
                        $url = $GLOBALS['webroot'] . $submenu['page_url'];
                    } else {
                        $url = $submenu['page_url'];
                    }
                    switch ($opentype) {
                        case 0:
                            $url = "javascript:admin_tools_obj.loadPageInDiv({menuid:'" . $id . "',title:'" . $submenu['name'] . "',path:'" . $this->buildOpentype($url, 0) . "'});";
                            break;
                        case 1:
                            $url = "javascript:admin_tools_obj.loadPageInDiv({menuid:'" . $id . "',title:'" . $submenu['name'] . "',path:'" . $this->buildOpentype($url, 1) . "'});";
                            break;
                        case 2:
                            $url = "javascript:AUI.dialog.inDialog(" . $submenu['dialog_w'] . ", " . $submenu['dialog_h'] . ", '" . $submenu['name'] . "', {innerUrl:'" . $url . "'});";
                            break;
                        case 3:
                            $url = "javascript:AUI.dialog.inDialog(" . $submenu['dialog_w'] . ", " . $submenu['dialog_h'] . ", '" . $submenu['name'] . "', '" . $url . "');";
                            break;
                        case 4:
                            $target = "_self";
                            break;
                        case 5:
                            $target = "_blank";
                            break;
                    }
                }
                if (!empty($submenu['icon_class'])) {
                    $result = $result . '<li class="' . $li_class . '"><a href="' . $url . '" class="main_menu' . $dropdown_class . '" target="' . $target . '" id="' . $id . '"><i class="menu-icon fa ' . $submenu['icon_class'] . '" style="color:' . $submenu['icon_color'] . '"></i><span class="menu-text">	' . $submenu['name'] . ' </span>' . $dropdown_trag . '</a><b class="arrow"></b>';
                } else {
                    $result = $result . '<li class="' . $li_class . '"><a href="' . $url . '" class="main_menu' . $dropdown_class . '" target="' . $target . '" id="' . $id . '"><i class="menu-icon fa fa-caret-right"></i><span class="menu-text">	' . $submenu['name'] . ' </span>' . $dropdown_trag . '</a><b class="arrow"></b>';
                }
                $result = $result . $this->generateSubMenus($submenus_tmp, $settings_hover, $settings_highlight);
                $result = $result . '</li>';
            }
            $result = $result . '</ul>';
        }
        return $result;
    }

    /**
     * 生成菜单
     *
     * @param array $topmenus
     * @param int $settings_hover
     * @param int $settings_highlight
     * @return string
     */
    public function generateMenu($topmenus, $settings_hover = 0, $settings_highlight = 0)
    {
        $li_class = "";
        if ($settings_hover == 1) {
            $li_class = $li_class . "hover";
        }
        if ($settings_highlight == 1) {
            if ($li_class == "") {
                $li_class = $li_class . "highlight";
            } else {
                $li_class = $li_class . " highlight";
            }
        }
        $result = '<ul class="nav nav-list" id="main_nav_list">';
        foreach ($topmenus as $topmenu) {
            $id = $topmenu['id'];
            $submenus_tmp = $this->commonTools->getDatasBySQL("select distinct m.* from t_menu m where m.parentid='$id' and m.appid='" . $topmenu['appid'] . "' and m.status=1 and m.id in
                (select tm.id from t_menu tm INNER JOIN t_role_menu_set rm on tm.id=rm.menuid INNER JOIN t_user_role_set ur on rm.roleid=ur.roleid INNER JOIN t_role r on r.id=ur.roleid
                where r.appid='" . $GLOBALS['application']->getId() . "' and ur.userid='" . self::getUser()->getId() . "') order by m.sort asc");
            $dropdown_class = "";
            $dropdown_trag = "";
            $opentype = intval($topmenu['opentype']);
            $target = "";
            if (count($submenus_tmp) > 0) {
                $dropdown_class = " dropdown-toggle";
                $dropdown_trag = '<b class="arrow fa fa-angle-down"></b>';
            }
            if (empty($topmenu['page_url']) || count($submenus_tmp) > 0) {
                $url = "javascript:void(0);";
            } else {
                if (intval($topmenu['model']) == 0) {
                    $url = $GLOBALS['webroot'] . $topmenu['page_url'];
                } else {
                    $url = $topmenu['page_url'];
                }
                switch ($opentype) {
                    case 0:
                        $url = "javascript:admin_tools_obj.loadPageInDiv({menuid:'" . $id . "',title:'" . $topmenu['name'] . "',path:'" . $this->buildOpentype($url, 0) . "'});";
                        break;
                    case 1:
                        $url = "javascript:admin_tools_obj.loadPageInDiv({menuid:'" . $id . "',title:'" . $topmenu['name'] . "',path:'" . $this->buildOpentype($url, 1) . "'});";
                        break;
                    case 2:
                        $url = "javascript:AUI.dialog.inDialog(" . $topmenu['dialog_w'] . ", " . $topmenu['dialog_h'] . ", '" . $topmenu['name'] . "', {innerUrl:'" . $url . "'});";
                        break;
                    case 3:
                        $url = "javascript:AUI.dialog.inDialog(" . $topmenu['dialog_w'] . ", " . $topmenu['dialog_h'] . ", '" . $topmenu['name'] . "', '" . $url . "');";
                        break;
                    case 4:
                        $target = "_self";
                        break;
                    case 5:
                        $target = "_blank";
                        break;
                }
            }
            if (!empty($topmenu['icon_class'])) {
                $result = $result . '<li class="' . $li_class . '"><a href="' . $url . '" class="main_menu' . $dropdown_class . '" target="' . $target . '" id="' . $id . '"><i class="menu-icon fa ' . $topmenu['icon_class'] . '" style="color:' . $topmenu['icon_color'] . '"></i><span class="menu-text">	' . $topmenu['name'] . ' </span>' . $dropdown_trag . '</a><b class="arrow"></b>';
            } else {
                $result = $result . '<li class="' . $li_class . '"><a href="' . $url . '" class="main_menu' . $dropdown_class . '" target="' . $target . '" id="' . $id . '"><span class="menu-text">	' . $topmenu['name'] . ' </span>' . $dropdown_trag . '</a><b class="arrow"></b>';
            }
            $result = $result . $this->generateSubMenus($submenus_tmp, $settings_hover, $settings_highlight);
            $result = $result . '</li>';
        }
        $result = $result . '</ul>';
        return $result;
    }

    /**
     * 获取当前登录用户对象
     *
     * @return \service\user\UserClass|NULL
     */
    public static function getUser()
    {
        return UserManagerClass::getUser(self::$LOGIN_USER_STR);
    }

    /**
     * 设置当前登录用户对象到session中
     *
     * @param \service\user\UserClass $user
     */
    public static function setUser($user)
    {
        UserManagerClass::setUser(self::$LOGIN_USER_STR, $user);
    }
}

?>