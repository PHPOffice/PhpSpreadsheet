<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper;

use PhpOffice\PhpSpreadsheet\Helper\Migrator;
use PHPUnit_Framework_TestCase;

class MigratorTest extends PHPUnit_Framework_TestCase
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
