<?php

namespace Lubart\Just\Tests\Feature\Blocks\Menu;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Just\Models\Route;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        foreach(Route::where('route', '<>', '')->get() as $route){
            $route->delete();
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion?'assertSee':'assertDontSee')}('input name="item"');
        $response->{($assertion?'assertSee':'assertDontSee')}('select id="parent"');
        $response->{($assertion?'assertSee':'assertDontSee')}('select id="route"');
        $response->{($assertion?'assertSee':'assertDontSee')}('input name="url"');
    }
    
    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        Block\Menu::insert([
            'block_id' => $block->id,
            'item' => $menuItem = $this->faker->word,
            'parent' => null,
            'route' => '',
            'url' => ''
        ]);
        
        $item = Block\Menu::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['item', 'parent', 'route', 'url', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($menuItem, $form->getElement('item')->value());
            $this->assertNull($form->getElement('parent')->value());
            $this->assertEquals(1, $form->getElement('route')->value());
            $this->assertEquals('', $form->getElement('url')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $menuItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => ''
        ]);
        
        $item = Block\Menu::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            $firstItem = array_first($block->content())['item'];
            
            $this->assertEquals($menuItem, $firstItem->item);
            $this->assertNull($firstItem->parent);
            $this->assertEquals('', $firstItem->route);
            $this->assertNull($firstItem->url);
            
            $this->get('admin')
                    ->assertSee($menuItem);
            
            $this->get('')
                    ->assertSee($menuItem);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($menuItem);
            
            $this->get('')
                    ->assertDontSee($menuItem);
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'parent' => 0,
            'route' => 1
        ]);
        
        $item = Block\Menu::all()->last();
        
        $response->assertRedirect();
        
        $this->assertNull($item);
        
        if($assertion){
            $this->followRedirects($response)
                    ->assertSee("The item field is required");
        }
        else{
            $this->followRedirects($response)
                    ->assertDontSee("The item field is required");
        }
    }
    
    public function create_new_item_with_link_to_another_page($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        $route = \Lubart\Just\Models\Route::create([
            'route' => $this->faker->word,
            'type' => 'page'
        ]);
        
        \Lubart\Just\Structure\Page::create([
            'title' => $this->faker->word,
            'route' => $route->route,
            'layout_id' => 1
        ]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $menuItem = $this->faker->word,
            'parent' => 0,
            'route' => $route->id,
            'url' => ''
        ]);
        
        $item = Block\Menu::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            $firstItem = array_first($block->content())['item'];
            
            $this->assertEquals($menuItem, $firstItem->item);
            $this->assertNull($firstItem->parent);
            $this->assertEquals($route->route, $firstItem->route);
            $this->assertNull($firstItem->url);
            
            $this->get('admin')
                    ->assertSee($menuItem)
                    ->assertSee('<a href="'.env('APP_URL').'/admin/'.$route->route.'"');
            
            $this->get('')
                    ->assertSee($menuItem)
                    ->assertSee('<a href="'.env('APP_URL').'/'.$route->route.'"');
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($menuItem)
                    ->assertDontSee('<a href="'.env('APP_URL').'/admin/'.$route->route.'"');
            
            $this->get('')
                    ->assertDontSee($menuItem)
                    ->assertDontSee('<a href="'.env('APP_URL').'/'.$route->route.'"');
        }
    }
    
    public function create_new_item_with_custom_url($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $menuItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => $url = $this->faker->word
        ]);
        
        $item = Block\Menu::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            $firstItem = array_first($block->content())['item'];
            
            $this->assertEquals($menuItem, $firstItem->item);
            $this->assertNull($firstItem->parent);
            $this->assertEquals('', $firstItem->route);
            $this->assertEquals($url, $firstItem->url);
            
            $this->get('admin')
                    ->assertSee($menuItem)
                    ->assertSee('<a href="'.env('APP_URL').'/'.$url.'"');
            
            $this->get('')
                    ->assertSee($menuItem)
                    ->assertSee('<a href="'.env('APP_URL').'/'.$url.'"');
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($menuItem)
                    ->assertDontSee('<a href="'.env('APP_URL').'/'.$url.'"');
            
            $this->get('')
                    ->assertDontSee($menuItem)
                    ->assertDontSee('<a href="'.env('APP_URL').'/'.$url.'"');
        }
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $menuItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => $url = $this->faker->url
        ]);
        
        $item = Block\Menu::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            $firstItem = array_last($block->content())['item'];
            
            $this->assertEquals($menuItem, $firstItem->item);
            $this->assertNull($firstItem->parent);
            $this->assertEquals('', $firstItem->route);
            $this->assertEquals($url, $firstItem->url);
            
            $this->get('admin')
                    ->assertSee($menuItem)
                    ->assertSee('<a href="'.$url.'"');
            
            $this->get('')
                    ->assertSee($menuItem)
                    ->assertSee('<a href="'.$url.'"');
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($menuItem)
                    ->assertDontSee('<a href="'.$url.'"');
            
            $this->get('')
                    ->assertDontSee($menuItem)
                    ->assertDontSee('<a href="'.$url.'"');
        }
    }
    
    public function create_few_items_in_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $firstItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => $url1 = $this->faker->url
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
       
        $item1 = Block\Menu::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $secondItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => $url2 = $this->faker->url
        ]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $thirdItem = $this->faker->word,
            'parent' => $item1->id,
            'route' => 1,
            'url' => $url3 = $this->faker->url
        ]);
        
        if($assertion){
            $this->get('')
                    ->assertSee($firstItem)
                    ->assertSee($secondItem)
                    ->assertSee($thirdItem)
                    ->assertSee($url1)
                    ->assertSee($url2)
                    ->assertSee($url3)
                    ->assertSee('<li><a href="'.$url1.'">'.$firstItem.'</a><ul><li><a href="'.$url3.'">'.$thirdItem.'</a></li></ul></li>');
            
            $this->get('')
                    ->assertSee($firstItem)
                    ->assertSee($secondItem)
                    ->assertSee($thirdItem)
                    ->assertSee($url1)
                    ->assertSee($url2)
                    ->assertSee($url3)
                    ->assertSee('<li><a href="'.$url1.'">'.$firstItem.'</a><ul><li><a href="'.$url3.'">'.$thirdItem.'</a></li></ul></li>');
        }
        else{
            $this->get('admin')
                    ->assertSee($firstItem)
                    ->assertDontSee($secondItem)
                    ->assertDontSee($thirdItem)
                    ->assertSee($url1)
                    ->assertDontSee($url2)
                    ->assertDontSee($url3)
                    ->assertDontSee('<li><a href="'.$url1.'">'.$firstItem.'</a><ul><li><a href="'.$url3.'">'.$thirdItem.'</a></li></ul></li>');
            
            $this->get('')
                    ->assertSee($firstItem)
                    ->assertDontSee($secondItem)
                    ->assertDontSee($thirdItem)
                    ->assertSee($url1)
                    ->assertDontSee($url2)
                    ->assertDontSee($url3)
                    ->assertDontSee('<li><a href="'.$url1.'">'.$firstItem.'</a><ul><li><a href="'.$url3.'">'.$thirdItem.'</a></li></ul></li>');
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
        Block\Menu::insert([
            'block_id' => $block->id,
            'item' => $this->faker->word,
            'parent' => null,
            'route' => '',
            'url' => null
        ]);
        
        $item = Block\Menu::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'item' => $updatedItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => ''
        ]);
        
        $item = Block\Menu::all()->last();
        
        if($assertion){
            $this->assertEquals($updatedItem, $item->item);
        }
        else{
            $this->assertNotEquals($updatedItem, $item->item);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'menu'])->specify();
        
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
