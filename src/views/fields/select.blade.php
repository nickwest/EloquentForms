@eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @eloquentforms_include($Field->view_namespace.'::pieces.label')

        @if($view_only)
            <div class="value">
                @foreach($Field->options as $key => $value)
                    @if( ($Field->multiple && in_array($key, $Field->multi_value)) || $Field->value == $key)
                        <div>{{ $value }}</div>
                    @endif
                @endforeach
            </div>
        @else
            <select {!! $Field->attributes !!}>
                @foreach($Field->options as $key => $value)
                    @eloquentforms_include($Field->view_namespace.'::fields.select_option')
                @endforeach
            </select>
        @endif

        @eloquentforms_include($Field->view_namespace.'::pieces.example')
        @eloquentforms_include($Field->view_namespace.'::pieces.error')
        @eloquentforms_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
