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
    $justRoutes = function() {
        $routes = \Just\Models\System\Route::all();

        foreach($routes as $route){
            if($route->type == "page"){
                if(!empty($route->page) and !!$route->page->isActive) {
                    Route::get($route->route, "\Just\Controllers\JustController@buildPage")->middleware('web');
                }

                Route::get("admin/".$route->route, "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
            }

            if($route->type == 'ajax'){
                Route::post($route->route, "\Just\Controllers\JustController@ajax")->middleware('web');
            }

            if($route->type == 'post'){
                Route::post($route->route, "\Just\Controllers\JustController@post")->middleware('web');
            }
        }
    };

    Route::middleware(['web', \Just\Middleware\SetDefaultLocale::class])
        ->group($justRoutes);

    Route::prefix('{locale}')
        ->where(['locale'=>'[a-z]{2}'])
        ->middleware(['web', \Just\Middleware\SetLocale::class])
        ->group($justRoutes);
}

Route::prefix('settings')->middleware(['web', 'auth', \Just\Middleware\CatchLocale::class])->group(function(){

    Route::get("", "\Just\Controllers\Settings\LayoutController@settingsHome");

    Route::prefix('layout')->middleware(['master'])->group(function(){
        Route::get('', "\Just\Controllers\Settings\LayoutController@actions");
        Route::get("{layoutId}", "\Just\Controllers\Settings\LayoutController@settingsForm")->where(['layoutId'=>'\d+']);
        Route::get("list", "\Just\Controllers\Settings\LayoutController@layoutList");

        Route::post("setup", "\Just\Controllers\Settings\LayoutController@setup");
        //TODO: create functionality
        //Route::post("setdefault", "\Just\Controllers\Settings\LayoutController@setDefault");
        Route::post("delete", "\Just\Controllers\Settings\LayoutController@delete");
    });

    Route::prefix('page')->group(function(){
        Route::get('', "\Just\Controllers\Settings\PageController@actions");
        Route::get("{pageId}", "\Just\Controllers\Settings\PageController@settingsForm")->where(['pageId'=>'\d+']);
        Route::get("list", "\Just\Controllers\Settings\PageController@pageList");

        Route::post("setup", "\Just\Controllers\Settings\PageController@setup");
        Route::post("delete", "\Just\Controllers\Settings\PageController@delete");
        Route::post('activate', '\Just\Controllers\Settings\PageController@activate');
        Route::post('deactivate', '\Just\Controllers\Settings\PageController@deactivate');

        Route::prefix('{pageId}/panel/{panelLocation}')->where(['pageId'=>'\d+', 'panelLocation'=>'[a-zA-Z]+'])->group(function(){
            Route::get('', "\Just\Controllers\Settings\BlockController@panelActions");
            Route::prefix('block')->group(function(){
                Route::get('create', "\Just\Controllers\Settings\BlockController@createForm")->where(['blockId'=>'\d+']);
                Route::get('list', "\Just\Controllers\Settings\BlockController@blockList");
            });
        });
    });

    Route::prefix('user')->group(function(){
        Route::middleware(['master'])->group(function(){
            Route::get('', "\Just\Controllers\Settings\UserController@actions");
            Route::get("{userId}", "\Just\Controllers\Settings\UserController@settingsForm")->where(['userId'=>'\d+']);
            Route::get("list", "\Just\Controllers\Settings\UserController@userList");

            Route::post("setup", "\Just\Controllers\Settings\UserController@setup");
            Route::post('activate', '\Just\Controllers\Settings\UserController@activate');
            Route::post('deactivate', '\Just\Controllers\Settings\UserController@deactivate');
            Route::post("delete", "\Just\Controllers\Settings\UserController@delete");
        });

        Route::get("password", "\Just\Controllers\Settings\UserController@changePasswordForm");
        Route::post("password/update", "\Just\Controllers\Settings\UserController@changePassword");
    });

    Route::prefix('add-on')->group(function(){
        Route::middleware(['master'])->group(function(){
            Route::get('', "\Just\Controllers\Settings\AddOnController@actions");
            Route::get("{addOnId}", "\Just\Controllers\Settings\AddOnController@settingsForm")->where(['addOnId'=>'\d+']);
            Route::get("list", "\Just\Controllers\Settings\AddOnController@addOnList");

            Route::post("setup", "\Just\Controllers\Settings\AddOnController@setup");
            Route::post('moveup', '\Just\Controllers\Settings\AddOnController@moveUp');
            Route::post('movedown', '\Just\Controllers\Settings\AddOnController@moveDown');
            Route::post('activate', '\Just\Controllers\Settings\AddOnController@activate');
            Route::post('deactivate', '\Just\Controllers\Settings\AddOnController@deactivate');
            Route::post("delete", "\Just\Controllers\Settings\AddOnController@delete");
        });
    });

    Route::prefix('add-on-option')->group(function(){
        Route::get('', "\Just\Controllers\Settings\AddOnOptionController@actions"); //+
        Route::get("{addOnId}", "\Just\Controllers\Settings\AddOnOptionController@optionList")->where(['addOnId'=>'\d+']); //+
        Route::get("{addOnType}/list", "\Just\Controllers\Settings\AddOnOptionController@list")->where(['addonType' => '(category|tag)']); //+

        Route::prefix('{addOnType}/option')->where(['addonType' => '(category|tag)'])->group(function(){
            Route::get("{optionId}", "\Just\Controllers\Settings\AddOnOptionController@optionForm")->where(['optionId' => '\d+']); //+

            Route::post("setup", "\Just\Controllers\Settings\AddOnOptionController@setup"); //+
            Route::post('moveup', '\Just\Controllers\Settings\AddOnOptionController@moveUp'); //+
            Route::post('movedown', '\Just\Controllers\Settings\AddOnOptionController@moveDown'); //+
            Route::post('activate', '\Just\Controllers\Settings\AddOnOptionController@activate'); //+
            Route::post('deactivate', '\Just\Controllers\Settings\AddOnOptionController@deactivate'); //+
            Route::post("delete", "\Just\Controllers\Settings\AddOnOptionController@delete"); //+
        });
    });

    Route::prefix('block')->group(function(){
        Route::prefix('{blockId}')->where(['blockId'=>'\d+'])->group(function(){
            Route::get('', "\Just\Controllers\Settings\BlockController@content");
            Route::get('settings', "\Just\Controllers\Settings\BlockController@settingsForm");
            Route::get('customization', "\Just\Controllers\Settings\BlockController@customizationForm");
            Route::get('item/{itemId}', "\Just\Controllers\Settings\BlockController@itemSettingsForm")->where(['itemId'=>'\d+']);
            Route::get('item/{itemId}/cropping', "\Just\Controllers\Settings\BlockController@itemCroppingForm")->where(['itemId'=>'\d+']);
        });

        Route::post('setup', '\Just\Controllers\Settings\BlockController@setup');
        Route::post('customize', '\Just\Controllers\Settings\BlockController@customize');
        Route::post('moveup', '\Just\Controllers\Settings\BlockController@moveUp');
        Route::post('movedown', '\Just\Controllers\Settings\BlockController@moveDown');
        Route::post('activate', '\Just\Controllers\Settings\BlockController@activate');
        Route::post('deactivate', '\Just\Controllers\Settings\BlockController@deactivate');
        Route::post('delete', '\Just\Controllers\Settings\BlockController@delete');

        Route::prefix('item')->group(function(){
            Route::post('save', '\Just\Controllers\Settings\BlockController@saveItem');
            Route::post('crop', '\Just\Controllers\Settings\BlockController@cropItem');
            Route::post('moveup', '\Just\Controllers\Settings\BlockController@itemMoveUp');
            Route::post('movedown', '\Just\Controllers\Settings\BlockController@itemMoveDown');
            Route::post('activate', '\Just\Controllers\Settings\BlockController@itemActivate');
            Route::post('deactivate', '\Just\Controllers\Settings\BlockController@itemDeactivate');
            Route::post('delete', '\Just\Controllers\Settings\BlockController@itemDelete');
        });
    });

    Route::post('upload-image', "\Just\Controllers\AdminController@uploadImage");
});

