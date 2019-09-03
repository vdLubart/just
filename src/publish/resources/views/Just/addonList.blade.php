<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            @lang('settings.title') :: @lang('addon.title') :: @lang('addon.list')
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='@lang('settings.actions.close')'><i class="fa fa-times"></i></a>
    </div>
</div>
<div class='col-md-12'>
    <ul>
        @foreach($addons as $addon)
        <li>
            <a href="javascript: deleteItem('addon', {{ $addon->id }})" title="@lang('settings.actions.delete')">
                <i class="fa fa-trash-alt"></i>
            </a>
            <a href="javascript: openSettings('addon', {{ $addon->id }})" title="@lang('settings.actions.edit')">
                <i class="fa fa-pencil-alt"></i>
            </a>
            @lang('addon.addonLocation', [
            'addon' => '<a href="javascript: openSettings(\'addon\', ' . $addon->id . ')">' . $addon->title . ' (' . $addon->name . ', ' . $addon->type . ')</a>',
            'block' => $addon->block->title . ' (' . $addon->block->type .')',
            'page' => '<a href="/admin/' . (is_null($addon->block->page())?"":$addon->block->page()->route) . '">' . (is_null($addon->block->page())?$addon->block->panelLocation:$addon->block->page()->title) . '}}</a>'
            ])
        </li>
        @endforeach
    </ul>
</div>
