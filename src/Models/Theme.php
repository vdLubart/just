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
    
    public function layout(){
        return $this->belongsTo(Layout::class, 'name', 'name')
                ->where('class', 'primary');
    }
    
    public static function setActive($layout){
        Theme::where('isActive', '=', 1)->update(['isActive' => 0]);
        
        Theme::where('name', $layout)->update(['isActive' => 1]);
    }
}
