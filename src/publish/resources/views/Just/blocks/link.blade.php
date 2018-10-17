<?php
$content = $block->content();
?>

@foreach($content as $model)
    <?php
    $block = $model->linkedBlock();
    ?>
    @if(!is_null($model->linkedBlock()))
        @include($layout->name.'.blocks.'.$model->linkedBlock()->name.($layout->class!="primary"?"_".$layout->class:""))
    @endif
@endforeach