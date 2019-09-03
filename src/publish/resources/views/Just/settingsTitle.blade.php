<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            <a href="javascript: openPanelSettings({{ @$panel->page()->id }}, '{{ $panel->location }}')">@lang('settings.title')</a> ::
            @if(isset($parentBlock) and !is_null($parentBlock)) <a href="javascript: openSettings({{ $parentBlock->id }}, 0)">{{ $parentBlock->title }} </a> :. <a href="javascript: openSettings({{ $parentBlock->id }}, {{ $parentBlock->model()->id }})">{{ $parentBlock->model()->settingsTitle() }}</a> :. @endif
            @if(!is_null($block->model()->id)) <a href="javascript: openSettings({{ $block->id }}, 0)">{{ $block->title }}</a> :. {{ $block->model()->settingsTitle() }} @else {{ $block->title }} @endif
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-times"></i></a>
    </div>
</div>