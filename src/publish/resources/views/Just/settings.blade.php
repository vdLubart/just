@include('Just.settingsTitle')

@if(!$block->isSetted() or (isset($setup) and $setup ))
    @include('Just.setupForm')
@elseif(isset($crop))
    @include('Just.cropForm')
@else
    <div class="container">	
        <ul class="nav nav-pills">
            @if(empty($block->model()->id))
            <li class="active">
                <a href="#{{ $block->type }}_content" data-toggle="tab"><i class="fa fa-list"></i> Content</a>
            </li>
            <li>
                <a href="#{{ $block->type }}_blockData" data-toggle="tab"><i class="fa fa-cube"></i> Block Data</a>
            </li>
            <li>
                <a href="#{{ $block->type }}_blockSetup" data-toggle="tab"><i class="fa fa-cogs"></i> Block Settings</a>
            </li>
            @endif
            <li @if(!empty($block->model()->id) and empty($relBlock)) class="active" @endif>
                <a href="#{{ $block->type }}_settingsForm" data-toggle="tab">
                    @if(empty($block->model()->id))
                        <i class="fa fa-plus"></i> Create New Item
                    @else
                        <i class="fa fa-pencil"></i> Edit Item
                    @endif
                </a>
            </li>
            @if(!empty($block->model()->id))
            <li @if(!empty($relBlock)) class="active" @endif>
                <a href="#{{ $block->type }}_relationsForm" data-toggle="tab">@if(!empty($relBlock)) <i class="fa fa-pencil"></i><i class="fa fa-link"></i> Edit Related Block @else <i class="fa fa-plus"></i><i class="fa fa-link"></i> Create Related Block @endif </a>
            </li>
            <li>
                <a href="#{{ $block->type }}_relations" data-toggle="tab"></i><i class="fa fa-link"></i> Related Blocks</a>
            </li>
            @endif
        </ul>
        <div class="tab-content clearfix">
            @if(empty($block->model()->id))
            <div class="tab-pane active" id="{{ $block->type }}_content">
                @if(!file_exists(resource_path('views/Just/settings/'.$block->type.'.blade.php')))
                    <?php
                    $zoom = 100;
                    if(isset($block->parameters()->itemsInRow)){
                        $zoom = (int)round(@$block->parameters()->itemsInRow / 3 * 100);
                    }
                    else{
                        $zoom = (isset($block->parameters()->settingsScale)?$block->parameters()->settingsScale:"100");
                    }
                    ?>

                    @if(is_null($block->model()->id))
                        @include('Just.categoryFilter')
                    @endif

                    <div id="dragula-container" class="dragula-list-container-{{ $zoom }}">
                        @foreach($block->content() as $item)
                        <div class="dragula-list-item  {{ ($block->categories()->first()?@$item->categories->first()->value:"") }}" data-no="{{$item->orderNo}}" data-id="{{$item->id}}">
                            @include('Just.settings.list')
                        </div>
                        @endforeach
                    </div>
                @else
                    @include('Just.settings.'.$block->type)
                @endif
                
                <script>
                $(document).ready(function(){
                    dragItems('dragula-container', {{$block->id}});
                });
                </script>
            </div>
            <div class="tab-pane" id="{{ $block->type }}_blockData">
                @include('Just.blockForm')
            </div>
            <div class="tab-pane" id="{{ $block->type }}_blockSetup">
                @include('Just.setupForm')
            </div>
            @endif
            <div class="tab-pane @if(!empty($block->model()->id) and empty($relBlock)) active @endif" id="{{ $block->type }}_settingsForm">
                @include('Just.settingsForm')
            </div>
            @if(!empty($block->model()->id))
            <div class="tab-pane @if(!empty($relBlock)) active @endif" id="{{ $block->type }}_relationsForm">
                @include('Just.relationsForm')
            </div>
            <div class="tab-pane" id="{{ $block->type }}_relations">
                @include('Just.relatedBlocks')
            </div>
            @endif
        </div>
    </div>
    
    
    
@endif