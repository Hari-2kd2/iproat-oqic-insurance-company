<?php

namespace App\Exports;

use App\Lib\Enumerations\AppConstants;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceMusterReportExport implements FromCollection, WithHeadings, WithProperties, WithEvents
{

    use Exportable;

    public $data;
    public $extraData;

    public function __construct($data, $extraData)
    {
        $this->data = $data;
        $this->extraData = $extraData;
    }

    public function collection()
    {

        return collect($this->data);
    }

    public function headings(): array
    {

        return $this->extraData['heading'];
    }

    public function registerEvents(): array
    {

        //font style
        $styleArray1 = [
            'font' => [
                'bold' => true,
            ],
        ];

        //column  text alignment
        $styleArray2 = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ),
        );

        //$styleArray3 used for vertical alignment
        $styleArray3 = array(
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
        );

        //column  text alignment
        $styleArray4 = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ),
        );
        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $styleArray1,
                $styleArray2,
                $styleArray3,
                $styleArray4
            ) {
                $cellRangeArr = [];

                // get layout counts (add 1 to rows for heading row)
                $row_count = count($this->data) + 1;
                $column_count = count($this->data[0]);
                // dd($row_count, $column_count);

                // // set columns to autosize
                for ($i = 1; $i <= 2; $i++) {
                    for ($i = 1; $i <= $column_count; $i++) {
                        $column = Coordinate::stringFromColumnIndex($i);
                        array_push($cellRangeArr, $column);
                        $event->sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }

                for ($i = 1; $i <= 1; $i++) {
                    $cellRange = $cellRangeArr[0] . $i . ':' . $cellRangeArr[count($cellRangeArr) - 1] . $i; // All headers
                    $event->sheet->getDelegate()->setMergeCells([$cellRange]);
                    $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                    $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray1);
                    $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray2);
                    $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray3);
                }

                for ($i = 2; $i <= 2; $i++) {
                    $cellRange = $cellRangeArr[0] . $i . ':' . $cellRangeArr[count($cellRangeArr) - 1] . $i; // All headers
                    $event->sheet->setAutoFilter($cellRange);
                    $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(12  );
                    $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray1);
                    $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray2);
                    $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray4);
                }

            },
        ];

    }

    public function properties(): array
    {
        return [
            //
        ];
    }
}
