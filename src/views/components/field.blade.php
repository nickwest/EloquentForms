<div id="field-{{ $Field->attributes->name.($Field->attributes->multi_key != '' ? '_'.$Field->attributes->multi_key : '') }}" class="type-{{ $Field->attributes->type }} field {{ ($Field->attributes->required ? 'required ' : '') }}{{ $Field->container_class }} {{ $Field->type }}">

	{!! $field_markup !!}

</div>
