<?php

namespace Just\Tests\Unit;

use Just\Models\Blocks\AbstractItem;
use Just\Models\Blocks\Gallery;
use Just\Models\Blocks\Text;
use Just\Tests\Feature\Helper;
use Tests\TestCase;
use Just\Models\Block;
use Illuminate\Foundation\Testing\WithFaker;

class BlockTest extends TestCase
{
    use Helper;
    use WithFaker;

    /** @test */
    function exception_is_thrown_on_call_unknown_block(){
        $block = new Block();
        $block->type = $type = $this->faker->word;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Block class \"".ucfirst($type)."\" not found");

        $block->specify();
    }

    /** @test */
    function get_block_by_unique_name(){
        $block = Block::factory()->create(['name'=>$name = $this->faker->word]);

        $foundedBlock = Block::findByName($name);

        $this->assertEquals($block->id, $foundedBlock->id);
        $this->assertNull(Block::findByName('unknown'));
    }

    /** @test */
    function cannot_find_model_of_the_unknown_block(){
        $this->assertNull(Block::findModel(0, 0));
    }

    /** @test */
    function get_all_block_models(){
        $block = Block::factory()->create(['name'=>$name = $this->faker->word]);

        $textItem = new Text();
        $textItem->block_id = $block->id;
        $textItem->text = $this->faker->paragraph;

        $textItem->save();

        $textItem = new Text();
        $textItem->block_id = $block->id;
        $textItem->text = $this->faker->paragraph;

        $textItem->save();

        $this->assertCount(2, $block->items);
    }

    /** @test */
    function get_image_path(){
        $gallery = new Gallery();

        $this->assertEquals('/storage/photos/imageCode.png', $gallery->imageSource(null, 'imageCode'));
        $this->assertEquals('/storage/photos/imageCode.png', $gallery->imageSource(6, 'imageCode'));
    }

    /** @test */
    function cannot_access_block_item_list_if_block_does_not_exist() {
        $this->actingAsAdmin();

        $block = Block::factory()->create();

        $block->delete();

        $this->get('settings')->assertSuccessful();

        $this->get('settings/block/'.$block->id)
            ->assertRedirect('settings');
    }

    /** @test */
    function image_path_for_the_text_item_returns_null(){
        $block = Block::factory()->create();

        $textItem = new Text();
        $textItem->block_id = $block->id;
        $textItem->text = $this->faker->paragraph;

        $textItem->save();

        $this->assertNull($textItem->imagePath());
    }
}
