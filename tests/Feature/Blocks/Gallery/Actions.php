<?php

namespace Just\Tests\Feature\Blocks\Gallery;

use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;
use Just\Models\User;
use Intervention\Image\ImageManagerStatic as Image;

class Actions extends LocationBlock {
    
    use WithFaker;

    protected $type = 'gallery';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/photos'))){
            exec('rm -rf ' . public_path('storage/photos'));
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
        
        Block\Gallery::insert([
            'block_id' => $block->id,
            'image' => $image = uniqid()
        ]);
        
        $item = Block\Gallery::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(5, $form->count());
            $this->assertEquals(['imageUploader', 'imagePreview_'.$block->id, 'caption', 'description', 'startUpload'], array_keys($form->getElements()));
            $this->assertEquals('<img src="/storage/photos/'.$image.'.png" width="300" />', $form->getElement('imagePreview_'.$block->id)->value());

            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimensions" => "4:3",
                "settingsScale" => "100",
                "orderDirection" =>	"desc"
            ]);

            $item = Block\Gallery::all()->last();

            $form = $item->form();
            $this->assertEquals(6, $form->count());
            $this->assertEquals(['imageUploader', 'imagePreview_'.$block->id, 'recrop', 'caption', 'description', 'startUpload'], array_keys($form->getElements()));
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock();
        
        $re = $this->post("admin/ajaxuploader", [
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

        $item = Block\Gallery::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($item->image, $block->firstItem()->image);
            
            $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
            $this->assertFileExists(public_path('storage/photos/'.$item->image.'_6.png'));
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
        
        $updatedItem = Block\Gallery::find($item->id);
        
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

        $twiceUpdatedItem = Block\Gallery::find($item->id);
        
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
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":true,"cropDimensions":"4:3"}')]);
        
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
            
            $item = Block\Gallery::all()->last();
            
            $this->assertFileExists(public_path('storage/photos/'.$item->image.".png"));
            
            $this->get('admin/settings/crop/'.$block->id.'/'.$item->id)
                    ->assertSuccessful();

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

    public function recrop_photo($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":true,"cropDimensions":"4:3"}')]);

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

            $item = Block\Gallery::all()->last();

            $this->assertFileExists(public_path('storage/photos/'.$item->image.".png"));

            $this->get('admin/settings/crop/'.$block->id.'/'.$item->id)
                ->assertSuccessful();

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
            $this->assertFileExists(public_path('storage/photos/'.$item->image."_original.png"));

            $image = Image::make(public_path('storage/photos/'.$item->image.".png"));
            $originImage = Image::make(public_path('storage/photos/'.$item->image."_original.png"));
            $originWidth = $originImage->width();
            $originHeight = $originImage->height();
            $originImage->destroy();

            $this->assertEquals(1170, $image->width());
            $this->assertEquals(878, $image->height());
            $image->destroy();

            $this->post("/admin/settings/crop", [
                'block_id' => $block->id,
                'id' => $item->id,
                'img' => $item->image,
                'x' => 0,
                'y' => 0,
                'w' => 500,
                'h' => 300,
            ]);

            $image = Image::make(public_path('storage/photos/'.$item->image.".png"));
            $originImage = Image::make(public_path('storage/photos/'.$item->image."_original.png"));

            $this->assertEquals($originWidth, $originImage->width());
            $this->assertEquals($originHeight, $originImage->height());

            $this->assertEquals(500, $image->width());
            $this->assertEquals(300, $image->height());
            $image->destroy();
            $originImage->destroy();
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
                $response->assertDontSee('Image Fields')
                        ->assertDontSee('Resize Images');
                
                $this->assertCount(4, $block->setupForm()->groups());
            
                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());
            }
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropDimensions" => "4:3",
                "ignoreCaption" => "on",
                "customSizes" => "on",
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
            $item = Block\Gallery::all()->last();
            
            $form = $item->form();
            $parameters = $block->parameters;

            if(\Auth::user()->role == 'master'){
                $this->assertEquals('4:3', $parameters->cropDimensions);
                $this->assertTrue($parameters->ignoreCaption);
                $this->assertTrue($parameters->customSizes);
                $this->assertEquals(["8", "3"], $parameters->photoSizes);
                $this->assertEquals(100, $parameters->settingsScale);

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
                $this->assertEquals('4:3', $parameters->cropDimensions);
                $this->assertEquals(100, $parameters->settingsScale);

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
            $item = Block\Gallery::all()->last();
            
            $form = $item->form();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('4:3', $parameters->cropDimensions);
                $this->assertTrue($parameters->ignoreCaption);
                $this->assertTrue($parameters->customSizes);
                $this->assertEquals(["8", "3"], $parameters->photoSizes);
                $this->assertEquals(100, $parameters->settingsScale);

                $this->assertNotNull($form->getElement('caption'));
                $this->assertNull($form->getElement('description'));
            }
            else{
                $this->assertEquals('4:3', $parameters->cropDimensions);
                $this->assertEquals(100, $parameters->settingsScale);

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

            $this->assertEmpty((array)$block->parameters);
        }
    }

    public function create_item_with_standard_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"settingsScale":"100","orderDirection":"desc"}')]);

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

        $item = Block\Gallery::all()->last();

        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileExists(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"photoSizes":["6","3"],"settingsScale":"100","orderDirection":"desc"}')]);

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

        $item = Block\Gallery::all()->last();

        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
        foreach ([6, 3] as $size) {
            $this->assertFileExists(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
        foreach ([12, 9, 8, 4] as $size) {
            $this->assertFileNotExists(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_empty_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"settingsScale":"100","orderDirection":"desc"}')]);

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

        $item = Block\Gallery::all()->last();

        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileNotExists(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
    }
}
