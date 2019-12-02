<?php

namespace Lubart\Just\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Lubart\Just\Models\Theme;

class SettingsController extends Controller
{
    public function __construct() {
        parent::__construct();

        \Config::set('isAdmin', true);
    }

    /**
     * Return class name
     *
     * @return string
     */
    private function itemClass() {
        return '\\Lubart\\Just\\Models\\' . $this->modelName();
    }

    /**
     * Return model name
     *
     * @return string
     */
    private function modelName() {
        return Str::substr(Arr::last(explode('\\', get_class($this))), 0, -10);
    }

    /**
     * Return item name
     *
     * @return string
     */
    private function itemName() {
        return Str::lower($this->modelName());
    }

    /**
     * Return translatable phrase for the item
     *
     * @param string $phrasePath key path in language file
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    private function itemPhrase($phrasePath) {
        return __($this->itemName() . "." . $phrasePath);
    }

    /**
     * Generate response
     *
     * @param array $caption settings chapter caption
     * @param mixed $item model
     * @throws \Throwable
     * @return \Illuminate\Http\JsonResponse
     */
    private function response(array $caption, $item, $type) {
        $response = new \stdClass();

        $response->caption = __('settings.title') . " :: " . implode(" :: ", $caption);
        $response->contentType = $type;
        switch ($type){
            case 'form':
                $response->content = $item->settingsForm()->toJson();
                break;
            case 'list':
                $response->content = $item;
                break;
        }


        return Response::json($response);
    }

    /**
     * Render view with the settings form
     *
     * @param integer $id item id
     * @throws \Throwable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function settingsFormView($id) {
        $item = $this->itemClass()::findOrNew($id);
        $caption = [
            $this->itemPhrase('title'),
            $id == 0 ? $this->itemPhrase('createForm.title') : $this->itemPhrase('editForm.title')
        ];

        return $this->response($caption, $item, 'form');
    }

    protected function listView() {
        $items = $this->itemClass()::all();
        $caption = [
            __($this->itemName() . '.title'),
            __($this->itemName() . '.list')
        ];

        return $this->response($caption, view(viewPath(Theme::active()->layout, $this->itemName().'List'))->with([Str::plural($this->itemName())=>$items]));
    }

    protected function noAccessView() {
        return view(viewPath(Theme::active()->layout, 'noAccess'));
    }
}