Route::prefix('admin')->middleware(['web', 'auth'])->group(function(){

    Route::prefix('settings')->group(function(){
        Route::get("{blockId}/{id}/{subid?}", "\Just\Controllers\AdminController@settingsForm")->where(['blockId'=>'\d+', 'id'=>'\d+', 'subId'=>'\d+']);

        Route::get("panel/{pageId}/{panelLocation}/{blockId?}", "\Just\Controllers\AdminController@panelSettingsForm")->where(['pageId'=>'\d+', 'blockId'=>'\d+']);
        Route::post("panel/setup", "\Just\Controllers\AdminController@handlePanelForm");

        Route::get("crop/{blockId}/{id}", "\Just\Controllers\AdminController@cropForm")->where(['blockId'=>'\d+', 'id'=>'\d+']);
        Route::post("crop", "\Just\Controllers\AdminController@handleCrop");
        //TODO: check functionality
        //Route::get("normalize/{blockId}", "\Just\Controllers\AdminController@normalizeContent")->where(['blockId'=>'\d+']);

        Route::post("setup", "\Just\Controllers\AdminController@handleSetup");

        Route::post("relations/create", "\Just\Controllers\AdminController@createRelation");

        Route::get("password", "\Just\Controllers\AdminController@changePasswordForm");
        Route::post("password/update", "\Just\Controllers\AdminController@changePassword");
        //TODO: create functionality
        //Route::get("lang/list", "AdminController@languageList");
    });
});
