<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Requests\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Models\Blocks\Contracts\ValidateRequest;
use Just\Models\User;

class InitializeBlockRequest extends FormRequest implements ValidateRequest{


    public function authorize() {
        return User::authAsAdmin();
    }

    public function rules() {
        return [
            'id' => 'required|integer|exists:blocks',
        ];
    }
}