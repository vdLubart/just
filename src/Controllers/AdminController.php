<?php

namespace Just\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Just\Models\Block;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Just\Requests\UploadImageRequest;

class AdminController extends Controller
{
    public function __construct() {
        parent::__construct();

        Config::set('isAdmin', true);
    }

    public function uploadImage(UploadImageRequest $request): JsonResponse {
        $image = Image::make($request->file('image'));
        $pieces = explode(".", $request->image->getClientOriginalName());
        array_pop($pieces);
        $basename = implode('.', $pieces);
        if(file_exists(public_path('images/library/'.$basename.'.png'))){
            $basename = $basename."_".$image->basename;
        }
        $url = '/images/library/'.$basename.".png";

        if($image->getWidth() > 1000){
            $image->resize(1000, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        if($image->getHeight() > 700){
            $image->resize(null, 700, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $image->encode('png')->save(public_path($url));

        return response()->json(["url"=>$url]);
    }

    /**
     * [POST] Create block related to the model
     *
     * @param Request $request
     * @return RedirectResponse
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
