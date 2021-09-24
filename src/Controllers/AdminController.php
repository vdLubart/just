<?php

namespace Just\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Just\Models\Block;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __construct() {
        parent::__construct();

        Config::set('isAdmin', true);
    }

    /**
     * [POST] Create block related to the model
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function createRelation(Request $request): RedirectResponse {
        if(Auth::user()->role != "master"){
            return redirect()->back();
        }

        $parentBlock = Block::find($request->block_id);
        $model = $parentBlock->specify($request->id)->model();

        $relatedBlock = new Block();
        $relatedBlock->type = $request->relatedBlockName;
        $relatedBlock->title = $request->title ?? "";
        $relatedBlock->description = $request->description ?? "";
        $relatedBlock->orderNo = 0;
        $relatedBlock->parent = $parentBlock->id;

        $relatedBlock->save();

        Block::createPivotTable($model->getTable());

        DB::table($model->getTable()."_blocks")->insert([
            'modelItem_id' => $request->id,
            'block_id' => $relatedBlock->id
        ]);

        return redirect()->back();
    }
}
