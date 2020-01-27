<?php

namespace Just\Controllers\Settings;


use Illuminate\Support\Collection;
use Just\Controllers\SettingsController;
use Just\Models\Layout;
use Just\Models\Page;
use Just\Models\Theme;
use Just\Requests\ChangeLayoutRequest;
use Just\Requests\DeleteLayoutRequest;
use Just\Requests\SetDefaultLayoutRequest;

class LayoutController extends SettingsController {

    /**
     * Generate settings form data
     *
     * @param int $layoutId layout id
     * @throws \Throwable
     * @return \Illuminate\Http\JsonResponse
     */
    public function settingsForm($layoutId) {
        return $this->settingsFormView($layoutId);
    }

    /**
     * Generate list data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function layoutList() {
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
                'image' => null,
                'featureIcon' => null,
                'text' => null,
                'caption' => $item->itemCaption(),
                'width' => $item->width
            ];
        }

        return json_encode($list);
    }

    /**
     * Create new or update existing layout
     *
     * @param ChangeLayoutRequest $request
     * @return string response in JSON format
     */
    public function setup(ChangeLayoutRequest $request) {
        $this->decodeRequest($request);

        $layout = Layout::findOrNew($request->layout_id);

        return $this->setupSettingsForm($layout, $request, $request->layout_id, '/settings/layout/list');
    }

    /**
     * Delete layout
     *
     * @param DeleteLayoutRequest $request
     * @return string|void
     */
    public function delete(DeleteLayoutRequest $request) {
        $layout = Layout::find($request->id);
        $response = new \stdClass();

        $pages = Page::where('layout_id', $request->id)->first();
        if(!empty($pages)){
            $response->error = __('layout.messages.error.usedOnPage', ['page' => $pages->first()->title]);
            return json_encode($response);
        }

        $layout->delete();

        $response->message = __('layout.messages.success.deleted');
        $response->redirect = '/settings/layout/list';

        return json_encode($response);
    }

    /**
     * Return list with available actions for the layout
     */
    public function actions() {
        $items = [
            $this->itemName() . '/0' => [
                'label' => __('navbar.layouts.create'),
                'icon' => 'plus'
            ],
            $this->itemName() . '/list' => [
                'label' => __('navbar.layouts.list'),
                'icon' => 'list'
            ],
            $this->itemName() . '/' . Theme::active()->layout->id => [
                'label' => __('navbar.layouts.settings'),
                'icon' => 'cogs'
            ]
        ];
        $caption = [
            '/settings/' . $this->itemName() => $this->itemTranslation('title')
        ];

        return $this->response($caption, $items, 'list');
    }

}