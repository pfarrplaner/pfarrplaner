<?php

require_once('vendor/autoload.php');

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('d:\downloads\Plan2019.xlsx');
$sheet = $spreadsheet->getActiveSheet();

for ($i=1; $i<=20; $i++) {
    $column = chr(64+$i);
    $width = $sheet->getColumnDimension($column)->getWidth();
    echo "{$column}: {$width}\r\n";
}
