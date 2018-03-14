<form {!! $Form->attributes !!}>
    @if($Form->laravel_csrf && function_exists('csrf_field'))
        {{ csrf_field() }}
    @endif
    <div class="fields">
        @php($prev_inline = false)
        @foreach($Form->getDisplayFields() as $Field)
            @if(!$Field->isSubform())
                {!! $Field->makeView($prev_inline, $view_only) !!}
            @else
                {!! $Field->Subform->makeSubformView($Field->subform_data, $view_only)->render() !!}
            @endif
            @php($prev_inline = $Field->is_inline ? true: false)
        @endforeach

        @if(!$view_only)
        <div class="field submit-buttons">
            <p class="control">
                @foreach($Form->getSubmitButtons() as $button)
                    <input name="{{ $button->attributes->name }}" id="submit_button_{{ $button->attributes->name }}" class="button is-success {{ $button->attributes->class }}" type="submit" value="{{ $button->label }}"/>
                @endforeach
            </p>
        </div>
        @endif
    </div>
</form>
