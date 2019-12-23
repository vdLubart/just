<?php

namespace Just\Tests\Unit;

use Just\Tests\Feature\Helper;
use Tests\TestCase;
use Just\Structure\Panel\Block;
use Illuminate\Foundation\Testing\WithFaker;

class AddonTest extends TestCase
{

    protected function tearDown(): void{
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

    /** @test */
    function get_all_string_addons(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $this->assertCount(2, $block->strings);

        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }

    /** @test */
    function get_all_image_addons(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'images', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'images', 'name'=>$name = $this->faker->word]);

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $this->assertCount(2, $block->images);

        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }

    /** @test */
    function get_all_paragraph_addons(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'paragraphs', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'paragraphs', 'name'=>$name = $this->faker->word]);

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $this->assertCount(2, $block->paragraphs);

        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
}
