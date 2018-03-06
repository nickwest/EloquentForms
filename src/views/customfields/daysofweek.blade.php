<div id="field-{{ $Field->attributes->id }}" class="field">
    <div class="daysofweek {{ $Field->options_container_class }}">
        <label class="{{ $Field->label_class }}">{!! $Field->label.($Field->label_postfix != '' ? $Field->label_postfix : '').($Field->is_required ? ' <em>*</em>' : '') !!}</label>

        @foreach($daysofweek as $key => $value)
            @if($view_only)
                @if(is_array($Field->multi_value) && isset($Field->multi_value[$key]) && $Field->multi_value[$key])
                    <div>{{ $value }}</div>
                @endif
            @else
                <span class="{{ $Field->option_wrapper_class }}">
                    <label class="{{ $Field->option_label_class }}" for="{{ $Field->attributes->id }}_{{ $loop->index }}">
                        <input type="checkbox" name="{{ $Field->name }}[]" value="{{ $key }}" id="{{ $Field->attributes->id }}_{{ $loop->index }}" {{ (is_array($Field->multi_value) && isset($Field->multi_value[$key]) && $Field->multi_value[$key] ? 'checked ' : '' )}}/>
                        {{ $value }}
                    </label>
                </span>
            @endif
        @endforeach

        @eloquentforms_include($Field->view_namespace.'::pieces.example')
        @eloquentforms_include($Field->view_namespace.'::pieces.error')
        @eloquentforms_include($Field->view_namespace.'::pieces.note')

    </div>
</div>
