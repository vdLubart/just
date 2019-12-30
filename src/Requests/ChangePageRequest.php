<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Just\Models\Page;
use Just\Models\System\Route;
use Just\Models\User;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class ChangePageRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->merge(['route' => urlencode(trim(is_null($this->route)?"":$this->route, "/"))]);

        return User::authAsAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'layout' => 'required|integer|min:1',
        ];

        if(empty($this->page_id)){
            $rules['route'] = [
                Rule::unique('routes', 'route')
            ];
        }
        else{
            $pageRoute = Page::find($this->page_id)->getRoute();

            $rules['route'] = [
                Rule::unique('routes', 'route')->ignore($pageRoute->id)
            ];
        }

        if(!empty(Route::where('route', '')->first())){
            $rules['route'][] = 'required';
        }

        return $rules;
    }

    public function messages() {
        return [
            'route.required' => __('validation.unique', ['attribute' => 'route'])
        ];
    }
}
