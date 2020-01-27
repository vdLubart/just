<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Controllers\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Just\Controllers\SettingsController;
use Just\Models\Block;
use Just\Requests\ChangeBlockRequest;
use Just\Requests\DeleteBlockRequest;
use Just\Validators\ValidatorExtended;

class BlockController extends SettingsController {

    /**
     * Set of actions of panel settings
     *
     * @param $pageId
     * @param $panelLocation
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
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

        $items = $block->model()->content();

        if(empty($caption)){
            $caption = [
                '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
                '/settings/block/' . $blockId => $this->itemTranslation('editForm.title', ['title'=>$block->itemCaption()])
            ];
        }

        return $this->response($caption, $items, 'items', ['blockTabs'=>'content', 'blockId'=>$blockId]);
    }

    /**
     * Return block by id
     *
     * @param $blockId
     * @return Block|null
     */
    protected function findBlock($blockId) {
        return Block::find($blockId);
    }

    /**
     * Form to create new block in the panel
     *
     * @param $pageId
     * @param $panelLocation
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function createForm($pageId, $panelLocation) {
        $caption = [
            '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
            '/settings/page/' . $pageId . '/panel/' . $panelLocation . '/block/create' => $this->itemTranslation('createForm.title')
        ];

        return $this->settingsFormView(0, ['page_id'=>$pageId, 'panelLocation' => $panelLocation], $caption);
    }

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
     * @param ChangeBlockRequest $request
     * @return string response in JSON format
     */
    public function setup(ChangeBlockRequest $request) {
        $this->decodeRequest($request);

        $block = Block::findOrNew($request->block_id);

        return $this->setupSettingsForm($block, $request, $request->block_id, '/settings/page/' . $block->page_id . '/panel/' . $block->panelLocation . '/block/list');
    }

    /**
     * Create new or update existing block item
     *
     * @param Request $request
     * @return string response in JSON format
     * @throws
     */
    public function itemSetup(Request $request) {
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
        $response->message = $this->itemTranslation('messages.success.' . ($request->id == 0 ? 'created' : 'updated'));
        $response->redirect = '/settings/block/' . $request->block_id;

        return json_encode($response);
    }

    public function blockList($pageId, $panelLocation) {
        $caption = [
            '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
            '/settings/' . $this->itemKebabName() . '/list' => $this->itemTranslation('list')
        ];

        return $this->listView($caption);
    }

    protected function buildItemList(Collection $items): string {
        $list = [];

        foreach($items as $item){

            $list[$this->itemName() . '/' . ($item->block_id ?? $item->id) . ( !!$item->block_id ? '/item/' . $item->id : '')] = [
                'image' => $item->itemImage(),
                'featureIcon' => $item->itemIcon(),
                'text' => $item->itemText(),
                'caption' => $item->itemCaption(),
                'width' => $item->width
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

        $item = $block->specify($itemId)->model();

        if(empty($caption)){
            $caption = [
                '/settings/page/' . $pageId . '/panel/' . $panelLocation => __('panel.title', ['panel'=>$panelLocation]),
                '/settings/block/' . $blockId => $this->itemTranslation('editForm.title', ['title'=>$block->title])
            ];

            if($itemId > 0){
                $caption['/settings/block/' . $blockId . '/item/' . $itemId] = $this->itemTranslation('editForm.item');
            }
        }

        return $this->response($caption, $item, 'form', ['blockTabs'=>($itemId == 0 ? 'createItem':null), 'itemTabs'=>($itemId == 0 ? null:'edit'),  'blockId'=>$blockId, 'itemId' => $itemId]);
    }

    /**
     * Delete page
     *
     * @param DeleteBlockRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteBlockRequest $request) {
        $block = Block::find($request->id);

        $response = new \stdClass();

        if(!empty($block)){
            $pageId = $block->page_id;
            $location = $block->panelLocation;

            $block->delete();

            $response->message = __('page.messages.success.deleted');
            $response->redirect = '/settings/page/' . $pageId . '/panel/' . $location . '/block/list';
        }

        return json_encode($response);
    }

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

        return Response::json($response);
    }

    /**
     * Customize parameters for existing block
     *
     * @param Request $request
     * @return string response in JSON format
     */
    public function customize(Request $request) {
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

                if(in_array($key, $settingsElements) or in_array($key."[]", $settingsElements)){
                    $parameters->{trim($key, "[]")} = ($value == 'on' and $block->customizationForm()->getElement($key)->type() == 'checkbox') ? true : $value;
                }
            }

            $block->parameters = $parameters;
            $block->save();

            $block->model()->setup();
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.customized');
        $response->redirect = '/settings/block/' . $block->id;

        return json_encode($response);
    }
}