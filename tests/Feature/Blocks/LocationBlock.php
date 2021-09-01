<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 12.07.19
 * Time: 16:58
 */

namespace Just\Tests\Feature\Blocks;


use Illuminate\Support\Facades\DB;
use Just\Models\Block;
use Just\Models\Blocks\Text;
use Tests\TestCase;

class LocationBlock extends TestCase {

    protected $blockParams = [];

    public function inContent() {
        $this->blockParams = ['panelLocation'=>'content', 'page_id'=>1, 'type'=>$this->type];

        return $this;
    }

    public function inHeader() {
        $this->blockParams = ['panelLocation'=>'header', 'page_id'=>null, 'type'=>$this->type];

        return $this;
    }

    public function relatedBlock() {
        $block = factory(Block::class)->create(['panelLocation'=>'content', 'page_id'=>1])->specify();

        $textBlock = new Text();
        $textBlock->block_id = $block->id;
        $textBlock->text = $this->faker->paragraph;

        $textBlock->save();

        $this->blockParams = ['panelLocation'=>null, 'page_id'=>null, 'type'=>$this->type, 'parent'=>$block->id];

        return $this;
    }

    protected function setupBlock($blockAttrib = []) {
        if(!is_null($this->blockParams['panelLocation'])) {
            return factory(Block::class)->create($blockAttrib + $this->blockParams)->specify();
        }
        else{
            $relatedBlock = factory(Block::class)->create($blockAttrib + $this->blockParams)->specify();

            $item = Text::all()->last();

            Block::createPivotTable($item->getTable());

            DB::table('texts_blocks')->insert([
                'modelItem_id' => $item->id,
                'block_id' => $relatedBlock->id
            ]);

            return $relatedBlock;
        }
    }
}
