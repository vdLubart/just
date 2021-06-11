<?php

namespace Just\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    protected Page $page;

    /**
     * Get all blocks related to the current panel on the current page
     *
     * @return Collection
     */
    public function blocks(): Collection {
        $blocks = $this->hasMany(Block::class, "panelLocation", "location")
                ->orderBy("orderNo");
        if($this->type == "dynamic"){
            $blocks = $blocks->where('page_id', $this->page->id);
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
     * @return BelongsTo
     */
    public function layout(): BelongsTo {
        return $this->belongsTo(Layout::class, 'layout_id','id');
    }

    /**
     * Set current page
     *
     * @param Page $page
     * @return Panel
     */
    public function setPage(Page $page): Panel {
        $this->page = $page;

        return $this;
    }

    /**
     * Current page
     *
     * @return Page
     */
    public function page(): Page {
        return $this->page;
    }
}
