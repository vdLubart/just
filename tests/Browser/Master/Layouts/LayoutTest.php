<?php

namespace Lubart\Just\Tests\Browser\Master\Layouts;

use Laravel\Dusk\Browser;
use App\User;
use Lubart\Just\Models\Layout;
use Lubart\Just\Structure\Panel\Block;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Tests\Browser\Master\MasterTests;

class LayoutTest extends MasterTests
{
    use WithFaker;

    /** @test - it accesses layout menu */
    function it_accesses_layout_menu() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1200, 700)
                ->visit('/admin')
                ->assertSee('Layouts')
                ->clickLink('Layouts')
                ->assertSee('Create Layout')
                ->assertSee('Layout List')
                ->assertSee('Layout Settings')
            ;
        });
    }

    /** @test - it access layout list */
    function it_access_layout_list() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1200, 700)
                ->visit('/admin')
                ->clickLink('Layouts')
                ->clickLink('Layout List')
                ->assertSee('Loading data')
                ->waitUntilMissing('.loading')
                ->assertSee('Settings :: Layouts :: Layout List')
                ->assertSeeLink('Just.primary')
                ->assertSeeLink('Just.specific')
            ;
        });
    }

    /** @test - it creates new layout */
    function it_creates_new_layout() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1200, 700)
                ->visit('/admin')
                ->clickLink('Layouts')
                ->clickLink('Create Layout')
                ->waitUntilMissing('.loading')
                ->type("class", $class = $this->faker->word)
                ->select('panel_1', 'header')
                ->clickLink("Add Panel")
                ->select('panel_2', 'content')
                ->select('panelType_2', 'dynamic')
                ->clickLink('Add Panel')
                ->select('panel_3', 'footer')
                ->press('Save')
                ->waitUntilMissing('#settings')
                ->clickLink('Layouts')
                ->clickLink('Layout List')
                ->waitUntilMissing('.loading')
                ->assertSeeLink('Just.'.$class)
            ;
        });

        Layout::where('id', '>', 2)->delete();
    }

    /** @test - it cannot change default layout settings */
    function it_cannot_change_default_layout_settings() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1200, 700)
                ->visit('/admin')
                ->clickLink('Layouts')
                ->clickLink('Layout Settings')
                ->waitUntilMissing('.loading')
                ->type('width', 1200)
                ->press('Save')
                ->waitFor('.alert')
                ->assertSee('This layout is default and cannot be changed')
            ;
        });
    }

    /** @test - it changes specific class of the Just layout */
    function it_changes_specific_class_of_the_just_layout() {
        $this->browse(function (Browser $browser) {
            $browser->resize(1200, 700)
                ->visit('/admin')
                ->clickLink('Layouts')
                ->clickLink('Layout List')
                ->waitUntilMissing('.loading')
                ->clickLink('Just.specific')
                ->pause(5000)
                ->screenshot('layout')
            ;
        });
    }
}
