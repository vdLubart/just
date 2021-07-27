<?php

namespace Just\Contracts\Requests;


use Just\Contracts\Requests\BlockItems\ValidateArticleRequest;

/**
 * Interface ValidateRequest
 * @package Just\Contracts\Requests
 *
 * @property int $id
 * @property int $block_id
 */
interface ValidateRequest extends ValidateArticleRequest {

    public function authorize();

    public function rules();

    public function messages();

    public function file($key = null, $default = null);

}
