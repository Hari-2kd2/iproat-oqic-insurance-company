<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SummaryAttendanceReportExport implements FromView, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners, Exportable;

    public $data;
    public $view;

    public function __construct($view, $data)
    {
        $this->data = $data;
        $this->view = $view;
    }

    public function view(): View
    {
        \set_time_limit(0);
        return view($this->view, $this->data);
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class => function (AfterSheet $event) {
                /** @var Sheet $sheet */
                $sheet = $event->sheet;
                $sheet->mergeCells('A1:AU1');
                // $sheet->mergeCells('C2:AU2');
                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $cellRange = 'A1:AU1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(16);
                // $event->sheet->getDelegate()->getStyle('A1:AU1')->getAlignment()->setWrapText(true);
            },
        ];
    }


    // public static function afterSheet(AfterSheet $event)
    // {
    //     $styleArray = [
    //         'borders' => [
    //             'outline' => [
    //                 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
    //                 'color' => ['argb' => 'FFFF0000'],
    //             ],
    //         ],
    //     ];

    //     // $event->getSheet()->getDelegate()->getStyle('A1:G1')->applyFromArray($styleArray);
    //     $event->getSheet()->getDelegate()->getStyle('A1:G1')->getFont()->setName('Calibri')->setSize(14);
    // }
}
