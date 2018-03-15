@if(isset($extends) && $extends != '')
    @extends($extends)
@endif

@if(isset($section) && $section != '')
    @section($section)
@endif

@yield('above_form')

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
                    <button name="{{ $button->attributes->name }}" id="input-{{ $button->attributes->name }}" class="button {{ $button->attributes->class }}" type="submit" value="{{ $button->attributes->value }}">{{ $button->label }}</button>
                @endforeach
            </p>
        </div>
        @endif
    </fieldset>
</form>

@yield('below_form')

@if(isset($section) && $section != '')
    @stop
@endif