<?php

require __DIR__ . '/../Header.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Accounting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Currency;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\CurrencyBase;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\CurrencyNegative;

$spreadsheet = new Spreadsheet();

$helper->log('First sheet - Accounting Wizard');
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Accounting');
$sheet->getCell('A1')->setValue('Currency');
$sheet->getCell('B1')->setValue('Decimals');
$sheet->getCell('C1')->setValue('ThouSep');
$sheet->getCell('D1')->setValue('Lead');
$sheet->getCell('E1')->setValue('Spacing');
$sheet->getCell('F1')->setValue('Neg');
$sheet->getCell('G1')->setValue('Pos');
$sheet->getCell('H1')->setValue('Zero');
$sheet->getCell('I1')->setValue('Neg');
$sheet->getCell('J1')->setValue('Text');
$sheet->getCell('L1')->setValue('ActWiz$');
$sheet->getCell('M1')->setValue('ActWiz€Trl');
$sheet->freezePane('A2');
$sheet->getComment('E1')->getText()->createText('ignored, always true for Accounting');
$sheet->getComment('F1')->getText()->createText('ignored, always () for Accounting');

$sheet->getCell('A2')->setValue('AcctUSD');
$sheet->getCell('G2')->setValue(1234.56);
$sheet->getCell('H2')->setValue(0);
$sheet->getCell('I2')->setValue(-1234.56);
$sheet->getCell('J2')->setValue('text');
$sheet->getStyle('G2:J2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_USD);

$sheet->getCell('A3')->setValue('AcctEur');
$sheet->getCell('G3')->setValue(1234.56);
$sheet->getCell('H3')->setValue(0);
$sheet->getCell('I3')->setValue(-1234.56);
$sheet->getCell('J3')->setValue('Text');
$sheet->getStyle('G3:J3')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_EUR);

$sheet->getCell('A4')->setValue('AcctWiz￥');
$sheet->getCell('E4')->setValue(true);
$sheet->getCell('G4')->setValue(1234.56);
$sheet->getCell('H4')->setValue(0);
$sheet->getCell('I4')->setValue(-1234.56);
$sheet->getCell('J4')->setValue('Text');
$sheet->getStyle('G4:J4')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Accounting('￥', currencySymbolSpacing: true))->format(),
        ],
    ]
);

$sheet->getCell('A5')->setValue('StalePR￥');
$sheet->getCell('G5')->setValue(1234.56);
$sheet->getCell('H5')->setValue(0);
$sheet->getCell('I5')->setValue(-1234.56);
$sheet->getCell('J5')->setValue('Text');
$sheet->getStyle('G5:J5')->getNumberFormat()->setFormatCode('_("￥"* #,##0.00_);_("￥"* -#,##0.00_);_("￥"* "-"??_);_(@_)');

$sheet->getCell('A6')->setValue('AcctWiz￥');
$sheet->getCell('E6')->setValue(true);
$sheet->getCell('F6')->setValue(CurrencyNegative::minus->name);
$sheet->getCell('G6')->setValue(1234.56);
$sheet->getCell('H6')->setValue(0);
$sheet->getCell('I6')->setValue(-1234.56);
$sheet->getCell('J6')->setValue('Text');
$sheet->getStyle('G6:J6')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Accounting('￥', currencySymbolSpacing: true, negative: CurrencyNegative::minus))->format(),
        ],
    ]
);

$sheet->getCell('A7')->setValue('AcctWiz￥');
$sheet->getCell('E7')->setValue(false);
$sheet->getCell('F7')->setValue(CurrencyNegative::minus->name);
$sheet->getCell('G7')->setValue(1234.56);
$sheet->getCell('H7')->setValue(0);
$sheet->getCell('I7')->setValue(-1234.56);
$sheet->getCell('J7')->setValue('Text');
$sheet->getStyle('G7:J7')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Accounting('￥', currencySymbolSpacing: false, negative: CurrencyNegative::minus))->format(),
        ],
    ]
);

$sheet->getCell('A8')->setValue('AcctWiz￥');
$sheet->getCell('E8')->setValue(false);
$sheet->getCell('F8')->setValue(CurrencyNegative::parentheses->name);
$sheet->getCell('G8')->setValue(1234.56);
$sheet->getCell('H8')->setValue(0);
$sheet->getCell('I8')->setValue(-1234.56);
$sheet->getCell('J8')->setValue('Text');
$sheet->getStyle('G8:J8')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Accounting('￥', currencySymbolSpacing: false, negative: CurrencyNegative::parentheses))->format(),
        ],
    ]
);

