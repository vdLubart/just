<?php

namespace Just\Controllers\Settings;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Just\Controllers\SettingsController;
use Just\Requests\InitializePageRequest;
use Just\Requests\SavePageRequest;
use Just\Models\Page;
use Just\Models\System\Route as JustRoute;
use Throwable;

class PageController extends SettingsController
{
    /**
     * Render view with the page settings form
     *
     * @param int $pageId page id
     * @return JsonResponse
     * @throws Throwable
     */
    public function settingsForm(int $pageId): JsonResponse {
        return $this->settingsFormView($pageId);
    }

    /**
     * Render view with page list
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function pageList(): JsonResponse {
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
                'caption' => $item->title . ' :: /' . $item->route,
                'isActive' => $item->isActive
            ];
        }

        return json_encode($list);
    }

    /**
     * Create new or update existing page
     *
     * @param SavePageRequest $request
     * @return JsonResponse
     */
    public function setup(SavePageRequest $request): JsonResponse {
        $this->decodeRequest($request);

        $page = Page::findOrNew($request->page_id);

        return $this->setupSettingsForm($page, $request, $request->page_id, '/settings/page/list');
    }

    /**
     * Delete page
     *
     * @param InitializePageRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(InitializePageRequest $request): JsonResponse {
        $page = Page::find($request->id);
        $route = JustRoute::where('route', $page->route)->first();

        if(!empty($page)){
            $page->delete();
            $route->delete();
        }

        $response = new \stdClass();
        $response->message = __('page.messages.success.deleted');
        $response->redirect = '/settings/page/list';

        return response()->json($response);
    }

    /**
     * Return list with available actions for the layout
     *
     * @return JsonResponse
     */
    public function actions(): JsonResponse {
        $items = [
            $this->itemName() . '/0' => [
                'label' => __('navbar.pages.create'),
                'icon' => 'plus'
            ],
            $this->itemName() . '/list' => [
                'label' => __('navbar.pages.list'),
                'icon' => 'list'
            ]
        ];
        $caption = [
            '/settings/' . $this->itemName() => $this->itemTranslation('title')
        ];

        return $this->response($caption, $items, 'list');
    }

    public function activate(InitializePageRequest $request) {
        return $this->pageVisibility($request, true);
    }

    public function deactivate(InitializePageRequest $request) {
        return $this->pageVisibility($request, false);
    }

    /**
     * Change add-on visibility
     *
     * @param InitializePageRequest $request
     * @param boolean $visibility
     * @return false|string
     */
    protected function pageVisibility(InitializePageRequest $request, bool $visibility) {
        $page = Page::find($request->id);

        if(!empty($page)){
            $page->isActive = (int)$visibility;
            $page->save();
        }

        $response = new \stdClass();
        $response->message = $this->itemTranslation('messages.success.' . ($visibility ? 'activated' : 'deactivated'));
        $response->redirect = '/settings/page/list';

        return json_encode($response);
    }
}
