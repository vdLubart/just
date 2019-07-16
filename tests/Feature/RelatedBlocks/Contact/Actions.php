<?php

namespace Lubart\Just\Tests\Feature\RelatedBlocks\Contact;

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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact', 'super_parameters'=>'{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="title"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="envelope"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="phone"');
    }

    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'super_parameters'=>'{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}'])->specify();

        $envelope = str_replace("\n", ", ", $this->faker->address);
        $phone = $this->faker->phoneNumber;
        $at = $this->faker->email;
        
        Block\Contact::insert([
            'block_id' => $block->id,
            'title' => $title = $this->faker->sentence,
            'channels' => '{"envelope":"'.$envelope.'","phone":"'.$phone.'","at":"'.$at.'"}'
        ]);
        
        $item = Block\Contact::all()->last();

        if($assertion){
            $form = $item->form();
            $this->assertEquals(5, $form->count());
            $this->assertEquals([
                    'title',
                    'envelope',
                    'phone',
                    'at',
                    'submit'
                ], array_keys($form->getElements()));

            $this->assertEquals($title, $form->getElement('title')->value());
            $this->assertEquals($envelope, $form->getElement('envelope')->value());
            $this->assertEquals($phone, $form->getElement('phone')->value());
            $this->assertEquals($at, $form->getElement('at')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact', 'super_parameters'=>'{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}'])->specify();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'title' => $title = $this->faker->sentence,
            'envelope' => $address = $envelope = str_replace(["\n","'"], [", ", ""], $this->faker->address),
            'phone' => $phone = $this->faker->phoneNumber,
            'at' => $email = $this->faker->email
        ]);
        
        $item = Block\Contact::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($title, $block->firstItem()->title);
            $this->assertEquals($address, $block->firstItem()->contact('envelope'));
            $this->assertEquals($phone, $block->firstItem()->contact('phone'));
            $this->assertEquals($email, $block->firstItem()->contact('at'));
            
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact', 'super_parameters'=>'{"channels":["envelope"],"additionalFields":null,"settingsScale":"100"}'])->specify();
        
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact', 'super_parameters'=>'{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}'])->specify();

        $envelope = $envelope = str_replace("\n", ", ", $this->faker->address);
        $phone = $this->faker->phoneNumber;
        $at = $this->faker->email;

        Block\Contact::insert([
            'block_id' => $block->id,
            'title' => $title = $this->faker->sentence,
            'channels' => '{"envelope":"'.$envelope.'","phone":"'.$phone.'","at":"'.$at.'"}'
        ]);
        
        $item = Block\Contact::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'title' => $title,
            'envelope' => $address = $envelope = str_replace("\n", ", ", $this->faker->address),
            'phone' => $phone,
            'at' => $email = $this->faker->email
        ]);
        
        $item = Block\Contact::all()->last();
        
        if($assertion){
            $this->assertEquals($title, $item->title);
            $this->assertEquals($address, $item->contact('envelope'));
            $this->assertEquals($phone, $item->contact('phone'));
            $this->assertEquals($email, $item->contact('at'));
        }
        else{
            $this->assertEquals($title, $item->title);
            $this->assertNotEquals($address, $item->contact('envelope'));
            $this->assertEquals($phone, $item->contact('phone'));
            $this->assertNotEquals($email, $item->contact('at'));
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            
            $this->assertCount(\Auth::user()->role == 'master' ? 5 : 4, $block->setupForm()->groups());

            if(\Auth::user()->role == 'master'){
                $fields = ['id', 'channels[]', 'additionalFields', 'settingsScale', 'orderDirection', 'submit'];
            }
            else{
                $fields = ['id', 'channels[]', 'settingsScale', 'orderDirection', 'submit'];
            }
            
            $this->assertEquals($fields, $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertEquals('{"settingsScale":"100"}', json_encode($block->parameters()));
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

    public function add_custom_contact_channel($assertion) {
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact'])->specify();

        $this->get('admin/settings/'.$block->id.'/0');

        $this->post('admin/settings/setup', [
            "id" => $block->id,
            "additionalFields" => "custom=>field"
        ]);

        $block = Block::find($block->id);

        if($assertion){
            $this->assertEquals('{"additionalFields":"custom=>field"}', json_encode($block->parameters()));

            $form = $block->specify()->model()->form();
            $this->assertEquals(3, $form->count());
            $this->assertEquals([
                'title',
                'custom',
                'submit'
            ], array_keys($form->getElements()));
        }
        else{
            $this->assertNotEquals('{"settingsScale":"100"}', json_encode($block->parameters()));
        }
    }

    public function change_contact_channels($assertion) {
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'contact', 'super_parameters'=>'{"channels":["envelope"],"additionalFields":null,"settingsScale":"100"}'])->specify();

        $response = $this->get('admin/settings/'.$block->id.'/0');

        if($assertion){
            $response->assertStatus(200);

            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100",
                "channels" => ['phone']
            ])
                ->assertSuccessful();

            $this->post("", [
                'block_id' => $block->id,
                'id' => null,
                'title' => $title = $this->faker->sentence,
                'envelope' => str_replace(["\n","'"], [", ", ""], $this->faker->address),
            ])
                ->assertSuccessful();

            $this->get('')
                ->assertSuccessful()
                ->assertSee($title);
        }
        else{
            $response->assertStatus(302);

            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100",
                "phone" => 'on'
            ])
                ->assertRedirect('/login');
        }

    }
}