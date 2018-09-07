<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //图书管理
    $router->resource('book/category','Book\CategoryController');
    $router->resource('book','Book\BookController');

    //区域活动管理
    $router->get('regional_activities/getDurationDataByClass', 'RegionalActivities\DurationStatisticController@getDurationDataByClass');
    $router->resource('regional_activities/duration_statistic','RegionalActivities\DurationStatisticController');
    $router->resource('regional_activities/rfid_reader','RegionalActivities\RFIDReaderController');
    $router->resource('regional_activities/rfid_read','RegionalActivities\RFIDReadController');

    //基础管理
    $router->resource('basic/school_class','Basic\SchoolClassController');
    $router->resource('basic/school','Basic\SchoolController');
    $router->resource('basic/student','Basic\StudentController');
    $router->resource('basic/department','Basic\DepartmentController');

    //系统配置
    $router->resource('system/dictionary','System\DictionaryController');
});
