<?php

namespace Just\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperEventRegistration
 */
class EventRegistration extends Model
{
    protected $fillable = [
        'event_id', 'name', 'email'
    ];

    protected $table = 'registrations';
}
