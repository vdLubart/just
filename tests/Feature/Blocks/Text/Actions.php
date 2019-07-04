<?php

namespace Lubart\Just\Tests\Feature\Blocks\Text;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion?'assertSee':'assertDontSee')}('textarea name="text"');
    }
    
    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $text = $this->faker->paragraph;
        
        Block\Text::insert([
            'block_id' => $block->id,
            'text' => $text,
        ]);
        
        $item = Block\Text::all()->last();
        
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $text = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $text
        ]);
        
        $item = Block\Text::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            
            $this->get('admin')
                    ->assertSee($text);
            
            $this->get('')
                    ->assertSee($text);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($text);
            
            $this->get('')
                    ->assertDontSee($text);
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
        ]);
        
        $item = Block\Text::all()->last();
        
        $response->assertRedirect();
        
        $this->assertNull($item);
        
        if($assertion){
            $this->followRedirects($response)
                    ->assertSee("The text field is required");
        }
        else{
            $this->followRedirects($response)
                    ->assertDontSee("The text field is required");
        }
    }
    
    public function create_few_items_in_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $firstText = $this->faker->paragraph;
        $secondText = $this->faker->paragraph;
        $thirdText = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $firstText
        ]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $secondText
        ]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $thirdText
        ]);
        
        if($assertion){
            $this->get('admin')
                    ->assertSee($firstText)
                    ->assertSee($secondText)
                    ->assertSee($thirdText);
            
            $this->get('')
                    ->assertSee($firstText)
                    ->assertSee($secondText)
                    ->assertSee($thirdText);
        }
        else{
            $this->get('admin')
                    ->assertDontSee($firstText)
                    ->assertDontSee($secondText)
                    ->assertDontSee($thirdText);
            
            $this->get('')
                    ->assertDontSee($firstText)
                    ->assertDontSee($secondText)
                    ->assertDontSee($thirdText);
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = factory(Block::class)->create();
        
        Block\Text::insert([
            'block_id' => $block->id,
            'text' => $this->faker->paragraph,
        ]);
        
        $item = Block\Text::all()->last();
        
        $text = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'text' => $text
        ]);
        
        $item = Block\Text::all()->last();
        
        if($assertion){
            $this->assertEquals($text, $item->text);
        }
        else{
            $this->assertNotEquals($text, $item->text);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text'])->specify();
        
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
