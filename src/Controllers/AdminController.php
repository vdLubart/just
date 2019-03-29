<?php

namespace Lubart\Just\Controllers;

use Illuminate\Http\Request;
use Lubart\Just\Structure\Panel\Block;
use Lubart\Just\Structure\Panel;
use Lubart\Just\Tools\AjaxUploader;
use Lubart\Just\Tools\Useful;
use Lubart\Just\Structure\Page;
use Lubart\Just\Structure\Layout;
use Lubart\Just\Structure\Panel\Block\Addon;
use Intervention\Image\ImageManagerStatic as Image;
use Lubart\Just\Requests\ChangeLayoutRequest;
use Lubart\Just\Requests\ChangePageRequest;
use Illuminate\Support\Facades\DB;
use Lubart\Just\Requests\UploadImageRequest;
use Lubart\Just\Models\User;
use Lubart\Just\Requests\ChangePasswordRequest;
use Lubart\Just\Validators\ValidatorExtended;
use Lubart\Just\Structure\Panel\Block\Addon\Categories;
use Lubart\Just\Models\Theme;
use Lubart\Just\Requests\ChangeCategoryRequest;
use Lubart\Just\Requests\AddonChangeRequest;
use Lubart\Just\Requests\UserChangeRequest;
use Lubart\Just\Requests\DeleteUserRequest;
use Lubart\Just\Requests\DeleteLayoutRequest;

class AdminController extends Controller
{
    public function __construct() {
        parent::__construct();
        
        \Config::set('isAdmin', true);
    }
    
