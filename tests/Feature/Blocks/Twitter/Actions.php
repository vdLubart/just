<?php

namespace Lubart\Just\Tests\Feature\Blocks\Twitter;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Panel\Block;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(Block::all() as $block){
            $block->delete();
        }
        
        parent::tearDown();
    }
    
    public function access_item_form($assertion){
        $account = $this->faker->word;
        $id = $this->faker->numberBetween();
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'twitter', 'parameters'=>'{"account":"'.$account.'","widgetId":"'.$id.'"}'])->specify();
        
        $response = $this->get("admin/settings/".$block->id."/0");
        
        $response->{($assertion?'assertSee':'assertDontSee')}('form');
        
        $twitter = new Block\Twitter();
        
        if($assertion){
            $form = $twitter->form();
            $this->assertEquals([], array_keys($form->getElements()));
            
            $this->get('admin')
                    ->assertSee('<div id="twitter_'.$block->id.'">')
                    ->assertSee('@'.$account);
            
            $this->get('')
                    ->assertSee('<div id="twitter_'.$block->id.'">')
                    ->assertSee('@'.$account);
        }
        else{
            $this->assertNull($twitter->form());
            
            $this->get('admin')
                    ->assertRedirect('/login');
            
            $this->get('')
                    ->assertSee('<div id="twitter_'.$block->id.'">')
                    ->assertSee('@'.$account);
        }
        
        $this->assertNull($twitter->content());
    }
    
    public function edit_block_settings($assertion){
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1, 'type'=>'twitter'])->specify();
        
        $response = $this->get('admin/settings/'.$block->id.'/0');
        
        if($assertion){
            $response->assertStatus(200)
                    ->assertSee('Settings View');
            
            $this->assertCount(3, $block->setupForm()->groups());
            
            $this->assertEquals(['id', 'settingsScale', 'account', 'widgetId', 'submit'], $block->setupForm()->names());
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100",
                'account' => 'Account',
                'widgetId' => 123
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertEquals('{"settingsScale":"100","account":"Account","widgetId":123}', $block->parameters);
        }
        else{
            $response->assertStatus(302);
            
            $this->post('admin/settings/setup', [
                "id" => $block->id,
                "settingsScale" => "100",
                'account' => 'Account',
                'widgetId' => 123
            ]);
            
            $block = Block::find($block->id);
            
            $this->assertNotEquals('{"settingsScale":"100","account":"Account","widgetId":123}', $block->parameters);
        }
    }
}