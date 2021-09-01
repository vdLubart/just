<?php

namespace Just\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Just\Models\Block;
use Just\Structure\Panel;
use Just\Tools\Useful;
use Just\Models\Page;
use Just\Models\AddOn;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\DB;
use Just\Requests\UploadImageRequest;
use Just\Models\User;
use Just\Requests\SavePasswordRequest;
use Just\Validators\ValidatorExtended;
use Just\Models\Blocks\AddOns\Category;
use Just\Models\Theme;
use Just\Requests\SaveCategoryRequest;
use Just\Requests\SaveAddonRequest;
use Just\Requests\SaveUserRequest;
use Just\Requests\DeleteUserRequest;
use Just\Requests\SaveBlockRequest;

class AdminController extends Controller
{
    public function __construct() {
        parent::__construct();

        Config::set('isAdmin', true);
    }

    public function settingsForm($blockId, $id) {
        return view(viewPath(Theme::active()->layout, 'settings'))->with($this->detectBlock($blockId, $id));
    }

    protected function detectBlock($blockId, $itemId){
        $block = Block::findModel($blockId, $itemId);
        $parentBlock = Block::find($block->parent);
        if(!empty($parentBlock)){
            $pivot = DB::table($parentBlock->details()->table."_blocks")->where('block_id', $blockId)->first();
            $parentBlock = $parentBlock->specify($pivot->modelItem_id);
        }

        if(!is_null($block->panelLocation)){
            $panel = Panel::where('location', $block->panelLocation)->first();
        }
        else{
            $panel = Panel::where('location', $parentBlock->panelLocation)->first();
        }

        if(!empty($panel) and $panel->type == 'dynamic'){
            if(!is_null($block->page_id)){
                $panel->setPage(Page::find($block->page_id));
            }
            else{
                $panel->setPage(Page::find($parentBlock->page_id));
            }
        }

        return ['block'=>$block, 'parentBlock'=>$parentBlock, 'panel'=>$panel];
    }


    public function panelSettingsForm($pageId, $panelLocation, $blockId = null) {
        $panel = Panel::where('location', $panelLocation)->first();
        if(!empty($panel) and $panel->type == 'dynamic'){
            $panel->setPage(Page::find($pageId));
        }

        $block = is_null($blockId)? new Block : Block::find($blockId);

        if(!empty($panel) and is_null($block->panel)){
            $block = $block->setPanel($panel);
        }

        return view(viewPath(Theme::active()->layout, 'panelSettings'))->with(['panel'=>$panel, 'block'=>$block]);
    }

    public function addonList() {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }

        $addons = AddOn::all();

