{{--  This is a really raw general purpose view that will show raw values as plain text  --}}
@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.label', ['Field' => $Field])

        <div class="value">
            @if(is_array($Field->attributes->value))
                @foreach($Field->attributes->value as $value)
                    @if(count($Field->getOptions()) > 0 && $Field->hasOption($value))
                        {{ $Field->getOption($value) . ($loop->last ? '' : ', ') }}
                    @else
                        {{ $value . ($loop->last ? '' : ', ') }}
                    @endif
                @endforeach
            @else
                @if(count($Field->getOptions()) > 0 && isset($Field->hasOption($Field->attributes->value))
                    {{ $Field->getOption($Field->attributes->value) }}
                @else
                    @if($Field->attributes->type == 'textarea')
                        {!! nl2br(htmlspecialchars($Field->attributes->value)) !!}
                    @else
                        {{ $Field->attributes->value }}
                    @endif
                @endif
            @endif
        </div>

    @endslot
@endcomponent
