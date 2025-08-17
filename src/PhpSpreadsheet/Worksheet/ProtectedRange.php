<?php

namespace PhpOffice\PhpSpreadsheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ProtectedRange
{
    private string $name = '';

    private string $password = '';

    private string $sqref;

    private string $securityDescriptor = '';

    /**
     * No setters aside from constructor.
     */
    public function __construct(string $sqref, string $password = '', string $name = '', string $securityDescriptor = '')
    {
        $this->sqref = $sqref;
        $this->name = $name;
        $this->password = $password;
        $this->securityDescriptor = $securityDescriptor;
    }

    public function getSqref(): string
    {
        return $this->sqref;
    }

    public function getName(): string
    {
        return $this->name ?: ('p' . md5($this->sqref));
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSecurityDescriptor(): string
    {
        return $this->securityDescriptor;
    }

    /**
     * Split range into coordinate strings.
     *
     * @return array<array<string>> Array containing one or more arrays containing one or two coordinate strings
     *                                e.g. ['B4','D9'] or [['B4','D9'], ['H2','O11']]
     *                                        or ['B4']
     */
    public function allRanges(): array
    {
        return Coordinate::allRanges($this->sqref, false);
    }
}
