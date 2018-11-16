<div id="{{ $block->name }}_setupForm">
    {!! $block->setupForm()->render() !!}
</div>
<br/><br/>

<script>
    $("#{{ $block->name }}_setupForm form").ajaxForm(function(data){
        openSettings({{ $block->id }}, 0);
    });
</script>