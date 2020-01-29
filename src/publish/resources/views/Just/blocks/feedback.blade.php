<script src='https://www.google.com/recaptcha/api.js'></script>

<div id="feedback_{{ $block->id }}" class="row">
    @if(\Session::has('successMessageFrom' . ucfirst($block->type . $block->id)) and \Session::get('successMessageFrom' . ucfirst($block->type . $block->id)) != '')
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('successMessageFrom' . ucfirst($block->type . $block->id)) !!}</li>
        </ul>
    </div>
    @endif
    
    {!! $block->item()->feedbackForm() !!}
    
    @foreach($block->content() as $comment)
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