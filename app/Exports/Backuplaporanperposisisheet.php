<?php

namespace App\Exports;

use App\Models\LaporanPerformance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;

class Backuplaporanperposisisheet implements FromQuery, WithHeadings, WithColumnFormatting, WithMapping, WithTitle, WithEvents
{
    protected $tahun, $posisi, $namaPosisi;

    public function __construct($tahun, $posisi, $namaPosisi)
    {
        $this->tahun = $tahun;
        $this->posisi = $posisi;
        $this->namaPosisi = $namaPosisi;  
    }

    public function query()
    {
        return LaporanPerformance::query()
            ->where('tahun', $this->tahun)
            ->where('posisi_id', $this->posisi);
    }

    public function headings(): array
    {
        return [
            [$this->namaPosisi], 
            ['Tahun ' . $this->tahun, 'Bulan', 'Target MPP Per Bulan', 'Jumlah Mitra Existing', '% Pemenuhan MPP', 'Jumlah Lamaran Masuk', 
             'Ikut serta Test HR (Konfirmasi Hadir)', 'Interview (Lolos)', 'Ikut Briefing (Training)', 'Tandem', 'PKM Baru', 'PKM Batal Join', 
             'Mitra Keluar / Resign', 'Penambahan / Pengurangan Mitra', 'Target Join tiap Bulan', 'Pencapaian (%)']
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => '0%', 
            'P' => '0%'  
        ];
    }

    public function map($laporan): array
    {
        $rowIndex = $laporan->bulan + 2;
        
        return [
            $this->tahun, 
            date('F', mktime(0, 0, 0, $laporan->bulan, 10)),  
            '', 
            '', 
            '=D'.$rowIndex.'/C'.$rowIndex.'*100%', 
            $laporan->lolos_sortir ?: 0,
            $laporan->konfirmasi_hadir ?: 0,
            $laporan->lolos ?: 0,
            $laporan->training ?: 0,
            $laporan->tandem ?: 0,
            $laporan->PKM_baru ?: 0,
            $laporan->PKM_batal_join ?: 0,
            $laporan->mitra_keluar_resign ?: 0,
            '=K'.$rowIndex.'-M'.$rowIndex,
            '', 
            '=K'.$rowIndex.'/O'.$rowIndex.'*100%' 
        ];
    }

    public function title(): string
    {
        return $this->namaPosisi;
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function(\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

              
               
                $sheet->mergeCells('A2:A14'); 
              
                $sheet->mergeCells('A15:B15'); 
                $sheet->setCellValue('A15', 'Total'); 
                
                
                $sheet->getStyle('A15')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                
                $sheet->getStyle('A2:A14')->applyFromArray([
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'wrapText' => true,
                    ],
                    'font' => [
                        'bold' => true, 
                    ],
                ]);
                
                
                $sheet->getStyle('B2:B14')->getFont()->setBold(true);

                $sheet->getStyle('C2:P2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 
                    ],
                ]);
                
                
                $sheet->getRowDimension(2)->setRowHeight(40);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(15); 
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('M')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(15);
                $sheet->getColumnDimension('N')->setWidth(15);
                $sheet->getColumnDimension('O')->setWidth(15);
                $sheet->getColumnDimension('P')->setWidth(15);
               
                $sheet->getStyle('C2:P2')->getAlignment()->setWrapText(true);
                
                
                $sheet->mergeCells('A1:P1'); 
                $sheet->getStyle('A1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);

                
                $yellowFill = [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FFFF00', 
                        ],
                    ],
                ];
                $sheet->getStyle('A2:A14')->applyFromArray($yellowFill);
                $sheet->getStyle('B2:B14')->applyFromArray($yellowFill);

                
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], 
                        ],
                    ],
                ];

               
                $sheet->getStyle('A1:P15')->applyFromArray($styleArray);

              
                $sheet->setCellValue('C15', '=SUM(C3:C14)');
                $sheet->setCellValue('D15', '=SUM(D3:D14)');
                $sheet->setCellValue('F15', '=SUM(F3:F14)');
                $sheet->setCellValue('G15', '=SUM(G3:G14)');
                $sheet->setCellValue('H15', '=SUM(H3:H14)');
                $sheet->setCellValue('I15', '=SUM(I3:I14)');
                $sheet->setCellValue('J15', '=SUM(J3:J14)');
                $sheet->setCellValue('K15', '=SUM(K3:K14)');
                $sheet->setCellValue('L15', '=SUM(L3:L14)');
                $sheet->setCellValue('M15', '=SUM(M3:M14)');
                $sheet->setCellValue('N15', '=SUM(N3:N14)');
                
                $sheet->setCellValue('O15', '=SUM(O3:O14)');
                
                $sheet->getStyle('C15:O15')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
            }
        ];
    }
}
