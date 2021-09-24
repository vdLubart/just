<?php

namespace Just\Tools;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Just\Models\System\Route as JustRoute;

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
        $routes = JustRoute::where('route', $route)->first();

        if(!empty($routes)){
            return true;
        }

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
