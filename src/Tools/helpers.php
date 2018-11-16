<?php

if(!function_exists('viewPath')){
    /**
     * Return path to the view
     * 
     * @param \Lubart\Just\Structure\Layout $layout
     * @param string|\Lubart\Just\Structure\Panel|Lubart\Just\Structure\Panel\Block $path
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
            case $path instanceof \Lubart\Just\Structure\Panel:
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
            case $path instanceof Lubart\Just\Structure\Panel\Block:
                if ($layout->class != "primary" and
                        file_exists(resource_path('views/' . $layout->name . '/blocks/' . $path->name . '_' . $layout->class . '.blade.php'))){
                    return $layout->name.'.blocks.'. $path->name . '_' . $layout->class;
                }
                elseif(($path->layoutClass != "primary" or $path->layoutClass != "") and
                        file_exists(resource_path('views/'.$layout->name.'/blocks/'.$path->name.'_'.$path->layoutClass.'.blade.php'))){
                    return $layout->name.'.blocks.'. $path->name . '_' . $path->layoutClass;
                }
                elseif(($path->layoutClass != "primary" or $path->layoutClass != "") and
                        !file_exists(resource_path('views/'.$layout->name.'/blocks/'.$path->name.'_'.$path->layoutClass.'.blade.php'))){
                    return $layout->name.'.blocks.'. $path->name;
                }
                elseif( ($path->layoutClass === "primary" or $path->layoutClass === "") and 
                        !file_exists(resource_path('views/'.$layout->name.'/blocks/'.$path->name.'.blade.php')) ){
                    return $layout->name.'.blocks.'. $path->name;
                }
                elseif($layout->name != 'Just'){
                    return viewPath(justLayout(), $path);
                }
                else{
                    return new \Exception("Resource \"Just.blocks.".$path->name."\" does not exists.");
                }
                break;
        }
    }
    
    /**
     * Return primary Just! layout
     * 
     * @return \Lubart\Just\Structure\Layout
     */
    function justLayout(){
        return \Lubart\Just\Structure\Layout::where('name', 'Just')
                        ->where('class', 'primary')
                        ->first();
    }
}
