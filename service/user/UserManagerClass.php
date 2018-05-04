<?php

namespace service\user;

use config\SystemConfig;
use service\tools\ToolsClass;
use service\tools\common\IPClass;
use service\tools\common\UUIDClass;
use service\tools\logger\LoggerClass;

class UserManagerClass
{

    /**
     * 重置登录密码
     * @param $userid
     * @param $loginno
     * @param $newpassword
     * @return bool
     */
    public static function resetPassword($userid, $loginno, $newpassword = '000000')
    {
        $commonTools = new ToolsClass(0);
        $sqlupdate = "update T_user set password='" . strtolower(md5(strtolower(md5($newpassword)) . $loginno)) . "' where id='" . $userid . "'";
        return $commonTools->doExecuteSQL($sqlupdate);
    }

    /**
     * 获取在线用户
     *
     * @param string $appid =''
     * @param string $userid =''
     * @param string $ip =''
     * @return array
     */
    public static function getOnlineUsers($appid = '', $userid = '', $ip = '')
    {
        $commonTools = new ToolsClass(0);
        $timeout = intval(SystemConfig::getInstance()['session']['timeout']);
        $limit_time = date('Y-m-d H:i:s', time() - $timeout);
        $search_sql = "select * from T_Online_User where last_active_time>'" . $limit_time . "' ";
        if ($appid !== '') {
            $search_sql = $search_sql . "and appid='" . $appid . "'";
        }
        if ($userid !== '') {
            $search_sql = $search_sql . "and userid='" . $userid . "'";
        }
        if ($ip !== '') {
            $search_sql = $search_sql . "and last_login_ip='" . $ip . "'";
        }
        return $commonTools->getDatasBySQL($search_sql);
    }

    /**
     * 更新在线用户信息
     *
     * @param string $appid
     * @param string $userid
     * @param string $ip
     * @param bool $isSingle =true
     */
    public static function updateOnlineUser($appid, $userid, $ip, $isSingle = true)
    {
        $commonTools = new ToolsClass(0);
        $search_sql = "select * from T_Online_User where appid='" . $appid . "' and userid='" . $userid . "' and last_login_ip='" . $ip . "'";
        $search = $commonTools->getDatasBySQL($search_sql);
        if (count($search) > 0) {
            $update_sql = "update T_Online_User set last_active_time='" . ToolsClass::getNowTime() . "' where appid='" . $appid . "'and userid='" . $userid . "' and last_login_ip='" . $ip . "'";
        } else {
            $update_sql = "insert into T_Online_User(id,userid,appid,last_active_time,last_login_ip) values('" . UUIDClass::getUUID() . "','" . $userid . "','" . $appid . "','" . ToolsClass::getNowTime() . "','" . $ip . "')";
        }
        $commonTools->doExecuteSQL($update_sql);
        if ($isSingle) {
            $commonTools->doExecuteSQL("delete from T_Online_User where appid='" . $appid . "' and userid='" . $userid . "' and last_login_ip<>'" . $ip . "'");
        }
    }

    /**
     * 销毁在线用户信息
     *
     * @param string $appid
     * @param string $userid
     * @param string $ip
     */
    public static function destroyOnlineUser($appid, $userid, $ip)
    {
        $commonTools = new ToolsClass(0);
        $commonTools->doExecuteSQL("delete from T_Online_User where appid='" . $appid . "' and userid='" . $userid . "' and last_login_ip='" . $ip . "'");
    }

    /**
     * 校验用户名和密码
     *
     * @param string $loginno 登录用户名
     * @param string $password 密码
     * @return string 用户id，校验失败则返回“”
     */
    public static function validateUserLoginNoAndPwd($loginno, $password)
    {
        $result = '';
        $commonTools = new ToolsClass(0);
        $searchResult = $commonTools->getDatasBySQL("select u.id,u.name,u.loginno,u.levels,ui.portrait,u.password from T_User u left join T_User_Info ui on u.id=ui.userid where u.loginno='$loginno' and u.status=1");
        if (count($searchResult) > 0) {
            if ($password === $searchResult[0]['password']) {
                $result = $searchResult[0]['id'];
                LoggerClass::info("用户密码校验成功：" . $loginno);
            } else {
                LoggerClass::info("用户密码校验失败，用户名或密码错误：" . $loginno);
            }
        } else {
            LoggerClass::info("用户密码校验失败，用户名或密码错误：" . $loginno);
        }
        return $result;
    }

