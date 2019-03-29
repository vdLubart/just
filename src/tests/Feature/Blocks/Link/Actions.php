<?php

namespace Lubart\Just\Tests\Feature\Blocks\Link;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        \Lubart\Just\Models\Route::where('id', '>', 1)->delete();
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'link'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion?'assertSee':'assertDontSee')}('select id="linkedBlock_id"');
    }
    
    public function access_edit_item_form($assertion){
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'link'])->specify();
        
        $text = $this->faker->paragraph;
        
        Block\Text::insert([
            'block_id' => $textBlock->id,
            'text' => $text,
        ]);
        
        Block\Link::insert([
            'block_id' => $block->id,
            'linkedBlock_id' => $textBlock->id,
        ]);
        
        $item = Block\Link::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(2, $form->count());
            $this->assertEquals(['linkedBlock_id', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($textBlock->id, $form->getElement('linkedBlock_id')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $route = \Lubart\Just\Models\Route::create([
            'route' => 'mirror',
            'type' => 'page'
        ]);
        
        $page = \Lubart\Just\Structure\Page::create([
            'title' => 'Mirror',
            'route' => $route->route,
            'layout_id' => 1
        ]);
        
        $this->app['router']->get($route->route, "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/'.$route->route, "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>$page->id, 'name'=>'link'])->specify();
        
        $text = $this->faker->paragraph;
        
        Block\Text::insert([
            'block_id' => $textBlock->id,
            'text' => $text,
        ]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'linkedBlock_id' => $textBlock->id
        ]);
        
        $item = Block\Link::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($textBlock->id, $block->firstItem()->linkedBlock_id);
            
            $this->get('admin/mirror')
                    ->assertSee($text);
            
            $this->get('mirror')
                    ->assertSee($text);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin/mirror')
                    ->assertDontSee($text);
            
            $this->get('mirror')
                    ->assertDontSee($text);
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'link'])->specify();
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ]);
        
        $item = Block\Link::all()->last();
        
        $response->assertRedirect();
        
        $this->assertNull($item);
        
        if($assertion){
            $this->followRedirects($response)
                ->assertSee("The linked block id field is required");
        }
        else{
            $this->followRedirects($response)
                ->assertDontSee("The linked block id field is required");
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        //TODO: continue here
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $contactBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'contact'])->specify();
        $route = \Lubart\Just\Models\Route::create([
            'route' => 'mirror',
            'type' => 'page'
        ]);
        
        $page = \Lubart\Just\Structure\Page::create([
            'title' => 'Mirror',
            'route' => $route->route,
            'layout_id' => 1
        ]);
        
        $this->app['router']->get($route->route, "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/'.$route->route, "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>$page->id, 'name'=>'link'])->specify();
        
        $text = $this->faker->paragraph;
        
        Block\Text::insert([
            'block_id' => $textBlock->id,
            'text' => $text,
        ]);
        
        Block\Contact::insert([
            'block_id' => $contactBlock->id,
            'title' => $title = $this->faker->sentence,
            'address' => $address = $this->faker->address,
            'phone' => $phone = $this->faker->phoneNumber,
            'email' => $email = $this->faker->email
        ]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'linkedBlock_id' => $textBlock->id
        ]);
        
        $item = Block\Link::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($textBlock->id, $block->firstItem()->linkedBlock_id);
            
            $this->get('admin/mirror')
                    ->assertSee($text);
            
            $this->get('mirror')
                    ->assertSee($text);
            
            $this->post("", [
                'block_id' => $block->id,
                'id' => $item->id,
                'linkedBlock_id' => $contactBlock->id
            ]);
            
            $item = Block\Link::all()->last();
            
            $this->assertEquals($contactBlock->id, $block->firstItem()->linkedBlock_id);
            
            $this->get('admin/mirror')
                    ->assertDontSee($text)
                    ->assertSee($title)
                    ->assertSee($address)
                    ->assertSee($phone)
                    ->assertSee($email);
            
            $this->get('mirror')
                    ->assertDontSee($text)
                    ->assertSee($title)
                    ->assertSee($address)
                    ->assertSee($phone)
                    ->assertSee($email);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin/mirror')
                    ->assertDontSee($text);
            
            $this->get('mirror')
                    ->assertDontSee($text);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'link'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            
            $this->assertCount(2, $block->setupForm()->groups());
            
            $this->assertEquals(['id', 'settingsScale', 'submit'], $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertEquals('{"settingsScale":"100"}', $block->parameters);
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"settingsScale":"100"}', $block->parameters);
        }
    }
}
