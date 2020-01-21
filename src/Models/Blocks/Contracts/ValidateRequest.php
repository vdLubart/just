<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 23.09.19
 * Time: 7:26
 */

namespace Just\Models\Blocks\Contracts;


interface ValidateRequest {

    public function authorize();

    public function rules();

    public function messages();

}