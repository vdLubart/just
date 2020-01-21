<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 20.09.19
 * Time: 18:27
 */

namespace Just\Models\Blocks\Contracts;

use Illuminate\Http\Request;
use Lubart\Form\Form;

interface ContainsPublicForm {

    /**
     * Return form available on the public side
     *
     * @return Form
     */
    public function publicForm();

    /**
     * Handle form from the public side
     *
     * @param Request $request
     * @return mixed
     */
    public function handlePublicForm(Request $request);

}