<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Text;

/**
 * @inheritDoc
 */
class XlExact extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'EXACT';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Text::class, 'exact'];

    /**
     * @var string
     */
    protected $argumentCount = '2';
}
