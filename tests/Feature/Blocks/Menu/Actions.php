<?php

namespace Just\Tests\Feature\Blocks\Menu;

use Illuminate\Support\Facades\Auth;
use Just\Models\Blocks\Menu;
use Just\Models\Page;
use Just\Models\User;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
use Just\Models\System\Route;

class Actions extends LocationBlock {

    use WithFaker;

    protected $type = 'menu';

    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }

        foreach(Route::where('route', '<>', '')->get() as $route){
            $route->delete();
        }

        parent::tearDown();
    }

    public function access_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $form = $block->form();
            $this->assertEquals(2, $form->count());
            $this->assertEquals(['block_id', 'id'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();

        $item = new Menu();
        $item->block_id = $block->id;
        $item->item = $menuItem = $this->faker->word;
        $item->parent = null;
        $item->route = '';
        $item->url = '';

        $item->save();

        $item = Menu::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['id', 'block_id', 'item', 'parent', 'route', 'url', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($menuItem, $form->getElement('item')->value()['en']);
            $this->assertNull($form->getElement('parent')->value());
            $this->assertEquals(1, $form->getElement('route')->value());
            $this->assertEquals('', $form->getElement('url')->value());
        }
        else{
            $this->assertEquals(0, $item->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $menuItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => ''
        ]);

        $item = Menu::all()->last();

        if($assertion){
            $this->assertNotNull($item);
            $menu = $block->content();
            $firstItem = $menu->{'block/'.$block->id.'/item/'.$item->id}->item;

            $this->assertEquals($menuItem, $firstItem->title);
            $this->assertEquals('admin/', $firstItem->url);
        }
        else{
            $this->assertNull($item);
        }
    }

    public function receive_an_error_on_sending_incomplete_create_item_form(){
        $block = $this->setupBlock();

        $this->get("admin/settings/".$block->id."/0");

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'parent' => 0,
            'route' => 1
        ])
            ->assertSessionHasErrors('item')
            ->assertRedirect();

        $item = Menu::all()->last();

        $this->assertNull($item);
    }

    public function create_new_item_with_link_to_another_page($assertion){
        $block = $this->setupBlock();

        $route = \Just\Models\System\Route::create([
            'route' => $this->faker->word,
            'type' => 'page'
        ]);

        Page::create([
            'title' => $this->faker->word,
            'route' => $route->route,
            'layout_id' => 1
        ]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $menuItem = $this->faker->word,
            'parent' => 0,
            'route' => $route->id,
            'url' => ''
        ]);

        $item = Menu::all()->last();

        if($assertion){
            $this->assertNotNull($item);
            $menu = $block->content();
            $firstItem = $menu->{'block/'.$block->id.'/item/'.$item->id}->item;

            $this->assertEquals($menuItem, $firstItem->title);
            $this->assertEquals('admin/'.$route->route, $firstItem->url);
        }
        else{
            $this->assertNull($item);
        }
    }

    public function create_new_item_with_custom_url($assertion){
        $block = $this->setupBlock();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $menuItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => $url = $this->faker->word
        ]);

        $item = Menu::all()->last();

        if($assertion){
            $this->assertNotNull($item);
            $menu = $block->content();
            $firstItem = $menu->{'block/'.$block->id.'/item/'.$item->id}->item;

            $this->assertEquals($menuItem, $firstItem->title);
            $this->assertEquals($url, $firstItem->url);
        }
        else{
            $this->assertNull($item);
        }
    }

    public function create_few_items_in_block($assertion){
        $block = $this->setupBlock();

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $firstItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => $url1 = $this->faker->url
        ]);

        if(!$assertion){
            Auth::logout();
        }

        $item1 = Menu::all()->last();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'item' => $secondItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => $url2 = $this->faker->url
        ]);

        $this->post("settings/block/item/save", [
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
                    ->assertSee('<li class=\'\'><a href="'.$url1.'">'.$firstItem.'</a><ul><li class=\'\'><a href="'.$url3.'">'.$thirdItem.'</a><ul></ul></li></ul></li>');

            $this->get('')
                    ->assertSee($firstItem)
                    ->assertSee($secondItem)
                    ->assertSee($thirdItem)
                    ->assertSee($url1)
                    ->assertSee($url2)
                    ->assertSee($url3)
                    ->assertSee('<li class=\'\'><a href="'.$url1.'">'.$firstItem.'</a><ul><li class=\'\'><a href="'.$url3.'">'.$thirdItem.'</a><ul></ul></li></ul></li>');
        }
        else{
            $this->get('admin')
                    ->assertSee($firstItem)
                    ->assertDontSee($secondItem)
                    ->assertDontSee($thirdItem)
                    ->assertSee($url1)
                    ->assertDontSee($url2)
                    ->assertDontSee($url3)
                    ->assertDontSee('<li class=\'\'><a href="'.$url1.'">'.$firstItem.'</a><ul><li class=\'\'><a href="'.$url3.'">'.$thirdItem.'</a><ul></ul></li></ul></li>');

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
        $block = $this->setupBlock();

        $item = new Menu();
        $item->block_id = $block->id;
        $item->item = $menuItem = $this->faker->word;
        $item->parent = null;
        $item->route = '';
        $item->url = '';

        $item->save();

        $item = Menu::all()->last();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'item' => $updatedItem = $this->faker->word,
            'parent' => 0,
            'route' => 1,
            'url' => ''
        ]);

        $item = Menu::all()->last();

        if($assertion){
            $this->assertEquals($updatedItem, $item->item);
        }
        else{
            $this->assertNotEquals($updatedItem, $item->item);
        }
    }

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            $this->assertCount(1, $form->groups());

            $this->assertEquals(['id', 'orderDirection', 'submit'], $form->names());

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc"
            ]);

            $block = Block::find($block->id);

            $this->assertEquals('asc', $block->parameters->orderDirection);
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc"
            ]);

            $block = Block::find($block->id);

            $this->assertEmpty((array) $block->parameters);
        }
    }

    public function change_items_order_in_the_block($assertion){
        $block = $this->setupBlock();

        $item = new Menu();
        $item->block_id = $block->id;
        $item->item = $this->faker->word;
        $item->parent = null;
        $item->route = '';
        $item->url = null;
        $item->orderNo = 1;

        $item->save();

        $item = new Menu();
        $item->block_id = $block->id;
        $item->item = $this->faker->word;
        $item->parent = null;
        $item->route = '';
        $item->url = null;
        $item->orderNo = 2;

        $item->save();

        $items = Menu::where('block_id', $block->id)->get();
        $firstItem = $items->first();
        $secondItem = $items->last();

        $item = new Menu();
        $item->block_id = $block->id;
        $item->item = $this->faker->word;
        $item->parent = $secondItem->id;
        $item->route = '';
        $item->url = null;
        $item->orderNo = 1;

        $item->save();

        $item = new Menu();
        $item->block_id = $block->id;
        $item->item = $this->faker->word;
        $item->parent = $secondItem->id;
        $item->route = '';
        $item->url = null;
        $item->orderNo = 2;

        $item->save();

        $secondItems = Menu::where('block_id', $block->id)->where('parent', $secondItem->id)->get();
        $second_firstItem = $secondItems->first();
        $second_secondItem = $secondItems->last();

        $item = new Menu();
        $item->block_id = $block->id;
        $item->item = $this->faker->word;
        $item->parent = null;
        $item->route = '';
        $item->url = null;
        $item->orderNo = 3;

        $item->save();

        $items = Menu::where('block_id', $block->id)->get();
        $thirdItem = $items->last();

        // test moving item up
        $this->post('settings/block/item/moveup', [
            'block_id' => $block->id,
            'id' => $thirdItem->id
        ]);

        $thirdItem = Menu::find($thirdItem->id);

        $this->assertEquals($assertion ? 2 : 3, $thirdItem->orderNo);

        // test minimum order value on moving up
        $this->post('settings/block/item/moveup', [
            'block_id' => $block->id,
            'id' => $firstItem->id
        ]);

        $firstItem = Menu::find($firstItem->id);

        $this->assertEquals(1, $firstItem->orderNo);

        // test moving item down
        $this->post('settings/block/item/movedown', [
            'block_id' => $block->id,
            'id' => $thirdItem->id
        ]);

        $thirdItem = Menu::find($thirdItem->id);

        $this->assertEquals(3, $thirdItem->orderNo);

        // test maximum order value on moving down
        $this->post('settings/block/item/movedown', [
            'block_id' => $block->id,
            'id' => $thirdItem->id
        ]);

        $thirdItem = Menu::find($thirdItem->id);

        $this->assertEquals(3, $thirdItem->orderNo);

        // test moving up in submenu
        $this->post('settings/block/item/moveup', [
            'block_id' => $block->id,
            'id' => $second_secondItem->id
        ]);

        $second_secondItem = Menu::find($second_secondItem->id);
        $second_firstItem = Menu::find($second_firstItem->id);

        $this->assertEquals(1, $second_secondItem->orderNo);
        $this->assertEquals(2, $second_firstItem->orderNo);
    }
}
