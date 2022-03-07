<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;

class Protection
{
    const ALGORITHM_MD2 = 'MD2';
    const ALGORITHM_MD4 = 'MD4';
    const ALGORITHM_MD5 = 'MD5';
    const ALGORITHM_SHA_1 = 'SHA-1';
    const ALGORITHM_SHA_256 = 'SHA-256';
    const ALGORITHM_SHA_384 = 'SHA-384';
    const ALGORITHM_SHA_512 = 'SHA-512';
    const ALGORITHM_RIPEMD_128 = 'RIPEMD-128';
    const ALGORITHM_RIPEMD_160 = 'RIPEMD-160';
    const ALGORITHM_WHIRLPOOL = 'WHIRLPOOL';

    /**
     * Sheet.
     *
     * @var bool
     */
    private $sheet = false;

    /**
     * Objects.
     *
     * @var bool
     */
    private $objects = false;

    /**
     * Scenarios.
     *
     * @var bool
     */
    private $scenarios = false;

    /**
     * Format cells.
     *
     * @var bool
     */
    private $formatCells = false;

    /**
     * Format columns.
     *
     * @var bool
     */
    private $formatColumns = false;

    /**
     * Format rows.
     *
     * @var bool
     */
    private $formatRows = false;

    /**
     * Insert columns.
     *
     * @var bool
     */
    private $insertColumns = false;

    /**
     * Insert rows.
     *
     * @var bool
     */
    private $insertRows = false;

    /**
     * Insert hyperlinks.
     *
     * @var bool
     */
    private $insertHyperlinks = false;

    /**
     * Delete columns.
     *
     * @var bool
     */
    private $deleteColumns = false;

    /**
     * Delete rows.
     *
     * @var bool
     */
    private $deleteRows = false;

    /**
     * Select locked cells.
     *
     * @var bool
     */
    private $selectLockedCells = false;

    /**
     * Sort.
     *
     * @var bool
     */
    private $sort = false;

    /**
     * AutoFilter.
     *
     * @var bool
     */
    private $autoFilter = false;

    /**
     * Pivot tables.
     *
     * @var bool
     */
    private $pivotTables = false;

    /**
     * Select unlocked cells.
     *
     * @var bool
     */
    private $selectUnlockedCells = false;

    /**
     * Hashed password.
     *
     * @var string
     */
    private $password = '';

    /**
     * Algorithm name.
     *
     * @var string
     */
    private $algorithm = '';

    /**
     * Salt value.
     *
     * @var string
     */
    private $salt = '';

    /**
     * Spin count.
     *
     * @var int
     */
    private $spinCount = 10000;

    /**
     * Create a new Protection.
     */
    public function __construct()
    {
    }

    /**
     * Is some sort of protection enabled?
     *
     * @return bool
     */
    public function isProtectionEnabled()
    {
        return $this->sheet ||
            $this->objects ||
            $this->scenarios ||
            $this->formatCells ||
            $this->formatColumns ||
            $this->formatRows ||
            $this->insertColumns ||
            $this->insertRows ||
            $this->insertHyperlinks ||
            $this->deleteColumns ||
            $this->deleteRows ||
            $this->selectLockedCells ||
            $this->sort ||
            $this->autoFilter ||
            $this->pivotTables ||
            $this->selectUnlockedCells;
    }

    /**
     * Get Sheet.
     *
     * @return bool
     */
    public function getSheet()
    {
        return $this->sheet;
    }

    /**
     * Set Sheet.
     *
     * @param bool $sheet
     *
     * @return $this
     */
    public function setSheet($sheet)
    {
        $this->sheet = $sheet;

        return $this;
    }

    /**
     * Get Objects.
     *
     * @return bool
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Set Objects.
     *
     * @param bool $objects
     *
     * @return $this
     */
    public function setObjects($objects)
    {
        $this->objects = $objects;

        return $this;
    }

    /**
     * Get Scenarios.
     *
     * @return bool
     */
    public function getScenarios()
    {
        return $this->scenarios;
    }

    /**
     * Set Scenarios.
     *
     * @param bool $scenarios
     *
     * @return $this
     */
    public function setScenarios($scenarios)
    {
        $this->scenarios = $scenarios;

        return $this;
    }

    /**
     * Get FormatCells.
     *
     * @return bool
     */
    public function getFormatCells()
    {
        return $this->formatCells;
    }

    /**
     * Set FormatCells.
     *
     * @param bool $formatCells
     *
     * @return $this
     */
    public function setFormatCells($formatCells)
    {
        $this->formatCells = $formatCells;

        return $this;
    }

    /**
     * Get FormatColumns.
     *
     * @return bool
     */
    public function getFormatColumns()
    {
        return $this->formatColumns;
    }

    /**
     * Set FormatColumns.
     *
     * @param bool $formatColumns
     *
     * @return $this
     */
    public function setFormatColumns($formatColumns)
    {
        $this->formatColumns = $formatColumns;

        return $this;
    }

    /**
     * Get FormatRows.
     *
     * @return bool
     */
    public function getFormatRows()
    {
        return $this->formatRows;
    }

    /**
     * Set FormatRows.
     *
     * @param bool $formatRows
     *
     * @return $this
     */
    public function setFormatRows($formatRows)
    {
        $this->formatRows = $formatRows;

        return $this;
    }

