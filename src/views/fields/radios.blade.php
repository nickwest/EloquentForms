@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @if($Field->label != '')
            <label class="{{ $Field->label_class }}">{!! $Field->label.($Field->label_suffix != '' ? $Field->label_suffix : '').(isset($Field->attributes->required) ? ' <em>*</em>' : '') !!}</label>
        @endif
        @foreach($Field->options->getOptions() as $key => $option)
            @if($key != '') {{-- Make this optional somehow --}}
                {!! $Field->makeOptionView($key, $Field->attributes->value, $view_only) !!}
            @endif
        @endforeach

        @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
