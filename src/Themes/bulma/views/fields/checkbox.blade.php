@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field])

    @slot('field_markup')

        @if($view_only)
            <div class="{{ $Field->options->wrapper_class }}">
                <label class="{{ $Field->options->label_class }}" for={{ $Field->attributes->id }}>
                    {{ $Field->label }}
                </label>
                <div class="value">{{ $Field->attributes->value }}</div>
            </div>
        @else
            <div class="{{ $Field->options->wrapper_class }}">
                <label class="{{ $Field->options->label_class }}" for={{ $Field->attributes->id }}>
                    <input {!! $Field->attributes !!}>
                    {{ $Field->label }}
                </label>
            </div>

            @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
            @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @endif

        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
