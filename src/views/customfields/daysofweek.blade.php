<div id="field-{{ $Field->attributes->id }}" class="field">
    <div class="daysofweek {{ $Field->options_container_class }}">
        <label class="{{ $Field->label_class }}">{!! $Field->label.($Field->label_suffix != '' ? $Field->label_suffix : '').(isset($Field->attributes->required) ? ' <em>*</em>' : '') !!}</label>

        @foreach($daysofweek as $key => $value)
            @if($view_only)
                @if( $Field->attributes->multi_key != '' && $key == $Field->attributes->multi_key)
                    <div>{{ $value }}</div>
                @endif
            @else
                <span class="{{ $Field->option_wrapper_class }}">
                    <label class="{{ $Field->option_label_class }}" for="{{ $Field->attributes->id }}_{{ $loop->index }}">
                        <input type="checkbox" name="{{ $Field->attributes->name }}[]" value="{{ $key }}" id="{{ $Field->attributes->id }}_{{ $loop->index }}" {{ ($Field->attributes->multi_value != '' && $Field->attributes->multi_value == $key ? 'checked ' : '' )}}/>
                        {{ $value }}
                    </label>
                </span>
            @endif
        @endforeach

        @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')

    </div>
</div>
