<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Escher\DgContainer\SpgrContainer;
use PHPUnit\Framework\TestCase;

class SpgrContainerTest extends TestCase
{
    public function testParent(): void
    {
        $container = new SpgrContainer();
        $contained = new SpgrContainer();
        $contained->setParent($container);
        self::assertSame($container, $contained->getParent());
    }
}
