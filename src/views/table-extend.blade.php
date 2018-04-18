@if(isset($extends) && $extends != '')
    @extends($extends)
@endif

@if(isset($section) && $section != '')
    @section($section)
@endif

@yield('above_table')

<table {!! $Table->attributes !!}>
    <thead>
        <tr>
        @foreach($Table->getDisplayFields() as $field_name)
            <th>{{ $Table->getLabel($field_name) }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($Table->Collection as $row)
        <tr>
            @foreach($Table->getDisplayFields() as $field_name)
                @eloquentforms_include($Table->getTheme()->getViewNamespace().'::pieces.table-cell', ['row' => $row, 'field_name' => $field_name, 'Table' => $Table])
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

@yield('below_table')

@if(isset($section) && $section != '')
    @stop
@endif
