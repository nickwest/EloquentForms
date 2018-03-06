@eloquentforms_component($Field->view_namespace.'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        @include($Field->view_namespace.'::pieces.label', ['Field' => $Field])

        @if($view_only)
            <div class="file-link value">
                @if($Field->link != '' && $Field->value != '')<a href="{{ $Field->link }}">@endif
                {{ $Field->value }}
                @if($Field->link != '' && $Field->value != '')</a>@endif
            </div>
        @elseif($Field->value == '')
            <input {!! $Field->attributes !!} />
        @else
            <div class="file-link">
                @if($Field->link != '' && $Field->value != '')<a href="{{ $Field->link }}">@endif
                {{ $Field->value }}
                @if($Field->link != '' && $Field->value != '')</a>@endif
                <input type="submit" value="{{ $Field->delete_button_value }}" name="{{ $Field->attributes->name.($Field->attributes->multi_key ? ($Field->attributes->multi_key === true ? '[]' : '['.$Field->attributes->multi_key.']') : '') }}" />
            </div>
        @endif

        @eloquentforms_include($Field->view_namespace.'::pieces.example')
        @eloquentforms_include($Field->view_namespace.'::pieces.error')
        @eloquentforms_include($Field->view_namespace.'::pieces.note')
    @endslot

@endcomponent
