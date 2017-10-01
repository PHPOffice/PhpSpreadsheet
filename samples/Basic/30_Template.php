<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

require __DIR__ . '/../Header.php';

$helper->log('Load from Xls template');
$reader = IOFactory::createReader('Xls');
$spreadsheet = $reader->load(__DIR__ . '/../templates/30template.xls');

$helper->log('Add new data to the template');
$data = [['title' => 'Excel for dummies',
        'price' => 17.99,
        'quantity' => 2,
    ],
    ['title' => 'PHP for dummies',
        'price' => 15.99,
        'quantity' => 1,
    ],
    ['title' => 'Inside OOP',
        'price' => 12.95,
        'quantity' => 1,
    ],
];

$spreadsheet->getActiveSheet()->setCellValue('D1', Date::PHPToExcel(time()));

$baseRow = 5;
foreach ($data as $r => $dataRow) {
    $row = $baseRow + $r;
    $spreadsheet->getActiveSheet()->insertNewRowBefore($row, 1);

    $spreadsheet->getActiveSheet()->setCellValue('A' . $row, $r + 1)
            ->setCellValue('B' . $row, $dataRow['title'])
            ->setCellValue('C' . $row, $dataRow['price'])
            ->setCellValue('D' . $row, $dataRow['quantity'])
            ->setCellValue('E' . $row, '=C' . $row . '*D' . $row);
}
$spreadsheet->getActiveSheet()->removeRow($baseRow - 1, 1);

// Save
$helper->write($spreadsheet, __FILE__);
