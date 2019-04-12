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
            <div id="{{ $block->type }}_{{ $block->id }}" class="block col-md-{{ $block->width }} @if($block->isActive ==0) inactive @endif  {{ @$block->cssClass }}" >
                @include(viewPath($layout, $block))
            </div>
        @endforeach
    </div>
</nav>