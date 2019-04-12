<?php

namespace Lubart\Just\Tests\Unit;

use Tests\TestCase;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Foundation\Testing\WithFaker;

class AddonTest extends TestCase
{
    
    use WithFaker;
    
    /** @test */
    function exception_is_thrown_on_call_unknown_addon(){
        $addon = new Block\Addon();
        $addon->type = $type = $this->faker->word;
        $addon->name = $this->faker->word;
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Addon class not found");
        
        $addon->addon();
    }
}
