<?php
namespace Just\Database\Helpers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

trait AddTranslations {

    protected function convertToJson($columns) {
        Schema::table($this->table, function (Blueprint $table) use ($columns){
            foreach($columns as $column){
                $table->text($column)->change();
            }
        });

        foreach (DB::table($this->table)->get() as $item){
            $translated = new \stdClass();
            $update = [];
            foreach($columns as $column){
                $translated->{$column} = new \stdClass();
                $translated->{$column}->en = $item->{$column};
                $update[$column] = json_encode($translated->{$column});
            }

            DB::table($this->table)
                ->where('id', $item->id)
                ->update($update);
        }

        Schema::table($this->table, function (Blueprint $table) use ($columns){
            foreach ($columns as $column){
                $table->json($column)->charset('')->change();
            }
        });
    }

    /**
     * Run down() logic
     *
     * @param $items items should be converted
     * @param $columns columns should be converted from json. Format [columnName => [type, length]]
     */
    protected function convertBack($items, $columns) {
        Schema::table($this->table, function (Blueprint $table) use ($columns){
            foreach($columns as $column=>$type){
                $table->text($column)->change();
            }
        });

        Schema::table($this->table, function (Blueprint $table) use ($columns){
            foreach($columns as $column=>$data){
                $table->{$data[0]}($column, @$data[1])->change();
            }
        });

        foreach($items as $item){
            $update = [];
            foreach($columns as $column=>$type){
                $update[$column] = $item->{$column};
            }

            DB::table($this->table)->where($item->getKeyName(), $item->{$item->getKeyName()})->update($update);
        }
    }

}