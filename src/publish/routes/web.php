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

Route::get('login', '\Just\Controllers\Auth\LoginController@showLoginForm')->name('login')->middleware('web');
Route::post('login', '\Just\Controllers\Auth\LoginController@login')->middleware('web');
Route::post('logout', '\Just\Controllers\Auth\LoginController@logout')->name('logout')->middleware(['web','auth']);

Route::get('password/reset', '\Just\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request')->middleware('web');
Route::post('password/email', '\Just\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email')->middleware('web');
Route::get('password/reset/{token}', '\Just\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset')->middleware('web');
Route::post('password/reset', '\Just\Controllers\Auth\ResetPasswordController@reset')->middleware('web');

if (Schema::hasTable('routes')){
    $routes = \Just\Models\System\Route::all();

    foreach($routes as $route){
        if($route->type == "page"){
            Route::get($route->route, "\Just\Controllers\JustController@buildPage")->middleware('web');
            Route::get("admin/".$route->route, "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
            Route::post ($route->route, "\Just\Controllers\AdminController@handleForm")->middleware(['web','auth']);
        }
        
        if($route->type == 'ajax'){
            Route::post($route->route, "\Just\Controllers\JustController@ajax")->middleware('web');
        }
        
        if($route->type == 'post'){
            Route::post($route->route, "\Just\Controllers\JustController@post")->middleware('web');
        }
    }
}

Route::prefix('settings')->middleware(['web', 'auth'])->group(function(){

    Route::get("", "\Just\Controllers\Settings\LayoutController@settingsHome");
    Route::get('noaccess', 'SettingsController@noAccessView')->name('noaccess');

    Route::prefix('layout')->middleware(['master'])->group(function(){
        Route::get('', "\Just\Controllers\Settings\LayoutController@actions");
        Route::get("{layoutId}", "\Just\Controllers\Settings\LayoutController@settingsForm")->where(['layoutId'=>'\d+']);
        Route::get("list", "\Just\Controllers\Settings\LayoutController@layoutList");

        Route::post("setup", "\Just\Controllers\Settings\LayoutController@setup");
        Route::post("setdefault", "\Just\Controllers\Settings\LayoutController@setDefault");
        Route::post("delete", "\Just\Controllers\Settings\LayoutController@delete");
    });

    Route::prefix('page')->group(function(){
        Route::get('', "\Just\Controllers\Settings\PageController@actions");
        Route::get("{pageId}", "\Just\Controllers\Settings\PageController@settingsForm")->where(['pageId'=>'\d+']);
        Route::get("list", "\Just\Controllers\Settings\PageController@pageList");

        Route::post("setup", "\Just\Controllers\Settings\PageController@setup");
        Route::post("delete", "\Just\Controllers\Settings\PageController@delete");
    });

    Route::prefix('add-on')->group(function(){
        Route::middleware(['master'])->group(function(){
            Route::get('', "\Just\Controllers\Settings\AddOnController@actions");
            Route::get("{addOnId}", "\Just\Controllers\Settings\AddOnController@settingsForm")->where(['addOnId'=>'\d+']);
            Route::get("list", "\Just\Controllers\Settings\AddOnController@addOnList");

            Route::post("setup", "\Just\Controllers\Settings\AddOnController@setup");
            Route::post("delete", "\Just\Controllers\Settings\AddOnController@delete");
        });
    });
});

Route::prefix('admin')->middleware(['web', 'auth'])->group(function(){

    Route::prefix('settings')->group(function(){
        Route::get("{blockId}/{id}/{subid?}", "\Just\Controllers\AdminController@settingsForm")->where(['blockId'=>'\d+', 'id'=>'\d+', 'subId'=>'\d+']);
        Route::post("block/setup", "\Just\Controllers\AdminController@handleBlockForm");

        Route::get("panel/{pageId}/{panelLocation}/{blockId?}", "\Just\Controllers\AdminController@panelSettingsForm")->where(['pageId'=>'\d+', 'blockId'=>'\d+']);
        Route::post("panel/setup", "\Just\Controllers\AdminController@handlePanelForm");

        Route::get("addon/list", "\Just\Controllers\AdminController@addonList");
        Route::get("addon/{addonId}", "\Just\Controllers\AdminController@addonSettingsForm")->where(['addonId'=>'\d+']);
        Route::post("addon/setup", "\Just\Controllers\AdminController@handleAddonForm");

        Route::get("category/list", "\Just\Controllers\AdminController@categoryList");
        Route::get("category/{categoryId}", "\Just\Controllers\AdminController@categorySettingsForm")->where(['categoryId'=>'\d+']);
        Route::post("category/setup", "\Just\Controllers\AdminController@handleCategoryForm");

        Route::get("user/list", "\Just\Controllers\AdminController@userList");
        Route::get("user/{userId}", "\Just\Controllers\AdminController@userSettingsForm")->where(['userId'=>'\d+']);
        Route::post("user/setup", "\Just\Controllers\AdminController@handleUserForm");

        Route::get("crop/{blockId}/{id}", "\Just\Controllers\AdminController@cropForm")->where(['blockId'=>'\d+', 'id'=>'\d+']);
        Route::post("crop", "\Just\Controllers\AdminController@handleCrop");

        Route::get("normalize/{blockId}", "\Just\Controllers\AdminController@normalizeContent")->where(['blockId'=>'\d+']);

        Route::post("setup", "\Just\Controllers\AdminController@handleSetup");

        Route::post("relations/create", "\Just\Controllers\AdminController@createRelation");

        Route::get("password", "\Just\Controllers\AdminController@changePasswordForm");
        Route::post("password/update", "\Just\Controllers\AdminController@changePassword");

        Route::get("lang/list", "AdminController@languageList");
    });


    Route::post("addon/delete", "\Just\Controllers\AdminController@deleteAddon");
    Route::post("user/delete", "\Just\Controllers\AdminController@deleteUser");
    Route::post("category/delete", "\Just\Controllers\AdminController@deleteCategory");

    Route::post("delete", "\Just\Controllers\AdminController@delete");
    Route::post("moveup", "\Just\Controllers\AdminController@moveup");
    Route::post("movedown", "\Just\Controllers\AdminController@movedown");
    Route::post("moveto", "\Just\Controllers\AdminController@moveto");
    Route::post("activate", "\Just\Controllers\AdminController@activate");
    Route::post("deactivate", "\Just\Controllers\AdminController@deactivate");
    Route::post("browseimages", "\Just\Controllers\AdminController@browseimages");
    Route::post("ajaxuploader", "\Just\Controllers\AdminController@ajaxuploader");

    Route::get("browseimages", "\Just\Controllers\AdminController@browseImages");
    Route::post("uploadimage", "\Just\Controllers\AdminController@uploadImage");

});