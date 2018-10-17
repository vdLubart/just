@if($errors->any())
<div class="alert-warning">
    <ul>
        @foreach($errors as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif