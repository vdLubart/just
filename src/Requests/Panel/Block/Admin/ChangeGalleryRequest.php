<?php

namespace Just\Requests\Panel\Block\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Just\Structure\Panel\Block\Contracts\ValidateRequest;

class ChangeGalleryRequest extends ValidateAuthRequest implements ValidateRequest {}
