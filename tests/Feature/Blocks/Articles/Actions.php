<?php

namespace Lubart\Just\Tests\Feature\Blocks\Articles;

use App\User;
use Lubart\Just\Tests\Feature\Blocks\LocationBlock;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
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

    public function access_item_form_without_initial_data($assertion){
        $block = $this->setupBlock();

        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->assertDontSee('input name="subject"');
        
        $response->{($assertion?'assertDontSee':'assertSee')}('Item Route');
        $response->{($assertion?'assertDontSee':'assertSee')}('Settings View Scale');
        
        $this->post('admin/settings/setup', [
            "id" => $block->id,
            "cropPhoto" =>	"on",
            "cropDimensions" => "4:3",
            "itemRouteBase" => "article",
            "settingsScale" => "100",
            "orderDirection" =>	"desc"
        ])
                ->assertStatus(200);

        $block = Block::find($block->id);

        if(\Auth::id()){
            $this->assertTrue($block->parameters->cropPhoto);
            $this->assertEquals("4:3", $block->parameters->cropDimensions);
            $this->assertEquals("article", $block->parameters->itemRouteBase);
            $this->assertEquals("100", $block->parameters->settingsScale);
            $this->assertEquals("desc", $block->parameters->orderDirection);
        }
        else{
            $this->assertEmpty($block->parameters);
        }
    }
    
    public function access_item_form_when_block_is_setted_up($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        $response = $this->get("admin/settings/".$block->id."/0");

        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="image"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="subject"');
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();
        
        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $image = uniqid();
        
        $article = new Block\Articles();
        $article->block_id = $block->id;
        $article->subject = $subject;
        $article->slug = str_slug($subject);
        $article->summary = $summary;
        $article->text = $text;
        $article->image = $image;
        $article->save();
        
        $item = Block\Articles::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(6, $form->count());
            $this->assertEquals(['image', 'imagePreview_'.$item->id, 'subject', 'summary', 'text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());

            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"on",
                "cropDimensions" => "4:3",
                "itemRouteBase" => "article",
                "settingsScale" => "100",
                "orderDirection" =>	"desc"
            ]);

            $item = Block\Articles::all()->last();

            $form = $item->form();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['image', 'imagePreview_'.$item->id, 'recrop', 'subject', 'summary', 'text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $response = json_decode($response->baseResponse->content());

        if($assertion) {
            $this->assertTrue($response->shouldBeCropped);

            $this->get('/admin/settings/crop/' . $response->block_id . '/' . $response->id)
                ->assertSuccessful();
        }
        else{
            $this->assertNull($response);
        }

        $articleRoute = \Lubart\Just\Models\Route::where('route', 'article/{id}')->first();
        $this->assertNotNull($articleRoute);
        
        $item = Block\Articles::all()->last();
        
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
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $response = json_decode($response->baseResponse->content());

        $this->assertNull(@$response->shouldBeCropped);
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ]);
        
        $item = Block\Articles::all()->last();
        
        $this->assertNull($item);
        
        if($assertion){
            $response->assertSessionHasErrors(['subject', 'text']);
        }
        else{
            $response->assertRedirect('/login');
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        if(!$assertion){
            $this->actingAs(User::first());
        }

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        if(!$assertion){
            \Auth::logout();
        }
        
        $item = Block\Articles::all()->last();

        $text = $this->faker->paragraph;
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'subject' => $item->subject,
            'summary' => $item->summary,
            'text' => $text
        ]);
        
        $item = Block\Articles::all()->last();
        
        if($assertion){
            $this->get("admin/settings/".$block->id."/".$item->id)
                ->assertSee('<img src="/storage/articles/'.$item->image.'_3.png" />');

            $this->assertEquals($subject, $item->subject);
            $this->assertEquals($summary, $item->summary);
            $this->assertEquals($text, $item->text);
        }
        else{
            $this->get("admin/settings/".$block->id."/".$item->id)
                ->assertRedirect('/login');

            $this->assertNotEquals($text, $item->text);
        }
    }
    
    public function access_created_item($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        $article = new Block\Articles();
        $article->block_id = $block->id;
        $article->subject = $subject = $this->faker->sentence;
        $article->slug = str_slug($subject);
        $article->summary = $summary = $this->faker->text;
        $article->text = $text = $this->faker->text;
        $article->image = uniqid();
        $article->save();
        
        $this->app['router']->get('article/{id}', "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/article/{id}', "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $item = Block\Articles::all()->last();
        
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
        }
        else{
            $this->get('article/'.$item->id)
                    ->assertStatus(404);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View')
                    ->assertSee('Image Cropping')
                    ->assertSee('Sorting');

            if(\Auth::user()->role == 'admin'){
                $this->assertCount(5, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'itemRouteBase', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "on",
                    "cropDimensions" => "4:3",
                    "itemRouteBase" => "article",
                    "settingsScale" => "100",
                    "orderDirection" => "desc",
                    "customSizes" => "on",
                    "photoSizes[]" => ["8", "6"],
                ])
                    ->assertSuccessful();

                $block = Block::find($block->id);

                $this->assertTrue($block->parameters->cropPhoto);
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertEquals("article", $block->parameters->itemRouteBase);
                $this->assertEquals("100", $block->parameters->settingsScale);
                $this->assertEquals("desc", $block->parameters->orderDirection);
                $this->assertNull(@$block->parameters->customSizes);
                $this->assertNull(@$block->parameters->photoSizes);
            }
            else {
                $response->assertSee('Resize Images')
                    ->assertSee('Item Route');

                $this->assertCount(6, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'customSizes', 'photoSizes[]', 'itemRouteBase', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "on",
                    "cropDimensions" => "4:3",
                    "itemRouteBase" => "article",
                    "settingsScale" => "100",
                    "orderDirection" => "desc",
                    "customSizes" => "on",
                    "photoSizes[]" => ["8", "6"],
                ]);

                $block = Block::find($block->id);

                $this->assertTrue($block->parameters->cropPhoto);
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertEquals("article", $block->parameters->itemRouteBase);
                $this->assertEquals("100", $block->parameters->settingsScale);
                $this->assertEquals("desc", $block->parameters->orderDirection);
                $this->assertTrue($block->parameters->customSizes);
                $this->assertEquals(["8", "6"], $block->parameters->photoSizes);
            }
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimensions" => "4:3",
                "settingsScale" => "100",
                "orderDirection" =>	"desc"
            ]);
            
            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }

    public function create_item_with_standard_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Block\Articles::all()->last();

        $this->assertFileExists(public_path('storage/articles/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileExists(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"photoSizes":["6","3"],"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Block\Articles::all()->last();

        $this->assertFileExists(public_path('storage/articles/'.$item->image.'.png'));
        foreach ([6, 3] as $size) {
            $this->assertFileExists(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
        foreach ([12, 9, 8, 4] as $size) {
            $this->assertFileNotExists(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_empty_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Block\Articles::all()->last();

        $this->assertFileExists(public_path('storage/articles/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileNotExists(public_path('storage/articles/' . $item->image . '_'.$size.'.png'));
        }
    }
}
