<?php

namespace Just\Tests\Feature\Blocks\Space;

use Just\Tests\Feature\Blocks\LocationBlock;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Structure\Panel\Block;

class Actions extends LocationBlock {
    
    use WithFaker;

    protected $type = 'space';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"height":"200px"}')]);

        $this->assertEmpty($block->content());
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion?'assertSee':'assertDontSee')}('form');
        
        $space = new Block\Space();
        
        if($assertion){
            $form = $space->form();
            $this->assertEquals([], array_keys($form->getElements()));
            
            $this->get('admin')
                    ->assertSee('<div id="space_'.$block->id.'"')
                    ->assertSee('<div style="height: 200px"></div>');
            
            $this->get('')
                    ->assertSee('<div id="space_'.$block->id.'"')
                    ->assertSee('<div style="height: 200px"></div>');
        }
        else{
            $this->assertNull($space->form());
            
            $this->get('admin')
                    ->assertRedirect('/login');
            
            $this->get('')
                    ->assertSee('<div id="space_'.$block->id.'"')
                    ->assertSee('<div style="height: 200px"></div>');
        }
    }
    
    public function edit_block_settings($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertDontSee('Settings View');
            
            $this->assertCount(0, $block->setupForm()->groups());
            
            $this->assertEquals(['id', 'height', 'submit'], $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "height" => "100px"
            ]);
            
            $block = Block::find($block->id);

            $this->assertEquals("100px", $block->parameters->height);
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "height" => "100px"
            ]);
            
            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }
}
