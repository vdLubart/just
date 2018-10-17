<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            Settings :: 
            @if(isset($parentBlock) and !is_null($parentBlock)) <a href="javascript: openSettings({{ $parentBlock->id }}, 0)">{{ $parentBlock->title }} </a> :. <a href="javascript: openSettings({{ $parentBlock->id }}, {{ $parentBlock->model()->id }})">{{ $parentBlock->model()->settingsTitle() }}</a> :. @endif
            @if(!is_null($block->model()->id)) <a href="javascript: openSettings({{ $block->id }}, 0)">{{ $block->title }}</a> @else {{ $block->title }} @endif
            @if(is_null($block->submodel()))
                @if(!is_null($block->model()->id)) :. {{ $block->model()->settingsTitle() }} @endif
            @else
                :. <a href="javascript: openSettings({{ $block->id }}, {{ $block->model()->id }})">{{ $block->model()->settingsTitle() }}</a>
            @endif
            @if(!is_null($block->submodel())) :. {{ $block->submodel()->settingsTitle() }} @endif
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>