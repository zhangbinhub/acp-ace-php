<?php

namespace config;

class DataBaseConfig implements base\IBaseConfig
{

    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            $instance = self::generateConfigInfo();
            self::$instance = $instance;
        }
        return self::$instance;
    }

    private static function generateConfigInfo()
    {
        $resource = array();

        /**
         * 系统默认数据源 MySQL
         */
        $resource[0] = array(
            /**
             * 数据源名称
             */
            "name" => "系统默认数据源",
            /**
             * 链接字符串
             */
            "url" => "mysql:host=127.0.0.1:3306;dbname=acp;charset=utf8",
            /**
             * 数据库类型，针对不同类型数据库进行优化，目前支持：mysql，mssql，oracle，postgresql
             */
            "dbtype" => "mysql",
            /**
             * 链接字符集
             */
            "charset" => "utf8",
            /**
             * 数据库用户名
             */
            "username" => "root",
            /**
             * 数据库密码
             */
            "password" => "test",
            /**
             * 错误报告：ERRMODE_SILENT（仅设置错误代码），ERRMODE_WARNING，ERRMODE_EXCEPTION
             */
            "ATTR_ERRMODE" => "ERRMODE_EXCEPTION",
            /**
             * 转换 NULL 和空字符串：NULL_NATURAL，NULL_EMPTY_STRING，NULL_TO_STRING
             */
            "ATTR_ORACLE_NULLS" => "NULL_TO_STRING"
        );

        /**
         * 1 号数据源 SQL Server
         */
        $resource[1] = array(
            /**
             * 数据源名称
             */
            "name" => "1号数据源",
            /**
             * 链接字符串
             */
            "url" => "sqlsrv:Server=127.0.0.1,1433;Database=reallink",
            /**
             * 数据库类型，针对不同类型数据库进行优化，目前支持：mysql，mssql，oracle，postgresql
             */
            "dbtype" => "mssql",
            /**
             * 链接字符集
             */
            "charset" => "utf8",
            /**
             * 数据库用户名
             */
            "username" => "sa",
            /**
             * 数据库密码
             */
            "password" => "test",
            /**
             * 错误报告：ERRMODE_SILENT（仅设置错误代码），ERRMODE_WARNING，ERRMODE_EXCEPTION
             */
            "ATTR_ERRMODE" => "ERRMODE_EXCEPTION",
            /**
             * 转换 NULL 和空字符串：NULL_NATURAL，NULL_EMPTY_STRING，NULL_TO_STRING
             */
            "ATTR_ORACLE_NULLS" => "NULL_TO_STRING"
        );

        /**
         * 2 号数据源 Oracle
         */
        $resource[2] = array(
            /**
             * 数据源名称
             */
            "name" => "2号数据源",
            /**
             * 链接字符串
             */
            "url" => "oci:dbname=//127.0.0.1:1521/ORCL;charset=utf8",
            /**
             * 数据库类型，针对不同类型数据库进行优化，目前支持：mysql，mssql，oracle，postgresql
             */
            "dbtype" => "oracle",
            /**
             * 链接字符集
             */
            "charset" => "utf8",
            /**
             * 数据库用户名
             */
            "username" => "C##test",
            /**
             * 数据库密码
             */
            "password" => "test",
            /**
             * 错误报告：ERRMODE_SILENT（仅设置错误代码），ERRMODE_WARNING，ERRMODE_EXCEPTION
             */
            "ATTR_ERRMODE" => "ERRMODE_EXCEPTION",
            /**
             * 转换 NULL 和空字符串：NULL_NATURAL，NULL_EMPTY_STRING，NULL_TO_STRING
             */
            "ATTR_ORACLE_NULLS" => "NULL_TO_STRING"
        );

        /**
         * 3 号数据源 postgresql
         */
        $resource[3] = array(
            /**
             * 数据源名称
             */
            "name" => "3号数据源",
            /**
             * 链接字符串
             */
            "url" => "pgsql:host=127.0.0.1;port=5432;dbname=acp",
            /**
             * 数据库类型，针对不同类型数据库进行优化，目前支持：mysql，mssql，oracle，postgresql
             */
            "dbtype" => "postgresql",
            /**
             * 链接字符集
             */
            "charset" => "utf8",
            /**
             * 数据库用户名
             */
            "username" => "postgres",
            /**
             * 数据库密码
             */
            "password" => "test",
            /**
             * 错误报告：ERRMODE_SILENT（仅设置错误代码），ERRMODE_WARNING，ERRMODE_EXCEPTION
             */
            "ATTR_ERRMODE" => "ERRMODE_EXCEPTION",
            /**
             * 转换 NULL 和空字符串：NULL_NATURAL，NULL_EMPTY_STRING，NULL_TO_STRING
             */
            "ATTR_ORACLE_NULLS" => "NULL_TO_STRING"
        );

        return $resource;
    }
}

?>