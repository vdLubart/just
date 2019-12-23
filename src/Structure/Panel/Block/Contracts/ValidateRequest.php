<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 23.09.19
 * Time: 7:26
 */

namespace Just\Structure\Panel\Block\Contracts;


interface ValidateRequest {

    public function authorize();

    public function rules();

    public function messages();

}