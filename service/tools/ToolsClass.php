<?php

namespace service\tools;

use service\application\ApplicationClass;
use service\tools\logger\LoggerClass;

class ToolsClass
{

    private $connection = null;

    function __construct($resourceIndex = -1, $isPersistent = false)
    {
        if ($resourceIndex === -1) {
            if (isset($GLOBALS['app_dbno'])) {
                $resourceIndex = $GLOBALS['app_dbno'];
            } else {
                $resourceIndex = 0;
            }
        }
        $this->connection = new connection\ConnectionFactoryClass($resourceIndex, $isPersistent);
    }

    /**
     * 获取数据库连接对象
     *
     * @return \service\tools\connection\ConnectionFactoryClass
     */
    function getDBConnection()
    {
        return $this->connection;
    }

    /**
     * 获取浏览器及版本号
     *
     * @return string
     */
    static function getBrowser()
    {
        $agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
        if (strpos($agent, 'msie') !== false || strpos($agent, 'rv:11.0')) {
            if (strpos($agent, 'msie 8.0')) {
                return "ie8";
            } else
                if (strpos($agent, 'msie 7.0')) {
                    return "ie7";
                } else
                    if (strpos($agent, 'msie 6.0')) {
                        return "ie6";
                    } else {
                        return "ie";
                    }
        } else
            if (strpos($agent, 'firefox') !== false) {
                return "firefox";
            } else
                if (strpos($agent, 'chrome') !== false) {
                    return "chrome";
                } else
                    if (strpos($agent, 'opera') !== false) {
                        return 'opera';
                    } else
                        if (strpos($agent, 'chrome') == false && strpos($agent, 'safari') !== false) {
                            return 'safari';
                        } else {
                            return 'unknown';
                        }
    }