$sheet->getCell('A9')->setValue('AcctW HUF');
$sheet->getCell('E9')->setValue(true);
$sheet->getCell('G9')->setValue(1234.56);
$sheet->getCell('H9')->setValue(0);
$sheet->getCell('I9')->setValue(-1234.56);
$sheet->getCell('J9')->setValue('Text');
$sheet->getStyle('G9:J9')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Accounting('HUF', currencySymbolSpacing: true))->format(),
        ],
    ]
);

$sheet->getCell('A10')->setValue('AcctW HUF');
$sheet->getCell('E10')->setValue(true);
$sheet->getCell('F10')->setValue(CurrencyNegative::redParentheses->name);
$sheet->getStyle('F10')->getFont()->getColor()->setRgb('FF0000');
$sheet->getCell('G10')->setValue(1234.56);
$sheet->getCell('H10')->setValue(0);
$sheet->getCell('I10')->setValue(-1234.56);
$sheet->getCell('J10')->setValue('Text');
$sheet->getStyle('G10:J10')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Accounting('HUF', currencySymbolSpacing: true, negative: CurrencyNegative::redParentheses))->format(),
        ],
    ]
);

$sheet->getCell('A11')->setValue('AcctW Kazakh');
$sheet->getCell('D11')->setValue(false);
$sheet->getCell('G11')->setValue(1234.56);
$sheet->getCell('H11')->setValue(0);
$sheet->getCell('I11')->setValue(-1234.56);
$sheet->getCell('J11')->setValue('Text');
$sheet->getStyle('G11:J11')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Accounting('₸', currencySymbolPosition: Accounting::TRAILING_SYMBOL))->format(),
        ],
    ]
);

$sheet->getCell('A12')->setValue('AcctW $');
$sheet->getCell('B12')->setValue(3);
$sheet->getCell('C12')->setValue(false);
$sheet->getCell('D12')->setValue(false);
$sheet->getCell('F12')->setValue(CurrencyNegative::redMinus->name);
$sheet->getStyle('F12')->getFont()->getColor()->setRgb('FF0000');
$sheet->getCell('G12')->setValue(1234.56);
$sheet->getCell('H12')->setValue(0);
$sheet->getCell('I12')->setValue(-1234.56);
$sheet->getCell('J12')->setValue('Text');
$format = new Accounting(
    '$',
    decimals: 3,
    thousandsSeparator: false,
    currencySymbolPosition: Accounting::TRAILING_SYMBOL,
    negative: CurrencyNegative::redMinus
);

$sheet->getStyle('G12:J12')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => $format->format(),
        ],
    ]
);

$sheet->getCell('L2')->setValue(1234.56);
$sheet->getCell('L3')->setValue(0);
$sheet->getCell('L4')->setValue(-1234.56);
$format = new Accounting('$');
$sheet->getStyle('L2:L4')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => $format->format(),
        ],
    ]
);

$sheet->getCell('M2')->setValue(1234.56);
$sheet->getCell('M3')->setValue(0);
$sheet->getCell('M4')->setValue(-1234.56);
$format = new Accounting('€', currencySymbolPosition: Accounting::TRAILING_SYMBOL);
$sheet->getStyle('M2:M4')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => $format->format(),
        ],
    ]
);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('I')->setAutoSize(true);
$sheet->getColumnDimension('L')->setAutoSize(true);
$sheet->getColumnDimension('M')->setAutoSize(true);
$sheet->setSelectedCells('J1');

// second sheet

$helper->log('Second sheet - Currency Wizard');
$sheet = $spreadsheet->createSheet();
$sheet->setTitle('Currency');
$sheet->getCell('A1')->setValue('Currency');
$sheet->getCell('B1')->setValue('Decimals');
$sheet->getCell('C1')->setValue('ThouSep');
$sheet->getCell('D1')->setValue('Lead');
$sheet->getCell('E1')->setValue('Spacing');
$sheet->getCell('F1')->setValue('Negative');
$sheet->getCell('G1')->setValue('Pos');
$sheet->getCell('H1')->setValue('Zero');
$sheet->getCell('I1')->setValue('Neg');
$sheet->getCell('J1')->setValue('Text');
$sheet->freezePane('A2');
$sheet->getComment('E1')->getText()->createText('ignored, always false for Currency');

$sheet->getCell('A2')->setValue('CurrUSD');
$sheet->getCell('G2')->setValue(1234.56);
$sheet->getCell('H2')->setValue(0);
$sheet->getCell('I2')->setValue(-1234.56);
$sheet->getCell('J2')->setValue('text');
$sheet->getStyle('G2:J2')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_USD);

