<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Services\ClientService;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

Auth::requireAuth();

$service = new ClientService();

$filters = [
  'q' => trim($_GET['q'] ?? ''),
  'status' => trim($_GET['status'] ?? ''),
  'country' => trim($_GET['country'] ?? ''),
  'date_from' => trim($_GET['date_from'] ?? ''),
  'date_to' => trim($_GET['date_to'] ?? ''),
];

$clients = $service->search($filters);
$format = $_GET['format'] ?? 'xlsx';

$columns = ['Date Registered', 'Full Name', 'Email', 'Phone', 'Country of Origin', 'Purpose of Visit', 'Status', 'Scheduled/Appointment Date'];

$rows = [];
foreach ($clients as $client) {
  $rows[] = [
    $client['created_at'],
    $client['full_name'],
    $client['email'] ?? '',
    $client['phone'] ?? '',
    $client['country_of_origin'] ?? '',
    $client['purpose_of_visit'] ?? '',
    ucfirst($client['status']),
    $client['scheduled_date'] ?? '',
  ];
}

$dateSuffix = date('Y-m-d');

if ($format === 'pdf') {
  $html = '<html><head><meta charset="utf-8"><style>'
    . 'body{font-family: Arial, Helvetica, sans-serif; font-size: 11px;}'
    . 'h2{font-size:16px;margin:0 0 10px;}'
    . 'table{border-collapse: collapse; width: 100%;}'
    . 'th, td{border: 1px solid #999; padding: 5px 7px; text-align: left;}'
    . 'th{background: #4318ff; color: #fff;}'
    . 'tr:nth-child(even) td{background: #f5f5f9;}'
    . '</style></head><body>';
  $html .= '<h2>Pentagon Safaris — Client Report (' . htmlspecialchars($dateSuffix) . ')</h2>';
  $html .= '<table><thead><tr>';
  foreach ($columns as $col) {
    $html .= '<th>' . htmlspecialchars($col) . '</th>';
  }
  $html .= '</tr></thead><tbody>';
  foreach ($rows as $row) {
    $html .= '<tr>';
    foreach ($row as $cell) {
      $html .= '<td>' . htmlspecialchars((string) $cell) . '</td>';
    }
    $html .= '</tr>';
  }
  if (empty($rows)) {
    $html .= '<tr><td colspan="' . count($columns) . '">No clients match this search.</td></tr>';
  }
  $html .= '</tbody></table></body></html>';

  $options = new Options();
  $options->set('isRemoteEnabled', false);
  $dompdf = new Dompdf($options);
  $dompdf->setPaper('A4', 'landscape');
  $dompdf->loadHtml($html);
  $dompdf->render();

  while (ob_get_level() > 0) {
    ob_end_clean();
  }
  header('Content-Type: application/pdf');
  header('Content-Disposition: attachment; filename="clients-' . $dateSuffix . '.pdf"');
  echo $dompdf->output();
  exit;
}

// Default: xlsx
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Clients');

foreach ($columns as $i => $col) {
  $cell = $sheet->getCellByColumnAndRow($i + 1, 1);
  $cell->setValue($col);
  $cell->getStyle()->getFont()->setBold(true);
}

foreach ($rows as $rowIndex => $row) {
  foreach ($row as $colIndex => $value) {
    $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 2, $value);
  }
}

foreach (range(1, count($columns)) as $colIndex) {
  $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
}

while (ob_get_level() > 0) {
  ob_end_clean();
}
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="clients-' . $dateSuffix . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
