<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 12.07.19
 * Time: 16:58
 */

namespace Lubart\Just\Tests\Feature\Blocks;


use Illuminate\Support\Facades\DB;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Just\Structure\Panel\Block\Text;
use Tests\TestCase;

class BlockLocation extends TestCase {

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

        Text::insert([
            'block_id' => $block->id,
            'text' => $this->faker->paragraph,
        ]);

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

            DB::table('texts_blocks')->insert([
                'modelItem_id' => $item->id,
                'block_id' => $relatedBlock->id
            ]);

            return $relatedBlock;
        }
    }
}