<?php

namespace Just\Tests\Feature\Blocks\Contact;

use Illuminate\Support\Facades\Auth;
use Just\Models\Blocks\Contact;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;

class Actions extends LocationBlock {

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
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null}')]);

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $response->assertSuccessful();

            $form = $block->item()->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['id', 'block_id', 'title', 'envelope', 'phone', 'at', 'submit'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null}')]);

        $envelope = str_replace("\n", ", ", $this->faker->address);
        $phone = $this->faker->phoneNumber;
        $at = $this->faker->email;

        Contact::insert([
            'block_id' => $block->id,
            'channels' => '{"envelope":"'.$envelope.'","phone":"'.$phone.'","at":"'.$at.'"}'
        ]);

        $item = Contact::all()->last();
        $item->title = '{"en":"'.($title = $this->faker->sentence).'"}';
        $item->save();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['id', 'block_id', 'title', 'envelope', 'phone', 'at', 'submit'], array_keys($form->elements()));

            $this->assertEquals($title, json_decode($form->element('title')->value())->en);
            $this->assertEquals($envelope, $form->element('envelope')->value());
            $this->assertEquals($phone, $form->element('phone')->value());
            $this->assertEquals($at, $form->element('at')->value());
        }
        else{
            $this->assertEquals(0, $item->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null}')]);
        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'title' => '{"en":"'.($title = $this->faker->sentence).'"}',
            'envelope' => $address = $envelope = str_replace(["\n","'"], [", ", ""], $this->faker->address),
            'phone' => $phone = $this->faker->phoneNumber,
            'at' => $email = $this->faker->email
        ]);

        $item = Contact::all()->last();

        if($assertion){
            $response->assertSuccessful();
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($title, $block->firstItem()->title);
            $this->assertEquals($address, $block->firstItem()->contact('envelope'));
            $this->assertEquals($phone, $block->firstItem()->contact('phone'));
            $this->assertEquals($email, $block->firstItem()->contact('at'));

            $this->get('admin')
                ->assertSuccessful()
                ->assertSee($envelope)
                ->assertSee($phone)
                ->assertSee($email)
                ->assertSee($title);

            $this->get('')
                ->assertSuccessful()
                ->assertSee($envelope)
                ->assertSee($phone)
                ->assertSee($email)
                ->assertSee($title);
        }
        else{
            $response->assertRedirect('/login');
            $this->assertNull($item);
        }
    }

    public function create_new_item_with_a_lot_of_data($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","facebook","github", "youtube", "instagram", "pinterest", "soundcloud"],"additionalFields":null}')]);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'title' => '{"en":"'.($title = $this->faker->sentence).'"}',
            'envelope' => $address = $envelope = str_replace(["\n","'"], [", ", ""], $this->faker->address),
            'facebook' => $facebook = $this->faker->url,
            'github' => $github = $this->faker->url,
            'youtube' => $youtube = $this->faker->url,
            'instagram' => $instagram = $this->faker->url,
            'pinterest' => $pinterest = $this->faker->url,
            'soundcloud' => $soundcloud = $this->faker->url
        ]);

        $item = Contact::all()->last();

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
                ->assertSuccessful()
                ->assertSee($envelope)
                ->assertSee($facebook)
                ->assertSee($github)
                ->assertSee($title)
                ->assertSee($youtube)
                ->assertSee($instagram)
                ->assertSee($pinterest)
                ->assertSee($soundcloud);

            $this->get('')
                ->assertSuccessful()
                ->assertSee($envelope)
                ->assertSee($facebook)
                ->assertSee($github)
                ->assertSee($title)
                ->assertSee($youtube)
                ->assertSee($instagram)
                ->assertSee($pinterest)
                ->assertSee($soundcloud);
        }
        else{
            $response->assertRedirect('/login');
            $this->assertNull($item);
        }
    }

    public function receive_errors_on_creating_item_with_wrong_data() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at","facebook"],"additionalFields":null}')]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'at' => $email = $this->faker->word,
            'facebook' => $this->faker->word,
        ])
            ->assertSessionHasErrors(['at', 'facebook']);
    }

    public function dont_receive_an_error_on_sending_incomplete_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope"],"additionalFields":null}')]);

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null
        ]);

        $item = Contact::all()->last();

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
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope","phone","at"],"additionalFields":null}')]);

        $envelope = $envelope = str_replace("\n", ", ", $this->faker->address);
        $phone = $this->faker->phoneNumber;
        $at = $this->faker->email;

        Contact::insert([
            'block_id' => $block->id,
            'channels' => '{"envelope":"'.$envelope.'","phone":"'.$phone.'","at":"'.$at.'"}'
        ]);

        $item = Contact::all()->last();
        $item->title = $title = $this->faker->sentence;
        $item->save();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'title' => $title,
            'envelope' => $address = $envelope = str_replace("\n", ", ", $this->faker->address),
            'phone' => $phone,
            'at' => $email = $this->faker->email
        ]);

        $item = Contact::all()->last();

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

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            $this->assertCount(Auth::user()->role == 'master' ? 5 : 4, $form->names());

            if(\Auth::user()->role == 'master'){
                $fields = ['id', 'channels', 'additionalFields', 'orderDirection', 'submit'];
            }
            else{
                $fields = ['id', 'channels', 'orderDirection', 'submit'];
            }

            $this->assertEquals($fields, $form->names());

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc"
            ]);

            $block = Block::find($block->id);

            $this->assertEquals('asc', $block->parameters->orderDirection);
        }
        else{
            $response->assertStatus(302);

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc"
            ]);

            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }

    public function add_custom_contact_channel($assertion) {
        $block = $this->setupBlock();

        $this->post('settings/block/customize', [
            "id" => $block->id,
            "additionalFields" => "custom=>field"
        ]);

        $block = Block::find($block->id);

        if($assertion){
            $this->assertEquals("custom=>field", $block->parameters->additionalFields);

            $form = $block->item()->itemForm();
            $this->assertEquals(5, $form->count());
            $this->assertEquals([
                'id',
                'block_id',
                'title',
                'custom',
                'submit'
            ], array_keys($form->elements()));
        }
        else{
            $this->assertNull(@$block->parameters->additionalFields);
        }
    }

    public function add_few_custom_contact_channels($assertion) {
        $block = $this->setupBlock();

        $response = $this->post('settings/block/customize', [
            "id" => $block->id,
            "additionalFields" => "custom=>field
newField=>New Field"
        ]);

        $block = Block::find($block->id);

        if($assertion){
            $response->assertSuccessful();
            $form = $block->item()->itemForm();
            $this->assertEquals(6, $form->count());
            $this->assertEquals([
                'id',
                'block_id',
                'title',
                'custom',
                'newField',
                'submit'
            ], array_keys($form->elements()));
        }
        else{
            $this->assertNull(@$block->parameters->additionalFields);
        }
    }

    public function change_contact_channels($assertion) {
        $block = $this->setupBlock(['parameters'=>json_decode('{"channels":["envelope"],"additionalFields":null}')]);

        if($assertion){
            $this->post('settings/block/customize', [
                "id" => $block->id,
                "channels" => ['phone']
            ])
                ->assertSuccessful();

            $this->post("settings/block/item/save", [
                'block_id' => $block->id,
                'id' => null,
                'title' => '{"en":"'.($title = $this->faker->sentence).'"}',
                'envelope' => $address= str_replace(["\n","'"], [", ", ""], $this->faker->address),
            ])
                ->assertSuccessful();

            $this->get('')
                ->assertSee($title)
                ->assertDontSee($address)
                ->assertSuccessful();
        }
        else{
            $this->post('settings/block/customize', [
                "id" => $block->id,
                "phone" => 'on'
            ])
                ->assertRedirect('/login');
        }

    }
}