    /**
     * 登录
     * @param string $loginno
     * @param string $password
     * @param string $yzm
     * @param string $LOGIN_USER_STR
     * @param \service\application\ApplicationClass $application
     * @param bool $singlePoint = true
     * @throws \Exception
     * @return array
     */
    public static function doLogin($loginno, $password, $yzm, $LOGIN_USER_STR, $application, $singlePoint = true)
    {
        $result = array();
        try {
            $commonTools = new ToolsClass(0);
            $searchResult = $commonTools->getDatasBySQL("select u.id,u.name,u.loginno,u.levels,ui.portrait,u.password from T_User u left join T_User_Info ui on u.id=ui.userid where u.loginno='$loginno' and u.status=1");
            if (count($searchResult) > 0) {
                if ($password === strtolower(md5($searchResult[0]['password'] . $yzm))) {
                    $roleids = $commonTools->getDatasBySQL("select r.id from T_Role r left join T_User_Role_Set ur on r.id=ur.roleid where ur.userid='" . $searchResult[0]['id'] . "' and r.appid='" . $application->getId() . "'");
                    if (count($roleids) > 0) {
                        $ip = IPClass::getRemoteIP();

                        $userlevels = intval($searchResult[0]['levels']);
                        if ($userlevels == 0) {
                            $userlevels = -1;
                        }
                        $loginUser = new UserClass();
                        $loginUser->setId($searchResult[0]['id']);
                        $loginUser->setName($searchResult[0]['name']);
                        $loginUser->setLoginno($searchResult[0]['loginno']);
                        $loginUser->setLevels($userlevels);
                        $loginUser->setPortrait($searchResult[0]['portrait']);

                        $connection = $commonTools->getDBConnection();
                        $userinfo = $commonTools->getDatasBySQL("select * from T_User_Info where userid='" . $loginUser->getId() . "'");
                        if (count($userinfo) > 0) {
                            $updateinfo = "update T_User_Info set last_login_time='" . ToolsClass::getNowTime() . "',last_login_ip='" . $ip . "' where userid='" . $loginUser->getId() . "'";
                        } else {
                            $updateinfo = "insert into T_User_Info(id,userid,portrait,last_login_time,last_login_ip) values('" . UUIDClass::getUUID() . "','" . $loginUser->getId() . "',null,'" . ToolsClass::getNowTime() . "','" . $ip . "')";
                        }
                        $connection->addBatch($updateinfo);
                        $connection->addBatch("insert into T_User_LoginRecord(id,userid,login_ip,login_time,login_date,appid) values('" . UUIDClass::getUUID() . "','" . $loginUser->getId() . "','" . $ip . "','" . ToolsClass::getNowTime() . "','" . ToolsClass::getNowDay() . "','" . $application->getId() . "')");
                        if (!$connection->doExecBatch()) {
                            throw new \Exception("数据库操作异常！");
                        }

                        self::setUser($LOGIN_USER_STR, $loginUser);
                        if ($singlePoint) {
                            self::updateOnlineUser($application->getId(), $loginUser->getId(), $ip, true);
                        } else {
                            self::updateOnlineUser($application->getId(), $loginUser->getId(), $ip, false);
                        }

                        $result[0] = 0;
                        $result[1] = "登录成功！";
                    } else {
                        $result[0] = 3;
                        $result[1] = "用户没有权限！";
                    }
                } else {
                    $result[0] = 2;
                    $result[1] = "用户名或密码错误！";
                }
            } else {
                $result[0] = 2;
                $result[1] = "用户名或密码错误！";
            }
        } catch (\Exception $e) {
            $result[0] = 3;
            $result[1] = "登录失败：" . $e->getMessage();
            LoggerClass::error_e('[应用:' . $application->getAppname() . ']' . $e->getMessage(), $e);
        }
        return $result;
    }

