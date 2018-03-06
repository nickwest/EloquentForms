@formmaker_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @formmaker_include($Field->view_namespace.'::pieces.label')

        @if($view_only)
            <div class="value">
                @foreach($Field->options as $key => $value)
                    @if( ($Field->multiple && in_array($key, $Field->multi_value)) || $Field->value == $key)
                        <div>{{ $value }}</div>
                    @endif
                @endforeach
            </div>
        @else
        <div class="{{ $Field->input_wrapper_class }}{{ $Field->multiple ? ' is-multiple' : '' }}">
                <select {!! $Field->attributes !!}>
                @foreach($Field->options as $key => $value)
                    @formmaker_include($Field->view_namespace.'::fields.select_option')
                @endforeach
            </select>
        </div>
        @endif

        @formmaker_include($Field->view_namespace.'::pieces.example')
        @formmaker_include($Field->view_namespace.'::pieces.error')
        @formmaker_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
