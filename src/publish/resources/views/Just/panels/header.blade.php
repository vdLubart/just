<nav class="navbar navbar-default navbar-static-top header">
    <div class="container">
        @if(\Config::get('isAdmin'))
        <div class="blockTitle">
            <a href="javascript:openPanelSettings({{ $page->id }}, '{{$panel->location}}')">
                <i class="fa fa-cogs"></i> Setup panel
            </a>
        </div>
        @endif
        
        @foreach($panel->blocks() as $block)
            <div id="{{ $block->name }}_{{ $block->id }}" class="block col-md-{{ $block->width }} @if($block->isActive ==0) inactive @endif  {{ @$block->cssClass }}" >
                @if($layout->class != "primary" and file_exists(resource_path('views/'.$layout->name.'/blocks/'.$block->name.'_'.$layout->class.'.blade.php')))
                    @include($layout->name.'.blocks.'. $block->name . '_' . $layout->class)
                @elseif($block->layoutClass != "primary" and file_exists(resource_path('views/'.$layout->name.'/blocks/'.$block->name.'_'.$block->layoutClass.'.blade.php')))
                    @include($layout->name.'.blocks.'. $block->name . '_' . $block->layoutClass)
                @else
                    @include($layout->name.'.blocks.'. $block->name)
                @endif
            </div>
        @endforeach
    </div>
</nav>