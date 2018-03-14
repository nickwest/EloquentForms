<option
	value="{{ $key }}"
	{{ ($Field->attributes->value == $key ? ' selected' : '') }}
	{{ ($Field->attributes->multi_key != '' && $key == $Field->attributes->multi_key ? ' selected' : '') }}
>
	{{ $value }}
</option>