    /**
     * Get InsertColumns.
     *
     * @return bool
     */
    public function getInsertColumns()
    {
        return $this->insertColumns;
    }

    /**
     * Set InsertColumns.
     *
     * @param bool $insertColumns
     *
     * @return $this
     */
    public function setInsertColumns($insertColumns)
    {
        $this->insertColumns = $insertColumns;

        return $this;
    }

    /**
     * Get InsertRows.
     *
     * @return bool
     */
    public function getInsertRows()
    {
        return $this->insertRows;
    }

    /**
     * Set InsertRows.
     *
     * @param bool $insertRows
     *
     * @return $this
     */
    public function setInsertRows($insertRows)
    {
        $this->insertRows = $insertRows;

        return $this;
    }

    /**
     * Get InsertHyperlinks.
     *
     * @return bool
     */
    public function getInsertHyperlinks()
    {
        return $this->insertHyperlinks;
    }

    /**
     * Set InsertHyperlinks.
     *
     * @param bool $insertHyperLinks
     *
     * @return $this
     */
    public function setInsertHyperlinks($insertHyperLinks)
    {
        $this->insertHyperlinks = $insertHyperLinks;

        return $this;
    }

    /**
     * Get DeleteColumns.
     *
     * @return bool
     */
    public function getDeleteColumns()
    {
        return $this->deleteColumns;
    }

    /**
     * Set DeleteColumns.
     *
     * @param bool $deleteColumns
     *
     * @return $this
     */
    public function setDeleteColumns($deleteColumns)
    {
        $this->deleteColumns = $deleteColumns;

        return $this;
    }

    /**
     * Get DeleteRows.
     *
     * @return bool
     */
    public function getDeleteRows()
    {
        return $this->deleteRows;
    }

    /**
     * Set DeleteRows.
     *
     * @param bool $deleteRows
     *
     * @return $this
     */
    public function setDeleteRows($deleteRows)
    {
        $this->deleteRows = $deleteRows;

        return $this;
    }

    /**
     * Get SelectLockedCells.
     *
     * @return bool
     */
    public function getSelectLockedCells()
    {
        return $this->selectLockedCells;
    }

    /**
     * Set SelectLockedCells.
     *
     * @param bool $selectLockedCells
     *
     * @return $this
     */
    public function setSelectLockedCells($selectLockedCells)
    {
        $this->selectLockedCells = $selectLockedCells;

        return $this;
    }

    /**
     * Get Sort.
     *
     * @return bool
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set Sort.
     *
     * @param bool $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get AutoFilter.
     *
     * @return bool
     */
    public function getAutoFilter()
    {
        return $this->autoFilter;
    }

    /**
     * Set AutoFilter.
     *
     * @param bool $autoFilter
     *
     * @return $this
     */
    public function setAutoFilter($autoFilter)
    {
        $this->autoFilter = $autoFilter;

        return $this;
    }

    /**
     * Get PivotTables.
     *
     * @return bool
     */
    public function getPivotTables()
    {
        return $this->pivotTables;
    }

    /**
     * Set PivotTables.
     *
     * @param bool $pivotTables
     *
     * @return $this
     */
    public function setPivotTables($pivotTables)
    {
        $this->pivotTables = $pivotTables;

        return $this;
    }

    /**
     * Get SelectUnlockedCells.
     *
     * @return bool
     */
    public function getSelectUnlockedCells()
    {
        return $this->selectUnlockedCells;
    }

    /**
     * Set SelectUnlockedCells.
     *
     * @param bool $selectUnlockedCells
     *
     * @return $this
     */
    public function setSelectUnlockedCells($selectUnlockedCells)
    {
        $this->selectUnlockedCells = $selectUnlockedCells;

        return $this;
    }

    /**
     * Get hashed password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set Password.
     *
     * @param string $password
     * @param bool $alreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function setPassword($password, $alreadyHashed = false)
    {
        if (!$alreadyHashed) {
            $salt = $this->generateSalt();
            $this->setSalt($salt);
            $password = PasswordHasher::hashPassword($password, $this->getAlgorithm(), $this->getSalt(), $this->getSpinCount());
        }

        $this->password = $password;

        return $this;
    }

    /**
     * Create a pseudorandom string.
     */
    private function generateSalt(): string
    {
        return base64_encode(random_bytes(16));
    }

    /**
     * Get algorithm name.
     */
    public function getAlgorithm(): string
    {
        return $this->algorithm;
    }

    /**
     * Set algorithm name.
     */
    public function setAlgorithm(string $algorithm): void
    {
        $this->algorithm = $algorithm;
    }

    /**
     * Get salt value.
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * Set salt value.
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * Get spin count.
     */
    public function getSpinCount(): int
    {
        return $this->spinCount;
    }

    /**
     * Set spin count.
     */
    public function setSpinCount(int $spinCount): void
    {
        $this->spinCount = $spinCount;
    }

    /**
     * Verify that the given non-hashed password can "unlock" the protection.
     */
    public function verify(string $password): bool
    {
        if (!$this->isProtectionEnabled()) {
            return true;
        }

        $hash = PasswordHasher::hashPassword($password, $this->getAlgorithm(), $this->getSalt(), $this->getSpinCount());

        return $this->getPassword() === $hash;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
