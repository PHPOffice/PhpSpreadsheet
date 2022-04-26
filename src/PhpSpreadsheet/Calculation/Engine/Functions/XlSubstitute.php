<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

/**
 * @inheritDoc
 */
class XlSubstitute extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SUBSTITUTE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Replace::class, 'substitute'];

    /**
     * @var string
     */
    protected $argumentCount = '3,4';
}
