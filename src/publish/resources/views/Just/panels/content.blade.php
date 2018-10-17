<div class="main container">
    @if(!is_null($block))
        @if(\Config::get('isAdmin'))
        <div class="blockTitle">
            {{ $block->title }}
            <a href="javascript:openSettings({{ $block->id }}, {{ $block->model()->id }})">
                <i class="fa fa-cog"></i>
            </a>
        </div>
        @endif

        @include($layout->name.'.blocks.'. $block->name)
    @elseif(\Config::get('isAdmin'))
        <div class="blockTitle">
            <a href="javascript:openPanelSettings({{ $page->id }}, '{{$panel->location}}')">
                <i class="fa fa-cogs"></i> Setup panel
            </a>
        </div>
    @endif
    
    @foreach($panel->blocks() as $block)
        <div class="block {{ $block->name }} col-md-{{ $block->width }} @if($block->isActive ==0) inactive @endif {{ $block->cssClass }}">
            @if(\Config::get('isAdmin'))
            <div class="blockTitle">
                {{ $block->title }}
                <a href="javascript:openSettings({{ $block->id }}, 0)">
                    <i class="fa fa-cog"></i>
                </a>
            </div>
            @endif
            <div id="{{ $block->name."_".$block->id }}">
            @if($layout->class != "primary" and file_exists(resource_path('views/'.$layout->name.'/blocks/'.$block->name.'_'.$layout->class.'.blade.php')))
                @include($layout->name.'.blocks.'. $block->name . '_' . $layout->class)
            @elseif($block->layoutClass != "primary" and file_exists(resource_path('views/'.$layout->name.'/blocks/'.$block->name.'_'.$block->layoutClass.'.blade.php')))
                @include($layout->name.'.blocks.'. $block->name . '_' . $block->layoutClass)
            @else
                @include($layout->name.'.blocks.'. $block->name)
            @endif
            </div>
        </div>
    @endforeach
</div>
<div style="margin-bottom: 70px"></div>