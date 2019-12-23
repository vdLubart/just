<?php

namespace Just\Models\System;

use Illuminate\Database\Eloquent\Model;
use Just\Models\Page;

class Route extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route', 'type', 'block_id', 'action' 
    ];
    
    protected $table = 'routes';
    
    public static function findByUrl($url) {
        return self::where('route', trim(preg_replace('/^\/?admin/', '', $url), '/'))->first();
    }
    
    public function page() {
        return $this->belongsTo(Page::class, 'route', 'route');
    }
}