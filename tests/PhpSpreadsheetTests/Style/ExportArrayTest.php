<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PHPUnit\Framework\TestCase;

class ExportArrayTest extends TestCase
{
    public function testStyleCopy(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('Cell A1');
        $cell1style = $cell1->getStyle();
        $cell1style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $cell1style->getFont()->getColor()->setARGB('FFFF0000');
        $cell1style->getFont()->setBold(true);
        $cell1style->getFill()->setFillType(Fill::FILL_PATTERN_GRAY125);
        $cell1style->getFill()->setStartColor(new Color('FF0000FF'));
        $cell1style->getFill()->setEndColor(new Color('FF00FF00'));
        $cell1style->getFont()->setUnderline(true);
        self::assertEquals(Font::UNDERLINE_SINGLE, $cell1style->getFont()->getUnderline());
        $cell1style->getProtection()->setHidden(Protection::PROTECTION_UNPROTECTED);
        $cell1style->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
        $styleArray = $cell1style->exportArray();
        $cell2 = $sheet->getCell('B1');
        $cell2->setValue('Cell B1');
        $cell2style = $cell2->getStyle();
        $cell2style->applyFromArray($styleArray);

        self::AssertEquals($cell1style->getAlignment()->getHorizontal(), $cell2style->getAlignment()->getHorizontal());
        self::AssertEquals($cell1style->getFont()->getColor()->getARGB(), $cell2style->getFont()->getColor()->getARGB());
        self::AssertEquals($cell1style->getFont()->getBold(), $cell2style->getFont()->getBold());
        self::AssertEquals($cell1style->getFont()->getUnderline(), $cell2style->getFont()->getUnderline());
        self::AssertEquals($cell1style->getFill()->getFillType(), $cell2style->getFill()->getFillType());
        self::AssertEquals($cell1style->getFill()->getStartColor()->getARGB(), $cell2style->getFill()->getStartColor()->getARGB());
        self::AssertEquals($cell1style->getFill()->getEndColor()->getARGB(), $cell2style->getFill()->getEndColor()->getARGB());
        self::AssertEquals($cell1style->getProtection()->getLocked(), $cell2style->getProtection()->getLocked());
        self::AssertEquals($cell1style->getProtection()->getHidden(), $cell2style->getProtection()->getHidden());

        self::AssertEquals($cell1style->getHashCode(), $cell2style->getHashCode());
        self::AssertEquals($cell1style->getAlignment()->getHashCode(), $cell2style->getAlignment()->getHashCode());
        self::AssertEquals($cell1style->getFont()->getHashCode(), $cell2style->getFont()->getHashCode());
        self::AssertEquals($cell1style->getFill()->getHashCode(), $cell2style->getFill()->getHashCode());
        self::AssertEquals($cell1style->getProtection()->getHashCode(), $cell2style->getProtection()->getHashCode());
        $spreadsheet->disconnectWorksheets();
    }

