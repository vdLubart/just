<?php

namespace Just\Tests\Feature\Blocks\Html;

use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Structure\Panel\Block;

class Actions extends LocationBlock {
    
    use WithFaker;

    protected $type = 'html';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion?'assertSee':'assertDontSee')}('textarea name="text"');
    }
    
    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();
        
        $htmlItem = new Block\Text();
        $htmlItem->block_id = $block->id;
        $htmlItem->text = $text = $this->faker->paragraph;

        $htmlItem->save();

        if($assertion){
            $form = $htmlItem->form();
            $this->assertEquals(2, $form->count());
            $this->assertEquals(['text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());
        }
        else{
            $this->assertNull($htmlItem->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock();
        
        $text = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $text
        ]);
        
        $item = Block\Html::where('block_id', $block->id)->first();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            
            $this->get('admin')
                    ->assertSuccessful();
            
            $this->get('')
                    ->assertSuccessful();
        }
        else{
            $this->assertNull($item);
        }
    }
    
    public function receive_an_error_on_sending_incomplete_create_item_form($assertion){
        $block = $this->setupBlock();

        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ])
            ->assertRedirect();

        if($assertion){
            $response->assertSessionHasErrors('text');
        }

        $item = Block\Html::where('block_id', $block->id)->first();

        $this->assertNull($item);
    }
    
    public function create_few_items_in_block($assertion){
        $block = $this->setupBlock();
        
        $firstText = $this->faker->paragraph;
        $secondText = $this->faker->paragraph;
        $thirdText = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $firstText
        ]);

        $firstItem = Block\Html::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $secondText
        ]);

        $secondItem = Block\Html::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $thirdText
        ]);

        $thirdItem = Block\Html::all()->last();
        
        if($assertion){
            $this->assertEquals($firstText, $firstItem->text);
            $this->assertEquals($secondText, $secondItem->text);
            $this->assertEquals($thirdText, $thirdItem->text);
        }
        else{
            $this->assertNull($firstItem);
            $this->assertNull($secondItem);
            $this->assertNull($thirdItem);
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock();

        $htmlItem = new Block\Text();
        $htmlItem->block_id = $block->id;
        $htmlItem->text = $this->faker->paragraph;

        $htmlItem->save();

        $text = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $htmlItem->id,
            'text' => $text
        ]);
        
        $htmlItem = Block\Html::all()->last();
        
        if($assertion){
            $this->assertEquals($text, $htmlItem->text);
        }
        else{
            $this->assertNotEquals($text, $htmlItem->text);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            
            $this->assertCount(3, $block->setupForm()->groups());
            
            $this->assertEquals(['id', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);

            $this->assertEquals(100, $block->parameters->settingsScale);
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }
}
