@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])
    @slot('field_markup')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.label', ['Field' => $Field])

        @if($view_only)
            <div class="value">
                {{ $Field->attributes->value }}
            </div>
        @else
            <div class="{{ $Field->input_wrapper_class }}">
                <input {!! $Field->attributes !!} />
            </div>
            @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
            @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @endif

        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