    public function testStyleFromArrayCopy(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('Cell A1');
        $cell1style = $cell1->getStyle();
        $cell1style->getAlignment()->applyFromArray(['horizontal' => Alignment::HORIZONTAL_RIGHT]);
        $cell1style->getFont()->getColor()->setARGB('FFFF0000');
        $cell1style->getFont()->applyFromArray(['bold' => true]);
        $cell1style->getFill()->applyFromArray(['fillType' => Fill::FILL_PATTERN_GRAY125]);
        $cell1style->getFill()->getStartColor()->applyFromArray(['argb' => 'FF0000FF']);
        $cell1style->getFill()->getEndColor()->setRGB('00FF00');
        $cell1style->getFill()->setRotation(45);
        $cell1style->getFont()->setUnderline(true);
        self::assertEquals(Font::UNDERLINE_SINGLE, $cell1style->getFont()->getUnderline());
        $cell1style->getProtection()->applyFromArray(['hidden' => Protection::PROTECTION_UNPROTECTED, 'locked' => Protection::PROTECTION_UNPROTECTED]);
        $styleArray = $cell1style->exportArray();
        $cell2 = $sheet->getCell('B1');
        $cell2->setValue('Cell B1');
        $cell2style = $cell2->getStyle();
        $cell2style->applyFromArray($styleArray);

        self::AssertEquals($cell1style->getAlignment()->getHorizontal(), $cell2style->getAlignment()->getHorizontal());
        self::AssertEquals($cell1style->getFont()->getColor()->getARGB(), $cell2style->getFont()->getColor()->getARGB());
        self::AssertEquals($cell1style->getFont()->getBold(), $cell2style->getFont()->getBold());
        self::AssertEquals($cell1style->getFont()->getUnderline(), $cell2style->getFont()->getUnderline());
        self::AssertEquals($cell1style->getFill()->getFillType(), $cell2style->getFill()->getFillType());
        self::AssertEquals($cell1style->getFill()->getRotation(), $cell2style->getFill()->getRotation());
        self::AssertEquals($cell1style->getFill()->getStartColor()->getARGB(), $cell2style->getFill()->getStartColor()->getARGB());
        self::AssertEquals($cell1style->getFill()->getEndColor()->getARGB(), $cell2style->getFill()->getEndColor()->getARGB());
        self::AssertEquals($cell1style->getProtection()->getLocked(), $cell2style->getProtection()->getLocked());
        self::AssertEquals($cell1style->getProtection()->getHidden(), $cell2style->getProtection()->getHidden());

        self::AssertEquals($cell1style->getFill()->getStartColor()->getHashCode(), $cell2style->getFill()->getStartColor()->getHashCode());
        self::AssertEquals($cell1style->getFill()->getEndColor()->getHashCode(), $cell2style->getFill()->getEndColor()->getHashCode());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNumberFormat(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1style = $cell1->getStyle();
        $fmt2 = '$ #,##0.000';
        $cell1style->getNumberFormat()->setFormatCode($fmt2);
        $cell1style->getFont()->setUnderline('');
        self::assertEquals(Font::UNDERLINE_NONE, $cell1style->getFont()->getUnderline());
        $cell1->setValue(2345.679);
        $styleArray = $cell1style->exportArray();
        self::assertEquals('$ 2,345.679', $cell1->getFormattedValue());

        $cell2 = $sheet->getCell('B1');
        $cell2->setValue(12345.679);
        $cell2style = $cell2->getStyle();
        $cell2style->applyFromArray($styleArray);
        self::assertEquals('$ 12,345.679', $cell2->getFormattedValue());

        self::AssertEquals($cell1style->getNumberFormat()->getHashCode(), $cell2style->getNumberFormat()->getHashCode());
        $spreadsheet->disconnectWorksheets();
    }

    public function testNumberFormatFromArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1style = $cell1->getStyle();
        $fmt2 = '$ #,##0.000';
        $cell1style->getNumberFormat()->applyFromArray(['formatCode' => $fmt2]);
        $cell1style->getFont()->setUnderline('');
        self::assertEquals(Font::UNDERLINE_NONE, $cell1style->getFont()->getUnderline());
        $cell1style->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $cell1->setValue(2345.679);
        $styleArray = $cell1style->exportArray();
        self::assertEquals('$ 2,345.679', $cell1->getFormattedValue());

        $cell2 = $sheet->getCell('B1');
        $cell2->setValue(12345.679);
        $cell2style = $cell2->getStyle();
        $cell2style->applyFromArray($styleArray);
        self::assertEquals('$ 12,345.679', $cell2->getFormattedValue());

        self::AssertEquals($cell1style->getNumberFormat()->getHashCode(), $cell2style->getNumberFormat()->getHashCode());
        self::AssertEquals($cell1style->getBorders()->getHashCode(), $cell2style->getBorders()->getHashCode());
        self::AssertEquals($cell1style->getBorders()->getTop()->getHashCode(), $cell2style->getBorders()->getTop()->getHashCode());
        self::AssertEquals($cell1style->getBorders()->getTop()->getBorderStyle(), $cell2style->getBorders()->getTop()->getBorderStyle());
        $spreadsheet->disconnectWorksheets();
    }

