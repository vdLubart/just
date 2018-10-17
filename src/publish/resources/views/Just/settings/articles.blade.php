<div id="articles_{{ $block->id }}" class="row">
@if(is_null($block->model()->id))
    @foreach($block->content() as $article)
    <div class="col-md-3">
        <div class="thumbnail">
            <a href="javascript: openSettings({{ $block->id }}, {{ $article->id }})">
                <img src="{{ '/storage/articles/'.$article->image."_3.png" }}" />
            </a>
            <div class="caption">
                <a href="javascript: openSettings({{ $block->id }}, {{ $article->id }})">
                    {{ $article->subject }}
                </a><br/>
                <a href="javascript: deleteModel({{ $block->id }}, {{ $article->id }})" title="Delete">
                    <i class="fa fa-trash-o"></i>
                </a>
                <a href="javascript: activate({{ $article->isActive?0:1 }}, {{ $block->id }}, {{ $article->id }})" title="{{ $article->isActive?"Deactivate":"Activate" }}">
                    <i class="fa fa-{{ $article->isActive?"eye-slash":"eye" }}"></i>
                </a>
                <a href="javascript: move('up', {{ $block->id }}, {{ $article->id }})" title="Move Up">
                    <i class="fa fa-arrow-up"></i>
                </a>
                <a href="javascript: move('down', {{ $block->id }}, {{ $article->id }})" title="Move Down">
                    <i class="fa fa-arrow-down"></i>
                </a>
                <a href="javascript: openSettings({{ $block->id }}, {{ $article->id }})" title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
@endif
</div>

<script>
    $(document).ready(function(){
        CKEDITOR.replace('summary');
        CKEDITOR.replace('text');
    });
</script>