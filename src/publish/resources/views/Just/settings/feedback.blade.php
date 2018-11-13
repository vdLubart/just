<div id="dragula-container" class="dragula-list-container-{{ (isset($block->parameters()->settingsScale)?$block->parameters()->settingsScale:"100") }}">
    @foreach($block->content()->messages as $item)
    <div class="dragula-list-item" data-no="{{$item->orderNo}}" data-id="{{$item->id}}">
        <div class="thumbnail">
            <div class="caption">
                <h4>
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        {{ $item->username }}
                    </a>
                     :: {{ $item->created_at }}
                </h4>
                <p>{{$item->message}}</p>
                <br/>
                @include('Just.settings.editItem')
            </div>
        </div>
    </div>
    @endforeach
</div>