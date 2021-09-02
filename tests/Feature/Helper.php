<?php

namespace Just\Tests\Feature;

use Just\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

trait Helper {

    public function actingAsAdmin(){
        $user = User::where('role', 'admin')->first();

        $this->actingAs($user);

        return $user;
    }

    public function actingAsMaster(){
        $user = User::where('role', 'master')->first();

        $this->actingAs($user);

        return $user;
    }

    public function createPivotTable($block, $addon){
        $addonTable = $this->addonTable($addon);
        $modelTable = $this->modelTable($block);

        if(!Schema::hasTable($this->modelTable($block).'_'.$this->addonTable($addon))){
            Schema::create($modelTable.'_'.$addonTable, function (Blueprint $table) use ($modelTable, $addonTable){
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('modelItem_id')->unsigned();
                $table->integer('addonItem_id')->unsigned();
                $table->timestamps();

                $table->foreign('modelItem_id')->references('id')->on($modelTable)->onUpdate('cascade')->onDelete('cascade');
                $table->foreign('addonItem_id')->references('id')->on($addonTable)->onUpdate('cascade')->onDelete('cascade');
            });
        }
    }

    public function removePivotTable($block, $addon){
        Schema::dropIfExists($this->modelTable($block).'_'.$this->addonTable($addon));
    }

    protected function modelTable($block) {
        return $block->item()->getTable();
    }

    protected function addonTable($addon) {
        $addonItem = $addon->addonItemClassName();

        return (new $addonItem)->getTable();
    }
}
