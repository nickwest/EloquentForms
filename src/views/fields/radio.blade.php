@eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        <input {!! $Field->attributes !!}>
        <label class="{{ $Field->label_class }}" for={{ $Field->attributes->id }}>
            {{ $Field->label }}
        </label>

        @eloquentforms_include($Field->view_namespace.'::pieces.example')
        @eloquentforms_include($Field->view_namespace.'::pieces.error')
        @eloquentforms_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
