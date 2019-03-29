<?php

namespace Lubart\Just\Structure\Panel\Block;

class Twitter extends AbstractBlock
{
    
    protected $neededParameters = [
        'account'  => 'Account name',
        'widgetId' => 'Widget ID',
    ];
    
    protected $settingsTitle = 'Twitter';
    
    public function content($id = null) {
        return;
    }
    
    public function form() {
        if(is_null($this->form)){
            return;
        }
        
        return $this->form;
    }
}
