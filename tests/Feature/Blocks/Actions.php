<?php

namespace Just\Tests\Feature\Blocks;

use Illuminate\Support\Facades\Auth;
use Just\Models\AddOn;
use Just\Models\Block;
use Just\Models\Blocks\AddOns\AddOnOption;
use Just\Models\Blocks\Gallery;
use Just\Models\Blocks\Text;
use Just\Tests\Feature\Helper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Just\Models\User;

class Actions extends TestCase{

    use WithFaker;
    use Helper;

    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }

        parent::tearDown();
    }

    public function create_new_block($assertion){
        $title = $this->faker->sentence;
        $description = $this->faker->paragraph;

        $request = [
            'block_id' => null,
            'page_id' => 1, // default home page
            'type' => 'text',
            'panelLocation' => 'content', // default content
            'title' => $title,
            'description' => $description,
            'width' => 12
        ];

        if(Auth::check() and Auth::user()->role == "master"){
            $request['layoutClass'] = null; // default class should be primary
            $request['cssClass'] = "";
        }

        $response = $this->get('settings/page/1/panel/content');
        if($assertion){
            $response->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }

        $response = $this->get('settings/page/1/panel/content/block/create');
        if($assertion){
            $response->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }

        $this->post('settings/block/setup', $request);

        $block = Block::all()->last();

        if($assertion){
            $this->assertNotNull($block);

            $this->assertNull($block->name);
            $this->assertEquals($title, $block->title);
            $this->assertEquals($description, $block->description);
            $this->assertEquals('primary', $block->layoutClass);
            $this->assertEquals('', $block->cssClass);

            $this->get('admin')
                ->assertSee('div class="block text col-md-12  ">', false)
                ->assertSee($title)
                ->assertSee($description);

            $this->get('')
                ->assertSee('div class="block text col-md-12  ">', false)
                ->assertSee($title)
                ->assertSee($description);

            $block->delete();
        }
        else{
            $this->assertNull($block);

            $this->get('admin')
                ->assertDontSee('div class="block text col-md-12  >', false)
                ->assertDontSee($title)
                ->assertDontSee($description);

            $this->get('')
                ->assertDontSee('div class="block text col-md-12  >', false)
                ->assertDontSee($title)
                ->assertDontSee($description);
        }
    }

    public function change_blocks_order($assertion){
        $htmlBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'html', 'orderNo'=>1]);
        $textBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text', 'orderNo'=>2]);
        $spaceBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'space', 'orderNo'=>3]);

        $response = $this->post("settings/block/moveup", [
            "block_id" => $spaceBlock->id,
            "id" => $spaceBlock->id
        ]);

        $htmlBlock = Block::find($htmlBlock->id);
        $textBlock = Block::find($textBlock->id);
        $spaceBlock = Block::find($spaceBlock->id);

        if($assertion){
            $response->assertSuccessful();
            $this->assertEquals(3, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
            $this->assertEquals(2, $spaceBlock->orderNo);
        }
        else{
            $response->assertRedirect();
            $this->assertEquals(2, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
            $this->assertEquals(3, $spaceBlock->orderNo);
        }

        $this->post("settings/block/movedown", [
            "block_id" => $htmlBlock->id,
            "id" => $spaceBlock->id
        ]);

        $htmlBlock = Block::find($htmlBlock->id);
        $textBlock = Block::find($textBlock->id);
        $spaceBlock = Block::find($spaceBlock->id);

        if($assertion) {
            $this->assertEquals(3, $textBlock->orderNo);
            $this->assertEquals(2, $htmlBlock->orderNo);
            $this->assertEquals(1, $spaceBlock->orderNo);
        }
        else{
            $this->assertEquals(2, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
            $this->assertEquals(3, $spaceBlock->orderNo);
        }
    }

    public function deactivate_block($assertion){
        $textBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text'])->specify();

        $block = new Text();
        $block->block_id = $textBlock->id;
        $block->text = $text = $this->faker->paragraph;

        $block->save();

        $this->post("settings/block/deactivate", [
            "block_id" => $textBlock->id,
            "id" => $textBlock->id
        ]);

        $response = $this->get('/');

        if($assertion){
            $response->assertDontSee($text);
        }
        else{
            $response->assertSee($text);
        }

        $response = $this->get('/admin');

        if($assertion){
            $response->assertSee($text);
        }
        else{
            $response->assertRedirect();
        }

    }

    public function activate_block($assertion){
        $textBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text', 'isActive'=>0])->specify();

        $block = new Text();
        $block->block_id = $textBlock->id;
        $block->text = $text = $this->faker->paragraph;

        $block->save();

        $this->post("settings/block/activate", [
            "block_id" => $textBlock->id,
            "id" => $textBlock->id,
        ]);

        $response = $this->get('/');

        if($assertion){
            $response->assertSee($text);
        }
        else{
            $response->assertDontSee($text);
        }

        $response = $this->get('/admin');

        if($assertion){
            $response->assertSee($text);
        }
        else{
            $response->assertRedirect();
        }
    }

    public function delete_block($assertion){
        $textBlock = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text'])->specify();

        $block = new Text();
        $block->block_id = $textBlock->id;
        $block->text = $text = $this->faker->paragraph;

        $block->save();

        $this->post("settings/block/delete", [
            "block_id" => $textBlock->id,
            "id" => $textBlock->id,
        ]);

        $response = $this->get('/');

        if($assertion){
            $response->assertDontSee($text);
        }
        else{
            $response->assertSee($text);
        }
    }

    public function change_items_order_in_the_block($assertion){
        $block = Block::factory()->create();

        $items = function() use ($block){
            $items = [];
            for($i=1; $i<=3; $i++){
                $textblock = new Text();
                $textblock->block_id = $block->id;
                $textblock->text = $this->faker->paragraph;
                $textblock->orderNo = $i;

                $textblock->save();
            }

            return $items;
        };

        $items();

        $firstItem = Text::first();
        $lastItem = Text::all()->last();

        $this->assertEquals(1, $firstItem->orderNo);
        $this->assertEquals(3, $lastItem->orderNo);

        // test moving item up
        $this->post('settings/block/item/moveup', [
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);

        $lastItem = Text::find($lastItem->id);

        $this->assertEquals($assertion ? 2 : 3, $lastItem->orderNo);

        // test minimum order value on moving up
        $this->post('settings/block/item/moveup', [
            'block_id' => $block->id,
            'id' => $firstItem->id
        ]);

        $firstItem = Text::find($firstItem->id);

        $this->assertEquals(1, $firstItem->orderNo);

        // test moving item down
        $this->post('settings/block/item/movedown', [
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);

        $lastItem = Text::find($lastItem->id);

        $this->assertEquals(3, $lastItem->orderNo);

        // test maximum order value on moving down
        $this->post('settings/block/item/movedown', [
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);

        $lastItem = Text::find($lastItem->id);

        $this->assertEquals(3, $lastItem->orderNo);
    }

    public function deactivate_item_in_the_block($assertion){
        $block = Block::factory()->create();

        $textBlock = new Text();
        $textBlock->block_id = $block->id;
        $textBlock->text = $text = $this->faker->paragraph;

        $textBlock->save();

        $item = Text::first();

        $this->post("settings/block/item/deactivate", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);

        $item = Text::find($item->id);

        $this->assertEquals($assertion ? 0 : 1, $item->isActive);

        if($assertion){
            $this->get('admin')
                    ->assertSee($item->text);
        }

        $this->get('')
                ->{($assertion ? 'assertDontSee' : 'assertSee')}($item->text);

        $this->post("settings/block/item/activate", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);

        $item = Text::find($item->id);

        $this->assertEquals(1, $item->isActive);

        $this->get('')
                ->assertSee($item->text);
    }

    public function delete_item_in_the_block($assertion){
        $block = Block::factory()->create();

        $textBlock = new Text();
        $textBlock->block_id = $block->id;
        $textBlock->text = $text = $this->faker->paragraph;

        $textBlock->save();

        $item = Text::first();

        $this->post("settings/block/item/delete", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);

        $this->{($assertion ? 'assertEmpty' : 'assertNotEmpty')}(Text::count());
    }

    public function delete_item_in_the_block_with_image($assertion){
        $block = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'gallery']);

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ])
            ->assertSuccessful();

        if(!$assertion){
            Auth::logout();
        }

        $item = Gallery::all()->last();

        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));

        $this->post("settings/block/item/delete", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);

        $this->{($assertion ? 'assertEmpty' : 'assertNotEmpty')}(Gallery::count());

        if($assertion){
            $this->assertFileDoesNotExist(public_path('storage/photos/'.$item->image.'.png'));
        }
        else{
            $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
            unlink(public_path('storage/photos/'.$item->image.'.png'));
        }
    }

    public function add_related_block_to_the_item($assertion){
        $this->addWarning('Related blocks are not implemented yet');
/*
        $block = Block::factory()->create()->specify();

        $textblock = new Text();
        $textblock->block_id = $block->id;
        $textblock->text = $text = $this->faker->paragraph;

        $textblock->save();

        $item = Text::all()->last();

        $this->post("admin/settings/relations/create", [
            "block_id" => $block->id,
            "id" => $item->id,
            "relatedBlockName" => "text",
            "title" => $title = $this->faker->sentence,
            "description" => $description = $this->faker->paragraph
        ]);

        if($assertion){
            $relatedBlock = $block->firstItem()->relatedBlocks->first();

            $this->assertNotNull($relatedBlock);
            $relatedBlock = $relatedBlock->specify();

            $this->assertEquals($title, $relatedBlock->title);
            $this->assertEquals($description, $relatedBlock->description);
        }
        else{
            $this->assertTrue($block->firstItem()->relatedBlocks->isEmpty());
        }
*/
    }

    public function add_item_to_related_block($assertion){
        $this->addWarning('Related blocks are not implemented yet');
        /*
        $block = Block::factory()->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $textblock = new Block\Text();
        $textblock->block_id = $block->id;
        $textblock->text = $text = $this->faker->paragraph;

        $textblock->save();

        $item = Block\Text::all()->last();

        $relatedBlock = factory(Block::class)->create(['panelLocation'=>null, 'page_id'=>null, 'parent'=>$block->id, 'title'=>$title = $this->faker->sentence])->specify();

        DB::table('texts_blocks')->insert([
            'modelItem_id' => $item->id,
            'block_id' => $relatedBlock->id
        ]);

        if($assertion){
            $this->get("admin/settings/".$block->id."/".$item->id)
                    ->assertSee($title." (text)");

            $this->get("admin/settings/".$relatedBlock->id."/0")
                    ->assertSuccessful();

            $this->post("", [
                'block_id' => $relatedBlock->id,
                'id' => null,
                'text' => $relatedText = $this->faker->paragraph,
            ]);

            $this->assertEquals($relatedText, $item->relatedBlock('text')->specify()->firstItem()->text);
            $this->assertEquals($relatedText, $item->relatedBlocks->first()->specify()->firstItem()->text);
        }
        else{
            $this->get("admin/settings/".$block->id."/".$item->id)
                    ->assertRedirect();

            $this->get("admin/settings/".$relatedBlock->id."/0")
                    ->assertRedirect();

            $this->post("", [
                'block_id' => $relatedBlock->id,
                'id' => null,
                'text' => $relatedText = $this->faker->paragraph,
            ]);

            $this->assertNull($item->relatedBlock('text')->firstItem());
        }
        */
    }

    public function access_data_from_related_block(){
        $this->addWarning('Related blocks are not implemented yet');
        /*
        $block = Block::factory()->create()->specify();

        $textblock = new Block\Text();
        $textblock->block_id = $block->id;
        $textblock->text = $this->faker->paragraph;

        $textblock->save();

        $item = Block\Text::all()->last();

        $relatedBlock = factory(Block::class)->create(['panelLocation'=>null, 'page_id'=>null, 'parent'=>$block->id, 'name'=>$name = $this->faker->word])->specify();

        DB::table('texts_blocks')->insert([
            'modelItem_id' => $item->id,
            'block_id' => $relatedBlock->id
        ]);

        $relTextblock = new Block\Text();
        $relTextblock->block_id = $relatedBlock->id;
        $relTextblock->text = $relText = $this->faker->paragraph;

        $relTextblock->save();

        $this->assertEquals($relText, $item->relatedBlock('text', $name)->firstItem()->text);
        $this->assertEquals($relText, $item->relatedBlock('text', null, $relatedBlock->id)->firstItem()->text);
        $this->assertNull($item->relatedBlock('text', null, 0));
        */
    }

    public function access_parent_block_from_the_related_one(){
        $this->addWarning('Related blocks are not implemented yet');
        /*
        $block = Block::factory()->create()->specify();

        $textblock = new Text();
        $textblock->block_id = $block->id;
        $textblock->text = $text = $this->faker->paragraph;

        $textblock->save();

        $item = Text::all()->last();

        $relatedBlock = factory(Block::class)->create(['panelLocation'=>null, 'page_id'=>null, 'parent'=>$block->id, 'title'=>$title = $this->faker->sentence])->specify();

        DB::table('texts_blocks')->insert([
            'modelItem_id' => $item->id,
            'block_id' => $relatedBlock->id
        ]);

        $relTextblock = new Text();
        $relTextblock->block_id = $relatedBlock->id;
        $relTextblock->text = $relText = $this->faker->paragraph;

        $relTextblock->save();

        $this->assertCount(1, $block->firstItem()->relatedBlocks);
        $this->assertEquals($relatedBlock->parentBlock()->id, $block->id);
        $this->assertEquals($relatedBlock->parentBlock(true)->id, $block->id);
        $this->assertEquals($relatedBlock->parentItem()->id, $item->id);
        $this->assertNull($block->parentItem());
        */
    }

    public function update_block_settings($assertion){
        $block = Block::factory()->create();

        $this->post('settings/block/setup', [
            'block_id' => $block->id,
            'name' => '',
            'title' => $title = $this->faker->word,
            'description' => $description = $this->faker->paragraph,
            'width' => 6,
            'layoutClass' => 'primary',
            'cssClass' => $class = $this->faker->word
        ]);

        $block = Block::all()->last();

        if($assertion){
            $this->assertEquals($title, $block->title);
            $this->assertEquals($description, $block->description);
            $this->assertEquals(6, $block->width);
            $this->assertEquals('primary', $block->layoutClass);
            if(Auth::user()->role == 'master') {
                $this->assertEquals($class, $block->cssClass);
            }
            else{
                $this->assertEquals('', $block->cssClass);
            }
        }
        else{
            $this->assertNotEquals($title, $block->title);
            $this->assertNotEquals($description, $block->description);
            $this->assertNotEquals(6, $block->width);
        }

        $this->assertNull($block->name);
    }

    public function update_block_unique_name($assertion){
        $block = Block::factory()->create();

        $this->post('settings/block/setup', [
            'block_id' => $block->id,
            'name' => $name = $this->faker->word,
            'title' => $block->title,
            'description' => $block->description,
            'width' => 12
        ]);

        $block = Block::all()->last();

        if($assertion){
            $this->assertEquals($name, $block->name);
        }
        else{
            $this->assertNotEquals($name, $block->name);
        }

        $this->assertEquals('primary', $block->layoutClass);
        $this->assertEquals('', $block->cssClass);
    }

    public function create_new_block_with_name($assertion){
        $request = [
            'panelLocation' => 'content', // default content
            'block_id' => null,
            'page_id' => 1, // default home page
            'name' => $name = $this->faker->name,
            'type' => 'text',
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'width' => 12
        ];

        $this->post('settings/block/setup', $request);

        $block = Block::all()->last();

        if($assertion){
            $this->assertNotNull($block);

            $this->assertEquals($name, $block->name);

            $block->delete();
        }
        else{
            $this->assertNull($block);
        }
    }

    public function cannot_create_block_with_existing_name(){
        Block::factory()->create(['name'=>$name = $this->faker->word]);

        $request = [
            'panel_id' => 2, // default content
            'block_id' => null,
            'page_id' => 1, // default home page
            'name' => $name,
            'type' => 'text',
            'title' => $this->faker->sentence,
            'blockDescription' => $this->faker->paragraph,
            'width' => 12
        ];

        $response = $this->post('settings/block/setup', $request);

        $this->assertCount(1, Block::all());

        if(Auth::check()){
            $response->assertSessionHasErrors('name')
                ->assertRedirect();
        }
    }

    public function cannot_update_block_name_if_it_exists(){
        $block = Block::factory()->create();
        Block::factory()->create(['name'=>$name = $this->faker->word]);

        $response = $this->post('settings/block/setup', [
            'block_id' => $block->id,
            'name' => $name,
            'title' => $block->title,
            'description' => $block->description,
            'width' => 12,
            'layoutClass' => 'primary',
            'cssClass' => ''
        ]);

        if(Auth::check()){
            $response->assertSessionHasErrors(['name']);
            $this->assertEquals(session('errors')->get('name')[0], 'The name has already been taken.');
        }
    }

    public function update_block_with_keeping_name_value($assertion){
        $block = Block::factory()->create(['name'=>$name = $this->faker->word]);

        $this->post('settings/block/setup', [
            'block_id' => $block->id,
            'name' => $name,
            'title' => $title = $this->faker->word,
            'description' => $block->description,
            'width' => 12,
            'layoutClass' => 'primary',
            'cssClass' => ''
        ]);

        $block = Block::find($block->id);

        if($assertion){
            $this->assertEquals($title, $block->title);
        }
        else{
            $this->assertNotEquals($title, $block->title);
        }
    }

    public function get_items_from_the_current_category() {
        $block = Block::factory()
            ->has(AddOn::factory()->type('category')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $firstOption = AddOnOption::create([
            'add_on_id' => $addon->id,
            'option' => '{"en":"'.$this->faker->word.'"}'
        ]);

        $secondOption = AddOnOption::create([
            'add_on_id' => $addon->id,
            'option' => '{"en":"'.($optionTitle2 = $this->faker->word).'"}'
        ]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => $firstOption->id,
            'text' => '{"en":"'.($text1 = $this->faker->paragraph).'"}'
        ]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => $secondOption->id,
            'text' => '{"en":"'.($text2 = $this->faker->paragraph).'"}'
        ]);

        $this->get("/?category=".$firstOption->id)
            ->assertSee($text1)
            ->assertDontSee($text2);

        $this->get("/?category=".$secondOption->id)
            ->assertSee($text2)
            ->assertDontSee($text1);

        $this->removePivotTable($block, $addon);
    }

    public function get_nullable_value_on_empty_addon_string() {
        $block = Block::factory()
            ->has(AddOn::factory()->type('phrase')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $this->faker->paragraph,
            $name."_".$addon->id => ""
        ]);

        $item = Text::all()->last();

        $this->assertNull($item->{$addon->name});

        $this->removePivotTable($block, $addon);
    }
}
