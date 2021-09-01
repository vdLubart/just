<?php

namespace Just\Controllers\Settings;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Just\Controllers\SettingsController;
use Just\Models\Layout;
use Just\Models\Page;
use Just\Models\Theme;
use Just\Requests\SaveLayoutRequest;
use Just\Requests\InitializeLayoutRequest;
use Throwable;

class LayoutController extends SettingsController {

    /**
     * Generate settings form data
     *
     * @param int $layoutId layout id
     * @return JsonResponse
     * @throws Throwable
     */
    public function settingsForm(int $layoutId): JsonResponse {
        return $this->settingsFormView($layoutId);
    }

    /**
     * Generate list data
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function layoutList(): JsonResponse {
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
                'caption' => $item->itemCaption(),
                'isActive' => true, // all layouts are active
                'width' => $item->width
            ];
        }

        return json_encode($list);
    }

    /**
     * Create new or update existing layout
     *
     * @param SaveLayoutRequest $request
     * @return JsonResponse
     */
    public function setup(SaveLayoutRequest $request): JsonResponse {
        $this->decodeRequest($request);

        $layout = Layout::findOrNew($request->layout_id);

        return $this->setupSettingsForm($layout, $request, $request->layout_id, '/settings/layout/list');
    }

    /**
     * Delete layout
     *
     * @param InitializeLayoutRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(InitializeLayoutRequest $request): JsonResponse {
        $layout = Layout::find($request->layout_id);
        $response = new \stdClass();

        $pages = Page::where('layout_id', $request->layout_id)->first();
        if(!empty($pages)){
            $response->error = __('layout.messages.error.usedOnPage', ['page' => $pages->first()->title]);
            return response()->json($response);
        }

        $layout->delete();

        $response->message = __('layout.messages.success.deleted');
        $response->redirect = '/settings/layout/list';

        return response()->json($response);
    }

    /**
     * Return list with available actions for the layout
     */
    public function actions(): JsonResponse {
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
