<?php

namespace Lubart\Just\Tests\Feature\Blocks\Articles;

use App\User;
use Lubart\Just\Tests\Feature\Blocks\BlockLocation;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;

class Actions extends BlockLocation {
    
    use WithFaker;

    protected $type = 'articles';

    public function tearDown(){
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
        
        $response->{($assertion?'assertDontSee':'assertSee')}('Item route base');
        $response->{($assertion?'assertDontSee':'assertSee')}('Settings View Scale');
        
        $this->post('admin/settings/setup', [
            "id" => $block->id,
            "cropPhoto" =>	"1",
            "cropDimentions" => "4:3",
            "itemRouteBase" => "article",
            "settingsScale" => "100",
            "orderDirection" =>	"desc"
        ])
                ->assertStatus(200);
        
        $block = Block::find($block->id);
        $this->{($assertion ? 'assertJsonStringNotEqualsJsonString' : 'assertJsonStringEqualsJsonString')}('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
        
        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}(100, $block->parameter('settingsScale'));
        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}("article", $block->parameter('itemRouteBase'));
        
    }
    
    public function access_item_form_when_block_is_setted_up($assertion){
        $block = $this->setupBlock(['parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);

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
        
        Block\Articles::insert([
            'block_id' => $block->id,
            'subject' => $subject,
            'slug' => str_slug($subject),
            'summary' => $summary,
            'text' => $text,
            'image' => $image
        ]);
        
        $item = Block\Articles::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(6, $form->count());
            $this->assertEquals(['image', 'imagePreview_'.$item->id, 'subject', 'summary', 'text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());

            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimentions" => "4:3",
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
        $block = $this->setupBlock(['parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $response = json_decode($response->baseResponse->content());
        if(isset($response->shouldBeCropped) and $response->shouldBeCropped){
            $this->get('/admin/settings/crop/' . $response->block_id . '/' . $response->id)
                ->assertSuccessful();
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
        $block = $this->setupBlock(['parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);

        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $response = json_decode($response->baseResponse->content());

        $this->assertEmpty(@$response->shouldBeCropped);
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ]);
        
        $item = Block\Articles::all()->last();
        
        $response->assertRedirect();
        
        $this->assertNull($item);
        
        if($assertion){
            $this->followRedirects($response)
                    ->assertSee("The subject field is required");
        }
        else{
            $this->followRedirects($response)
                    ->assertDontSee("The subject field is required");
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock(['parameters'=>'{"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);

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
        $block = $this->setupBlock(['parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);

        Block\Articles::insert([
            'block_id' => $block->id,
            'subject' => $subject = $this->faker->sentence,
            'slug' => str_slug($subject),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => uniqid()
        ]);
        
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
                    ->assertSee('Image cropping')
                    ->assertSee('Ordering Direction');

            if(\Auth::user()->role == 'admin'){
                $this->assertCount(5, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimentions', 'itemRouteBase', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "1",
                    "cropDimentions" => "4:3",
                    "itemRouteBase" => "article",
                    "settingsScale" => "100",
                    "orderDirection" => "desc"
                ]);

                $block = Block::find($block->id);

                $this->assertEquals('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
            }
            else {
                $response->assertSee('Resize images')
                    ->assertSee('Item route');

                $this->assertCount(6, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimentions', 'customSizes', 'photoSizes[]', 'itemRouteBase', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "1",
                    "cropDimentions" => "4:3",
                    "itemRouteBase" => "article",
                    "settingsScale" => "100",
                    "orderDirection" => "desc"
                ]);

                $block = Block::find($block->id);

                $this->assertEquals('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
            }
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimentions" => "4:3",
                "settingsScale" => "100",
                "orderDirection" =>	"desc"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
        }
    }

    public function create_item_with_standard_image_sizes() {
        $block = $this->setupBlock(['parameters'=>'{"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);

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
        $block = $this->setupBlock(['parameters'=>'{"customSizes":1,"photoSizes":["6","3"],"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);

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
        $block = $this->setupBlock(['parameters'=>'{"customSizes":1,"itemRouteBase":"article","settingsScale":"100","orderDirection":"desc"}']);

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
