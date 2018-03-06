@formmaker_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @formmaker_include($Field->view_namespace.'::pieces.label', ['Field' => $Field])

        @if($view_only)
            <div class="value">
                {{ $Field->value }}
            </div>
        @else
            <div class="{{ $Field->input_wrapper_class }}">
                <input {!! $Field->attributes !!} />
            </div>
            @formmaker_include($Field->view_namespace.'::pieces.example')
            @formmaker_include($Field->view_namespace.'::pieces.error')
        @endif

        @formmaker_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
