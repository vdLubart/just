<div id="simpleSlide_{{ $block->id }}" class="row">
@if(is_null($block->model()->id))
    @foreach($block->content() as $slide)
    <div class="col-md-3">
        <div class="thumbnail">
            <a href="javascript: openSettings({{ $block->id }}, {{ $slide->id }})">
                <img src="{{ '/storage/photos/'.$slide->image.".png" }}" />
            </a>
            <div class="caption">
                <a href="javascript: deleteModel({{ $block->id }}, {{ $slide->id }})" title="Delete">
                    <i class="fa fa-trash-o"></i>
                </a>
                <a href="javascript: activate({{ $slide->isActive?0:1 }}, {{ $block->id }}, {{ $slide->id }})" title="{{ $slide->isActive?"Deactivate":"Activate" }}">
                    <i class="fa fa-{{ $slide->isActive?"eye-slash":"eye" }}"></i>
                </a>
                <a href="javascript: move('up', {{ $block->id }}, {{ $slide->id }})" title="Move Up">
                    <i class="fa fa-arrow-up"></i>
                </a>
                <a href="javascript: move('down', {{ $block->id }}, {{ $slide->id }})" title="Move Down">
                    <i class="fa fa-arrow-down"></i>
                </a>
                <a href="javascript: openSettings({{ $block->id }}, {{ $slide->id }})" title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
@elseif(is_null($block->submodel()))
    <div class="col-md-6">
        <div class="thumbnail">
            <img src="{{ '/storage/photos/'.$block->model()->image.".png" }}" />
            <div class="caption">
                <p>Lorem ipsum</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div>
            <a href="javascript: openSettings({{ $block->id }}, {{ $block->model()->id }}, 0)"><i class="fa fa-plus"></i> Add new title</a>
        </div>
        @foreach($block->model()->submodels()->get() as $submodel)
        <a href="javascript: deleteBlock({{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Delete">
            <i class="fa fa-trash-o"></i>
        </a>
        <a href="javascript: activate({{ $submodel->isActive?0:1 }}, {{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="{{ $submodel->isActive?"Deactivate":"Activate" }}">
            <i class="fa fa-{{ $submodel->isActive?"eye-slash":"eye" }}"></i>
        </a>
        <a href="javascript: move('up', {{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Move Up">
            <i class="fa fa-arrow-up"></i>
        </a>
        <a href="javascript: move('down', {{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Move Down">
            <i class="fa fa-arrow-down"></i>
        </a>
        <a href="javascript: openSettings({{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Edit">
            <i class="fa fa-pencil"></i> {{ $submodel->type }}: {{$submodel->title}}
        </a> <br/>
        @endforeach
    </div>
@else
    <div class="col-md-6">
        <div class="thumbnail">
            <div class="caption">
                <p>{{ $block->submodel()->title }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div>
            <a href="javascript: openSettings({{ $block->id }}, {{ $block->model()->id }}, 0)"><i class="fa fa-plus"></i> Add new title</a>
        </div>
        @foreach($block->model()->submodels()->get() as $submodel)
        <a href="javascript: deleteBlock({{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Delete">
            <i class="fa fa-trash-o"></i>
        </a>
        <a href="javascript: activate({{ $submodel->isActive?0:1 }}, {{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="{{ $submodel->isActive?"Deactivate":"Activate" }}">
            <i class="fa fa-{{ $submodel->isActive?"eye-slash":"eye" }}"></i>
        </a>
        <a href="javascript: move('up', {{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Move Up">
            <i class="fa fa-arrow-up"></i>
        </a>
        <a href="javascript: move('down', {{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Move Down">
            <i class="fa fa-arrow-down"></i>
        </a>
        <a href="javascript: openSettings({{ $block->id }}, {{ $block->model()->id }}, {{ $submodel->id }})" title="Edit">
            <i class="fa fa-pencil"></i> {{ $submodel->type }}: {{$submodel->title}}
        </a> <br/>
        @endforeach
    </div>
@endif
</div>