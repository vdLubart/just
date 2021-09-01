<?php

namespace Just\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Just\Models\AddOn;
use Just\Models\Blocks\Text;
use Just\Tests\Feature\Helper;
use Tests\TestCase;
use Just\Models\Block;
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
        $addon = new AddOn();
        $addon->type = $type = $this->faker->word;
        $addon->name = $this->faker->word;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Add-on class not found");

        $addon->addonItemClassName();
    }

    /** @test */
    public function access_item_addon_by_name() {
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'phrase', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addonItemClassName();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->item()->getTable(), $addonTable);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name."_".$addon->id => '{"en":"'.($string = $this->faker->sentence).'"}',
            'text' => $text = $this->faker->paragraph
        ]);

        $item = Text::all()->last();

        $this->assertEquals($addon->id, $item->addon($name)->add_on_id);

        $this->removePivotTable($block->item()->getTable(), $addonTable);
    }

    /** @test */
    public function access_block_addon_by_name(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'phrase', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addonItemClassName();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->item()->getTable(), $addonTable);

        $this->assertEquals($addon->id, $block->addon($name)->id);

        $this->removePivotTable($block->item()->getTable(), $addonTable);
    }

    /** @test */
    function get_all_string_addons(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'phrase', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addonItemClassName();
        $addonTable = (new $addonItem)->getTable();

        factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'phrase', 'name'=>$name = $this->faker->word]);

        $this->createPivotTable($block->item()->getTable(), $addonTable);

        $this->assertCount(2, $block->phrases);

        $this->removePivotTable($block->item()->getTable(), $addonTable);
    }

    /** @test */
    function get_all_image_addons(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'image', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addonItemClassName();
        $addonTable = (new $addonItem)->getTable();

        factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'image', 'name'=>$name = $this->faker->word]);

        $this->createPivotTable($block->item()->getTable(), $addonTable);

        $this->assertCount(2, $block->images);

        $this->removePivotTable($block->item()->getTable(), $addonTable);
    }

    /** @test */
    function get_all_paragraph_addons(){
        $this->actingAsAdmin();

        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $addon = factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'paragraph', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addonItemClassName();
        $addonTable = (new $addonItem)->getTable();

        factory(AddOn::class)->create(['block_id'=>$block->id, 'type'=>'paragraph', 'name'=>$name = $this->faker->word]);

        $this->createPivotTable($block->item()->getTable(), $addonTable);

        $this->assertCount(2, $block->paragraphs);

        $this->removePivotTable($block->item()->getTable(), $addonTable);
    }
}
