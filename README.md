# acp-ace
在 ace-admin 样式库基础上进行重新修改，并结合 PHP 开发完成一套前端框架
# 环境要求
- php 7 以上
- mysql 5.6 以上
- apache 2.4.20 以上

# php 环境安装
### windows
- 安装 [Visual Studio 2015](https://www.microsoft.com/zh-CN/download/details.aspx?id=48145)
- 安装 [Apache](http://www.apachelounge.com/download/ )
    - 解压缩 httpd-2.4.20-win64-VC14.zip 到 D:\phpEnv
    - 增加windows系统服务：D:\phpEnv\Apache24\bin 目录下执行命令 httpd.exe -kinstall -n "Apache24"
    - 卸载windows系统服务：D:\phpEnv\Apache24\bin 目录下执行命令 httpd.exe -k uninstall -n "Apache24"
    - 配置 httpd.conf
        - ServerRoot "D:/phpEnv/Apache24"
        - Listen 8888
        - ServerName localhost:8888
        - DocumentRoot "D:/" 和 <Directory "D:/"> （注：今后部署的文件所在路径的顶层必须包含此目录）
        - Include conf/extra/httpd-vhosts.conf
        - DirectoryIndex index.html index.php
        - LoadModule vhost_alias_module modules/mod_vhost_alias.so
        - LoadModule proxy_module modules/mod_proxy.so
        - LoadModule proxy_http_module modules/mod_proxy_http.so
        - LoadModule proxy_ajp_module modules/mod_proxy_ajp.so
        - LoadModule rewrite_module modules/mod_rewrite.so
        - 所有AllowOverride None修改为：AllowOverride All
        - httpd-vhosts.conf 中配置 
        ```
        <VirtualHost *:8888>
            DocumentRoot "D:/phpEnv/document/cms"
            ServerName 127.0.0.1
            ErrorLog "logs/cms-error.log"
            CustomLog "logs/cms-access.log" common
        </VirtualHost>
        ```
- 安装 [php](http://windows.php.net/download#php-7.0 )
    - 解压缩 php-7.0.8-Win32-VC14-x64.zip 到 D:\phpEnv\php7.0.8 下
    - 修改 php.ini 配置文件（复制 php.ini-development 或 php.ini-production）
    - date.timezone = PRC
    - extension_dir = "D:\phpEnv\php7.0.8\ext"
    - file_uploads = On
    - upload_tmp_dir = "上传文件时的临时文件夹"
    - Apache http.conf 最后加上
    ```
    # php7 support
    LoadModule php7_module "D:/phpEnv/php7.0.8/php7apache2_4.dll"
    AddType application/x-httpd-php .php .html .htm
    # configure the path to php.ini
    PHPIniDir "D:/phpEnv/php7.0.8"
    ```
    - 修改系统环境变量
    ```$xslt
    PHP_HOME=D:\phpEnv\php7.0.8
    Path中增加 %PHP_HOME%； %PHP_HOME%\ext
    PATHEXT中增加 .PHP；
    ```
    - 开启扩展
    ```$xslt
    curl
    gd
    mbstring
    openssl
    mcrypt
    mysqli
    pdo_mysql
    ```
### linux
- 安装 [Apache](http://httpd.apache.org/download.cgi#apache24)
    - 安装 [apr](http://apr.apache.org/download.cgi)
    ```$xslt
    cd /usr/local
    tar jxvf apr-1.5.2.tar.bz2
    cd apr-1.5.2
    ./configure --prefix=/usr/local/apr
    make
    make install
    ```
    - 安装 [apr-util](http://apr.apache.org/download.cgi)
    ```$xslt
    cd /usr/local
    tar jxvf apr-util-1.5.4.tar.bz2
    cd apr-util-1.5.4
    ./configure --prefix=/usr/local/apr --with-apr=/usr/local/apr
    make
    make install
    ```
    - 安装 [pcre](https://sourceforge.net/projects/pcre/ )
    ```$xslt
    cd /usr/local
    tar -zxvf pcre-8.38.tar.gz
    cd pcre-8.38
    ./configure --prefix=/usr/local/pcre
    make
    make install
    注意：如果在安装 pcre 时，遇到问题：configure: error: You need a C++ compiler for C++ support.
    解决方法：sudo apt-get install build-essential
    ```
    - 安装 httpd
    ```$xslt
    tar -zxvf httpd-2.4.20.tar.gz
    cd httpd-2.4.20
    
    ./configure --prefix=/usr/local/apache2420 --with-apr=/usr/local/apr --with-apr-util=/usr/local/apr --with-pcre=/usr/local/pcre --with-mpm=worker --enable-mpms-shared=all --enable-rewrite
    
    或直接指定 --with-mpm=worker
    
    make
    make install
    ```
    - 添加开机启动
    ```$xslt
    1、cp /usr/local/apache2420/bin/apachectl /etc/rc.d/init.d/apache2420
    2、编辑apache2420文件，#!/bin/sh下面增加：
    #chkconfig: 2345 10 90
    #description: Activates/Deactivates Apache Web Server
    3、cd /etc/rc.d/init.d
    chkconfig --add apache2420
    chkconfig --levels 345 apache2420 on
    4、启动、停止、重启
    service apache2420 start|stop|restart
    ```
    - 配置参考 windows
- 安装 [php](http://php.net/get/php-7.0.8.tar.bz2/from/a/mirror )
    - 准备工作
    ```$xslt
    yum -y install php-mcrypt libmcrypt libmcrypt-devel libxml2-devel openssl-devel libcurl-devel libjpeg.x86_64 libpng.x86_64 freetype.x86_64 libjpeg-devel.x86_64 libpng-devel.x86_64 freetype-devel.x86_64 libjpeg-turbo-devel mhash re2c
    ```
    - 执行安装
    ```$xslt
    1、	上传php-7.0.8.tar.bz2至/usr/local
    2、	cd /usr/local
    3、	tar jxvf php-7.0.8.tar.bz2
    4、	cd php-7.0.8
    5、	依次执行
    ./configure --prefix=/usr/local/php-7.0.8 --with-apxs2=/usr/local/apache2420/bin/apxs --with-iconv-dir=/usr --with-freetype-dir=/usr/include/freetype2/freetype --with-jpeg-dir --with-png-dir --with-zlib --enable-xml --disable-rpath --enable-bcmath --enable-shmop --enable-sysvsem --enable-inline-optimization --with-curl --enable-mbregex --enable-mbstring --with-gd --enable-gd-native-ttf --with-openssl --with-mhash --enable-sockets --with-xmlrpc --enable-zip --enable-soap --enable-exif --enable-calendar --with-mysqli --with-pdo-mysql --with-mcrypt=/usr/include --with-libmcrypt
    
    make
    make install
    ```
    - 安装扩展
        - curl
        ```$xslt
        1、cd /usr/local/php-7.0.8/ext/curl
        2、/usr/local/php-7.0.8/bin/phpize
        3、./configure --with-php-config=/usr/local/php-7.0.8/bin/php-config
        4、make
        5、make install
        ```
        - gd
        ```$xslt
        1、cd /usr/local/php-7.0.8/ext/gd
        2、/usr/local/php-7.0.8/bin/phpize
        3、./configure --with-php-config=/usr/local/php-7.0.8/bin/php-config
        4、make
        5、make install
        ```
        - openssl
        ```$xslt
        1、cd /usr/local/php-7.0.8/ext/openssl
        2、cp config0.m4 config.m4
        3、/usr/local/php-7.0.8/bin/phpize
        4、./configure --with-php-config=/usr/local/php-7.0.8/bin/php-config
        5、make
        6、make install
        ```
        - mbstring
        ```$xslt
        1、cd /usr/local/php-7.0.8/ext/mbstring
        2、/usr/local/php-7.0.8/bin/phpize
        3、./configure --with-php-config=/usr/local/php-7.0.8/bin/php-config
        4、make
        5、make install
        ```
        - mysqli
        ```$xslt
        1、cd /usr/local/php-7.0.8/ext/mysqli
        2、/usr/local/php-7.0.8/bin/phpize
        3、./configure --with-php-config=/usr/local/php-7.0.8/bin/php-config
        4、make
        5、make install
        ```
        - pdo_mysql
        ```$xslt
        1、cd /usr/local/php-7.0.8/ext/pdo_mysql
        2、/usr/local/php-7.0.8/bin/phpize
        3、./configure --with-php-config=/usr/local/php-7.0.8/bin/php-config
        4、make
        5、make install
        ```
        - mcrypt
        ```$xslt
        1、cd /usr/local/php-7.0.8/ext/mcrypt
        2、/usr/local/php-7.0.8/bin/phpize
        3、./configure --with-php-config=/usr/local/php-7.0.8/bin/php-config
        4、make
        5、make install
        ```
        - 配置参考 windows
### php 其他配置
    其他配置项已在以下文件中进行动态配置
    .htaccess
    /view/common/config.php
# 工程配置
数据源配置：/config/DataBaseConfig.php
# .htaccess重写URL说明
    1.请求的地址如果以“/”结尾，则地址后增加“index.php”连接到index.php并结束；
    2.请求的地址格式如果是“/xxxx”，则地址后增加“/index.php”连接到index.php并结束；
    3.除以上两种情况外，请求地址中的目标文件如果没有扩展名或扩展名是“.php”，则地址后增加“.php”；
# 相关开源库源码修改记录
[点击这里查看](libmodify.md)

