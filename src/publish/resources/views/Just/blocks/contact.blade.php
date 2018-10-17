<div id="contact_{{ $block->id }}" class="row">
    @foreach($block->model()->content() as $office)
    <div id="office_{{$office->id}}">
        @if(!empty($office->title))
        <h2>{{$office->title}}</h2>
        @endif
        
        @foreach($office->fields() as $field=>$attr)
            @if(!empty($office->{$field}))
            <div>
                <label>{{$attr[0]}}</label>: {{ $office->{$field} }}
            </div>
            @endif
        @endforeach
    </div>
    @endforeach
</div>