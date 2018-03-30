{{-- The funny tabbing in this file makes for clean HTML output --}}
{{-- This view is rendered by Table::getLinkView() --}}
<td data-field="{{ $field_name }}">@if($Table->hasFieldReplacement($field_name))
{!! $Table->getFieldReplacement($field_name, $row) !!}@elseif(is_object($row))
@if(!in_array($field_name, $Table->raw_fields)){{ $row->$field_name }}@else {!! $row->field_name !!}@endif
@elseif(is_array($row))
@if(!in_array($field_name, $Table->raw_fields)){{ $row[$field_name] }}@else{!! $row[$field_name] !!}@endif
@endif</td>
