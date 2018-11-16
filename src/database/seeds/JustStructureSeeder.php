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
            'type' => 'float',
            'width' => 1170, // 1920 for fully responsive design
        ]);
        
        $justSpecificLayout = Structure\Layout::create([
            'name' => 'Just',
            'class' => 'specific',
            'type' => 'float',
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
            'table' => ''
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
        
        // Icons
        $faIconSet = Models\IconSet::create([
            'title' => 'Font Awesome',
            'tag' => 'i',
            'class' => 'fa'
        ]);
        
        Models\Icon::insert(
            array(
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-adjust'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-adn'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-align-center'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-align-justify'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-align-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-align-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ambulance'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-anchor'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-android'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angellist'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-double-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-double-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-double-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-double-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-angle-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-apple'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-archive'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-area-chart'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-o-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-o-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-o-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-o-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-circle-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrow-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrows'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrows-alt'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrows-h'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-arrows-v'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-asterisk'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-at'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-automobile'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-backward'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ban'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bank'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bar-chart'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bar-chart-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-barcode'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bars'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-beer'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-behance'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-behance-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bell'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bell-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bell-slash'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bell-slash-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bicycle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-binoculars'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-birthday-cake'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bitbucket'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bitbucket-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bitcoin'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bold'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bolt'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bomb'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-book'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bookmark'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bookmark-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-briefcase'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-btc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bug'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-building'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-building-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bullhorn'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bullseye'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-bus'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cab'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-calculator'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-calendar'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-calendar-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-camera'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-camera-retro'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-car'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-square-o-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-square-o-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-square-o-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-square-o-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-caret-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cc-amex'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cc-discover'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cc-mastercard'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cc-paypal'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cc-stripe'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cc-visa'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-certificate'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chain'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chain-broken'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-check'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-check-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-check-circle-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-check-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-check-square-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-circle-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-circle-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-circle-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-circle-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-chevron-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-child'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-circle-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-circle-o-notch'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-circle-thin'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-clipboard'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-clock-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-close'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cloud'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cloud-download'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cloud-upload'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cny'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-code'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-code-fork'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-codepen'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-coffee'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cog'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cogs'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-columns'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-comment'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-comment-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-comments'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-comments-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-compass'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-compress'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-copy'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-copyright'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-credit-card'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-crop'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-crosshairs'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-css3'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cube'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cubes'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cut'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-cutlery'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-dashboard'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-database'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-dedent'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-delicious'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-desktop'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-deviantart'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-digg'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-dollar'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-dot-circle-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-download'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-dribbble'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-dropbox'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-drupal'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-edit'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-eject'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ellipsis-h'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ellipsis-v'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-empire'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-envelope'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-envelope-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-envelope-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-eraser'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-eur'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-euro'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-exchange'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-exclamation'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-exclamation-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-exclamation-triangle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-expand'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-external-link'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-external-link-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-eye'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-eye-slash'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-eyedropper'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-facebook'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-facebook-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-fast-backward'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-fast-forward'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-fax'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-female'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-fighter-jet'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-archive-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-audio-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-code-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-excel-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-image-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-movie-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-pdf-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-photo-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-picture-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-powerpoint-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-sound-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-text'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-text-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-video-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-word-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-file-zip-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-files-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-film'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-filter'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-fire'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-fire-extinguisher'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-flag'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-flag-checkered'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-flag-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-flash'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-flask'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-flickr'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-floppy-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-folder'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-folder-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-folder-open'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-folder-open-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-font'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-forward'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-foursquare'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-frown-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-futbol-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-gamepad'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-gavel'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-gbp'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ge'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-gear'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-gears'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-gift'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-git'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-git-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-github'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-github-alt'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-github-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-gittip'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-glass'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-globe'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-google'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-google-plus'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-google-plus-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-google-wallet'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-graduation-cap'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-group'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-h-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-hacker-news'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-hand-o-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-hand-o-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-hand-o-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-hand-o-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-hdd-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-header'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-headphones'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-heart'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-heart-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-history'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-home'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-hospital-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-html5'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ils'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-image'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-inbox'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-indent'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-info'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-info-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-inr'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-instagram'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-institution'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ioxhost'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-italic'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-joomla'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-jpy'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-jsfiddle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-key'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-keyboard-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-krw'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-language'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-laptop'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-lastfm'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-lastfm-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-leaf'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-legal'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-lemon-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-level-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-level-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-life-bouy'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-life-buoy'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-life-ring'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-life-saver'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-lightbulb-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-line-chart'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-link'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-linkedin'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-linkedin-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-linux'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-list'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-list-alt'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-list-ol'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-list-ul'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-location-arrow'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-lock'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-long-arrow-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-long-arrow-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-long-arrow-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-long-arrow-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-magic'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-magnet'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-mail-forward'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-mail-reply'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-mail-reply-all'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-male'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-map-marker'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-maxcdn'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-meanpath'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-medkit'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-meh-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-microphone'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-microphone-slash'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-minus'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-minus-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-minus-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-minus-square-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-mobile'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-mobile-phone'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-money'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-moon-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-mortar-board'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-music'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-navicon'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-newspaper-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-openid'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-outdent'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pagelines'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paint-brush'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paper-plane'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paper-plane-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paperclip'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paragraph'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paste'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pause'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paw'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-paypal'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pencil'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pencil-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pencil-square-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-phone'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-phone-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-photo'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-picture-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pie-chart'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pied-piper'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pied-piper-alt'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pinterest'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-pinterest-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-plane'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-play'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-play-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-play-circle-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-plug'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-plus'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-plus-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-plus-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-plus-square-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-power-off'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-print'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-puzzle-piece'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-qq'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-qrcode'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-question'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-question-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-quote-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-quote-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ra'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-random'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rebel'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-recycle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-reddit'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-reddit-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-refresh'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-remove'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-renren'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-reorder'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-repeat'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-reply'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-reply-all'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-retweet'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rmb'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-road'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rocket'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rotate-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rotate-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rouble'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rss'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rss-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rub'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ruble'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-rupee'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-save'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-scissors'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-search'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-search-minus'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-search-plus'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-send'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-send-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-share'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-share-alt'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-share-alt-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-share-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-share-square-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-shekel'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sheqel'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-shield'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-shopping-cart'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sign-in'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sign-out'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-signal'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sitemap'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-skype'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-slack'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sliders'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-slideshare'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-smile-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-soccer-ball-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-alpha-asc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-alpha-desc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-amount-asc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-amount-desc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-asc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-desc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-numeric-asc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-numeric-desc'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sort-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-soundcloud'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-space-shuttle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-spinner'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-spoon'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-spotify'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-square-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-stack-exchange'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-stack-overflow'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-star'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-star-half'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-star-half-empty'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-star-half-full'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-star-half-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-star-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-steam'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-steam-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-step-backward'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-step-forward'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-stethoscope'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-stop'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-strikethrough'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-stumbleupon'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-stumbleupon-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-subscript'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-suitcase'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-sun-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-superscript'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-support'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-table'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tablet'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tachometer'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tag'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tags'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tasks'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-taxi'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tencent-weibo'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-terminal'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-text-height'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-text-width'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-th'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-th-large'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-th-list'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-thumb-tack'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-thumbs-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-thumbs-o-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-thumbs-o-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-thumbs-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-ticket'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-times'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-times-circle'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-times-circle-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tint'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-toggle-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-toggle-left'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-toggle-off'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-toggle-on'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-toggle-right'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-toggle-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-trash'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-trash-o'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tree'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-trello'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-trophy'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-truck'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-try'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tty'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tumblr'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-tumblr-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-turkish-lira'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-twitch'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-twitter'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-twitter-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-umbrella'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-underline'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-undo'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-university'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-unlink'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-unlock'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-unlock-alt'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-unsorted'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-upload'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-usd'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-user'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-user-md'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-users'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-video-camera'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-vimeo-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-vine'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-vk'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-volume-down'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-volume-off'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-volume-up'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-warning'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-wechat'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-weibo'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-weixin'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-wheelchair'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-wifi'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-windows'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-won'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-wordpress'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-wrench'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-xing'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-xing-square'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-yahoo'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-yelp'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-yen'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-youtube'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-youtube-play'),
                array('icon_set_id' => $faIconSet->id, 'class' => 'fa-youtube-square')
            )
        );
        
        // Theme
        $justTheme = Models\IconSet::create([
            'title' => 'Font Awesome',
            'tag' => 'i',
            'class' => 'fa'
        ]);
    }
}
