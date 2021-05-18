<?php

namespace PhpOffice\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LocaleGenerator
{
    private const EXCEL_FUNCTIONS_WORKSHEET = 'Excel Functions';
    private const LOCALE_NAME_ROW = 1;
    private const LOCALE_LANGUAGE_NAME_ROW = 2;
    private const ENGLISH_LANGUAGE_NAME_ROW = 3;
    private const FUNCTION_NAME_LIST_FIRST_ROW = 4;
    private const ENGLISH_FUNCTION_CATEGORIES_COLUMN = 'A';
    private const ENGLISH_FUNCTION_NAMES_COLUMN = 'B';

    /**
     * @var string $translationSpreadsheetName
     */
    private $translationSpreadsheetName;

    /**
     * @var string $translationBaseFolder
     */
    protected $translationBaseFolder;

    protected $phpSpreadsheetFunctions;
    /**
     * @var Worksheet $translations
     */
    private $translations;

    protected $languageMap = [];

    protected $functionMap = [];

    public function __construct(
        string $translationBaseFolder,
        string $translationSpreadsheetName,
        array $phpSpreadsheetFunctions
    ) {
        $this->translationBaseFolder = $translationBaseFolder;
        $this->translationSpreadsheetName = $translationSpreadsheetName;
        $this->phpSpreadsheetFunctions = $phpSpreadsheetFunctions;
    }

    public function generateLocales()
    {
        $this->openTranslationSheet();
        $this->mapLanguageColumns();
        $this->mapFunctionRows();

        foreach ($this->languageMap as $column => $locale) {
            $this->buildLocaleFile($column, $locale);
        }
    }

    protected function buildLocaleFile($column, $locale)
    {
        $language = $this->translations->getCell($column . self::ENGLISH_LANGUAGE_NAME_ROW)->getValue();
        $localeLanguage = $this->translations->getCell($column . self::LOCALE_LANGUAGE_NAME_ROW)->getValue();
        $functionFile = $this->openFunctionFile($locale, $language, $localeLanguage);

        foreach ($this->functionMap as $functionName => $row) {
            $translationCell = $this->translations->getCell($column . $row);
            $translationValue = $translationCell->getValue();
            if ($this->isCategoryEntry($translationCell)) {
                fwrite($functionFile, PHP_EOL . '##' . PHP_EOL);
                fwrite($functionFile, '## ' . $translationValue . " ({$functionName})" . PHP_EOL);
                fwrite($functionFile, '##' . PHP_EOL);
            } elseif (!array_key_exists($functionName, $this->phpSpreadsheetFunctions)) {
                echo "Function {$functionName} is not defined in PhpSpreadsheet", PHP_EOL;
            } elseif (!empty($translationValue)) {
                $functionTranslation = "{$functionName} = {$translationValue}" . PHP_EOL;
                fwrite($functionFile, $functionTranslation);
            } else {
                echo "No {$language} translation available for function {$functionName}", PHP_EOL;
            }
        }

        fclose($functionFile);
    }

    protected function openFunctionFile(string $locale, string $language, string $localeLanguage)
    {
        echo "Building locale {$locale} ($language)", PHP_EOL;
        $localeFolder = $this->translationBaseFolder . '/' . str_replace('_', DIRECTORY_SEPARATOR, $locale);
        if (!file_exists($localeFolder) || !is_dir($localeFolder)) {
            mkdir($localeFolder, 0777, true);
        }

        $functionFileName = realpath($localeFolder . '/' . 'functions');
        echo "Writing local function names to {$functionFileName}", PHP_EOL;
        $functionFileName = 'php://stdout';

        $functionFile = fopen($functionFileName, 'w');

        fwrite($functionFile, '##' . PHP_EOL);
        fwrite($functionFile, "## {$localeLanguage} ({$language})" . PHP_EOL);
        fwrite($functionFile, '##' . PHP_EOL . PHP_EOL);

        return $functionFile;
    }

    protected function openTranslationSheet(): void
    {
        $filepathName = $this->translationBaseFolder . '/' . $this->translationSpreadsheetName;
        $translationSpreadsheet = IOFactory::load($filepathName);
        $this->translations = $translationSpreadsheet->setActiveSheetIndexByName(self::EXCEL_FUNCTIONS_WORKSHEET);
        if ($this->translations === null) {
            throw new Exception('Translation Worksheet not found');
        }
    }

    protected function mapLanguageColumns(): void
    {
        echo 'MAPPING LANGUAGES:', PHP_EOL;
        $baseColumn = self::ENGLISH_FUNCTION_NAMES_COLUMN;
        $languagesList = $this->translations->getColumnIterator(++$baseColumn);
        foreach ($languagesList as $languageColumn) {
            /** @var Column $languageColumn */
            $cells = $languageColumn->getCellIterator(self::LOCALE_NAME_ROW, self::LOCALE_NAME_ROW);
            $cells->setIterateOnlyExistingCells(true);
            foreach ($cells as $cell) {
                /** @var Cell $cell */
                $this->languageMap[$cell->getColumn()] = $cell->getValue();
                echo $cell->getColumn(), ' -> ', $cell->getValue(), PHP_EOL;
            }
        }
    }

    protected function mapFunctionRows(): void
    {
        echo 'MAPPING FUNCTIONS:', PHP_EOL;
        $functionList = $this->translations->getRowIterator(self::FUNCTION_NAME_LIST_FIRST_ROW);
        foreach ($functionList as $functionRow) {
            /** @var Row $functionRow */
            $cells = $functionRow->getCellIterator(self::ENGLISH_FUNCTION_NAMES_COLUMN, self::ENGLISH_FUNCTION_NAMES_COLUMN);
            $cells->setIterateOnlyExistingCells(true);
            foreach ($cells as $cell) {
                /** @var Cell $cell */
                if ($this->isCategoryEntry($cell)) {
                    if (!empty($cell->getValue())) {
                        echo 'CATEGORY: ', $cell->getValue(), PHP_EOL;
                        $this->functionMap[$cell->getValue()] = $cell->getRow();
                    }
                    continue;
                }
                if ($cell->getValue() != '') {
                    if (is_bool($cell->getValue())) {
                        echo $cell->getRow(), ' -> ', ($cell->getValue() ? 'TRUE' : 'FALSE'), PHP_EOL;
                        $this->functionMap[($cell->getValue() ? 'TRUE' : 'FALSE')] = $cell->getRow();
                    } else {
                        echo $cell->getRow(), ' -> ', $cell->getValue(), PHP_EOL;
                        $this->functionMap[$cell->getValue()] = $cell->getRow();
                    }
                }
            }
        }
    }

    private function isCategoryEntry(Cell $cell): bool
    {
        $categoryCheckCell = self::ENGLISH_FUNCTION_CATEGORIES_COLUMN . $cell->getRow();
        if ($this->translations->getCell($categoryCheckCell)->getValue() != '') {
            return true;
        }

        return false;
    }
}
