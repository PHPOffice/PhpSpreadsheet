<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Collection;

use PhpOffice\PhpSpreadsheet\Collection\Cells;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class Cells2Test extends TestCase
{
    public function testThrowsWhenCellCannotBeStoredInClonedCache(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->expectExceptionMessage('Failed to copy cells in cache');

        $cache = new SimpleCache3xxx();

        $worksheet = new Worksheet();
        $collection = new Cells($worksheet, $cache);

        $collection->add('A1', $worksheet->getCell('A1'));
        $collection->add('A2', $worksheet->getCell('A2'));
        $collection->cloneCellCollection($worksheet);
    }
}
