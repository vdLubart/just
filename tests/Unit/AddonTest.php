<?php

namespace Lubart\Just\Tests\Unit;

use Lubart\Just\Tests\Feature\Helper;
use Tests\TestCase;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Foundation\Testing\WithFaker;

class AddonTest extends TestCase
{

    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }

        if(file_exists(public_path('storage/texts'))){
            exec('rm -rf ' . public_path('storage/texts'));
        }

        parent::tearDown();
    }
    
    use WithFaker;
    use Helper;
    
    /** @test */
    function exception_is_thrown_on_call_unknown_addon(){
        $addon = new Block\Addon();
        $addon->type = $type = $this->faker->word;
        $addon->name = $this->faker->word;
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Addon class not found");
        
        $addon->addon();
    }

    /** @test */
    public function access_item_addon_by_name() {
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name."_".$addon->id => $string = $this->faker->sentence,
            'text' => $text = $this->faker->paragraph
        ]);

        $item = Block\Text::all()->last();

        $this->assertEquals($addon->id, $item->addon($name)->addon_id);

        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }

    /** @test */
    public function access_block_addon_by_name(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $this->assertEquals($addon->id, $block->addon($name)->id);

        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
}
