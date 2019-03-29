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
        $block->name = $name = $this->faker->word;
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Block class \"".ucfirst($name)."\" not found");
        
        $block->specify();
    }
}
