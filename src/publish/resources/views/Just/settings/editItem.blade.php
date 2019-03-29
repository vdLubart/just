<a href="javascript: deleteModel({{ $block->id }}, {{ $item->id }})" title="Delete">
    <i class="fa fa-trash-alt"></i>
</a>
<a href="javascript: activate({{ $item->isActive?0:1 }}, {{ $block->id }}, {{ $item->id }})" title="{{ $item->isActive?"Deactivate":"Activate" }}">
    <i class="fa fa-{{ $item->isActive?"eye-slash":"eye" }}"></i>
</a>
<a href="javascript: move('up', {{ $block->id }}, {{ $item->id }})" title="Move Up">
    <i class="fa fa-arrow-up"></i>
</a>
<a href="javascript: move('down', {{ $block->id }}, {{ $item->id }})" title="Move Down">
    <i class="fa fa-arrow-down"></i>
</a>
<a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})" title="Edit">
    <i class="fa fa-pencil-alt"></i>
</a>