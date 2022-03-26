<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Format;

/**
 * @inheritDoc
 */
class XlText extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'TEXT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Format::class, 'TEXTFORMAT'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
