<?php

if(Config::get('app.debug')){
    Log::info("Route ---------------------------------------------");
    query_debug();
}

Route::controller('api', 'APIController');
Route::controller('user', 'UserController');
Route::controller('loading', 'LoadingController');
Route::controller('war-room/load', 'WarRoomController');
Route::controller('war-room', 'WarRoomController');

Route::get('/admin', function() {
    $data = get_default_data();
    return View::make('admin/home/index')->with($data);;
});

Route::controller('admin/ao', 'AdminAOController');
Route::controller('admin/application', 'AdminApplicationController');
Route::controller('admin/clan', 'AdminClanController');
Route::controller('admin/server', 'AdminServerController');
Route::controller('admin/user', 'AdminUserController');
Route::resource('admin/group', 'AdminGroupController');

Route::get('/war-room/load', 'WarRoomController@getIndex');

Route::get('/missions', function() {
    return View::make('public/missions/index');
});

Route::get('/unsubscribe', function() {
    return View::make('public/unsubscribe/index');
});

Route::get('/thankyou', function() {
    return View::make('public/thankyou/index');
});

Route::get('/staginghome', function() {
    return View::make('public/staginghome/index');
});

Route::get('/', function() {
    return View::make('public/home/index');
});



