@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.label', ['Field' => $Field])

        @if($view_only)
            <div class="{{ $Field->input_wrapper_class.($view_only ? ' value' : '') }}">
                {!! nl2br($Field->attributes->value) !!}
            </div>
        @else
            <textarea {!! $Field->attributes !!}>{!! $Field->attributes->value !!}</textarea>
        @endif

        @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
