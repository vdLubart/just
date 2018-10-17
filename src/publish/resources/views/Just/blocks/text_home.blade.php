<h3>!!Special template!!</h3>
<h3>{{ $block->title }}</h3>

<div>{!! $block->description !!}</div>

@foreach($block->content() as $item)
    <div>{!! $item->text !!}</div>
@endforeach