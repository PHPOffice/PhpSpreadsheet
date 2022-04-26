<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Engine;

use ArrayAccess;
use Countable;
use DirectoryIterator;
use Iterator;
use PhpOffice\PhpSpreadsheet\Calculation\Exception;
use SplFileInfo;

/**
 * @implements ArrayAccess<string, XlFunctionAbstract>
 * @implements Iterator<string, XlFunctionAbstract>
 */
class ExcelFunctions implements ArrayAccess, Countable, Iterator
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var array<string, XlFunctionAbstract>
     */
    private static $excelFunctions = [];

    /**
     * @var bool
     */
    private $loading = false;

    public function isRecognisedExcelFunction(string $functionName): bool
    {
        return isset(self::$excelFunctions[$functionName]) || class_exists($this->buildClassName($functionName));
    }

    private function loadFunctionDefinition(string $className): XlFunctionAbstract
    {
        $this->loading = true;
        /** @var XlFunctionAbstract $definition */
        $definition = new $className();
        $excelFunctionName = $definition->name;
        self::$excelFunctions[$excelFunctionName] = $definition;

        if (isset($definition->synonyms)) {
            /** @var string $synonym */
            foreach ($definition->synonyms as $synonym) {
                self::$excelFunctions[$synonym] = $definition;
            }
        }
        $this->loading = false;

        return $definition;
    }

    private function buildClassName(string $functionName): string
    {
        $classPath = __NAMESPACE__ . '\\Functions\\Xl';

        return $classPath . ucfirst(strtolower(str_replace('.', '_', $functionName)));
    }

    private function functionDefinitionFactory(string $functionName): ?XlFunctionAbstract
    {
        if (isset(self::$excelFunctions[$functionName])) {
            return self::$excelFunctions[$functionName];
        }

        $className = $this->buildClassName($functionName);
        if (class_exists($className)) {
            $functionDefinition = $this->loadFunctionDefinition($className);

            return $functionDefinition;
        }

        return null;
    }

    private function functionNameFromFile(SplFileInfo $file): string
    {
        return str_replace('_', '.', strtoupper(substr($file->getBasename('.php'), 2)));
    }

    public function loadAll(): void
    {
        $functionFileDirectory = __DIR__ . '/Functions/';
        foreach (new DirectoryIterator($functionFileDirectory) as $file) {
            $this->functionDefinitionFactory($this->functionNameFromFile($file));
        }
    }

    /**
     * @param string $functionName
     */
    public function offsetExists($functionName): bool
    {
        $functionName = strtoupper($functionName);

        return (isset(self::$excelFunctions[$functionName])) ||
            ($this->functionDefinitionFactory($functionName) !== null);
    }

    /**
     * @param string $functionName
     */
    public function offsetGet($functionName): ?XlFunctionAbstract
    {
        $functionName = strtoupper($functionName);

        return (isset(self::$excelFunctions[$functionName]))
            ? self::$excelFunctions[$functionName]
            : $this->functionDefinitionFactory($functionName);
    }

    /**
     * @param string $functionName
     * @param mixed $value
     */
    public function offsetSet($functionName, $value): void
    {
        throw new Exception('Action not permitted');
    }

    /**
     * @param string $functionName
     */
    public function offsetUnset($functionName): void
    {
        throw new Exception('Action not permitted');
    }

    public function __isset(string $functionName): bool
    {
        $functionName = strtoupper($functionName);

        return (isset(self::$excelFunctions[$functionName])) ||
            ($this->functionDefinitionFactory($functionName) !== null);
    }

    public function __get(string $functionName): ?XlFunctionAbstract
    {
        $functionName = strtoupper($functionName);

        return (isset(self::$excelFunctions[$functionName]))
            ? self::$excelFunctions[$functionName]
            : $this->functionDefinitionFactory($functionName);
    }

    /**
     * @param mixed $value
     */
    public function __set(string $functionName, $value): void
    {
        $functionName = strtoupper($functionName);
        if ($this->loading === false) {
            throw new Exception('Action not permitted');
        }

        self::$excelFunctions[$functionName] = $value;
    }

    public function __unset(string $functionName): void
    {
        throw new Exception('Action not permitted');
    }

    public function current(): ?XlFunctionAbstract
    {
        $functionName = array_keys(self::$excelFunctions)[$this->index];
        if (isset(self::$excelFunctions[$functionName])) {
            return self::$excelFunctions[$functionName];
        }

        return $this->functionDefinitionFactory($functionName);
    }

    public function next(): void
    {
        ++$this->index;
    }

    public function key(): string
    {
        return array_keys(self::$excelFunctions)[$this->index];
    }

    public function valid(): bool
    {
        return $this->index < count(self::$excelFunctions);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }

    public function count(): int
    {
        return count(self::$excelFunctions);
    }
}
