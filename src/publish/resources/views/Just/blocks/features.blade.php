<h3>{{ $block->title }}</h3>

<div>{!! $block->description !!}</div>

@foreach($block->content() as $feature)
<div class="col-md-{{ @$block->parameters()->itemsInRow }}">
    <div class='featureItem'>
        <h1>
            <?php
            $iconSet = $feature->icon->iconSet;
            echo '<'.$iconSet->tag.' class="'.$iconSet->class.' '.$feature->icon->class.'"></'.$iconSet->tag.'>';
            ?>
        </h1>
        <h3>
            <a href="{{ $feature->link }}">
                {{ $feature->title }}
            </a>
        </h3>
        <h5>
            {!! $feature->description !!}
        </h5>
    </div>
</div>
@endforeach