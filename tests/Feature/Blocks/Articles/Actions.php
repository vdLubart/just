<?php

namespace Just\Tests\Feature\Blocks\Articles;

use App\User;
use Illuminate\Support\Facades\Auth;
use Just\Models\Blocks\Articles;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
use Illuminate\Http\UploadedFile;

class Actions extends LocationBlock {

    use WithFaker;

    protected $type = 'articles';

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
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $response->assertSuccessful();

            $form = $block->item()->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['id', 'block_id', 'submit', 'image', 'subject', 'summary', 'text'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $image = uniqid();

        $article = new Articles();
        $article->block_id = $block->id;
        $article->subject = $subject;
        $article->slug = str_slug($subject);
        $article->summary = $summary;
        $article->text = $text;
        $article->image = $image;
        $article->save();

        $item = Articles::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(8, $form->count());
            $this->assertEquals(['id', 'block_id', 'submit', 'imagePreview_'.$item->id, 'image', 'subject', 'summary', 'text'], array_keys($form->elements()));
            $this->assertEquals($text, $form->element('text')->value()['en']);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "cropPhoto" =>	"on",
                "cropDimensions" => "4:3",
                "itemRouteBase" => "article",
                "orderDirection" =>	"desc"
            ])
                ->assertSuccessful();

            $item = Articles::all()->last();

            $form = $item->itemForm();
            $this->assertEquals(9, $form->count());
            $this->assertEquals(['id', 'block_id', 'submit', 'imagePreview_'.$item->id, 'recrop', 'image', 'subject', 'summary', 'text'], array_keys($form->elements()));
            $this->assertEquals($text, $form->element('text')->value()['en']);
        }
        else{
            $this->assertEquals(0, $item->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","orderDirection":"desc"}')]);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => '{"en":"'.($subject = $this->faker->sentence).'"}',
            'summary' => '{"en":"'.($summary = $this->faker->text).'"}',
            'text' => '{"en":"'.($text = $this->faker->text).'"}',
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        if($assertion) {
            $response->assertSuccessful();

            $this->get(json_decode($response->content())->redirect)
                ->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }

        $articleRoute = \Just\Models\System\Route::where('route', 'article/{id}')->first();
        $this->assertNotNull($articleRoute);

        $item = Articles::all()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($subject, $block->firstItem()->subject);
            $this->assertEquals($summary, $block->firstItem()->summary);
            $this->assertEquals($text, $block->firstItem()->text);
        }
        else{
            $this->assertNull($item);
        }
    }

    public function create_new_item_in_block_without_cropping_image(){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"article","orderDirection":"desc"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => '{"en":"'.($subject = $this->faker->sentence).'"}',
            'summary' => '{"en":"'.($summary = $this->faker->text).'"}',
            'text' => '{"en":"'.($text = $this->faker->text).'"}',
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Articles::all()->last();
        $this->assertNotNull($item);
    }

    public function receive_an_error_on_sending_incomplete_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","orderDirection":"desc"}')]);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null
        ]);

        $item = Articles::all()->last();

        $this->assertNull($item);

        if($assertion){
            $response->assertSessionHasErrors(['subject', 'text', 'image']);
        }
        else{
            $response->assertRedirect('/login');
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"article","orderDirection":"desc"}')]);

        if(!$assertion){
            $this->actingAs(User::first());
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => '{"en":"'.($subject = $this->faker->sentence).'"}',
            'summary' => '{"en":"'.($summary = $this->faker->text).'"}',
            'text' => '{"en":"'.($text1 = $this->faker->text).'"}',
            'image' => UploadedFile::fake()->image('photo.jpg')
        ])
            ->assertSuccessful();

        if(!$assertion){
            \Auth::logout();
        }

        $item = Articles::all()->last();

        $text = $this->faker->paragraph;

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'subject' => '{"en":"'.$subject.'"}',
            'summary' => '{"en":"'.$summary.'"}',
            'text' => '{"en":"'.$text.'"}',
        ]);

        $item = Articles::all()->last();

        if($assertion){
            $response->assertSuccessful();

            $this->assertNotNull($item->image);
            $this->assertEquals($subject, $item->subject);
            $this->assertEquals($summary, $item->summary);
            $this->assertEquals($text, $item->text);
        }
        else{
            $response->assertRedirect();
            $this->get("settings/block/".$block->id."/item/".$item->id)
                ->assertRedirect('/login');

            $this->assertNotEquals($text, $item->text);
        }
    }

    public function access_created_item($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","orderDirection":"desc"}')]);

        $article = new Articles();
        $article->block_id = $block->id;
        $article->subject = $subject = $this->faker->sentence;
        $article->slug = str_slug($subject);
        $article->summary = $summary = $this->faker->text;
        $article->text = $text = $this->faker->text;
        $article->image = uniqid();
        $article->save();

        $this->app['router']->get('article/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/article/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);

        $item = Articles::all()->last();

        if($assertion){
            if(\Auth::id()){
                $this->get('admin/article/'.$item->id)
                        ->assertStatus(200);
            }
            else{
                $this->get('admin/article/'.$item->id)
                        ->assertStatus(302);
            }

            $this->get('article/'.$item->id)
                    ->assertStatus(200);

            $this->get('article/'.$item->slug)
                ->assertStatus(200);
        }
        else{
            $this->get('article/'.$item->id)
                    ->assertStatus(404);
        }
    }

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            if(\Auth::user()->role == 'admin'){
                $this->assertCount(3, $form->groups());

                $this->assertEquals(['id', 'submit', 'cropPhoto', 'cropDimensions', 'itemRouteBase', 'orderDirection'], array_keys($form->elements()));

                $this->post('settings/block/customize', [
                    "id" => $block->id,
                    "cropPhoto" => "on",
                    "cropDimensions" => "4:3",
                    "itemRouteBase" => "article",
                    "orderDirection" => "desc",
                    // these two values should be ignored
                    "customSizes" => "on",
                    "photoSizes" => ["8", "6"],
                ])
                    ->assertSuccessful();

                $block = Block::find($block->id);

                $this->assertTrue($block->parameters->cropPhoto);
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertEquals("article", $block->parameters->itemRouteBase);
                $this->assertEquals("desc", $block->parameters->orderDirection);
                $this->assertNull(@$block->parameters->customSizes);
                $this->assertNull(@$block->parameters->photoSizes);
            }
            else {
                $this->assertCount(4, $form->groups());

                $this->assertEquals(['id', 'submit', 'cropPhoto', 'cropDimensions', 'customSizes', 'emptyParagraph', 'photoSizes', 'itemRouteBase', 'orderDirection'], array_keys($form->elements()));

                $this->post('settings/block/customize', [
                    "id" => $block->id,
                    "cropPhoto" => "on",
                    "cropDimensions" => "4:3",
                    "itemRouteBase" => "article",
                    "orderDirection" => "desc",
                    "customSizes" => "on",
                    "photoSizes" => ["8", "6"],
                ]);

                $block = Block::find($block->id);

                $this->assertTrue($block->parameters->cropPhoto);
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertEquals("article", $block->parameters->itemRouteBase);
                $this->assertEquals("desc", $block->parameters->orderDirection);
                $this->assertTrue($block->parameters->customSizes);
                $this->assertEquals(["8", "6"], $block->parameters->photoSizes);
            }
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimensions" => "4:3",
                "orderDirection" =>	"desc"
            ]);

            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }

    public function create_item_with_standard_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"article","orderDirection":"desc"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => '{"en":"'.($subject = $this->faker->sentence).'"}',
            'summary' => '{"en":"'.($summary = $this->faker->text).'"}',
            'text' => '{"en":"'.($text = $this->faker->text).'"}',
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Articles::all()->last();

        $this->assertFileExists(public_path('storage/articles/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileExists(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"photoSizes":["6","3"],"itemRouteBase":"article","orderDirection":"desc"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => '{"en":"'.($subject = $this->faker->sentence).'"}',
            'summary' => '{"en":"'.($summary = $this->faker->text).'"}',
            'text' => '{"en":"'.($text = $this->faker->text).'"}',
            'image' => UploadedFile::fake()->image('photo.jpg')
        ])
            ->assertSuccessful();

        $item = Articles::all()->last();

        $this->assertFileExists(public_path('storage/articles/'.$item->image.'.png'));
        foreach ([6, 3] as $size) {
            $this->assertFileExists(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
        foreach ([12, 9, 8, 4] as $size) {
            $this->assertFileDoesNotExist(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_empty_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"itemRouteBase":"article","orderDirection":"desc"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => '{"en":"'.($subject = $this->faker->sentence).'"}',
            'summary' => '{"en":"'.($summary = $this->faker->text).'"}',
            'text' => '{"en":"'.($text = $this->faker->text).'"}',
            'image' => UploadedFile::fake()->image('photo.jpg')
        ])
            ->assertSuccessful();

        $item = Articles::all()->last();

        $this->assertFileExists(public_path('storage/articles/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileDoesNotExist(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
    }
}
