<?php

namespace Lubart\Just\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Structure\Layout;
use Lubart\Just\Structure\Panel;
use Lubart\Just\Models\Theme;
use Lubart\Just\Structure\Panel\Block;
use Illuminate\Support\Facades\DB;

/**
 * Description of ViewPath
 *
 * @author lubart
 */
class ViewPathTest extends TestCase {
    
    use WithFaker;
    
    /** @test */
    public function exception_thrown_if_view_resource_not_found(){
        $layout = Layout::where('name', 'Just')->where('class', 'primary')->first();
        
        $exception = viewPath($layout, "unknown");
        
        $this->assertEquals("Exception", get_class($exception));
        $this->assertEquals('Resource "Just.unknown" does not exists.', $exception->getMessage());
    }
    
    /** @test */
    public function get_view_for_panel_in_non_primary_layout(){
        $layout = Layout::where('name', 'Just')->where('class', '<>', 'primary')->first();
        
        $panel = Panel::where('layout_id', $layout->id)->first();
        
        $panelViewFile = resource_path('views/'.$layout->name.'/panels/'.$panel->location.'_'.$layout->class.'.blade.php');
        touch($panelViewFile);
        
        $this->assertEquals('Just.panels.'.$panel->location.'_'.$layout->class, viewPath($layout, $panel));
        
        unlink($panelViewFile);
    }
    
    /** @test */
    public function get_view_for_panel_from_primary_if_specific_file_not_exists(){
        $layout = Layout::where('name', 'Just')->where('class', '<>', 'primary')->first();
        
        $panel = Panel::where('layout_id', $layout->id)->first();
        
        $this->assertEquals('Just.panels.'.$panel->location, viewPath($layout, $panel));
    }
    
    /** @test */
    public function get_view_for_panel_from_Just_theme_if_other_theme_file_not_exists(){
        $newTheme = Theme::create([
            'name' => $this->faker->word
        ]);
        
        $layout = Layout::create([
            "name" => $newTheme->name,
            "class" => $class = "primary",
            "width" => $this->faker->numberBetween(980, 1920),
            "type" => "float"
        ]);
        
        $panel = Panel::create([
            "location" => "content",
            "layout_id" => $layout->id,
            "type" => "dynamic"
        ]);
        
        $this->assertEquals('Just.panels.content', viewPath($layout, $panel));
        
        $panel->delete();
        $layout->delete();
        $newTheme->delete();
    }
    
    /** @test */
    public function exception_thrown_if_panel_view_resource_not_found(){
        $layout = Layout::where('name', 'Just')->where('class', 'primary')->first();
        
        $panel = Panel::create([
            "location" => "pageTitle", // this file does not exists
            "layout_id" => $layout->id,
            "type" => "static"
        ]);
        
        $exception = viewPath($layout, $panel);
        
        $this->assertEquals("Exception", get_class($exception));
        $this->assertEquals('Resource "Just.panels.pageTitle" does not exists.', $exception->getMessage());
        
        $panel->delete();
    }
    
    /** @test */
    public function get_view_for_block_in_non_primary_layout(){
        $layout = Layout::where('name', 'Just')->where('class', '<>', 'primary')->first();
        
        $block = factory(Block::class)->create()->specify();
        
        $blockViewFile = resource_path('views/'.$layout->name.'/blocks/'.$block->type.'_'.$layout->class.'.blade.php');
        touch($blockViewFile);
        
        $this->assertEquals('Just.blocks.'.$block->type.'_'.$layout->class, viewPath($layout, $block));
        
        unlink($blockViewFile);
        
        $block->delete();
    }
    
    /** @test */
    public function get_view_for_block_with_non_primary_class(){
        $layout = Layout::where('name', 'Just')->where('class', 'primary')->first();
        
        $block = factory(Block::class)->create(['layoutClass'=>'custom'])->specify();
        
        $blockViewFile = resource_path('views/'.$layout->name.'/blocks/'.$block->type.'_custom.blade.php');
        touch($blockViewFile);
        
        $this->assertEquals('Just.blocks.'.$block->type.'_custom', viewPath($layout, $block));
        
        unlink($blockViewFile);
        
        $block->delete();
    }
    
    /** @test */
    public function get_view_for_block_from_primary_if_specific_file_not_exists(){
        $layout = Layout::where('name', 'Just')->where('class', '<>', 'primary')->first();
        
        $block = factory(Block::class)->create()->specify();
        
        $this->assertEquals('Just.blocks.'.$block->type, viewPath($layout, $block));
        
        $block->delete();
    }
    
    /** @test */
    public function get_view_for_block_from_Just_theme_if_other_theme_file_not_exists(){
        $newTheme = Theme::create([
            'name' => $this->faker->word
        ]);
        
        $layout = Layout::create([
            "name" => $newTheme->name,
            "class" => $class = "primary",
            "width" => $this->faker->numberBetween(980, 1920),
            "type" => "float"
        ]);
        
        $block = factory(Block::class)->create()->specify();
        
        $this->assertEquals('Just.blocks.text', viewPath($layout, $block));
        
        $block->delete();
        $layout->delete();
        $newTheme->delete();
    }
    
    /** @test */
    public function exception_thrown_if_block_view_resource_not_found(){
        $layout = Layout::where('name', 'Just')->where('class', 'primary')->first();
        
        DB::table('blockList')->insert([
           'block' => 'custom'
        ]);
        $block = factory(Block::class)->create(['type'=>'custom']);
        
        $exception = viewPath($layout, $block);
        
        $this->assertEquals("Exception", get_class($exception));
        $this->assertEquals('Resource "Just.blocks.'.$block->type.'" does not exists.', $exception->getMessage());
        
        $block->delete();
        DB::table('blockList')->where('block', 'custom')->delete();
    }
    
    /** @test */
    public function get_current_version(){
        $this->assertEquals("9999999-dev", justVersion());
    }
}
