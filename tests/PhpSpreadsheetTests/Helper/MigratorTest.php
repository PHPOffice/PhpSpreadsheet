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
}