    public function testStackedRotation(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $cell1 = $sheet->getCell('A1');
        $cell1->setValue('Cell A1');
        $cell1style = $cell1->getStyle();
        $cell1style->getAlignment()->setTextRotation(Alignment::TEXTROTATION_STACK_EXCEL);
        self::assertEquals(Alignment::TEXTROTATION_STACK_PHPSPREADSHEET, $cell1style->getAlignment()->getTextRotation());
        $styleArray = $cell1style->exportArray();
        $cell2 = $sheet->getCell('B1');
        $cell2->setValue('Cell B1');
        $cell2style = $cell2->getStyle();
        $cell2style->applyFromArray($styleArray);

        self::AssertEquals($cell1style->getAlignment()->getTextRotation(), $cell2style->getAlignment()->getTextRotation());
        $spreadsheet->disconnectWorksheets();
    }

    public function testFillColors(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $cell1 = $sheet->getCell('A2');
        $cell1style = $cell1->getStyle();
        $cell1style->getFill()->setFillType(Fill::FILL_PATTERN_GRAY125);
        $cell1style->getFill()->getStartColor()->setArgb('FF112233');
        $styleArray = $cell1style->exportArray();
        self::assertEquals(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 0.0,
                'endColor' => ['argb' => 'FF000000'],
                'startColor' => ['argb' => 'FF112233'],
            ],
            $styleArray['fill'],
            'changed start color with setArgb'
        );

        $cell1 = $sheet->getCell('A1');
        $cell1style = $cell1->getStyle();
        $cell1style->getFill()->setFillType(Fill::FILL_PATTERN_GRAY125);
        $styleArray = $cell1style->exportArray();
        self::assertEquals(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 0.0,
            ],
            $styleArray['fill'],
            'default colors'
        );

        $cell1 = $sheet->getCell('A3');
        $cell1style = $cell1->getStyle();
        $cell1style->getFill()->setFillType(Fill::FILL_PATTERN_GRAY125);
        $cell1style->getFill()->getEndColor()->setArgb('FF112233');
        $styleArray = $cell1style->exportArray();
        self::assertEquals(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 0.0,
                'endColor' => ['argb' => 'FF112233'],
                'startColor' => ['argb' => 'FFFFFFFF'],
            ],
            $styleArray['fill'],
            'changed end color with setArgb'
        );

        $cell1 = $sheet->getCell('A4');
        $cell1style = $cell1->getStyle();
        $cell1style->getFill()->setFillType(Fill::FILL_PATTERN_GRAY125);
        $cell1style->getFill()->setEndColor(new Color('FF0000FF'));
        $styleArray = $cell1style->exportArray();
        self::assertEquals(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 0.0,
                'endColor' => ['argb' => 'FF0000FF'],
                'startColor' => ['argb' => 'FFFFFFFF'],
            ],
            $styleArray['fill'],
            'changed end color with setEndColor'
        );

        $cell1 = $sheet->getCell('A5');
        $cell1style = $cell1->getStyle();
        $cell1style->getFill()->setFillType(Fill::FILL_PATTERN_GRAY125);
        $cell1style->getFill()->setStartColor(new Color('FF0000FF'));
        $styleArray = $cell1style->exportArray();
        self::assertEquals(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 0.0,
                'startColor' => ['argb' => 'FF0000FF'],
                'endColor' => ['argb' => 'FF000000'],
            ],
            $styleArray['fill'],
            'changed start color with setStartColor'
        );

        $cell1 = $sheet->getCell('A6');
        $cell1->getStyle()->getFill()->applyFromArray(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 45.0,
                'startColor' => ['argb' => 'FF00FFFF'],
            ]
        );
        $cell1style = $cell1->getStyle();
        $styleArray = $cell1style->exportArray();
        self::assertEquals(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 45.0,
                'startColor' => ['argb' => 'FF00FFFF'],
                'endColor' => ['argb' => 'FF000000'],
            ],
            $styleArray['fill'],
            'applyFromArray with startColor'
        );

        $cell1 = $sheet->getCell('A7');
        $cell1->getStyle()->getFill()->applyFromArray(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
            ]
        );
        $cell1style = $cell1->getStyle();
        $styleArray = $cell1style->exportArray();
        self::assertEquals(
            [
                'fillType' => Fill::FILL_PATTERN_GRAY125,
                'rotation' => 0.0,
            ],
            $styleArray['fill'],
            'applyFromArray without start/endColor'
        );
        $spreadsheet->disconnectWorksheets();
    }
}
