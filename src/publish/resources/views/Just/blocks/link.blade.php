<?php
$content = $block->content();
?>

@foreach($content as $model)
    <?php
    $block = $model->linkedBlock();
    ?>
    @if($layout->class != "primary" and file_exists(resource_path('views/'.$layout->name.'/blocks/'.$block->name.'_'.$layout->class.'.blade.php')))
        @include($layout->name.'.blocks.'. $block->name . '_' . $layout->class)
    @elseif($block->layoutClass != "primary" and file_exists(resource_path('views/'.$layout->name.'/blocks/'.$block->name.'_'.$block->layoutClass.'.blade.php')))
        @include($layout->name.'.blocks.'. $block->name . '_' . $block->layoutClass)
    @else
        @include($layout->name.'.blocks.'. $block->name)
    @endif
@endforeach