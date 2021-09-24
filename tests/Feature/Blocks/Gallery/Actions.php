<?php

namespace Just\Tests\Feature\Blocks\Gallery;

use Illuminate\Support\Facades\Auth;
use Just\Models\Blocks\Gallery;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
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

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $response->assertSuccessful();

            $form = $block->item()->itemForm();
            $this->assertEquals(3, $form->count());
            $this->assertEquals(['id', 'block_id', 'externalUrl'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();

        Gallery::insert([
            'block_id' => $block->id,
            'image' => $image = uniqid()
        ]);

        $item = Gallery::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(6, $form->count());
            $this->assertEquals(['id', 'block_id', 'imagePreview_'.$block->id, 'image', 'caption', 'description'], array_keys($form->elements()));
            $this->assertEquals('<img src="/storage/photos/'.$image.'.png" width="300" />', $form->element('imagePreview_'.$block->id)->value());

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimensions" => "4:3",
                "orderDirection" =>	"desc"
            ]);

            $item = Gallery::all()->last();

            $form = $item->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['id', 'block_id', 'imagePreview_'.$block->id, 'recrop', 'image', 'caption', 'description'], array_keys($form->elements()));
        }
        else{
            $this->assertEquals(0, $item->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock();

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $item = Gallery::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($item->image, $block->firstItem()->image);

            $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
            $this->assertFileExists(public_path('storage/photos/'.$item->image.'_6.png'));
        }
        else{
            $response->assertRedirect('login');
            $this->assertNull($item);
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock();

        if(!$assertion){
            $user = User::where('role', 'admin')->first();
            $this->actingAs($user);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ])
            ->assertSuccessful();

        if(!$assertion){
            Auth::logout();
        }

        $item = Gallery::all()->last();

        if($assertion){
            $form = $item->itemForm();

            $this->assertEquals('<img src="/storage/photos/' .$item->image.'_3.png" />', $form->element('imagePreview_'.$block->id)->value());
        }

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'image' => UploadedFile::fake()->image('update.jpg'),
            'caption' => $caption = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $updatedItem = Gallery::find($item->id);

        if($assertion){
            $response->assertSuccessful();
            $this->assertEquals($item->id, $updatedItem->id);
            $this->assertNotEquals($item->image, $updatedItem->image);
            $this->assertEquals($caption, $updatedItem->caption);
            $this->assertEquals($description, $updatedItem->description);
        }
        else{
            $response->assertRedirect('login');
            $this->assertEquals($item->image, $updatedItem->image);
            $this->assertEquals('', $updatedItem->caption);
            $this->assertEquals('', $updatedItem->description);
        }

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'caption' => $caption = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph
        ]);

        $twiceUpdatedItem = Gallery::find($item->id);

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

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        if($assertion){
            $response->assertSuccessful();

            $item = Gallery::all()->last();

            $this->assertFileExists(public_path('storage/photos/'.$item->image.".png"));

            $this->get('settings/block/'.$block->id.'/item/'.$item->id.'/cropping')
                    ->assertSuccessful();

            $image = Image::make(public_path('storage/photos/'.$item->image.".png"));

            $this->assertNotEquals(1170, $image->width());
            $this->assertNotEquals(878, $image->height());

            $this->post("settings/block/item/crop", [
                'block_id' => 0,
                'id' => $item->id,
                'img' => $item->image,
                'x' => 0,
                'y' => 0,
                'w' => 1170,
                'h' => 878,
            ])
                ->assertRedirect('settings');

            $this->post("settings/block/item/crop", [
                'block_id' => $block->id,
                'id' => $item->id,
                'img' => $item->image,
                'x' => 0,
                'y' => 0,
                'w' => 1170,
                'h' => 878,
            ]);

            $item = Gallery::all()->last();
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

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ])
        ;

        if($assertion){
            $response->assertSuccessful();

            $item = Gallery::all()->last();

            $this->assertFileExists(public_path('storage/photos/'.$item->image.".png"));

            $image = Image::make(public_path('storage/photos/'.$item->image.".png"));

            $this->assertNotEquals(1170, $image->width());
            $this->assertNotEquals(878, $image->height());

            $this->post("settings/block/item/crop", [
                'block_id' => $block->id,
                'id' => $item->id,
                'img' => $item->image,
                'x' => 0,
                'y' => 0,
                'w' => 1170,
                'h' => 878,
            ])
                ->assertSuccessful();

            $item = Gallery::all()->last();
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

            $this->post("settings/block/item/crop", [
                'block_id' => $block->id,
                'id' => $item->id,
                'img' => $item->image,
                'x' => 0,
                'y' => 0,
                'w' => 500,
                'h' => 300,
            ]);

            $item = Gallery::all()->last();
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

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            if(\Auth::user()->role == 'master'){
                $this->assertCount(4, $form->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'ignoreCaption', 'ignoreDescription', 'customSizes', 'emptyParagraph', 'photoSizes', 'orderDirection', 'submit'], $form->names());
            }
            else{
                $this->assertCount(2, $form->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'orderDirection', 'submit'], $form->names());
            }

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "cropPhoto" => "true",
                "cropDimensions" => "4:3",
                "ignoreCaption" => "true",
                "customSizes" => "true",
                "photoSizes" => ["8","3"]
            ])
                ->assertSuccessful();

            $this->post("settings/block/item/save", [
                'block_id' => $block->id,
                'id' => null,
                'image' => UploadedFile::fake()->image('photo.jpg')
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ])
                ->assertSuccessful();

            $block = Block::find($block->id)->specify();
            $item = Gallery::all()->last();

            $form = $item->itemForm();
            $parameters = $block->parameters;

            if(\Auth::user()->role == 'master'){
                $this->assertEquals('4:3', $parameters->cropDimensions);
                $this->assertTrue($parameters->ignoreCaption);
                $this->assertTrue($parameters->customSizes);
                $this->assertEquals(["8", "3"], $parameters->photoSizes);

                $this->assertNull($form->element('caption'));
                $this->assertNotNull($form->element('description'));

                $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
                $this->assertFileDoesNotExist(public_path('storage/photos/'.$item->image.'_12.png'));
                $this->assertFileDoesNotExist(public_path('storage/photos/'.$item->image.'_9.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_8.png'));
                $this->assertFileDoesNotExist(public_path('storage/photos/'.$item->image.'_6.png'));
                $this->assertFileDoesNotExist(public_path('storage/photos/'.$item->image.'_4.png'));
                $this->assertFileExists(public_path('storage/photos/'.$item->image.'_3.png'));
            }
            else{
                $this->assertEquals('4:3', $parameters->cropDimensions);

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

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "cropDimensions" => "4:3",
                "ignoreDescription" => "true",
                "customSizes" => "true",
                "photoSizes" => ["8","3"]
            ]);

            $block = Block::find($block->id)->specify();
            $item = Gallery::all()->last();

            $form = $item->itemForm();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals('4:3', $parameters->cropDimensions);
                $this->assertTrue($parameters->ignoreCaption);
                $this->assertTrue($parameters->customSizes);
                $this->assertEquals(["8", "3"], $parameters->photoSizes);

                $this->assertNotNull($form->getElement('caption'));
                $this->assertNull($form->getElement('description'));
            }
            else{
                $this->assertEquals('4:3', $parameters->cropDimensions);

                $this->assertNotNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));
            }
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc",
            ]);

            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }

    public function create_item_with_standard_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ])
            ->assertSuccessful();

        $item = Gallery::all()->last();

        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileExists(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"photoSizes":["6","3"],"settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ])
            ->assertSuccessful();

        $item = Gallery::all()->last();

        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
        foreach ([6, 3] as $size) {
            $this->assertFileExists(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
        foreach ([12, 9, 8, 4] as $size) {
            $this->assertFileDoesNotExist(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_empty_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ])
            ->assertSuccessful();

        $item = Gallery::all()->last();

        $this->assertFileExists(public_path('storage/photos/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileDoesNotExist(public_path('storage/photos/' . $item->image . '_'.$size.'.png'));
        }
    }
}
