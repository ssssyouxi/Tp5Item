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
//other
Route::rule('service/', 'service/service_index');
Route::rule('v3/About-Kefid/:name', 'about/about_technology');
Route::rule('v3/About-Kefid/:name', 'about_honor');
Route::rule('v3/About-Kefid/:name', 'about/about_agent');
Route::rule('v3/About-Kefid/', 'about/about_index');
Route::rule('v3/contact', 'contact/contact_index');
Route::rule('v3/products/article/:id','product/ppc_article')->pattern(['id' => '\d+$']);
Route::rule('v3/products/article/:filename','product/ppc_article')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
//product list
Route::rule('v3/Mobile-Crusher/', 'product/mobile_list');
Route::rule('v3/Mobile-Crusher/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('v3/Mobile-Crusher/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('v3/Crushing-Plant/', 'product/stationary_list');
Route::rule('/v3/Crushing-Plant/Jaw-Crushers/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('/v3/Crushing-Plant/Jaw-Crushers/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('/v3/Crushing-Plant/Impact-Crushers/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('/v3/Crushing-Plant/Impact-Crushers/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('/v3/Crushing-Plant/Cone-Crushers/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('/v3/Crushing-Plant/Cone-Crushers/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('/v3/Crushing-Plant/VSI-Crushers/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('/v3/Crushing-Plant/VSI-Crushers/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
// Route::rule('/v3/Crushing-Plant/:id','product/artproduct')->pattern(['id' => '\d+$']);
// Route::rule('/v3/Crushing-Plant/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);



Route::rule('v3/Grinding-Mill/', 'product/grindingmill_list');
Route::rule('v3/Grinding-Mill/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('v3/Grinding-Mill/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('v3/Screening-Washing/', 'product/vsi_list');
Route::rule('v3/Screening-Washing/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('v3/Screening-Washing/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('v3/Aggregate-Production-Line/', 'product/aggregate_production_line_list');
Route::rule('v3/Aggregate-Production-Line/:id','product/aggregate_production_line_article')->pattern(['id' => '\d+$']);
Route::rule('v3/Aggregate-Production-Line/:filename','product/aggregate_production_line_article')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('v3/Feeding-Conveying/', 'product/feeding_conveying_list');
Route::rule('v3/Feeding-Conveying/:id','product/artproduct')->pattern(['id' => '\d+$']);
Route::rule('v3/Feeding-Conveying/:filename','product/artproduct')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
//product index 如果写上边，下边会继承，v3/Mobile-Crusher/ 会访问到product/products，写下边则不会
Route::rule('v3/products', 'product/products');
//case 路由
Route::rule('Case/:typedir/:id','caselist/case_article')->pattern(['id' => '\d+$']);
Route::rule('Case/:typedir/:filename','caselist/case_article')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('Case/:typedir', 'caselist/case_list');
Route::rule('Case', 'caselist/case_index');
//solution 路由
Route::rule('solutions/', 'solution/solution_index');
Route::rule('solutions/:id','solution/solution_article')->pattern(['id' => '\d+$']);
Route::rule('solutions/:filename','solution/solution_article')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);

//news 路由
Route::rule('News/', 'news/news_index');
Route::rule('special/:name','spec/:name');
Route::rule('special/', 'news/news_focus');
Route::rule('company/:id','news/news_article')->pattern(['id' => '\d+$']);
Route::rule('company/:filename','news/news_article')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('company', 'news/company');
Route::rule('industry/:id','news/news_article')->pattern(['id' => '\d+$']);
Route::rule('industry/:filename','news/news_article')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('industry', 'news/industry');
Route::rule('knowledge/:id','news/news_article')->pattern(['id' => '\d+$']);
Route::rule('knowledge/:filename','news/news_article')->pattern(['filename' => '[0-9a-zA-Z-_ ]+']);
Route::rule('knowledge', 'news/knowledge');

//测试开始
Route::rule('/v3/Crushing-Plant/:type','product/sta_type')->pattern(['type' => '[0-9a-zA-Z-_ ]+']);
//测试结束

return [
];
