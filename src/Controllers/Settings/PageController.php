<?php

namespace Just\Controllers\Settings;

use Just\Controllers\SettingsController;
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsForm($pageId) {
        return $this->settingsFormView($pageId);
    }

    /**
     * Render view with page list
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pageList() {
        return $this->listView();
    }

    /**
     * Create new or update existing page
     *
     * @param ChangePageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setup(ChangePageRequest $request) {
        $page = Page::findOrNew($request->page_id);

        $page->handleSettingsForm($request);

        return redirect()->back();
    }

    /**
     * Delete page
     *
     * @param DeletePageRequest $request
     */
    public function delete(DeletePageRequest $request) {
        $page = Page::find($request->id);
        $route = JustRoute::where('route', $page->route)->first();

        if(!empty($page)){
            $page->delete();
            $route->delete();
        }

        return ;
    }
}
