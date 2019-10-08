<?php
    $zoom = 100;
    if(isset($block->parameters->itemsInRow)){
        $zoom = (int)round(@$block->parameters->itemsInRow / 3 * 100);
    }
    else{
        $zoom = (isset($block->parameters->settingsScale)?$block->parameters->settingsScale:"100");
    }
?>

@if(is_null($block->model()->id))
    @include('Just.categoryFilter')
@endif


<h3>Future events</h3>

@if($block->content()->isEmpty())
    There is no event planned yet
@endif

<div class="dragula-list-container-{{ $zoom }}">
    @foreach($block->content() as $item)
        <div class="{{ ($block->categories()->first()?@$item->categories->first()->value:"") }}" data-no="{{$item->orderNo}}" data-id="{{$item->id}}">

            <div class="thumbnail">
                @if( in_array('image', array_keys($item->getAttributes()))  and !empty($item->image))
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        @if(file_exists('/storage/'.$block->model()->getTable().'/'. $item->image . '_'.round($zoom/100*3).'.png'))
                            <img src="{{ '/storage/'.$block->model()->getTable().'/'. $item->image . '_'.round($zoom/100*3).'.png' }}" />
                        @else
                            <img src="{{ '/storage/'.$block->model()->getTable().'/'. $item->image . '.png' }}" />
                        @endif
                    </a>
                @elseif(isset($item->text))
                    {!! substr($item->text, 0, 200) !!}
                @elseif(isset($item->linkedBlock_id))
                    <h4>{!! $item->linkedBlock()->title. '('.$item->linkedBlock()->type.') at '.@$item->linkedBlock()->page()->title !!} </h4>
                @endif
                <div class="caption">
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->start_date)->format('d.m.Y') }}<br>
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        {{ $item->caption ?? $item->subject ?? $item->title ?? 'Untitled' }}
                    </a><br/>

                    <a href="javascript: deleteModel({{ $block->id }}, {{ $item->id }})" title="Delete">
                        <i class="fa fa-trash-alt"></i>
                    </a>
                    <a href="javascript: activate({{ $item->isActive?0:1 }}, {{ $block->id }}, {{ $item->id }})" title="{{ $item->isActive?"Deactivate":"Activate" }}">
                        <i class="fa fa-{{ $item->isActive?"eye-slash":"eye" }}"></i>
                    </a>
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})" title="Edit">
                        <i class="fa fa-pencil-alt"></i>
                    </a>

                </div>
            </div>

        </div>
    @endforeach
</div>


<h3>Past events</h3>

@if($block->model()->pastEvents()->isEmpty())
    There is no event held in the past
@endif

<div class="dragula-list-container-{{ $zoom }}">
    @foreach($block->model()->pastEvents() as $item)
        <div class="{{ ($block->categories()->first()?@$item->categories->first()->value:"") }}" data-no="{{$item->orderNo}}" data-id="{{$item->id}}">

            <div class="thumbnail">
                @if( in_array('image', array_keys($item->getAttributes())) and !empty($item->image))
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        @if(file_exists('/storage/'.$block->model()->getTable().'/'. $item->image . '_'.round($zoom/100*3).'.png'))
                            <img src="{{ '/storage/'.$block->model()->getTable().'/'. $item->image . '_'.round($zoom/100*3).'.png' }}" />
                        @else
                            <img src="{{ '/storage/'.$block->model()->getTable().'/'. $item->image . '.png' }}" />
                        @endif
                    </a>
                @elseif(isset($item->text))
                    {!! substr($item->text, 0, 200) !!}
                @elseif(isset($item->linkedBlock_id))
                    <h4>{!! $item->linkedBlock()->title. '('.$item->linkedBlock()->type.') at '.@$item->linkedBlock()->page()->title !!} </h4>
                @endif
                <div class="caption">
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->start_date)->format('d.m.Y') }}<br>
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})">
                        {{ $item->caption ?? $item->subject ?? $item->title ?? 'Untitled' }}
                    </a><br/>

                    <a href="javascript: deleteModel({{ $block->id }}, {{ $item->id }})" title="Delete">
                        <i class="fa fa-trash-alt"></i>
                    </a>
                    <a href="javascript: activate({{ $item->isActive?0:1 }}, {{ $block->id }}, {{ $item->id }})" title="{{ $item->isActive?"Deactivate":"Activate" }}">
                        <i class="fa fa-{{ $item->isActive?"eye-slash":"eye" }}"></i>
                    </a>
                    <a href="javascript: openSettings({{ $block->id }}, {{ $item->id }})" title="Edit">
                        <i class="fa fa-pencil-alt"></i>
                    </a>

                </div>
            </div>

        </div>
    @endforeach
</div>