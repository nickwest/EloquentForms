@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.label', ['Field' => $Field])

        <div class="{{ $Field->input_wrapper_class.($view_only ? ' value' : '') }}">
            @if($view_only)
                {!! nl2br($Field->attributes->value) !!}
            @else
                <textarea class="{{ $Field->attributes->class }}" id="{{ $Field->attributes->id }}" name="{{ $Field->attributes->name }}" class="{{ $Field->attributes->class }}" placeholder="{{ $Field->attributes->placeholder }}">{!! $Field->attributes->value !!}</textarea>
            @endif
        </div>

        @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
