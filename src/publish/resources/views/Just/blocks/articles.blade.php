@if(is_null($block->model()->id))
    @foreach($block->content() as $article)
    <div class="col-md-12">
        <div class="thumbnail">
            <a href="{{ url((\Config::get('isAdmin')?'admin/':'').'article', ['id'=>$article->id]) }}">
                <img src="{{ '/storage/articles/'.$article->image."_12.png" }}" />
            </a>
            <div class="caption">
                <a href="{{ url((\Config::get('isAdmin')?'admin/':'').'article', ['id'=>$article->id]) }}">
                    {{ $article->subject }}
                </a>
            </div>
            <div>
                {!! $article->summary !!}
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="col-md-12">
        <div class="thumbnail">
            <a href="{{ url((\Config::get('isAdmin')?'admin/':'').'article', ['id'=>$block->model()->id]) }}">
                <img src="{{ '/storage/articles/'.$block->model()->image."_12.png" }}" />
            </a>
            <div class="caption">
                <h3>{{ $block->model()->subject }}</h3>
            </div>
            {!! $block->model()->text !!}
        </div>
    </div>
@endif