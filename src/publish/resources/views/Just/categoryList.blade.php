<div class='col-md-12'>
    <div class='col-md-10'>
        <h4>
            Settings :: Category
        </h4>
    </div>
    <div class='col-md-1 text-right'>
        <a href="javascript:closeSettings()" title='Close settings'><i class="fa fa-close"></i></a>
    </div>
</div>
<div class='col-md-12'>
    @if(empty($categories) or empty($categories->first()))
        No category is created yet
    @endif
        
    <ul>
        @foreach($categories as $category)
            @if($currentId != $category->addon_id)
            </ul>
            <h5>{{ $category->addonTitle . ' on ' . $category->blockTitle . ' ('.$category->blockName.')' }}</h5>
            <?php $currentId = $category->addon_id; ?>
            <ul>
            @endif
            <li>
                <a href="javascript: deleteItem('category', {{ $category->id }})" title="Delete">
                    <i class="fa fa-trash-o"></i>
                </a>
                <a href="javascript: openSettings('category', {{ $category->id }})" title="Edit">
                    <i class="fa fa-pencil"></i>
                </a>
                <a href="javascript: openSettings('category', {{ $category->id }})">{{ $category->name }}:{{ $category->value }}</a>
            </li>
        @endforeach
    </ul>
</div>
