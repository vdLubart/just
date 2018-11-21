<?php

namespace Lubart\Just\Structure\Panel\Block\Addon;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Lubart\Just\Structure\Panel\Block\Addon;
use Illuminate\Support\Facades\DB;

abstract class AbstractAddon extends Model
{   
    /**
     * Update existing settings form and add new elements
     * 
     * @param Form $form Form object
     */
    abstract public static function updateForm(Addon $addon, Form $form, $values);
    
    /**
     * Return validation rules for current addon
     * 
     * @param Addon $addon current addon object
     */
    abstract public static function validationRules(Addon $addon);

    public function addon() {
        return $this->belongsTo(Addon::class, "addon_id", "id");
    }
    
    public function model() {
        return self::belongsToMany();
    }
    
    protected static function handleData($value, $addon, $item){
        $oldData = DB::table($item->getTable()."_".$addon->type)
                        ->join($addon->type, 'addonItem_id', '=', $addon->type.'.id')
                        ->where('modelItem_id', $item->id)
                        ->where('addon_id', $addon->id)
                        ->first();
        
        if(empty($oldData)){
            $addonData = self::create(['addon_id'=>$addon->id, 'value'=>$value]);
        
            DB::table($item->getTable()."_".$addon->type)
                ->insert([
                    'modelItem_id' => $item->id,
                    'addonItem_id' => $addonData->id
                ]);
        }
        else{
            self::updateOrCreate(['id'=>$oldData->addonItem_id], ['value'=>$value]);
        }
    }
}
