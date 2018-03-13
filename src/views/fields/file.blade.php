@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @include($Field->getViewNamespace().'::pieces.label', ['Field' => $Field])

        @if($view_only)
            <div class="file-link value">
                @if($Field->link != '' && $Field->attributes->value != '')<a href="{{ $Field->link }}">@endif
                {{ $Field->attributes->value }}
                @if($Field->link != '' && $Field->attributes->value != '')</a>@endif
            </div>
        @elseif($Field->attributes->value == '')
            <input {!! $Field->attributes !!} />
        @else
            <div class="file-link">
                @if($Field->link != '' && $Field->attributes->value != '')<a href="{{ $Field->link }}">@endif
                {{ $Field->attributes->value }}
                @if($Field->link != '' && $Field->attributes->value != '')</a>@endif
                <input type="submit" value="{{ $Field->file_delete_button_value }}" name="{{ $Field->attributes->name.($Field->attributes->multi_key ? ($Field->attributes->multi_key === true ? '[]' : '['.$Field->attributes->multi_key.']') : '') }}" />
            </div>
        @endif

        @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
