@eloquentforms_component($Field->getViewNamespace().'::components.field', ['Field' => $Field, 'prev_inline' => $prev_inline])

    @slot('field_markup')
        <label class="{{ $Field->label_class }}" for="{{ $Field->attributes->id }}">
            {{ $Field->label }}{{ $Field->label_suffix ? $Field->label_suffix : '' }}
        </label>
        @if($view_only)
            <div class="file">{{ $Field->attributes->value }}</div>
            {{--  TODO: Link the file somehow...  --}}
        @else
            <div class="file has-name is-fullwidth">
                <label class="file-label">
                    @if($Field->attributes->value == '')
                        <input {!! $Field->attributes !!} />
                    <span class="file-cta">
                        <span class="file-icon">
                            <i class="fa fa-upload"></i>
                        </span>
                        <span class="file-label">
                            Choose a file…
                        </span>
                    </span>
                    @else
                    <span class="file-name">
                        {{ $Field->attributes->value }}
                    </span>
                    @endif
                    @if($Field->attributes->value != '')
                        <input class="button is-danger" type="submit" value="{{ $Field->file_delete_button_value }}" name="{{ $Field->attributes->name }}" />
                    @endif
                </label>
            </div>
        @endif

        @eloquentforms_include($Field->getViewNamespace().'::pieces.example')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.error')
        @eloquentforms_include($Field->getViewNamespace().'::pieces.note')
    @endslot

@endcomponent
