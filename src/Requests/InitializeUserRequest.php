<?php
/**
 * @author Viacheslav Dymarchuk
 */

namespace Just\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Just\Contracts\Requests\ValidateRequest;
use Just\Models\User;

class InitializeUserRequest extends FormRequest implements ValidateRequest{

    public function authorize(): bool {
        return User::authAsMaster();
    }

    public function rules(): array {
        return [
            'id' => 'required|integer|exists:users',
        ];
    }
}
