<?php

namespace Lubart\Just\Tests\Feature\Blocks\Contact;

use Lubart\Just\Tests\Feature\Blocks\BlockLocation;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;

class Actions extends BlockLocation {
    
    use WithFaker;

    protected $blockParams = [];

    protected $type = 'contact';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/articles'))){
            exec('rm -rf ' . public_path('storage/articles'));
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}')]);
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="title"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="envelope"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="phone"');
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}')]);

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
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}')]);
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'title' => $title = $this->faker->sentence,
            'envelope' => $address = $envelope = str_replace(["\n","'"], [", ", ""], $this->faker->address),
            'phone' => $phone = $this->faker->phoneNumber,
            'at' => $email = $this->faker->email
        ]);

        $item = Block\Contact::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($title, $block->firstItem()->title);
            $this->assertEquals($address, $block->firstItem()->contact('envelope'));
            $this->assertEquals($phone, $block->firstItem()->contact('phone'));
            $this->assertEquals($email, $block->firstItem()->contact('at'));
            
            $this->get('admin')
                ->assertSuccessful();
            
            $this->get('')
                ->assertSuccessful();
        }
        else{
            $response->assertRedirect('/login');
            $this->assertNull($item);
        }
    }

    public function create_new_item_with_a_lot_of_data($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","facebook","github", "youtube", "instagram", "pinterest", "soundcloud"],"additionalFields":null,"settingsScale":"100"}')]);

        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'title' => $title = $this->faker->sentence,
            'envelope' => $address = $envelope = str_replace(["\n","'"], [", ", ""], $this->faker->address),
            'facebook' => $facebook = $this->faker->url,
            'github' => $github = $this->faker->url,
            'youtube' => $youtube = $this->faker->url,
            'instagram' => $instagram = $this->faker->url,
            'pinterest' => $pinterest = $this->faker->url,
            'soundcloud' => $soundcloud = $this->faker->url
        ]);

        $item = Block\Contact::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($title, $block->firstItem()->title);
            $this->assertEquals($address, $block->firstItem()->contact('envelope'));
            $this->assertEquals($facebook, $block->firstItem()->contact('facebook'));
            $this->assertEquals($github, $block->firstItem()->contact('github'));
            $this->assertEquals($youtube, $block->firstItem()->contact('youtube'));
            $this->assertEquals($instagram, $block->firstItem()->contact('instagram'));
            $this->assertEquals($pinterest, $block->firstItem()->contact('pinterest'));
            $this->assertEquals($soundcloud, $block->firstItem()->contact('soundcloud'));

            $this->get('admin')
                ->assertSuccessful();

            $this->get('')
                ->assertSuccessful();
        }
        else{
            $response->assertRedirect('/login');
            $this->assertNull($item);
        }
    }

    public function receive_errors_on_creating_item_with_wrong_data() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at","facebook"],"additionalFields":null,"settingsScale":"100"}')]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'at' => $email = $this->faker->word,
            'facebook' => $this->faker->word,
        ])
            ->assertSessionHasErrors(['at', 'facebook']);
    }
    
    public function dont_receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope"],"additionalFields":null,"settingsScale":"100"}')]);
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ]);

        $item = Block\Contact::all()->last();
        
        if($assertion){
            $this->assertNotNull($item);
            $response->assertSuccessful();
        }
        else{
            $this->assertNull($item);
            $response->assertRedirect();
        }
    }
    
    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null,"settingsScale":"100"}')]);

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
        $block = $this->setupBlock();

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

            $this->assertEquals(100, $block->parameters->settingsScale);
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

    public function add_custom_contact_channel($assertion) {
        $block = $this->setupBlock();

        $this->get('admin/settings/'.$block->id.'/0');

        $this->post('admin/settings/setup', [
            "id" => $block->id,
            "additionalFields" => "custom=>field"
        ]);

        $block = Block::find($block->id);

        if($assertion){
            $this->assertEquals("custom=>field", $block->parameters->additionalFields);

            $form = $block->specify()->model()->form();
            $this->assertEquals(3, $form->count());
            $this->assertEquals([
                'title',
                'custom',
                'submit'
            ], array_keys($form->getElements()));
        }
        else{
            $this->assertNull(@$block->parameters->additionalFields);
        }
    }

    public function add_few_custom_contact_channels($assertion) {
        $block = $this->setupBlock();

        $this->get('admin/settings/'.$block->id.'/0');

        $this->post('admin/settings/setup', [
            "id" => $block->id,
            "additionalFields" => "custom=>field
newField=>New Field"
        ]);

        $block = Block::find($block->id);

        if($assertion){
            $form = $block->specify()->model()->form();
            $this->assertEquals(4, $form->count());
            $this->assertEquals([
                'title',
                'custom',
                'newField',
                'submit'
            ], array_keys($form->getElements()));
        }
        else{
            $this->assertNull(@$block->parameters->additionalFields);
        }
    }

    public function change_contact_channels($assertion) {
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope"],"additionalFields":null,"settingsScale":"100"}')]);

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
                ->assertSuccessful();
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
