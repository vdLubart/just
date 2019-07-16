<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\Features;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Just\Tools\Useful;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/articles'))){
            exec('rm -rf ' . public_path('storage/articles'));
        }
        
        parent::tearDown();
    }
    
    public function access_item_form_without_initial_data($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'features'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->assertDontSee('input name="title"');
        
        $response->{($assertion?'assertDontSee':'assertSee')}('Items amount in single row');
        
        $this->post('admin/settings/setup', [
            'id' => $block->id,
            'itemsInRow' => "4"
        ])
                ->assertStatus(200);
        
        $block = Block::find($block->id);
        $this->{($assertion ? 'assertJsonStringNotEqualsJsonString' : 'assertJsonStringEqualsJsonString')}('{"itemsInRow":"4"}', json_encode($block->parameters()));

        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}(4, $block->parameter('itemsInRow'));
        
    }
    
    public function access_item_form_when_block_is_setted_up($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'features', 'parameters'=>'{"itemsInRow":"4"}'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('select id="iconSet"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="title"');
    }

    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'features'])->specify();
        
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'features', 'parameters'=>'{"itemsInRow":"4"}'])->specify();
        
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
        
        $icon = \Lubart\Just\Models\Icon::find(1);
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals(1, $block->firstItem()->icon_id);
            $this->assertEquals($title, $block->firstItem()->title);
            $this->assertEquals($description, $block->firstItem()->description);
            $this->assertEquals($link, $block->firstItem()->link);
            
            $this->get('admin')
                    ->assertSee($icon->class)
                    ->assertSee("<".$icon->iconSet->tag)
                    ->assertSee('class="'.$icon->iconSet->class)
                    ->assertSee($title)
                    ->assertSee($description)
                    ->assertSee($link);
            
            $this->get('')
                    ->assertSee($icon->class)
                    ->assertSee("<".$icon->iconSet->tag)
                    ->assertSee('class="'.$icon->iconSet->class)
                    ->assertSee($title)
                    ->assertSee($description)
                    ->assertSee($link);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($icon->class)
                    ->assertDontSee("<".$icon->iconSet->tag)
                    ->assertDontSee('class="'.$icon->iconSet->class)
                    ->assertDontSee($title)
                    ->assertDontSee($description)
                    ->assertDontSee($link);
            
            $this->get('')
                    ->assertDontSee($icon->class)
                    ->assertDontSee("<".$icon->iconSet->tag)
                    ->assertDontSee('class="'.$icon->iconSet->class)
                    ->assertDontSee($title)
                    ->assertDontSee($description)
                    ->assertDontSee($link);
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'features', 'parameters'=>'{"itemsInRow":"4"}'])->specify();
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'iconSet' => 1
        ]);
        
        $item = Block\Features::all()->last();
        
        $response->assertRedirect();
        
        $this->assertNull($item);
        
        if($assertion){
            $this->followRedirects($response)
                    ->assertSee("The icon field is required")
                    ->assertSee("The title field is required");
        }
        else{
            $this->followRedirects($response)
                    ->assertDontSee("The icon field is required")
                    ->assertDontSee("The title field is required");
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'features', 'parameters'=>'{"itemsInRow":"4"}'])->specify();
        
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'features'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            if(\Auth::user()->role == 'master'){
                $response->assertSee('Image fields');
                
                $this->assertCount(3, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'itemsInRow', 'ignoreCaption', 'ignoreDescription', 'submit'], $block->setupForm()->names());
            }
            else{
                $response->assertDontSee('Image fields');
                
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
                $this->assertEquals('{"itemsInRow":"4","ignoreCaption":"on"}', json_encode($block->parameters()));
                $this->assertNull($form->getElement('title'));
                $this->assertNotNull($form->getElement('description'));
            }
            else{
                $this->assertEquals('{"itemsInRow":"4"}', json_encode($block->parameters()));
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
                $this->assertEquals('{"itemsInRow":"4","ignoreDescription":"on"}', json_encode($block->parameters()));
                $this->assertNotNull($form->getElement('title'));
                $this->assertNull($form->getElement('description'));
            }
            else{
                $this->assertEquals('{"itemsInRow":"4"}', json_encode($block->parameters()));
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
            
            $this->assertNotEquals('{"settingsScale":"100"}', json_encode($block->parameters()));
        }
    }
}