    /**
     * 判断浏览器是否是IE
     *
     * @return boolean
     */
    static function isIE()
    {
        $browser = self::getBrowser();
        if (strpos($browser, 'ie')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断浏览器是否是老版本IE
     *
     * @return boolean
     */
    static function isOldIE()
    {
        $browser = self::getBrowser();
        if (strpos($browser, 'ie8') !== false || strpos($browser, 'ie7') !== false || strpos($browser, 'ie6') !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 序列化对象
     *
     * @param object $obj
     * @return string
     */
    public static function serializeObj($obj)
    {
        return base64_encode(gzcompress(serialize($obj)));
    }

    /**
     * 反序列化对象
     *
     * @param string $seriaStr
     * @return object
     */
    public static function unSerializeObj($seriaStr)
    {
        return unserialize(gzuncompress(base64_decode($seriaStr)));
    }

    /**
     * 通过SQL语句从数据库查询数据
     *
     * @param string $query_string
     * @return array
     */
    public function getDatasBySQL($query_string)
    {
        if ($this->connection->isConnSucc()) {
            $query_result = $this->connection->doQuery($query_string);
            if ($query_result != null) {
                return $query_result->fetchAll();
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * 分页查询
     *
     * @param int $curr_page
     *            当前页数
     * @param int $max_count
     *            每页最大记录数
     * @param string $pKey
     *            查询语句主键索引，用于优化分页性能
     * @param array $sqlArray :[0]-字段名，[1]-表名，[2]-where条件（where 开头），[3]-附加条件（例如order by）
     * @return array:[0]-总页数，[1]-总记录数，[2]-查询结果集
     */
    public function getDatasBySQL_Pagin($curr_page, $max_count, $pKey = 'id', $sqlArray)
    {
        if ($this->connection->isConnSucc()) {
            $dbtype = $this->connection->getDbType();
            if (isset($sqlArray) && $sqlArray != null && count($sqlArray) > 0) {
                $query_string = "select " . $sqlArray[0] . " from " . $sqlArray[1] . " " . $sqlArray[2] . " " . $sqlArray[3];
                $totalrecord = intval($this->connection->doQuery('select count(' . $pKey . ') as cNum from ' . $sqlArray[1] . ' ' . $sqlArray[2])->fetchAll()[0]['cnum']);
                $totalpage = intval($totalrecord / $max_count);
                if ($totalrecord % $max_count > 0) {
                    $totalpage = $totalpage + 1;
                }
                $query_result = null;
                switch ($dbtype) {
                    case 'mysql':
                        $result = $this->connection->doQuery('select ' . $sqlArray[0] . ' from ' . $sqlArray[1] . ' inner join
                        (select ' . $pKey . ' as q_key_id from ' . $sqlArray[1] . ' ' . $sqlArray[2] . ' ' . $sqlArray[3] . ' limit ' . (($curr_page - 1) * $max_count) . ',' . $max_count . ')
                        _q_tmp_t on _q_tmp_t.q_key_id=' . $pKey . ' ' . $sqlArray[2] . ' ' . $sqlArray[3])->fetchAll();
                        if ($result != null) {
                            $query_result = array(
                                $totalpage,
                                $totalrecord,
                                $result
                            );
                        }
                        break;
                    case 'mssql':
                        $sql = "select " . $sqlArray[0] . ' from ' . $sqlArray[1];
                        $result = $this->connection->doQuery('select top ' . $max_count . ' ' . $sqlArray[0] . ' from (select * from (' . $sql . ')q_table where q_table.' . $pKey . ' not in (select top ' . (($curr_page - 1) * $max_count) . ' ' . $pKey . ' from ' . $sqlArray[1] . ' ' . $sqlArray[2] . ' ' . $sqlArray[3] . '))q_table_t ' . $sqlArray[2] . ' ' . $sqlArray[3])->fetchAll();
                        if ($result != null) {
                            $query_result = array(
                                $totalpage,
                                $totalrecord,
                                $result
                            );
                        }
                        break;
                    case 'oracle':
                        $result = $this->connection->doQuery('select q_table_t.* from (select q_table.*,ROWNUM as rn from (' . $query_string . ') q_table
                        where ROWNUM<=' . $curr_page * $max_count . ') q_table_t where q_table_t.rn>' . (($curr_page - 1) * $max_count))->fetchAll();
                        if ($result != null) {
                            $query_result = array(
                                $totalpage,
                                $totalrecord,
                                $result
                            );
                        }
                        break;
                    case 'postgresql':
                        $result = $this->connection->doQuery('select ' . $sqlArray[0] . ' from ' . $sqlArray[1] . ' inner join
                        (select ' . $pKey . ' as q_key_id from ' . $sqlArray[1] . ' ' . $sqlArray[2] . ' ' . $sqlArray[3] . ' limit ' . $max_count . ' offset ' . (($curr_page - 1) * $max_count) . ')
                        _q_tmp_t on _q_tmp_t.q_key_id=' . $pKey . ' ' . $sqlArray[2] . ' ' . $sqlArray[3])->fetchAll();
                        if ($result != null) {
                            $query_result = array(
                                $totalpage,
                                $totalrecord,
                                $result
                            );
                        }
                        break;
                    default:
                        $s_result = $this->connection->doQuery($query_string);
                        if ($s_result != null) {
                            $result_array = $s_result->fetchAll();
                            $max = $curr_page * $max_count;
                            if ($max > $totalrecord) {
                                $max = $totalrecord;
                            }
                            $result = array();
                            for ($i = ($curr_page - 1) * $max_count; $i < $max; $i++) {
                                array_push($result, $result_array[$i]);
                            }
                            $query_result = array(
                                $totalpage,
                                $totalrecord,
                                $result
                            );
                        }
                        break;
                }
                if ($query_result != null) {
                    return $query_result;
                } else {
                    return array(
                        1,
                        0,
                        array()
                    );
                }
            } else {
                return array(
                    1,
                    0,
                    array()
                );
            }
        } else {
            return array(
                1,
                0,
                array()
            );
        }
    }

    /**
     * 执行SQL语句
     *
     * @param string $sql_string
     * @return boolean
     */
    public function doExecuteSQL($sql_string)
    {
        if ($this->connection->isConnSucc()) {
            if ($this->connection->doExcute($sql_string)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 执行SQL语句
     * @param $sql_string
     * @param null $param
     * @return bool
     */
    public function doExecuteSQLByPre($sql_string, $param = null)
    {
        if ($this->connection->isConnSucc()) {
            if ($this->connection->doExcuteByPre($sql_string, $param)) {
                return true;
            }
        }
        return false;
    }

    /**
     * RSA公钥加密
     *
     * @param string $text
     * @return string
     */
    public function encryptRSA_public($text)
    {
        return security\RSAUtilsClass::_encrypt_public($text);
    }

    /**
     * RSA私钥解密
     * @param string $encryptedText
     * @return string
     */
    public function decryptRSA_private($encryptedText)
    {
        return security\RSAUtilsClass::_decrypt_private($encryptedText);
    }

    /**
     * RSA私钥加密
     *
     * @param string $text
     * @return string
     */
    public function encryptRSA_private($text)
    {
        return security\RSAUtilsClass::_encrypt_private($text);
    }

    /**
     * RSA公钥解密
     *
     * @param string $encryptedText
     * @return string
     */
    public function decryptRSA_public($encryptedText)
    {
        return security\RSAUtilsClass::_decrypt_public($encryptedText);
    }

    /**
     * AES加密 AES/ECB/PKCS5Padding
     *
     * @param string $text
     * @param string $key
     * @return string
     */
    public function encryptAES($text, $key)
    {
        $security = new security\AESUtilsClass();
        return $security->_encrypt($text, $key);
    }

    /**
     * AES解密 AES/ECB/PKCS5Padding
     *
     * @param string $encryptedText
     * @param string $key
     * @return string
     */
    public function decryptAES($encryptedText, $key)
    {
        $security = new security\AESUtilsClass();
        return $security->_decrypt($encryptedText, $key);
    }

    /**
     * 解密前端JS加密的密文
     *
     * @param string $encrypt_str
     * @return string
     */
    public function decryptFromJS($encrypt_str)
    {
        $json_str = urldecode($encrypt_str);
        $encrypt = (array)json_decode($json_str, true);
        if ($encrypt != null) {
            $key = $this->decryptRSA_private($encrypt['key']);
            if ($key != '') {
                return $this->decryptAES($encrypt['encryptedstr'], $key);
            }
        }
        return '';
    }

    /**
     * 获取应用信息
     * @param $webroot
     * @return ApplicationClass
     */
    public static function getApplicationInfo($webroot)
    {
        $applicationinfo = new ApplicationClass();
        $commonTools = new ToolsClass(0);
        $app_result = $commonTools->getDatasBySQL("select * from T_Application where webroot='" . $webroot . "'");
        if (count($app_result) > 0) {
            $app = $app_result[0];
            $applicationinfo->setId($app['id']);
            $applicationinfo->setWebroot($app['webroot']);
            $applicationinfo->setAppname($app['appname']);
            $applicationinfo->setDbno($app['dbno']);
            $applicationinfo->setLanguage($app['language']);
            $applicationinfo->setCopyrightOwner($app['copyright_owner']);
            $applicationinfo->setCopyrightBegin($app['copyright_begin']);
            $applicationinfo->setCopyrightEnd($app['copyright_end']);
            $applicationinfo->setVersion($app['version']);
            // info
            $info_result = $commonTools->getDatasBySQL("select * from T_Application_Info where appid='" . $app['id'] . "' and isEnabled='是'");
            $application_info = null;
            if (count($info_result) > 0) {
                $application_info = array();
                $index = 0;
                foreach ($info_result as $info) {
                    $child = array();
                    $child['id'] = $info['id'];
                    $child['info_name'] = $info['info_name'];
                    $child['info_value'] = $info['info_value'];
                    $application_info[$index] = $child;
                    $index++;
                }
            }
            $applicationinfo->setInfo($application_info);
            // link
            $link_result = $commonTools->getDatasBySQL("select * from T_Application_Link where appid='" . $app['id'] . "' and isEnabled='是'");
            $application_link = null;
            if (count($link_result) > 0) {
                $application_link = array();
                $index = 0;
                foreach ($link_result as $link) {
                    $child = array();
                    $child['id'] = $link['id'];
                    $child['link_type'] = $link['link_type'];
                    $child['link_name'] = $link['link_name'];
                    $child['link_url'] = $link['link_url'];
                    $child['link_image_url'] = $link['link_image_url'];
                    $application_link[$index] = $child;
                    $index++;
                }
            }
            $applicationinfo->setLink($application_link);
        }
        return $applicationinfo;
    }

    /**
     * 发送HTTP请求方法
     *
     * @param string $url
     *            请求URL
     * @param array $params
     *            请求参数
     * @param string $method ='POST'
     *            请求方法GET/POST
     * @param string $charset ='utf-8'
     *            请求字符编码
     * @param int $timeout
     *            超时时间
     * @param string $dataType = 'nomal'
     *            请求数据类型，nomal:键值对，json:json数据，xml:xml数据，byte:字节流数据
     * @return array $data 响应数据
     */
    function doHttp($url, $params, $method = 'POST', $charset = 'utf-8', $timeout = 30, $dataType = 'nomal')
    {
        $result = null;
        $opts = array();
        $header = array();
        /**
         * 根据请求类型设置特定参数
         */
        switch (strtoupper($method)) {
            case 'GET':
                array_push($header, "content-type: text/html; charset=" . $charset);
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                switch (strtolower($dataType)) {
                    case 'json':
                        array_push($header, "content-type: application/json; charset=" . $charset);
                        $data_string = json_encode($params);
                        break;
                    case 'xml':
                        array_push($header, "content-type: application/xml; charset=" . $charset);
                        $data_string = $params;
                        break;
                    case 'byte':
                        array_push($header, "content-type: application/octet-stream; charset=" . $charset);
                        $data_string = $params;
                        break;
                    default:
                        array_push($header, "content-type: application/x-www-form-urlencoded; charset=" . $charset);
                        $data_string = http_build_query($params);
                }
                array_push($header, "Accept-Charset=" . $charset);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_CUSTOMREQUEST] = "POST";
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_RETURNTRANSFER] = 1;
                $opts[CURLOPT_POSTFIELDS] = $data_string;
                array_push($header, 'content-Length: ' . strlen($data_string));
                break;
            default:
                $result = ToolsClass::buildJSONErrorStr("不支持的请求方式！");
                return $result;
        }
        array_push($header, "CLIENT-IP:" . common\IPClass::getRemoteIP());
        array_push($header, "X-FORWARDED-FOR:" . common\IPClass::getRemoteIP());

        $opts[CURLOPT_TIMEOUT] = $timeout;
        $opts[CURLOPT_FOLLOWLOCATION] = 1;
        $opts[CURLOPT_SSL_VERIFYPEER] = false;
        $opts[CURLOPT_SSL_VERIFYHOST] = false;
        $opts[CURLOPT_HTTPHEADER] = $header;

        /**
         * 初始化并执行curl请求
         */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            LoggerClass::error('请求发生错误：' . $error);
            $result = ToolsClass::buildJSONErrorStr('请求发生错误：' . $error);
        }
        return $result;
    }

    /**
     * 发送HTTP文件下载请求
     *
     * @param string $url
     *            请求URL
     * @param array $params
     *            请求参数
     * @param string $filename
     *            保存时的文件名称
     * @param string $charset ='utf-8'
     *            请求字符编码
     * @param int $timeout
     *            超时时间
     * @return array $data 响应数据
     */
    function doHttpDownload($url, $params, $filename, $charset = 'utf-8', $timeout = 30)
    {
        $result = null;
        $opts = array();
        $header = array();
        array_push($header, "content-type: application/octet-stream; charset=" . $charset);
        $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
        array_push($header, "CLIENT-IP:" . common\IPClass::getRemoteIP());
        array_push($header, "X-FORWARDED-FOR:" . common\IPClass::getRemoteIP());

        $opts[CURLOPT_TIMEOUT] = $timeout;
        $opts[CURLOPT_RETURNTRANSFER] = 1;
        $opts[CURLOPT_FOLLOWLOCATION] = 1;
        $opts[CURLOPT_SSL_VERIFYPEER] = false;
        $opts[CURLOPT_SSL_VERIFYHOST] = false;
        $opts[CURLOPT_HTTPHEADER] = $header;

        /**
         * 初始化并执行curl请求
         */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $result = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            $result = ToolsClass::buildJSONErrorStr('请求发生错误：' . $error);
        }
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length:" . count($result));
        header("Content-Disposition: attachment; filename=" . $filename);
        return $result;
    }

    /**
     * 获取系统当前时间
     *
     * @return string Y-m-d H:i:s
     */
    static function getNowTime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 获取系统当前日期
     *
     * @return string Y-m-d
     */
    static function getNowDay()
    {
        return date('Y-m-d');
    }

    /**
     * 构建JSON错误返回字符串
     *
     * @param string $message =""
     *            错误信息
     * @return array
     */
    static function buildJSONError($message = "")
    {
        $result_tmp = array();
        $result_tmp['errmsg'] = $message;
        return $result_tmp;
    }

    /**
     * 构建JSON错误返回字符串
     *
     * @param string $message =""
     *            错误信息
     * @return string
     */
    static function buildJSONErrorStr($message = "")
    {
        return json_encode(self::buildJSONError($message));
    }

    /**
     * 获取数据库表T_RuntimeConfig中系统参数值
     *
     * @param string $sysParamName
     * @return string
     */
    static function getSysParamValue($sysParamName)
    {
        $commonTools = new ToolsClass(0);
        $result = $commonTools->getDatasBySQL("select confvalue from T_RuntimeConfig where confname='" . $sysParamName . "' and status=1");
        if (count($result) == 1) {
            return $result[0]['confvalue'];
        } else {
            return '';
        }
    }

    /**
     * 判断数据库表T_RuntimeConfig中系统参数是否可用
     *
     * @param string $sysParamName
     * @return boolean
     */
    static function getSysParamIsEnabled($sysParamName)
    {
        $commonTools = new ToolsClass(0);
        $result = $commonTools->getDatasBySQL("select status from T_RuntimeConfig where confname='" . $sysParamName . "'");
        if (count($result) == 1) {
            $status = $result[0]['status'];
            if ($status == '1') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除文件夹
     * @param $path
     */
    static function deleteDirOrFile($path)
    {
        if (is_dir($path)) {
            $op = dir($path);
            while (false != ($item = $op->read())) {
                if ($item == '.' || $item == '..') {
                    continue;
                }
                self::deleteDirOrFile($op->path . '/' . $item);
            }
            rmdir($path);
        } else if (file_exists($path)) {
            unlink($path);
        }
    }
}

?>