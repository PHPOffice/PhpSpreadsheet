<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

/**
 * @inheritDoc
 */
class XlDollar extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'DOLLAR';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Format::class, 'DOLLAR'];

    /**
     * @var string
     */
    protected $argumentCount = '1,2';
}
