<?php

namespace Lubart\Just\Tests\Feature\Blocks\ImageLibrary;

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
        
        parent::tearDown();
    }
    
    public function access_library($assertion){
        $response = $this->get('admin/browseimages');
        
        if($assertion){
            $response->assertSee('Image Library')
                    ->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }
    }
    
    public function upload_image_to_the_library($assertion){
        $this->post('admin/uploadimage', [
             'image' => UploadedFile::fake()->image('random.jpg')
        ]);
        
        if($assertion){
            $this->assertFileExists(public_path('images/library/random.png'));
        }
        else{
            $this->assertFileNotExists(public_path('images/library/random.png'));
        }
        
        $this->post('admin/uploadimage', [
             'image' => UploadedFile::fake()->image('random.jpg')
        ]);
        
        if($assertion){
            $this->assertFalse(empty(glob(public_path('images/library/random_*.png'))));
        }
        else{
            $this->assertTrue(empty(glob(public_path('images/library/random*'))));
        }
        
        foreach(glob(public_path('images/library/random*')) as $file){
            unlink($file);
        }
    }
}
