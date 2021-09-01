<?php

namespace Just\Database\Seeds;

use Illuminate\Database\Seeder;
use Just\Models;
use Illuminate\Support\Facades\DB;
use Just\Models\Layout;
use Just\Models\Page;
use Just\Models\System\Route;

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
        $justPrimaryLayout = Layout::create([
            'name' => 'Just',
            'class' => 'primary',
            'width' => 1170, // 1920 for fully responsive design
        ]);

        $justSpecificLayout = Layout::create([
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
        $justPrimaryHeader = Models\Panel::create([
            'location' => 'header',
            'layout_id' => $justPrimaryLayout->id,
            'type' => 'static',
            'orderNo' => 1
        ]);

        $justPrimaryContent = Models\Panel::create([
            'location' => 'content',
            'layout_id' => $justPrimaryLayout->id,
            'type' => 'dynamic',
            'orderNo' => 2
        ]);

        $justPrimaryFooter = Models\Panel::create([
            'location' => 'footer',
            'layout_id' => $justPrimaryLayout->id,
            'type' => 'static',
            'orderNo' => 3
        ]);

        $justSpecificHeader = Models\Panel::create([
            'location' => 'header',
            'layout_id' => $justSpecificLayout->id,
            'type' => 'static',
            'orderNo' => 1
        ]);

        $justSpecificContent = Models\Panel::create([
            'location' => 'content',
            'layout_id' => $justSpecificLayout->id,
            'type' => 'dynamic',
            'orderNo' => 2
        ]);

        $justSpecificFooter = Models\Panel::create([
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
            'table' => 'links'
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
            'addon' => 'category',
            'table' => 'categories'
        ]);

        DB::table('addonList')->insert([
            'addon' => 'tag',
            'table' => 'tags'
        ]);

        DB::table('addonList')->insert([
            'addon' => 'phrase',
            'table' => 'phrases'
        ]);

        DB::table('addonList')->insert([
            'addon' => 'image',
            'table' => 'images'
        ]);

        DB::table('addonList')->insert([
            'addon' => 'paragraph',
            'table' => 'paragraphs'
        ]);

        // Routes
        $homeRoute = Route::create([
            'route' => ''
        ]);

        //Pages
        $homePage = Page::create([
            'title' => 'Home',
            'description' => 'Home page',
            'author' => '{}',
            'copyright' => '{}',
            'route' => $homeRoute->route,
            'layout_id' => $justPrimaryLayout->id
        ]);
    }
}
