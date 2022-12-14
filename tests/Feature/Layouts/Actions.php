<?php

namespace Just\Tests\Feature\Layouts;

use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Page;
use Just\Models\System\Route;
use Just\Models\Layout;
use Just\Models\Theme;
use Just\Models\Panel;

class Actions extends TestCase{

    use WithFaker;

    protected function tearDown(): void{
        foreach(Route::all() as $route){
            if($route->route != ""){
                $route->delete();
            }
        }

        foreach(Page::all() as $page){
            if($page->id != 1){
                $page->delete();
            }
            else{
                $page->layout_id = 1;
                $page->save();
            }
        }

        foreach (Layout::all() as $layout){
            if($layout->id > 2){
                $layout->delete();
            }
        }

        foreach (Theme::all() as $theme){
            if($theme->name != "Just"){
                $theme->where('name', $theme->name)->delete();
            }
            else{
                $theme->update(['isActive'=>1]);
            }
        }

        parent::tearDown();
    }

    public function access_actions_page($assertion) {
        $response = $this->get('settings/layout');

        if($assertion) {
            $response->assertSuccessful();
            $this->assertEquals(3, count(json_decode(json_decode($response->content())->content, true)));
        }
        else{
            $response->assertRedirect();
        }
    }

    public function cannot_change_default_layout(){
        $this->get('/settings/layout/1');

        $response = $this->post("settings/layout/setup", [
            'layout_id' => 1,
            'width'=> $width = $this->faker->numberBetween(980, 1920),
        ]);

        if(!Auth::id()){
            $response->assertRedirect('/login');
        }
        elseif(Auth::user()->role == 'master'){
            $response->assertSessionHasErrors('layout_id');
        }
        else{
            $response->assertRedirect('settings/noaccess');
        }

        $layout = Layout::find(1);
        $this->assertNotEquals($width, $layout->width);
    }

    public function create_new_layout($assertion){
        $newTheme = Theme::create([
            'name' => $this->faker->word
        ]);

        $response = $this->get('/settings/layout/0');

        if($assertion){
            $response->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }

        $response = $this->post("/settings/layout/setup", [
            "layout_id" => null,
            "name" => $newTheme->name,
            "class" => $class = "primary",
            "width" => $width = $this->faker->numberBetween(980, 1920),
            "panel_1" => "header",
            "panelType_1" => "static",
            "panel_2" => "content",
            "panelType_2" => "dynamic"
        ]);

        $layout = Layout::where('name', $newTheme->name)->first();

        if($assertion){
            $this->assertNotNull($layout);

            $this->assertEquals($width, $layout->width);

            $this->assertFileExists(resource_path('views/'.$newTheme->name.'/panels/content.blade.php'));
            $this->assertFileExists(resource_path('views/'.$newTheme->name.'/panels/header.blade.php'));

            if(file_exists(resource_path('views/'.$newTheme->name))){
                exec('rm -rf ' . resource_path('views/'.$newTheme->name));
            }
        }
        else{
            if(Auth::id()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('/login');
            }
        }
    }

    public function cannot_create_layout_with_existing_class(){
        $this->get('/settings/layout/0');

        $response = $this->post("/settings/layout/setup", [
            "layout_id" => null,
            "name" => "Just",
            "class" => "specific",
            "width" => $width = $this->faker->numberBetween(980, 1920),
            "panel_1" => "content",
            "panelType_1" => "dynamic"
        ]);

        if(!\Auth::id()){
            $response->assertRedirect('/login');
        }
        elseif(\Auth::user()->role == 'master'){
            $response->assertSessionHasErrors('class');
        }
        else{
            $response->assertRedirect('settings/noaccess');
        }
    }

    public function choose_default_layout($assertion){
        $this->addWarning('Create default layout functionality is not implemented');
        /*
        $newTheme = Theme::create([
            'name' => $this->faker->word
        ]);

        $newLayout = Layout::create([
            "name" => $newTheme->name,
            "class" => "primary",
            "width" => $width = $this->faker->numberBetween(980, 1920),
        ]);

        $route = Route::create([
            'route' => $this->faker->word
        ]);
        $page = Page::create([
            'route' => $route->route,
            'layout_id' => 1
        ]);

        $response = $this->post("/settings/layout/setdefault", [
            "layout" => $newLayout->name,
            "change_all" => "on"
        ]);

        $page = Page::find($page->id);

        if($assertion){
            $this->assertEquals($newLayout->id, $page->layout_id);

            $this->get('/settings/layout/default')
                    ->assertSuccessful();

            $this->get('/settings/layout/0')
                    ->assertSee("value=\"".$newTheme->name."\" selected=\"selected\"");
        }
        else{
            if(\Auth::id()){
                $response->assertRedirect('settings/noaccess');

                $this->get('/settings/layout/default')
                    ->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('/login');

                $this->get('/settings/layout/default')
                    ->assertRedirect('login');
            }
        }
        */
    }

    public function access_layout_list($assertion){
        $response = $this->get('/settings/layout/list');

        if($assertion){
            $response->assertSuccessful();
        }
        else{
            if(\Auth::id()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('/login');
            }
        }
    }

    public function delete_layout($assertion){
        $newTheme = Theme::create([
            'name' => $this->faker->word
        ]);

        $layout = Layout::create([
            "name" => $newTheme->name,
            "class" => $class = "primary",
            "width" => $this->faker->numberBetween(980, 1920),
        ]);

        $panel = Panel::create([
            "location" => "content",
            "layout_id" => $layout->id,
            "type" => "dynamic"
        ]);

        $this->post('settings/layout/delete', [
            'layout_id' => $layout->id
        ]);

        $layout = Layout::find($layout->id);
        $panel = Panel::find($panel->id);

        if($assertion){
            $this->assertNull($layout);
            $this->assertNull($panel);
        }
        else{
            $this->assertNotNull($layout);
            $this->assertNotNull($panel);
        }
    }

    public function cannot_delete_layout_if_it_is_in_use() {
        $newTheme = Theme::create([
            'name' => $this->faker->word
        ]);

        $layout = Layout::create([
            "name" => $newTheme->name,
            "class" => $class = "primary",
            "width" => $this->faker->numberBetween(980, 1920),
        ]);

        Panel::create([
            "location" => "content",
            "layout_id" => $layout->id,
            "type" => "dynamic"
        ]);

        $route = Route::create([
            'route' => $this->faker->word,
            'type' => 'page',
            'block_id' => null,
            'action' => null
        ]);

        $page = Page::create([
            'title' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'keywords' => $this->faker->word,
            'author' => $this->faker->name,
            'copyright' => "",
            'route' => $route->route,
            'layout_id' => $layout->id,
            'isActive' => 1
        ]);

        $this->post('settings/layout/delete', [
            'layout_id' => $layout->id
        ])
            ->assertSuccessful()
            ->assertJsonFragment(['error'=>"Layout cannot be deleted because page \"".$page->title."\" is using it"]);

    }

    public function get_layout_from_the_panel() {
        $panel = Panel::where('location', 'content')->first();
        $layout = Layout::find($panel->layout_id);

        $this->assertEquals($layout->name, $panel->layout->name);
        $this->assertEquals($layout->class, $panel->layout->class);
    }
}
