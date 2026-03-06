<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth-guard.php';
cekLogin();
cekRole('hr');
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/cuti-calculator.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

$filter_status = isset($_GET['filter_status']) ? trim((string) $_GET['filter_status']) : '';
$allowed_filters = ['Tersedia', 'Menunggu', 'Rencana'];
if (!in_array($filter_status, $allowed_filters, true)) {
    $filter_status = '';
}

$currentYear = (int) date('Y');
$reports = [];

$sql = 'SELECT id, nik, nama, tanggal_bergabung FROM karyawan ORDER BY nik ASC, nama ASC';
$result = mysqli_query($koneksi, $sql);

if ($result instanceof mysqli_result) {
    while ($karyawan = mysqli_fetch_assoc($result)) {
        $tanggal_bergabung = isset($karyawan['tanggal_bergabung']) ? trim((string) $karyawan['tanggal_bergabung']) : '';

        if ($tanggal_bergabung === '' || strtotime($tanggal_bergabung) === false) {
            continue;
        }

        $tahun_bergabung = (int) date('Y', strtotime($tanggal_bergabung));
        $calc = hitungHakCuti($tahun_bergabung);
        $status_tahun_ini = '—';

        foreach ($calc['data'] as $row) {
            if ((int) $row['tahun_kalender'] === $currentYear) {
                $status_tahun_ini = $row['status'];
                break;
            }
        }

        if ($filter_status !== '' && $status_tahun_ini !== $filter_status) {
            continue;
        }

        $reports[] = [
            'id' => (int) $karyawan['id'],
            'nik' => $karyawan['nik'],
            'nama' => $karyawan['nama'],
            'tahun_bergabung' => $tahun_bergabung,
            'total_cuti' => array_sum(array_column($calc['data'], 'hari_cuti')),
            'status_tahun_ini' => $status_tahun_ini,
            'detail_rows' => $calc['data'],
        ];
    }

    mysqli_free_result($result);
}

if (count($reports) === 0) {
    $_SESSION['flash'] = [
        'type' => 'info',
        'message' => 'Belum ada data karyawan yang bisa diekspor dari laporan live.'
    ];

    $redirect = 'Location: laporan.php';
    if ($filter_status !== '') {
        $redirect .= '?filter_status=' . urlencode($filter_status);
    }

    header($redirect);
    exit;
}

$COLOR_DEEP_TEAL = 'FF0F4C5C';
$COLOR_WARM_AMBER = 'FFE8A838';
$COLOR_WHITE = 'FFFFFFFF';
$COLOR_TEXT = 'FF1E293B';
$COLOR_BORDER = 'FFE2E8F0';

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

$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('Ringkasan');

$sheet1->mergeCells('A1:F1');
$sheet1->setCellValue('A1', 'Sicuti HRD — Laporan Cuti Karyawan');
$sheet1->getStyle('A1')->applyFromArray($brandingStyle);

$sheet1->mergeCells('A2:F2');
$sheet1->setCellValue('A2', 'Diekspor: ' . date('d/m/Y H:i'));
$sheet1->getStyle('A2')->applyFromArray($timestampStyle);

$sheet1->setCellValue('A4', 'No.');
$sheet1->setCellValue('B4', 'NIK');
$sheet1->setCellValue('C4', 'Nama Karyawan');
$sheet1->setCellValue('D4', 'Tahun Bergabung');
$sheet1->setCellValue('E4', 'Total Hari Cuti (8 Tahun)');
$sheet1->setCellValue('F4', 'Status Tahun Ini');
$sheet1->getStyle('A4:F4')->applyFromArray($headerStyle);

$rowNum = 5;
foreach ($reports as $index => $report) {
    $sheet1->setCellValue('A' . $rowNum, $index + 1);
    $sheet1->setCellValue('B' . $rowNum, $report['nik']);
    $sheet1->setCellValue('C' . $rowNum, $report['nama']);
    $sheet1->setCellValue('D' . $rowNum, $report['tahun_bergabung']);
    $sheet1->setCellValue('E' . $rowNum, $report['total_cuti']);
    $sheet1->setCellValue('F' . $rowNum, $report['status_tahun_ini']);

    $sheet1->getStyle('A' . $rowNum . ':F' . $rowNum)->applyFromArray($dataStyle);
    $sheet1->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $rowNum++;
}

foreach (range('A', 'F') as $col) {
    $sheet1->getColumnDimension($col)->setAutoSize(true);
}

$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('Detail');

$sheet2->mergeCells('A1:G1');
$sheet2->setCellValue('A1', 'Sicuti HRD — Detail Entitlement Karyawan');
$sheet2->getStyle('A1')->applyFromArray($brandingStyle);

$sheet2->mergeCells('A2:G2');
$sheet2->setCellValue('A2', 'Diekspor: ' . date('d/m/Y H:i'));
$sheet2->getStyle('A2')->applyFromArray($timestampStyle);

$sheet2->setCellValue('A4', 'NIK');
$sheet2->setCellValue('B4', 'Nama Karyawan');
$sheet2->setCellValue('C4', 'Tahun Bergabung');
$sheet2->setCellValue('D4', 'Tahun Ke');
$sheet2->setCellValue('E4', 'Tahun Kalender');
$sheet2->setCellValue('F4', 'Hari Cuti');
$sheet2->setCellValue('G4', 'Status');
$sheet2->getStyle('A4:G4')->applyFromArray($headerStyle);

$rowNum = 5;
foreach ($reports as $report) {
    foreach ($report['detail_rows'] as $row) {
        $sheet2->setCellValue('A' . $rowNum, $report['nik']);
        $sheet2->setCellValue('B' . $rowNum, $report['nama']);
        $sheet2->setCellValue('C' . $rowNum, $report['tahun_bergabung']);
        $sheet2->setCellValue('D' . $rowNum, $row['tahun_ke']);
        $sheet2->setCellValue('E' . $rowNum, $row['tahun_kalender']);
        $sheet2->setCellValue('F' . $rowNum, $row['hari_cuti']);
        $sheet2->setCellValue('G' . $rowNum, $row['status']);

        $sheet2->getStyle('A' . $rowNum . ':G' . $rowNum)->applyFromArray($dataStyle);
        $sheet2->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle('G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNum++;
    }
}

foreach (range('A', 'G') as $col) {
    $sheet2->getColumnDimension($col)->setAutoSize(true);
}

$spreadsheet->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan-cuti-sicuti-hrd.xlsx"');
header('Cache-Control: max-age=0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
