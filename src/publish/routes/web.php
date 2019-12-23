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

use Illuminate\Support\Facades\Route;

Route::get('login', '\Lubart\Just\Controllers\Auth\LoginController@showLoginForm')->name('login')->middleware('web');
Route::post('login', '\Lubart\Just\Controllers\Auth\LoginController@login')->middleware('web');
Route::post('logout', '\Lubart\Just\Controllers\Auth\LoginController@logout')->name('logout')->middleware(['web','auth']);

Route::get('password/reset', '\Lubart\Just\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request')->middleware('web');
Route::post('password/email', '\Lubart\Just\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email')->middleware('web');
Route::get('password/reset/{token}', '\Lubart\Just\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset')->middleware('web');
Route::post('password/reset', '\Lubart\Just\Controllers\Auth\ResetPasswordController@reset')->middleware('web');

if (Schema::hasTable('routes')){
    $routes = \Lubart\Just\Models\System\Route::all();

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

Route::prefix('settings')->middleware(['web', 'auth'])->group(function(){

    Route::get("", "\Lubart\Just\Controllers\Settings\LayoutController@settingsHome");

    Route::prefix('page')->group(function(){
        Route::get("{pageId}", "\Lubart\Just\Controllers\Settings\PageController@settingsForm")->where(['pageId'=>'\d+']);
        Route::get("list", "\Lubart\Just\Controllers\Settings\PageController@pageList");
        Route::post("setup", "\Lubart\Just\Controllers\Settings\PageController@setup");
        Route::post("delete", "\Lubart\Just\Controllers\Settings\PageController@delete");
    });

    Route::get('noaccess', 'SettingsController@noAccessView')->name('noaccess');

    Route::prefix('layout')->middleware(['master'])->group(function(){
        Route::get("{layoutId}", "\Lubart\Just\Controllers\Settings\LayoutController@settingsForm")->where(['layoutId'=>'\d+']);
        Route::get("list", "\Lubart\Just\Controllers\Settings\LayoutController@layoutList");
        Route::get("default", "\Lubart\Just\Controllers\Settings\LayoutController@defaultLayout");

        Route::post("setup", "\Lubart\Just\Controllers\Settings\LayoutController@setup");
        Route::post("setdefault", "\Lubart\Just\Controllers\Settings\LayoutController@setDefault");
        Route::post("delete", "\Lubart\Just\Controllers\Settings\LayoutController@delete");
    });
});

Route::prefix('admin')->middleware(['web', 'auth'])->group(function(){

    Route::prefix('settings')->group(function(){
        Route::get("{blockId}/{id}/{subid?}", "\Lubart\Just\Controllers\AdminController@settingsForm")->where(['blockId'=>'\d+', 'id'=>'\d+', 'subId'=>'\d+']);
        Route::post("block/setup", "\Lubart\Just\Controllers\AdminController@handleBlockForm");

        Route::get("panel/{pageId}/{panelLocation}/{blockId?}", "\Lubart\Just\Controllers\AdminController@panelSettingsForm")->where(['pageId'=>'\d+', 'blockId'=>'\d+']);
        Route::post("panel/setup", "\Lubart\Just\Controllers\AdminController@handlePanelForm");

        Route::get("addon/list", "\Lubart\Just\Controllers\AdminController@addonList");
        Route::get("addon/{addonId}", "\Lubart\Just\Controllers\AdminController@addonSettingsForm")->where(['addonId'=>'\d+']);
        Route::post("addon/setup", "\Lubart\Just\Controllers\AdminController@handleAddonForm");

        Route::get("category/list", "\Lubart\Just\Controllers\AdminController@categoryList");
        Route::get("category/{categoryId}", "\Lubart\Just\Controllers\AdminController@categorySettingsForm")->where(['categoryId'=>'\d+']);
        Route::post("category/setup", "\Lubart\Just\Controllers\AdminController@handleCategoryForm");

        Route::get("user/list", "\Lubart\Just\Controllers\AdminController@userList");
        Route::get("user/{userId}", "\Lubart\Just\Controllers\AdminController@userSettingsForm")->where(['userId'=>'\d+']);
        Route::post("user/setup", "\Lubart\Just\Controllers\AdminController@handleUserForm");

        Route::get("crop/{blockId}/{id}", "\Lubart\Just\Controllers\AdminController@cropForm")->where(['blockId'=>'\d+', 'id'=>'\d+']);
        Route::post("crop", "\Lubart\Just\Controllers\AdminController@handleCrop");

        Route::get("normalize/{blockId}", "\Lubart\Just\Controllers\AdminController@normalizeContent")->where(['blockId'=>'\d+']);

        Route::post("setup", "\Lubart\Just\Controllers\AdminController@handleSetup");

        Route::post("relations/create", "\Lubart\Just\Controllers\AdminController@createRelation");

        Route::get("password", "\Lubart\Just\Controllers\AdminController@changePasswordForm");
        Route::post("password/update", "\Lubart\Just\Controllers\AdminController@changePassword");

        Route::get("lang/list", "AdminController@languageList");
    });


    Route::post("addon/delete", "\Lubart\Just\Controllers\AdminController@deleteAddon");
    Route::post("user/delete", "\Lubart\Just\Controllers\AdminController@deleteUser");
    Route::post("category/delete", "\Lubart\Just\Controllers\AdminController@deleteCategory");

    Route::post("delete", "\Lubart\Just\Controllers\AdminController@delete");
    Route::post("moveup", "\Lubart\Just\Controllers\AdminController@moveup");
    Route::post("movedown", "\Lubart\Just\Controllers\AdminController@movedown");
    Route::post("moveto", "\Lubart\Just\Controllers\AdminController@moveto");
    Route::post("activate", "\Lubart\Just\Controllers\AdminController@activate");
    Route::post("deactivate", "\Lubart\Just\Controllers\AdminController@deactivate");
    Route::post("browseimages", "\Lubart\Just\Controllers\AdminController@browseimages");
    Route::post("ajaxuploader", "\Lubart\Just\Controllers\AdminController@ajaxuploader");

    Route::get("browseimages", "\Lubart\Just\Controllers\AdminController@browseImages");
    Route::post("uploadimage", "\Lubart\Just\Controllers\AdminController@uploadImage");

});