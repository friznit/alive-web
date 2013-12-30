<?php

if(Config::get('app.debug')){
    Log::info("Route ---------------------------------------------");
    query_debug();
}

Route::controller('api', 'APIController');
Route::controller('user', 'UserController');
Route::controller('war-room', 'WarRoomController');
Route::controller('admin/ao', 'AdminAOController');
Route::controller('admin/application', 'AdminApplicationController');
Route::controller('admin/clan', 'AdminClanController');
Route::controller('admin/server', 'AdminServerController');
Route::controller('admin/user', 'AdminUserController');
Route::resource('admin/group', 'AdminGroupController');

Route::get('/', function() {
    return View::make('public/home/index');
});

