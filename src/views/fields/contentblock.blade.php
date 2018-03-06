@eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @if($Field->value != '')
            <div class="{{ $Field->class }}" id="{{ $Field->attributes->id }}">
                {!! $Field->value !!}
            </div>
        @endif
    @endslot

@endcomponent
