<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Trim;

/**
 * @inheritDoc
 */
class XlTrim extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TRIM';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Trim::class, 'spaces'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
