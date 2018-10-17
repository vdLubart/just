<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            Settings :: Addons
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>
<div class='col-md-12'>
    <ul>
        @foreach($addons as $addon)
        <li>
            <a href="javascript: deleteAddon({{ $addon->id }})" title="Delete">
                <i class="fa fa-trash-o"></i>
            </a>
            <a href="javascript: openAddonSettings({{ $addon->id }})" title="Edit">
                <i class="fa fa-pencil"></i>
            </a>
            <a href="javascript: openAddonSettings({{ $addon->id }})">{{ $addon->title }}</a>  in {{ $addon->block->title }} ({{ $addon->block->name }}) block at 
            <a href="/admin/{{ (is_null($addon->block->page)?"":$addon->block->page->route) }}">{{ (is_null($addon->block->page)?$addon->block->panelLocation:$addon->block->page->title." page") }}</a>
        </li>
        @endforeach
    </ul>
</div>
