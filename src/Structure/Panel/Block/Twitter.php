<?php

namespace Lubart\Just\Structure\Panel\Block;

use Illuminate\Http\Request;

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
        return $this->form;
    }
    
    public function handleForm(Request $request) {
        return;
    }
}
