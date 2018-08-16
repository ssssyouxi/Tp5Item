<?php
return [
    'http_exception_template'    =>  [
        // 定义404错误的模板文件地址
        404 =>  Env::get('app_path') . 'admin/404.html',
        // 还可以定义其它的HTTP status
        401 =>  Env::get('app_path') . '401.html',
    ]
    ];