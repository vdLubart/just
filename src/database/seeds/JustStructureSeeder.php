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
            'table' => 'texts'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'logo',
            'table' => 'logos'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'menu',
            'table' => 'menus'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'langs',
            'table' => 'locations'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'slider',
            'table' => 'slides'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'articles',
            'table' => 'articles'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'twitter',
            'table' => 'twitters'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'gallery',
            'table' => 'photos'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'feedback',
            'table' => 'feedbacks'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'contact',
            'table' => 'contacts'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'link',
            'table' => ''
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'features',
            'table' => 'features'
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'space',
            'table' => ''
        ]);
        
        DB::table('blockList')->insert([
            'block' => 'html',
            'table' => 'texts'
        ]);

        DB::table('blockList')->insert([
            'block' => 'events',
            'table' => 'events'
        ]);
        
        // Addons
        DB::table('addonList')->insert([
            'addon' => 'categories',
            'table' => 'categories'
        ]);
        
        DB::table('addonList')->insert([
            'addon' => 'strings',
            'table' => 'strings'
        ]);
        
        DB::table('addonList')->insert([
            'addon' => 'images',
            'table' => 'images'
        ]);
        
        DB::table('addonList')->insert([
            'addon' => 'paragraphs',
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
