<div id="field-{{ $Field->attributes->id }}" class="field">
    <div class="daysofweek {{ $Field->options->container_class }}">
        <label class="{{ $Field->label_class }}">{!! $Field->label.($Field->label_suffix != '' ? $Field->label_suffix : '').(isset($Field->attributes->required) ? ' <em>*</em>' : '') !!}</label>

        @foreach($daysofweek as $key => $value)
            @if($view_only)
                @if( is_array($Field->attributes->value) && in_array($key, $Field->attributes->value))
                    <div>{{ $value }}</div>
                @endif
            @else
                <span class="{{ $Field->options->wrapper_class }}">
                    <label class="{{ $Field->options->label_class }}" for="{{ $Field->attributes->id }}_{{ $loop->index }}">
                        <input type="checkbox" name="{{ $Field->attributes->name }}[]" value="{{ $key }}" id="{{ $Field->attributes->id }}_{{ $loop->index }}" {{ (is_array($Field->attributes->value) && in_array($key, $Field->attributes->value) ? 'checked ' : '' )}}/>
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
