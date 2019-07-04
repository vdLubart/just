@if(is_null($block->model()->id))

    <h3>Future events</h3>

    @if($block->content()->isEmpty())
        There is no event planned yet
    @endif

    @foreach($block->content() as $event)
        <div class="col-md-2">
            {{ $event->start_date }} -
            {{ $event->end_date }} at
            {{ $event->location }}
        </div>
        @if(!empty($event->image))
        <div class="col-md-4">
            <a href="{{ url((\Config::get('isAdmin')?'admin/':'') . $block->parameter('itemRouteBase'), ['id'=>$event->slug]) }}">
                <img src="{{ '/storage/events/'.$event->image."_4.png" }}" />
            </a>
        </div>
        @endif
        <div class="col-md-{{ !empty($event->image) ? 6 : 10 }}">
            <h3>
                <a href="{{ url((\Config::get('isAdmin')?'admin/':'') . $block->parameter('itemRouteBase'), ['id'=>$event->slug]) }}">
                    {{ $event->subject }}
                </a>
            </h3>
            <div>
                {!! $event->summary !!}
            </div>
        </div>
    @endforeach

    <h3>Past events</h3>

    @if($block->model()->pastEvents()->isEmpty())
        There is no event held in the past
    @endif

    @foreach($block->model()->pastEvents() as $event)
        <div class="col-md-2">
            {{ $event->start_date }} -
            {{ $event->end_date }} at
            {{ $event->location }}
        </div>
        @if(!empty($event->image))
        <div class="col-md-4">
            <a href="{{ url((\Config::get('isAdmin')?'admin/':'') . $block->parameter('itemRouteBase'), ['id'=>$event->slug]) }}">
                <img src="{{ '/storage/events/'.$event->image."_4.png" }}" />
            </a>
        </div>
        @endif
        <div class="col-md-{{ !empty($event->image) ? 6 : 10 }}">
            <h3>
                <a href="{{ url((\Config::get('isAdmin')?'admin/':'') . $block->parameter('itemRouteBase'), ['id'=>$event->slug]) }}">
                    {{ $event->subject }}
                </a>
            </h3>
            <div>
                {!! $event->summary !!}
            </div>
        </div>
    @endforeach

@else
    <div class="col-md-12">
        <div class="thumbnail">
            @if(!empty($block->model()->image))
            <a href="{{ url((\Config::get('isAdmin')?'admin/':''). $block->parameter('itemRouteBase'), ['id'=>$block->model()->slug]) }}">
                <img src="{{ '/storage/events/'.$block->model()->image."_12.png" }}" />
            </a>
            @endif
            <div class="caption">
                <h3>{{ $block->model()->subject }}</h3>
            </div>
            {!! $block->model()->text !!}
        </div>

        <div>

            @if(\Session::has('successMessageFrom' . ucfirst($block->type . $block->id)) and \Session::get('successMessageFrom' . ucfirst($block->type . $block->id)) != '')
                <div class="alert alert-success">
                    <ul>
                        <li>{!! \Session::get('successMessageFrom' . ucfirst($block->type . $block->id)) !!}</li>
                    </ul>
                </div>
            @else
                @if(isset($errors))
                    <?php
                    $errorBag = $errors->{'errorsFromEvents' . $block->id};
                    ?>
                    @if($errorBag->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errorBag->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endif

                <a href="#registerForm" id="regButton" onclick="openRegisterForm()" class="btn btn-primary">Register</a>
                <div id="registerForm" style="display: none">
                    {!!  $block->model()->registerForm() !!}
                </div>

                <script>

                    function openRegisterForm(id){
                        $("#registerForm").show();
                        $("#regButton").hide();

                        return false;
                    }

                    @if(@$errorBag->any())
                    openRegisterForm();
                    @endif

                </script>
            @endif
        </div>
    </div>
@endif