        return view(viewPath(Theme::active()->layout, 'addonList'))->with(['addons'=>$addons]);
    }

    public function addonSettingsForm($addonId) {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }

        $addon = AddOn::findOrNew($addonId);

        return view(viewPath(Theme::active()->layout, 'addonSettings'))->with(['addon'=>$addon]);
    }

    public function handleAddonForm(SaveAddonRequest $request) {
        $addon = AddOn::findOrNew($request->addon_id);

        $addon->handleSettingsForm($request);

        return redirect()->back();
    }

    public function userList() {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }

        $users = User::all();

        return view(viewPath(Theme::active()->layout, 'userList'))->with(['users'=>$users]);
    }

    public function userSettingsForm($userId) {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }

        $user = User::findOrNew($userId);

        return view(viewPath(Theme::active()->layout, 'userSettings'))->with(['user'=>$user]);
    }

    public function handleUserForm(SaveUserRequest $request) {
        $user = User::findOrNew($request->user_id);

        $user->handleSettingsForm($request);

        return redirect()->back();
    }

    public function categoryList() {
        $categories = Category::join("addons", "categories.addon_id", "=", "addons.id")
                ->join("blocks", "addons.block_id", "=", "blocks.id")
                ->select(['categories.*', DB::raw("addons.title->>'$.".(\App::getLocale())."' as addonTitle"), DB::raw("blocks.type as blockType"), DB::raw("blocks.title->>'$.".(\App::getLocale())."' as blockTitle")])
                ->orderBy("addons.id", "desc")
                ->get();

        return view(viewPath(Theme::active()->layout, 'categoryList'))->with(['categories'=>$categories, 'currentId' => 0]);
    }

    public function categorySettingsForm($categoryId) {
        $category = Category::findOrNew($categoryId);

        return view(viewPath(Theme::active()->layout, 'categorySettings'))->with(['category'=>$category]);
    }

    public function handleCategoryForm(SaveCategoryRequest $request) {
        $category = Category::findOrNew($request->category_id);

        $category->handleSettingsForm($request);

        return redirect()->back();
    }

    public function cropForm($blockId, $id) {
        $blockData = $this->detectBlock($blockId, $id);

        return view(viewPath(Theme::active()->layout, 'settings'))->with($blockData + ['crop'=>true, 'image'=>$blockData['block']->model()->image]);
    }

    public function handleForm(Request $request) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $model = $block->handleItemSetupForm($request);
            if($model instanceof ValidatorExtended){
                $model->validate();
                return null;
            }
        }
        else{
            $model = null;
        }

        return $model;
    }



    public function handlePanelForm(SaveBlockRequest $request) {
        $block = Block::findOrNew($request->block_id);

        $block->handlePanelForm($request);

        return redirect()->back();
    }

    public function handleCrop(Request $request) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $model = $block->handleCrop($request);
        }

        return $model;
    }

    public function handleSetup(Request $request) {
        $block = Block::find($request->id)->specify();
        $settingsElements = $block->setupForm()->names();

        $block->unsettleAddons();

        if(!empty($block)){
            $parameters = $block->parameters ?? json_decode('{}');

            foreach($settingsElements as $name){
                if(!in_array($name, ['id', '_token', 'submit', 'button'])){
                    if($block->setupForm()->element($name)->type() == 'checkbox') {
                        $parameters->{$name} = false;
                    }
                    else{
                        $parameters->{$name} = '';
                    }
                }
            }

            $values = $request->all();
            unset($values['id']);
            unset($values['_token']);
            unset($values['submit']);

            foreach($values as $key=>$value){
                if(is_string($value) and $value === (string)(int)$value){
                    $value = (int)$value;
                }

                if(in_array($key, $settingsElements) or in_array($key."[]", $settingsElements)){
                    $parameters->{trim($key, "[]")} = ($value == 'on' and $block->setupForm()->getElement($key)->type() == 'checkbox') ? true : $value;
                }
            }

            $block->parameters = $parameters;
            $block->save();
        }

        return $block;
    }

    public function delete(Request $request) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->deleteItem();

            Useful::normalizeOrder($block->item()->getTable());
        }

        return ['id'=>$block->id, 'panelLocation'=>$block->panelLocation, 'page_id'=>(is_null($block->page_id)?0:$block->page_id), 'parent'=>$block->parent];
    }

    public function deleteAddon(Request $request) {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }

        $addon = AddOn::find($request->id);

        if(!empty($addon)){
            $addon->delete();
        }

        return ;
    }

    public function deleteCategory(Request $request) {
        $category = Category::find($request->id);

        if(!empty($category)){
            $category->delete();
        }

        return ;
    }

    /**
     * Specify block
     *
     * @param Request $request
     * @return Block
     */
    private function specifyBlock(Request $request) {
        $block = Block::find($request->block_id);

        if (!empty($block)) {
            $block->specify($request->id);
        }

        return $block;
    }

    protected function move(Request $request, $dir) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->move($dir);
        }

        return ['id'=>$block->id, 'panelLocation'=>$block->panelLocation, 'page_id'=>(is_null($block->page_id)?0:$block->page_id)];
    }

    /**
     * Move item to the specific position in the block. Methos id used for
     * drag&drop action
     *
     * @param Request $request
     * @return type
     */
    public function moveto(Request $request) {

        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->moveTo($request->newPosition);
        }

        return ['id'=>$block->id, 'panelLocation'=>$block->panelLocation, 'page_id'=>(is_null($block->page_id)?0:$block->page_id)];
    }

    /**
     * Move item to one position up in the block
     *
     * @param Request $request
     * @return type
     */
    public function moveup(Request $request) {
        return $this->move($request, 'up');
    }

    /**
     * Move item to one position down in the block
     *
     * @param Request $request
     * @return type
     */
    public function movedown(Request $request) {
        return $this->move($request, 'down');
    }

    /**
     * Change item visibility
     *
     * @param Request $request
     * @param $visibility
     * @return array
     */
    protected function visibility(Request $request, $visibility): array {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block = $block->visibility($visibility);
        }

        return ['id'=>$block->id, 'panelLocation'=>$block->panelLocation, 'page_id'=>(is_null($block->page_id)?0:$block->page_id)];
    }

    /**
     * Make item visible ob the page
     *
     * @param Request $request
     * @return array
     */
    public function activate(Request $request): array {
        return $this->visibility($request, 1);
    }

    /**
     * Make item invisible on the page
     *
     * @param Request $request
     * @return array
     */
    public function deactivate(Request $request): array {
        return $this->visibility($request, 0);
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
