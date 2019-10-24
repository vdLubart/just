<?php

namespace Lubart\Just\Models;

use Illuminate\Database\Eloquent\Model;

class BlockList extends Model
{
    protected $table = 'blockList';

    protected $fillable = ['block', 'table'];

    protected $keyType = 'string';

    protected $primaryKey = 'block';

    public $timestamps = false;
}
