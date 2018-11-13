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
        
        <?php
        $author = null;
        if(!empty($block->relatedBlock('text', 'Author'))){
            $author = $block->relatedBlock('text', 'Author');
            if(!empty($author)){
                $author = $author->firstItem();
            }
        }
        elseif(!empty($block->relatedBlock('link', 'Author'))){
            
            $author = $block->relatedBlock('link', 'Author');
            if(!empty($author)){
                $author = $author->firstItem();
            }
                    
            if(!empty($author)){
                $author = $author->linkedBlock()->firstItem();
            }
        }
        ?>
        @if(!empty($author))
        <div class="thumbnail">
            <div class="caption">
                <h3>Author: {{ $author->strings->first()->value }}</h3>
            </div>
            {!! $author->text !!}
        </div>
        @endif
    </div>
@endif