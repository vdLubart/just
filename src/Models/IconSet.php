<?php

namespace Lubart\Just\Models;

use Illuminate\Database\Eloquent\Model;

class IconSet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'tag', 'class',
    ];

    protected $table = 'iconsets';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public function icons() {
        return $this->hasMany(Icon::class);
    }
    
    public static function getList() {
        return self::all()->mapWithKeys(function($item){
            return [$item->id => $item->title];
        })->toArray();
    }
}
