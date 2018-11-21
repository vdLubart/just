@if(!empty($block->firstItem()))
<div class="col-md-4">
    <img src="/storage/logos/{{ $block->firstItem()->image }}_4.png" height="120">
</div>
@endif
<div class="col-md-8">
    <h4>{!! $block->description !!}</h4>
</div>