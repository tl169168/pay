<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');

define('AUTO_REDIS','auto_redis_');
define('PROXY_REDIS','proxy_redis_');
define('INNET_REDIS','innet_redis_');
define('ADMIN_REDIS','admin_redis_');
define('AUTO_WX_REDIS','auto_wx_redis_');
define('BH_REDIS','bh_redis_');
//define('WWJ_PB_WX_REDIS','wwj_pb_wx_redis_');
// 定义错误码
//define('ADMIN_UPLOADS',__DIR__.'/../admin/uploads/');
include_once __DIR__.'/err.php';
require __DIR__ . '/thinkphp/start.php';
