<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Controllers\Settings;


use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Just\Controllers\SettingsController;
use Just\Models\AddOn;
use Just\Models\Blocks\AddOns\AddOnOption;
use Just\Requests\InitializeAddOnOptionRequest;
use Just\Requests\SaveAddOnOptionRequest;

class AddOnOptionController extends SettingsController {

    /**
     * Return list with available actions for the layout
     *
     * @return JsonResponse
     */
    public function actions(): JsonResponse {
        $items = [
            $this->itemKebabName() . '/category/option/0' => [
                'label' => __('navbar.addOns.options.create'),
                'icon' => 'plus'
            ],
            $this->itemKebabName() . '/category/list' => [
                'label' => __('navbar.addOns.options.categoryList'),
                'icon' => 'list'
            ],
            $this->itemKebabName() . '/tag/list' => [
                'label' => __('navbar.addOns.options.tagList'),
                'icon' => 'list'
            ]
        ];
        $caption = [
            '/settings/add-on' => $this->itemTranslation('title'),
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('category.title')
        ];

        return $this->response($caption, $items, 'list');
    }

    /**
     * Render view with page list
     *
     * @param string $addOnType
     * @return JsonResponse
     */
    public function list(string $addOnType): JsonResponse {
        $addOns = AddOn::where('type', $addOnType)->get();
        $items = [];
        foreach ($addOns as $addOn){
            $items['add-on-option/' . $addOn->id] = [
                'icon' => 'list',
                'label' => __('addOn.addOnLocation', ['addOn' => $addOn->title, 'block' => $addOn->block->title, 'page' => $addOn->block->page()->title])
            ];
        }
        $caption = [
            '/settings/add-on' => __('addOn.title'),
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemKebabName() . '/' . $addOnType . '/list' => $this->itemTranslation($addOnType . '.list')
        ];

        return $this->response($caption, $items, 'list');
    }

    public function optionList(int $addOnId): JsonResponse {
        $addOn = AddOn::find($addOnId);
        $items = $addOn->options;

        $caption = [
            '/settings/add-on' => __('addOn.title'),
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemKebabName() . '/' . $addOn->id => $addOn->id == 0 ? $this->itemTranslation('createForm.title') : $this->itemTranslation('editForm.title')
        ];

        return $this->response($caption, $items, 'items', ['addonType' => $addOn->type]);
    }

    /**
     * @param string $addOnType
     * @param int $optionId
     * @return JsonResponse
     */
    public function optionForm(string $addOnType, int $optionId): JsonResponse {
        $option = AddOnOption::findOrNew($optionId);

        $caption = [
            '/settings/add-on' => __('addOn.title'),
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemKebabName() . '/' . $addOnType . '/option/' . $optionId => $this->itemTranslation( ($optionId == 0 ? 'create' : 'edit') . 'Form.title')
        ];

        return $this->response($caption, $option, 'form');
    }

    /**
     * @param SaveAddOnOptionRequest $request
     * @return JsonResponse
     */
    public function setup(SaveAddOnOptionRequest $request): JsonResponse {
        $this->decodeRequest($request);

        $categoryOption = AddonOption::findOrNew($request->id);
        $addon = AddOn::find($request->add_on_id);

        return $this->setupSettingsForm($categoryOption, $request, $request->add_on_id, '/settings/add-on-option/' . $addon->id);
    }

    protected function buildItemList(Collection $items): string {
        $list = [];

        foreach($items as $item){
            $list[$this->itemKebabName() . '/category/option/'. $item->id] = [
                'caption' => $item->option,
                'isActive' => !!$item->isActive
            ];
        }

        return json_encode($list);
    }

    /**
     * Delete add-on option
     *
     * @param InitializeAddOnOptionRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function delete(InitializeAddOnOptionRequest $request): JsonResponse {
        $addonOption = AddOnOption::find($request->id);
        $addOn = $addonOption->addOn;

        if(!empty($addonOption)){
            $addonOption->delete();
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.deleted');
        $response->redirect = '/settings/add-on-option/' . $addOn->type . '/' . $addonOption->add_on_id;

        return response()->json($response);
    }

    public function moveUp(InitializeAddOnOptionRequest $request) {
        return $this->moveOption($request, 'up');
    }

    public function moveDown(InitializeAddOnOptionRequest $request) {
        return $this->moveOption($request, 'down');
    }

    protected function moveOption(InitializeAddOnOptionRequest $request, $dir) {
        $addOnOption = AddOnOption::find($request->id);
        $addOn = $addOnOption->addOn;

        if(!empty($addOnOption)){
            $addOnOption->move($dir);
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.moved');
        $response->redirect = '/settings/add-on-option/' . $addOn->type . '/' . $addOnOption->add_on_id;

        return json_encode($response);
    }

    public function activate(InitializeAddOnOptionRequest $request) {
        return $this->optionVisibility($request, true);
    }

    public function deactivate(InitializeAddOnOptionRequest $request) {
        return $this->optionVisibility($request, false);
    }

    /**
     * Change add-on option visibility
     *
     * @param InitializeAddOnOptionRequest $request
     * @param boolean $visibility
     * @return false|string
     */
    protected function optionVisibility(InitializeAddOnOptionRequest $request, bool $visibility) {
        $addOnOption = AddOnOption::find($request->id);
        $addOn = $addOnOption->addOn;

        if(!empty($addOnOption)){
            $addOnOption->isActive = (int)$visibility;
            $addOnOption->save();
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.' . ($visibility ? 'activated' : 'deactivated'));
        $response->redirect = '/settings/add-on-option/' .$addOn->type. '/' . $addOnOption->add_on_id;

        return json_encode($response);
    }
}
