<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

/**
 * @inheritDoc
 */
class XlReplace extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'REPLACE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Replace::class, 'replace'];

    /**
     * @var string
     */
    protected $argumentCount = '4';
}
