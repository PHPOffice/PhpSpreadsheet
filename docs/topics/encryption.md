# File encryption

PhpSpreadsheet supports password-to-open encryption for `.xlsx` files. It
uses the Microsoft Office Agile encryption format, so the encrypted file can
be opened by supported versions of Microsoft Excel after the password is
entered.

## Reading an encrypted `.xlsx` file

Create an Xlsx reader, set its password, and then load the file:

```php
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$reader->setEncryptionPassword('open-password');
$spreadsheet = $reader->load('protected.xlsx');
```

## Writing an encrypted `.xlsx` file

Set a password on the Xlsx writer before saving:

```php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->setEncryptionPassword('open-password');
$writer->save('protected.xlsx');
```

To write a compatible AES/CBC profile explicitly, configure it before saving:

```php
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->setEncryptionPassword('open-password');
$writer->setEncryptionProfile(128, 'SHA1', 100000);
$writer->save('protected.xlsx');
```

The default AES-256/SHA-512 profile is recommended whenever the receiving
application supports it.

## Agile encryption profiles

The writer defaults to AES-256/CBC with SHA-512 and 100,000 password-hash
iterations. This is the recommended profile. It can also write AES-128 or
AES-192 with SHA1, SHA256, SHA384, or SHA512 when compatibility requires a
different profile. The reader accepts those AES/CBC and SHA profiles when the
file is protected with a password.

The reader does not attempt to decrypt custom encryption providers,
certificate-only encryption, CFB-mode encryption, or obsolete ciphers and
hashes. These profiles cannot be safely or reliably supported without a
separate interoperability contract.

## Scope and limitations

- Only password-to-open encryption for `.xlsx` is supported.
- Legacy `.xls` encryption, ODS encryption, password-to-modify, and IRM are
  not supported by this feature.
- Workbook, worksheet, and cell protection are separate features. They help
  prevent editing; they do not conceal workbook contents. See the
  [security recipe](./recipes.md#security).
- Encryption prevents opening the file without the password. It does not
  prevent copying, screenshots, or access after a successful open.

When writing an encrypted file, PhpSpreadsheet temporarily creates the normal
XLSX package before encrypting it. Configure PHP's temporary directory with
filesystem permissions appropriate to the sensitivity of the workbook.

## Specifications

ECMA-376 Part 2 defines an OPC package as a ZIP archive and prohibits
ZIP-level encryption (section 10.2.5). Its Annex C, Table C-5 identifies
general-purpose ZIP bit 0 as the encrypted-file flag. Encrypted Office
documents therefore use an OLE Compound File Binary container with a Data
Spaces structure; the XLSX package itself remains a normal, unencrypted ZIP
after it has been decrypted.

The [ECMA-376 standard](https://ecma-international.org/publications-and-standards/standards/ecma-376/)
defines the OOXML package format. The [ECMA-376 document-encryption security
guidance](https://learn.microsoft.com/en-us/openspecs/office_file_formats/ms-offcrypto/cab78f5c-9c17-495e-bea9-032c63f02ad8)
explains why Agile encryption uses CBC and integrity checking, and recommends
SHA-2 hashes rather than obsolete algorithms.
