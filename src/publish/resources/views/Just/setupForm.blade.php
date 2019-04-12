<div id="{{ $block->type }}_setupForm">
    {!! $block->setupForm()->render() !!}
</div>
<br/><br/>

<script>
    $("#{{ $block->type }}_setupForm form").ajaxForm(function(data){
        openSettings({{ $block->id }}, 0);
    });
</script>