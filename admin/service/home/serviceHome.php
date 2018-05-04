<?php
require dirname(__FILE__) . '/../../view/common/serviceHead.php';
$result = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cmd'])) {
        $cmd = $_POST['cmd'];
        $commonTools = new \service\tools\ToolsClass(0);
        switch ($cmd) {
            case "getOnlineUserInfo":
                $isShowAll = \service\user\UserManagerClass::validatePermissions($GLOBALS['application']->getId(), \admin\service\tools\ToolsClass::getUser()->getId(), 'allonlineuser');
                if ($isShowAll) {
                    $info = array();
                    $info['isall'] = true;
                    $apps = $commonTools->getDatasBySQL("select * from t_application order by sort asc");
                    $count = 0;
                    $appnames = array();
                    $usercounts = array();
                    foreach ($apps as $app) {
                        $users = \service\user\UserManagerClass::getOnlineUsers($app['id']);
                        $count = $count + count($users);
                        array_push($appnames, $app['appname']);
                        array_push($usercounts, count($users));
                    }
                    $info['total'] = $count;
                    $info['appnames'] = $appnames;
                    $info['usercounts'] = $usercounts;
                    $result['info'] = $info;
                } else {
                    $info = array();
                    $info['isall'] = false;
                    $totalusers = $commonTools->getDatasBySQL("select count(id) as num from t_User");
                    $users = \service\user\UserManagerClass::getOnlineUsers($GLOBALS['application']->getId());
                    $total = intval($totalusers[0]['num']);
                    $info['usercounts'] = count($users);
                    $splitNumber = 1;
                    $axisTick_splitNumber = 1;
                    if ($total <= 10) {
                        $splitNumber = $total;
                    } else {
                        if ($total % 10 == 0) {
                            $splitNumber = 10;
                            $axisTick_splitNumber = $total / 10;
                        } else
                            if ($total % 9 == 0) {
                                $splitNumber = 9;
                                $axisTick_splitNumber = $total / 9;
                            } else
                                if ($total % 8 == 0) {
                                    $splitNumber = 8;
                                    $axisTick_splitNumber = $total / 8;
                                } else
                                    if ($total % 7 == 0) {
                                        $splitNumber = 7;
                                        $axisTick_splitNumber = $total / 7;
                                    } else
                                        if ($total % 6 == 0) {
                                            $splitNumber = 6;
                                            $axisTick_splitNumber = $total / 6;
                                        } else
                                            if ($total % 5 == 0) {
                                                $splitNumber = 5;
                                                $axisTick_splitNumber = $total / 5;
                                            } else
                                                if ($total % 4 == 0) {
                                                    $splitNumber = 4;
                                                    $axisTick_splitNumber = $total / 4;
                                                } else
                                                    if ($total % 3 == 0) {
                                                        $splitNumber = 3;
                                                        $axisTick_splitNumber = $total / 3;
                                                    } else {
                                                        $total = $total + (5 - $total % 5);
                                                        $splitNumber = 5;
                                                        $axisTick_splitNumber = $total / 5;
                                                    }
                    }
                    $info['total'] = $total;
                    $info['splitNumber'] = $splitNumber;
                    $info['axisTick']['splitNumber'] = $axisTick_splitNumber;
                    $result['info'] = $info;
                }
                break;
        }
        echo json_encode($result);
    }
}
?>