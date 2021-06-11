<nav class="navbar navbar-default navbar-static-top header">
    <div class="container">
        @if(\Config::get('isAdmin'))
            <div class="blockTitle">
                <slink href="/settings/page/{{ $page->id }}/panel/{{$panel->location}}" dusk="content-panel-settings">
                    <i class="fa fa-cogs"></i> @lang('settings.panel.setup')
                </slink>
            </div>
        @endif

        @foreach($panel->blocks() as $block)
            <div id="{{ $block->type }}_{{ $block->id }}" class="block col-md-{{ $block->width }} @if($block->isActive ==0) inactive @endif  {{ @$block->cssClass }}" >
                @include(viewPath($layout, $block))
            </div>
        @endforeach
    </div>
</nav>
