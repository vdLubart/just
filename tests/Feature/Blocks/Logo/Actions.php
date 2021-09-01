<?php

namespace Just\Tests\Feature\Blocks\Logo;

use Illuminate\Support\Facades\Auth;
use Just\Models\Blocks\Logo;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
use Illuminate\Http\UploadedFile;
use Just\Models\User;
use Intervention\Image\ImageManagerStatic as Image;

class Actions extends LocationBlock {

    use WithFaker;

    protected $type = 'logo';

    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }

        if(file_exists(public_path('storage/logos'))){
            exec('rm -rf ' . public_path('storage/logos'));
        }

        parent::tearDown();
    }

    public function access_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $form = $block->form();
            $this->assertEquals(2, $form->count());
            $this->assertEquals(['block_id', 'id'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();

        Logo::insert([
            'block_id' => $block->id,
            'image' => $image = uniqid()
        ]);

        $item = Logo::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(6, $form->count());
            $this->assertEquals(['id', 'block_id', 'imagePreview_'.$block->id, 'image', 'caption', 'description'], array_keys($form->elements()));
            $this->assertEquals('<img src="/storage/logos/'.$image.'.png" width="300" />', $form->element('imagePreview_'.$block->id)->value());
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

        $item = Logo::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($item->image, $block->firstItem()->image);
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

        $response = $this->post("settings/block/item/save", [
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

        $item = Logo::all()->last();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'image' => UploadedFile::fake()->image('update.jpg'),
            'caption' => $caption = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph,
        ],
        [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $updatedItem = Logo::find($item->id);

        if($assertion){
            $response->assertSuccessful();

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

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'caption' => $caption = $this->faker->sentence,
            'description' => $description = $this->faker->paragraph
        ]);

        $twiceUpdatedItem = Logo::find($item->id);

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
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":true}')]);

        $this->post("settings/block/customize", [
            'cropPhoto' => 'on',
            'cropDimensions' => '4:3'
        ]);

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

            $item = Logo::all()->last();

            $this->assertFileExists(public_path('storage/logos/'.$item->image.".png"));

            $this->get('settings/block/'.$block->id.'/item/'.$item->id.'/cropping')
                ->assertSuccessful();

            $image = Image::make(public_path('storage/logos/'.$item->image.".png"));

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

            $item = Logo::all()->last();

            $this->assertFileExists(public_path('storage/logos/'.$item->image.".png"));

            $image = Image::make(public_path('storage/logos/'.$item->image.".png"));

            $this->assertEquals(1170, $image->width());
            $this->assertEquals(878, $image->height());
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
                "cropDimensions" => "4:3",
                "ignoreCaption" => "on",
                "customSizes" => "on",
                "photoSizes" => ["8","3"]
            ]);

            $this->post("settings/block/item/save", [
                'block_id' => $block->id,
                'id' => null,
                'image' => UploadedFile::fake()->image('photo.jpg')
            ],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

            $block = Block::find($block->id)->specify();
            $item = Logo::all()->last();

            $form = $item->itemForm();
            if(Auth::user()->role == 'master'){
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertTrue($block->parameters->ignoreCaption);
                $this->assertTrue($block->parameters->customSizes);
                $this->assertEquals(["8", "3"], $block->parameters->photoSizes);

                $this->assertNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));

                $this->assertFileExists(public_path('storage/logos/'.$item->image.'.png'));
                $this->assertFileDoesNotExist(public_path('storage/logos/'.$item->image.'_12.png'));
                $this->assertFileDoesNotExist(public_path('storage/logos/'.$item->image.'_9.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_8.png'));
                $this->assertFileDoesNotExist(public_path('storage/logos/'.$item->image.'_6.png'));
                $this->assertFileDoesNotExist(public_path('storage/logos/'.$item->image.'_4.png'));
                $this->assertFileExists(public_path('storage/logos/'.$item->image.'_3.png'));
            }
            else{
                $this->assertEquals("4:3", $block->parameters->cropDimensions);

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

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "cropDimensions" => "4:3",
                "ignoreDescription" => "on",
                "customSizes" => "on",
                "photoSizes" => ["8","3"]
            ]);

            $block = Block::find($block->id)->specify();
            $item = Logo::all()->last();

            $form = $item->itemForm();
            if(\Auth::user()->role == 'master'){
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertTrue($block->parameters->ignoreDescription);
                $this->assertTrue($block->parameters->customSizes);
                $this->assertEquals(["8", "3"], $block->parameters->photoSizes);

                $this->assertNotNull($form->getElement('caption'));
                $this->assertNull($form->getElement('description'));
            }
            else{
                $this->assertEquals("4:3", $block->parameters->cropDimensions);

                $this->assertNotNull($form->getElement('caption'));
                $this->assertNotNull($form->getElement('description'));
            }
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" =>	"desc"
            ]);

            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }
}
