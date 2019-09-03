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

        @include($layout->name.'.blocks.'. $block->type)
    @elseif(\Config::get('isAdmin'))
        <div class="blockTitle">
            <a href="javascript:openPanelSettings({{ $page->id }}, '{{$panel->location}}')" dusk="content-panel-settings">
                <i class="fa fa-cogs"></i> @lang('settings.panel.setup')
            </a>
        </div>
    @endif
    
    @foreach($panel->blocks() as $block)
        <div class="block {{ $block->type }} col-md-{{ $block->width }} @if($block->isActive ==0) inactive @endif {{ $block->cssClass }}">
            @if(\Config::get('isAdmin'))
            <div class="blockTitle">
                {{ $block->title }}
                <a href="javascript:openSettings({{ $block->id }}, 0)">
                    <i class="fa fa-cog"></i>
                </a>
            </div>
            @endif
            <div id="{{ $block->type."_".$block->id }}">
            @include(viewPath($layout, $block))
            </div>
        </div>
    @endforeach
</div>
<div style="margin-bottom: 70px"></div>