<?php

namespace Lubart\Just\Tests\Feature\Blocks\Articles;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;

class Actions extends TestCase{
    
    use WithFaker;
    
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->assertDontSee('input name="subject"');
        
        $response->{($assertion?'assertDontSee':'assertSee')}('Article image width');
        $response->{($assertion?'assertDontSee':'assertSee')}('Article image height');
        $response->{($assertion?'assertDontSee':'assertSee')}('Settings View Scale');
        
        $this->post('admin/settings/setup', [
            'id' => $block->id,
            'settingsScale' => "100",
            'imageWidth' => "1040",
            'imageHeight' => "200"
        ])
                ->assertStatus(200);
        
        $block = Block::find($block->id);
        $this->{($assertion ? 'assertJsonStringNotEqualsJsonString' : 'assertJsonStringEqualsJsonString')}('{"settingsScale":"100","imageWidth":"1040","imageHeight":"200"}', $block->parameters);
        
        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}(100, $block->parameter('settingsScale'));
        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}(1040, $block->parameter('imageWidth'));
        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}(200, $block->parameter('imageHeight'));
        
    }
    
    public function access_item_form_when_block_is_setted_up($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles', 'parameters'=>'{"settingsScale":"100","imageWidth":"1040","imageHeight":"200"}'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="image"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="subject"');
    }

    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles'])->specify();
        
        $subject = $this->faker->sentence;
        $summary = $this->faker->paragraph;
        $text = $this->faker->paragraph;
        $image = uniqid();
        
        Block\Articles::insert([
            'block_id' => $block->id,
            'subject' => $subject,
            'summary' => $summary,
            'text' => $text,
            'image' => $image
        ]);
        
        $item = Block\Articles::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(6, $form->count());
            $this->assertEquals(['image', 'recrop', 'subject', 'summary', 'text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles', 'parameters'=>'{"settingsScale":"100","imageWidth":"1040","imageHeight":"200"}'])->specify();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->paragraph,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);
        
        $articleRoute = \Lubart\Just\Models\Route::where('route', 'article/{id}')->first();
        $this->assertNotNull($articleRoute);
        
        $item = Block\Articles::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($subject, $block->firstItem()->subject);
            $this->assertEquals($summary, $block->firstItem()->summary);
            $this->assertEquals($text, $block->firstItem()->text);
            
            $this->get('admin')
                    ->assertSee($subject)
                    ->assertSee($summary);
            
            $this->get('')
                    ->assertSee($subject)
                    ->assertSee($summary);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($subject)
                    ->assertDontSee($summary);
            
            $this->get('')
                    ->assertDontSee($subject)
                    ->assertDontSee($summary);
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles', 'parameters'=>'{"settingsScale":"100","imageWidth":"1040","imageHeight":"200"}'])->specify();
        
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles', 'parameters'=>'{"settingsScale":"100","imageWidth":"1040","imageHeight":"200"}'])->specify();
        
        Block\Articles::insert([
            'block_id' => $block->id,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->paragraph,
            'text' => $this->faker->paragraph,
            'image' => uniqid()
        ]);
        
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
            $this->assertEquals($subject, $item->subject);
            $this->assertEquals($summary, $item->summary);
            $this->assertEquals($text, $item->text);
        }
        else{
            $this->assertNotEquals($text, $item->text);
        }
    }
    
    public function access_created_item($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles', 'parameters'=>'{"settingsScale":"100","imageWidth":"1040","imageHeight":"200"}'])->specify();
        
        Block\Articles::insert([
            'block_id' => $block->id,
            'subject' => $subject = $this->faker->sentence,
            'summary' => $summary = $this->faker->paragraph,
            'text' => $text = $this->faker->paragraph,
            'image' => uniqid()
        ]);
        
        $this->app['router']->get('article/{id}', "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/article/{id}', "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $item = Block\Articles::all()->last();
        
        if($assertion){
            if(\Auth::id()){
                $this->get('admin/article/'.$item->id)
                        ->assertStatus(200)
                        ->assertSee($subject)
                        ->assertDontSee($summary)
                        ->assertSee($text);
            }
            else{
                $this->get('admin/article/'.$item->id)
                        ->assertStatus(302);
            }

            $this->get('article/'.$item->id)
                    ->assertStatus(200)
                    ->assertSee($subject)
                    ->assertDontSee($summary)
                    ->assertSee($text);
        }
        else{
            $this->get('article/'.$item->id)
                    ->assertStatus(404);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'name'=>'articles'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View')
                    ->assertSee('Block parameters');
            
            $this->assertCount(3, $block->setupForm()->groups());
            
            $this->assertEquals(['id', 'settingsScale', 'imageWidth', 'imageHeight', 'submit'], $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100",
                "imageWidth" => "700",
                "imageHeight" => "300"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertEquals('{"settingsScale":"100","imageWidth":"700","imageHeight":"300"}', $block->parameters);
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100",
                "imageWidth" => "700",
                "imageHeight" => "300"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"settingsScale":"100","imageWidth":"700","imageHeight":"300"}', $block->parameters);
        }
    }
}
