@formmaker_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @formmaker_include($Field->view_namespace.'::pieces.label', ['Field' => $Field])

        <div class="{{ $Field->input_wrapper_class.($view_only ? ' value' : '') }}">
            @if($view_only)
                {!! nl2br($Field->value) !!}
            @else
                <textarea class="{{ $Field->attributes->class }}" id="{{ $Field->attributes->id }}" name="{{ $Field->attributes->name }}" class="{{ $Field->attributes->class }}" placeholder="{{ $Field->attributes->placeholder }}">{!! $Field->value !!}</textarea>
            @endif
        </div>

        @formmaker_include($Field->view_namespace.'::pieces.example')
        @formmaker_include($Field->view_namespace.'::pieces.error')
        @formmaker_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
