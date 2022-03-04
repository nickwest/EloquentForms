<?php

namespace Nickwest\EloquentForms\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Nickwest\EloquentForms\Table;

class TableExporter implements FromCollection, WithEvents
{
    use Exportable, RegistersEventListeners;

    protected $Table = null;

    public function __construct(Table &$Table)
    {
        $this->Table = $Table;
    }

    public function collection()
    {
        // Get the collection from the table data
        $collection = $this->Table->Collection->map(function ($item) {
            $collection = new Collection($item);

            return $collection->only($this->Table->getDisplayFields())->all();
        });

        $headings = [];

        // Add Headings
        foreach ($this->Table->getDisplayFields() as $field) {
            $headings[] = $this->Table->getLabel($field);
        }
        $collection->prepend($headings);

        return $collection;
    }

    public static function afterSheet(AfterSheet $event)
    {
        $highestRow = $event->sheet->getHighestRow();
        $highestColumn = $event->sheet->getHighestColumn();

        // Set up the page settings
        $event->sheet->getPageSetup()->setFitToWidth(true);
        $event->sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

        // Set the font
        $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setName('Calibri');
        $event->sheet->getDelegate()->getParent()->getDefaultStyle()->getFont()->setSize(16);

        // Set the margins
        self::setMargins($event->sheet, 0.7, 0.25, 0.25, 0.25);

        // Bold the heading row
        $event->sheet->getStyle('A1:'.$highestColumn.'1')->getFont()->setBold(true);

        // Format all rows as text
        $event->sheet->getStyle('A1:'.$highestColumn.$highestRow)
            ->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // Auto wrap all cells by default
        $event->sheet->getStyle('A1:'.$highestColumn.$highestRow)
            ->getAlignment()->setWrapText(true);

        // All cells are text by default
        $event->sheet->getStyle('A1:'.$highestColumn.$highestRow)
            ->getNumberFormat()->setFormatCode('@');

        // Align top
        $event->sheet->getStyle('A1:'.$highestColumn.$highestRow)
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        // Set columns to auto size
        for ($c = 'A'; $c <= $highestColumn; $c++) {
            $event->sheet->getColumnDimension($c)->setAutoSize(true);
        }

        self::applyZebraStriping($event->sheet, $highestRow, $highestColumn);
    }

    /**
     * Apply Zebra striping to the sheet.
     *
     * @param  Maatwebsite\Excel\Sheet  $sheet
     * @param  int  $highestRow
     * @param  string  $highestColumn
     * @return void
     */
    public static function applyZebraStriping(\Maatwebsite\Excel\Sheet &$sheet, int $highestRow, string $highestColumn): void
    {
        // Set Zebra stripes
        for ($i = 2; $i <= $highestRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A'.$i.':'.$highestColumn.$i)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEEEEEE');
            }
        }
    }

    /**
     * Set the margins on a sheet.
     *
     * @param  Maatwebsite\Excel\Sheet  $sheet
     * @param  float  $top
     * @param  float  $right
     * @param  float  $left
     * @param  float  $bottom
     * @return void
     */
    public static function setMargins(\Maatwebsite\Excel\Sheet &$sheet, float $top = 0.7, float $right = 0.25, float $left = 0.25, float $bottom = 0.25): void
    {
        $sheet->getPageMargins()->setTop($top);
        $sheet->getPageMargins()->setRight($right);
        $sheet->getPageMargins()->setLeft($left);
        $sheet->getPageMargins()->setBottom($bottom);
    }
}
