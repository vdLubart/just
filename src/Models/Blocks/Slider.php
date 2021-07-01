<?php

namespace Just\Models\Blocks;

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
