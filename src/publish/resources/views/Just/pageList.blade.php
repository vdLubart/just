<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            Settings :: Pages
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>
<div class='col-md-12'>
    <ul>
        @foreach($pages as $page)
        <li>
            <a href="javascript: deleteItem('page', {{ $page->id }})" title="Delete">
                <i class="fa fa-trash-o"></i>
            </a>
            <a href="javascript: openSettings('page', {{ $page->id }})" title="Edit">
                <i class="fa fa-pencil"></i>
            </a>
            <a href="{{ "admin/".$page->route }}">{{ $page->title }}</a>  - {{ substr(strip_tags($page->description), 0, 100) }}
        </li>
        @endforeach
    </ul>
</div>
