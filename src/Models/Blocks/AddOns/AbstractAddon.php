<?php

namespace Just\Models\Blocks\AddOns;

use Illuminate\Database\Eloquent\Model;
use Lubart\Form\Form;
use Just\Models\AddOn;
use Illuminate\Support\Facades\DB;

abstract class AbstractAddOn extends Model
{   
    /**
     * Update existing settings form and add new elements
     * 
     * @param Form $form Form object
     */
    abstract public static function updateForm(AddOn $addon, Form $form, $values);
    
    /**
     * Return validation rules for current addon
     * 
     * @param AddOn $addon current addon object
     */
    abstract public static function validationRules(AddOn $addon);

    public function addon() {
        return $this->belongsTo(AddOn::class, "addon_id", "id");
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