    /**
     * 退出登录
     * @param string $userid
     * @param string $LOGIN_USER_STR
     * @param \service\application\ApplicationClass $application
     * @return boolean
     */
    public static function doLogout($userid, $LOGIN_USER_STR, $application)
    {
        if (self::getUser($LOGIN_USER_STR)->getId() == $userid) {
            self::destroyOnlineUser($application->getId(), $userid, IPClass::getRemoteIP());
            session_unset();
            session_destroy();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 从session中获取当前登录用户对象
     *
     * @param string $LOGIN_USER_STR
     * @return \service\user\UserClass|NULL
     */
    public static function getUser($LOGIN_USER_STR)
    {
        if (isset($_SESSION[$LOGIN_USER_STR])) {
            return ToolsClass::unSerializeObj($_SESSION[$LOGIN_USER_STR]);
        } else {
            return null;
        }
    }

    /**
     * 设置当前登录用户对象到session中
     *
     * @param string $LOGIN_USER_STR
     * @param \service\user\UserClass $user
     */
    public static function setUser($LOGIN_USER_STR, $user)
    {
        $_SESSION[$LOGIN_USER_STR] = ToolsClass::serializeObj($user);
    }

    /**
     * 校验权限
     *
     * @param string $appid
     *            应用ID
     * @param string $userid
     *            用户ID
     * @param string $permissionCode
     *            编码
     * @return boolean
     */
    public static function validatePermissions($appid, $userid, $permissionCode)
    {
        $commonTools = new ToolsClass(0);
        $func = $commonTools->getDatasBySQL("select mf.islog,mf.moduleid from t_Module_Func mf,t_Role_Module_Func_Set rmf,
            t_User_Role_Set ur where mf.id=rmf.funcid and rmf.roleid=ur.roleid and ur.userid='" . $userid . "' and mf.code='" . $permissionCode . "' and mf.appid='" . $appid . "'");
        $module = $commonTools->getDatasBySQL("select m.parentid,case when m.parentid in (select id from t_module) then 'false' else 'true' end as istop
            from t_Module m,t_Role_Module_Set rm,t_User_Role_Set ur
            where m.id=rm.moduleid and rm.roleid=ur.roleid and ur.userid='" . $userid . "' and m.code='" . $permissionCode . "' and m.appid='" . $appid . "'");
        if (count($func) > 0) {
            $result = self::validateModulePermissions($appid, $userid, $func[0]['moduleid']);
            if ($result) {
                if ($func[0]['islog'] == '1') {
                    LoggerClass::info("校验权限：" . $permissionCode);
                }
            }
            return $result;
        } else {
            if (count($module) > 0) {
                if ($module[0]['istop'] == 'true') {
                    return true;
                } else {
                    return self::validateModulePermissions($appid, $userid, $module[0]['parentid']);
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 校验系统模块权限
     *
     * @param string $appid
     *            应用ID
     * @param string $userid
     *            用户ID
     * @param string $moduleid
     *            模块ID
     * @return boolean
     */
    private static function validateModulePermissions($appid, $userid, $moduleid = null)
    {
        $tools = new ToolsClass(0);
        $module = $tools->getDatasBySQL("select m.parentid,case when m.parentid in (select id from t_module) then 'false' else 'true' end as istop
            from t_Module m,t_Role_Module_Set rm,t_User_Role_Set ur
            where m.id=rm.moduleid and rm.roleid=ur.roleid and ur.userid='" . $userid . "' and m.id='" . $moduleid . "' and m.appid='" . $appid . "'");
        if (count($module) > 0) {
            if ($module[0]['istop'] == 'true') {
                return true;
            } else {
                return self::validateModulePermissions($appid, $userid, $module[0]['parentid']);
            }
        } else {
            return false;
        }
    }

    /**
     * 获取用户所在机构
     *
     * @param string $userid 用户ID，多个用户使用“,”分隔
     * @param int $infoType 0-获取机构ID，1-获取机构名称，2-获取机构编码，3-id|名称|编码，默认1
     * @return string 机构信息，多个结果用“,”分隔
     */
    public static function getDepartments($userid = '', $infoType = 1)
    {
        $tools = new ToolsClass(0);
        $whereuserid = '\'' . str_replace(',', '\',\'', $userid) . '\'';
        $searchStr = "select d.id as id,d.name as name,d.code as code,d.parentid as parentid,d.levels,d.sort from t_Department d where d.id in(select ud.departmentid from t_User_Department_Set ud where ud.userid in (" . $whereuserid . ")) order by d.levels asc,d.sort asc,d.code,d.name,d.parentid";
        $searchResult = $tools->getDatasBySQL($searchStr);
        if (count($searchResult) > 0) {
            $resultStr = '';
            foreach ($searchResult as $department) {
                if ($resultStr !== '') {
                    $resultStr = $resultStr . ',';
                }
                $dinfo = "";
                if ($infoType == 0) {
                    $dinfo = $department['id'];
                } else if ($infoType == 1) {
                    $dinfo = $department['name'];
                } else if ($infoType == 2) {
                    $dinfo = $department['code'];
                } else if ($infoType == 3) {
                    $dinfo = $department['id'] . '|' . $department['name'] . '|' . $department['code'];
                }
                $resultStr = $resultStr . $dinfo;
            }
            return $resultStr;
        } else {
            return '';
        }
    }

    /**
     * 获取用户所在机构及所有子机构
     *
     * @param string $userid 用户ID，多个用户使用“,”分隔
     * @param int $infoType 0-获取机构ID，1-获取机构名称，2-获取机构编码，3-id|名称|编码，默认1
     * @return string 机构信息，多个结果用“,”分隔
     */
    public static function getDepartmentsForChildren($userid = '', $infoType = 1)
    {
        $tools = new ToolsClass(0);
        $whereuserid = '\'' . str_replace(',', '\',\'', $userid) . '\'';
        $searchStr = "select d.id as id,d.name as name,d.code as code,d.parentid as parentid from t_Department d where d.id in (select ud.departmentid from t_User_Department_Set ud where ud.userid in (" . $whereuserid . ")) order by d.levels asc,d.sort asc";
        $searchResult = $tools->getDatasBySQL($searchStr);
        $alldepartments = $tools->getDatasBySQL("select id,name,code,parentid from t_department");
        if (count($searchResult) > 0) {
            $resultStr = '';
            $departments = array();
            foreach ($searchResult as $department) {
                array_push($departments, $department);
                $children = self::getDepartmentsChildren($department, $alldepartments);
                if (count($children) > 0) {
                    $departments = array_merge($departments, $children);
                }
            }
            foreach ($departments as $department) {
                if ($resultStr !== '') {
                    $resultStr = $resultStr . ',';
                }
                $dinfo = "";
                if ($infoType == 0) {
                    $dinfo = $department['id'];
                } else if ($infoType == 1) {
                    $dinfo = $department['name'];
                } else if ($infoType == 2) {
                    $dinfo = $department['code'];
                } else if ($infoType == 3) {
                    $dinfo = $department['id'] . '|' . $department['name'] . '|' . $department['code'];
                }
                $resultStr = $resultStr . $dinfo;
            }
            return $resultStr;
        } else {
            return '';
        }
    }

    /**
     * 获取子机构
     * @param array $parentDepartment 父机构（id,name,code,parentid）
     * @param array $departments 全部机构集合（id,name,code,parentid）
     * @return array 子机构集合
     */
    private static function getDepartmentsChildren($parentDepartment, $departments)
    {
        $resultarray = array();
        foreach ($departments as $department) {
            if ($department['parentid'] == $parentDepartment['id']) {
                array_push($resultarray, $department);
                $children = self::getDepartmentsChildren($department, $departments);
                if (count($children) > 0) {
                    $resultarray = array_merge($resultarray, $children);
                }
            }
        }
        return $resultarray;
    }

    /**
     * 获取用户最高角色级别
     * @param $userid
     * @return int
     */
    public static function getHighestRoleLevel($userid)
    {
        $tools = new ToolsClass(0);
        return intval($tools->getDatasBySQL("select min(r.levels) as levels from t_User_Role_Set ur,t_Role r where ur.roleid=r.id and ur.userid='" . $userid . "'")[0]['levels']);
    }

    /**
     * 获取用户所属角色
     * @param string $userid 用户ID，多个用户使用“,”分隔
     * @param bool $isId true-获取角色ID，false-获取角色名称，默认false
     * @param string $appid 应用ID
     * @return string 角色信息（ID或名称），多个结果用“,”分隔
     */
    public static function getRoles($userid = '', $isId = false, $appid = '')
    {
        $tools = new ToolsClass(0);
        $whereuserid = '\'' . str_replace(',', '\',\'', $userid) . '\'';
        $searchStr = "select r.id as id,r.name as name from t_Role r where r.id in (select ur.roleid from t_User_Role_Set ur where ur.userid in (" . $whereuserid . "))";
        if ($appid != '') {
            $searchStr .= " and r.appid='" . $appid . "'";
        }
        $searchStr .= " order by r.levels asc,r.sort asc";
        $searchResult = $tools->getDatasBySQL($searchStr);
        if (count($searchResult) > 0) {
            $resultStr = '';
            foreach ($searchResult as $role) {
                if ($resultStr !== '') {
                    $resultStr = $resultStr . ',';
                }
                if ($isId) {
                    $dinfo = $role['id'];
                } else {
                    $dinfo = $role['name'];
                }
                $resultStr = $resultStr . $dinfo;
            }
            return $resultStr;
        } else {
            return '';
        }
    }
}