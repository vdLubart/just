<?php

namespace Lubart\Just\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name', 'isActive' 
    ];
    
    protected $table = 'themes';
    
    public static function active() {
        return self::where('isActive', 1)->first();
    }
}
