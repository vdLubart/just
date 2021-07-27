<?php

namespace Just\Requests\Block\Admin;

use Illuminate\Validation\Rule;
use Just\Contracts\Requests\ValidateRequest;

class ChangeGalleryRequest extends ValidateAuthRequest implements ValidateRequest {

    public function rules(): array {
        return [
            'id' => 'nullable|integer|exists:photos',
            'block_id' => 'required|integer|exists:blocks,id',
            'image' => [
                Rule::requiredIf(empty($this->id) and empty($this->video) and empty($this->externalUrl)),
                'mimetypes:image/jpeg,image/png',
            ],
            'video' => [
                Rule::requiredIf(empty($this->id) and empty($this->image) and empty($this->externalUrl)),
                'mimetypes:video/mpeg',
            ],
            'externalUrl' => [
                Rule::requiredIf(empty($this->id) and empty($this->image) and empty($this->video)),
                'string',
                'max:1000'
            ]
        ];
    }

}
