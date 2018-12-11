<?php

namespace Lubart\Just;

use Illuminate\Support\ServiceProvider;
use Lubart\Just\Validators\ValidatorExtended;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class JustServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/publish/routes/web.php';
        include __DIR__.'/publish/routes/console.php';
        include __DIR__.'/Tools/helpers.php';
        
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        
        $this->publishes([
            __DIR__.'/publish/resources/assets' => base_path('resources/assets'),
            __DIR__.'/publish/resources/views/Just' => base_path('resources/views/Just'),
            __DIR__.'/publish/webpack.mix.js' => base_path('webpack.mix.js'),
            __DIR__.'/publish/public/css' => base_path('public/css'),
            __DIR__.'/publish/public/fonts' => base_path('public/fonts'),
            __DIR__.'/publish/public/js' => base_path('public/js'),
        ], 'just');
        
        $this->publishes([
            __DIR__.'/publish/resources/views/Just' => base_path('resources/views/Just'),
            __DIR__.'/publish/public/css' => base_path('public/css'),
            __DIR__.'/publish/public/fonts' => base_path('public/fonts'),
            __DIR__.'/publish/public/js' => base_path('public/js'),
            __DIR__.'/publish/routes/api.php' => base_path('routes/api.php'),
            __DIR__.'/publish/routes/channels.php' => base_path('routes/channels.php'),
            __DIR__.'/publish/routes/console_empty.php' => base_path('routes/console.php'),
            __DIR__.'/publish/routes/web_empty.php' => base_path('routes/web.php'),
        ], 'just-public');
        
        Validator::extend(
            'recaptcha',
            'Lubart\\Just\\Validators\\Recaptcha@validate'
        );
        
        $this->app->validator->resolver(function($translator, $data, $rules, $messages){
            return new ValidatorExtended($translator, $data, $rules, $messages);
        });
        
        if (Schema::hasTable('themes')) {
            \Config::set('mail.markdown.paths', [resource_path('views/'.(@Models\Theme::active()->name ?? 'Just').'/emails/mail')]);
        }
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
