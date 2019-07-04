<?php

namespace Lubart\Just\Tests\Feature\Blocks\Events;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lubart\Just\Models\Route;
use Lubart\Just\Tests\Feature\Helper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Http\UploadedFile;

class Actions extends TestCase{
    
    use WithFaker;
    use Helper;
    
    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        if(file_exists(public_path('storage/articles'))){
            exec('rm -rf ' . public_path('storage/articles'));
        }
        
        parent::tearDown();
    }
    
    public function access_item_form_without_initial_data($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->assertDontSee('input name="subject"');
        
        $response->{($assertion?'assertDontSee':'assertSee')}('Item route base');
        $response->{($assertion?'assertDontSee':'assertSee')}('Settings View Scale');
        
        $this->post('admin/settings/setup', [
            "id" => $block->id,
            "cropPhoto" =>	"1",
            "cropDimentions" => "4:3",
            "itemRouteBase" => "event",
            "settingsScale" => "100",
            "orderDirection" =>	"desc"
        ])
                ->assertStatus(200);
        
        $block = Block::find($block->id);
        $this->{($assertion ? 'assertJsonStringNotEqualsJsonString' : 'assertJsonStringEqualsJsonString')}('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
        
        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}(100, $block->parameter('settingsScale'));
        $this->{($assertion ? 'assertNotEquals' : 'assertEquals')}("event", $block->parameter('itemRouteBase'));
        
    }
    
    public function access_item_form_when_block_is_setted_up($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events', 'parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="image"');
        $response->{($assertion ? 'assertSee' : 'assertDontSee')}('input name="subject"');
    }

    public function access_edit_item_form($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events'])->specify();
        
        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", $this->faker->address);
        $image = uniqid();
        
        Block\Events::insert([
            'block_id' => $block->id,
            'subject' => $subject,
            'summary' => $summary,
            'start_date' => Carbon::now()->modify("+2 hours")->format("Y-m-d H:i"),
            'end_date' => Carbon::now()->modify("+4 hours")->format("Y-m-d H:i"),
            'location' => $address,
            'text' => $text,
            'image' => $image
        ]);
        
        $item = Block\Events::all()->last();
        
        if($assertion){
            $form = $item->form();
            $this->assertEquals(11, $form->count());
            $this->assertEquals(['topGroup', 'startDate', 'endDate'], array_keys($form->groups()));
            $this->assertEquals(['image', 'imagePreview_'.$item->id, 'subject'], array_keys($form->group('topGroup')->getElements()));
            $this->assertEquals(['location', 'summary', 'text', 'submit'], array_keys($form->getElements()));
            $this->assertEquals($text, $form->getElement('text')->value());

            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimentions" => "4:3",
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events', 'parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}'])->specify();

        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null,
            'subject' => $subject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("+2 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("+4 hours")->format("H:i"),
            'location' => $location = str_replace("\n", "", $this->faker->address),
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

        $eventRoute = \Lubart\Just\Models\Route::where('route', 'event/{id}')->first();
        $this->assertNotNull($eventRoute);
        
        $item = Block\Events::all()->last();

        if($assertion){
            $this->assertNotNull($item);
            
            $this->assertEquals($block->id, $block->firstItem()->block_id);
            $this->assertEquals($subject, $block->firstItem()->subject);
            $this->assertEquals($summary, $block->firstItem()->summary);
            $this->assertEquals($text, $block->firstItem()->text);
            $this->assertEquals(Carbon::now()->modify("+2 hours")->format("Y-m-d H:i:00"), $block->firstItem()->start_date);
            $this->assertEquals(Carbon::now()->modify("+4 hours")->format("Y-m-d H:i:00"), $block->firstItem()->end_date);
            $this->assertEquals($location, $block->firstItem()->location);
            
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events', 'parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}'])->specify();
        
        $this->get("admin/settings/".$block->id."/0");
        
        $response = $this->post("", [
            'block_id' => $block->id,
            'id' => null
        ]);
        
        $item = Block\Articles::all()->last();
        
        $response->assertRedirect();
        
        $this->assertNull($item);
        
        if($assertion){
            $this->followRedirects($response)
                    ->assertSee("The subject field is required");
        }
        else{
            $this->followRedirects($response)
                    ->assertDontSee("The subject field is required");
        }
    }

    public function edit_existing_item_in_the_block($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events', 'parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}'])->specify();

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", $this->faker->address);
        $image = uniqid();
        
        Block\Events::insert([
            'block_id' => $block->id,
            'subject' => $subject,
            'summary' => $summary,
            'start_date' => Carbon::now()->modify("+2 hours")->format("Y-m-d H:i"),
            'end_date' => Carbon::now()->modify("+4 hours")->format("Y-m-d H:i"),
            'location' => $address,
            'text' => $text,
            'image' => $image
        ]);
        
        $item = Block\Events::all()->last();
        
        $this->post("", [
            'block_id' => $block->id,
            'id' => $item->id,
            'subject' => $newSubject = $this->faker->sentence,
            'start_date' => Carbon::today()->format("Y-m-d"),
            'start_time' => Carbon::now()->modify("+4 hours")->format("H:i"),
            'end_date' => Carbon::today()->format("Y-m-d"),
            'end_time' => Carbon::now()->modify("+6 hours")->format("H:i"),
            'location' => $newLocation = str_replace("\n", "", $this->faker->address),
            'summary' => $newSummary = $this->faker->text,
            'text' => $newText = $this->faker->text,
        ]);
        
        $item = Block\Events::all()->last();
        
        if($assertion){
            $this->assertEquals($newSubject, $item->subject);
            $this->assertEquals($newSummary, $item->summary);
            $this->assertEquals($newText, $item->text);
            $this->assertEquals($newLocation, $item->location);
            $this->assertEquals(Carbon::now()->modify("+4 hours")->format("Y-m-d H:i:00"), $item->start_date);
            $this->assertEquals(Carbon::now()->modify("+6 hours")->format("Y-m-d H:i:00"), $item->end_date);
        }
        else{
            $this->assertNotEquals($newText, $item->text);
        }
    }
    
    public function access_created_item($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events', 'parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}'])->specify();

        $subject = $this->faker->sentence;
        $summary = $this->faker->text;
        $text = $this->faker->text;
        $address = str_replace("\n", "", $this->faker->address);
        $image = uniqid();

        Block\Events::insert([
            'block_id' => $block->id,
            'subject' => $subject,
            'summary' => $summary,
            'start_date' => Carbon::now()->modify("+2 hours")->format("Y-m-d H:i"),
            'end_date' => Carbon::now()->modify("+4 hours")->format("Y-m-d H:i"),
            'location' => $address,
            'text' => $text,
            'image' => $image
        ]);
        
        $this->app['router']->get('event/{id}', "\Lubart\Just\Controllers\JustController@buildPage")->middleware('web');
        $this->app['router']->get('admin/event/{id}', "\Lubart\Just\Controllers\AdminController@buildPage")->middleware(['web','auth']);
        
        $item = Block\Events::all()->last();

        if($assertion){
            if(\Auth::id()){
                $this->get('admin/event/'.$item->id)
                        ->assertStatus(200)
                        ->assertSee($subject)
                        ->assertDontSee($summary)
                        ->assertSee($text);
            }
            else{
                $this->get('admin/event/'.$item->id)
                        ->assertStatus(302);
            }

            $this->get('event/'.$item->id)
                    ->assertStatus(200)
                    ->assertSee($subject)
                    ->assertDontSee($summary)
                    ->assertSee($text);
        }
        else{
            $this->get('event/'.$item->id)
                    ->assertStatus(404);
        }
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View')
                    ->assertSee('Image cropping')
                    ->assertSee('Ordering Direction');

            if(\Auth::user()->role == 'admin'){
                $this->assertCount(6, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimentions', 'itemRouteBase', 'successText', 'notify', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "1",
                    "cropDimentions" => "4:3",
                    "itemRouteBase" => "event",
                    "settingsScale" => "100",
                    "orderDirection" => "desc"
                ]);

                $block = Block::find($block->id);

                $this->assertEquals('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
            }
            else {
                $response->assertSee('Resize images')
                    ->assertSee('Item route');

                $this->assertCount(7, $block->setupForm()->groups());

                $this->assertEquals(['id', 'cropPhoto', 'cropDimentions', 'customSizes', 'photoSizes[]', 'itemRouteBase', 'successText', 'notify', 'settingsScale', 'orderDirection', 'submit'], $block->setupForm()->names());

                $this->post('admin/settings/setup', [
                    "id" => $block->id,
                    "cropPhoto" => "1",
                    "cropDimentions" => "4:3",
                    "itemRouteBase" => "event",
                    "settingsScale" => "100",
                    "orderDirection" => "desc"
                ]);

                $block = Block::find($block->id);

                $this->assertEquals('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
            }
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "cropPhoto" =>	"1",
                "cropDimentions" => "4:3",
                "settingsScale" => "100",
                "orderDirection" =>	"desc"
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}', json_encode($block->parameters()));
        }
    }

    public function create_event_with_addon($assertion) {
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events', 'parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}'])->specify();
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
            'location' => $location = str_replace("\n", "", $this->faker->address),
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

        $eventRoute = \Lubart\Just\Models\Route::where('route', 'event/{id}')->first();
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
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'events', 'parameters'=>'{"cropPhoto":"1","cropDimentions":"4:3","itemRouteBase":"event","settingsScale":"100","orderDirection":"desc"}'])->specify();

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
            'location' => $location = str_replace("\n", "", $this->faker->address),
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
            'location' => $location = str_replace("\n", "", $this->faker->address),
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
}
