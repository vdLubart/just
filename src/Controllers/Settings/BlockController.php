<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Controllers\Settings;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Just\Controllers\SettingsController;
use Just\Models\Block;
use Just\Models\Blocks\Events;
use Just\Requests\Block\Admin\InitializeBlockRequest;
use Just\Requests\SaveBlockRequest;
use Just\Requests\DeleteBlockRequest;
use Just\Requests\Block\Admin\InitializeItemRequest;
use Just\Tools\Useful;
use Just\Validators\ValidatorExtended;
use Throwable;

class BlockController extends SettingsController {

    /**
     * Set of actions of panel settings
     *
     * @param $pageId
     * @param $panelLocation
     * @return JsonResponse
     * @throws Throwable
     */
    public function panelActions($pageId, $panelLocation) {
        $items = [
            'page/' . $pageId . '/panel/' . $panelLocation . '/block/create' => [
                'label' => $this->itemTranslation('create'),
                'icon' => 'plus'
            ],
            'page/' . $pageId . '/panel/' . $panelLocation . '/block/list' => [
                'label' => $this->itemTranslation('list'),
                'icon' => 'list'
            ]
        ];
        $caption = [
            '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation])
        ];

        return $this->response($caption, $items, 'list');
    }

    public function content($blockId) {
        if(empty($block = $this->findBlock($blockId))){
            return redirect()->back();
        }

        $pageId = $block->page_id;
        $panelLocation = $block->panelLocation;

        $items = $block->item()->content();

        $type = $block->type === 'menu' ? 'menu' : 'items';

        if(empty($caption)){
            $caption = [
                '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
                '/settings/block/' . $blockId => $this->itemTranslation('editForm.title', ['title'=>$block->itemCaption()])
            ];
        }

        return $this->response($caption, $items, $type, ['blockTabs'=>'content', 'blockId'=>$blockId, 'blockType'=>$block->type]);
    }

    /**
     * Return block by id
     *
     * @param $blockId
     * @return Block|null
     */
    protected function findBlock($blockId): ?Block {
        return Block::find($blockId);
    }

    /**
     * Specify block
     *
     * @param Request $request
     * @return Block
     */
    private function specifyBlock(Request $request): Block {
        $block = Block::find($request->block_id);

        if (!empty($block)) {
            $block->specify($request->id);
        }

        return $block;
    }

    /**
     * Form to create new block in the panel
     *
     * @param string $pageId
     * @param string $panelLocation
     * @return JsonResponse
     * @throws Throwable
     */
    public function createForm(string $pageId, string $panelLocation): JsonResponse {
        $caption = [
            '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
            '/settings/page/' . $pageId . '/panel/' . $panelLocation . '/block/create' => $this->itemTranslation('createForm.title')
        ];

        return $this->settingsFormView(0, ['page_id'=>$pageId, 'panelLocation' => $panelLocation], $caption);
    }

    /**
     * @throws Throwable
     */
    public function settingsForm($blockId) {
        $block = Block::find($blockId);

        if(empty($block)){
            return redirect()->back();
        }

        $pageId = $block->page_id;
        $panelLocation = $block->panelLocation;

        $caption = [
            '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
            '/settings/page/block/'.$blockId => $this->itemTranslation('editForm.title', ['title'=> $block->itemCaption()])
        ];

        return $this->settingsFormView($blockId, ['page_id'=>$pageId, 'panelLocation' => $panelLocation], $caption, ['blockTabs'=>'blockSettings', 'blockId'=>$blockId]);
    }

    /**
     * Create new or update existing block
     *
     * @param SaveBlockRequest $request
     * @return JsonResponse
     */
    public function setup(SaveBlockRequest $request): JsonResponse {
        $this->decodeRequest($request);

        $block = Block::findOrNew($request->block_id);

        return $this->setupSettingsForm($block, $request, $request->block_id, '/settings/page/' . $request->page_id . '/panel/' . $request->panelLocation . '/block/list');
    }

    public function moveUp(InitializeBlockRequest $request) {
        return $this->moveBlock($request, 'up');
    }

    public function moveDown(InitializeBlockRequest $request) {
        return $this->moveBlock($request, 'down');
    }

    protected function moveBlock(InitializeBlockRequest $request, $dir) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->move($dir);
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.moved');
        $response->redirect = '/settings/page/' . $block->page_id . '/panel/' . $block->panelLocation . '/block/list';

