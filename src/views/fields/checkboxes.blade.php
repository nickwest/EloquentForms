@eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @if($Field->label != '')
            <label class="{{ $Field->label_class }}">{!! $Field->label.($Field->label_postfix != '' ? $Field->label_postfix : '').($Field->is_required ? ' <em>*</em>' : '') !!}</label>
        @endif
        @foreach($Field->options as $key => $option)
            {!! $Field->makeOptionView($key, $view_only) !!}
        @endforeach

        @eloquentforms_include($Field->view_namespace.'::pieces.example')
        @eloquentforms_include($Field->view_namespace.'::pieces.error')
        @eloquentforms_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
