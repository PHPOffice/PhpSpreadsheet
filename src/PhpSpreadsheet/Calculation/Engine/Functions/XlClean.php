<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim;

/**
 * @inheritDoc
 */
class XlClean extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'CLEAN';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Trim::class, 'nonPrintable'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