        return json_encode($response);
    }

    public function activate(InitializeBlockRequest $request) {
        return $this->blockVisibility($request, true);
    }

    public function deactivate(InitializeBlockRequest $request) {
        return $this->blockVisibility($request, false);
    }

    /**
     * Change item visibility
     *
     * @param InitializeBlockRequest $request
     * @param boolean $visibility
     * @return false|string
     */
    protected function blockVisibility(InitializeBlockRequest $request, bool $visibility) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->visibility($visibility);
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.' . ($visibility ? 'activated' : 'deactivated'));
        $response->redirect = '/settings/page/' . $block->page_id . '/panel/' . $block->panelLocation . '/block/list';

        return json_encode($response);
    }

    /**
     * Create new or update existing block item
     *
     * @param Request $request
     * @return array|false|RedirectResponse|string
     * @throws ValidationException
     * @throws Exception
     */
    public function saveItem(Request $request) {
        $this->decodeRequest($request);

        if(empty($block = $this->findBlock($request->block_id))){
            return redirect()->back();
        }
        $block->specify($request->id);

        $item = $block->handleItemSetupForm($request);
        if($item instanceof ValidatorExtended){
            return $item->validate();
        }

        $response = new \stdClass();
        if($item->shouldBeCropped){
            $response->message = 'Image should be cropped';
            $response->redirect = '/settings/block/'.$block->id.'/item/'.$item->id.'/cropping';
        }
        else{
            $response->message = $this->itemTranslation('messages.success.' . ($request->id == 0 ? 'created' : 'updated'));
            $response->redirect = '/settings/block/' . $request->block_id;
        }

        return json_encode($response);
    }

    public function itemCroppingForm($blockId, $itemId): JsonResponse {
        /**
         * @var Block $block
         */
        $block = Block::find($blockId);
        $pageId = $block->page_id;
        $panelLocation = $block->panelLocation;

        $item = $block->specify($itemId)->item();

        if(empty($caption)){
            $caption = [
                '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
                '/settings/block/' . $block->id => $this->itemTranslation('editForm.title', ['title'=>$block->title])
            ];

            if($item->id > 0){
                $caption['/settings/block/' . $block->id . '/item/' . $item->id] = $this->itemTranslation('editForm.item');
            }
        }

        $response = new \stdClass();

        $response->caption = [
                '/settings' =>  __('settings.title')
            ] + $caption;
        $response->contentType = 'crop';

        $response->parameters = [];

        $parameters = ['blockId'=>$block->id, 'itemId' => $item->id, 'image'=>$item->imageSource('original'), 'imageCode'=>$item->image, 'dimensions'=>$item->parameter('cropDimensions')];

        foreach ($parameters as $key=>$parameter){
            $response->parameters[$key] = $parameter;
        }

        return Response::json((array)$response);
    }

    /**
     * [POST] Crop the image in the requested block
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function cropItem(Request $request) {
        if(empty($block = $this->specifyBlock($request))){
            return redirect()->back();
        }

        if(!empty($block)){
            $block->handleCrop($request);
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.updated');
        $response->redirect = '/settings/block/'.$block->id;

        return Response::json((array)$response);
    }

    public function itemMoveUp(InitializeItemRequest $request) {
        return $this->moveItem($request, 'up');
    }

    public function itemMoveDown(InitializeItemRequest $request) {
        return $this->moveItem($request, 'down');
    }

    protected function moveItem(InitializeItemRequest $request, $dir) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->move($dir);
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.item.moved');
        $response->redirect = '/settings/block/' . $block->id;

        return json_encode($response);
    }

    public function itemActivate(InitializeItemRequest $request) {
        return $this->itemVisibility($request, true);
    }

    public function itemDeactivate(InitializeItemRequest $request) {
        return $this->itemVisibility($request, false);
    }

    /**
     * Change item visibility
     *
     * @param InitializeItemRequest $request
     * @param boolean $visibility
     * @return false|string
     */
    protected function itemVisibility(InitializeItemRequest $request, bool $visibility) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->visibility($visibility);
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.item.' . ($visibility ? 'activated' : 'deactivated'));
        $response->redirect = '/settings/block/' . $block->id;

        return json_encode($response);
    }

    public function itemDelete(InitializeItemRequest $request) {
        $block = $this->specifyBlock($request);

        if(!empty($block)){
            $block->deleteItem();

            if(!$block->item() instanceof Events) {
                Useful::normalizeOrder($block->item()->getTable());
            }
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.item.deleted');
        $response->redirect = '/settings/block/' . $block->id;

        return json_encode($response);
    }


    /**
     * @param int $pageId
     * @param string $panelLocation
     * @return JsonResponse
     * @throws Throwable
     */
    public function blockList(int $pageId, string $panelLocation): JsonResponse {
        $caption = [
            '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
            '/settings/' . $this->itemKebabName() . '/list' => $this->itemTranslation('list')
        ];

        return $this->listView($caption, ['panelLocation' => $panelLocation]);
    }

    protected function buildItemList(Collection $items): string {
        $list = [];

        foreach($items as $item){

            $list[$this->itemName() . '/' . ($item->block_id ?? $item->id) . ( !!$item->block_id ? '/item/' . $item->id : '')] = [
                'image' => $item->itemImage(),
                'featureIcon' => $item->itemIcon(),
                'text' => $item->itemText(),
                'caption' => $item->itemCaption(),
                'width' => $item->width,
                'isActive' => !!$item->isActive
            ];
        }

        return json_encode($list);
    }

    public function itemSettingsForm($blockId, $itemId) {
        if(empty($block = $this->findBlock($blockId))){
            return redirect()->back();
        }

        $pageId = $block->page_id;
        $panelLocation = $block->panelLocation;

        $item = $block->specify($itemId)->item();

        if(empty($caption)){
            $caption = [
                '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
                '/settings/block/' . $blockId => $this->itemTranslation('editForm.title', ['title'=>$block->itemCaption()])
            ];

            if($itemId > 0){
                $caption['/settings/block/' . $blockId . '/item/' . $itemId] = $this->itemTranslation('editForm.item');
            }
        }

        return $this->response($caption, $item, 'form', ['blockTabs'=>($itemId == 0 ? 'createItem':null), 'itemTabs'=>($itemId == 0 ? null:'edit'),  'blockId'=>$blockId, 'itemId' => $itemId]);
    }

    /**
     * Delete block
     *
     * @param DeleteBlockRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteBlockRequest $request): JsonResponse {
        $block = Block::find($request->id);

        $response = new \stdClass();

        if(!empty($block)){
            $pageId = $block->page_id;
            $location = $block->panelLocation;

            $block->delete();

            $response->message = __('page.messages.success.deleted');
            $response->redirect = '/settings/page/' . $pageId . '/panel/' . $location . '/block/list';
        }

        return Response::json(json_encode($response));
    }

    /**
     * @param $blockId
     * @return JsonResponse|RedirectResponse
     */
    public function customizationForm($blockId) {
        $block = Block::find($blockId);

        if(empty($block)){
            return redirect()->back();
        }

        $pageId = $block->page_id;
        $panelLocation = $block->panelLocation;

        $caption = [
            '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
            '/settings/page/block/'.$blockId => $this->itemTranslation('editForm.title', ['title'=> $block->itemCaption()])
        ];

        $response = new \stdClass();

        $response->caption = [
                '/settings' =>  __('settings.title')
            ] + $caption;
        $response->contentType = 'form';

        $response->parameters = [
            'blockId'=>$blockId,
            'blockTabs'=>'blockCustomization'
        ];

        $response->content = $block->customizationForm()->toJson();

        return Response::json(json_encode($response));
    }

    /**
     * Customize parameters for existing block
     *
     * @param Request $request
     * @return string response in JSON format
     */
    public function customize(Request $request): string {
        $block = Block::find($request->id)->specify();
        $settingsElements = $block->customizationForm()->names();

        $block->unsettleAddons();

        if(!empty($block)){
            $parameters = $block->parameters ?? json_decode('{}');

            foreach($settingsElements as $name){
                if(!in_array($name, ['id', 'submit', 'button'])){
                    if($block->customizationForm()->element($name)->type() == 'checkbox') {
                        $parameters->{$name} = false;
                    }
                    else{
                        $parameters->{$name} = '';
                    }
                }
            }

            $values = $request->all();
            unset($values['id']);
            unset($values['submit']);

            foreach($values as $key=>$value){
                if(is_string($value) and $value === (string)(int)$value){
                    $value = (int)$value;
                }

                if($block->customizationForm()->getElement($key)->type() == 'checkbox' and in_array($value, ['true', 'false'])){
                    $value = $value === 'true';
                }

                if(in_array($key, $settingsElements) or in_array($key."[]", $settingsElements)){
                    $parameters->{trim($key, "[]")} = ($value == 'on' and $block->customizationForm()->getElement($key)->type() == 'checkbox') ? true : $value;
                }
            }

            $block->parameters = $parameters;
            $block->save();

            $block->item()->setup();
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.customized');
        $response->redirect = '/settings/block/' . $block->id;

        return json_encode($response);
    }
}