    public function settingsForm($blockId, $id, $subid = null) {
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
    
    public function pageSettingsForm($pageId) {
        $page = Page::findOrNew($pageId);
        
        return view(viewPath(Theme::active()->layout, 'pageSettings'))->with(['page'=>$page]);
    }
    
    public function pageList() {
        $pages = Page::all();
        
        return view(viewPath(Theme::active()->layout, 'pageList'))->with(['pages'=>$pages]);
    }
    
    public function layoutSettingsForm($layoutId) {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        $layout = Layout::findOrNew($layoutId);
        
        return view(viewPath(Theme::active()->layout, 'layoutSettings'))->with(['layout'=>$layout]);
    }
    
    public function layoutList() {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        $layouts = Layout::all();
        
        return view(viewPath(Theme::active()->layout, 'layoutList'))->with(['layouts'=>$layouts]);
    }
    
    public function addonList() {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        $addons = Addon::all();
        
        return view(viewPath(Theme::active()->layout, 'addonList'))->with(['addons'=>$addons]);
    }
    
    public function addonSettingsForm($addonId) {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        $addon = Addon::findOrNew($addonId);
        
        return view(viewPath(Theme::active()->layout, 'addonSettings'))->with(['addon'=>$addon]);
    }
    
    public function handleAddonForm(AddonChangeRequest $request) {
        $addon = Addon::findOrNew($request->addon_id);
        
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
    
    public function handleUserForm(UserChangeRequest $request) {
        $user = User::findOrNew($request->user_id);
        
        $user->handleSettingsForm($request);
        
        return redirect()->back();
    }
    
    public function categoryList() {
        $categories = Categories::join("addons", "categories.addon_id", "=", "addons.id")
                ->join("blocks", "addons.block_id", "=", "blocks.id")
                ->select(['categories.*', DB::raw("addons.title as addonTitle"), DB::raw("blocks.name as blockName"), DB::raw("blocks.title as blockTitle")])
                ->orderBy("addons.id", "desc")
                ->get();
        
        return view(viewPath(Theme::active()->layout, 'categoryList'))->with(['categories'=>$categories, 'currentId' => 0]);
    }
    
    public function categorySettingsForm($categoryId) {
        $category = Categories::findOrNew($categoryId);
        
        return view(viewPath(Theme::active()->layout, 'categorySettings'))->with(['category'=>$category]);
    }
    
    public function handleCategoryForm(ChangeCategoryRequest $request) {
        $category = Categories::findOrNew($request->category_id);
        
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
            $model = $block->handleForm($request);
            if($model instanceof ValidatorExtended){
                $model->validate();
                return;
            }
        }
        else{
            $model = null;
        }
        
        return $model;
    }
    
    public function handlePanelForm(Request $request) {
        $block = Block::findOrNew($request->block_id);
        
        $block->handlePanelForm($request);
        
        return redirect()->back();
    }
    
    public function handlePageForm(ChangePageRequest $request) {
        $page = Page::findOrNew($request->page_id);
        
        $page->handleSettingsForm($request);
        
        return redirect()->back();
    }
    
    public function handleLayoutForm(ChangeLayoutRequest $request) {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        $layout = Layout::findOrNew($request->layout_id);
        
        $layout->handleSettingsForm($request);
        
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
        
        if(!empty($block)){
            $parameters = new \stdClass;
            $values = $request->all();
            unset($values['id']);
            unset($values['_token']);
            unset($values['submit']);
            foreach($values as $key=>$value){
                if(in_array($key, $settingsElements) or in_array($key."[]", $settingsElements)){
                    $parameters->{$key} = $value;
                }
            }
            $block->parameters = json_encode($parameters);
            $block->save();
        }
        
        return $block;
    }
    
    public function delete(Request $request) {
        $block = $this->specifyBlock($request);
        
        if(!empty($block)){
            $block->deleteModel();
            
            Useful::normalizeOrder($block->model()->getTable());
        }
        
        return ['id'=>$block->id, 'panelLocation'=>$block->panelLocation, 'page_id'=>(is_null($block->page_id)?0:$block->page_id)];
    }
    
    public function deletePage(Request $request) {
        $page = Page::find($request->id);
        $route = \Lubart\Just\Models\Route::where('route', $page->route)->first();
        
        if(!empty($page)){
            $page->delete();
            $route->delete();
        }
        
        return ;
    }
    
    public function deleteAddon(Request $request) {
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        $addon = Addon::find($request->id);
        
        if(!empty($addon)){
            $addon->delete();
        }
        
        return ;
    }
    
    public function deleteUser(DeleteUserRequest $request) {
        $user = User::find($request->id);
        
        if(!empty($user) and \Auth::id() != $user->id){
            $user->delete();
        }
        
        return ;
    }
    
    public function deleteCategory(Request $request) {
        $category = Categories::find($request->id);
        
        if(!empty($category)){
            $category->delete();
        }
        
        return ;
    }
    
    public function deleteLayout(DeleteLayoutRequest $request) {
        $layout = Layout::find($request->layout_id);
        
        if(!empty($layout)){
            $pages = Page::where('layout_id', $request->layout_id)->first();
            if(!empty($pages)){
                return json_encode(['error'=>'Layout cannot be deleted because page "'.$pages->first()->title.'" is using it']);
            }
            
            $layout->delete();
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
     * @param type $visability
     * @return type
     */
    protected function visabiliy(Request $request, $visability) {
        $block = $this->specifyBlock($request);
        
        if(!empty($block)){
            $block = $block->visabiliy($visability);
        }
        
        return ['id'=>$block->id, 'panelLocation'=>$block->panelLocation, 'page_id'=>(is_null($block->page_id)?0:$block->page_id)];
    }
    
    /**
     * Make item visible ob the page
     * 
     * @param Request $request
     * @return type
     */
    public function activate(Request $request) {
        return $this->visabiliy($request, 1);
    }
    
    /**
     * Make item invisible on the page
     * 
     * @param Request $request
     * @return type
     */
    public function deactivate(Request $request) {
        return $this->visabiliy($request, 0);
    }
    
    /**
     * Upload file through AjaxUploader
     * 
     * @return type
     */
    public function ajaxuploader() {
        $uploader = new AjaxUploader;
        
        return $uploader->uploadFile();
    }
    
    /**
     * Shows list of images
     *
     * @middleware auth.marketing
     */
    public function browseImages() {
        $this->data['files'] = Useful::browseImages("images/library");
        $this->data['action'] = '/admin/uploadimage';
        
        return view(viewPath(Theme::active()->layout, 'system.ckeditor.browseimages'))->with($this->data);
    }
    
    /**
     * [POST] Uploads images to the server
     *
     * @param UploadImageRequest $request
     */
    public function uploadImage(UploadImageRequest $request) {
        $image = Image::make($request->file('image'));
        $pieces = explode(".", $request->image->name);
        array_pop($pieces);
        $basename = implode('.', $pieces);
        if(!file_exists(public_path('images/library/'.$basename.'.png'))){
            $image->encode('png')->save(public_path('images/library/'.$basename.".png"));
        }
        else{
            $image->encode('png')->save(public_path('images/library/'.$basename."_".$image->basename.".png"));
        }
        
        return redirect('admin/browseimages');
    }
    
    /**
     * [POST] Create block related to the model
     */
    public function createRelation(Request $request) {
        $parentBlock = Block::find($request->block_id);
        $model = $parentBlock->specify($request->id)->model();
        
        $relatedBlock = Block::create([
            'name' => $request->relatedBlockName,
            'title' => $request->title?$request->title:"",
            'description' => $request->description?$request->description:"",
            'orderNo' => 0,
            'parent' => $parentBlock->id
        ]);
        
        Block::createPivotTable($model->getTable());
        
        DB::table($model->getTable()."_blocks")->insert([
            'modelItem_id' => $request->id,
            'block_id' => $relatedBlock->id
        ]);
        
        return redirect()->back();
    }
    
    public function changePasswordForm() {
        return view(viewPath(Theme::active()->layout, 'changePassword'))->with(['form'=>User::changePasswordForm()]);
    }
    
    public function changePassword(ChangePasswordRequest $request) {
        $user = \Auth::user();
        
        $user->password = bcrypt($request->new_password);
        $user->save();
        
        return;
    }
    
    public function defaultLayout(){
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        return view(viewPath(Theme::active()->layout, 'defaultLayout'))->with(['form'=>Layout::setDefaultForm()]);
    }
    
    public function setDefaultLayout(Request $request){
        if(\Auth::user()->role != "master"){
            return view(viewPath(Theme::active()->layout, 'noAccess'));
        }
        
        $validator = \Validator::make($request->all(),
                    [
                        'layout' => "required|string",
                        'change_all' => "nullable"
                    ]);
        $validator->validate();
        
        Theme::setActive($request->layout);
        
        if(isset($request->change_all)){
            Page::setLayoutToAllPages(Theme::where('name', $request->layout)->first()->layout);
        }
        
        return;
    }
}