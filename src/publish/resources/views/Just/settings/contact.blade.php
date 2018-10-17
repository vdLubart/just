<div id="{{ $block->model()->getTable() }}_{{ $block->id }}">
    <div id="dragula-container" class="dragula-list-container-{{ (isset($block->parameters()->settingsScale)?$block->parameters()->settingsScale:"100") }}">
        @foreach($block->model()->content() as $item)
            <div class="dragula-list-item" data-no="{{$item->orderNo}}" data-id="{{$item->id}}">
                <div class="thumbnail">
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        @if(!empty($item->title))
                        <h2>{{$item->title}}</h2>
                        @else
                        <h2>Untitled Office</h2>
                        @endif
                    </a>
                    <div class="caption">
                        @include('Just.settings.editItem')
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    
    dragItems('dragula-container', {{$block->id}});
    
</script>