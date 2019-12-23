<?php

if(!function_exists('viewPath')){
    /**
     * Return path to the view
     * 
     * @param \Just\Models\Layout $layout
     * @param string|\Just\Structure\Panel|Just\Structure\Panel\Block $path
     */
    function viewPath($layout, $path){
        switch(true){
            case is_string($path):
                if(file_exists(resource_path('views/'.$layout->name.'/'.str_replace('.', '/', $path).'.blade.php'))){
                    return $layout->name . '.' . $path;
                }
                elseif($layout->name != 'Just'){
                    return viewPath(justLayout(), $path);
                }
                else{
                    return new \Exception("Resource \"Just.".$path."\" does not exists.");
                }
                break;
            case $path instanceof \Just\Structure\Panel:
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
                    return new \Exception("Resource \"Just.panels.".$path->location."\" does not exists.");
                }
                break;
            case $path instanceof Just\Structure\Panel\Block:
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
                    return new \Exception("Resource \"Just.blocks.".$path->type."\" does not exists.");
                }
                break;
        }
    }
}

if(!function_exists('justLayout')){
    
    /**
     * Return primary Just! layout
     * 
     * @return \Just\Models\Layout
     */
    function justLayout(){
        return \Just\Models\Layout::where('name', 'Just')
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
    function justVersion(){
        $packages = json_decode(file_get_contents(base_path('vendor/composer/installed.json')));
        
        foreach($packages as $package){
            if($package->name == "lubart/just"){
                return $package->version_normalized;
            }
        }
    }
}
