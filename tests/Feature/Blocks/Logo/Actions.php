<?php

namespace Lubart\Just\Tests\Feature\Blocks\Logo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;
use Lubart\Just\Models\User;
use Intervention\Image\ImageManagerStatic as Image;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/logos'))){
            exec('rm -rf ' . public_path('storage/logos'));
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'logo'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('<div id="imageUploader"></div>');
        $response->assertDontSee('input name="caption"');
        
        if($assertion){
            $form = $block->form();
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['imageUploader', 'startUpload', 'block_id', 'id'], array_keys($form->getElements()));
        }
    }
    
    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'logo'])->specify();
        
        Block\Logo::insert([
            'block_id' => $block->id,
            'image' => $image = uniqid()
        ]);
        
        $item = Block\Logo::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['imageUploader', 'imagePreview_'.$block->id, 'caption', 'description', 'startUpload'], array_keys($form->getElements()));
            $this->assertEquals('<img src="/storage/logos/'.$image.'.png" width="300" />', $form->getElement('imagePreview_'.$block->id)->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'logo'])->specify();
        
        $this->post("admin/ajaxuploader", [
            'block_id' => $block->id,
            'id' => null,
            'ax_file_input' => UploadedFile::fake()->image('photo.jpg'),
            'ax-file-name' => 'photo.jpg',
            'ax-max-file-size' => '8M',
            'ax-file-path' => '../storage/app/public/logos',
            'ax-allow-ext' => 'jpg|png|jpeg',
            'ax-file-size' => (150 * 1024),
            'ax-thumbPostfix' => '_thumb',
            'ax-override' => 1,
            'startUpload' => "Upload images"
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        $item = Block\Logo::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($item->image, $block->firstItem()->image);
            
            $this->get('admin')
                    ->assertSee('/storage/logos/'.$item->image.'_4.png');
            
            $this->get('')
                    ->assertSee('storage/logos/'.$item->image.'_4.png');
        }
        else{
            $this->assertNull($item);
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'logo'])->specify();
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $r = $this->post("admin/ajaxuploader", [
            'block_id' => $block->id,
            'id' => null,
            'ax_file_input' => UploadedFile::fake()->image('photo.jpg'),
            'ax-file-name' => 'photo.jpg',
            'ax-max-file-size' => '8M',
            'ax-file-path' => '../storage/app/public/logos',
            'ax-allow-ext' => 'jpg|png|jpeg',
            'ax-file-size' => (150 * 1024),
            'ax-thumbPostfix' => '_thumb',
            'ax-override' => 1,
            'startUpload' => "Upload images"
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        if(!$assertion){
            \Auth::logout();
        }
        
        //dd($r->baseResponse->exception->getMessage());
        
        $item = Block\Logo::all()->last();
        
        if($assertion){
            $this->get("admin/settings/".$block->id."/".$item->id)
                    ->assertSee($item->image."_3.png");
        }
        
        $this->post("admin/ajaxuploader", [
            'block_id' => $block->id,
            'id' => $item->id,
            'ax_file_input' => UploadedFile::fake()->image('update.jpg'),
            'ax-max-file-size' => '8M',
            'ax-file-path' => '../storage/app/public/logos',
            'ax-allow-ext' => 'jpg|png|jpeg',
            'ax-override' => true,
            'caption' => $caption = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
            'startUpload' => "Upload images"
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        $updatedItem = Block\Logo::find($item->id);
        
        if($assertion){
            $this->assertEquals(1, Block\Logo::count());
            $this->assertEquals($item->id, $updatedItem->id);
            $this->assertNotEquals($item->image, $updatedItem->image);
            $this->assertEquals($caption, $updatedItem->caption);
            $this->assertEquals($description, $updatedItem->description);
        }
        else{
            $this->assertEquals($item->image, $updatedItem->image);
            $this->assertEquals('', $updatedItem->caption);
            $this->assertEquals('', $updatedItem->description);
        }
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'caption' => $caption = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph
        ]);
        
        $twiceUpdatedItem = Block\Logo::find($item->id);
        
        if($assertion){
            $this->assertEquals(1, Block\Logo::count());
            $this->assertEquals($updatedItem->id, $twiceUpdatedItem->id);
            $this->assertEquals($updatedItem->image, $twiceUpdatedItem->image);
            $this->assertNotEquals($updatedItem->caption, $twiceUpdatedItem->caption);
            $this->assertNotEquals($updatedItem->description, $twiceUpdatedItem->description);
        }
        else{
            $this->assertEquals($item->image, $updatedItem->image);
            $this->assertEquals('', $twiceUpdatedItem->caption);
            $this->assertEquals('', $twiceUpdatedItem->description);
        }
    }
    
    public function crop_photo($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'logo', 'parameters'=>'{"cropPhoto":"on"}'])->specify();
        
        $this->post("/admin/settings/setup", [
            'cropPhoto' => 'on',
            'cropDimentions' => '4:3'
        ]);
        
        $response = $this->post("admin/ajaxuploader", [
            'block_id' => $block->id,
            'id' => null,
            'ax_file_input' => UploadedFile::fake()->image('photo.jpg'),
            'ax-file-name' => 'photo.jpg',
            'ax-max-file-size' => '8M',
            'ax-file-path' => '../storage/app/public/logos',
            'ax-allow-ext' => 'jpg|png|jpeg',
            'ax-file-size' => (150 * 1024),
            'ax-thumbPostfix' => '_thumb',
            'ax-override' => 1,
            'startUpload' => "Upload images"
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        $content = json_decode($response->baseResponse->content());
        
        if($assertion){
            $this->assertTrue($content->crop);
            
            $item = Block\Logo::all()->last();
            
            $this->assertFileExists(public_path('storage/logos/'.$item->image.".png"));
            
            $image = Image::make(public_path('storage/logos/'.$item->image.".png"));
            
            $this->assertNotEquals(1170, $image->width());
            $this->assertNotEquals(878, $image->height());

            $this->post("/admin/settings/crop", [
                'block_id' => $block->id,
                'id' => $item->id,
                'img' => $item->image,
                'x' => 0,
                'y' => 0,
                'w' => 1170,
                'h' => 878,
            ]);
            
            $this->assertFileExists(public_path('storage/logos/'.$item->image.".png"));
            
            $image = Image::make(public_path('storage/logos/'.$item->image.".png"));
            
            $this->assertEquals(1170, $image->width());
            $this->assertEquals(878, $image->height());
        }
        else{
            $this->assertEquals("Unauthenticated.", $content->message);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'logo'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View')
                    ->assertSee('Image cropping');
            if(\Auth::user()->role == 'master'){
                $response->assertSee('Image fields')
                        ->assertSee('Resize images');
                
                $this->assertCount(6, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'cropPhoto', 'cropDimentions', 'ignoreCaption', 'ignoreDescription', 'customSizes', 'photoSizes[]', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());
            }
            else{
                $response->assertDontSee('Image fields')
                        ->assertDontSee('Resize images');
                
                $this->assertCount(4, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'cropPhoto', 'cropDimentions', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());
            }
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropDimentions" => "4:3",
                "ignoreCaption" => "on",
                "customSizes" => "1",
                "photoSizes" => ["8","3"],
                "settingsScale" => "100"
            ]);
            
            $r = $this->post("admin/ajaxuploader", [
                'block_id' => $block->id,
                'id' => null,
                'ax_file_input' => UploadedFile::fake()->image('photo.jpg'),
                'ax-file-name' => 'photo.jpg',
                'ax-max-file-size' => '8M',
                'ax-file-path' => '../storage/app/public/logos',
                'ax-allow-ext' => 'jpg|png|jpeg',
                'ax-file-size' => (150 * 1024),
                'ax-thumbPostfix' => '_thumb',
                'ax-override' => 1,
                'startUpload' => "Upload images"
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
            
            $block = Block::find($block->id)->specify();
            $item = Block\Logo::all()->last();
            
            $form = $item->form();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('{"cropDimentions":"4:3","ignoreCaption":"on","customSizes":"1","photoSizes":["8","3"],"settingsScale":"100"}', json_encode($block->parameters()));
                $this->assertNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));
                
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'.png'));
                $this->assertFileNotExists(public_path('storage/logos/'.$item->image.'_12.png'));
                $this->assertFileNotExists(public_path('storage/logos/'.$item->image.'_9.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_8.png'));
                $this->assertFileNotExists(public_path('storage/logos/'.$item->image.'_6.png'));
                $this->assertFileNotExists(public_path('storage/logos/'.$item->image.'_4.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_3.png'));
            }
            else{
                $this->assertEquals('{"cropDimentions":"4:3","settingsScale":"100"}', json_encode($block->parameters()));
                $this->assertNotNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));
                
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_12.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_9.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_8.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_6.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_4.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_3.png'));
            }
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropDimentions" => "4:3",
                "ignoreDescription" => "on",
                "customSizes" => "on",
                "photoSizes" => ["8","3"],
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id)->specify();
            $item = Block\Logo::all()->last();
            
            $form = $item->form();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('{"cropDimentions":"4:3","ignoreDescription":"on","customSizes":"on","photoSizes":["8","3"],"settingsScale":"100"}', json_encode($block->parameters()));
                $this->assertNotNull($form->getElement('caption'));
                $this->assertNull($form->getElement('description'));
            }
            else{
                $this->assertEquals('{"cropDimentions":"4:3","settingsScale":"100"}', json_encode($block->parameters()));
                $this->assertNotNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));
            }
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"settingsScale":"100"}', json_encode($block->parameters()));
        }
    }
}
