<?php

namespace Lubart\Just\Models\System;

use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'icon_set_id', 'class',
    ];

    protected $table = 'icons';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public function iconSet() {
        return $this->belongsTo(IconSet::class);
    }
}
