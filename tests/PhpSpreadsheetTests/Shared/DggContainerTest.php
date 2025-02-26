<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\Shared\Escher\DggContainer;
use PHPUnit\Framework\TestCase;

class DggContainerTest extends TestCase
{
    public function testBseParent(): void
    {
        $container = new DggContainer\BstoreContainer();
        $bse = new DggContainer\BstoreContainer\BSE();
        $bse->setParent($container);
        self::assertSame($container, $bse->getParent());
    }
}
