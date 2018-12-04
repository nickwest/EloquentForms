<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class TableExport implements FromCollection, WithEvents
{
    use Exportable, RegistersEventListeners;

    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
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
        $event->sheet->getPageMargins()->setTop(0.7);
        $event->sheet->getPageMargins()->setRight(0.25);
        $event->sheet->getPageMargins()->setLeft(0.25);
        $event->sheet->getPageMargins()->setBottom(0.25);

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

        // Set Zebra stripes
        for ($i = 2; $i <= $highestRow; $i++) {
            if ($i % 2 == 0) {
                $event->sheet->getStyle('A'.$i.':'.$highestColumn.$i)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEEEEEE');
            }
        }
    }
}
