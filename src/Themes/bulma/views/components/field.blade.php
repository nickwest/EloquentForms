@if($Field->is_inline && !$prev_inline)
    @include($Field->getViewNamespace().'::components.inline-start')
@endif

<div id="field-{{ $Field->attributes->name.($Field->attributes->multi_key != '' ? '_'.$Field->attributes->multi_key : '') }}" class="type-{{ $Field->attributes->type }} field {{ (isset($Field->attributes->required) ? 'required ' : '') }}{{ $Field->container_class }} {{ $Field->attributes->type }} {{ (isset($display_only) ? 'display_only' : '') }}">
        {!! $field_markup !!}
</div>

@if(!$Field->is_inline && $prev_inline)
    @include($Field->getViewNamespace().'::components.inline-end')
@endif
