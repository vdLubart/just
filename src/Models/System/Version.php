<?php

namespace Just\Models\System;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of Version
 *
 * @author lubart
 */
class Version extends Model{
    
    protected $table = 'version';
    
    protected $fillable = ['version', 'comment'];
    
    /**
     * Return current installed in the DB version of Just!
     * 
     * @return string
     */
    public static function current(){
        if(self::count() > 0){
            return self::orderBy('created_at', 'desc')->limit(1)->first()->version;
        }
        
        return '0.0.0.1';
    }
    
    /**
     * Return current installed Just! version through composer.
     * This version can be higher than specified in DB, in that case
     * just:update artisan command should be run
     * 
     * @return string
     */
    public static function inComposer(){
        return justVersion();
    }
    
    /**
     * Return should version in DB be updated or not. This method can be used
     * to define should just:update command be run
     * 
     * @return boolean
     */
    public static function shouldBeUpdated(){
        return version_compare(self::current(), self::inComposer(), '<');
    }
}
