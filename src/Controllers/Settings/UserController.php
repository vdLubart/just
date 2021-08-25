<?php

namespace Just\Controllers\Settings;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Just\Controllers\SettingsController;
use Just\Models\Theme;
use Just\Models\User;
use Just\Requests\InitializeUserRequest;
use Just\Requests\SavePasswordRequest;
use Just\Requests\SaveUserRequest;
use stdClass;
use Throwable;

class UserController extends SettingsController
{
    /**
     * Render view with the user settings form
     *
     * @param int $pageId page id
     * @return JsonResponse
     * @throws Throwable
     */
    public function settingsForm(int $pageId): JsonResponse {
        return $this->settingsFormView($pageId);
    }

    /**
     * Render view with user list
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function userList(): JsonResponse {
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
                'caption' => $item->name . ' :: ' . $item->email . ' (' . $item->role . ')',
                'isActive' => $item->isActive
            ];
        }

        return json_encode($list);
    }

    /**
     * Create new or update existing page
     *
     * @param SaveUserRequest $request
     * @return JsonResponse
     */
    public function setup(SaveUserRequest $request): JsonResponse {
        $this->decodeRequest($request);

        $user = User::findOrNew($request->user_id);

        return $this->setupSettingsForm($user, $request, $request->user_id, '/settings/user/list');
    }

    /**
     * Delete page
     *
     * @param InitializeUserRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(InitializeUserRequest $request): JsonResponse {
        $user = User::find($request->id);

        if(!empty($user)){
            $user->delete();
        }

        $response = new stdClass();
        $response->message = __('user.messages.success.deleted');
        $response->redirect = '/settings/user/list';

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
                'label' => __('navbar.users.add'),
                'icon' => 'plus'
            ],
            $this->itemName() . '/list' => [
                'label' => __('navbar.user.list'),
                'icon' => 'list'
            ]
        ];
        $caption = [
            '/settings/' . $this->itemName() => $this->itemTranslation('title')
        ];

        return $this->response($caption, $items, 'list');
    }

    public function activate(InitializeUserRequest $request): JsonResponse {
        return $this->userVisibility($request, true);
    }

    public function deactivate(InitializeUserRequest $request): JsonResponse {
        return $this->userVisibility($request, false);
    }

    /**
     * Change add-on visibility
     *
     * @param InitializeUserRequest $request
     * @param boolean $visibility
     * @return JsonResponse
     */
    protected function userVisibility(InitializeUserRequest $request, bool $visibility): JsonResponse {
        $user = User::find($request->id);

        if(!empty($user)){
            $user->isActive = (int)$visibility;
            $user->save();
        }

        $response = new stdClass();
        $response->message = $this->itemTranslation('messages.success.' . ($visibility ? 'activated' : 'deactivated'));
        $response->redirect = '/settings/user/list';

        return response()->json($response);
    }

    public function changePasswordForm() {
        $response = new stdClass();

        $response->contentType = 'form';

        $response->caption = [
            '/settings' =>  __('settings.title'),
            '/settings/' . $this->itemKebabName() => $this->itemTranslation('title'),
            '/settings/' . $this->itemKebabName() . '/password' => $this->itemTranslation('passwordForm.title')
        ];

        $response->parameters = [];

        $response->content = User::changePasswordForm()->toJson();

        return Response::json((array) $response);
    }

    public function changePassword(SavePasswordRequest $request) {
        $user = Auth::user();

        $user->password = bcrypt($request->new_password);
        $user->save();

        $response = new stdClass();
        $response->message = $this->itemTranslation('messages.success.passwordUpdated');
        $response->redirect = '/settings/user/list';

        return response()->json($response);
    }
}
