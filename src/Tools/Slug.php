<?php
namespace Just\Tools;

trait Slug{

    protected $allSlugs;

    /**
     * @param $title
     * @return string
     * @throws \Exception
     */
    public function createSlug($title){
        // Normalize the title
        $slug = str_slug($title);
        // Get any that could possibly be related.
        // This cuts the queries down by doing it once.
        $this->allSlugs = $this->getRelatedSlugs($slug);
        // If we haven't used it before then we are all good.
        if (! $this->allSlugs->contains('slug', $slug)){
            return $slug;
        }

        // Just append numbers like a savage until we find not used.
        $this->incrementSlug($slug);
    }

    protected function getRelatedSlugs($slug){
        return $this->select('slug')->where('slug', 'like', $slug.'%')
            ->where('id', '<>', $this->id)
            ->where('block_id', $this->block_id)
            ->get();
    }

    protected function incrementSlug($slug, $i = 1){
        $newSlug = $slug.'-'.$i;
        if (! $this->allSlugs->contains('slug', $newSlug)) {
            return $newSlug;
        }

        return $this->incrementSlug($slug, ++$i);
    }

    public function findBySlug($slug){
        return $this->where('slug', $slug)
            ->where('block_id', $this->block_id)
            ->first();
    }

    /**
     * Confirm block is using Slug trait
     *
     * @return bool
     */
    public function haveSlug(){
        return true;
    }

}