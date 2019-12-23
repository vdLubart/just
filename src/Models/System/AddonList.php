<?php

namespace Just\Models\System;

use Illuminate\Database\Eloquent\Model;

class AddonList extends Model {

    protected $table = 'addonList';

    protected $fillable = ['addon', 'table'];

    protected $keyType = 'string';

    protected $primaryKey = 'addon';

    public $timestamps = false;

}
