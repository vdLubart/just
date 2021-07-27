<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Just\Models\Layout;
use Just\Models\Panel;

if(!function_exists('viewPath')){
    /**
     * Return path to the view
     *
     * @param Layout $layout
     * @param string|Panel|Just\Models\Block $path
     * @return string
     * @throws Exception
     */
    function viewPath(Layout $layout, $path): string {
        switch(true){
            case is_string($path):
                if(file_exists(resource_path('views/'.$layout->name.'/'.str_replace('.', '/', $path).'.blade.php'))){
                    return $layout->name . '.' . $path;
                }
                elseif($layout->name != 'Just'){
                    return viewPath(justLayout(), $path);
                }
                else{
                    throw new Exception("Resource \"Just.".$path."\" does not exists.");
                }
                break;
            case $path instanceof Panel:
                if($layout->class != "primary" and
                        file_exists(resource_path('views/'.$layout->name.'/panels/'.$path->location.'_'.$layout->class.'.blade.php'))){
                    return $layout->name.'.panels.'. $path->location . '_' . $layout->class;
                }
                elseif($layout->class != "primary" and
                       !file_exists(resource_path('views/'.$layout->name.'/panels/'.$path->location.'_'.$layout->class.'.blade.php')) and
                        file_exists(resource_path('views/'.$layout->name.'/panels/'.$path->location.'.blade.php')) ){
                    return $layout->name.'.panels.'. $path->location;
                }
                elseif($layout->class === "primary" and
                        file_exists(resource_path('views/'.$layout->name.'/panels/'.$path->location.'.blade.php')) ){
                    return $layout->name.'.panels.'. $path->location;
                }
                elseif($layout->name != 'Just'){
                    return viewPath(justLayout(), $path);
                }
                else{
                    throw new Exception("Resource \"Just.panels.".$path->location."\" does not exists.");
                }
                break;
            case $path instanceof Just\Models\Block:
                if ($layout->class != "primary" and
                        file_exists(resource_path('views/' . $layout->name . '/blocks/' . $path->type . '_' . $layout->class . '.blade.php'))){
                    return $layout->name.'.blocks.'. $path->type . '_' . $layout->class;
                }
                elseif(($path->layoutClass != "primary" and $path->layoutClass != "") and
                        file_exists(resource_path('views/'.$layout->name.'/blocks/'.$path->type.'_'.$path->layoutClass.'.blade.php'))){
                    return $layout->name.'.blocks.'. $path->type . '_' . $path->layoutClass;
                }
                elseif(($path->layoutClass != "primary" and $path->layoutClass != "") and
                        !file_exists(resource_path('views/'.$layout->name.'/blocks/'.$path->type.'_'.$path->layoutClass.'.blade.php'))){
                    return $layout->name.'.blocks.'. $path->type;
                }
                elseif( ($path->layoutClass === "primary" or $path->layoutClass === "") and
                        file_exists(resource_path('views/'.$layout->name.'/blocks/'.$path->type.'.blade.php')) ){
                    return $layout->name.'.blocks.'. $path->type;
                }
                elseif($layout->name != 'Just'){
                    return viewPath(justLayout(), $path);
                }
                else{
                    throw new Exception("Resource \"Just.blocks.".$path->type."\" does not exists.");
                }
                break;
        }
    }
}

if(!function_exists('justLayout')){

    /**
     * Return primary Just! layout
     *
     * @return Layout
     */
    function justLayout(): Layout {
        return Layout::where('name', 'Just')
                        ->where('class', 'primary')
                        ->first();
    }
}

if(!function_exists('justVersion')){

    /**
     * Return installed through composer version of Just!
     *
     * @return string
     */
    function justVersion(): string {
        $packages = json_decode(file_get_contents(base_path('vendor/composer/installed.json')));

        foreach($packages as $package){
            if($package->name == packageName()){
                return $package->version_normalized;
            }
        }
    }
}

if(!function_exists('packageName')){
    /**
     * Return the package name mentioned in composer.json
     *
     * @return string
     */
    function packageName(): string {
        $composer = json_decode(file_get_contents(__DIR__ . '/../../composer.json'));

        return $composer->name;
    }
}

if(!function_exists('rebuildTranslationCache')){
    /**
     * Create new or update cached translation values in case if current locale was changed
     */
    function rebuildTranslationCache(){
        if(!Cache::has('settings-translations') or Cache::get('settings-translations')['locale'] != App::getLocale()) {
            Cache::forget('settings-translations');
            Cache::rememberForever('settings-translations', function () {
                return array_merge(trans('settings'), ['blockTabs' => trans('block.tabs'), 'itemTabs' => trans('block.itemTabs')], ['locale' => App::getLocale()]);
            });
        }
    }
}
