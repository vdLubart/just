<div id="contact_{{ $block->id }}" class="row">
    @foreach($block->item()->content() as $office)
    <div id="office_{{$office->id}}">
        @if(!empty($office->title))
        <h2>{{$office->title}}</h2>
        @endif

        @foreach($office->contacts() as $icon=>$contact)
            <div>
                <label>{{ $contact['label']}}</label>: {{ $contact['value'] }}
            </div>
        @endforeach
    </div>
    @endforeach
</div>