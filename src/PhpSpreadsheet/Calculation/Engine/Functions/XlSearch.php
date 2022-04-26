<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine\Functions;

use PhpOffice\PhpSpreadsheet\Calculation\Category;
use PhpOffice\PhpSpreadsheet\Calculation\Engine\XlFunctionAbstract;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;

/**
 * @inheritDoc
 */
class XlSearch extends XlFunctionAbstract
{
    /**
     * @var string
     */
    protected $name = 'SEARCH';

    /**
     * @var string
     */
    protected $category = Category::CATEGORY_TEXT_AND_DATA;

    /**
     * @var callable
     */
    protected $functionCall = [Search::class, 'insensitive'];

    /**
     * @var string
     */
    protected $argumentCount = '2,3';
}
