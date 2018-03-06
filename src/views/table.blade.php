<table class="{{ $Table->getClassesString() }}">
    <thead>
        <tr>
        @foreach($Table->display_fields as $field_name)
            <th>{{ $Table->getLabel($field_name) }}</th>
        @endforeach
        </tr>
    </thead>
    <tobdy>
        @foreach($Table->Collection as $row)
        <tr>
            @foreach($Table->display_fields as $field_name)
                @eloquentforms_include($Table->view_namespace.'::pieces.table-cell', ['row' => $row, 'field_name' => $field_name, 'Table' => $Table])
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
