<?php

namespace Just\Models\System;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBlockList
 */
class BlockList extends Model
{
    protected $table = 'blockList';

    protected $fillable = ['block', 'table'];

    protected $keyType = 'string';

    protected $primaryKey = 'block';

    public $timestamps = false;
}