$sheet->getCell('A3')->setValue('CurrEur');
$sheet->getCell('G3')->setValue(1234.56);
$sheet->getCell('H3')->setValue(0);
$sheet->getCell('I3')->setValue(-1234.56);
$sheet->getCell('J3')->setValue('Text');
$sheet->getStyle('G3:J3')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_CURRENCY_EUR);

$sheet->getCell('A4')->setValue('CurrWiz￥');
$sheet->getCell('E4')->setValue(true);
$sheet->getCell('G4')->setValue(1234.56);
$sheet->getCell('H4')->setValue(0);
$sheet->getCell('I4')->setValue(-1234.56);
$sheet->getCell('J4')->setValue('Text');
$sheet->getStyle('G4:J4')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Currency('￥', currencySymbolSpacing: true))->format(),
        ],
    ]
);

$sheet->getCell('A5')->setValue('StalePR￥');
$sheet->getCell('G5')->setValue(1234.56);
$sheet->getCell('H5')->setValue(0);
$sheet->getCell('I5')->setValue(-1234.56);
$sheet->getCell('J5')->setValue('Text');
$sheet->getStyle('G5:J5')->getNumberFormat()->setFormatCode('￥ #,##0');

$sheet->getCell('A6')->setValue('CurrWiz￥');
$sheet->getCell('E6')->setValue(true);
$sheet->getCell('F6')->setValue(CurrencyNegative::minus->name);
$sheet->getCell('G6')->setValue(1234.56);
$sheet->getCell('H6')->setValue(0);
$sheet->getCell('I6')->setValue(-1234.56);
$sheet->getCell('J6')->setValue('Text');
$sheet->getStyle('G6:J6')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Currency('￥', currencySymbolSpacing: true, negative: CurrencyNegative::minus))->format(),
        ],
    ]
);

$sheet->getCell('A7')->setValue('CurrWiz￥');
$sheet->getCell('E7')->setValue(false);
$sheet->getCell('F7')->setValue(CurrencyNegative::minus->name);
$sheet->getCell('G7')->setValue(1234.56);
$sheet->getCell('H7')->setValue(0);
$sheet->getCell('I7')->setValue(-1234.56);
$sheet->getCell('J7')->setValue('Text');
$sheet->getStyle('G7:J7')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Currency('￥', currencySymbolSpacing: false, negative: CurrencyNegative::minus))->format(),
        ],
    ]
);

$sheet->getCell('A8')->setValue('CurrWiz￥');
$sheet->getCell('E8')->setValue(false);
$sheet->getCell('F8')->setValue(CurrencyNegative::parentheses->name);
$sheet->getCell('G8')->setValue(1234.56);
$sheet->getCell('H8')->setValue(0);
$sheet->getCell('I8')->setValue(-1234.56);
$sheet->getCell('J8')->setValue('Text');
$sheet->getStyle('G8:J8')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Currency('￥', currencySymbolSpacing: false, negative: CurrencyNegative::parentheses))->format(),
        ],
    ]
);

$sheet->getCell('A9')->setValue('CurrW HUF');
$sheet->getCell('E9')->setValue(true);
$sheet->getCell('G9')->setValue(1234.56);
$sheet->getCell('H9')->setValue(0);
$sheet->getCell('I9')->setValue(-1234.56);
$sheet->getCell('J9')->setValue('Text');
$sheet->getStyle('G9:J9')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Currency('HUF', currencySymbolSpacing: true))->format(),
        ],
    ]
);

$sheet->getCell('A10')->setValue('CurrW HUF');
$sheet->getCell('E10')->setValue(true);
$sheet->getCell('F10')->setValue(CurrencyNegative::redParentheses->name);
$sheet->getStyle('F10')->getFont()->getColor()->setRgb('FF0000');
$sheet->getCell('G10')->setValue(1234.56);
$sheet->getCell('H10')->setValue(0);
$sheet->getCell('I10')->setValue(-1234.56);
$sheet->getCell('J10')->setValue('Text');
$sheet->getStyle('G10:J10')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Currency('HUF', currencySymbolSpacing: true, negative: CurrencyNegative::redParentheses))->format(),
        ],
    ]
);

$sheet->getCell('A11')->setValue('CurrW Kazakh');
$sheet->getCell('D11')->setValue(false);
$sheet->getCell('G11')->setValue(1234.56);
$sheet->getCell('H11')->setValue(0);
$sheet->getCell('I11')->setValue(-1234.56);
$sheet->getCell('J11')->setValue('Text');
$sheet->getStyle('G11:J11')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new Currency('₸', currencySymbolPosition: Accounting::TRAILING_SYMBOL))->format(),
        ],
    ]
);

