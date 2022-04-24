<?php

namespace PhpOffice\PhpSpreadsheetTests\Reader\Xls;

use PhpOffice\PhpSpreadsheet\Reader\Xls\MD5;
use PHPUnit\Framework\TestCase;

class Md5Test extends TestCase
{
    public function testMd5(): void
    {
        $md5 = new MD5();
        $md5->add('123456789a123456789b123456789c123456789d123456789e123456789f1234');
        $context = $md5->getContext();
        self::assertSame('0761293f016b925b0bca11b34f1ed613', bin2hex($context));
    }
}
