@eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field])

    @slot('field_markup')

        @if($view_only)
            <div class="{{ $Field->option_wrapper_class }}">
                <label class="{{ $Field->option_label_class }}" for={{ $Field->attributes->id }}>
                    {{ $Field->label }}
                </label>
                <div class="value">{{ $Field->value }}</div>
            </div>
        @else
            <input {!! $Field->attributes !!}>
            <label class="{{ $Field->option_label_class }}" for={{ $Field->attributes->id }}>
                {{ $Field->label }}
            </label>

            @eloquentforms_include($Field->view_namespace.'::pieces.example')
            @eloquentforms_include($Field->view_namespace.'::pieces.error')
        @endif

        @eloquentforms_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
