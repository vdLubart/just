<?php

namespace Lubart\Just\Tests\Feature\Blocks\Contact;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;

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
    
    public function access_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="title"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="address"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="phone"');
    }

    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();
        
        Block\Contact::insert([
            'block_id' => $block->id,
            'title' => $title = $this->faker->sentence,
            'address' => $address = $this->faker->address,
            'phone' => $phone = $this->faker->phoneNumber,
            'email' => $email = $this->faker->email
        ]);
        
        $item = Block\Contact::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(23, $form->count());
            $this->assertEquals([
                    'title',
                    'address',
                    'phone',
                    'phone2',
                    'fax',
                    'email',
                    'facebook',
                    'youtube',
                    'twitter',
                    'linkedin',
                    'github',
                    'google-plus',
                    'instagram',
                    'pinterest',
                    'reddit',
                    'skype',
                    'slack',
                    'soundcloud',
                    'telegram',
                    'viber',
                    'vimeo',
                    'whatsapp',
                    'submit'
                ], array_keys($form->getElements()));
            $this->assertEquals($title, $form->getElement('title')->value());
            $this->assertEquals($address, $form->getElement('address')->value());
            $this->assertEquals($phone, $form->getElement('phone')->value());
            $this->assertEquals($email, $form->getElement('email')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact'])->specify();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'title' => $title = $this->faker->sentence,
            'address' => $address = $this->faker->address,
            'phone' => $phone = $this->faker->phoneNumber,
            'email' => $email = $this->faker->email
        ]);
        
        $item = Block\Contact::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($title, $block->firstItem()->title);
            $this->assertEquals($address, $block->firstItem()->address);
            $this->assertEquals($phone, $block->firstItem()->phone);
            $this->assertEquals($email, $block->firstItem()->email);
            
            $this->get('admin')
                    ->assertSee($title)
                    ->assertSee($address)
                    ->assertSee($phone)
                    ->assertSee($email);
            
            $this->get('')
                    ->assertSee($title)
                    ->assertSee($address)
                    ->assertSee($phone)
                    ->assertSee($email);
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($title)
                    ->assertDontSee($address)
                    ->assertDontSee($phone)
                    ->assertDontSee($email);
            
            $this->get('')
                    ->assertDontSee($title)
                    ->assertDontSee($address)
                    ->assertDontSee($phone)
                    ->assertDontSee($email);
        }
    }
    
    public function dont_receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact'])->specify();
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ]);
        
        $item = Block\Contact::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            $response->assertStatus(200);
        }
        else{
            $this->assertNull($item);
            $response->assertRedirect();
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact'])->specify();
        
        Block\Contact::insert([
            'block_id' => $block->id,
            'title' => $title = $this->faker->sentence,
            'address' => $this->faker->address,
            'phone' => $phone = $this->faker->phoneNumber,
            'email' => $this->faker->email
        ]);
        
        $item = Block\Contact::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'title' => $title,
            'address' => $address = $this->faker->address,
            'phone' => $phone,
            'email' => $email = $this->faker->email
        ]);
        
        $item = Block\Contact::all()->last();
        
        if($assertion){
            $this->assertEquals($title, $item->title);
            $this->assertEquals($address, $item->address);
            $this->assertEquals($phone, $item->phone);
            $this->assertEquals($email, $item->email);
        }
        else{
            $this->assertEquals($title, $item->title);
            $this->assertNotEquals($address, $item->address);
            $this->assertEquals($phone, $item->phone);
            $this->assertNotEquals($email, $item->email);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            
            $this->assertCount(2, $block->setupForm()->groups());
            
            $this->assertEquals(['id', 'settingsScale', 'submit'], $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertEquals('{"settingsScale":"100"}', $block->parameters);
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"settingsScale":"100"}', $block->parameters);
        }
    }
}
