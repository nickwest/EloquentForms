@if($Field->is_inline && !$prev_inline)
    @include($Field->view_namespace.'::components.inline-start')
@endif

<div id="field-{{ $Field->attributes->name.($Field->attributes->multi_key != '' ? '_'.$Field->attributes->multi_key : '') }}" class="type-{{ $Field->attributes->type }} field {{ ($Field->attributes->required ? 'required ' : '') }}{{ $Field->container_class }}">
	{!! $field_markup !!}
</div>

@if(!$Field->is_inline && $prev_inline)
    @include($Field->view_namespace.'::components.inline-end')
@endif
