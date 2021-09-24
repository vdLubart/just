<?php

namespace Just\Tests\Feature\AddOnOption;

use Illuminate\Support\Facades\Auth;
use Just\Models\AddOn;
use Just\Models\Blocks\AddOns\AddOnOption;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Block;
use Just\Tests\Feature\Helper;

class Actions extends TestCase{

    use WithFaker;
    use Helper;

    protected function tearDown(): void {
        foreach(Block::all() as $block){
            $block->delete();
        }

        if(file_exists(public_path('storage/texts'))){
            exec('rm -rf ' . public_path('storage/texts'));
        }

        parent::tearDown();
    }

    public function access_create_addon_option_form($assertion) {
        AddOn::factory()
            ->type('category')
            ->for(Block::factory())
            ->create();

        $response = $this->get('settings/add-on-option/category/option/0');

        if($assertion){
            $response->assertSuccessful();
            $addonOption = new AddOnOption();

            $form = $addonOption->itemForm();
            $this->assertEquals(4, $form->count());
            $this->assertEquals(['id', 'add_on_id', 'option', 'submit'], array_keys($form->elements()));
        }
        else{
            $response->assertRedirect('login');
        }
    }

    public function access_actions_settings_page($assertion) {
        $response = $this->get('settings/add-on-option');

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(3, count(json_decode(json_decode($response->content())->content, true)));
        }
        else{
            $response->assertRedirect('login');
        }
    }

    public function access_actions_settings_page_for_category($assertion) {
        AddOn::factory()
            ->type('category')
            ->for(Block::factory())
            ->create();

        $response = $this->get('settings/add-on-option/category/list');

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(1, count(json_decode(json_decode($response->content())->content, true)));
        }
        else{
            $response->assertRedirect('login');
        }
    }

    public function access_actions_settings_page_for_tag($assertion) {
        AddOn::factory()
            ->type('tag')
            ->for(Block::factory())
            ->create();

        $response = $this->get('settings/add-on-option/tag/list');

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(1, count(json_decode(json_decode($response->content())->content, true)));
        }
        else{
            $response->assertRedirect('login');
        }
    }

    public function access_addon_option_list($assertion) {
        $addon = AddOn::factory()
            ->has(AddOnOption::factory()->count(3), 'options')
            ->for(Block::factory())
            ->create();

        $response = $this->get('settings/add-on-option/' . $addon->id);

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(3, count(json_decode(json_decode($response->content())->content, true)));
        }
        else{
            $response->assertRedirect('login');
        }
    }

    public function activate_addon_option($assertion) {
        $addon = AddOn::factory()
                    ->has(AddOnOption::factory()->deactivate(), 'options')
                    ->for(Block::factory())
                    ->create();
        $option = $addon->options->first();

        $response = $this->post('settings/add-on-option/category/option/activate',
            [
                'id' => $option->id
            ]
        );

        $option = AddOnOption::find($option->id);

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(1, $option->isActive);
        }
        else{
            if(Auth::check()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('login');
            }

            $this->assertEquals(0, $option->isActive);
        }
    }

    public function deactivate_addon_option($assertion) {
        $addon = AddOn::factory()
            ->has(AddOnOption::factory(), 'options')
            ->for(Block::factory())
            ->create();
        $option = $addon->options->first();

        $response = $this->post('settings/add-on-option/category/option/deactivate',
            [
                'id' => $option->id
            ]
        );

        $option = AddOnOption::find($option->id);

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(0, $option->isActive);
        }
        else{
            if(Auth::check()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('login');
            }

            $this->assertEquals(1, $option->isActive);
        }
    }

    public function move_addon_option_up($assertion) {
        $addon = AddOn::factory()
            ->has(AddOnOption::factory()->count(2), 'options')
            ->for(Block::factory())
            ->create();

        $option1 = $addon->options->first();
        $option2 = $addon->options->last();
        $option2->update(['orderNo' => 2]);

        $response = $this->post('settings/add-on-option/category/option/moveup',
            [
                'id' => $option2->id
            ]
        );

        $option1 = AddOnOption::find($option1->id);
        $option2 = AddOnOption::find($option2->id);

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(1, $option2->orderNo);
            $this->assertEquals(2, $option1->orderNo);
        }
        else{
            if(Auth::check()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('login');
            }

            $this->assertEquals(1, $option1->orderNo);
            $this->assertEquals(2, $option2->orderNo);
        }
    }

    public function move_addon_option_down($assertion) {
        $addon = AddOn::factory()
            ->has(AddOnOption::factory()->count(2), 'options')
            ->for(Block::factory())
            ->create();

        $option1 = $addon->options->first();
        $option2 = $addon->options->last();
        $option2->update(['orderNo' => 2]);

        $response = $this->post('settings/add-on-option/category/option/movedown',
            [
                'id' => $option1->id
            ]
        );

        $option1 = AddOnOption::find($option1->id);
        $option2 = AddOnOption::find($option2->id);

        if($assertion){
            $response->assertSuccessful();

            $this->assertEquals(2, $option1->orderNo);
            $this->assertEquals(1, $option2->orderNo);
        }
        else{
            if(Auth::check()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('login');
            }

            $this->assertEquals(1, $option1->orderNo);
            $this->assertEquals(2, $option2->orderNo);
        }
    }

    public function delete_addon_option($assertion) {
        $addon = AddOn::factory()
            ->has(AddOnOption::factory(), 'options')
            ->for(Block::factory())
            ->create();
        $option = $addon->options->first();

        $response = $this->post('settings/add-on-option/category/option/delete',
            [
                'id' => $option->id
            ]
        );

        $option = AddOnOption::find($option->id);

        if($assertion){
            $response->assertSuccessful();

            $this->assertNull($option);
        }
        else{
            if(Auth::check()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('login');
            }

            $this->assertNotNull($option);
        }
    }

    public function delete_addon_option_when_it_used_in_block($assertion) {
        $addon = AddOn::factory()
            ->type('category')
            ->has(AddOnOption::factory(), 'options')
            ->for(Block::factory())
            ->create();
        $option = $addon->options->first();
        $block = $addon->block;

        $this->createPivotTable($block, $addon);

        if(!$assertion){
            $this->actingAsAdmin();
        }

        $this->post('settings/block/item/save', [
            'block_id' => $block->id,
            'id' => null,
            'text' => '{"en":"'.( $this->faker->paragraph).'"}',
            $addon->name.'_'.$addon->id => $option->id
        ])
            ->assertSuccessful();

        if(!$assertion){
            Auth::logout();
        }
        $block = Block::find($block->id);

        $response = $this->post('settings/add-on-option/category/option/delete',
            [
                'id' => $option->id
            ]
        );

        $option = AddOnOption::find($option->id);

        if($assertion){
            $response->assertSuccessful();

            $this->assertNull($option);

            $this->assertNull($block->items->first()->content()->first()->{$addon->name});
        }
        else{
            if(Auth::check()){
                $response->assertRedirect('settings/noaccess');
            }
            else{
                $response->assertRedirect('login');
            }

            $this->assertNotNull($option);
            $this->assertNotNull($block->items->first()->content()->first()->{$addon->name});
        }

        $this->removePivotTable($block, $addon);
    }
}
