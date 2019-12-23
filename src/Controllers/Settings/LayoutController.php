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
    protected function buildList(Collection $items):string {
        $list = [];

        foreach($items as $item){
            $list[$item->id] = $item->name. ".". $item->class;
        }

        return json_encode($list);
    }

    /**
     * Create new or update existing layout
     *
     * @param ChangeLayoutRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function setup(ChangeLayoutRequest $request) {
        $layout = Layout::findOrNew($request->layout_id);

        $layout->handleSettingsForm($request);

        return redirect()->back();
    }

    /**
     * Render view with default layout form
     *
     * TODO: delete this view and move functoinality to layoutList
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function defaultLayout(){
        return view(viewPath(Theme::active()->layout, 'defaultLayout'))->with(['form'=>Layout::setDefaultForm()]);
    }

    /**
     * Set default layout
     *
     * @param SetDefaultLayoutRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function setDefault(SetDefaultLayoutRequest $request){
        Theme::setActive($request->layout);

        if(isset($request->change_all)){
            Page::setLayoutToAllPages(Theme::where('name', $request->layout)->first()->layout);
        }

        return;
    }

    /**
     * Delete layout
     *
     * @param DeleteLayoutRequest $request
     * @return string|void
     */
    public function delete(DeleteLayoutRequest $request) {
        $layout = Layout::find($request->layout_id);

        if(!empty($layout)){
            $pages = Page::where('layout_id', $request->layout_id)->first();
            if(!empty($pages)){
                return json_encode(['error'=>'Layout cannot be deleted because page "'.$pages->first()->title.'" is using it']);
            }

            $layout->delete();
        }

        return ;
    }

}