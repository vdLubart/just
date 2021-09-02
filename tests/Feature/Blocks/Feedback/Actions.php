<?php

namespace Just\Tests\Feature\Blocks\Feedback;

use Just\Models\Blocks\Feedback;
use Just\Tests\Feature\Blocks\LocationBlock;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
use Just\Models\User;
use Illuminate\Support\Facades\Notification;
use Just\Notifications\NewFeedback;

class Actions extends LocationBlock {

    use WithFaker;

    protected $type = 'feedback';

    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }

        parent::tearDown();
    }

    public function access_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->get("settings/block/".$block->id."/item/0");

        if($assertion){
            $response->assertSuccessful();

            $form = $block->item()->itemForm();
            $this->assertEquals(6, $form->count());
            $this->assertEquals(['id', 'block_id', 'username', 'email', 'message', 'submit'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');

            $this->assertEquals(0, $block->item()->itemForm()->count());
        }

        $response = $this->get("");

        $response->assertSee('input name="username"', false);
        $response->assertSee('input name="email"', false);
        $response->assertSee('textarea name="message"', false);
        $response->assertSee('div class="g-recaptcha"', false);
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();

        Feedback::insert([
            'block_id' => $block->id,
            'username' => $name = $this->faker->name,
            'email' => $email = $this->faker->email,
            'message' => $message = $this->faker->paragraph,
        ]);

        $item = Feedback::all()->last();

        if($assertion){
            $form = $item->itemForm();
            $this->assertEquals(7, $form->count());
            $this->assertEquals(['id', 'block_id', 'username', 'email', 'created', 'message', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($name, $form->getElement('username')->value());
            $this->assertEquals($email, $form->getElement('email')->value());
            $this->assertEquals($message, $form->getElement('message')->value());
        }
        else{
            $this->assertEquals(0, $item->itemForm()->count());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'username' => $name = $this->faker->name,
            'email' => $email = $this->faker->email,
            'message' => $message = $this->faker->paragraph
        ]);

        $item = Feedback::all()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($name, $block->firstItem()->username);
            $this->assertEquals($email, $block->firstItem()->email);
            $this->assertEquals($message, $block->firstItem()->message);

            $this->get('admin')
                    ->assertSuccessful();

            $this->get('')
                    ->assertSuccessful();
        }
        else{
            $this->assertNull($item);
        }
    }

    public function receive_an_error_on_sending_incomplete_create_item_form($assertion){
        $block = $this->setupBlock();

        $response = $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null
        ]);

        $item = Feedback::all()->last();

        $this->assertNull($item);

        if($assertion){
            $response->assertSessionHasErrors(['username', 'email', 'message']);
        }
        else{
            $response->assertRedirect('/login');
        }
    }

    public function leave_feedback_from_the_website($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"defaultActivation":"1","successText":"Thank you for your feedback","notify":"1"}')]);

        $this->app['router']->post('feedback/add', "\Just\Controllers\JustController@post")->middleware('web');

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('{"success":true}');

        $note = Notification::fake();

        $this->post("feedback/add", [
            'block_id' => $block->id,
            'username' => $name = $this->faker->name,
            'email' => $email = $this->faker->email,
            'message' => $message = $this->faker->paragraph,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHas('successMessageFromFeedback'.$block->id);

        $note->assertSentTo(User::where('role', 'admin')->first(), NewFeedback::class);

        $item = Feedback::all()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($name, $block->firstItem()->username);
            $this->assertEquals($email, $block->firstItem()->email);
            $this->assertEquals($message, $block->firstItem()->message);

            $this->get('')
                    ->assertSuccessful();
        }
        else{
            $this->assertNull($item);
        }
    }

    public function receive_an_error_on_sending_incompleate_feedback_on_the_website(){
        $block = $this->setupBlock(['parameters'=>json_decode('{"defaultActivation":"1","successText":"Thank you for your feedback","notify":"1"}')]);

        $this->app['router']->post('feedback/add', "\Just\Controllers\JustController@post")->middleware('web');

        $note = Notification::fake();

        $this->post("feedback/add", [
            'block_id' => $block->id
        ])
            ->assertSessionHasErrors(['username', 'email', 'message', 'g-recaptcha-response'], 'messages', 'errorsFromFeedback'.$block->id)
            ->assertRedirect();

        $note->assertNotSentTo(User::where('role', 'admin')->first(), NewFeedback::class);

        $item = Feedback::all()->last();

        $this->assertNull($item);
    }

    public function create_few_items_in_block($assertion){
        $block = $this->setupBlock();

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'username' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $firstMessage = $this->faker->paragraph
        ]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'username' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $secondMessage = $this->faker->paragraph
        ]);

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => null,
            'username' => $this->faker->name,
            'email' => $this->faker->email,
            'message' => $thirdMessage = $this->faker->paragraph
        ]);

        if($assertion){
            $this->assertDatabaseHas('feedbacks', ['message'=>$firstMessage])
                ->assertDatabaseHas('feedbacks', ['message'=>$secondMessage])
                ->assertDatabaseHas('feedbacks', ['message'=>$thirdMessage]);
        }
        else{
            $this->assertDatabaseMissing('feedbacks', ['message'=>$firstMessage])
                ->assertDatabaseMissing('feedbacks', ['message'=>$secondMessage])
                ->assertDatabaseMissing('feedbacks', ['message'=>$thirdMessage]);
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
            'username' => $name = $this->faker->name,
            'email' => $email = $this->faker->email,
            'message' => $this->faker->paragraph,
        ]);

        if(!$assertion){
            \Auth::logout();
        }

        $item = Feedback::all()->last();

        $date = $item->created_at;

        $this->post("settings/block/item/save", [
            'block_id' => $block->id,
            'id' => $item->id,
            'username' => $name,
            'email' => $email,
            'message' => $updatedMessage = $this->faker->paragraph,
            'created' => $date = $this->faker->date('Y-m-d')
        ]);

        $item = Feedback::all()->last();

        if($assertion){
            $this->assertEquals($updatedMessage, $item->message);
            $this->assertNotEquals($date, $item->created_at);
        }
        else{
            $this->assertNotEquals($updatedMessage, $item->text);
        }
    }

    public function customize_block($assertion){
        $block = $this->setupBlock();

        $response = $this->get('settings/block/'.$block->id.'/customization');

        if($assertion){
            $response->assertStatus(200);

            $form = $block->customizationForm();

            $this->assertCount(2, $form->groups());

            $this->assertEquals(['id', 'defaultActivation', 'successText', 'notify', 'orderDirection', 'submit'], $form->names());

            $this->post('settings/block/customize', [
                "id" => $block->id,
                "orderDirection" => "asc",
            ]);

            $block = Block::find($block->id);

            $this->assertEquals('asc', $block->parameters->orderDirection);
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
}
