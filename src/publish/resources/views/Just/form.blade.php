<div class='errors alert alert-danger hide'>
    
</div>
{!! Form::open([ 'url' => $form->action(), 'method'=>$form->method(), 'files'=>$form->files() ]) !!}

@foreach($form->getElements() as $key=>$element)
    <div>
        @if($element->type() == 'select')
            {!! Form::label($element->name(), $element->label())!!}<br/>
            {!! Form::{$element->type()}(
                $element->name(),
                $element->options(),
                $element->value(),
                $element->parameters()
                );
            !!}
        @elseif(in_array($element->type(), ['checkbox', 'radio']))
            <label>
                {!! Form::{$element->type()}(
                    $element->name(),
                    $element->value(),
                    $element->check(),
                    $element->parameters()
                    );
                !!}
                {!! $element->label() !!}
            </label>
        @elseif(in_array($element->type(), ['submit', 'button']))
            {!! Form::{$element->type()}(
                $element->value(),
                $element->parameters()+['name'=>$element->name()]
                );
            !!}
        @elseif($element->type() == 'html')
            {!! $element->value() !!}
        @else
            @if($element->type() != "hidden")
            {!! Form::label($element->name(), $element->label())!!}<br/>
            @endif
            {!! Form::{$element->type()}(
                $element->name(),
                $element->value(),
                $element->parameters()
                );
            !!}
        @endif
    </div>
@endforeach

{!! Form::close() !!}

@if($form->js() && !empty($block))
<script src="/js/blocks/{{$block->name}}/{{$form->type()}}Form.js" />
@elseif($form->type() === 'layoutSettings')
<script src="/js/layouts/{{$form->type()}}Form.js" />
@endif