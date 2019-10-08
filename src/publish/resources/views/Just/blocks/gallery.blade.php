<div class="col-md-12 margin-bottom-20"><strong>$block->title:</strong> {!!$block->title!!}</div>
<div class="col-md-12 margin-bottom-20"><strong>$block->description:</strong> {!!$block->description!!}</div>
<div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#gallery_{{ $block->id }}_attributes">$block</a>:</strong> Click to show attributes
    <div id="gallery_{{ $block->id }}_attributes" class="collapse">
        {{var_dump($block->getAttributes())}}
    </div>
</div>
<div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#gallery_params_{{ $block->id }}_attributes">$block->parameters</a>:</strong> Click to show parameters
    <div id="gallery_params_{{ $block->id }}_attributes" class="collapse">
        {{var_dump($block->parameters)}}
    </div>
</div>

<!-- Addon List -->
<div class="col-md-12"><strong>{{"@"}}foreach($block->addons as $addon)</strong>
    @foreach($block->addons as $addon)
        <div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#cat_{{ $addon->id }}_attributes">$addon</a>:</strong> Click to show attributes
            <div id="cat_{{ $addon->id }}_attributes" class="collapse">
                {{var_dump($addon->getAttributes())}}
            </div>
        </div>
    @endforeach
</div>
<div class="col-md-12 margin-bottom-20"><strong>{{"@"}}endforeach</strong></div>
<!-- End Addon List -->

<div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#current_category_{{ $block->id }}_attributes">$block->currentCategory()</a>:</strong> Click to show attributes
    <div id="current_category_{{ $block->id }}_attributes" class="collapse">
        <?php
        $currentCategory = $block->currentCategory();
        ?>
        {{var_dump(is_null($currentCategory) ? $currentCategory : $currentCategory->getAttributes())}}
    </div>
</div>

<div class="col-md-12"><strong>{{"@"}}foreach($block->content() as $photo)</strong></div>
<div class="col-md-12 margin-bottom-20">
    @foreach($block->content() as $photo)
        <div class="col-md-6">
            <div class="thumbnail">
                <a href="#">
                    <img src="{{ '/storage/photos/'. $photo->image . '_6.png' }}" />
                </a>
            </div>
            <div class="caption">
                <div><strong>$photo->caption:</strong> {{$photo->caption}}</div>
                <div>
                    <strong><a href="javascript:" data-toggle="collapse" data-target="#photo_{{ $photo->id }}_attributes">$photo</a>:</strong> Click to show attributes
                    <div id="photo_{{ $photo->id }}_attributes" class="collapse">
                        {{var_dump($photo->getAttributes())}}
                    </div>
                </div>
            </div>
            <div class="caption">
                <div>
                    <strong><a href="javascript:" data-toggle="collapse" data-target="#photo_{{ $photo->id }}_category">$photo->category</a>:</strong> Click to show addon content
                    <div id="photo_{{ $photo->id }}_category" class="collapse">
                        {{var_dump($photo->category)}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<div class="col-md-12 margin-bottom-20"><strong>{{"@"}}endforeach</strong></div>