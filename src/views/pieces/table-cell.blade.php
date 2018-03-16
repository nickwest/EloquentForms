{{-- The funny tabbing in this file makes for clean HTML output --}}
{{-- This view is rendered by Table::getLinkView() --}}
<td>@if($Table->hasFieldReplacement($field_name))
{!! $Table->getFieldReplacement($field_name, $row) !!}
    @elseif(is_object($row))
{{ $row->$field_name }}
    @elseif(is_array($row))
{{ $row[$field_name] }}@endif</td>
