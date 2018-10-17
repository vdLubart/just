@if(!empty($block->content()->first()))
<div class="col-md-4">
    <img src="/storage/logos/{{ $block->content()->first()->image }}_4.png" height="120">
</div>
@endif
<div class="col-md-8">
    <h4>{!! $block->description !!}</h4>
</div>

@switch($block->cssClass)
    @case("left")
        {{ $block->model()->content()->first()->strings[0]->value }}
    @break
    @case("right")
        {{ $block->model()->content()->first()->strings[0]->value }}
    @break
@endswitch
