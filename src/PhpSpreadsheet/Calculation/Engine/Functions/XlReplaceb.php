<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

/**
 * @inheritDoc
 */
class XlReplaceb extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'REPLACEB';

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
