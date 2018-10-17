<div id="gallery_{{ $block->id }}" class="row">
@if(is_null($block->model()->id))
    
    @foreach($block->content() as $logo)
    <div class="col-md-3">
        <div class="thumbnail">
            <a href="javascript: openSettings({{ $block->id }}, {{ $logo->id }})">
                <img src="{{ '/storage/'.$block->model()->getTable().'/'. $logo->image . '_3.png' }}" />
            </a>
            <div class="caption"
                <a href="javascript: openSettings({{ $block->id }}, {{ $logo->id }})">
                    {{ $logo->caption }}
                </a><br/>
                <a href="javascript: deleteModel({{ $block->id }}, {{ $logo->id }})" title="Delete">
                    <i class="fa fa-trash-o"></i>
                </a>
                <a href="javascript: activate({{ $logo->isActive?0:1 }}, {{ $block->id }}, {{ $logo->id }})" title="{{ $logo->isActive?"Deactivate":"Activate" }}">
                    <i class="fa fa-{{ $logo->isActive?"eye-slash":"eye" }}"></i>
                </a>
                <a href="javascript: move('up', {{ $block->id }}, {{ $logo->id }})" title="Move Up">
                    <i class="fa fa-arrow-up"></i>
                </a>
                <a href="javascript: move('down', {{ $block->id }}, {{ $logo->id }})" title="Move Down">
                    <i class="fa fa-arrow-down"></i>
                </a>
                <a href="javascript: openSettings({{ $block->id }}, {{ $logo->id }})" title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
@endif
</div>