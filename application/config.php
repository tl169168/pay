<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'trim',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'admin',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Login',
    // 默认操作名
    'default_action'         => 'login',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,


    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__STATIC__' => '/pay/static',
        ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    //支付宝游艺100应用生成的私钥
    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEpQIBAAKCAQEAyMyX9OVjb/kYLax4Ix50k8RtPZoeNpB+LsV+BTCr8m/oepvPZLz+TN6EBbmZidqwFYEoX+xpwZhT9wWo0C8P5k7tp3k7t/GotqCPowuUyCQEaKei46ZCWY3FUZ5pbeBkGoE+EziQjJbiicQJTrdHPFW/Ui6yH/gYpCaLdVljQdJcEFl/pgviaFQWL+bF27wSgIjBFAJ2LnHCe2oRNPwk99pmngsH76T9PClYVD9pugou/rwvRLxSvDOBLYZZi0tIT7bMqXgkvr431yZTdNvVGoxZ+1QhrThKgeVHavz54P8HCApjvZV3ek+PpKmjqznYyHZ8xpPCCmoG3mtVebLM7QIDAQABAoIBAQCiikqvKEg3yZEy15tgAjUnsCclaG3wiUI7Jg5+sQle9AthxGI7D2liW/TOlZCdsHI81hISo9JvrZi6KtCdxJrAOT/TAyW+HlYNjyb2OlezMsSG2rvWPy0SRNpm2S5KjQs7EUdoU69evnyePBu0plN5mYeHRNlXW4LGzZSx5yubiPd+yUNffz16x6Xze9copih38Zrbms/Bmbk18nlBg1sKD09NxsMnonJH+nZ/BBNFA/6sPEBIK9mw+8dZHbfSeSlzNPIj8woNwoR9XB6gasNBMVL2Uy5YnCSJk6cYrTN0/K5X/AjjQvluoc1DC35KRFtlxHNCmKC/kfFWYdx+zOoBAoGBAOSThna1Om9+ongfeErCIxZvVHgtXOeAVOpocFhqJw2k4iFtfkFbEavY/YaF3u/4CO5WrWyP4il3wr5shGD0MW83tQYHqhQkoPrz04lYCs6pPaRDzA9Iag0x07TCSt+k9GCthXo9fwhtM8NvdtUQb/TzgxXSyJEk0OU3ik99mhEtAoGBAODj7Nxn81RspYBmfjMeUgNwtn7xN2kTp/3+3R6LtcoUANMJ71ICqOiBRRgM9m0SH6fT1NTFh2lvRAaHzyIeZbyBQwWLXCuQVa/awoDHlFR0PRhzfUdLHJBOL4vmx+no4rTYuiABJSYNBoHq+f5Dh0JbFbyGbRC39Dc9fbAw/4LBAoGBAKJSteHlT4tQRfbCen20opBTHYx+woRQmX0iD+5p7DP/TOtqQ4gMMV91qI140lpeLZF8kooPIBBM0UrD67qij9yolfCjTsAhRwUQVMArevlKLNFTqD2OMmoOYYbzl5J9JWLmt2yY8Xa9fk4jASPMGYW9zPCZkP+qQoMVTy6mRtw9AoGBAI6evWYB3oUZ6dOGLF6KygQ4hOP4YeWXe6BO8zgd7gnbqbIsyMM/wJLC+GiKP/Vn2v96Da1qH7gzwfZKRogisu5bI3/uo2NVQ6Ikn9k/uMfed6h8BWUjM1go3Wphz0J+WNfL4i3NzBtXg1r9A9HGTY89aImIzr4dixlFO+JzEHeBAoGAVl/s4yp9RTiO6oDgKzAyfxApWY8+FcY9Ew4uiF9k2ixdy0EZv9JN05hzUB8DAkUbo3rcH2C96mBaTekMjbbCcXgFct5EqWI38rch5tJZQZVYL/DUGm+LHaMQUqMBnKQ7zPgAYxfpYL+7BJOWEAsWrCU56nhFY0VNGF+AWG5aDAc="
    ,
    'merchant_public_key' =>"MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyMyX9OVjb/kYLax4Ix50k8RtPZoeNpB+LsV+BTCr8m/oepvPZLz+TN6EBbmZidqwFYEoX+xpwZhT9wWo0C8P5k7tp3k7t/GotqCPowuUyCQEaKei46ZCWY3FUZ5pbeBkGoE+EziQjJbiicQJTrdHPFW/Ui6yH/gYpCaLdVljQdJcEFl/pgviaFQWL+bF27wSgIjBFAJ2LnHCe2oRNPwk99pmngsH76T9PClYVD9pugou/rwvRLxSvDOBLYZZi0tIT7bMqXgkvr431yZTdNvVGoxZ+1QhrThKgeVHavz54P8HCApjvZV3ek+PpKmjqznYyHZ8xpPCCmoG3mtVebLM7QIDAQAB"
,
    'alipay_public_key' =>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAgA5iliOwNjsKuQ/YKe87q3sZYs7L86pT6NCo1K5JlYZNgFJC20d+jOX7JPDwvyB2kH9WiyQmNf1DconftKiE85/9drFbPnGlBs1gEHKwKqIQrDokjpW96FP3x8wQqAssDr6tQQFhlVV4YQzwz6oi+9VHpO19+2AqvI/MiTcontOytTU1xLw4O//JNxHE9t1Dpk9ZsYm1SAMWBkZx0ATJ1TJ2SgzbzymEtri5Reea5R1KD2ZjhDEjzv8SGNqVT+NekAuVlC3WbINHU8RNBmQKLixD8GFXOMBFJucu1PyOt3vE6HT9YX5hn/ZXl+o7rFunudOBehVUVVQUQgfxyGs51wIDAQAB'
    ];
