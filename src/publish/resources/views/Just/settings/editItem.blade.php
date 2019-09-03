<a href="javascript: deleteModel({{ $block->id }}, {{ $item->id }})" title="@lang('settings.actions.delete')">
    <i class="fa fa-trash-alt"></i>
</a>
<a href="javascript: activate({{ $item->isActive?0:1 }}, {{ $block->id }}, {{ $item->id }})" title="{{ $item->isActive?"@lang('settings.actions.deactivate')":"@lang('settings.actions.activate')" }}">
    <i class="fa fa-{{ $item->isActive?"eye-slash":"eye" }}"></i>
</a>
<a href="javascript: move('up', {{ $block->id }}, {{ $item->id }})" title="@lang('settings.actions.moveUp')">
    <i class="fa fa-arrow-up"></i>
</a>
<a href="javascript: move('down', {{ $block->id }}, {{ $item->id }})" title="@lang('settings.actions.moveDown')">
    <i class="fa fa-arrow-down"></i>
</a>
<a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})" title="@lang('settings.actions.edit')">
    <i class="fa fa-pencil-alt"></i>
</a>