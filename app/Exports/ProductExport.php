<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductExport implements FromView, Responsable, ShouldAutoSize, WithEvents, WithTitle
{
    use Exportable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        return view('admin.exports.products-export', ['products' => $this->data]);
    }

    public function title(): string
    {
        return 'Products-Export';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $cellRange = 'A1:N1'; // All headers
                $columnCount = 6; // Replace this with the actual number of columns you have
                $lastColumnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnCount); // Convert the column count to a letter
            
                // Define the cell range dynamically based on the column count
                $cellRange = "A1:$lastColumnLetter" . '1'; // All headers from A1 to the last column and the first row
            
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->getColor()
                    ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('e9ff32');
                $event->sheet->setAutoFilter($cellRange);
            },
        ];
    }
}
