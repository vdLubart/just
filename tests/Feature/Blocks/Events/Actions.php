<?php

namespace Just\Tests\Feature\Blocks\Events;

use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Just\Models\User;
use Just\Notifications\NewRegistration;
use Just\Tests\Feature\Blocks\LocationBlock;
use Just\Tests\Feature\Helper;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;

class Actions extends LocationBlock {
    
    use WithFaker;
    use Helper;

    protected $type = 'events';
    
    protected function tearDown(): void{
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/events'))){
            exec('rm -rf ' . public_path('storage/events'));
        }
        
        parent::tearDown();
    }
    
    public function access_item_form_without_initial_data($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->assertDontSee('input name="subject"');
        
        $response->{($assertion?'assertDontSee':'assertSee')}('Item Route');
        $response->{($assertion?'assertDontSee':'assertSee')}('Settings View Scale');
        
        $this->post('admin/settings/setup', [
            "id" => $block->id,
            "cropPhoto" =>	"on",
            "cropDimensions" => "4:3",
            "itemRouteBase" => "event",
            "settingsScale" => "100",
            "orderDirection" =>	"desc"
        ])
                ->assertStatus(200);
        
        $block = Block::find($block->id);

        if(\Auth::id()){
            $this->assertTrue($block->parameters->cropPhoto);
            $this->assertEquals("4:3", $block->parameters->cropDimensions);
            $this->assertEquals("event", $block->parameters->itemRouteBase);
            $this->assertEquals("100", $block->parameters->settingsScale);
            $this->assertEquals("desc", $block->parameters->orderDirection);
        }
        else{
            $this->assertEmpty($block->parameters);
        }
        
    }
    
    public function access_item_form_when_block_is_setted_up($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="image"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="subject"');
    }

    public function access_edit_item_form($assertion){
        $block = $this->setupBlock();
        
        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $item = Block\Events::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(11, $form->count());
            $this->assertEquals(['topGroup', 'startDate', 'endDate'], array_keys($form->groups()));
            $this->assertEquals(['image', 'imagePreview_'.$item->id, 'subject'], array_keys($form->group('topGroup')->getElements()));
            $this->assertEquals(['location', 'summary', 'text', 'submit', 'image', 'imagePreview_'.$item->id, 'subject', 'start_date', 'start_time', 'end_date', 'end_time'], array_keys($form->elements()));
            $this->assertEquals($text, $form->getElement('text')->value());

            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"on",
                "cropDimensions" => "4:3",
                "itemRouteBase" => "event",
                "settingsScale" => "100",
                "orderDirection" =>	"desc"
            ]);

            $item = Block\Events::all()->last();

            $form = $item->form();
            $this->assertEquals(12, $form->count());
            $this->assertEquals(['image', 'imagePreview_'.$item->id, 'recrop', 'subject'], array_keys($form->group('topGroup')->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());
        }
        else{
            $this->assertNull($item->form());
        }
    }

    public function create_new_item_in_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);

        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("+2 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("+4 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        if($assertion){
            $response->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }

        $eventRoute = \Just\Models\Route::where('route', 'event/{id}')->first();
        $this->assertNotNull($eventRoute);
        
        $item = Block\Events::all()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEventBlockCreatedSuccessfully($item, $subject, $summary, $text, $location);

            if(!is_null($this->blockParams['panelLocation'])) {
                $this->get('admin')
                    ->assertSee($subject)
                    ->assertSee($summary)
                    ->assertSee($location);

                $this->get('')
                    ->assertSee($subject)
                    ->assertSee($summary)
                    ->assertSee($location);
            }
            else{
                $this->get('admin')
                    ->assertSuccessful();

                $this->get('')
                    ->assertSuccessful();
            }
        }
        else{
            $this->assertNull($item);
            
            $this->get('admin')
                    ->assertDontSee($subject)
                    ->assertDontSee($summary);
            
            $this->get('')
                    ->assertDontSee($subject)
                    ->assertDontSee($summary);
        }

        if(\Auth::check()){
            $form = $item->form();

            $this->assertEquals('<img src="/storage/events/'.$item->image.'_3.png" />', $form->group('topGroup')->getElement('imagePreview'.'_'.$item->id)->value());
        }
    }
    
    public function receive_an_error_on_sending_incompleate_create_item_form($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ]);
        
        $item = Block\Articles::all()->last();
        
        $this->assertNull($item);
        
        if($assertion){
            $response->assertSessionHasErrors(['subject', 'start_date']);
        }
        else{
            $response->assertRedirect('/login');
        }
    }

    public function receive_an_error_on_wrong_date_and_time_format($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);

        $this->get("admin/settings/".$block->id."/0");

        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $this->faker->sentence,
            'start_date' => $this->faker->word,
            'start_time' => $this->faker->word,
            'end_date' => $this->faker->word,
            'end_time' => $this->faker->word
        ]);

        $item = Block\Articles::all()->last();

        $this->assertNull($item);

        if($assertion){
            $response->assertSessionHasErrors(['start_date', 'start_time', 'end_date', 'end_time']);
        }
        else{
            $response->assertRedirect('/login');
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();
        
        $item = Block\Events::all()->last();

        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'subject' => $newSubject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("+4 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("+6 hours")->format("H:i"),
            'location' => $newLocation = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $newSummary = $this->faker->text,
            'text' => $newText = $this->faker->text,
        ]);
        
        $item = Block\Events::all()->last();
        
        if($assertion){
            $this->assertEventBlockCreatedSuccessfully($item, $newSubject, $newSummary, $newText, $newLocation, "+4 hours", "+6 hours");
        }
        else{
            $this->assertNotEquals($newText, $item->text);
        }
    }
    
    public function access_created_item($assertion){
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();
        
        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $item = Block\Events::all()->last();

        if($assertion){
            if(\Auth::id()){
                $this->get('admin/event/'.$item->id)
                    ->assertSuccessful();
            }
            else{
                $this->get('admin/event/'.$item->id)
                        ->assertStatus(302);
            }

            $this->get('event/'.$item->id)
                ->assertSuccessful();
        }
        else{
            $this->get('event/'.$item->id)
                    ->assertStatus(404);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = $this->setupBlock();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View')
                    ->assertSee('Image Cropping')
                    ->assertSee('Sorting');

            if(\Auth::user()->role == 'admin'){
                $this->assertCount(6, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'itemRouteBase', 'successText', 'notify', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "on",
                    "cropDimensions" => "4:3",
                    "itemRouteBase" => "event",
                    "settingsScale" => "100",
                    "orderDirection" => "desc",
                    "customSizes" => "on",
                    "photoSizes[]" => ["8", "6"],
                ]);

                $block = Block::find($block->id);

                $this->assertTrue($block->parameters->cropPhoto);
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertEquals("event", $block->parameters->itemRouteBase);
                $this->assertEquals("100", $block->parameters->settingsScale);
                $this->assertEquals("desc", $block->parameters->orderDirection);
                $this->assertNull(@$block->parameters->customSizes);
                $this->assertNull(@$block->parameters->photoSizes);
            }
            else {
                $response->assertSee('Resize Images')
                    ->assertSee('Item Route');

                $this->assertCount(7, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimensions', 'customSizes', 'photoSizes[]', 'itemRouteBase', 'successText', 'notify', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "on",
                    "cropDimensions" => "4:3",
                    "itemRouteBase" => "event",
                    "settingsScale" => "100",
                    "orderDirection" => "desc",
                    "customSizes" => "on",
                    "photoSizes[]" => ["8", "6"],
                ]);

                $block = Block::find($block->id);

                $this->assertTrue($block->parameters->cropPhoto);
                $this->assertEquals("4:3", $block->parameters->cropDimensions);
                $this->assertEquals("event", $block->parameters->itemRouteBase);
                $this->assertEquals("100", $block->parameters->settingsScale);
                $this->assertEquals("desc", $block->parameters->orderDirection);
                $this->assertTrue($block->parameters->customSizes);
                $this->assertEquals(["8", "6"], $block->parameters->photoSizes);
            }
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimensions" => "4:3",
                "settingsScale" => "100",
                "orderDirection" =>	"desc"
            ]);
            
            $block = Block::find($block->id);

            $this->assertEmpty((array)$block->parameters);
        }
    }

    public function create_event_with_addon($assertion) {
        $block = $this->setupBlock(['parameters'=>json_decode('{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);
        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'strings', 'name'=>$name = $this->faker->word, 'title'=> $title = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("-4 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("-2 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg'),
            $name."_".$addon->id => $string = $this->faker->word
        ]);

        if($assertion){
            $response->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }

        $eventRoute = \Just\Models\Route::where('route', 'event/{id}')->first();
        $this->assertNotNull($eventRoute);

        $item = Block\Events::all()->last();

        if($assertion){
            $this->assertNotNull($item);

            $this->assertEquals($string, $item->pastEvents()->first()->{$name});
        }
        else{
            $this->assertNull($item);
        }
    }

    public function get_events_from_the_current_category() {
        $block = $this->setupBlock(['parameters'=>'{"cropPhoto":"1","cropDimensions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}']);

        $addon = factory(Block\Addon::class)->create(['block_id'=>$block->id, 'type'=>'categories', 'name'=>$name = $this->faker->word]);
        $addonItem = $addon->addon();
        $addonTable = (new $addonItem)->getTable();

        $this->createPivotTable($block->model()->getTable(), $addonTable);

        $firstCategory = Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' => $this->faker->word,
            'value' => $this->faker->word
        ]);

        $secondCategory = Block\Addon\Categories::create([
            'addon_id' => $addon->id,
            'name' => $categoryName = $this->faker->word,
            'value' => $categoryValue = $this->faker->word
        ]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject1 = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("-4 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("-2 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg'),
            $name."_".$addon->id => $firstCategory->id
        ]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject2 = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("-4 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("-2 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg'),
            $name."_".$addon->id => $secondCategory->id
        ]);

        $this->get("/?category=".$firstCategory->value)
            ->assertSee($subject1)
            ->assertDontSee($subject2);

        $this->get("/?category=".$secondCategory->value)
            ->assertSee($subject2)
            ->assertDontSee($subject1);


    }

    public function create_item_with_standard_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("+2 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("+4 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Block\Events::all()->last();

        $this->assertEventBlockCreatedSuccessfully($item, $subject, $summary, $text, $location);

        $this->assertFileExists(public_path('storage/events/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileExists(public_path('storage/events/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"photoSizes":["6","3"],"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("+2 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("+4 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Block\Events::all()->last();

        $this->assertEventBlockCreatedSuccessfully($item, $subject, $summary, $text, $location);

        $this->assertFileExists(public_path('storage/events/'.$item->image.'.png'));
        foreach ([6, 3] as $size) {
            $this->assertFileExists(public_path('storage/events/' . $item->image . '_'.$size.'.png'));
        }
        foreach ([12, 9, 8, 4] as $size) {
            $this->assertFileNotExists(public_path('storageeventss/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function create_item_with_empty_custom_image_sizes() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"customSizes":1,"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}')]);

        $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("+2 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("+4 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES)),
            'summary' => $summary = $this->faker->text,
            'text' => $text = $this->faker->text,
            'image' => UploadedFile::fake()->image('photo.jpg')
        ]);

        $item = Block\Events::all()->last();

        $this->assertEventBlockCreatedSuccessfully($item, $subject, $summary, $text, $location);

        $this->assertFileExists(public_path('storage/events/'.$item->image.'.png'));
        foreach ([12, 9, 8, 6, 4, 3] as $size) {
            $this->assertFileNotExists(public_path('storage/events/' . $item->image . '_'.$size.'.png'));
        }
    }

    public function register_on_event() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('{"success":true}');

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->get("/event/".str_slug($subject))
            ->assertSuccessful();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name,
            'email' => $userEmail = $this->faker->email,
            'comment' => $userComment = $this->faker->sentence,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHas('successMessageFromEvents'.$block->id);
    }

    public function register_on_event_without_comment() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('{"success":true}');

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->get("/event/".str_slug($subject))
            ->assertSuccessful();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name,
            'email' => $userEmail = $this->faker->email,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHas('successMessageFromEvents'.$block->id);
    }

    public function cannot_register_on_event_without_name() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('{"success":true}');

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->get("/event/".str_slug($subject))
            ->assertSuccessful();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'email' => $userEmail = $this->faker->email,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHasErrors('name', 'messages', 'errorsFromEvents'.$block->id);
    }

    public function cannot_register_on_event_without_email() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('{"success":true}');

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web', 'auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->get("/event/".str_slug($subject))
            ->assertSuccessful();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHasErrors('email', 'messages', 'errorsFromEvents'.$block->id);
    }

    public function cannot_register_on_event_without_captcha() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web', 'auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->get("/event/".str_slug($subject))
            ->assertSuccessful();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name
        ])
            ->assertSessionHasErrors('g-recaptcha-response', 'messages', 'errorsFromEvents'.$block->id);
    }

    public function cannot_register_on_event_twice() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->twice()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->twice()
            ->andReturn('{"success":true}');

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->get("/event/".str_slug($subject))
            ->assertSuccessful();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name,
            'email' => $userEmail = $this->faker->email,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHas('successMessageFromEvents'.$block->id);

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name,
            'email' => $userEmail,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHasErrors('email', 'messages', 'errorsFromEvents'.$block->id);
    }

    public function admin_is_notified_about_new_registration_on_event() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"notify":"1","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('{"success":true}');

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->get("/event/".str_slug($subject))
            ->assertSuccessful();

        $note = Notification::fake();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name,
            'email' => $userEmail = $this->faker->email,
            'comment' => $userComment = $this->faker->sentence,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHas('successMessageFromEvents'.$block->id);

        $note->assertSentTo(User::where('role', 'admin')->first(), NewRegistration::class);
    }

    private function assertEventBlockCreatedSuccessfully($item, $subject, $summary, $text, $location, $startDateModify = "+2 hours", $endDateModify = "+4 hours") {
        $this->assertEquals($subject, $item->subject);
        $this->assertEquals($summary, $item->summary);
        $this->assertEquals($text, $item->text);
        $this->assertEquals($location, $item->location);
        $this->assertEquals(Carbon::now()->modify($startDateModify)->format("Y-m-d H:i:00"), $item->start_date);
        $this->assertEquals(Carbon::now()->modify($endDateModify)->format("Y-m-d H:i:00"), $item->end_date);
    }

    public function user_can_see_list_of_registered_users() {
        $block = $this->setupBlock(['parameters'=>json_decode('{"notify":"1","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc","successText":"'.($successMessage = $this->faker->sentence).'"}')]);

        $client = \Mockery::mock(\GuzzleHttp\Client::class);
        \Just\Validators\Recaptcha::setClient($client);

        $response = \Mockery::mock(\GuzzleHttp\Psr7\Response::class);

        $client->shouldReceive('post')
            ->once()
            ->andReturn($response);

        $response->shouldReceive('getBody')
            ->once()
            ->andReturn('{"success":true}');

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", htmlspecialchars($this->faker->address, ENT_QUOTES));
        $image = uniqid();

        $event = new Block\Events();
        $event->block_id = $block->id;
        $event->subject = $subject;
        $event->slug = str_slug($subject);
        $event->summary = $summary;
        $event->start_date = Carbon::now()->modify("+2 hours")->format("Y-m-d H:i");
        $event->end_date = Carbon::now()->modify("+4 hours")->format("Y-m-d H:i");
        $event->location = $address;
        $event->text = $text;
        $event->image = $image;

        $event->save();

        $this->app['router']->get('event/{id}', "\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        $this->app['router']->post('register-event', "\Just\Controllers\JustController@post")->middleware(['web']);

        $item = Block\Events::all()->last();

        $this->post('/register-event', [
            'block_id' => $block->id,
            'event_id' => $item->id,
            'name' => $userName = $this->faker->name,
            'email' => $userEmail = $this->faker->email,
            'comment' => $userComment = $this->faker->sentence,
            'g-recaptcha-response' => true
        ])
            ->assertSessionHas('successMessageFromEvents'.$block->id);

        $this->get("admin/settings/".$block->id."/".$item->id)
            ->assertSee($userName)
            ->assertSee($userEmail);
    }
}
