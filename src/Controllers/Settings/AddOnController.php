<?php

namespace Just\Controllers\Settings;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Just\Controllers\SettingsController;
use Just\Models\AddOn;
use Just\Models\Blocks\AddOns\Image;
use Just\Requests\DeleteAddOnRequest;
use Just\Requests\InitializeAddOnRequest;
use Just\Requests\SaveAddonRequest;
use Throwable;

class AddOnController extends SettingsController
{
    /**
     * Render view with the add-on settings form
     *
     * @param int $addOnId page id
     * @return JsonResponse
     * @throws Throwable
     */
    public function settingsForm(int $addOnId): JsonResponse {
        return $this->settingsFormView($addOnId);
    }

    /**
     * Render view with page list
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function addOnList(): JsonResponse {
        return $this->listView();
    }

    /**
     * Build model list from the Collection in JSON format
     *
     * @param Collection $items
     * @return string
     */
    protected function buildItemList(Collection $items): string {
        $list = [];

        foreach($items as $item){
            $list[$this->itemKebabName() . '/'. $item->id] = [
                'caption' => $item->title,
                'params' => [
                    "Type" => $item->type,
                    "Variable name" => $item->name,
                    "Block" => "'" . $item->block->title . "' at the '" . $item->block->page()->title . "' page"
                ],
                'isActive' => !!$item->isActive
            ];
        }

        return json_encode($list);
    }

    /**
     * Create new or update existing add-on
     *
     * @param SaveAddonRequest $request
     * @return JsonResponse response in JSON format
     */
    public function setup(SaveAddonRequest $request): JsonResponse {
        $this->decodeRequest($request);

        $addOn = AddOn::findOrNew($request->addon_id);

        return $this->setupSettingsForm($addOn, $request, $request->addon_id);
    }

    /**
     * Delete add-on
     *
     * @param DeleteAddOnRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(DeleteAddOnRequest $request): JsonResponse {
        $addon = AddOn::find($request->id);

        if(!empty($addon)){
            if($addon->type === 'image'){
                foreach(Image::where('add_on_id', $addon->id)->get() as $image){
                    $addon->block->item()->deleteImage($image->value);
                }
            }

            $addon->delete();
        }

        $response = new \stdClass();
        $response->message = __('addon.messages.success.deleted');
        $response->redirect = '/settings/add-on/list';

        return response()->json(json_encode($response));
    }

    /**
     * Return list with available actions for the layout
     */
    public function actions(): JsonResponse {
        $items = [
            $this->itemKebabName() . '/0' => [
                'label' => __('navbar.addOns.create'),
                'icon' => 'plus'
            ],
            $this->itemKebabName() . '/list' => [
                'label' => __('navbar.addOns.list'),
                'icon' => 'list'
            ]
        ];
        $caption = [
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title')
        ];

        return $this->response($caption, $items, 'list');
    }

    public function activate(InitializeAddOnRequest $request) {
        return $this->addOnVisibility($request, true);
    }

    public function deactivate(InitializeAddOnRequest $request) {
        return $this->addOnVisibility($request, false);
    }

    /**
     * Change add-on visibility
     *
     * @param InitializeAddOnRequest $request
     * @param boolean $visibility
     * @return false|string
     */
    protected function addOnVisibility(InitializeAddOnRequest $request, bool $visibility) {
        $addon = AddOn::find($request->id);

        if(!empty($addon)){
            $addon->isActive = (int)$visibility;
            $addon->save();
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.' . ($visibility ? 'activated' : 'deactivated'));
        $response->redirect = '/settings/add-on/list';

        return json_encode($response);
    }

    public function moveUp(InitializeAddOnRequest $request) {
        return $this->moveAddOn($request, 'up');
    }

    public function moveDown(InitializeAddOnRequest $request) {
        return $this->moveAddOn($request, 'down');
    }

    protected function moveAddOn(InitializeAddOnRequest $request, $dir) {
        $addon = AddOn::find($request->id);

        if(!empty($addon)){
            $addon->move($dir);
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.moved');
        $response->redirect = '/settings/add-on/list';

        return json_encode($response);
    }
}
