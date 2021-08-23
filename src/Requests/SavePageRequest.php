<?php

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Just\Models\Page;
use Just\Models\System\Route;
use Just\Models\User;
use Just\Contracts\Requests\ValidateRequest;

class SavePageRequest extends FormRequest implements ValidateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool {
        $this->merge(['route' => urlencode(trim(is_null($this->route)?"":$this->route, "/"))]);

        return User::authAsAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array {
        $rules = [
            'layout' => 'required|integer|min:1',
        ];

        if(empty($this->page_id)){
            $rules['route'] = [
                Rule::unique('routes', 'route')
            ];

            if(!empty(Route::where('route', '')->first())){
                $rules['route'][] = 'required';
            }
        }
        else{
            $pageRoute = Page::find($this->page_id)->getRoute();

            $rules['route'] = [
                Rule::unique('routes', 'route')->ignore($pageRoute->id)
            ];
        }

        return $rules;
    }

    public function messages(): array {
        return [
            'route.required' => __('validation.unique', ['attribute' => 'route'])
        ];
    }
}
