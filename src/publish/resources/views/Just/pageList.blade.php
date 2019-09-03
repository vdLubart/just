<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            @lang('settings.title') :: @lang('page.title') :: @lang('page.list')
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='@lang('settings.actions.close')'><i class="fa fa-times"></i></a>
    </div>
</div>
<div class='col-md-12'>
    <ul>
        @foreach($pages as $page)
        <li>
            <a href="javascript: deleteItem('page', {{ $page->id }})" title="@lang('settings.actions.delete')">
                <i class="fa fa-trash-alt"></i>
            </a>
            <a href="javascript: openSettings('page', {{ $page->id }})" title="@lang('settings.actions.edit')">
                <i class="fa fa-pencil-alt"></i>
            </a>
            <a href="{{ "admin/".$page->route }}">{{ $page->title }}</a>  - {{ substr(strip_tags($page->description), 0, 100) }}
        </li>
        @endforeach
    </ul>
</div>
