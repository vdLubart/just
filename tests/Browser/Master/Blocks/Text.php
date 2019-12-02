<?php

namespace Tests\Browser\Blocks;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\User;
use Lubart\Just\Structure\Panel\Block;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\WithFaker;

class Text extends DuskTestCase
{
    use WithFaker;
    
    public function tearDown():void {
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        parent::tearDown();
    }
    
    private function typeInCKEditor ($selector, $browser, $text)
    {
        $ckIframe = $browser->elements($selector)[0];
        $browser->driver->switchTo()->frame($ckIframe);
        $body = $browser->driver->findElement(WebDriverBy::xpath('//body'));
        $body->sendKeys($text);
        $browser->driver->switchTo()->defaultContent();
    }


    public function master_can_create_text_block()
    {
        $this->masterUser = factory(User::class)->create(['role'=>'master']);
        
        $blockTitle = $this->faker->title;
        $blockDescr = $this->faker->paragraph;
        
        $this->browse(function (Browser $browser) use ($blockTitle, $blockDescr){
            $browser->resize(1920, 1080)
                    ->loginAs($this->masterUser)
                    ->visit('/admin')
                    ->assertSee('Just!')
                    ->assertSee($this->masterUser->name . ':master')
                    ->click("@content-panel-settings")
                    ->waitFor("div#settings")
                    ->assertSee("Settings :: Panel")
                    ->clickLink("Add new block")
                    ->select('name', 'Text')
                    ->type('title', $blockTitle);
            
            $this->typeInCKEditor('#cke_blockDescription iframe', $browser, $blockDescr);
                    
            $browser->select('width', '100%')
                    ->press('Save')
                    ->assertSee($blockTitle)
                    ;
        });
        
        $this->masterUser->delete();
    }
}
