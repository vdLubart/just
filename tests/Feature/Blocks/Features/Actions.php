<?php

namespace Just\Tests\Feature\Blocks\Features;

use Illuminate\Support\Facades\Auth;
use Just\Models\Blocks\Features;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
use Just\Tools\Useful;

class Actions extends LocationBlock {

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

    public function access_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $response->assertSuccessful();

            $form = $block->item()->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals([
                "id",
                "block_id",
                "description",
                "submit",
                "icon",
                "title",
                "link"
            ], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();

        $feature = new Features();
        $feature->block_id = $block->id;
        $feature->icon_id = 1;
        $feature->title = $title = $this->faker->sentence;
        $feature->description = $description = $this->faker->paragraph;
        $feature->link = $link = $this->faker->url;

        $feature->save();

        $this->assertTrue(Useful::isRouteExists("iconset/{id}/{page?}"));

        $this->app['router']->get('iconset/{id}/{page?}', "\Just\Controllers\JustController@ajax")->middleware('web');

        $this->get("iconset/1")
                ->assertStatus(200);

        $item = Features::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['id', 'block_id', 'description', 'submit', 'icon', 'title', 'link'], array_keys($form->elements()));
            $this->assertEquals(1, $form->element('icon')->parameter('vueComponentAttrs')['value']->id);
            $this->assertEquals($title, $form->element('title')->value()['en']);
            $this->assertEquals($description, $form->element('description')->value()['en']);
            $this->assertEquals($link, $form->element('link')->value());
        }
        else{
            $this->assertEquals(0, $item->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemsInRow":"4"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'iconSet' => 1,
            'icon' => 1,
            'title' => $title = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
            'link' => $link = $this->faker->url
        ]);

        $this->assertTrue(Useful::isRouteExists("iconset/{id}/{page?}"));

        $item = Features::all()->last();

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

    public function receive_an_error_on_sending_incomplete_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemsInRow":"4"}')]);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'iconSet' => 1
        ]);

        $item = Features::all()->last();

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

        $feature = new Features();
        $feature->block_id = $block->id;
        $feature->icon_id = 1;
        $feature->title = $title = $this->faker->sentence;
        $feature->description = $description = $this->faker->paragraph;
        $feature->link = $link = $this->faker->url;

        $feature->save();

        $item = Features::all()->last();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'iconSet' => 1,
            'icon' => 1,
            'title' => $title = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
            'link' => $link
        ]);

        $item = Features::all()->last();

        if($assertion){
            $this->assertEquals($title, $item->title);
            $this->assertEquals($description, $item->description);
            $this->assertEquals($link, $item->link);
        }
        else{
            $this->assertNotEquals($title, $item->title);
        }
    }

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            if(Auth::user()->role == 'master'){

                $this->assertCount(2, $form->groups());

                $this->assertEquals(['id', 'ignoreCaption', 'ignoreDescription', 'orderDirection', 'submit'], $form->names());
            }
            else{
                $this->assertCount(1, $form->groups());

                $this->assertEquals(['id', 'orderDirection', 'submit'], $form->names());
            }

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc",
                "ignoreCaption" => "on"
            ])
                ->assertSuccessful();

            $block = Block::find($block->id)->specify();

            $form = $block->item()->itemForm();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('asc', $block->parameters->orderDirection);
                $this->assertTrue($block->parameters->ignoreCaption);
                $this->assertFalse($block->parameters->ignoreDescription);
                $this->assertNull($form->element('title'));
                $this->assertNotNull($form->element('description'));
            }
            else{
                $this->assertEquals('asc', $block->parameters->orderDirection);
                $this->assertNull(@$block->parameters->ignoreCaption);
                $this->assertNull(@$block->parameters->ignoreDescription);
                $this->assertNotNull($form->element('title'));
                $this->assertNotNull($form->element('description'));
            }

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc",
                "ignoreDescription" => "on"
            ]);

            $block = Block::find($block->id)->specify();

            $form = $block->item()->itemForm();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('asc', $block->parameters->orderDirection);
                $this->assertFalse($block->parameters->ignoreCaption);
                $this->assertTrue($block->parameters->ignoreDescription);
                $this->assertNotNull($form->element('title'));
                $this->assertNull($form->element('description'));
            }
            else{
                $this->assertEquals('asc', $block->parameters->orderDirection);
                $this->assertNull(@$block->parameters->ignoreCaption);
                $this->assertNull(@$block->parameters->ignoreDescription);
                $this->assertNotNull($form->element('title'));
                $this->assertNotNull($form->element('description'));
            }
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc",
            ]);

            $block = Block::find($block->id);

            $this->assertNotEquals(json_decode('{"orderDirection":"asc"}'), $block->parameters);
        }
    }
}
