<?php

namespace Lubart\Just\Structure\Panel\Block;

// TODO: create script for different types of sliders
class Slider extends Gallery
{
    
    public function form() {
        $this->form = parent::form();
        
        if(is_null($this->form)){
            return;
        }
        
        $this->form->useJSFile('/js/blocks/slider/settingsForm.js');
        
        return $this->form;
    }
}
