<div id="simpleSlide_{{ $block->id }}" class="row">
    @foreach($block->content() as $slide)
    <div class="col-md-12">
        <div class="thumbnail">
            <img src="{{ '/storage/photos/'.$slide->image.".png" }}" />
        </div>
    </div>
    @endforeach
</div>