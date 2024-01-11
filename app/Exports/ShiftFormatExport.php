<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class ShiftFormatExport implements WithHeadings, FromCollection, WithProperties, WithEvents
{
    public $data;
    public $extraData;

    public function __construct($data, $extraData)
    {
        $this->data = $data;
        $this->extraData = $extraData;
    }

    public function headings(): array
    {
        return $this->extraData['heading'];
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function registerEvents(): array
    {

        //border style
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    //'color' => ['argb' => 'FFFF0000'],
                ],
            ],
        ];

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

        $styleArray4 = array(
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'startColor' => [
                    'argb' => 'FFA0A0A0',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        );

        $styleArray5 = array(
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                'startColor' => [
                    'argb' => 'E0E0E0',
                ]
            ]
        );

        $styleArray6 = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'D3D3D3'),
                ),
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ffe5ec'),
            ),
        );

        $styleArray7 = array(
            'borders' => array(
                'outline' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'D3D3D3'),
                ),
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'C8F7D4'),
            ),
        );
        $styleArray8 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'D3D3D3'),
                ),
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'E0EFFF'),
            ),
        );

        $styleArray9 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'D3D3D3'),
                ),
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'F4F9FF'),
            ),
        );

        $styleArray10 = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'D3D3D3'),
                ),
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'EAF4FF'),
            ),
        );

        //column  text alignment
        $styleArray11 = array(
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ),
        );

        return [
            AfterSheet::class => function (AfterSheet $event) use ($styleArray, $styleArray1, $styleArray2, $styleArray3, $styleArray4, $styleArray5, $styleArray6, $styleArray7, $styleArray8, $styleArray9, $styleArray10, $styleArray11) {

                $cellRange = $this->extraData['cellRange'];
                $cellRange = 'A1:' . $cellRange[count($cellRange) - 1] . '1'; // All headers
    
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(11);
                // $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray1);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray2);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray3);
                $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray8);
                // $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray4);
                // $event->sheet->getStyle($cellRange)->ApplyFromArray($styleArray5);
    
                // get layout counts (add 1 to rows for heading row)
                $row_count = count($this->data);

                $column_count = count($this->data[0]);

                // set dropdown options
                $options = $this->extraData['shiftName'];

                $cell_range = $this->extraData['cellRange'];

                foreach ($cell_range as $drop_column) {
                    // set dropdown list for first data row
    
                    for ($i = 0; $i < $row_count; $i++) {
                        $j = $i + 2;
                        // if ($j % 2 == 0) {
                        //     $event->sheet->getStyle("{$drop_column}$j")->ApplyFromArray($styleArray10);
                        // } else {
                        //     $event->sheet->getStyle("{$drop_column}$j")->ApplyFromArray($styleArray9);
                        // }
                        $validation = $event->sheet->getCell("{$drop_column}$j")->getDataValidation();
                        $validation->setType(DataValidation::TYPE_LIST);
                        $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                        $validation->setAllowBlank(true);
                        $validation->setShowInputMessage(true);
                        $validation->setShowErrorMessage(true);
                        $validation->setShowDropDown(true);
                        $validation->setErrorTitle('Input error');
                        $validation->setError('Value is not in list.');
                        $validation->setPromptTitle('Pick from list');
                        if (in_array($drop_column, $this->extraData['holidays'])) {
                            $validation->setPrompt('Selected date is Sunday! Are you sure to assign shift?.');
                        } elseif (in_array($drop_column, $this->extraData['ph'])) {
                            $validation->setPrompt('Selected date is holiday! Are you sure to assign shift?.');
                        } else {
                            $validation->setPrompt('Please pick a value from the drop-down list.');
                        }
                        $validation->setFormula1(sprintf('"%s"', implode(',', $options)));
                    }

                    // // clone validation to remaining rows
                    // for ($i = 3; $i <= $row_count; $i++) {
                    //     $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    // }
                }

                // // change color for weekly holidays
                for ($i = 2; $i <= ($row_count + 1); $i++) {
                    foreach ($this->extraData['holidays'] as $key => $value) {
                        $event->sheet->getStyle("{$value}$i")->ApplyFromArray($styleArray6);
                    }
                }

                // // align
                for ($i = 2; $i <= ($row_count + 1); $i++) {
                    $event->sheet->getStyle('A' . $i)->ApplyFromArray($styleArray2);
                    $event->sheet->getStyle('B' . $i)->ApplyFromArray($styleArray2);
                    $event->sheet->getStyle('C' . $i)->ApplyFromArray($styleArray2);
                }

                // // set columns to autosize
                for ($i = 1; $i <= 3; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }

            },
        ];

    }

    public function properties(): array
    {
        return [
            'creator' => 'DUROFLEX PVT LTD, FRN.',
            'lastModifiedBy' => 'DUROFLEX PVT LTD, FRN.',
            'title' => 'EmployeeShiftInfo',
            'description' => 'DUROFLEX PVT LTD, FRN. - EmployeeShiftInfo',
            'subject' => 'DUROFLEX PVT LTD, FRN. - EmployeeShiftInfo',
            'keywords' => 'EmployeeShiftInfo,export,spreadsheet',
            'category' => 'EmployeeShiftInfo',
            'manager' => 'DUROFLEX PVT LTD',
            'company' => 'DUROFLEX PVT LTD , FRN.',
        ];
    }
}