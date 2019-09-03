<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            @lang('settings.title') :: @lang('layout.title') :: @lang('layout.list')
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='@lang('settings.actions.close')'><i class="fa fa-times"></i></a>
    </div>
</div>
<div class='col-md-12'>
    <ul>
        @foreach($layouts as $layout)
        <li>
            <a href="javascript: deleteLayout({{ $layout->id }})" title="@lang('settings.actions.delete')">
                <i class="fa fa-trash-alt"></i>
            </a>
            <a href="javascript: openSettings('layout', {{ $layout->id }})" title="@lang('settings.actions.edit')">
                <i class="fa fa-pencil-alt"></i>
            </a>
            <a href="javascript: openSettings('layout', {{ $layout->id }})">{{ $layout->name. ".". $layout->class }}</a>
        </li>
        @endforeach
    </ul>
</div>
