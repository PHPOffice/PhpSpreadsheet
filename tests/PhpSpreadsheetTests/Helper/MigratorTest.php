<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Migrator;
use PHPUnit\Framework\TestCase;

class MigratorTest extends TestCase
{
    public function testMappingOnlyContainExistingClasses()
    {
        $migrator = new Migrator();

        foreach ($migrator->getMapping() as $classname) {
            if (substr_count($classname, '\\')) {
                self::assertTrue(class_exists($classname) || interface_exists($classname), 'mapping is wrong, class does not exists in project: ' . $classname);
            }
        }
    }

    public function testReplace()
    {
        $input = <<<'STRING'
<?php

namespace Foo;

use PHPExcel;
use PHPExcel_Worksheet;

class Bar
{
    /**
     * @param PHPExcel $workbook
     * @param PHPExcel_Worksheet $sheet
     *
     * @return string
     */
    public function baz(PHPExcel $workbook, PHPExcel_Worksheet $sheet)
    {
        PHPExcel::class;
        \PHPExcel::class;
        $PHPExcel->do();
        $fooobjPHPExcel->do();
        $objPHPExcel->do();
        $this->objPHPExcel->do();
        $this->PHPExcel->do();

        return \PHPExcel_Cell::stringFromColumnIndex(9);
    }
}
STRING;

        $expected = <<<'STRING'
<?php

namespace Foo;

use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Bar
{
    /**
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $workbook
     * @param \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet
     *
     * @return string
     */
    public function baz(\PhpOffice\PhpSpreadsheet\Spreadsheet $workbook, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        \PhpOffice\PhpSpreadsheet\Spreadsheet::class;
        \PhpOffice\PhpSpreadsheet\Spreadsheet::class;
        $PHPExcel->do();
        $fooobjPHPExcel->do();
        $objPHPExcel->do();
        $this->objPHPExcel->do();
        $this->PHPExcel->do();

        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(9);
    }
}
STRING;

        $migrator = new Migrator();
        self::assertSame($expected, $migrator->replace($input));
    }
}
