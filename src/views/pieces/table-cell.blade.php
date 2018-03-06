{{-- This view is rendered by Table::getLinkView() --}}
<td>
	@if($Table->getLink($field_name, $row))
		<a href="{!! $Table->getLink($field_name, $row) !!}">
	@endif

    @if($Table->hasFieldReplacement($field_name))
        {!! $Table->getFieldReplacement($field_name, $row) !!}
    @elseif(is_object($row))
	    {{ $row->$field_name }}
    @elseif(is_array($row))
        {{ $row[$field_name] }}
    @endif

	@if($Table->getLink($field_name, $row))
		</a>
	@endif
</td>
