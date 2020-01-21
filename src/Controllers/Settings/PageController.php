<?php

namespace Just\Controllers\Settings;

use Illuminate\Support\Collection;
use Just\Controllers\SettingsController;
use Just\Models\Theme;
use Just\Requests\DeletePageRequest;
use Just\Requests\ChangePageRequest;
use Just\Models\Page;
use Just\Models\System\Route as JustRoute;

class PageController extends SettingsController
{
    /**
     * Render view with the page settings form
     *
     * @param int $pageId page id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function settingsForm($pageId) {
        return $this->settingsFormView($pageId);
    }

    /**
     * Render view with page list
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pageList() {
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
                'caption' => $item->title . ' :: /' . $item->route
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
        $page = Page::findOrNew($request->page_id);

        return $this->setupSettingsForm($page, $request, $request->page_id, '/settings/page/list');
    }

    /**
     * Delete page
     *
     * @param DeletePageRequest $request
     * @return \Illuminate\Http\JsonResponse
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
        $response->redirect = '/settings/page/list';

        return json_encode($response);
    }

    /**
     * Return list with available actions for the layout
     */
    public function actions() {
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
}
