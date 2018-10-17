<?php

/**
 * Manage with custom validators
 *
 * @author Viacheslav Dymarchuk
 */
namespace Lubart\Just\Validators;

use Illuminate\Validation\Validator;

class ValidatorExtended extends Validator{
    
    public function __construct( $translator, $data, $rules, $messages = array(), $customAttributes = array() ) {
        parent::__construct( $translator, $data, $rules, $messages, $customAttributes );
    }
    
}
