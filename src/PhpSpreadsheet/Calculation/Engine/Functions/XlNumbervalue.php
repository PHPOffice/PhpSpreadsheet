<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

/**
 * @inheritDoc
 */
class XlNumbervalue extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'NUMBERVALUE';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Format::class, 'NUMBERVALUE'];

    /**
     * @var string
     */
    protected $argumentCount = '1+';
}
