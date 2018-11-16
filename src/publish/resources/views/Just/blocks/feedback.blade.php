<script src='https://www.google.com/recaptcha/api.js'></script>

<div id="feedback_{{ $block->id }}" class="row">
    @if($errors->{'errorsFrom'.ucfirst($block->name . $block->id)}->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->{'errorsFrom'.ucfirst($block->name . $block->id)}->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @elseif(\Session::has('successMessageFrom' . ucfirst($block->name . $block->id)) and \Session::get('successMessageFrom' . ucfirst($block->name . $block->id)) != '')
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('successMessageFrom' . ucfirst($block->name . $block->id)) !!}</li>
        </ul>
    </div>
    @endif
    
    {!! $block->content()->form->render() !!}
    
    @foreach($block->content()->messages as $comment)
    <div class="col-md-6">
        <div class="thumbnail">
            <div class="caption">
                <h4>
                    <a href="mailto:{{$comment->email}}">
                        {{ $comment->username }}
                    </a>
                     :: {{ $comment->created_at }}
                </h4>
                <p>{{$comment->message}}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>