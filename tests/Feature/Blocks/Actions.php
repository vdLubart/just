<?php

namespace Lubart\Just\Tests\Feature\Blocks;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;
use Lubart\Just\Models\User;
use Illuminate\Support\Facades\DB;

class Actions extends TestCase{
    
    use WithFaker;
    
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
            'panel_id' => 2, // default content
            'block_id' => null,
            'page_id' => 1, // default home page
            'type' => 'text',
            'title' => $title,
            'blockDescription' => $description,
            'width' => 12
        ];
        
        if(\Auth::check() and \Auth::user()->role == "master"){
            $request['layoutClass'] = null; // default class should be primary
            $request['cssClass'] = "";
        }
        
        $response = $this->get('admin/settings/panel/1/content/');
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: Panel");
        }
        else{
            $response->assertRedirect();
        }
        
        
        $this->post('admin/settings/panel/setup', $request);
        
        $block = Block::all()->last();
        
        if($assertion){
            $this->assertNotNull($block);
            
            $this->assertNull($block->name);
            $this->assertEquals($title, $block->title);
            $this->assertEquals($description, $block->description);
            $this->assertEquals('primary', $block->layoutClass);
            $this->assertEquals('', $block->cssClass);
            
            $this->get('admin')
                ->assertSee('div class="block text col-md-12  ">')
                ->assertSee($title)
                ->assertSee($description);
            
            $this->get('')
                ->assertSee('div class="block text col-md-12  ">')
                ->assertSee($title)
                ->assertSee($description);
            
            $block->delete();
        }
        else{
            $this->assertNull($block);
            
            $this->get('admin')
                ->assertDontSee('div class="block text col-md-12  >')
                ->assertDontSee($title)
                ->assertDontSee($description);
            
            $this->get('')
                ->assertDontSee('div class="block text col-md-12  >')
                ->assertDontSee($title)
                ->assertDontSee($description);
        }
    }
    
    public function change_blocks_order($assertion){
        $htmlBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'html', 'orderNo'=>1]);
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text', 'orderNo'=>2]);
        $spaceBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'space', 'orderNo'=>3]);
        
        $this->post("/admin/moveup", [
            "block_id" => $spaceBlock->id,
            "id" => 0
        ]);
        
        $htmlBlock = Block::find($htmlBlock->id);
        $textBlock = Block::find($textBlock->id);
        $spaceBlock = Block::find($spaceBlock->id);
        
        if($assertion){
            $this->assertEquals(3, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
            $this->assertEquals(2, $spaceBlock->orderNo);
        }
        else{
            $this->assertEquals(2, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
            $this->assertEquals(3, $spaceBlock->orderNo);
        }
        
        $this->post("/admin/movedown", [
            "block_id" => $htmlBlock->id,
            "id" => 0
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

        $this->post("/admin/moveto", [
            "block_id" => $spaceBlock->id,
            "id" => 0,
            'newPosition' => 3,
        ]);

        $htmlBlock = Block::find($htmlBlock->id);
        $textBlock = Block::find($textBlock->id);
        $spaceBlock = Block::find($spaceBlock->id);

        if($assertion) {
            $this->assertEquals(2, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
            $this->assertEquals(3, $spaceBlock->orderNo);
        }
        else{
            $this->assertEquals(2, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
            $this->assertEquals(3, $spaceBlock->orderNo);
        }
    }
    
    public function deactivate_block($assertion){
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text'])->specify();

        $block = new Block\Text();
        $block->block_id = $textBlock->id;
        $block->text = $text = $this->faker->paragraph;

        $block->save();
        
        $this->post("/admin/deactivate", [
            "block_id" => $textBlock->id,
            "id" => 0
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
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text', 'isActive'=>0])->specify();

        $block = new Block\Text();
        $block->block_id = $textBlock->id;
        $block->text = $text = $this->faker->paragraph;

        $block->save();
        
        $this->post("/admin/activate", [
            "block_id" => $textBlock->id,
            "id" => 0
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
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'text'])->specify();

        $block = new Block\Text();
        $block->block_id = $textBlock->id;
        $block->text = $text = $this->faker->paragraph;

        $block->save();
        
        $this->post("/admin/delete", [
            "block_id" => $textBlock->id,
            "id" => 0
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
        $block = factory(Block::class)->create();
        
        $items = function() use ($block){
            $items = [];
            for($i=1; $i<=3; $i++){
                $textblock = new Block\Text();
                $textblock->block_id = $block->id;
                $textblock->text = $this->faker->paragraph;
                $textblock->orderNo = $i;

                $textblock->save();
            }
            
            return $items;
        };

        $items();
        
        $firstItem = Block\Text::first();
        $lastItem = Block\Text::all()->last();
        
        $this->assertEquals(1, $firstItem->orderNo);
        $this->assertEquals(3, $lastItem->orderNo);
        
        // test moving item up
        $this->post('admin/moveup', [
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);
        
        $lastItem = Block\Text::find($lastItem->id);
        
        $this->assertEquals($assertion ? 2 : 3, $lastItem->orderNo);
        
        // test minimum order value on moving up
        $this->post('admin/moveup', [
            'block_id' => $block->id,
            'id' => $firstItem->id
        ]);
        
        $firstItem = Block\Text::find($firstItem->id);
        
        $this->assertEquals(1, $firstItem->orderNo);
        
        // test moving item down
        $this->post('admin/movedown', [
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);
        
        $lastItem = Block\Text::find($lastItem->id);
        
        $this->assertEquals(3, $lastItem->orderNo);
        
        // test maximum order value on moving down
        $this->post('admin/movedown', [
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);
        
        $lastItem = Block\Text::find($lastItem->id);
        
        $this->assertEquals(3, $lastItem->orderNo);
        
        // test drop item to some position
        $this->post('admin/moveto', [
            'newPosition' => 1,
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);
        
        $lastItem = Block\Text::find($lastItem->id);
        $firstItem = Block\Text::find($firstItem->id);
        
        $this->assertEquals($assertion ? 1 : 3, $lastItem->orderNo);
        $this->assertEquals($assertion ? 2 : 1, $firstItem->orderNo);
        
        // test drop item to some position
        $this->post('admin/moveto', [
            'newPosition' => 3,
            'block_id' => $block->id,
            'id' => $lastItem->id
        ]);
        
        $lastItem = Block\Text::find($lastItem->id);
        $firstItem = Block\Text::find($firstItem->id);
        
        $this->assertEquals(3, $lastItem->orderNo);
        $this->assertEquals(1, $firstItem->orderNo);
    }
    
    public function deactivate_item_in_the_block($assertion){
        $block = factory(Block::class)->create();

        $textBlock = new Block\Text();
        $textBlock->block_id = $block->id;
        $textBlock->text = $text = $this->faker->paragraph;

        $textBlock->save();
        
        $item = Block\Text::first();
        
        $this->post("admin/deactivate", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);
        
        $item = Block\Text::find($item->id);
        
        $this->assertEquals($assertion ? 0 : 1, $item->isActive);
        
        if($assertion){
            $this->get('admin')
                    ->assertSee($item->text);
        }
        
        $this->get('')
                ->{($assertion ? 'assertDontSee' : 'assertSee')}($item->text);
                
        $this->post("admin/activate", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);
        
        $item = Block\Text::find($item->id);
        
        $this->assertEquals(1, $item->isActive);
        
        $this->get('')
                ->assertSee($item->text);
    }
    
    public function delete_item_in_the_block($assertion){
        $block = factory(Block::class)->create();

        $textBlock = new Block\Text();
        $textBlock->block_id = $block->id;
        $textBlock->text = $text = $this->faker->paragraph;

        $textBlock->save();
        
        $item = Block\Text::first();
        
        $this->post("admin/delete", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);
        
        $this->{($assertion ? 'assertEmpty' : 'assertNotEmpty')}(Block\Text::count());
    }
    
    public function delete_item_in_the_block_with_image($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'gallery']);
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $this->post("admin/ajaxuploader", [
            'block_id' => $block->id,
            'id' => null,
            'ax_file_input' => UploadedFile::fake()->image('photo.jpg'),
            'ax-max-file-size' => '100M',
            'ax-file-path' => '../storage/app/public/photos',
            'ax-allow-ext' => 'jpg|png|jpeg',
            'ax-override' => true,
            'startUpload' => "Upload images"
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
        
        $item = Block\Gallery::all()->last();
        
        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
        
        $this->post("admin/delete", [
            'block_id' => $block->id,
            'id' => $item->id
        ]);
        
        $this->{($assertion ? 'assertEmpty' : 'assertNotEmpty')}(Block\Gallery::count());
        
        if($assertion){
            $this->assertFileNotExists(public_path('storage/photos/'.$item->image.'.png'));
        }
        else{
            $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
            unlink(public_path('storage/photos/'.$item->image.'.png'));
        }
    }
    
    public function add_related_block_to_the_item($assertion){
        $block = factory(Block::class)->create()->specify();

        $textblock = new Block\Text();
        $textblock->block_id = $block->id;
        $textblock->text = $text = $this->faker->paragraph;

        $textblock->save();
        
        $item = Block\Text::all()->last();
        
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
    }
    
    public function add_item_to_related_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

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
    }
    
    public function access_data_from_related_block(){
        $block = factory(Block::class)->create()->specify();

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
    }
    
    public function access_parent_block_from_the_related_one(){
        $block = factory(Block::class)->create()->specify();

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

        $relTextblock = new Block\Text();
        $relTextblock->block_id = $relatedBlock->id;
        $relTextblock->text = $relText = $this->faker->paragraph;

        $relTextblock->save();
        
        $this->assertCount(1, $block->firstItem()->relatedBlocks);
        $this->assertEquals($relatedBlock->parentBlock()->id, $block->id);
        $this->assertEquals($relatedBlock->parentBlock(true)->id, $block->id);
        $this->assertEquals($relatedBlock->parentItem()->id, $item->id);
        $this->assertNull($block->parentItem());
    }
    
    public function update_block_data($assertion){
        $block = factory(Block::class)->create();
        
        $this->post('admin/settings/block/setup', [
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
            $this->assertEquals($class, $block->cssClass);
        }
        else{
            $this->assertNotEquals($title, $block->title);
            $this->assertNotEquals($description, $block->description);
            $this->assertNotEquals(6, $block->width);
            $this->assertNotEquals($class, $block->cssClass);
        }
        
        $this->assertNull($block->name);
    }
    
    public function update_block_unique_name($assertion){
        $block = factory(Block::class)->create();
        
        $this->post('admin/settings/block/setup', [
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
            'panel_id' => 2, // default content
            'block_id' => null,
            'page_id' => 1, // default home page
            'name' => $name = $this->faker->name,
            'type' => 'text',
            'title' => $this->faker->sentence,
            'blockDescription' => $this->faker->paragraph,
            'width' => 12
        ];
        
        $this->post('admin/settings/panel/setup', $request);
        
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
        factory(Block::class)->create(['name'=>$name = $this->faker->word]);
        
        $this->get('admin/settings/panel/1/content/');
        
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
        
        $response = $this->post('admin/settings/panel/setup', $request);
        
        $this->assertCount(1, Block::all());
        
        if(\Auth::check()){
            $this->followRedirects($response)
                    ->assertSee('The name has already been taken');
        }
    }
    
    public function cannot_update_block_name_if_it_exists(){
        $block = factory(Block::class)->create();
        factory(Block::class)->create(['name'=>$name = $this->faker->word]);
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post('admin/settings/block/setup', [
            'block_id' => $block->id,
            'name' => $name,
            'title' => $block->title,
            'description' => $block->description,
            'width' => 12,
            'layoutClass' => 'primary',
            'cssClass' => ''
        ]);
        
        if(\Auth::check()){
            $this->followRedirects($response)
                    ->assertSee('The name has already been taken');
        }
    }
    
    public function update_block_with_keeping_name_value($assertion){
        $block = factory(Block::class)->create(['name'=>$name = $this->faker->word]);
        
        $this->get("admin/settings/".$block->id."/0");
        
        $this->post('admin/settings/block/setup', [
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
        $block = factory(Block::class)->create(['name'=>$name = $this->faker->word])->specify();

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'categories', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $firstCategory = Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' => $this->faker->word,
            'value' => $this->faker->word
        ]);

        $secondCategory = Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' => $categoryName = $this->faker->word,
            'value' => $categoryValue = $this->faker->word
        ]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $text1 = $this->faker->paragraph,
            $name."_".$addon->id => $firstCategory->id
        ]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $text2 = $this->faker->paragraph,
            $name."_".$addon->id => $secondCategory->id
        ]);

        $this->get("/?category=".$firstCategory->value)
            ->assertSee($text1)
            ->assertDontSee($text2);

        $this->get("/?category=".$secondCategory->value)
            ->assertSee($text2)
            ->assertDontSee($text1);
    }

    public function get_nullable_value_on_empty_addon_string() {
        $block = factory(Block::class)->create(['name'=>$name = $this->faker->word])->specify();

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'text' => $text1 = $this->faker->paragraph,
            $name."_".$addon->id => ""
        ]);

        $item = Block\Text::all()->last();

        $this->assertNull($item->{$addon->name});

        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
}
