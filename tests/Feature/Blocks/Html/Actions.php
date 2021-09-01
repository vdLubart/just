<?php

namespace Just\Tests\Feature\Blocks\Html;

use Just\Models\Blocks\Html;
use Just\Models\Blocks\Text;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;

class Actions extends LocationBlock {

    use WithFaker;

    protected $type = 'html';

    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }

        parent::tearDown();
    }

    public function access_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $response->assertSuccessful();

            $form = $block->item()->itemForm();
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', 'submit'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();

        $htmlItem = new Text();
        $htmlItem->block_id = $block->id;
        $htmlItem->text = $text = $this->faker->paragraph;

        $htmlItem->save();

        if($assertion){
            $form = $htmlItem->itemForm();
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', 'submit'], array_keys($form->elements()));
            $this->assertEquals($text, $form->element('text')->value()['en']);
        }
        else{
            $this->assertEquals(0, $htmlItem->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock();

        $text = $this->faker->paragraph;

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $text
        ]);

        $item = Html::where('block_id', $block->id)->first();

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

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null
        ])
            ->assertRedirect();

        if($assertion){
            $response->assertSessionHasErrors('text');
        }

        $item = Html::where('block_id', $block->id)->first();

        $this->assertNull($item);
    }

    public function create_few_items_in_block($assertion){
        $block = $this->setupBlock();

        $firstText = $this->faker->paragraph;
        $secondText = $this->faker->paragraph;
        $thirdText = $this->faker->paragraph;

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $firstText
        ]);

        $firstItem = Html::all()->last();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $secondText
        ]);

        $secondItem = Html::all()->last();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $thirdText
        ]);

        $thirdItem = Html::all()->last();

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

        $htmlItem = new Text();
        $htmlItem->block_id = $block->id;
        $htmlItem->text = $this->faker->paragraph;

        $htmlItem->save();

        $text = $this->faker->paragraph;

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $htmlItem->id,
            'text' => $text
        ]);

        $htmlItem = Html::all()->last();

        if($assertion){
            $this->assertEquals($text, $htmlItem->text);
        }
        else{
            $this->assertNotEquals($text, $htmlItem->text);
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
