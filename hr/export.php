<?php
// Prevent any output before headers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/cuti-calculator.php';
require_once __DIR__ . '/../includes/reports-data.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Get reports
$reports = initReports();

if (count($reports) === 0) {
    header('Location: dashboard.php?export_error=empty');
    exit;
}

// Define color constants (ARGB format)
$COLOR_DEEP_TEAL  = 'FF0F4C5C';
$COLOR_WARM_AMBER = 'FFE8A838';
$COLOR_WHITE      = 'FFFFFFFF';
$COLOR_TEXT       = 'FF1E293B';
$COLOR_BORDER     = 'FFE2E8F0';

// Define reusable style arrays
$brandingStyle = [
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['argb' => $COLOR_WARM_AMBER],
    ],
];

$timestampStyle = [
    'font' => [
        'italic' => true,
        'size' => 10,
        'color' => ['argb' => $COLOR_TEXT],
    ],
];

$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => $COLOR_WHITE],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => $COLOR_DEEP_TEAL],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'bottom' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => $COLOR_WHITE],
        ],
    ],
];

$dataStyle = [
    'font' => [
        'size' => 11,
        'color' => ['argb' => $COLOR_TEXT],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_LEFT,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'bottom' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => $COLOR_BORDER],
        ],
    ],
];

$spreadsheet = new Spreadsheet();
$currentYear = (int)date('Y');

// --- Sheet 1: "Ringkasan" ---
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Ringkasan');

// Row 1 - Branding
$sheet1->mergeCells('A1:E1');
$sheet1->setCellValue('A1', 'Sicuti HRD — Laporan Cuti Karyawan');
$sheet1->getStyle('A1')->applyFromArray($brandingStyle);

// Row 2 - Timestamp
$sheet1->mergeCells('A2:E2');
$sheet1->setCellValue('A2', 'Diekspor: ' . date('d/m/Y H:i'));
$sheet1->getStyle('A2')->applyFromArray($timestampStyle);

// Row 3 - Empty spacer

// Row 4 - Column headers
$sheet1->setCellValue('A4', 'No.');
$sheet1->setCellValue('B4', 'Nama Karyawan');
$sheet1->setCellValue('C4', 'Tahun Bergabung');
$sheet1->setCellValue('D4', 'Total Hari Cuti (8 Tahun)');
$sheet1->setCellValue('E4', 'Status Tahun Ini');
$sheet1->getStyle('A4:E4')->applyFromArray($headerStyle);

// Data rows
$rowNum = 5;
foreach ($reports as $index => $report) {
    $calc = hitungHakCuti($report['tahun_bergabung']);
    $statusTahunIni = '—';
    foreach ($calc['data'] as $row) {
        if ($row['tahun_kalender'] == $currentYear) {
            $statusTahunIni = $row['status'];
            break;
        }
    }

    $sheet1->setCellValue('A' . $rowNum, $index + 1);
    $sheet1->setCellValue('B' . $rowNum, $report['nama']);
    $sheet1->setCellValue('C' . $rowNum, $report['tahun_bergabung']);
    $sheet1->setCellValue('D' . $rowNum, $report['total_cuti']);
    $sheet1->setCellValue('E' . $rowNum, $statusTahunIni);
    
    $sheet1->getStyle('A' . $rowNum . ':E' . $rowNum)->applyFromArray($dataStyle);
    // Center align for some columns
    $sheet1->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $rowNum++;
}

// Auto-size columns for Sheet 1
foreach (range('A', 'E') as $col) {
    $sheet1->getColumnDimension($col)->setAutoSize(true);
}

// --- Sheet 2: "Detail" ---
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Detail');

// Row 1 - Branding
$sheet2->mergeCells('A1:F1');
$sheet2->setCellValue('A1', 'Sicuti HRD — Detail Entitlement Karyawan');
$sheet2->getStyle('A1')->applyFromArray($brandingStyle);

// Row 2 - Timestamp
$sheet2->mergeCells('A2:F2');
$sheet2->setCellValue('A2', 'Diekspor: ' . date('d/m/Y H:i'));
$sheet2->getStyle('A2')->applyFromArray($timestampStyle);

// Row 3 - Empty spacer

// Row 4 - Column headers
$sheet2->setCellValue('A4', 'Nama Karyawan');
$sheet2->setCellValue('B4', 'Tahun Bergabung');
$sheet2->setCellValue('C4', 'Tahun Ke');
$sheet2->setCellValue('D4', 'Tahun Kalender');
$sheet2->setCellValue('E4', 'Hari Cuti');
$sheet2->setCellValue('F4', 'Status');
$sheet2->getStyle('A4:F4')->applyFromArray($headerStyle);

// Data rows
$rowNum = 5;
foreach ($reports as $report) {
    $calc = hitungHakCuti($report['tahun_bergabung']);
    foreach ($calc['data'] as $row) {
        $sheet2->setCellValue('A' . $rowNum, $report['nama']);
        $sheet2->setCellValue('B' . $rowNum, $report['tahun_bergabung']);
        $sheet2->setCellValue('C' . $rowNum, $row['tahun_ke']);
        $sheet2->setCellValue('D' . $rowNum, $row['tahun_kalender']);
        $sheet2->setCellValue('E' . $rowNum, $row['hari_cuti']);
        $sheet2->setCellValue('F' . $rowNum, $row['status']);
        
        $sheet2->getStyle('A' . $rowNum . ':F' . $rowNum)->applyFromArray($dataStyle);
        // Center align for some columns
        $sheet2->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum++;
    }
}

// Auto-size columns for Sheet 2
foreach (range('A', 'F') as $col) {
    $sheet2->getColumnDimension($col)->setAutoSize(true);
}

// Set active sheet back to Sheet 1
$spreadsheet->setActiveSheetIndex(0);

// Stream download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan-cuti-sicuti-hrd.xlsx"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
