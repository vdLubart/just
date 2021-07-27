<?php

namespace Just\Models\Blocks\Contracts;


interface ValidateRequest {

    public function authorize();

    public function rules();

    public function messages();

    public function file($key = null, $default = null);

}