$sheet->getCell('A12')->setValue('CurrW $');
$sheet->getCell('B12')->setValue(3);
$sheet->getCell('C12')->setValue(false);
$sheet->getCell('D12')->setValue(false);
$sheet->getCell('F12')->setValue(CurrencyNegative::redMinus->name);
$sheet->getStyle('F12')->getFont()->getColor()->setRgb('FF0000');
$sheet->getCell('G12')->setValue(1234.56);
$sheet->getCell('H12')->setValue(0);
$sheet->getCell('I12')->setValue(-1234.56);
$sheet->getCell('J12')->setValue('Text');
$format = new Currency(
    '$',
    decimals: 3,
    thousandsSeparator: false,
    currencySymbolPosition: Currency::TRAILING_SYMBOL,
    negative: CurrencyNegative::redMinus
);

$sheet->getStyle('G12:J12')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => $format->format(),
        ],
    ]
);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('H')->setAutoSize(true);
$sheet->getColumnDimension('I')->setAutoSize(true);
$sheet->setSelectedCells('J1');

// third sheet

$helper->log('Third sheet - CurrencyBase Wizard');
$sheet = $spreadsheet->createSheet();
$sheet->setTitle('CurrencyBase');
$sheet->getCell('A1')->setValue('Currency');
$sheet->getCell('B1')->setValue('Decimals');
$sheet->getCell('C1')->setValue('ThouSep');
$sheet->getCell('D1')->setValue('Lead');
$sheet->getCell('E1')->setValue('Spacing');
$sheet->getCell('F1')->setValue('Negative');
$sheet->getCell('G1')->setValue('Pos');
$sheet->getCell('H1')->setValue('Zero');
$sheet->getCell('I1')->setValue('Neg');
$sheet->getCell('J1')->setValue('Text');
$sheet->freezePane('A2');

$sheet->getCell('A2')->setValue('StaleAct￥');
$sheet->getCell('G2')->setValue(1234.56);
$sheet->getCell('H2')->setValue(0);
$sheet->getCell('I2')->setValue(-1234.56);
$sheet->getCell('J2')->setValue('Text');
$sheet->getStyle('G2:J2')->getNumberFormat()->setFormatCode('_("￥"* #,##0.00_);_("￥"* -#,##0.00_);_("￥"* "-"??_);_(@_)');

$sheet->getCell('A3')->setValue('CurBase ￥');
$sheet->getCell('E3')->setValue(true);
$sheet->getCell('F3')->setValue(CurrencyNegative::minus->name);
$sheet->getCell('G3')->setValue(1234.56);
$sheet->getCell('H3')->setValue(0);
$sheet->getCell('I3')->setValue(-1234.56);
$sheet->getCell('J3')->setValue('Text');
$sheet->getStyle('G3:J3')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new CurrencyBase('￥', currencySymbolSpacing: true, negative: CurrencyNegative::minus))->format(),
        ],
    ]
);
$sheet->getCell('G4')->setValue(-1234.56);
$sheet->getStyle('G4')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new CurrencyBase('￥', currencySymbolSpacing: true, negative: CurrencyNegative::minus))->format(),
        ],
    ]
);
$sheet->getCell('G5')->setValue(0);
$sheet->getStyle('G5')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new CurrencyBase('￥', currencySymbolSpacing: true, negative: CurrencyNegative::minus))->format(),
        ],
    ]
);

$sheet->getCell('A6')->setValue('StaleCur￥');
$sheet->getCell('G6')->setValue(1234.56);
$sheet->getCell('H6')->setValue(0);
$sheet->getCell('I6')->setValue(-1234.56);
$sheet->getCell('J6')->setValue('Text');
$sheet->getStyle('G6:J6')->getNumberFormat()->setFormatCode('￥ #,##0');

$sheet->getCell('A7')->setValue('CurBase ￥');
$sheet->getCell('B7')->setValue(0);
$sheet->getCell('G7')->setValue(1234.56);
$sheet->getCell('H7')->setValue(0);
$sheet->getCell('I7')->setValue(-1234.56);
$sheet->getCell('J7')->setValue('Text');
$sheet->getStyle('G7:J7')->applyFromArray(
    [
        'numberFormat' => [
            'formatCode' => (new CurrencyBase('￥', 0))->format(),
        ],
    ]
);

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('H')->setAutoSize(true);
$sheet->getColumnDimension('I')->setAutoSize(true);
$sheet->setSelectedCells('J1');

$spreadsheet->setActiveSheetIndex(0);

$helper->write($spreadsheet, __FILE__, ['Xls', 'Xlsx']);
$spreadsheet->disconnectWorksheets();
