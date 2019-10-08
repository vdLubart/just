<?php

namespace Lubart\Just\Tests\Feature\Blocks\Features;

use Lubart\Just\Tests\Feature\Blocks\BlockLocation;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Just\Tools\Useful;

class Actions extends BlockLocation {
    
    use WithFaker;

    protected $type = 'features';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/articles'))){
            exec('rm -rf ' . public_path('storage/articles'));
        }
        
        parent::tearDown();
    }
    
    public function access_item_form_without_initial_data($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get("admin/settings/".$block->id."/0");

        $response->assertDontSee('input name="title"');
        
        $response->{($assertion?'assertDontSee':'assertSee')}('Items amount in single row');
        
        $this->post('admin/settings/setup', [
            'id' => $block->id,
            'itemsInRow' => "4"
        ])
                ->assertStatus(200);

        $block = Block::find($block->id);
        $this->assertEquals(4, $block->parameters->itemsInRow);
        if(\Auth::user()->role == 'master') {
            $this->assertFalse($block->parameters->ignoreCaption);
            $this->assertFalse($block->parameters->ignoreDescription);
        }
        else{
            $this->assertNull(@$block->parameters->ignoreCaption);
            $this->assertNull(@$block->parameters->ignoreDescription);
        }
    }
    
    public function access_item_form_when_block_is_setted_up($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemsInRow":"4"}')]);
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('select name="iconSet"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="title"');
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();
        
        Block\Features::insert([
            'block_id' => $block->id,
            'icon_id' => 1,
            'title' => $title = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
            'link' => $link = $this->faker->url
        ]);
        
        $this->assertTrue(Useful::isRouteExists("iconset/{id}/{page?}"));

        $this->app['router']->get('iconset/{id}/{page?}', "\Lubart\Just\Controllers\JustController@ajax")->middleware('web');
        
        $this->get("iconset/1")
                ->assertStatus(200);
        
        $item = Block\Features::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(8, $form->count());
            $this->assertEquals(['currentIcon', 'iconSet', 'divicon', 'icon', 'title', 'description', 'link', 'submit'], array_keys($form->getElements()));
            $this->assertEquals(1, $form->getElement('icon')->value());
            $this->assertEquals($title, $form->getElement('title')->value());
            $this->assertEquals($description, $form->getElement('description')->value());
            $this->assertEquals($link, $form->getElement('link')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemsInRow":"4"}')]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'iconSet' => 1,
            'icon' => 1,
            'title' => $title = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
            'link' => $link = $this->faker->url
        ]);
        
        $this->assertTrue(Useful::isRouteExists("iconset/{id}/{page?}"));
        
        $item = Block\Features::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals(1, $block->firstItem()->icon_id);
            $this->assertEquals($title, $block->firstItem()->title);
            $this->assertEquals($description, $block->firstItem()->description);
            $this->assertEquals($link, $block->firstItem()->link);
            
            $this->get('admin')
                ->assertSuccessful();
            
            $this->get('')
                ->assertSuccessful();
        }
        else{
            $this->assertNull($item);
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemsInRow":"4"}')]);
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'iconSet' => 1
        ]);
        
        $item = Block\Features::all()->last();
        
        $this->assertNull($item);
        
        if($assertion){
            $response->assertSessionHasErrors(['icon', 'title']);
        }
        else{
            $response->assertRedirect('/login');
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemsInRow":"4"}')]);
        
        Block\Features::insert([
            'block_id' => $block->id,
            'icon_id' => 1,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'link' => $link = $this->faker->url
        ]);
        
        $item = Block\Features::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'iconSet' => 1,
            'icon' => 1,
            'title' => $title = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
            'link' => $link
        ]);
        
        $item = Block\Features::all()->last();
        
        if($assertion){
            $this->assertEquals($title, $item->title);
            $this->assertEquals($description, $item->description);
            $this->assertEquals($link, $item->link);
        }
        else{
            $this->assertNotEquals($title, $item->title);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            if(\Auth::user()->role == 'master'){
                $response->assertSee('Item Fields');
                
                $this->assertCount(3, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'itemsInRow', 'ignoreCaption', 'ignoreDescription', 'submit'], $block->setupForm()->names());
            }
            else{
                $response->assertDontSee('Item Fields');
                
                $this->assertCount(2, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'itemsInRow', 'submit'], $block->setupForm()->names());
            }
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "itemsInRow" => "4",
                "ignoreCaption" => "on"
            ]);
            
            $block = Block::find($block->id)->specify();
            
            $form = $block->form();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals(4, $block->parameters->itemsInRow);
                $this->assertTrue($block->parameters->ignoreCaption);
                $this->assertFalse($block->parameters->ignoreDescription);
                $this->assertNull($form->getElement('title'));
                $this->assertNotNull($form->getElement('description'));
            }
            else{
                $this->assertEquals(4, $block->parameters->itemsInRow);
                $this->assertNull(@$block->parameters->ignoreCaption);
                $this->assertNull(@$block->parameters->ignoreDescription);
                $this->assertNotNull($form->getElement('title'));
                $this->assertNotNull($form->getElement('description'));
            }
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "itemsInRow" => "4",
                "ignoreDescription" => "on"
            ]);
            
            $block = Block::find($block->id)->specify();
            
            $form = $block->form();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals(4, $block->parameters->itemsInRow);
                $this->assertFalse($block->parameters->ignoreCaption);
                $this->assertTrue($block->parameters->ignoreDescription);
                $this->assertNotNull($form->getElement('title'));
                $this->assertNull($form->getElement('description'));
            }
            else{
                $this->assertEquals(4, $block->parameters->itemsInRow);
                $this->assertNull(@$block->parameters->ignoreCaption);
                $this->assertNull(@$block->parameters->ignoreDescription);
                $this->assertNotNull($form->getElement('title'));
                $this->assertNotNull($form->getElement('description'));
            }
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals(json_decode('{"settingsScale":"100"}'), $block->parameters);
        }
    }
}
