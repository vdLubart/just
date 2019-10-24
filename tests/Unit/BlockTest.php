<?php

namespace Lubart\Just\Tests\Unit;

use Lubart\Just\Structure\Panel;
use Tests\TestCase;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Foundation\Testing\WithFaker;

class BlockTest extends TestCase
{
    
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
        $block = factory(Block::class)->create(['name'=>$name = $this->faker->word]);
        
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
        $block = factory(Block::class)->create(['name'=>$name = $this->faker->word]);

        $textItem = new Block\Text();
        $textItem->block_id = $block->id;
        $textItem->text = $this->faker->paragraph;

        $textItem->save();

        $textItem = new Block\Text();
        $textItem->block_id = $block->id;
        $textItem->text = $this->faker->paragraph;

        $textItem->save();

        $this->assertCount(2, $block->models);
    }

    /** @test */
    function get_image_path(){
        $gallery = new Block\Gallery();

        $this->assertEquals(public_path('storage/photos/imageCode.png'), $gallery->image('imageCode'));
        $this->assertEquals(public_path('storage/photos/imageCode_6.png'), $gallery->image('imageCode', 6));
    }
}
