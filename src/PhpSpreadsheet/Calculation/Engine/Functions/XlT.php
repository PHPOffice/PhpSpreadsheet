<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Text;

/**
 * @inheritDoc
 */
class XlT extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'T';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Text::class, 'test'];

    /**
     * @var string
     */
    protected $argumentCount = '1';
}
