@if(!empty($block->model()->relatedBlocks))
    @foreach($block->model()->relatedBlocks as $relBlock)
    <div class="col-md-{{ $block->width }}">
        <div class="thumbnail">
            <div class="caption">
                <a href="javascript: openSettings({{ $relBlock->id }}, 0)">
                    {{ $relBlock->title }} ({{ $relBlock->type }})
                </a>
                <br/>
                <a href="javascript: deleteModel({{ $relBlock->id }}, 0)" title="Delete">
                    <i class="fa fa-trash-alt"></i>
                </a>
                <a href="javascript: activate({{ $relBlock->isActive?0:1 }}, {{ $relBlock->id }}, 0)" title="{{ $relBlock->isActive?"Deactivate":"Activate" }}">
                    <i class="fa fa-{{ $relBlock->isActive?"eye-slash":"eye" }}"></i>
                </a>
                <a href="javascript: move('up', {{ $relBlock->id }}, 0)" title="Move Up">
                    <i class="fa fa-arrow-up"></i>
                </a>
                <a href="javascript: move('down', {{ $relBlock->id }}, 0)" title="Move Down">
                    <i class="fa fa-arrow-down"></i>
                </a>
                <a href="javascript: openSettings({{ $relBlock->id }}, 0)" title="Edit">
                    <i class="fa fa-pencil-alt"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
@endif