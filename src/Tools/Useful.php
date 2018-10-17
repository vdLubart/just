<?php

namespace Lubart\Just\Tools;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Description of Useful
 *
 * @author lubart
 */
class Useful {
    
    public static function getMaxNo($table, $where = [], $orderColumn = 'orderNo') {
        $query = DB::table($table);
        foreach($where as $key=>$val){
            $query->where($key, $val);
        }
        $res = $query->select([DB::raw("max(`".$orderColumn."`) as maxNo")])
                ->first()
                ;
        
        return !!$res?($res->maxNo+1):1;
    }
    
    public static function normalizeOrder($table, $where = [], $orderColumn = 'orderNo') {
        $query = DB::table($table);
        foreach($where as $key=>$val){
            $query->where($key, $val);
        }
        $res = $query->orderBy($orderColumn, "asc")
                ->orderBy("id", "desc")->get();
        
        foreach($res as $no=>$item){
            if($no+1 != $item->orderNo){
                DB::table($table)
                        ->where('id', $item->id)
                        ->update([$orderColumn=>$no+1]);
            }
        }
        
        return;
    }
    
    public static function isRouteExists($route) {
        $routes = \Route::getRoutes()->getRoutes();
        
        foreach ($routes as $r) {
            if ($r->uri == $route) {
                return true;
            }
        }

        return false;
    }
    
    public static function moveModel($model, $dir, $where) {
        $currentNo = $model->orderNo;
        $modelClass = get_class($model);
        
        if($dir == 'up'){
            if($currentNo > 1){
                $sql = $modelClass::query();
                foreach($where as $col=>$val){
                    $sql->where($col, $val);
                }
                $sql->where('orderNo', $currentNo - 1)
                        ->update(['orderNo'=>$currentNo]);
                
                $model->orderNo = $currentNo - 1;
            }
        }
        else{
            $sql = $modelClass::query();
            foreach ($where as $col => $val) {
                $sql->where($col, $val);
            }
            $sql->where('orderNo', $currentNo + 1)
                    ->update(['orderNo' => $currentNo]);

            $model->orderNo = $currentNo + 1;
        }
        
        $model->save();
        
        self::normalizeOrder($model->getTable(), $where);
        
        return $model;
    }
    
    public static function moveModelTo($model, $newPosition, $where) {
        $currentNo = $model->orderNo;
        $modelClass = get_class($model);
        
        $sql = $modelClass::query();
        foreach($where as $col=>$val){
            $sql->where($col, $val);
        }
        
        if($currentNo < (int)$newPosition){
            $set = $sql->where('orderNo', '>', $currentNo)
                    ->where('orderNo', '<=', $newPosition)
                    ->get();
            
            foreach($set as $el){
                $el->orderNo = $el->orderNo-1;
                $el->save();
            }
        }
        else{
            $set = $sql->where('orderNo', '<', $currentNo)
                    ->where('orderNo', '>=', $newPosition)
                    ->get();
            
            foreach($set as $el){
                $el->orderNo = $el->orderNo+1;
                $el->save();
            }
        }
        
        $model->orderNo = $newPosition;
        
        $model->save();
        
        self::normalizeOrder($model->getTable(), $where);
        
        return $model;
    }
    
    /**
     * Get list of images from the directory
     *
     * @param string $folder folder where images are stored
     * @param string $imgUri images uri. Keep it null if images are stored on the
     * public zone, otherwise specify uri which can read file in non-public zone
     * @return array
     */
    public static function browseImages($folder, $imgUri = null) {
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0774, true);
        }

        $files = [];
        // create list of images
        foreach (glob(public_path($folder) . "/*") as $file) {
            if (in_array(File::mimeType($file), array("image/jpeg", "image/pjpeg", "image/png", "image/gif"))) {
                $img = null;
                switch (File::mimeType($file)) {
                    case "image/jpeg" :
                    case "image/pjpeg" :
                        $img = imageCreateFromJpeg($file);
                        break;
                    case "image/png" :
                        $img = imageCreateFromPng($file);
                        break;
                    case "image/gif" :
                        $img = imageCreateFromGif($file);
                        break;
                }
                $path = is_null($imgUri) ? str_replace(public_path(), '', $file) : $imgUri . basename($file);
                $files[$path] = [
                    "modify" => date("d.m.Y H:i:s", filemtime($file)),
                    "size" => round(filesize($file) / 1024) . " KB",
                    "width" => imageSX($img),
                    "height" => imageSY($img),
                ];

                imagedestroy($img);
            }
            // if file is not an image, it should be removed
            else {
                unlink($file);
            }
        }

        return $files;
    }
    
    /**
     * Generate pagination for the collection
     *
     * @param array|Collection      $items
     * @param int   $perPage
     * @param int  $page
     * @param array $options
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage = 15, $page = null, $options = []) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

}
