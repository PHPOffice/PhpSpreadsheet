<?php

// not yet ready for prime time

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Calculation\Calculation;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

// TODO - I think the spreadsheet below is too difficult for PhpSpreadsheet to calculate correctly.

class ArrayFunctions2Test extends TestCase
{
    private const STYLESIZE14 = [
        'font' => [
            'size' => 14,
        ],
    ];
    private const STYLEBOLD = [
        'font' => [
            'bold' => true,
        ],
    ];
    private const STYLEBOLD14 = [
        'font' => [
            'bold' => true,
            'size' => 14,
        ],
    ];
    private const STYLECENTER = [
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
        ],
    ];

    private const STYLETHICKBORDER = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THICK,
                'color' => ['argb' => '00000000'],
            ],
        ],
    ];

    private array $trn;

    private string $outputFile = '';

    protected function tearDown(): void
    {
        if ($this->outputFile !== '') {
            unlink($this->outputFile);
            $this->outputFile = '';
        }
    }

    private function odd(int $i): bool
    {
        return ($i % 2) === 1;
    }

    private function doPartijen(Worksheet $ws): int
    {
        $saring = explode("\n", $this->trn['PARINGEN']);
        $s = $this->trn['PLAYERSNOID'];
        $g = $this->trn['RONDEGAMES'];
        $KD = $this->trn['KALENDERDATA'];
        $si = $this->trn['PLAYERSIDS'];

        $a = [
            ['Wit', null, null, null, 'Zwart', null, null, null, 'Wit', 'Uitslag', 'Zwart', 'Opmerking', 'Datum'],
            ['Winstpunten', 'Weerstandspunten', 'punten', 'Tegenpunten', 'Winstpunten', 'Weerstandspunten', 'punten', 'Tegenpunten'],

        ];
        $ws->fromArray($a, null, 'A1');

        $ws->getStyle('A1:L1')->applyFromArray(self::STYLEBOLD);
        $ws->getStyle('A1:L1')->applyFromArray(self::STYLESIZE14);

        $lijn = 1;
        for ($i = 1; $i <= $this->trn['RONDEN']; ++$i) {//aantal ronden oneven->heen en even->terug
            $countSaring = count($saring);
            for ($j = 0; $j < $countSaring; ++$j) {//subronden
                ++$lijn;
                if (isset($KD[(($i - 1) * $this->trn['SUB_RONDEN']) + $j], $KD[(($i - 1) * $this->trn['SUB_RONDEN']) + $j]['RONDE'])) {
                    $ws->setCellValue([9, $lijn], $KD[(($i - 1) * $this->trn['SUB_RONDEN']) + $j]['RONDE']);
                } else {
                    $ws->setCellValue([9, $lijn], 'Kalenderdata zijn niet(volledig) ingevuld');
                }
                if (isset($KD[(($i - 1) * $this->trn['SUB_RONDEN']) + $j], $KD[(($i - 1) * $this->trn['SUB_RONDEN']) + $j]['TXT'])) {
                    $ws->setCellValue([10, $lijn], $KD[(($i - 1) * $this->trn['SUB_RONDEN']) + $j]['TXT']);
                } else {
                    $ws->setCellValue([10, $lijn], 'Kalenderdata zijn niet(volledig) ingevuld');
                }

                $ws->getStyle('A' . $lijn . ':L' . $lijn . '')->applyFromArray(self::STYLEBOLD14);

                $s2 = explode(' ', $saring[$j]);
                $counts2 = count($s2);
                for ($k = 0; $k < $counts2; ++$k) {//borden
                    if (trim($s2[$k]) == '') {
                        continue;
                    }
                    $s3 = explode('-', $s2[$k]); //wit-zwart
                    $s3[0] = (int) $s3[0];
                    $s3[1] = (int) $s3[1];
                    ++$lijn;
                    $ws->setCellValue([1, $lijn], '=IF(SUBSTITUTE(TRIM($J' . $lijn . '),"-","")="","",XLOOKUP($K' . $lijn . ',Spelers!$B$2:$B$' . (count($s) + 1) . ',Spelers!$C$2:$C$' . (count($s) + 1) . ',FALSE) * VLOOKUP(J' . $lijn . ', PuntenLijst,4,FALSE))');
                    $ws->setCellValue([2, $lijn], '=IF(SUBSTITUTE(TRIM($J' . $lijn . '),"-","")="","",VLOOKUP($K' . $lijn . ',Spelers!$B$2:$D$' . (count($s) + 1) . ',3,FALSE) * VLOOKUP(J' . $lijn . ', PuntenLijst,5,FALSE))');
                    $ws->setCellValue([3, $lijn], '=IF(TRIM($J' . $lijn . ')="","",XLOOKUP($J' . $lijn . ', Punten!$A$2:$A$50,Punten!$B$2:$B$50,0,0))');
                    $ws->setCellValue([4, $lijn], '=IF(TRIM($J' . $lijn . ')="","",XLOOKUP($J' . $lijn . ', Punten!$A$2:$A$50,Punten!$G$2:$G$50,0,0))');

                    $ws->setCellValue([5, $lijn], '=IF(SUBSTITUTE(TRIM($J' . $lijn . '),"-","")="","",VLOOKUP($I' . $lijn . ',Spelers!$B$2:$D$' . (count($s) + 1) . ',3,FALSE) * VLOOKUP(J' . $lijn . ', PuntenLijst,6,FALSE))');
                    $ws->setCellValue([6, $lijn], '=IF(SUBSTITUTE(TRIM($J' . $lijn . '),"-","")="","",VLOOKUP($I' . $lijn . ',Spelers!$B$2:$D$' . (count($s) + 1) . ',3,FALSE) * VLOOKUP(J' . $lijn . ', PuntenLijst,5,FALSE))');
                    $ws->setCellValue([7, $lijn], '=IF(TRIM($J' . $lijn . ')="","",XLOOKUP($J' . $lijn . ', Punten!$A$2:$A$50,Punten!$C$2:$C$50,0,0))');
                    $ws->setCellValue([8, $lijn], '=IF(TRIM($J' . $lijn . ')="","",XLOOKUP($J' . $lijn . ', Punten!$A$2:$A$50,Punten!$H$2:$H$50,0,0))');

                    if ($this->odd($i)) {
                        if (
                            isset($g[$i][$si[((int) $s3[0]) - 1]][$si[((int) $s3[1]) - 1]])
                        ) {
                            $pw = $g[$i][$si[((int) $s3[0]) - 1]][$si[((int) $s3[1]) - 1]];
                        } else {
                            $pw = ['SYMBOOLWIT' => '', 'SYMBOOLZWART' => '', 'UITSLAG' => '', 'OPMERKING' => '', 'DATUM' => ''];
                        }
                        $ws->setCellValue([9, $lijn], '=Spelers!$B$' . ($s3[0] + 1));
                        $ws->setCellValue([11, $lijn], '=Spelers!$B$' . ($s3[1] + 1));
                    } else {
                        if (
                            isset($g[$i][$si[((int) $s3[1]) - 1]][$si[((int) $s3[0]) - 1]])
                        ) {
                            $pw = $g[$i][$si[((int) $s3[1]) - 1]][$si[((int) $s3[0]) - 1]];
                        } else {
                            $pw = ['SYMBOOLWIT' => '', 'SYMBOOLZWART' => '', 'UITSLAG' => '', 'OPMERKING' => '', 'DATUM' => ''];
                        }
                        $ws->setCellValue([9, $lijn], '=Spelers!$B$' . ($s3[1] + 1));
                        $ws->setCellValue([11, $lijn], '=Spelers!$B$' . ($s3[0] + 1));
                    }
                    if ($pw['SYMBOOLWIT'] != '') {
                        $ws->setCellValue([10, $lijn], $pw['SYMBOOLWIT'] . '-' . $pw['SYMBOOLZWART']);
                    }
                    $ws->setCellValue([13, $lijn], $pw['OPMERKING']);
                    $ws->setCellValue([14, $lijn], $pw['DATUM']);

                    $this->doValidationPunten($ws, 'J' . $lijn);

                    $ws->getRowDimension($lijn)->setOutlineLevel(1);
                }
                ++$lijn;
            }
            ++$lijn;
        }
        $ws->getStyle('J1:J' . $lijn)->applyFromArray(self::STYLECENTER);

        $ws->getColumnDimension('A')->setVisible(false);
        $ws->getColumnDimension('B')->setVisible(false);
        $ws->getColumnDimension('C')->setVisible(false);
        $ws->getColumnDimension('D')->setVisible(false);
        $ws->getColumnDimension('E')->setVisible(false);
        $ws->getColumnDimension('F')->setVisible(false);
        $ws->getColumnDimension('G')->setVisible(false);
        $ws->getColumnDimension('H')->setVisible(false);

        for ($i = 65; $i < ord('M'); ++$i) {
            $ws->getColumnDimension(chr($i))->setAutoSize(true);
        }
        $ws->setAutoFilter('A2:M' . $lijn);
        $ws->setSelectedCell('A1');

        return $lijn;
    }

    private function doValidationPunten(Worksheet $s, string $cel): void
    {
        $validation = $s->getCell($cel)->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(false);
        $validation->setShowInputMessage(false);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setFormula1('=punten');
    }

    private function doPunten(Spreadsheet $ss, Worksheet $ws): void
    {
        $ws->fromArray(['Uitslag', 'Wit', 'Zwart', 'Winstverhouding WIT', 'Weerstandverhouding', 'Winstverhouding ZWART', 'Tegenpunten Wit', 'Tegenpunten Zwart'], null, 'A1');
        $ws->fromArray(['0-3', '0', '3', '0', '1', '1', '0', '0'], null, 'A2');
        $ws->fromArray(['1-3', '1', '3', '0', '1', '1', '0', '-1'], null, 'A3');
        $ws->fromArray(['2-3', '2', '3', '0', '1', '1', '0', '-2'], null, 'A4');
        $ws->fromArray(['3-0', '3', '0', '1', '1', '0', '0', '0'], null, 'A5');
        $ws->fromArray(['3-1', '3', '1', '1', '1', '0', '-1', '0'], null, 'A6');
        $ws->fromArray(['3-2', '3', '2', '1', '1', '0', '-2', '0'], null, 'A7');
        $ws->fromArray(['U-U', '0', '0', '0', '0', '0', '0', '0'], null, 'A8');

        $ss->addNamedRange(new NamedRange('Punten', $ws, '=$A$2:$A$8'));
        $ss->addNamedRange(new NamedRange('PuntenLijst', $ws, '=$A$2:$H$8'));

        $ws->getStyle('A1:H1')->applyFromArray(self::STYLETHICKBORDER);
        $ws->getStyle('A1:A8')->applyFromArray(self::STYLETHICKBORDER);
        $ws->getStyle('B2:H8')->applyFromArray(self::STYLETHICKBORDER);

        for ($i = 65; $i < ord('I'); ++$i) {
            $ws->getColumnDimension(chr($i))->setAutoSize(true);
        }
        $ws->setSelectedCell('A1');
    }

    private function doSpelers(Worksheet $ws, int $maxLijn): void
    {
        $this->doKoppen($ws);

        $i = 1;
        foreach ($this->trn['SPELERS'] as $speler) {
            $ws->setCellValue([1, ++$i], '=RANK(D' . $i . ',$D$2:$D$' . (count($this->trn['SPELERS']) + 1) . ',0)-1+COUNTIF($D$2:D' . $i . ',D' . $i . ')');
            $ws->setCellValue([2, $i], $speler);
            $ws->setCellValue([3, $i], '=COUNTIFS(Partijen!$I$3:$I$' . $maxLijn . ',$B' . $i . ',Partijen!$J$3:$J$' . $maxLijn . ',"<>")+COUNTIFS(Partijen!$K$3:$K$' . $maxLijn . ',Spelers!$B' . $i . ',Partijen!$J$3:$J$' . $maxLijn . ',"<>")'); // - SUM(H' . $i . ':I' . $i . ')
            $ws->setCellValue([4, $i], '=SUMIFS(Partijen!C$3:C$' . $maxLijn . ',Partijen!$I$3:$I$' . $maxLijn . ',$B' . $i . ')+SUMIFS(Partijen!G$3:G$' . $maxLijn . ',Partijen!$K$3:$K$' . $maxLijn . ',$B' . $i . ')');
            $ws->setCellValue([5, $i], '=SUMIFS(Partijen!D$3:D$' . $maxLijn . ',Partijen!$I$3:$I$' . $maxLijn . ',$B' . $i . ')+SUMIFS(Partijen!H$3:H$' . $maxLijn . ',Partijen!$K$3:$K$' . $maxLijn . ',$B' . $i . ')');
            $ws->setCellValue([6, $i], '=SUMIFS(Partijen!A$3:A$' . $maxLijn . ',Partijen!$I$3:$I$' . $maxLijn . ',$B' . $i . ')+SUMIFS(Partijen!E$3:E$' . $maxLijn . ',Partijen!$K$3:$K$' . $maxLijn . ',$B' . $i . ')');
            $ws->setCellValue([7, $i], '=SUMIFS(Partijen!B$3:B$' . $maxLijn . ',Partijen!$I$3:$I$' . $maxLijn . ',$B' . $i . ')+SUMIFS(Partijen!F$3:F$' . $maxLijn . ',Partijen!$K$3:$K$' . $maxLijn . ',$B' . $i . ')');
            $ws->setCellValue([8, $i], '=C' . $i . '*MAX(Punten!$B$2:$B$50)-D' . $i . '');
        }
        $ws->setSelectedCell('A1');
    }

    private function doSort1(Worksheet $ws): void
    {
        $ws->setCellValue('A2', '=FILTER(SORTBY(Spelers!A2:H101,Spelers!D2:D101,-1,Spelers!E2:E101,1),(Spelers!B2:B101<>"Bye")*(NOT(ISBLANK(Spelers!B2:B101))))');
        $this->doKoppen($ws);
        $ws->setSelectedCell('A1');
    }

    private function doSort2(Worksheet $ws): void
    {
        $ws->setCellValue('A2', '=SORT(FILTER(Spelers!A2:H101,(Spelers!B2:B101<>"Bye") * (NOT(ISBLANK(Spelers!B2:B101)))),7,1,FALSE)');
        $this->doKoppen($ws);
        $ws->setSelectedCell('A1');
    }

    private function doKoppen(Worksheet $ws): void
    {
        $ws->setCellValue('A1', 'Plaats');
        $ws->setCellValue('B1', 'Naam');
        $ws->setCellValue('C1', 'Partijen');
        $ws->setCellValue('D1', 'Punten');
        $ws->setCellValue('E1', 'Tegenpunten');
        $ws->setCellValue('F1', 'Winstpunten');
        $ws->setCellValue('G1', 'Weerstandspunten');
        $ws->setCellValue('H1', 'Verlorenpunten');

        for ($i = 65; $i < ord('I'); ++$i) {
            $ws->getColumnDimension(chr($i))->setAutoSize(true);
        }

        $ws->getStyle('A1:H1')->applyFromArray(self::STYLEBOLD);
    }

    public function testManyArraysOutput(): void
    {
        $json = file_get_contents('tests/data/Writer/XLSX/ArrayFunctions2.json');
        self::assertNotFalse($json);
        $this->trn = json_decode($json, true);
        $spreadsheet = new Spreadsheet();
        Calculation::getInstance($spreadsheet)->setInstanceArrayReturnType(Calculation::RETURN_ARRAY_AS_ARRAY);

        $wsPartijen = $spreadsheet->getActiveSheet();
        $wsPartijen->setTitle('Partijen');
        $wsPunten = new Worksheet($spreadsheet, 'Punten');
        $wsSpelers = new Worksheet($spreadsheet, 'Spelers');
        $wsSort1 = new Worksheet($spreadsheet, 'Gesorteerd punten');
        $wsSort2 = new Worksheet($spreadsheet, 'Gesorteerd verlorenpunten');

        $wsPartijen->getTabColor()->setRGB('FF0000');
        $wsPunten->getTabColor()->setRGB('00FF00');
        $wsSpelers->getTabColor()->setRGB('0000FF');
        $wsSort1->getTabColor()->setRGB('FFFF00');
        $wsSort2->getTabColor()->setRGB('00FFFF');

        foreach ([$wsPunten, $wsSpelers, $wsSort1, $wsSort2] as $ws) {
            $spreadsheet->addSheet($ws);
        }

        $this->doPunten($spreadsheet, $wsPunten);
        $maxLijn = $this->doPartijen($wsPartijen);
        $this->doSpelers($wsSpelers, $maxLijn);
        $this->doSort1($wsSort1);
        $this->doSort2($wsSort2);

        self::assertSame('Dirk', $wsPartijen->getCell('I3')->getCalculatedValue());
        self::assertSame('Rudy', $wsPartijen->getCell('K4')->getCalculatedValue());
        $calcArray = $wsSort2->getCell('A2')->getCalculatedValue();
        self::assertCount(8, $calcArray);
        $spreadsheet->disconnectWorksheets();
    }
}
