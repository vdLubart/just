<?php

namespace Just\Controllers\Settings;

use Illuminate\Support\Collection;
use Just\Controllers\SettingsController;
use Just\Models\AddOn;
use Just\Models\Block\AddOns\Categories;
use Just\Models\Theme;
use Just\Requests\DeletePageRequest;
use Just\Requests\ChangePageRequest;
use Just\Models\Page;
use Just\Models\System\Route as JustRoute;

class AddOnController extends SettingsController
{
    /**
     * Render view with the add-on settings form
     *
     * @param int $addOnId page id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function settingsForm($addOnId) {
        return $this->settingsFormView($addOnId);
    }

    /**
     * Render view with page list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOnList() {
        return $this->listView();
    }

    /**
     * Build model list from the Collection in JSON format
     *
     * @param Collection $items
     * @return string
     */
    protected function buildItemList(Collection $items):string {
        $list = [];

        foreach($items as $item){
            $list[$this->itemName() . '/'. $item->id] = [
                'caption' => $this->caption($item)
            ];
        }

        return json_encode($list);
    }

    /**
     * Create new or update existing page
     *
     * @param ChangePageRequest $request
     * @return string response in JSON format
     */
    public function setup(ChangePageRequest $request) {
        $this->decodeRequest($request);

        $addOn = AddOn::findOrNew($request->addon_id);

        return $this->setupSettingsForm($addOn, $request, $request->addon_id);
    }

    /**
     * Delete page
     *
     * @param DeletePageRequest $request
     * @return string response in JSON format
     */
    public function delete(DeletePageRequest $request) {
        $page = Page::find($request->id);
        $route = JustRoute::where('route', $page->route)->first();

        if(!empty($page)){
            $page->delete();
            $route->delete();
        }

        $response = new \stdClass();
        $response->message = __('page.messages.success.deleted');

        return json_encode($response);
    }

    /**
     * Return list with available actions for the layout
     */
    public function actions() {
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

    public function categorySettingsForm($categoryId){
        return $this->addOnSettingsFormView($categoryId, 'category');
    }

    /**
     * Return list with available actions for the layout
     */
    public function categoryActions() {
        $items = [
            $this->itemKebabName() . '/category/0' => [
                'label' => __('navbar.addOns.categories.create'),
                'icon' => 'plus'
            ],
            $this->itemKebabName() . '/category/list' => [
                'label' => __('navbar.addOns.categories.list'),
                'icon' => 'list'
            ]
        ];
        $caption = [
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title')
        ];

        return $this->response($caption, $items, 'list');
    }

    /**
     * Render view with page list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categoryList() {
        return $this->addOnListView('category');
    }
}
