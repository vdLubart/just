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
