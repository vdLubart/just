<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            @lang('settings.title') :: @lang('user.title') :: @lang('user.list')
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title=@lang('settings.actions.close')><i class="fa fa-times"></i></a>
    </div>
</div>
<div class='col-md-12'>
    <ul>
        @foreach($users as $user)
        <li>
            @if(\Auth::user()->id != $user->id)
            <a href="javascript: deleteItem('user', {{ $user->id }})" title="@lang('settings.actions.delete')">
            @endif
                <i class="fa fa-trash-alt"></i>
            @if(\Auth::user()->id != $user->id)    
            </a>
            @endif
            <a href="javascript: openSettings('user', {{ $user->id }})" title="@lang('settings.actions.edit')">
                <i class="fa fa-pencil-alt"></i>
            </a>
            <a href="javascript: openSettings('user', {{ $user->id }})">{{ $user->name }} ({{ $user->role }})</a>
        </li>
        @endforeach
    </ul>
</div>
