<?php

namespace Just\Tests\Feature\Blocks\Link;

use Just\Models\Blocks\Contact;
use Just\Models\Blocks\Link;
use Just\Models\Blocks\Text;
use Just\Models\Page;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;

class Actions extends LocationBlock {

    use WithFaker;

    protected $type = 'link';

    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }

        \Just\Models\System\Route::where('id', '>', 1)->delete();

        parent::tearDown();
    }

    public function access_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $response->assertSuccessful();

            $form = $block->item()->itemForm();
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'linkedBlock_id', 'submit'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $textBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $block = $this->setupBlock();

        $textItem = new Text();
        $textItem->block_id = $textBlock->id;
        $textItem->text = $text = $this->faker->paragraph;

        $textItem->save();

        Link::insert([
            'block_id' => $block->id,
            'linkedBlock_id' => $textBlock->id,
        ]);

        $item = Link::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'linkedBlock_id', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($textBlock->id, $form->getElement('linkedBlock_id')->value());
            $this->assertEquals($textBlock->id, $item->linkedBlock()->id);
        }
        else{
            $this->assertEquals(0, $item->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $textBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $route = \Just\Models\System\Route::create([
            'route' => 'mirror-'.$this->faker->word,
            'type' => 'page'
        ]);

        $page = Page::create([
            'title' => 'Mirror',
            'route' => $route->route,
            'layout_id' => 1
        ]);

        $this->app['router']->get($route->route, "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/'.$route->route, "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);

        $block = $this->setupBlock(['page_id'=>$page->id]);

        $textItem = new Text();
        $textItem->block_id = $textBlock->id;
        $textItem->text = $text = $this->faker->paragraph;

        $textItem->save();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'linkedBlock_id' => $textBlock->id
        ]);

        $item = Link::all()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($textBlock->id, $block->firstItem()->linkedBlock_id);
        }
        else{
            $this->assertNull($item);
        }
    }

    public function receive_an_error_on_sending_incomplete_create_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null
        ]);

        $item = Link::all()->last();

        $response->assertRedirect();

        $this->assertNull($item);

        if($assertion){
            $response->assertSessionHasErrors('linkedBlock_id');
        }
        else{
            $response->assertRedirect('login');
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $textBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        $contactBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact', 'parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":100}')])->specify();
        $route = \Just\Models\System\Route::create([
            'route' => $path = 'mirror-'.$this->faker->word,
            'type' => 'page'
        ]);

        $page = Page::create([
            'title' => 'Mirror',
            'route' => $route->route,
            'layout_id' => 1
        ]);

        $this->app['router']->get($route->route, "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/'.$route->route, "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);

        $block = $this->setupBlock(['page_id'=>$page->id]);

        $textItem = new Text();
        $textItem->block_id = $textBlock->id;
        $textItem->text = $text = $this->faker->paragraph;

        $textItem->save();

        $envelope = str_replace("\n", ", ", $this->faker->address);
        $phone = $this->faker->phoneNumber;
        $at = $this->faker->email;

        $item = new Contact();
        $item->block_id = $contactBlock->id;
        $item->title = $title = $this->faker->sentence;
        $item->channels = '{"envelope":"'.$envelope.'","phone":"'.$phone.'","at":"'.$at.'"}';
        $item->save();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'linkedBlock_id' => $textBlock->id
        ]);

        $item = Link::all()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($textBlock->id, $block->firstItem()->linkedBlock_id);

            $this->post("settings/block/item/save", [
                'block_id' => $block->id,
                'id' => $item->id,
                'linkedBlock_id' => $contactBlock->id
            ]);

            $item = Link::all()->last();

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

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            $this->assertCount(1, $form->groups());

            $this->assertEquals(['id', 'orderDirection', 'submit'], $form  ->names());

            $this->post('settings/block/customize', [
                "id" => $block->id,
                'orderDirection' => 'asc'
            ]);

            $block = Block::find($block->id);

            $this->assertEquals('asc', $block->parameters->orderDirection);
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                'orderDirection' => 'asc'
            ]);

            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }
}
