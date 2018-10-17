@foreach($block->content() as $feature)
<div class="col-md-{{ @$block->parameters()->itemsInRow }}">
    <div class="thumbnail featureItem">
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
        <div class="caption">
            <a href="javascript: deleteModel({{ $block->id }}, {{ $feature->id }})" title="Delete">
                <i class="fa fa-trash-o"></i>
            </a>
            <a href="javascript: activate({{ $feature->isActive?0:1 }}, {{ $block->id }}, {{ $feature->id }})" title="{{ $feature->isActive?"Deactivate":"Activate" }}">
                <i class="fa fa-{{ $feature->isActive?"eye-slash":"eye" }}"></i>
            </a>
            <a href="javascript: move('up', {{ $block->id }}, {{ $feature->id }})" title="Move Up">
                <i class="fa fa-arrow-up"></i>
            </a>
            <a href="javascript: move('down', {{ $block->id }}, {{ $feature->id }})" title="Move Down">
                <i class="fa fa-arrow-down"></i>
            </a>
            <a href="javascript: openSettings({{ $block->id }}, {{ $feature->id }})" title="Edit">
                <i class="fa fa-pencil"></i>
            </a>
        </div>
    </div>
</div>
@endforeach