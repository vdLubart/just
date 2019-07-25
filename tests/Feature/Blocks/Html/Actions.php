<?php

namespace Lubart\Just\Tests\Feature\Blocks\Html;

use Lubart\Just\Tests\Feature\Blocks\BlockLocation;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;

class Actions extends BlockLocation {
    
    use WithFaker;

    protected $type = 'html';
    
    public function tearDown(){
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
        
        $text = $this->faker->paragraph;
        
        Block\Html::insert([
            'block_id' => $block->id,
            'text' => $text,
        ]);
        
        $item = Block\Html::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(2, $form->count());
            $this->assertEquals(['text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());
        }
        else{
            $this->assertNull($item->form());
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
        
        Block\Html::insert([
            'block_id' => $block->id,
            'text' => $this->faker->paragraph,
        ]);
        
        $item = Block\Html::all()->last();
        
        $text = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'text' => $text
        ]);
        
        $item = Block\Html::all()->last();
        
        if($assertion){
            $this->assertEquals($text, $item->text);
        }
        else{
            $this->assertNotEquals($text, $item->text);
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
            
            $this->assertEquals('{"settingsScale":"100"}', json_encode($block->parameters()));
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"settingsScale":"100"}', json_encode($block->parameters()));
        }
    }
}
