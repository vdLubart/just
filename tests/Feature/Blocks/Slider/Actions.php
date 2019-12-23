<?php

namespace Just\Tests\Feature\Blocks\Slider;

use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;
use Just\Models\User;
use Intervention\Image\ImageManagerStatic as Image;

class Actions extends LocationBlock {
    
    use WithFaker;

    protected $type = 'slider';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/photos'))){
//            exec('rm -rf ' . public_path('storage/photos'));
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = $this->setupBlock();
        
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
        $block = $this->setupBlock();
        
        Block\Slider::insert([
            'block_id' => $block->id,
            'image' => $image = uniqid()
        ]);
        
        $item = Block\Slider::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['imageUploader', 'imagePreview_'.$block->id, 'caption', 'description', 'startUpload'], array_keys($form->getElements()));
            $this->assertEquals('<img src="/storage/photos/'.$image.'.png" width="300" />', $form->getElement('imagePreview_'.$block->id)->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock();
        
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
        
        $item = Block\Slider::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($item->image, $block->firstItem()->image);
            
            $this->assertFileExists(public_path('/storage/photos/'.$item->image.'.png'));
        }
        else{
            $this->assertNull($item);
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock();
        
        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }
        
        $r = $this->post("admin/ajaxuploader", [
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
        
        $item = Block\Slider::all()->last();
        
        if($assertion){
            $this->get("admin/settings/".$block->id."/".$item->id)
                    ->assertSee($item->image."_3.png");
        }
        
        $this->post("admin/ajaxuploader", [
            'block_id' => $block->id,
            'id' => $item->id,
            'ax_file_input' => UploadedFile::fake()->image('update.jpg'),
            'ax-max-file-size' => '100M',
            'ax-file-path' => '../storage/app/public/photos',
            'ax-allow-ext' => 'jpg|png|jpeg',
            'ax-override' => true,
            'caption' => $caption = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
            'startUpload' => "Upload images"
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);
        
        $updatedItem = Block\Slider::find($item->id);
        
        if($assertion){
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
        
        $twiceUpdatedItem = Block\Slider::find($item->id);
        
        if($assertion){
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
        $block = $this->setupBlock(['parameters'=>'{"cropPhoto":"on"}']);
        
        $this->post("/admin/settings/setup", [
            'cropPhoto' => 'on',
            'cropDimensions' => '4:3'
        ]);
        
        $response = $this->post("admin/ajaxuploader", [
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
        
        $content = json_decode($response->baseResponse->content());
        
        if($assertion){
            $this->assertTrue($content->crop);
            
            $item = Block\Slider::all()->last();
            
            $this->assertFileExists(public_path('storage/photos/'.$item->image.".png"));
            
            $image = Image::make(public_path('storage/photos/'.$item->image.".png"));
            
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
            
            $this->assertFileExists(public_path('storage/photos/'.$item->image.".png"));
            
            $image = Image::make(public_path('storage/photos/'.$item->image.".png"));
            
            $this->assertEquals(1170, $image->width());
            $this->assertEquals(878, $image->height());
        }
        else{
            $response->assertRedirect('login');
        }
    }
    
    public function edit_block_settings($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View')
                    ->assertSee('Image Cropping');
            if(\Auth::user()->role == 'master'){
                $response->assertSee('Item Fields')
                        ->assertSee('Resize Images');
                
                $this->assertCount(6, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'ignoreCaption', 'ignoreDescription', 'customSizes', 'photoSizes[]', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());
            }
            else{
                $response->assertDontSee('Item Fields')
                        ->assertDontSee('Resize Images');
                
                $this->assertCount(4, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());
            }
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropDimensions" => "4:3",
                "ignoreCaption" => "on",
                "customSizes" => "1",
                "photoSizes" => ["8","3"],
                "settingsScale" => "100"
            ]);
            
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

            $block = Block::find($block->id)->specify();
            $item = Block\Slider::all()->last();
            
            $form = $item->form();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('{"cropDimensions":"4:3","ignoreCaption":"on","customSizes":"1","photoSizes":["8","3"],"settingsScale":"100"}', json_encode($block->parameters()));
                $this->assertNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));
                
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
                $this->assertFileNotExists(public_path('storage/photos/'.$item->image.'_12.png'));
                $this->assertFileNotExists(public_path('storage/photos/'.$item->image.'_9.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_8.png'));
                $this->assertFileNotExists(public_path('storage/photos/'.$item->image.'_6.png'));
                $this->assertFileNotExists(public_path('storage/photos/'.$item->image.'_4.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_3.png'));
            }
            else{
                $this->assertEquals('{"cropDimensions":"4:3","settingsScale":"100"}', json_encode($block->parameters()));
                $this->assertNotNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));
                
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_12.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_9.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_8.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_6.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_4.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_3.png'));
            }
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropDimensions" => "4:3",
                "ignoreDescription" => "on",
                "customSizes" => "on",
                "photoSizes" => ["8","3"],
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id)->specify();
            $item = Block\Slider::all()->last();
            
            $form = $item->form();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('{"cropDimensions":"4:3","ignoreDescription":"on","customSizes":"on","photoSizes":["8","3"],"settingsScale":"100"}', json_encode($block->parameters()));
                $this->assertNotNull($form->getElement('caption'));
                $this->assertNull($form->getElement('description'));
            }
            else{
                $this->assertEquals('{"cropDimensions":"4:3","settingsScale":"100"}', json_encode($block->parameters()));
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
