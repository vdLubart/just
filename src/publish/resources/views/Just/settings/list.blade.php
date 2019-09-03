<div class="thumbnail">
    @if( in_array('image', array_keys($item->getAttributes())) )
    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
        @if(file_exists('/storage/'.$block->model()->getTable().'/'. $item->image . '_'.round($zoom/100*3).'.png'))
        <img src="{{ '/storage/'.$block->model()->getTable().'/'. $item->image . '_'.round($zoom/100*3).'.png' }}" />
        @else
        <img src="{{ '/storage/'.$block->model()->getTable().'/'. $item->image . '.png' }}" />
        @endif
    </a>
    @elseif(isset($item->icon))
    <h1 class='featureItem'>
        <?php
        $iconSet = $item->icon->iconSet;
        echo '<' . $iconSet->tag . ' class="' . $iconSet->class . ' ' . $item->icon->class . '"></' . $iconSet->tag . '>';
        ?>
    </h1>
    @elseif(isset($item->text))
    {!! substr($item->text, 0, 200) !!}
    @elseif(isset($item->linkedBlock_id))
    <h4>{!! $item->linkedBlock()->title. '('.$item->linkedBlock()->type.') at '.@$item->linkedBlock()->page()->title !!} </h4>
    @endif
    <div class="caption">
        <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
            {{ $item->caption ?? $item->subject ?? $item->title ?? @lang('block.untitled') }}
        </a><br/>
        @include('Just.settings.editItem')
    </div>
</div>