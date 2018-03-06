{{--
    This template can be overriden to be unique,
    by default it uses the default input markup
 --}}

{{--  Custom view only view  --}}
@if($view_only)
    @eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

        @slot('field_markup')
            @eloquentforms_include($Field->view_namespace.'::pieces.label', ['Field' => $Field])

            <div class="value">
                <div style="display: inline-block; padding: 2px; border: 10px solid {{ $Field->value }}">{{ $Field->value }}</div>
            </div>

            @eloquentforms_include($Field->view_namespace.'::pieces.note')
        @endslot

    @endcomponent

{{--  Use default input template for non-view only --}}
@else
    @include("Nickwest\EloquentForms::pieces.default-input")
@endif
