<?php

namespace Just\Tests\Feature\Blocks\Text;

use Just\Models\Blocks\Text;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;

class Actions extends LocationBlock {

    use WithFaker;

    protected $type = 'text';

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

        $text = $this->faker->paragraph;

        $textBlock = new Text();
        $textBlock->block_id = $block->id;
        $textBlock->text = $text;

        $textBlock->save();

        $item = Text::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value()['en']);
        }
        else{
            $this->assertEquals(0, $block->item()->itemForm()->count());
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

        $item = Text::where('block_id', $block->id)->get()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
        }
        else{
            $this->assertNull($item);
        }
    }

    public function receive_an_error_on_sending_incomplete_create_item_form(){
        $block = $this->setupBlock();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
        ])
            ->assertSessionHasErrors('text')
            ->assertRedirect();

        $item = Text::where('block_id', $block->id)->get()->last();

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

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $secondText
        ]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $thirdText
        ]);

        $block = Block::find($block->id)->specify();

        if($assertion){
            $this->assertTrue(in_array($firstText, $block->content()->pluck(['text'])->toArray()));
            $this->assertTrue(in_array($secondText, $block->content()->pluck(['text'])->toArray()));
            $this->assertTrue(in_array($thirdText, $block->content()->pluck(['text'])->toArray()));
        }
        else{
            $this->assertEmpty($block->content());
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock();

        $textBlock = new Text();
        $textBlock->block_id = $block->id;
        $textBlock->text = $this->faker->paragraph;

        $textBlock->save();

        $item = Text::all()->last();

        $text = $this->faker->paragraph;

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'text' => $text
        ]);

        $item = Text::all()->last();

        if($assertion){
            $this->assertEquals($text, $item->text);
        }
        else{
            $this->assertNotEquals($text, $item->text);
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

            $this->assertEmpty((array)$block->parameters);
        }
    }
}
