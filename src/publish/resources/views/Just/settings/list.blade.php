<div id="{{ $block->model()->getTable() }}_{{ $block->id }}">
@if(is_null($block->model()->id))
    @if($block->categories()->first())
    <div class="button-group filter-button-group">
        <button data-filter="*" class="btn btn-default btn-danger">Show all</button>
        @foreach($block->categories()->first()->values()->get() as $cat)
            <button data-filter=".{{ $cat->value }}" class="btn btn-default">{{ $cat->name }}</button>
        @endforeach
    </div>
    @endif
    
    <div id="dragula-container" class="dragula-list-container-{{ (isset($block->parameters()->settingsScale)?$block->parameters()->settingsScale:"100") }}">
        @foreach($block->content() as $item)
        <div class="dragula-list-item  {{ ($block->categories()->first()?$item->categories->first()->value:"") }}" data-no="{{$item->orderNo}}" data-id="{{$item->id}}">
            <div class="thumbnail">
                @if( in_array('image', array_keys($item->getAttributes())) )
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        <img src="{{ '/storage/'.$block->model()->getTable().'/'. $item->image . '_3.png' }}" />
                    </a>
                @elseif(isset($item->text))
                    {!! $item->text !!}
                @endif
                <div class="caption">
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        {{ $item->caption }}
                    </a><br/>
                    @include('Just.settings.editItem')
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
</div>

<script>
    $(document).ready(function(){
        
        dragItems('dragula-container', {{$block->id}});
        
        $('.filter-button-group button').on('click', function() {
            var filterValue = $(this).attr('data-filter');
            $('.dragula-list-item').css('display', 'none');
            $('.dragula-list-item'+filterValue).css('display', 'block');
            $('.filter-button-group button').removeClass('btn-danger');
            $(this).addClass('btn-danger');
        });
    });
</script>