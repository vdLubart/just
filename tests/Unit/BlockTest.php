<?php

namespace Lubart\Just\Tests\Unit;

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
    }
}
