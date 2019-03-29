<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            Settings :: Users
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-times"></i></a>
    </div>
</div>
<div class='col-md-12'>
    <ul>
        @foreach($users as $user)
        <li>
            @if(\Auth::user()->id != $user->id)
            <a href="javascript: deleteItem('user', {{ $user->id }})" title="Delete">
            @endif
                <i class="fa fa-trash-alt"></i>
            @if(\Auth::user()->id != $user->id)    
            </a>
            @endif
            <a href="javascript: openSettings('user', {{ $user->id }})" title="Edit">
                <i class="fa fa-pencil-alt"></i>
            </a>
            <a href="javascript: openSettings('user', {{ $user->id }})">{{ $user->name }} ({{ $user->role }})</a>
        </li>
        @endforeach
    </ul>
</div>
