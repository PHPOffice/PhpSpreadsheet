<?php

namespace PhpOffice\PhpSpreadsheet\Document;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;

/**
 * Copyright (c) 2006 - 2016 PhpSpreadsheet.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category   PhpSpreadsheet
 *
 * @copyright  Copyright (c) 2006 - 2016 PhpSpreadsheet (https://github.com/PHPOffice/PhpSpreadsheet)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 */
class Security
{
    /**
     * LockRevision.
     *
     * @var bool
     */
    private $lockRevision = false;

    /**
     * LockStructure.
     *
     * @var bool
     */
    private $lockStructure = false;

    /**
     * LockWindows.
     *
     * @var bool
     */
    private $lockWindows = false;

    /**
     * RevisionsPassword.
     *
     * @var string
     */
    private $revisionsPassword = '';

    /**
     * WorkbookPassword.
     *
     * @var string
     */
    private $workbookPassword = '';

    /**
     * Create a new Document Security instance.
     */
    public function __construct()
    {
    }

    /**
     * Is some sort of document security enabled?
     *
     * @return bool
     */
    public function isSecurityEnabled()
    {
        return  $this->lockRevision ||
                $this->lockStructure ||
                $this->lockWindows;
    }

    /**
     * Get LockRevision.
     *
     * @return bool
     */
    public function getLockRevision()
    {
        return $this->lockRevision;
    }

    /**
     * Set LockRevision.
     *
     * @param bool $pValue
     *
     * @return Security
     */
    public function setLockRevision($pValue)
    {
        $this->lockRevision = $pValue;

        return $this;
    }

    /**
     * Get LockStructure.
     *
     * @return bool
     */
    public function getLockStructure()
    {
        return $this->lockStructure;
    }

    /**
     * Set LockStructure.
     *
     * @param bool $pValue
     *
     * @return Security
     */
    public function setLockStructure($pValue)
    {
        $this->lockStructure = $pValue;

        return $this;
    }

    /**
     * Get LockWindows.
     *
     * @return bool
     */
    public function getLockWindows()
    {
        return $this->lockWindows;
    }

    /**
     * Set LockWindows.
     *
     * @param bool $pValue
     *
     * @return Security
     */
    public function setLockWindows($pValue)
    {
        $this->lockWindows = $pValue;

        return $this;
    }

    /**
     * Get RevisionsPassword (hashed).
     *
     * @return string
     */
    public function getRevisionsPassword()
    {
        return $this->revisionsPassword;
    }

    /**
     * Set RevisionsPassword.
     *
     * @param string $pValue
     * @param bool $pAlreadyHashed If the password has already been hashed, set this to true
     *
     * @return Security
     */
    public function setRevisionsPassword($pValue, $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PasswordHasher::hashPassword($pValue);
        }
        $this->revisionsPassword = $pValue;

        return $this;
    }

    /**
     * Get WorkbookPassword (hashed).
     *
     * @return string
     */
    public function getWorkbookPassword()
    {
        return $this->workbookPassword;
    }

    /**
     * Set WorkbookPassword.
     *
     * @param string $pValue
     * @param bool $pAlreadyHashed If the password has already been hashed, set this to true
     *
     * @return Security
     */
    public function setWorkbookPassword($pValue, $pAlreadyHashed = false)
    {
        if (!$pAlreadyHashed) {
            $pValue = PasswordHasher::hashPassword($pValue);
        }
        $this->workbookPassword = $pValue;

        return $this;
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
