<?php

namespace PhpOffice\PhpSpreadsheet\Document;

use PhpOffice\PhpSpreadsheet\Shared\PasswordHasher;

class Security
{
    /**
     * LockRevision.
     */
    private bool $lockRevision = false;

    /**
     * LockStructure.
     */
    private bool $lockStructure = false;

    /**
     * LockWindows.
     */
    private bool $lockWindows = false;

    /**
     * RevisionsPassword.
     */
    private string $revisionsPassword = '';

    /**
     * WorkbookPassword.
     */
    private string $workbookPassword = '';

    private string $workbookAlgorithmName = '';

    private string $workbookHashValue = '';

    private string $workbookSaltValue = '';

    private int $workbookSpinCount = 0;

    private string $revisionsAlgorithmName = '';

    private string $revisionsHashValue = '';

    private string $revisionsSaltValue = '';

    private int $revisionsSpinCount = 0;

    /**
     * Is some sort of document security enabled?
     */
    public function isSecurityEnabled(): bool
    {
        return $this->lockRevision
                || $this->lockStructure
                || $this->lockWindows;
    }

    public function getLockRevision(): bool
    {
        return $this->lockRevision;
    }

    public function setLockRevision(?bool $locked): self
    {
        if ($locked !== null) {
            $this->lockRevision = $locked;
        }

        return $this;
    }

    public function getLockStructure(): bool
    {
        return $this->lockStructure;
    }

    public function setLockStructure(?bool $locked): self
    {
        if ($locked !== null) {
            $this->lockStructure = $locked;
        }

        return $this;
    }

    public function getLockWindows(): bool
    {
        return $this->lockWindows;
    }

    public function setLockWindows(?bool $locked): self
    {
        if ($locked !== null) {
            $this->lockWindows = $locked;
        }

        return $this;
    }

    public function getRevisionsPassword(): string
    {
        return $this->revisionsPassword;
    }

    /**
     * Set RevisionsPassword.
     *
     * @param bool $alreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function setRevisionsPassword(?string $password, bool $alreadyHashed = false): static
    {
        if ($password !== null) {
            if ($this->advancedRevisionsPassword()) {
                if (!$alreadyHashed) {
                    $password = PasswordHasher::hashPassword($password, $this->revisionsAlgorithmName, $this->revisionsSaltValue, $this->revisionsSpinCount);
                }
                $this->revisionsHashValue = $password;
                $this->revisionsPassword = '';
            } else {
                if (!$alreadyHashed) {
                    $password = PasswordHasher::hashPassword($password);
                }
                $this->revisionsPassword = $password;
            }
        }

        return $this;
    }

    public function getWorkbookPassword(): string
    {
        return $this->workbookPassword;
    }

    /**
     * Set WorkbookPassword.
     *
     * @param bool $alreadyHashed If the password has already been hashed, set this to true
     *
     * @return $this
     */
    public function setWorkbookPassword(?string $password, bool $alreadyHashed = false): static
    {
        if ($password !== null) {
            if ($this->advancedPassword()) {
                if (!$alreadyHashed) {
                    $password = PasswordHasher::hashPassword($password, $this->workbookAlgorithmName, $this->workbookSaltValue, $this->workbookSpinCount);
                }
                $this->workbookHashValue = $password;
                $this->workbookPassword = '';
            } else {
                if (!$alreadyHashed) {
                    $password = PasswordHasher::hashPassword($password);
                }
                $this->workbookPassword = $password;
            }
        }

        return $this;
    }

    public function getWorkbookHashValue(): string
    {
        return $this->advancedPassword() ? $this->workbookHashValue : '';
    }

    public function advancedPassword(): bool
    {
        return $this->workbookAlgorithmName !== '' && $this->workbookSaltValue !== '' && $this->workbookSpinCount > 0;
    }

    public function getWorkbookAlgorithmName(): string
    {
        return $this->workbookAlgorithmName;
    }

    public function setWorkbookAlgorithmName(string $workbookAlgorithmName): static
    {
        $this->workbookAlgorithmName = $workbookAlgorithmName;

        return $this;
    }

    public function getWorkbookSpinCount(): int
    {
        return $this->workbookSpinCount;
    }

    public function setWorkbookSpinCount(int $workbookSpinCount): static
    {
        $this->workbookSpinCount = $workbookSpinCount;

        return $this;
    }

    public function getWorkbookSaltValue(): string
    {
        return $this->workbookSaltValue;
    }

    public function setWorkbookSaltValue(string $workbookSaltValue, bool $base64Required): static
    {
        $this->workbookSaltValue = $base64Required ? base64_encode($workbookSaltValue) : $workbookSaltValue;

        return $this;
    }

    public function getRevisionsHashValue(): string
    {
        return $this->advancedRevisionsPassword() ? $this->revisionsHashValue : '';
    }

    public function advancedRevisionsPassword(): bool
    {
        return $this->revisionsAlgorithmName !== '' && $this->revisionsSaltValue !== '' && $this->revisionsSpinCount > 0;
    }

    public function getRevisionsAlgorithmName(): string
    {
        return $this->revisionsAlgorithmName;
    }

    public function setRevisionsAlgorithmName(string $revisionsAlgorithmName): static
    {
        $this->revisionsAlgorithmName = $revisionsAlgorithmName;

        return $this;
    }

    public function getRevisionsSpinCount(): int
    {
        return $this->revisionsSpinCount;
    }

    public function setRevisionsSpinCount(int $revisionsSpinCount): static
    {
        $this->revisionsSpinCount = $revisionsSpinCount;

        return $this;
    }

    public function getRevisionsSaltValue(): string
    {
        return $this->revisionsSaltValue;
    }

    public function setRevisionsSaltValue(string $revisionsSaltValue, bool $base64Required): static
    {
        $this->revisionsSaltValue = $base64Required ? base64_encode($revisionsSaltValue) : $revisionsSaltValue;

        return $this;
    }
}
