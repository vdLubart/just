<?php

namespace Just\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Just\Models\Theme;
use stdClass;
use Throwable;

abstract class SettingsController extends Controller
{
    public function __construct() {
        parent::__construct();

        Config::set('isAdmin', true);
    }

    /**
     * Return class name
     *
     * @return string
     */
    private function itemClass(): string {
        return '\\Just\\Models\\' . $this->modelName();
    }

    /**
     * Return model name
     *
     * @return string
     */
    private function modelName(): string {
        return Str::substr(Arr::last(explode('\\', get_class($this))), 0, -10);
    }

    /**
     * Return item name
     *
     * @return string
     */
    protected function itemName(): string {
        return lcfirst($this->modelName());
    }

    /**
     * Return item name in kebab case
     *
     * @return string
     */
    protected function itemKebabName(){
        return Str::kebab($this->itemName());
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
    protected function itemTranslation($phrasePath, $replace = []) {
        return __($this->itemName() . "." . $phrasePath, $replace);
    }

    /**
     * Generate response
     *
     * @param array $caption settings chapter caption
     * @param mixed $content model
     * @param $type
     * @param array $parameters additional parameters added to the response
     * @return JsonResponse
     */
    protected function response(array $caption, $content, $type, array $parameters = []): JsonResponse {
        $response = new stdClass();

        $response->caption = [
           '/settings' =>  __('settings.title')
        ] + $caption;
        $response->contentType = $type;

        $response->parameters = [];

        foreach ($parameters as $key=>$parameter){
            $response->parameters[$key] = $parameter;
        }

        switch ($type){
            case 'form':
                $response->content = $content->itemForm()->toJson();
                break;
            case 'items':
                $response->content = $this->buildItemList($content);
                break;
            case 'list':
            case 'menu':
                $response->content = json_encode($content);
                break;
        }

        return Response::json((array) $response);
    }

    /**
     * Render view with the settings form
     *
     * @param int $id item id
     * @param array $itemParams predefined item data
     * @param array $caption predefined captions
     * @param array $responseParameters additional parameters added to the response
     * @return JsonResponse
     * @throws Throwable
     */
    protected function settingsFormView(int $id, array $itemParams = [], array $caption = [], array $responseParameters = []): JsonResponse {
        $item = $this->itemClass()::findOrNew($id);

        foreach ($itemParams as $key=>$param){
            $item->$key = $param;
        }

        if(empty($caption)){
            $caption = [
                '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
                '/settings/' . $this->itemKebabName() . '/' . $id => $id == 0 ? $this->itemTranslation('createForm.title') : $this->itemTranslation('editForm.title', [$this->itemName() => $item->itemCaption()])
            ];
        }

        return $this->response($caption, $item, 'form', $responseParameters);
    }

    protected function addOnSettingsFormView($id, $addOn) {
        $addOnClass = '\\Just\\Models\\Blocks\\AddOns\\' . ucfirst(Str::plural($addOn));
        $item = $addOnClass::findOrNew($id);
        $caption = [
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemKebabName() . '/' . $addOn => $this->itemTranslation($addOn . '.title'),
            '/settings/' . $this->itemKebabName() . '/' . $addOn . '/' . $id => $id == 0 ? $this->itemTranslation($addOn . '.createForm.title') : $this->itemTranslation($addOn . '.editForm.title', [$this->itemName() => $item->itemCaption()])
        ];

        return $this->response($caption, $item, 'form');
    }


    /**
     * @param array $caption
     * @param array $where
     * @return JsonResponse
     * @throws Throwable
     */
    protected function listView(array $caption = [], array $where = []): JsonResponse {
        if($this->itemName() === 'block'){
            $items = $this->itemClass()::where('panelLocation', $where['panelLocation'])->orderBy('orderNo')->get();
        }
        elseif($this->itemName() === 'addOn'){
            $items = $this->itemClass()::orderBy('block_id')->orderBy('orderNo')->get();
        }
        else {
            $items = $this->itemClass()::orderBy('orderNo')->get();
        }

        if(empty($caption)){
            $caption = [
                '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
                '/settings/' . $this->itemKebabName() . '/list' => $this->itemTranslation('list')
            ];
        }

        return $this->response($caption, $items, 'items');
    }

    /**
     * @param string $addOn
     * @return JsonResponse
     */
    protected function addOnListView(string $addOn): JsonResponse {
        $addOnClass = '\\Just\\Models\\Blocks\\AddOns\\' . ucfirst(Str::plural($addOn));
        $items = $addOnClass::orderBy('orderNo')->get();
        $caption = [
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemKebabName() . '/' . $addOn => $this->itemTranslation($addOn . '.title'),
            '/settings/' . $this->itemKebabName() . '/' . $addOn . '/list' => $this->itemTranslation($addOn . '.list')
        ];

        return $this->response($caption, $items, 'items');
    }

    /**
     * Build model list from the Collection
     *
     * @param Collection $items
     * @return string
     */
    abstract protected function buildItemList(Collection $items): string;

    protected function noAccessView() {
        return view(viewPath(Theme::active()->layout, 'noAccess'));
    }

    public function settingsHome() {
        $items = [];
        if(Auth::user()->role == "master"){
            $items['layout'] = [
                'label' => __('navbar.layouts.top'),
                'icon' => 'paint-brush'
            ];
        }

        $items += [
            'page' => [
                'label' => __('navbar.pages.top'),
                'icon' => 'sitemap'
            ]
        ];

        if(Auth::user()->role == "master"){
            $items['addon'] = [
                'label' => __('navbar.addOns.top'),
                'icon' => 'puzzle-piece'
            ];
            $items['user'] = [
                'label' => __('navbar.users.top'),
                'icon' => 'users'
            ];
        }

        return $this->response([], $items, 'list');
    }

    /**
     * @param $item
     * @param $request
     * @param int|null $id
     * @param string|null $redirect
     * @return JsonResponse
     */
    protected function setupSettingsForm($item, $request, ?int $id = 0, ?string $redirect = null): JsonResponse {
        $item->handleSettingsForm($request);

        $response = new stdClass();
        $response->message = $this->itemTranslation('messages.success.' . ($id == 0 ? 'created' : 'updated'));
        $response->redirect = $redirect;

        return response()->json(json_encode($response));
    }

    protected function decodeRequest(&$request) {
        $decodedRequest = [];
        foreach ($request->all() as $key=>$item){
            $decodedRequest[$key] = json_decode($item, true) ?? $item;
        }

        $request->replace($decodedRequest);
    }
}
