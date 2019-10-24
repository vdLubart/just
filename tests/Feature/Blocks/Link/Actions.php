<?php

namespace Lubart\Just\Tests\Feature\Blocks\Link;

use Lubart\Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;

class Actions extends LocationBlock {
    
    use WithFaker;

    protected $type = 'link';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        \Lubart\Just\Models\Route::where('id', '>', 1)->delete();
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion?'assertSee':'assertDontSee')}('select name="linkedBlock_id"');
    }
    
    public function access_edit_item_form($assertion){
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $block = $this->setupBlock();
        
        $textItem = new Block\Text();
        $textItem->block_id = $textBlock->id;
        $textItem->text = $text = $this->faker->paragraph;

        $textItem->save();

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
            $this->assertEquals($textBlock->id, $item->linkedBlock()->id);
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $route = \Lubart\Just\Models\Route::create([
            'route' => 'mirror-'.$this->faker->word,
            'type' => 'page'
        ]);
        
        $page = \Lubart\Just\Structure\Page::create([
            'title' => 'Mirror',
            'route' => $route->route,
            'layout_id' => 1
        ]);
        
        $this->app['router']->get($route->route, "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/'.$route->route, "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $block = $this->setupBlock(['page_id'=>$page->id]);
        
        $textItem = new Block\Text();
        $textItem->block_id = $textBlock->id;
        $textItem->text = $text = $this->faker->paragraph;

        $textItem->save();
        
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
        }
        else{
            $this->assertNull($item);
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = $this->setupBlock();
        
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
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $contactBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact', 'parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":100}')])->specify();
        $route = \Lubart\Just\Models\Route::create([
            'route' => $path = 'mirror-'.$this->faker->word,
            'type' => 'page'
        ]);
        
        $page = \Lubart\Just\Structure\Page::create([
            'title' => 'Mirror',
            'route' => $route->route,
            'layout_id' => 1
        ]);
        
        $this->app['router']->get($route->route, "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/'.$route->route, "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $block = $this->setupBlock(['page_id'=>$page->id]);
        
        $textItem = new Block\Text();
        $textItem->block_id = $textBlock->id;
        $textItem->text = $text = $this->faker->paragraph;

        $textItem->save();

        $envelope = str_replace("\n", ", ", $this->faker->address);
        $phone = $this->faker->phoneNumber;
        $at = $this->faker->email;

        $item = new Block\Contact();
        $item->block_id = $contactBlock->id;
        $item->title = $title = $this->faker->sentence;
        $item->channels = '{"envelope":"'.$envelope.'","phone":"'.$phone.'","at":"'.$at.'"}';
        $item->save();

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
            
            $this->post("", [
                'block_id' => $block->id,
                'id' => $item->id,
                'linkedBlock_id' => $contactBlock->id
            ]);

            $item = Block\Link::all()->last();

            $this->assertEquals($contactBlock->id, $item->firstItem()->linkedBlock_id);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin/' . $path)
                    ->assertDontSee($text);
            
            $this->get($path)
                    ->assertDontSee($text);
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
