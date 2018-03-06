@eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @if($Field->label != '')
            <h2 class="{{ $Field->label_class }}" id="{{ $Field->attributes->id }}">
                {{ $Field->label }}
            </h2>
        @endif
    @endslot

@endcomponent
