<?php

namespace Lubart\Just\Database\Seeds;

use Illuminate\Database\Seeder;
use Lubart\Just\Structure;
use Lubart\Just\Models;
use Illuminate\Support\Facades\DB;

class JustStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Theme
        Models\Theme::create([
            'name' => 'Just',
            'isActive' => 1
        ]);
        
        // Layout
        $justPrimaryLayout = Structure\Layout::create([
            'name' => 'Just',
            'class' => 'primary',
            'width' => 1170, // 1920 for fully responsive design
        ]);
        
        $justSpecificLayout = Structure\Layout::create([
            'name' => 'Just',
            'class' => 'specific',
            'width' => 1170, // 1920 for fully responsive design
        ]);
        
        // Panel Locations
        DB::table('panelLocations')->insert([
            'location' => 'subheader'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'header'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'pageTitle'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'leftPanel'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'content'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'rightPanel'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'advertisement'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'footer'
        ]);
        
        DB::table('panelLocations')->insert([
            'location' => 'subfooter'
        ]);
        
        // Panels
        $justPrimaryHeader = Structure\Panel::create([
            'location' => 'header',
            'layout_id' => $justPrimaryLayout->id,
            'type' => 'static',
            'orderNo' => 1
        ]);
        
        $justPrimaryContent = Structure\Panel::create([
            'location' => 'content',
            'layout_id' => $justPrimaryLayout->id,
            'type' => 'dynamic',
            'orderNo' => 2
        ]);
        
        $justPrimaryFooter = Structure\Panel::create([
            'location' => 'footer',
            'layout_id' => $justPrimaryLayout->id,
            'type' => 'static',
            'orderNo' => 3
        ]);
        
        $justSpecificHeader = Structure\Panel::create([
            'location' => 'header',
            'layout_id' => $justSpecificLayout->id,
            'type' => 'static',
            'orderNo' => 1
        ]);
        
        $justSpecificContent = Structure\Panel::create([
            'location' => 'content',
            'layout_id' => $justSpecificLayout->id,
            'type' => 'dynamic',
            'orderNo' => 2
        ]);
        
        $justSpecificFooter = Structure\Panel::create([
            'location' => 'footer',
            'layout_id' => $justSpecificLayout->id,
            'type' => 'static',
            'orderNo' => 3
        ]);
        
        // Blocks
        DB::table('blockList')->insert([
            'block' => 'text',
            'title' => "Text",
            'description' => "Adds well-formatetd text with the caption to the website",
            'table' => 'texts'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'logo',
            'title' => "Logo",
            'description' => "Website logos",
            'table' => 'logos'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'menu',
            'title' => "Menu",
            'description' => "Menu biulder",
            'table' => 'menus'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'langs',
            'title' => "Languages",
            'description' => "Makes availabel localizations",
            'table' => 'locations'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'slider',
            'title' => "Slider",
            'description' => "Adds image slider with or without text descriptions",
            'table' => 'slides'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'articles',
            'title' => "Articles",
            'description' => "Blog or newsline with set of articles",
            'table' => 'articles'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'twitter',
            'title' => "Twitter",
            'description' => "Adds Twitter block to the website",
            'table' => 'twitters'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'gallery',
            'title' => "Photo Gallery",
            'description' => "Shows photo gallery on the website",
            'table' => 'photos'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'feedback',
            'title' => "Feedback",
            'description' => "Adds feedback form to the website",
            'table' => 'feedbacks'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'contact',
            'title' => "Contact",
            'description' => "Shows contact information",
            'table' => 'contacts'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'link',
            'title' => "Link",
            'description' => "Shows data from other block",
            'table' => ''
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'features',
            'title' => "Feature",
            'description' => "Adds short feature description with icon",
            'table' => 'features'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'space',
            'title' => "Empty",
            'description' => "Adds empty space with fixed height",
            'table' => ''
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'html',
            'title' => "HTML",
            'description' => "Adds HTML piece of code",
            'table' => 'texts'
        ]);

        DB::table('blockList')->insert([
            'block' => 'events',
            'title' => "Events",
            'description' => "Adds event block to the page",
            'table' => 'events'
        ]);
        
        // Addons
        DB::table('addonList')->insert([
            'addon' => 'categories',
            'title' => 'Categories',
            'description' => 'Helps categorize module',
            'table' => 'categories'
        ]);
        
        DB::table('addonList')->insert([
            'addon' => 'strings',
            'title' => 'String Value',
            'description' => 'Add a string to the item',
            'table' => 'strings'
        ]);
        
        DB::table('addonList')->insert([
            'addon' => 'images',
            'title' => 'Related Image',
            'description' => 'Add an image to the item',
            'table' => 'images'
        ]);
        
        DB::table('addonList')->insert([
            'addon' => 'paragraphs',
            'title' => 'Related Text',
            'description' => 'Add an article to the item',
            'table' => 'paragraphs'
        ]);
        
        // Routes
        $homeRoute = Models\Route::create([
            'route' => ''
        ]);
        
        //Pages
        $homePage = Structure\Page::create([
            'title' => 'Home',
            'description' => 'Home page',
            'route' => $homeRoute->route,
            'layout_id' => $justPrimaryLayout->id
        ]);
    }
}
