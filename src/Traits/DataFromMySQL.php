<?php
namespace Nickwest\EloquentForms\Traits;

use Illuminate\Support\Facades\DB;

trait DataFromMySQL
{
    /**
     * If we have a MySQL Driver, then query directly to get Enum option values
     *
     * @return void
     */
    protected function setColumnsFromMySQL(): void
    {
        $query = 'SHOW COLUMNS FROM '.$this->getTable();

        foreach (DB::connection($this->connection)->select($query) as $column) {
            $this->columns[$column->Field] = [
                'name' => $column->Field,
                'type' => $this->getSQLType($column->Type),
                'default' => $column->Default,
                'length' => $this->getSQLLength($column->Type),
                'values' => $this->getSQLEnumOptions($column->Type, $column->{'Null'} == 'YES'),
            ];
            $this->valid_columns[$column->Field] = $column->Field;
        }
    }

    /**
     * Isolate and return the column type.
     *
     * @param string $type
     * @return string
     */
    protected function getSQLType(string $type): string
    {
        $types = [
            'int', 'tinyint', 'smallint', 'mediumint', 'bigint', 'decimal',
            'float', 'double', 'real', 'bit', 'boolean', 'serial', 'date',
            'datetime', 'timestamp', 'time', 'year', 'char', 'varchar',
            'tinytext', 'text', 'mediumtext', 'longtext', 'binary', 'varbinary',
            'tinyblob', 'mediumblob', 'blob', 'longblob', 'enum', 'set',
        ];

        foreach ($types as $key) {
            if (strpos($type, $key) === 0) {
                return $key;
            }
        }

        return 'varchar';
    }

    /**
     * Isolate and return the column length.
     *
     * @param string $type
     * @return mixed
     */
    protected function getSQLLength(string $type)
    {
        if (strpos($type, 'enum') === 0) {
            return;
        }

        if (strpos($type, '(') !== false) {
            return substr($type, strpos($type, '(') + 1, strpos($type, ')') - strpos($type, '(') - 1);
        }

        $lengths = [
            'tinytext' => 255,
            'text' => 65535,
            'mediumtext' => 1677215,
            'longtext' => 4294967295,

        ];

        foreach ($lengths as $key => $length) {
            if (strpos($type, $key) === 0) {
                return $length;
            }
        }
    }

    /**
     * Isolate and return the values for enums.
     *
     * @param string $type
     * @param bool $nullable
     * @return mixed
     */
    protected function getSQLEnumOptions(string $type, bool $nullable = false)
    {
        if (strpos($type, 'enum') !== 0) {
            return;
        }
        $values = explode(',', str_replace("'", '', substr($type, strpos($type, '(') + 1, strpos($type, ')') - strpos($type, '(') - 1)));

        $return_array = null;

        foreach ($values as $value) {
            if ($value == '') {
                $return_array[$value] = $this->blank_select_text;
            } else {
                $return_array[$value] = $value;
            }
        }

        if (! isset($return_array['']) && $nullable) {
            $return_array = array_merge(['' => $this->blank_select_text], $return_array);
        }

        return $return_array;
    }
}
