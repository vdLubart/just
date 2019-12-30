<?php

namespace Just\Models;

use Illuminate\Database\Eloquent\Model;

class Panel extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location',  'layout_id', 'type', 'orderNo', 
    ];
    
    protected $table = 'panels';
    
    /**
     * Current page;
     * 
     * @var Page $page
     */
    protected $page;
    
    /**
     * Get all blocks related to the current panel on the current page
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function blocks() {
        $blocks = $this->hasMany(Block::class, "panelLocation", "location")
                ->orderBy("orderNo");
        if($this->type == "dynamic"){
            $blocks = $blocks->where('page_id', $this->page->id);
        }
        else{
            $blocks = $blocks->whereNull('page_id');
        }

        if(!\Config::get('isAdmin')){
            $blocks = $blocks->where('isActive', 1);
        }
        $blocks = $blocks->get();
        
        foreach($blocks as $block){
            $block->specify();
        }
        
        return $blocks;
    }
    
    /**
     * Current layout
     * 
     * @return Layout
     */
    public function layout() {
        return $this->belongsTo(Layout::class, 'layout_id','id');
    }
    
    /**
     * Set current page
     * 
     * @param Page $page
     * @return Panel
     */
    public function setPage($page) {
        $this->page = $page;
        
        return $this;
    }
    
    /**
     * Current page
     * 
     * @return Page
     */
    public function page() {
        return $this->page;
    }
}
