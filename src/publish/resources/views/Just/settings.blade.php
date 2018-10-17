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
                <a href="#{{ $block->name }}_content" data-toggle="tab"><i class="fa fa-list"></i> Content</a>
            </li>
            <li>
                <a href="#{{ $block->name }}_blockData" data-toggle="tab"><i class="fa fa-cube"></i> Block Data</a>
            </li>
            <li>
                <a href="#{{ $block->name }}_blockSetup" data-toggle="tab"><i class="fa fa-cogs"></i> Block Settings</a>
            </li>
            @endif
            <li @if(!empty($block->model()->id) and empty($relBlock)) class="active" @endif>
                <a href="#{{ $block->name }}_settingsForm" data-toggle="tab">
                    @if(empty($block->model()->id))
                        <i class="fa fa-plus"></i> Create New Item
                    @else
                        <i class="fa fa-pencil"></i> Edit Item
                    @endif
                </a>
            </li>
            @if(!empty($block->model()->id))
            <li @if(!empty($relBlock)) class="active" @endif>
                <a href="#{{ $block->name }}_relationsForm" data-toggle="tab">@if(!empty($relBlock)) <i class="fa fa-pencil"></i><i class="fa fa-link"></i> Edit Related Block @else <i class="fa fa-plus"></i><i class="fa fa-link"></i> Create Related Block @endif </a>
            </li>
            <li>
                <a href="#{{ $block->name }}_relations" data-toggle="tab"></i><i class="fa fa-link"></i> Related Blocks</a>
            </li>
            @endif
        </ul>
        <div class="tab-content clearfix">
            @if(empty($block->model()->id))
            <div class="tab-pane active" id="{{ $block->name }}_content">
                @include('Just.settings.'.$block->name)
            </div>
            <div class="tab-pane" id="{{ $block->name }}_blockData">
                @include('Just.blockForm')
            </div>
            <div class="tab-pane" id="{{ $block->name }}_blockSetup">
                @include('Just.setupForm')
            </div>
            @endif
            <div class="tab-pane @if(!empty($block->model()->id) and empty($relBlock)) active @endif" id="{{ $block->name }}_settingsForm">
                @include('Just.settingsForm')
            </div>
            @if(!empty($block->model()->id))
            <div class="tab-pane @if(!empty($relBlock)) active @endif" id="{{ $block->name }}_relationsForm">
                @include('Just.relationsForm')
            </div>
            <div class="tab-pane" id="{{ $block->name }}_relations">
                @include('Just.relatedBlocks')
            </div>
            @endif
        </div>
    </div>
    
    
    
@endif