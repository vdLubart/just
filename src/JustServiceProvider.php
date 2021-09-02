<?php

namespace Just;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Just\Middleware\IsActiveUser;
use Just\Middleware\MasterAccess;
use Just\Validators\ValidatorExtended;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class JustServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/publish/routes/console.php';
        include __DIR__.'/Tools/helpers.php';

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        $this->publishes([
            __DIR__.'/publish/resources/views/Just' => base_path('resources/views/Just'),
            __DIR__.'/publish/resources/lang' => base_path('resources/lang'),
            __DIR__.'/publish/config' => base_path('config'),
            __DIR__.'/publish/public/css' => base_path('public/css'),
            __DIR__.'/publish/public/fonts' => base_path('public/fonts'),
            __DIR__.'/publish/public/js' => base_path('public/js'),
            __DIR__.'/publish/routes/api.php' => base_path('routes/api.php'),
            __DIR__.'/publish/routes/channels.php' => base_path('routes/channels.php'),
            __DIR__.'/publish/routes/console_empty.php' => base_path('routes/console.php'),
            __DIR__.'/publish/routes/web_empty.php' => base_path('routes/web.php'),
            __DIR__.'/publish/app/Providers/RouteServiceProvider.php' => base_path('app/Providers/RouteServiceProvider.php'),
        ], 'just');

        $this->publishes([
            __DIR__.'/publish/resources/views/Just' => base_path('resources/views/Just'),
            __DIR__.'/publish/resources/lang' => base_path('resources/lang'),
        ], 'just-resources');

        $this->publishes([
            __DIR__.'/publish/config' => base_path('config'),
        ], 'just-config');

        $this->publishes([
            __DIR__.'/publish/public/css' => base_path('public/css'),
            __DIR__.'/publish/public/fonts' => base_path('public/fonts'),
            __DIR__.'/publish/public/js' => base_path('public/js'),
        ], 'just-public');

        $this->publishes([
            __DIR__.'/publish/routes/api.php' => base_path('routes/api.php'),
            __DIR__.'/publish/routes/channels.php' => base_path('routes/channels.php'),
            __DIR__.'/publish/routes/console.php' => base_path('routes/console.php'),
            __DIR__.'/publish/routes/web.php' => base_path('routes/web.php'),
        ], 'just-routes');

        $this->publishes([
            __DIR__.'/publish/app/Providers/RouteServiceProvider.php' => base_path('app/Providers/RouteServiceProvider.php'),
        ], 'just-app');

        Validator::extend(
            'recaptcha',
            'Just\\Validators\\Recaptcha@validate'
        );

        $this->app->validator->resolver(function($translator, $data, $rules, $messages){
            return new ValidatorExtended($translator, $data, $rules, $messages);
        });

        if (Schema::hasTable('themes')) {
            Config::set('mail.markdown.paths', [resource_path('views/'.(Models\Theme::active()->name ?? 'Just').'/emails/mail')]);
        }

        $this->app['router']->aliasMiddleware('master', MasterAccess::class);
        $this->app['router']->aliasMiddleware('isActiveUser', IsActiveUser::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
