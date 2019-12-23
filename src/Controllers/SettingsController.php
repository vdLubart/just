<?php

namespace Just\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Just\Models\Theme;

abstract class SettingsController extends Controller
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
        return '\\Just\\Models\\' . $this->modelName();
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

    private function pluralItemName() {
        return Str::plural($this->itemName());
    }

    /**
     * Return translatable phrase for the item
     *
     * @param string $phrasePath key path in language file
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    private function itemTranslation($phrasePath) {
        return __($this->itemName() . "." . $phrasePath);
    }

    /**
     * Generate response
     *
     * @param array $caption settings chapter caption
     * @param mixed $content model
     * @throws \Throwable
     * @return \Illuminate\Http\JsonResponse
     */
    private function response(array $caption, $content, $type) {
        $response = new \stdClass();

        $response->caption = [
           '/settings' =>  __('settings.title')
        ] + $caption;
        $response->contentType = $type;
        switch ($type){
            case 'form':
                $response->content = $content->settingsForm()->toJson();
                break;
            case 'list':
                $response->content = $this->buildList($content);
                break;
        }


        return Response::json($response);
    }

    /**
     * Render view with the settings form
     *
     * @param integer $id item id
     * @throws \Throwable
     * @return \Illuminate\Http\JsonResponse
     */
    protected function settingsFormView($id) {
        $item = $this->itemClass()::findOrNew($id);
        $caption = [
            '/settings/' . $this->itemName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemName() . '/' . $id => $id == 0 ? $this->itemTranslation('createForm.title') : $this->itemTranslation('editForm.title')
        ];

        return $this->response($caption, $item, 'form');
    }

    protected function listView() {
        $items = $this->itemClass()::all();
        $caption = [
            '/settings/' . $this->itemName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemName() . '/list' => $this->itemTranslation('list')
        ];

        return $this->response($caption, $items, 'list');
    }

    /**
     * Build model list from the Collection
     *
     * @param Collection $items
     * @return string
     */
    abstract protected function buildList(Collection $items): string;

    protected function noAccessView() {
        return view(viewPath(Theme::active()->layout, 'noAccess'));
    }

    public function settingsHome() {
        return $this->response([], [], 'items');
    }
}
