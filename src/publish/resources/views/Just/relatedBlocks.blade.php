@if(!empty($block->item()->relatedBlocks))
    @foreach($block->item()->relatedBlocks as $relBlock)
    <div class="col-md-{{ $block->width }}">
        <div class="thumbnail">
            <div class="caption">
                <a href="javascript: openSettings({{ $relBlock->id }}, 0)">
                    {{ $relBlock->title }} ({{ $relBlock->type }})
                </a>
                <br/>
                <a href="javascript: deleteModel({{ $relBlock->id }}, 0)" title="@lang('settings.actions.delete')">
                    <i class="fa fa-trash-alt"></i>
                </a>
                <a href="javascript: activate({{ $relBlock->isActive?0:1 }}, {{ $relBlock->id }}, 0)" title="{{ $relBlock->isActive?"@lang('settings.actions.deactivate')":"@lang('settings.actions.activate')" }}">
                    <i class="fa fa-{{ $relBlock->isActive?"eye-slash":"eye" }}"></i>
                </a>
                <a href="javascript: move('up', {{ $relBlock->id }}, 0)" title="@lang('settings.actions.moveUp')">
                    <i class="fa fa-arrow-up"></i>
                </a>
                <a href="javascript: move('down', {{ $relBlock->id }}, 0)" title="@langsettings.actions.moveDown)">
                    <i class="fa fa-arrow-down"></i>
                </a>
                <a href="javascript: openSettings({{ $relBlock->id }}, 0)" title="@lang('settings.actions.edit')">
                    <i class="fa fa-pencil-alt"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
@endif