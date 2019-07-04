<h4>Following people are registered in the "{{ $block->model()->subject }}" event:</h4>

<table border="1" cellpadding="10" width="90%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Email</th>
            <th>Comment</th>
        </tr>
    </thead>
    <tbody>
        @foreach($block->model()->registrations as $reg)
            <tr>
                <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $reg->created_at)->format('M d, Y H:i') }}</td>
                <td>{{ $reg->name }}</td>
                <td><a href="mailto:{{ $reg->email }}">{{ $reg->email }}</a></td>
                <td>{{ $reg->comment }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
