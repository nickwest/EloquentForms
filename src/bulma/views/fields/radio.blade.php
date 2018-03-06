@formmaker_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        <label class="{{ $Field->label_class }}" for={{ $Field->attributes->id }}>
            <input {!! $Field->attributes !!}>
            {{ $Field->label }}
        </label>

        @formmaker_include($Field->view_namespace.'::pieces.example')
        @formmaker_include($Field->view_namespace.'::pieces.error')
        @formmaker_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
