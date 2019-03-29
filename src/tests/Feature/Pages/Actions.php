<?php

namespace Lubart\Just\Tests\Feature\Just\Pages;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Page;
use Lubart\Just\Models\Route;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(Route::all() as $route){
            if($route->route != ""){
                $route->delete();
            }
        }
        
        foreach(Page::all() as $page){
            if($page->id != 1){
                $page->delete();
            }
        }
        
        parent::tearDown();
    }
    
    public function setup_current_page($assertion){
        $route = Route::create([
            'route' => $this->faker->word
        ]);
        $page = Page::create([
            'route' => $route->route,
            'layout_id' => 1
        ]);
        
        $response = $this->get('/admin/settings/page/'.$page->id);
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee("Settings :: Page");
        }
        else{
            $response->assertStatus(302);
        }
        
        $this->post("/admin/settings/page/setup", [
            "page_id" => $page->id,
            "title" => $title = $this->faker->word,
            "description" => $description = $this->faker->paragraph,
            "keywords" => $keywords = $this->faker->word,
            "author" => $author = $this->faker->name,
            "copyright" => $copyright = $this->faker->sentence,
            "route" => $newRoute = $this->faker->word,
            "layout_id" => 1
        ]);
        
        $newPage = Page::find($page->id);
        
        if($assertion){
            $this->assertEquals($title, $newPage->title);
            $this->assertEquals($description, $newPage->description);
            $this->assertEquals($keywords, $newPage->keywords);
            $this->assertEquals($author, $newPage->author);
            $this->assertEquals($copyright, $newPage->copyright);
            $this->assertEquals($newRoute, $newPage->route);
        }
        else{
            $this->assertNotEquals($title, $newPage->title);
            $this->assertNotEquals($description, $newPage->description);
            $this->assertNotEquals($keywords, $newPage->keywords);
            $this->assertNotEquals($author, $newPage->author);
            $this->assertNotEquals($copyright, $newPage->copyright);
            $this->assertEquals($route->route, $newPage->route);
        }
    }
    
    public function create_new_page($assertion){
        $response = $this->get('admin/settings/page/0');
        if($assertion){
            $response->assertStatus(200);
        }
        else{
            $response->assertStatus(302);
        }
        
        $this->post("/admin/settings/page/setup", [
            "page_id" => null,
            "title" => $title = $this->faker->word,
            "description" => $description = $this->faker->paragraph,
            "keywords" => $keywords = $this->faker->word,
            "author" => $author = $this->faker->name,
            "copyright" => $copyright = $this->faker->sentence,
            "route" => $newRoute = $this->faker->word,
            "layout_id" => 1
        ]);
        
        $newPage = Page::where('id', '>', 1)->get()->last();
        
        $route = Route::where('route', $newRoute)->first();
        
        if($assertion){
            $this->assertEquals($title, $newPage->title);
            $this->assertEquals($description, $newPage->description);
            $this->assertEquals($keywords, $newPage->keywords);
            $this->assertEquals($author, $newPage->author);
            $this->assertEquals($copyright, $newPage->copyright);
            $this->assertEquals($newRoute, $newPage->route);
            
            $this->assertNotNull($route);
        }
        else{
            $this->assertNull($newPage);
            
            $this->assertNull($route);
        }
    }
    
    public function apply_meta_to_all_pages($assertion){
        $route1 = Route::create([
            'route' => $this->faker->word
        ]);
        $route2 = Route::create([
            'route' => $this->faker->word
        ]);
        $page1 = Page::create([
            'route' => $route1->route,
            'layout_id' => 1
        ]);
        $page2 = Page::create([
            'route' => $route2->route,
            'layout_id' => 1
        ]);
        $homePage = Page::find(1);
        
        $response = $this->get('/admin/settings/page/'.$page1->id);
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee("Settings :: Page");
        }
        else{
            $response->assertStatus(302);
        }
        
        $this->post("/admin/settings/page/setup", [
            "page_id" => $page1->id,
            "title" => $page1->title,
            "description" => $page1->description,
            "keywords" => $keywords = $this->faker->word,
            "author" => $author = $this->faker->name,
            "copyright" => $copyright = $this->faker->sentence,
            "copyMeta" => 'on',
            "route" => $page1->route,
            "layout_id" => 1
        ]);
        
        $page1 = Page::find($page1->id);
        $page2 = Page::find($page2->id);
        
        if($assertion){
            $this->assertEquals($keywords, $page1->keywords);
            $this->assertEquals($author, $page1->author);
            $this->assertEquals($copyright, $page1->copyright);
            
            $this->assertEquals($keywords, $page2->keywords);
            $this->assertEquals($author, $page2->author);
            $this->assertEquals($copyright, $page2->copyright);
            
            $this->post("/admin/settings/page/setup", [
            "page_id" => $homePage->id,
            "title" => $homePage->title,
            "description" => $homePage->description,
            "keywords" => $homePage->keywords,
            "author" => $homePage->author,
            "copyright" => $homePage->copyright,
            "route" => '',
            "layout_id" => 1
        ]);
        }
        else{
            $this->assertNotEquals($keywords, $page2->keywords);
            $this->assertNotEquals($author, $page2->author);
            $this->assertNotEquals($copyright, $page2->copyright);
        }
    }
    
    public function access_page_list($assertion){
        $response = $this->get('admin/settings/page/list');
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: Pages");
        }
        else{
            $response->assertRedirect();
        }
    }
    
    public function edit_specific_page($assertion){
        $route1 = Route::create([
            'route' => $this->faker->word
        ]);
        $page1 = Page::create([
            'title' => $title1 = $this->faker->word,
            'route' => $route1->route,
            'layout_id' => 1
        ]);
        
        $route2 = Route::create([
            'route' => $this->faker->word
        ]);
        $page2 = Page::create([
            'title' => $title2 = $this->faker->word,
            'route' => $route2->route,
            'layout_id' => 1
        ]);
        
        $response = $this->get('admin/settings/page/list');
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: Pages")
                    ->assertSee($title1)
                    ->assertSee($title2);
        }
        else{
            $response->assertRedirect();
        }
        
        $response = $this->get('admin/settings/page/'.$page1->id);
        
        if($assertion){
            $response->assertSuccessful();
        }
        else{
            $response->assertRedirect();
        }
        
        $this->post("/admin/settings/page/setup", [
            "page_id" => $page1->id,
            "title" => $title = $this->faker->word,
            "description" => $description = $this->faker->paragraph,
            "keywords" => $keywords = $this->faker->word,
            "author" => $author = $this->faker->name,
            "copyright" => $copyright = $this->faker->sentence,
            "route" => $route1->route,
            "layout_id" => 1
        ]);
        
        $updatedPage = Page::find($page1->id);
        
        if($assertion){
            $this->assertEquals($title, $updatedPage->title);
            $this->assertEquals($description, $updatedPage->description);
            $this->assertEquals($keywords, $updatedPage->keywords);
            $this->assertEquals($author, $updatedPage->author);
            $this->assertEquals($copyright, $updatedPage->copyright);
        }
        else{
            $this->assertNotEquals($title, $updatedPage->title);
            $this->assertNotEquals($description, $updatedPage->description);
            $this->assertNotEquals($keywords, $updatedPage->keywords);
            $this->assertNotEquals($author, $updatedPage->author);
            $this->assertNotEquals($copyright, $updatedPage->copyright);
        }
    }
    
    public function delete_specific_page($assertion){
        $route = Route::create([
            'route' => $this->faker->word
        ]);
        $page = Page::create([
            'title' => $this->faker->word,
            'route' => $route->route,
            'layout_id' => 1
        ]);
        
        $this->post('admin/page/delete', [
            'id' => $page->id
        ]);
        
        $deletedPage = Page::find($page->id);
        $route = Route::findByUrl($route->route);
        
        if($assertion){
            $this->assertNull($deletedPage);
            $this->assertNull($route);
        }
        else{
            $this->assertNotNull($deletedPage);
            $this->assertNotNull($route);
        }
    }
}
