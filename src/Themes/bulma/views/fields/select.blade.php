@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.label')

        @if($view_only)
            <div class="value">
                @foreach($Field->getOptions() as $key => $value)
                    @if( ($Field->attributes->multi_key != '' && $key == $Field->attributes->multi_key) || $Field->attributes->value == $key)
                        <div>{{ $value }}</div>
                    @endif
                @endforeach
            </div>
        @else
        <div class="{{ $Field->input_wrapper_class }}{{ $Field->attributes->multi_key != '' ? ' is-multiple' : '' }}">
            <select {!! $Field->attributes->getString(['value']) !!}>
                    @foreach($Field->getOptions() as $key => $value)
                    @eloquentforms_include($Field->getViewNamespace().'::fields.select_option')
                @endforeach
            </select>
        </div>
        @endif

        @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
