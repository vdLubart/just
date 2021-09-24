<?php

namespace Just\Controllers\Settings;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Just\Controllers\SettingsController;
use Just\Requests\InitializePageRequest;
use Just\Requests\SavePageRequest;
use Just\Models\Page;
use Just\Models\System\Route as JustRoute;
use stdClass;
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
        $page = Page::findOrNew($pageId);

        if(empty($caption)){
            $caption = [
                '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
                '/settings/' . $this->itemKebabName() . '/' . $pageId . '/settings' => $pageId == 0 ? $this->itemTranslation('createForm.title') : $this->itemTranslation('editForm.title', ['page' => $page->title])
            ];
        }

        return $this->response($caption, $page, 'form');
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

    public function panelList(int $pageId) {
        $page = Page::find($pageId);

        if(empty($page)){
            return redirect('settings');
        }

        $panels = $page->layout->panels();

        $response = new stdClass();

        $response->caption = [
                '/settings' =>  __('settings.title'),
                '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
                '/settings/' . $this->itemKebabName() . '/' . $pageId => $this->itemTranslation('actions', ['page' => $page->title]),
                '/settings/' . $this->itemKebabName() . '/' . $pageId . '/panels' => $this->itemTranslation('panels')
            ];
        $response->contentType = 'items';

        $response->parameters = [];

        $contentList = [];

        foreach($panels as $panel){
            $contentList[$this->itemName() . '/'. $page->id . '/panel/' . $panel->location] = [
                'caption' => $panel->location,
                'isActive' => true
            ];
        }

        $response->content = json_encode($contentList);

        return Response::json((array) $response);
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

        $response = new stdClass();
        $response->message = __('page.messages.success.deleted');
        $response->redirect = '/settings/page/list';

        return response()->json($response);
    }

    /**
     * Return list with available actions for the pages
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

    /**
     * Return list with available actions for the specific page
     *
     * @param int $pageId
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function pageActions(int $pageId) {
        $page = Page::find($pageId);

        if(empty($page)){
            if($pageId === 0){
                return redirect('settings/page/0/settings');
            }
            return redirect('settings');
        }

        $items = [
            $this->itemName() . '/' . $pageId . '/settings' => [
                'label' => __('settings.title'),
                'icon' => 'cog'
            ],
            $this->itemName() . '/' . $pageId . '/panels' => [
                'label' => __('page.panels'),
                'icon' => 'columns'
            ],
        ];

        $caption = [
            '/settings/' . $this->itemName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemName() . '/' . $pageId => $this->itemTranslation('actions', ['page' => $page->title])
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

        $response = new stdClass();
        $response->message = $this->itemTranslation('messages.success.' . ($visibility ? 'activated' : 'deactivated'));
        $response->redirect = '/settings/page/list';

        return json_encode($response);
    }
}
