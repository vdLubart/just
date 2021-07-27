<?php
/**
 * Created by PhpStorm.
 * User: lubart
 * Date: 20.09.19
 * Time: 18:27
 */

namespace Just\Contracts;

use Illuminate\Http\Request;
use Lubart\Form\Form;

interface ContainsPublicForm {

    /**
     * Return form available on the public side
     *
     * @return Form
     */
    public function publicForm(): Form;

    /**
     * Handle form from the public side
     *
     * @param Request $request
     */
    public function handlePublicForm(Request $request);

}
