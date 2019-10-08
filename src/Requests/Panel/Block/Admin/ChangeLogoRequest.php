<?php

namespace Lubart\Just\Requests\Panel\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Lubart\Just\Structure\Panel\Block\Contracts\ValidateRequest;

class ChangeLogoRequest extends ValidateAuthRequest implements ValidateRequest {}
