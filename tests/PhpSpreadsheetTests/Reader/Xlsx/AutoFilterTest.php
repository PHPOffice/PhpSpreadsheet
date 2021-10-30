<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx\AutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter as WorksheetAutoFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

class AutoFilterTest extends TestCase
{
    private function getWorksheetInstance()
    {
        return $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getXMLInstance($ref)
    {
        return new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<root>' .
                '<autoFilter ref="' . $ref . '"></autoFilter>' .
            '</root>'
        );
    }

    private function getAutoFilterInstance()
    {
        $instance = $this->getMockBuilder(WorksheetAutoFilter::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $instance;
    }

    public function loadDataProvider(): array
    {
        return [
            ['$B3$E8', 0, 'B3E8'],
            ['$B3:$E8', 1, 'B3:E8'],
        ];
    }

    /**
     * @dataProvider loadDataProvider
     *
     * @param string $ref
     * @param int $expectedReadAutoFilterCalled
     * @param string $expectedRef
     */
    public function testLoad($ref, $expectedReadAutoFilterCalled, $expectedRef): void
    {
        $worksheetAutoFilter = $this->getAutoFilterInstance();
        $worksheetAutoFilter->expects(self::exactly($expectedReadAutoFilterCalled ? 1 : 0))
            ->method('setRange')
            ->with($expectedRef);

        $worksheet = $this->getWorksheetInstance();
        $worksheet->expects(self::exactly($expectedReadAutoFilterCalled ? 1 : 0))
            ->method('getAutoFilter')
            ->willReturn($worksheetAutoFilter);

        $autoFilter = new AutoFilter($worksheet, $this->getXMLInstance($ref));

        $autoFilter->load();
    }
}
