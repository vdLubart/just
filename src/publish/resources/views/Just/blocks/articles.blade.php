@if(is_null($block->item()->id))
    @foreach($block->content() as $article)
    <div class="col-md-12">
        <div class="thumbnail">
            <a href="{{ url((\Config::get('isAdmin')?'admin/':'') . $block->parameter('itemRouteBase'), ['id'=>$article->slug]) }}">
                <img src="{{ '/storage/articles/'.$article->image."_12.png" }}" />
            </a>
            <div class="caption">
                <a href="{{ url((\Config::get('isAdmin')?'admin/':'') . $block->parameter('itemRouteBase'), ['id'=>$article->slug]) }}">
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
            <a href="{{ url((\Config::get('isAdmin')?'admin/':''). $block->parameter('itemRouteBase'), ['id'=>$block->item()->id]) }}">
                <img src="{{ '/storage/articles/'.$block->item()->image."_12.png" }}" />
            </a>
            <div class="caption">
                <h3>{{ $block->item()->subject }}</h3>
            </div>
            {!! $block->item()->text !!}
        </div>
    </div>
@endif