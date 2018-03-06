@if(isset($extends) && $extends != '')
    @extends($extends)
@endif

@if(isset($section) && $section != '')
    @section($section)
@endif

@yield('above_form')

<form method="{{ $Form->method }}" action="{{ $Form->url }}" id="{{ $Form->id_attr }}"{!! ($Form->multipart ? ' enctype="multipart/form-data"' : '') !!}>
    @if($Form->laravel_csrf && function_exists('csrf_field'))
        {{ csrf_field() }}
    @endif
    <div class="fields">
        @php($prev_inline = false)
        @foreach($Form->getDisplayFields() as $Field)
            @if(!$Field->is_subform)
                {!! $Field->makeView($prev_inline, $view_only) !!}
            @else
                {!! $Field->subform->makeSubformView($Field->subform_data, $view_only)->render() !!}
            @endif

            @php($prev_inline = $Field->is_inline ? true: false)
        @endforeach

        @if(!$view_only)
        <div class="field submit-buttons">
            <p class="control">
            @if(count($Form->getSubmitButtons()) > 0)
                @foreach($Form->getSubmitButtons() as $button)
                    <input name="{{ $button['name'] }}" id="submit_button_{{ $button['name'] }}" class="button is-success {{ $button['class'] }}" type="submit" value="{{ $button['label'] }} "/>
                @endforeach
            @else
                <input name="submit_button" id="submit_button_save" class="button is-success" type="submit" value="{{ (isset($Form->submit_button) ? $Form->submit_button : 'Save') }} "/>
            @endif

            @if($Form->getAllowDelete())
                <input name="submit_button" id="submit_button_delete" class="button is-danger" type="submit" value="{{ (isset($Form->delete_button) ? $Form->delete_button : 'Delete') }}" />
            @endif
            </p>
        </div>
        @endif
    </fieldset>
</form>

@yield('below_form')

@if(isset($section) && $section != '')
    @stop
@endif
