<div class='col-md-12'>
    <div class='col-md-11'>
        <h4>
            @lang('settings.title') :: @lang('settings.panel.title')
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='@lang('settings.actions.close')'><i class="fa fa-times"></i></a>
    </div>
</div>

<a href="javascript:" id="addBlockForm" data-toggle="collapse" data-target="#{{ $panel->location }}_settingsForm"><i class="fa fa-plus"></i> @lang('settings.panel.addBlock')</a>
<div class="@if(!$block->blockForm()->isOpen()) collapse @endif" id="{{ $panel->location }}_settingsForm">
    {!! $block->panelForm()->render() !!}
</div>
<br/><br/>

@foreach($panel->blocks() as $block)
<div class="col-md-{{ $block->width }}">
    <div class="thumbnail">
        <div class="caption">
            <a href="javascript: openSettings({{ $block->id }}, 0)">
                {{ $block->title }} ({{ $block->type }})
            </a>
            <br/>
            <a href="javascript: deleteModel({{ $block->id }}, 0)" title="@lang('settings.actions.delete')">
                <i class="fa fa-trash-alt"></i>
            </a>
            <a href="javascript: activate({{ $block->isActive?0:1 }}, {{ $block->id }}, 0)" title="{{ $block->isActive? __('settings.actions.deactivate'):__('settings.actions.activate') }}">
                <i class="fa fa-{{ $block->isActive?"eye-slash":"eye" }}"></i>
            </a>
            <a href="javascript: move('up', {{ $block->id }}, 0)" title="@lang('settings.actions.moveUp')">
                <i class="fa fa-arrow-up"></i>
            </a>
            <a href="javascript: move('down', {{ $block->id }}, 0)" title="@lang('settings.actions.moveDown')">
                <i class="fa fa-arrow-down"></i>
            </a>
            <a href="javascript: openSettings({{ $block->id }}, 0)" title="@lang('settings.actions.edit')">
                <i class="fa fa-pencil-alt"></i>
            </a>
        </div>
    </div>
</div>
@endforeach