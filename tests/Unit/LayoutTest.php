<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Just\Models\Layout;
use Just\Models\Page;
use Tests\TestCase;

class LayoutTest extends TestCase {

    use WithFaker;

    /** @test */
    public function get_primary_layout_view_if_layout_file_with_specific_class_does_not_exists(){
        $layout = Layout::create([
            "name" => "Just",
            "class" => $class = $this->faker->word,
            "width" => $this->faker->numberBetween(980, 1920)
        ]);

        Page::find(1)->update(['layout_id' => $layout->id]);

        $response = $this->get('')
            ->assertSuccessful();

        $this->assertEquals('Just.layouts.primary', $response->getOriginalContent()->getName());

        Page::find(1)->update(['layout_id' => 1]);

        $layout->delete();
    }

}
