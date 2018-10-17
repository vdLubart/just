<div id="simpleSlide_{{ $block->id }}" class="row">
    @foreach($block->content() as $slide)
    <div class="col-md-3">
        <div class="thumbnail">
            <img src="{{ '/storage/photos/'.$slide->image.".png" }}" />
            <div class="caption">
                <p>Lorem ipsum</p>
            </div>
        </div>
    </div>
    @endforeach
</div>