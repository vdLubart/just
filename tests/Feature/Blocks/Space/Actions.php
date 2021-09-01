<?php

namespace Just\Tests\Feature\Blocks\Space;

use Just\Models\Blocks\Space;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;

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

        $space = new Space();

        if($assertion){
            $form = $space->itemForm();
            $this->assertEquals(['submit'], array_keys($form->elements()));

            $this->get('admin')
                    ->assertSee('<div id="space_'.$block->id.'"')
                    ->assertSee('<div style="height: 200px"></div>');

            $this->get('')
                    ->assertSee('<div id="space_'.$block->id.'"')
                    ->assertSee('<div style="height: 200px"></div>');
        }
        else{
            $this->assertEquals(0, $space->itemForm()->count());

            $this->get('admin')
                    ->assertRedirect('/login');

            $this->get('')
                    ->assertSee('<div id="space_'.$block->id.'"')
                    ->assertSee('<div style="height: 200px"></div>');
        }
    }

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            $this->assertCount(0, $form->groups());

            $this->assertEquals(['id', 'height', 'submit'], $form->names());

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "height" => "100px"
            ]);

            $block = Block::find($block->id);

            $this->assertEquals("100px", $block->parameters->height);
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "height" => "100px"
            ]);

            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }
}
