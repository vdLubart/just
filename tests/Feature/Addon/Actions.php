<?php

namespace Just\Tests\Feature\Addon;

use Illuminate\Support\Facades\Auth;
use Just\Models\AddOn;
use Just\Models\Blocks\AddOns\AddOnOption;
use Just\Models\Blocks\AddOns\Category;
use Just\Models\Blocks\Text;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
use Just\Tests\Feature\Helper;
use Illuminate\Http\UploadedFile;
use Just\Models\User;
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

    public function access_create_addon_form($assertion) {
        $response = $this->get('settings/add-on/0');

        if($assertion){
            $response->assertSuccessful();
            $addon = new AddOn();

            $form = $addon->itemForm();
            $this->assertEquals(8, $form->count());
            $this->assertEquals(['addon_id', 'type', 'name', 'block_id', 'isRequired', 'title', 'description', 'submit'], array_keys($form->elements()));
        }
        else{
            if(Auth::check()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('login');
            }
        }
    }

    public function add_phrase_addon_to_the_block($assertion){
        $block = Block::factory()->create();

        $response = $this->post('settings/add-on/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'phrase',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $title = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($addon);
            $this->assertEquals('phrase', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $response->assertRedirect();
            $this->assertNull($addon);
        }

        $form = $block->item()->itemForm();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', $name."_".$addon->id, 'submit'], array_keys($form->elements()));

            Artisan::call('migrate:rollback');

            exec('rm -rf database/migrations/*');
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', 'submit'], array_keys($form->getElements()));
        }
        else{
            $this->assertEquals(0, $form->count());
        }
    }

    public function add_paragraph_addon_to_the_block($assertion){
        $block = Block::factory()->create();

        $response = $this->post('settings/add-on/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'paragraph',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $title = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($addon);
            $this->assertEquals('paragraph', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $response->assertRedirect();
            $this->assertNull($addon);
        }

        $form = $block->item()->itemForm();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', $name."_".$addon->id, 'submit'], array_keys($form->getElements()));

            Artisan::call('migrate:rollback');

            exec('rm -rf database/migrations/*');
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', 'submit'], array_keys($form->getElements()));
        }
        else{
            $this->assertEquals(0, $form->count());
        }
    }

    public function add_image_addon_to_the_block($assertion){
        $block = Block::factory()->create();

        $response = $this->post('settings/add-on/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'image',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $title = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($addon);
            $this->assertEquals('image', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $response->assertRedirect();
            $this->assertNull($addon);
        }

        $form = $block->item()->itemForm();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', $name."_".$addon->id, 'submit'], array_keys($form->getElements()));

            Artisan::call('migrate:rollback');

            exec('rm -rf database/migrations/*');
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', 'submit'], array_keys($form->getElements()));
        }
        else{
            $this->assertEquals(0, $form->count());
        }
    }

    public function add_category_addon_to_the_block($assertion){
        $block = Block::factory()->create();

        $this->post('settings/add-on/setup', [
                'addon_id' => null,
                'name' => $name = $this->faker->word,
                'type' => 'category',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $title = $this->faker->word,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $this->assertNotNull($addon);
            $this->assertEquals('category', $addon->type);
            $this->assertEquals($name, $addon->name);
            $this->assertEquals($title, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $this->assertNull($addon);
        }

        $form = $block->item()->itemForm();
        if($assertion){
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', $name."_".$addon->id, 'submit'], array_keys($form->getElements()));
        }
        elseif(auth()->check()){
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'block_id', 'text', 'submit'], array_keys($form->getElements()));
        }
        else{
            $this->assertEquals(0, $form->count());
        }

        if($assertion or Auth::check()){
            $revertPivot = false;
            if(is_null($addon)){
                $addon = AddOn::create([
                    'block_id' => $block->id,
                    'type' => 'category',
                    'name' => $name = $this->faker->word,
                    'title' => $title = $this->faker->word
                ]);
                $this->createPivotTable($block, $addon);

                $revertPivot = true;
            }

            $this->get('settings/add-on-option/category/option/0')
                    ->assertSuccessful();

            $this->post('settings/add-on-option/category/option/setup', [
                'id' => null,
                'add_on_id' => $addon->id,
                'option' => '{"en":"'.($catTitle = $this->faker->word).'"}'
            ])
                ->assertSuccessful();

            $this->assertEquals($catTitle, $addon->options->first()->option);

            $block = Block::find($block->id);
            $form = $block->item()->itemForm();
            $lastOption = AddOnOption::all()->last();

            $this->assertEquals([$lastOption->id=>$catTitle], $form->element($name."_".$addon->id)->options());

            // Update category value
            $this->post('settings/add-on-option/category/option/setup', [
                'id' => $lastOption->id,
                'add_on_id' => $addon->id,
                'option' => '{"en":"'.($catTitle = $this->faker->word).'"}'
            ])
                ->assertSuccessful();

            $addon = AddOn::find($addon->id);
            $this->assertEquals($catTitle, $addon->options->first()->option);

            $block = Block::find($block->id);
            $form = $block->item()->itemForm();
            $lastOption = AddOnOption::all()->last();

            $this->assertEquals([$lastOption->id=>$catTitle], $form->element($name."_".$addon->id)->options());

            if($revertPivot){
                $this->removePivotTable($block, $addon);
            }
            else{
                Artisan::call('migrate:rollback');
                exec('rm -rf database/migrations/*');
            }
        }
        else{
            $this->get('settings/add-on-option/category/option/0')
                ->assertRedirect();

            $this->get('settings/add-on-option/category/list')
                ->assertRedirect();

            $addon = AddOn::create([
                'block_id' => $block->id,
                'type' => 'category',
                'name' => $name = $this->faker->word,
                'title' => $title = $this->faker->word
            ]);

            $this->post('admin/settings/category/setup', [
                'category_id' => null,
                'addon_id' => $addon->id,
                'name' => $catTitle = $this->faker->word,
                'value' => $catValue = $this->faker->word
            ]);

            $category = Category::all()->last();

            $this->assertNull($category);
        }
    }

    public function edit_existing_phrase_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('phrase'))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if($assertion){
            $this->get('settings/add-on/list')
                    ->assertSuccessful();

            $this->get('settings/add-on/'.$addon->id)
                ->assertSuccessful();
        }
        else{
            if(\Auth::check()){
                $this->get('settings/add-on/list')
                    ->assertRedirect('settings/noaccess');
            }
            else{
                $this->get('settings/add-on/list')
                        ->assertRedirect();
            }
        }

        $response = $this->post('settings/add-on/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'strings',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }

        $this->removePivotTable($block, $addon);
    }

    public function edit_existing_paragraph_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('paragraph'))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if($assertion){
            $this->get('settings/add-on/list')
                    ->assertSuccessful();

            $this->get('settings/add-on/'.$addon->id)
                    ->assertSuccessful();
        }
        else{
            if(Auth::check()){
                $this->get('settings/add-on/'.$addon->id)
                    ->assertRedirect('settings/noaccess');
            }
            else{
                $this->get('settings/add-on/'.$addon->id)
                        ->assertRedirect('login');
            }
        }

        $response = $this->post('settings/add-on/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'paragraphs',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }

        $this->removePivotTable($block, $addon);
    }

    public function edit_existing_image_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('image'))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if($assertion){
            $this->get('settings/add-on/list')
                    ->assertSuccessful();

            $this->get('settings/add-on/'.$addon->id)
                    ->assertSuccessful();
        }
        else{
            if(Auth::check()){
                $this->get('settings/add-on/'.$addon->id)
                    ->assertRedirect('settings/noaccess');
            }
            else{
                $this->get('settings/add-on/'.$addon->id)
                        ->assertRedirect('login');
            }
        }

        $response = $this->post('settings/add-on/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'images',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }

        $this->removePivotTable($block, $addon);
    }

    public function edit_existing_category_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('category'))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if($assertion){
            $this->get('settings/add-on/list')
                ->assertSuccessful();

            $this->get('settings/add-on/'.$addon->id)
                ->assertSuccessful();
        }
        else{
            if(Auth::check()){
                $this->get('settings/add-on/'.$addon->id)
                    ->assertRedirect('settings/noaccess');
            }
            else{
                $this->get('settings/add-on/'.$addon->id)
                    ->assertRedirect('login');
            }
        }

        $response = $this->post('settings/add-on/setup', [
                'addon_id' => $addon->id,
                'name' => $newName = $this->faker->word,
                'type' => 'categories',
                'block_id' => $block->id,
                'isRequired' => false,
                'title' => $newTitle = $this->faker->title,
                'description' => $description = $this->faker->paragraph
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertEquals($newName, $addon->name);
            $this->assertEquals($newTitle, $addon->title);
            $this->assertEquals($description, $addon->description);
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($newName, $addon->name);
            $this->assertNotEquals($newTitle, $addon->title);
            $this->assertNotEquals($description, $addon->description);
        }

        $this->removePivotTable($block, $addon);
    }

    public function delete_existing_phrase_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('phrase'))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $response = $this->post('settings/add-on/delete', [
                'id' => $addon->id
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNull($addon);
        }
        else{
            $response->assertRedirect();
            $this->assertNotNull($addon);
        }

        $this->removePivotTable($block, $block->addons->first());
    }

    public function delete_existing_paragraph_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('paragraph'))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $response = $this->post('settings/add-on/delete', [
                'id' => $addon->id
            ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNull($addon);
        }
        else{
            $response->assertRedirect();
            $this->assertNotNull($addon);
        }

        $this->removePivotTable($block, $block->addons->first());
    }

    public function delete_existing_image_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('image')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => $this->faker->paragraph
        ])
            ->assertSuccessful()
        ;

        $textItem = Text::all()->last();
        $image = $textItem->addon($name)->value;

        $this->assertFileExists(public_path('storage/texts/'.$image.'.png'));

        if(!$assertion){
            Auth::logout();
        }

        $response = $this->post('settings/add-on/delete', [
            'id' => $addon->id
        ]);

        $addon = AddOn::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNull($addon);
            $this->assertFileDoesNotExist(public_path('storage/texts/'.$image.'.png'));
        }
        else{
            $response->assertRedirect();
            $this->assertNotNull($addon);
            $this->assertFileExists(public_path('storage/texts/'.$image.'.png'));
        }

        $this->removePivotTable($block, $block->addons->first());
    }

    public function delete_existing_category_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('category'))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $option = AddOnOption::create([
            'add_on_id' => $addon->id,
            'option' => '{"en":"'.$this->faker->word.'"}'
        ]);

        $response = $this->post('settings/add-on/delete', [
                'id' => $addon->id
            ]);

        $addon = AddOn::all()->last();
        $option = AddOnOption::find($option->id);

        if($assertion){
            $response->assertSuccessful();
            $this->assertNull($addon);
            $this->assertNull($option);
        }
        else{
            $response->assertRedirect();
            $this->assertNotNull($addon);
            $this->assertNotNull($option);
        }

        $this->removePivotTable($block, $block->addons->first());
    }

    public function create_new_item_with_phrase_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('phrase')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name."_".$addon->id => '{"en":"'.($string = $this->faker->sentence).'"}',
            'text' => $text = $this->faker->paragraph
        ]);

        $element = Text::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($element);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals($string, $block->firstItem()->{$name});
        }
        else{
            $response->assertRedirect();
            $this->assertNull($element);
        }

        $this->removePivotTable($block, $addon);
    }

    public function create_new_item_with_paragraph_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('paragraph')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => '{"en":"'.($string = $this->faker->sentence).'"}',
            'text' => $text = $this->faker->paragraph
        ]);

        $element = Text::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($element);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals($string, $block->firstItem()->{$name});
        }
        else{
            $response->assertRedirect();
            $this->assertNull($element);
        }

        $this->removePivotTable($block, $addon);
    }

    public function create_new_item_with_image_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('image')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => $text = $this->faker->paragraph
        ]);

        $item = Block::find($block->id)->firstItem();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $item->block_id);
            $this->assertEquals($text, $item->text);

            $this->assertFileExists(public_path('storage/texts/'.$item->{$name}.".png"));
            $this->assertFileExists(public_path('storage/texts/'.$item->{$name}."_3.png"));
        }
        else{
            $response->assertRedirect();
            $this->assertNull($item);
        }

        $this->removePivotTable($block, $addon);
    }

    public function create_new_item_with_category_addon($assertion){
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
            $name.'_'.$addon->id => $secondOption->id,
            'text' => $text = $this->faker->paragraph
        ]);

        $item = Block::find($block->id)->firstItem();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $item->block_id);
            $this->assertEquals($text, $item->text);
            $this->assertEquals($secondOption->id, $item->{$name});
        }
        else{
            $this->assertNull($item);
        }

        $this->removePivotTable($block, $addon);
    }

    public function edit_item_with_phrase_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('phrase')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name."_".$addon->id => '{"en":"'.$this->faker->sentence.'"}',
            'text' => $this->faker->paragraph
        ]);

        if(!$assertion){
            Auth::logout();
        }

        $item = Block::find($block->id)->firstItem();

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            $name."_".$addon->id => '{"en":"'.($updatedString = $this->faker->sentence).'"}',
            'text' => $updatedText = $this->faker->paragraph
        ]);

        $item = Block::find($block->id)->firstItem();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($updatedText, $block->firstItem()->text);
            $this->assertEquals($updatedString, $block->firstItem()->{$name});
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($updatedText, $block->firstItem()->text);
            $this->assertNotEquals($updatedString, $block->firstItem()->{$name});
        }

        $this->removePivotTable($block, $addon);
    }

    public function edit_item_with_paragraph_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('paragraph')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name."_".$addon->id => '{"en":"'.$this->faker->sentence.'"}',
            'text' => $this->faker->paragraph
        ]);

        if(!$assertion){
            \Auth::logout();
        }

        $item = Text::all()->last();

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            $name."_".$addon->id => '{"en":"'.($updatedString = $this->faker->sentence).'"}',
            'text' => '{"en":"'.($text = $this->faker->paragraph).'"}',
        ]);

        $item = Block::find($block->id)->firstItem();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $item->block_id);
            $this->assertEquals($text, $item->text);
            $this->assertEquals($updatedString, $item->{$name});
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($text, $item->text);
            $this->assertNotEquals($updatedString, $item->{$name});
        }

        $this->removePivotTable($block, $addon);
    }

    public function edit_item_with_image_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('image')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => '{"en":"'.($this->faker->paragraph).'"}',
        ]);

        if(!$assertion){
            Auth::logout();
        }

        $element = Text::all()->last();

        $imageName = $block->firstItem()->{$name};

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $element->id,
            $name.'_'.$addon->id => UploadedFile::fake()->image('photo.jpg'),
            'text' => '{"en":"'.($text = $this->faker->paragraph).'"}',
        ]);

        $item = Block::find($block->id)->firstItem();
        $updatedImageName = $block->firstItem()->{$name};

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($element);
            $this->assertNotEquals($imageName, $updatedImageName);

            $this->assertEquals($block->id, $item->block_id);
            $this->assertEquals($text, $item->text);

            $this->assertFileExists(public_path('storage/texts/'.$item->{$name}.".png"));
            $this->assertFileExists(public_path('storage/texts/'.$item->{$name}."_3.png"));
            $form = $element->itemForm();
            $this->assertEquals('<img src="/storage/texts/'.$item->{$name}.'_3.png" />', $form->element('addonImagePreview_'.$addon->id)->value());
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($text, $item->text);

            $this->assertEquals($imageName, $updatedImageName);
        }

        $this->removePivotTable($block, $addon);
    }

    public function edit_item_with_category_addon($assertion){
        $block = Block::factory()
            ->has(AddOn::factory()->type('category')->name($name = $this->faker->word))
            ->create();
        $addon = $block->addons->first();

        $this->createPivotTable($block, $addon);

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

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
            'text' => '{"en":"'.($this->faker->paragraph).'"}',
        ]);

        if(!$assertion){
            Auth::logout();
        }

        $element = Text::all()->last();

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $element->id,
            $name.'_'.$addon->id => $secondOption->id,
            'text' => '{"en":"'.($text = $this->faker->paragraph).'"}',
        ]);

        $item = Block::find($block->id)->firstItem();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($element);

            $this->assertEquals($block->id, $item->block_id);
            $this->assertEquals($text, $item->text);
            $this->assertEquals($secondOption->id, $item->{$name});
        }
        else{
            $response->assertRedirect();
            $this->assertNotEquals($text, $item->text);
            $this->assertNotEquals($secondOption->id, $item->{$name});
        }

        $this->removePivotTable($block, $addon);
    }
}
