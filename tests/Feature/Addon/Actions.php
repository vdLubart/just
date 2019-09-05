<?php

namespace Lubart\Just\Tests\Feature\Addon;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Just\Tests\Feature\Helper;
use Illuminate\Http\UploadedFile;
use Lubart\Just\Models\User;
use Illuminate\Support\Facades\Artisan;

class Actions extends TestCase{
    
    use WithFaker;
    use Helper;
    
    protected function tearDown(): void {
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/texts'))){
            exec('rm -rf ' . public_path('storage/texts'));
        }
        
        parent::tearDown();
    }
    
    public function add_string_addon_to_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $response = $this->get('admin/settings/addon/0');
        
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add Add-on');
        }
        else{
            if(\Auth::check()){
                $response->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $response->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'strings',
                'block_id' => $block->id,
                'title' => $title = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertNotNull($addon);
            $this->assertEquals('strings', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNull($addon);
        }
        
        $form = $block->form();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['text', $name."_".$addon->id, 'submit', 'block_id', 'id'], array_keys($form->getElements()));
            
            Artisan::call('migrate:rollback');
            
            exec('rm -rf database/migrations/*');
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['text', 'submit', 'block_id', 'id'], array_keys($form->getElements()));
        }
        else{
            $this->assertNull($form);
        }
    }
    
    public function add_paragraph_addon_to_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        if($assertion){
            $this->get('admin/settings/addon/0')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add Add-on');
        }
        else{
            if(\Auth::check()){
                $this->get('admin/settings/addon/0')
                    ->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $this->get('admin/settings/addon/0')
                        ->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'paragraphs',
                'block_id' => $block->id,
                'title' => $title = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertNotNull($addon);
            $this->assertEquals('paragraphs', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNull($addon);
        }
        
        $form = $block->form();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['text', $name."_".$addon->id, 'submit', 'block_id', 'id'], array_keys($form->getElements()));
            
            Artisan::call('migrate:rollback');
            
            exec('rm -rf database/migrations/*');
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['text', 'submit', 'block_id', 'id'], array_keys($form->getElements()));
        }
        else{
            $this->assertNull($form);
        }
    }
    
    public function add_image_addon_to_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        if($assertion){
            $this->get('admin/settings/addon/0')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add Add-on');
        }
        else{
            if(\Auth::check()){
                $this->get('admin/settings/addon/0')
                    ->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $this->get('admin/settings/addon/0')
                        ->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'images',
                'block_id' => $block->id,
                'title' => $title = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);
        
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertNotNull($addon);
            $this->assertEquals('images', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNull($addon);
        }
        
        $form = $block->form();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['text', $name."_".$addon->id, 'submit', 'block_id', 'id'], array_keys($form->getElements()));
            
            Artisan::call('migrate:rollback');
            
            exec('rm -rf database/migrations/*');
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['text', 'submit', 'block_id', 'id'], array_keys($form->getElements()));
        }
        else{
            $this->assertNull($form);
        }
    }
    
    public function add_categories_addon_to_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        if($assertion){
            $this->get('admin/settings/addon/0')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add Add-on');
        }
        else{
            if(\Auth::check()){
                $this->get('admin/settings/addon/0')
                    ->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $this->get('admin/settings/addon/0')
                        ->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'categories',
                'block_id' => $block->id,
                'title' => $title = $this->faker->word,
                'description' => $description = $this->faker->paragraph
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertNotNull($addon);
            $this->assertEquals('categories', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNull($addon);
        }
        
        $form = $block->form();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['text', $name."_".$addon->id, 'submit', 'block_id', 'id'], array_keys($form->getElements()));
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['text', 'submit', 'block_id', 'id'], array_keys($form->getElements()));
        }
        else{
            $this->assertNull($form);
        }
        
        if($assertion or \Auth::check()){
            $revertPivot = false;
            if(is_null($addon)){
                $addon = Block\Addon::create([
                    'block_id' => $block->id,
                    'type' => 'categories',
                    'name' => $name = $this->faker->word,
                    'title' => $title = $this->faker->word
                ]);
                $addonItem = $addon->addon();
                $addonTable = (new $addonItem)->getTable();
                
                $revertPivot = true;
                $this->createPivotTable($block->model()->getTable(), $addonTable);
            }
            
            $this->get('admin/settings/category/0')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Categories :: Create New Category');
            
            $this->post('admin/settings/category/setup', [
                'category_id' => null,
                'addon_id' => $addon->id,
                'name' => $catName = $this->faker->word,
                'value' => $catTitle = $this->faker->word
            ]);
            
            $category = Block\Addon\Categories::all()->last();
            
            $this->get('admin/settings/category/list')
                    ->assertSee($catName)
                    ->assertSee($catTitle);
            
            $form = $block->specify()->form();
            
            $this->assertEquals([$category->id=>$catName], $form->getElement($name."_".$addon->id)->options());
            
            // Update category value
            $this->post('admin/settings/category/setup', [
                'category_id' => $category->id,
                'addon_id' => $addon->id,
                'name' => $catName = $this->faker->word,
                'value' => $catTitle = $this->faker->word
            ]);
            
            $category = Block\Addon\Categories::all()->last();
            
            $this->get('admin/settings/category/list')
                    ->assertSee($catName)
                    ->assertSee($catTitle);
            
            $form = $block->specify()->form();
            
            $this->assertEquals([$category->id=>$catName], $form->getElement($name."_".$addon->id)->options());
            
            if($revertPivot){
                $this->removePivotTable($block->model()->getTable(), $addonTable);
            }
            else{
                Artisan::call('migrate:rollback');
                exec('rm -rf database/migrations/*');
            }
        }
        else{
            $this->get('admin/settings/category/0')
                ->assertRedirect();
            
            $this->get('admin/settings/category/list')
                ->assertRedirect();
            
            $addon = Block\Addon::create([
                'block_id' => $block->id,
                'type' => 'categories',
                'name' => $name = $this->faker->word,
                'title' => $title = $this->faker->word
            ]);
            
            $this->post('admin/settings/category/setup', [
                'category_id' => null,
                'addon_id' => $addon->id,
                'name' => $catName = $this->faker->word,
                'value' => $catTitle = $this->faker->word
            ]);
            
            $category = Block\Addon\Categories::all()->last();
            
            $this->assertNull($category);
        }
    }
    
    public function edit_existing_string_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if($assertion){
            $this->get('admin/settings/addon/list')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add-on list')
                    ->assertSee($title." (".$name.", strings)")
                    ->assertSee("in ".$blockTitle." (text)");
            
            $this->get('admin/settings/addon/'.$addon->id)
                    ->assertSee('select disabled="disabled" id="type"');
        }
        else{
            if(\Auth::check()){
                $this->get('admin/settings/addon/list')
                    ->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $this->get('admin/settings/addon/list')
                        ->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'strings',
                'block_id' => $block->id,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }
    }
    
    public function edit_existing_paragraph_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'paragraphs', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if($assertion){
            $this->get('admin/settings/addon/list')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add-on list')
                    ->assertSee($title." (".$name.", paragraphs)")
                    ->assertSee("in ".$blockTitle." (text)");
            
            $this->get('admin/settings/addon/'.$addon->id)
                    ->assertSee('select disabled="disabled" id="type"');
        }
        else{
            if(\Auth::check()){
                $this->get('admin/settings/addon/'.$addon->id)
                    ->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $this->get('admin/settings/addon/'.$addon->id)
                        ->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'paragraphs',
                'block_id' => $block->id,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }
    }
    
    public function edit_existing_image_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'images', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if($assertion){
            $this->get('admin/settings/addon/list')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add-on list')
                    ->assertSee($title." (".$name.", images)")
                    ->assertSee("in ".$blockTitle." (text)");
            
            $this->get('admin/settings/addon/'.$addon->id)
                    ->assertSee('select disabled="disabled" id="type"');
        }
        else{
            if(\Auth::check()){
                $this->get('admin/settings/addon/'.$addon->id)
                    ->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $this->get('admin/settings/addon/'.$addon->id)
                        ->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'images',
                'block_id' => $block->id,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }
    }
    
    public function edit_existing_categories_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'categories', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if($assertion){
            $this->get('admin/settings/addon/list')
                    ->assertSuccessful()
                    ->assertSee('Settings :: Add-ons :: Add-on list')
                    ->assertSee($title." (".$name.", categories)")
                    ->assertSee("in ".$blockTitle." (text)");
            
            $this->get('admin/settings/addon/'.$addon->id)
                    ->assertSee('select disabled="disabled" id="type"');
        }
        else{
            if(\Auth::check()){
                $this->get('admin/settings/addon/'.$addon->id)
                    ->assertSuccessful()
                    ->assertSee('Settings :: No Access');
            }
            else{
                $this->get('admin/settings/addon/'.$addon->id)
                        ->assertRedirect();
            }
        }
        
        $this->post('admin/settings/addon/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'categories',
                'block_id' => $block->id,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }
    }
    
    public function delete_existing_string_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        $this->post('admin/addon/delete', [
                'id' => $addon->id
            ]);
        
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertNull($addon);
        }
        else{
            $this->assertNotNull($addon);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function delete_existing_paragraph_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'paragraphs', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        $this->post('admin/addon/delete', [
                'id' => $addon->id
            ]);
            
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertNull($addon);
        }
        else{
            $this->assertNotNull($addon);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function delete_existing_image_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'images', 'name'=>$name = $this->faker->word, 'title'=> $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => $this->faker->paragraph
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
        
        $this->post('admin/addon/delete', [
            'id' => $addon->id
        ]);
        
        $addon = Block\Addon::all()->last();
        
        if($assertion){
            $this->assertNull($addon);
        }
        else{
            $this->assertNotNull($addon);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function delete_existing_categories_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'categories', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' => $this->faker->word,
            'value' => $this->faker->word
        ]);
        
        $this->post('admin/addon/delete', [
                'id' => $addon->id
            ]);
            
        $addon = Block\Addon::all()->last();
        $category = Block\Addon\Categories::all()->last();
        
        if($assertion){
            $this->assertNull($addon);
            $this->assertNull($category);
        }
        else{
            $this->assertNotNull($addon);
            $this->assertNotNull($category);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function delete_existing_category_value($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'title'=>$blockTitle = $this->faker->word])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'categories', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        $keyValue = Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' => $this->faker->word,
            'value' => $this->faker->word
        ]);
        
        $this->post('admin/category/delete', [
                'id' => $keyValue->id
            ]);
            
        $category = Block\Addon\Categories::all()->last();
        
        if($assertion){
            $this->assertNull($category);
        }
        else{
            $this->assertNotNull($category);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }

    public function create_new_item_with_string_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name."_".$addon->id => $string = $this->faker->sentence,
            'text' => $text = $this->faker->paragraph
        ]);
        
        $element = Block\Text::all()->last();
        
        if($assertion){
            $this->assertNotNull($element);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals($string, $block->firstItem()->{$name});
        }
        else{
            $this->assertNull($element);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function create_new_item_with_paragraph_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'paragraphs', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => $anotherText = $this->faker->sentence,
            'text' => $text = $this->faker->paragraph
        ]);
        
        $element = Block\Text::all()->last();
        
        if($assertion){
            $this->assertNotNull($element);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals($anotherText, $block->firstItem()->{$name});
        }
        else{
            $this->assertNull($element);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function create_new_item_with_image_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'images', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => $text = $this->faker->paragraph
        ]);
        
        $element = Block\Text::all()->last();
        
        if($assertion){
            $this->assertNotNull($element);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            
            $this->assertFileExists(public_path('storage/texts/'.$block->firstItem()->{$name}.".png"));
            $this->assertFileExists(public_path('storage/texts/'.$block->firstItem()->{$name}."_3.png"));
        }
        else{
            $this->assertNull($element);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function create_new_item_with_categories_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'categories', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        Block\Addon\Categories::create([
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
            $name.'_'.$addon->id => $secondCategory->id,
            'text' => $text = $this->faker->paragraph
        ]);
        
        $element = Block\Text::all()->last();
        
        if($assertion){
            $this->assertNotNull($element);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals([$categoryValue => $categoryName], $block->firstItem()->{$name});
        }
        else{
            $this->assertNull($element);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function edit_item_with_string_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name."_".$addon->id => $this->faker->sentence,
            'text' => $this->faker->paragraph
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
        
        $element = Block\Text::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $element->id,
            $name."_".$addon->id => $updatedString = $this->faker->sentence,
            'text' => $updatedText = $this->faker->paragraph
        ]);
        
        if($assertion){
            $this->assertNotNull($element);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($updatedText, $block->firstItem()->text);
            $this->assertEquals($updatedString, $block->firstItem()->{$name});
        }
        else{
            $this->assertNotEquals($updatedText, $block->firstItem()->text);
            $this->assertNotEquals($updatedString, $block->firstItem()->{$name});
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function edit_item_with_paragraph_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'paragraphs', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => $this->faker->sentence,
            'text' => $this->faker->paragraph
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
        
        $element = Block\Text::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $element->id,
            $name.'_'.$addon->id => $anotherText = $this->faker->sentence,
            'text' => $text = $this->faker->paragraph
        ]);
        
        if($assertion){
            $this->assertNotNull($element);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals($anotherText, $block->firstItem()->{$name});
        }
        else{
            $this->assertNotEquals($text, $block->firstItem()->text);
            $this->assertNotEquals($anotherText, $block->firstItem()->{$name});
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function edit_item_with_image_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'images', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => $this->faker->paragraph
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
        
        $element = Block\Text::all()->last();
        
        $imageName = $block->firstItem()->{$name};
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $element->id,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => $text = $this->faker->paragraph
        ]);
        
        $updatedImageName = $block->firstItem()->{$name};
        
        if($assertion){
            $this->assertNotNull($element);
            $this->assertNotEquals($imageName, $updatedImageName);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            
            $this->assertFileExists(public_path('storage/texts/'.$block->firstItem()->{$name}.".png"));
            $this->assertFileExists(public_path('storage/texts/'.$block->firstItem()->{$name}."_3.png"));
            $form = $element->form();
            $this->assertEquals('<img src="/storage/texts/'.$block->firstItem()->{$name}.'_3.png" />', $form->getElement('addonImagePreview_'.$addon->id)->value());
        }
        else{
            $this->assertNotEquals($text, $block->firstItem()->text);
            
            $this->assertEquals($imageName, $updatedImageName);
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function edit_item_with_categories_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'categories', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $firstCategory = Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' => $categoryName = $this->faker->word,
            'value' => $categoryValue = $this->faker->word
        ]);
        
        $secondCategory = Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' =>  $this->faker->word,
            'value' => $this->faker->word
        ]);
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => $secondCategory->id,
            'text' => $this->faker->paragraph
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
        
        $element = Block\Text::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $element->id,
            $name.'_'.$addon->id => $firstCategory->id,
            'text' => $text = $this->faker->paragraph
        ]);
        
        if($assertion){
            $this->assertNotNull($element);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals([$categoryValue => $categoryName], $block->firstItem()->{$name});
        }
        else{
            $this->assertNotEquals($text, $block->firstItem()->text);
            $this->assertNotEquals([$categoryValue => $categoryName], $block->firstItem()->{$name});
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function save_settings_for_block_with_addon($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($block->model()->getTable(), $addonTable);
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            
            $this->assertCount(3, $block->setupForm()->groups());
            
            $this->assertEquals(['id', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "200"
            ]);
            
            $block = Block::find($block->id)->specify();
            
            $this->assertEquals('{"settingsScale":"200"}', json_encode($block->parameters()));
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "200"
            ]);
            
            $block = Block::find($block->id)->specify();
            
            $this->assertNotEquals('{"settingsScale":"200"}', json_encode($block->parameters()));
        }
        
        $this->removePivotTable($block->model()->getTable(), $addonTable);
    }
    
    public function move_block_with_addon($assertion){
        $htmlBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'html', 'orderNo'=>1]);
        $textBlock = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'orderNo'=>2])->specify();
        
        $addon = factory(Block\Addon::class)->create(['block_id'=>$textBlock->id, 'type'=>'strings', 'name'=>$name = $this->faker->word]);$addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();
        
        $this->createPivotTable($textBlock->model()->getTable(), $addonTable);
        
        $this->post("/admin/moveup", [
            "block_id" => $textBlock->id,
            "id" => 0
        ]);
        
        $htmlBlock = Block::find($htmlBlock->id);
        $textBlock = Block::find($textBlock->id);
        
        if($assertion){
            $this->assertEquals(1, $textBlock->orderNo);
            $this->assertEquals(2, $htmlBlock->orderNo);
        }
        else{
            $this->assertEquals(2, $textBlock->orderNo);
            $this->assertEquals(1, $htmlBlock->orderNo);
        }
        
        $this->post("/admin/movedown", [
            "block_id" => $textBlock->id,
            "id" => 0
        ]);
        
        $htmlBlock = Block::find($htmlBlock->id);
        $textBlock = Block::find($textBlock->id)->specify();
        
        $this->assertEquals(2, $textBlock->orderNo);
        $this->assertEquals(1, $htmlBlock->orderNo);
        
        $this->removePivotTable($textBlock->model()->getTable(), $addonTable);
    }
}
