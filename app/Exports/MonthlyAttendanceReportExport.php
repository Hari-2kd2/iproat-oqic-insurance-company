<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class MonthlyAttendanceReportExport implements FromView
{
    use RegistersEventListeners;

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
        // dd($this->view);
        return view($this->view, $this->data);
    }

    public static function afterSheet(AfterSheet $event)
    {
        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => ['argb' => 'FFFF0000'],
                ],
            ],
        ];

        // $event->getSheet()->getDelegate()->getStyle('A1:G1')->applyFromArray($styleArray);
        $event->getSheet()->getDelegate()->getStyle('A1:G1')->getFont()->setName('Calibri')->setSize(14);
    }
}
