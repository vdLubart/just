<h3>!!! Specific template !!!</h3>
<div id="gallery_{{ $block->id }}" class="row">
    <div class="col-md-12 margin-bottom-20"><strong>$block->title:</strong> {!!$block->title!!}</div>
    <div class="col-md-12 margin-bottom-20"><strong>$block->description:</strong> {!!$block->description!!}</div>
    <div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#gallery_{{ $block->id }}_attributes">$block</a>:</strong> Click to show attributes
        <div id="gallery_{{ $block->id }}_attributes" class="collapse">
            {{var_dump($block->getAttributes())}}
        </div>
    </div>
    <div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#gallery_params_{{ $block->id }}_attributes">$block->parameters</a>:</strong> Click to show attributes
        <div id="gallery_params_{{ $block->id }}_attributes" class="collapse">
            {{var_dump($block->parameters)}}
        </div>
    </div>
    <div class="col-md-12"><strong>{{"@"}}foreach($block->categories() as $category)</strong>
        @if($block->categories()->first())
            @foreach($block->categories() as $category)
                <div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#cat_{{ $category->id }}_attributes">$category</a>:</strong> Click to show attributes
                    <div id="cat_{{ $category->id }}_attributes" class="collapse">
                        {{var_dump($category->getAttributes())}}
                    </div>
                </div>
                <div class="col-md-12"><strong>{{"@"}}foreach($category->values()->get() as $cat)</strong></div>
                <div class="col-md-12">
                    @foreach($category->values()->get() as $cat)
                        <div class="col-md-4">
                            <div class="thumbnail">
                                <div><strong>$cat->name:</strong> {{$cat->name}}</div>
                                <div>
                                    <strong><a href="javascript:" data-toggle="collapse" data-target="#catVal_{{ $cat->id }}_attributes">$cat</a>:</strong> Click to show attributes
                                    <div id="catVal_{{ $cat->id }}_attributes" class="collapse">
                                        {{var_dump($cat->getAttributes())}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-md-12 margin-bottom-20"><strong>{{"@"}}endforeach</strong></div>
            @endforeach
        @endif
    </div>
    <div class="col-md-12 margin-bottom-20"><strong>{{"@"}}endforeach</strong></div>
    
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
                    <div>
                        <strong>{{"@"}}foreach($block->addons()->get() as $addon)</strong>
                    </div>
                    @foreach($block->addons()->get() as $addon)
                    <div class="col-md-12 margin-bottom-20"><strong><a href="javascript:" data-toggle="collapse" data-target="#addon_{{ $addon->id }}_attributes">$addon</a>:</strong> Click to show attributes
                        <div id="addon_{{ $addon->id }}_attributes" class="collapse">
                            {{var_dump($addon->getAttributes())}}
                        </div>
                    </div>
                    <div>
                        <div class="col-md-12">
                            <div><strong>{{"@"}}foreach($photo->addonValues($addon->id) as $val)</strong></div>
                            <div class="col-md-12">
                                <strong><a href="javascript:" data-toggle="collapse" data-target="#photo_cat_{{ $photo->id }}_attributes">Addons values</a>:</strong> Click to show values
                                <div id="photo_cat_{{ $photo->id }}_attributes" class="collapse">
                                    @foreach($photo->addonValues($addon->id) as $val)
                                        {{ var_dump($val->getAttributes()) }}
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12"><strong>{{"@"}}endforeach</strong></div>
                    </div>
                    @endforeach
                    <div>
                        <strong>{{"@"}}endforeach</strong>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="col-md-12 margin-bottom-20"><strong>{{"@"}}endforeach</strong></div>
    
</div>
