<a href="javascript:" data-toggle="collapse" data-target="#{{ $block->type }}_settingsForm"><i class="fa fa-cog"></i> Setup form</a>

<div id="{{ $block->type }}_cropForm">
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
    $("#{{ $block->type }}_cropForm form").ajaxForm({
        beforeSerialize: function(form, options) {
            $("input[type=submit]").attr('disabled', 'disabled');
        },
        success: function(data){
            openSettings({{ $block->id }}, 0);
        },
        error: function(data){
            $("input[type=submit]").removeAttr('disabled');
            console.log(data);
            showErrors(data);
        }
    });
    <?php
    $parameters = json_decode($block->parameters);
    $dev = explode(":", $parameters->cropDimentions);
    $cropDimentions = $dev[0]/$dev[1];
    $cropWidth = $block->layout()->width;
    $cropHeight = $cropWidth/$cropDimentions;
    ?>
    runCroper({{ $cropWidth }}, {{ $cropHeight }});
</script>