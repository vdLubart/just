<?php
$form = $block->setupForm();
?>
<div id="{{ $block->name }}_setupForm">
    @include('Just.form')
</div>
<br/><br/>

<script>
    $("#{{ $block->name }}_setupForm form").ajaxForm(function(data){
        openSettings({{ $block->id }}, {{ !is_null($block->submodel())? $block->model()->id : 0 }});
    });
</script>