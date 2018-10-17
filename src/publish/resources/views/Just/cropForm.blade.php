<a href="javascript:" data-toggle="collapse" data-target="#{{ $block->name }}_settingsForm"><i class="fa fa-cog"></i> Setup form</a>

<div id="{{ $block->name }}_cropForm">
    <img class='cropper' src='{{ '/storage/'.$block->model()->getTable().'/'.$image.'.png' }}' />
    
    {!! Form::open([ 'url' => '/admin/settings/crop', 'method'=>'post']) !!}

    {!! Form::hidden('block_id', $block->id) !!}
    {!! Form::hidden('id', $block->model()->id) !!}
    {!! Form::hidden('x') !!}
    {!! Form::hidden('y') !!}
    {!! Form::hidden('w') !!}
    {!! Form::hidden('h') !!}
    {!! Form::hidden('img', $image) !!}
    {!! Form::submit('Crop image') !!}

    {!! Form::close() !!}
</div>
<br/><br/>

<script>
    $("#{{ $block->name }}_cropForm form").ajaxForm({
        success: function(data){
            openSettings({{ $block->id }}, 0);
        },
        error: function(data){
            console.log("error");
            console.log(data);
        }
    });
    <?php
    $parameters = json_decode($block->parameters);
    ?>
    runCroper({{ $parameters->imageWidth }}, {{ $parameters->imageHeight }});
</script>