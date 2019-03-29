<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Auth::routes();

Route::get('login', '\Lubart\Just\Controllers\Auth\LoginController@showLoginForm')->name('login')->middleware('web');
Route::post('login', '\Lubart\Just\Controllers\Auth\LoginController@login')->middleware('web');
Route::post('logout', '\Lubart\Just\Controllers\Auth\LoginController@logout')->name('logout')->middleware(['web','auth']);

Route::get('password/reset', '\Lubart\Just\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request')->middleware('web');
Route::post('password/email', '\Lubart\Just\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email')->middleware('web');
Route::get('password/reset/{token}', '\Lubart\Just\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset')->middleware('web');
Route::post('password/reset', '\Lubart\Just\Controllers\Auth\ResetPasswordController@reset')->middleware('web');

if (Schema::hasTable('routes')){
    $routes = \Lubart\Just\Models\Route::all();

    foreach($routes as $route){
        if($route->type == "page"){
            Route::get($route->route, "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
            Route::get("admin/".$route->route, "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
            Route::post ($route->route, "\Lubart\Just\Controllers\AdminController@handleForm")->middleware(['web','auth']);
        }
        
        if($route->type == 'ajax'){
            Route::post($route->route, "\Lubart\Just\Controllers\JustController@ajax")->middleware('web');
        }
        
        if($route->type == 'post'){
            Route::post($route->route, "\Lubart\Just\Controllers\JustController@post")->middleware('web');
        }
    }
}

Route::get("admin/settings/{blockId}/{id}/{subid?}", "\Lubart\Just\Controllers\AdminController@settingsForm")->where(['blockId'=>'\d+', 'id'=>'\d+', 'subId'=>'\d+'])->middleware(['web','auth']);
Route::get("admin/settings/panel/{pageId}/{panelLocation}/{blockId?}", "\Lubart\Just\Controllers\AdminController@panelSettingsForm")->where(['pageId'=>'\d+', 'panelLocation'=>'[a-z]+', 'blockId'=>'\d+'])->middleware(['web','auth']);
Route::get("admin/settings/page/{pageId}", "\Lubart\Just\Controllers\AdminController@pageSettingsForm")->where(['pageId'=>'\d+'])->middleware(['web','auth']);
Route::get("admin/settings/page/list", "\Lubart\Just\Controllers\AdminController@pageList")->middleware(['web','auth']);
Route::get("admin/settings/layout/{layoutId}", "\Lubart\Just\Controllers\AdminController@layoutSettingsForm")->where(['layoutId'=>'\d+'])->middleware(['web','auth']);
Route::get("admin/settings/layout/list", "\Lubart\Just\Controllers\AdminController@layoutList")->middleware(['web','auth']);
Route::get("admin/settings/addon/list", "\Lubart\Just\Controllers\AdminController@addonList")->middleware(['web','auth']);
Route::get("admin/settings/addon/{addonId}", "\Lubart\Just\Controllers\AdminController@addonSettingsForm")->where(['addonId'=>'\d+'])->middleware(['web','auth']);
Route::get("admin/settings/user/list", "\Lubart\Just\Controllers\AdminController@userList")->middleware(['web','auth']);
Route::get("admin/settings/user/{userId}", "\Lubart\Just\Controllers\AdminController@userSettingsForm")->where(['userId'=>'\d+'])->middleware(['web','auth']);
Route::get("admin/settings/category/list", "\Lubart\Just\Controllers\AdminController@categoryList")->middleware(['web','auth']);
Route::get("admin/settings/category/{categoryId}", "\Lubart\Just\Controllers\AdminController@categorySettingsForm")->where(['categoryId'=>'\d+'])->middleware(['web','auth']);
Route::post("admin/page/delete", "\Lubart\Just\Controllers\AdminController@deletePage")->middleware(['web','auth']);
Route::post("admin/addon/delete", "\Lubart\Just\Controllers\AdminController@deleteAddon")->middleware(['web','auth']);
Route::post("admin/user/delete", "\Lubart\Just\Controllers\AdminController@deleteUser")->middleware(['web','auth']);
Route::post("admin/category/delete", "\Lubart\Just\Controllers\AdminController@deleteCategory")->middleware(['web','auth']);
Route::post("admin/layout/delete", "\Lubart\Just\Controllers\AdminController@deleteLayout")->middleware(['web','auth']);
Route::get("admin/settings/crop/{blockId}/{id}", "\Lubart\Just\Controllers\AdminController@cropForm")->where(['blockId'=>'\d+', 'id'=>'\d+'])->middleware(['web','auth']);
Route::get("admin/settings/normalize/{blockId}", "\Lubart\Just\Controllers\AdminController@normalizeContent")->where(['blockId'=>'\d+'])->middleware(['web','auth']);
Route::post("admin/settings/crop", "\Lubart\Just\Controllers\AdminController@handleCrop")->middleware(['web','auth']);
Route::post("admin/settings/setup", "\Lubart\Just\Controllers\AdminController@handleSetup")->middleware(['web','auth']);
Route::post("admin/delete", "\Lubart\Just\Controllers\AdminController@delete")->middleware(['web','auth']);
Route::post("admin/moveup", "\Lubart\Just\Controllers\AdminController@moveup")->middleware(['web','auth']);
Route::post("admin/movedown", "\Lubart\Just\Controllers\AdminController@movedown")->middleware(['web','auth']);
Route::post("admin/moveto", "\Lubart\Just\Controllers\AdminController@moveto")->middleware(['web','auth']);
Route::post("admin/activate", "\Lubart\Just\Controllers\AdminController@activate")->middleware(['web','auth']);
Route::post("admin/deactivate", "\Lubart\Just\Controllers\AdminController@deactivate")->middleware(['web','auth']);
Route::post("admin/browseimages", "\Lubart\Just\Controllers\AdminController@browseimages")->middleware(['web','auth']);
Route::post("admin/ajaxuploader", "\Lubart\Just\Controllers\AdminController@ajaxuploader")->middleware(['web','auth']);
Route::post("admin/settings/panel/setup", "\Lubart\Just\Controllers\AdminController@handlePanelForm")->middleware(['web','auth']);
Route::post("admin/settings/page/setup", "\Lubart\Just\Controllers\AdminController@handlePageForm")->middleware(['web','auth']);
Route::post("admin/settings/layout/setup", "\Lubart\Just\Controllers\AdminController@handleLayoutForm")->middleware(['web','auth']);
Route::post("admin/settings/addon/setup", "\Lubart\Just\Controllers\AdminController@handleAddonForm")->middleware(['web','auth']);
Route::post("admin/settings/user/setup", "\Lubart\Just\Controllers\AdminController@handleUserForm")->middleware(['web','auth']);
Route::post("admin/settings/category/setup", "\Lubart\Just\Controllers\AdminController@handleCategoryForm")->middleware(['web','auth']);
Route::get("admin/browseimages", "\Lubart\Just\Controllers\AdminController@browseImages")->middleware(['web','auth']);
Route::post("admin/uploadimage", "\Lubart\Just\Controllers\AdminController@uploadImage")->middleware(['web','auth']);
Route::post("admin/settings/relations/create", "\Lubart\Just\Controllers\AdminController@createRelation")->middleware(['web','auth']);
Route::get("admin/settings/password", "\Lubart\Just\Controllers\AdminController@changePasswordForm")->middleware(['web','auth']);
Route::post("admin/settings/password/update", "\Lubart\Just\Controllers\AdminController@changePassword")->middleware(['web','auth']);
Route::get("admin/settings/layout/default", "\Lubart\Just\Controllers\AdminController@defaultLayout")->middleware(['web','auth']);
Route::post("admin/settings/layout/default/set", "\Lubart\Just\Controllers\AdminController@setDefaultLayout")->middleware(['web','auth']);