<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            @lang('settings.title') :: @lang('addon.category.title') :: @lang('addon.category.list')
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='@lang('settings.actions.close')'><i class="fa fa-times"></i></a>
    </div>
</div>
<div class='col-md-12'>
    @if(empty($categories) or empty($categories->first()))
        @lang('addon.category.emptyList')
    @endif
        
    <ul>
        @foreach($categories as $category)
            @if($currentId != $category->addon_id)
            </ul>
            <h5>@lang('addon.category.listItem', ['title' => $category->addonTitle, 'block' => $category->blockTitle . ' ('.$category->blockName.')'])</h5>
            <?php $currentId = $category->addon_id; ?>
            <ul>
            @endif
            <li>
                <a href="javascript: deleteItem('category', {{ $category->id }})" title="@lang('settings.actions.delete')">
                    <i class="fa fa-trash-o"></i>
                </a>
                <a href="javascript: openSettings('category', {{ $category->id }})" title="@lang('settings.actions.edit')">
                    <i class="fa fa-pencil"></i>
                </a>
                <a href="javascript: openSettings('category', {{ $category->id }})">{{ $category->name }}:{{ $category->value }}</a>
            </li>
        @endforeach
    </ul>
</div>
