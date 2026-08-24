<?php

namespace PhpOffice\PhpSpreadsheet\Shared\Xlsx;

use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Shared\File as SharedFile;
use PhpOffice\PhpSpreadsheet\Shared\OLE;
use SimpleXMLElement;
use Stringable;
use Throwable;

/** ECMA-376 Agile Encryption (AES-256/SHA-512 profile). */
final class AgileEncryption
{
    private const BLOCK_KEY_VERIFIER = "\xFE\xA7\xD2\x76\x3B\x4B\x9E\x79";

    private const BLOCK_KEY_VERIFIER_HASH = "\xD7\xAA\x0F\x6D\x30\x61\x34\x4E";

    private const BLOCK_KEY_ENCRYPTED_KEY = "\x14\x6E\x0B\xE7\xAB\xAC\xD0\xD6";

    private const BLOCK_KEY_HMAC_KEY = "\x5F\xB2\xAD\x01\x0C\xB9\xE1\xF6";

    private const BLOCK_KEY_HMAC_VALUE = "\xA0\x67\x7F\x02\xB2\x2C\x84\x33";

    private const BLOCK_SIZE = 16;

    private const SEGMENT_SIZE = 4096;

    /** @var array<string, array{string, int}> */
    private const HASH_ALGORITHMS = [
        'SHA-1' => ['sha1', 20],
        'SHA256' => ['sha256', 32],
        'SHA384' => ['sha384', 48],
        'SHA512' => ['sha512', 64],
    ];

    /**
     * @return array{keyDataSalt: string, passwordSalt: string, encryptedVerifier: string, encryptedVerifierHash: string, encryptedKey: string, encryptedHmacKey: string, encryptedHmacValue: string, spinCount: int, keyBits: int, hashAlgorithm: string, hashSize: int}
     */
    public static function parse(string $encryptionInfo): array
    {
        if (substr($encryptionInfo, 0, 8) !== "\x04\x00\x04\x00\x40\x00\x00\x00") {
            throw new Exception('Unsupported XLSX encryption profile.');
        }

        $xml = simplexml_load_string(substr($encryptionInfo, 8));
        if (!$xml instanceof SimpleXMLElement) {
            throw new Exception('Malformed XLSX encryption information.');
        }
        $keyData = $xml->keyData;
        $integrity = $xml->dataIntegrity;
        $encryptedKeys = $xml->xpath('//*[local-name()="encryptedKey"]') ?: [];
        $encryptedKey = $encryptedKeys[0] ?? null;
        $keyEncryptors = $xml->xpath('//*[local-name()="keyEncryptor"]') ?: [];
        $keyEncryptor = $keyEncryptors[0] ?? null;
        if (!$encryptedKey instanceof SimpleXMLElement) {
            throw new Exception('Unsupported XLSX encryption profile.');
        }
        $keyBits = (int) $keyData['keyBits'];
        $hashAlgorithm = (string) $keyData['hashAlgorithm'];
        $hashSize = (int) $keyData['hashSize'];
        $spinCount = (int) $encryptedKey['spinCount'];
        if (
            count($encryptedKeys) !== 1 || count($keyEncryptors) !== 1 || !$keyData instanceof SimpleXMLElement || !$integrity instanceof SimpleXMLElement || !$keyEncryptor instanceof SimpleXMLElement
            || (string) $keyData['cipherAlgorithm'] !== 'AES' || (string) $keyData['cipherChaining'] !== 'ChainingModeCBC'
            || !self::isSupportedProfile($keyBits, $hashAlgorithm, $hashSize) || (int) $keyData['saltSize'] !== 16 || (int) $keyData['blockSize'] !== 16
            || (string) $encryptedKey['cipherAlgorithm'] !== 'AES'
            || (string) $encryptedKey['cipherChaining'] !== 'ChainingModeCBC'
            || (string) $encryptedKey['hashAlgorithm'] !== $hashAlgorithm || (int) $encryptedKey['saltSize'] !== 16 || (int) $encryptedKey['blockSize'] !== 16 || (int) $encryptedKey['keyBits'] !== $keyBits
            || (int) $encryptedKey['hashSize'] !== $hashSize || $spinCount < 0 || $spinCount > 10000000
            || (string) $keyEncryptor['uri'] !== 'http://schemas.microsoft.com/office/2006/keyEncryptor/password'
        ) {
            throw new Exception('Unsupported XLSX encryption profile.');
        }

        $result = [
            'keyDataSalt' => self::decode($keyData['saltValue']),
            'passwordSalt' => self::decode($encryptedKey['saltValue']),
            'encryptedVerifier' => self::decode($encryptedKey['encryptedVerifierHashInput']),
            'encryptedVerifierHash' => self::decode($encryptedKey['encryptedVerifierHashValue']),
            'encryptedKey' => self::decode($encryptedKey['encryptedKeyValue']),
            'encryptedHmacKey' => self::decode($integrity['encryptedHmacKey']),
            'encryptedHmacValue' => self::decode($integrity['encryptedHmacValue']),
            'spinCount' => $spinCount,
            'keyBits' => $keyBits,
            'hashAlgorithm' => $hashAlgorithm,
            'hashSize' => $hashSize,
        ];
        if (
            strlen($result['keyDataSalt']) !== 16 || strlen($result['passwordSalt']) !== 16
            || strlen($result['encryptedVerifier']) !== 16 || strlen($result['encryptedVerifierHash']) !== self::paddedLength($hashSize)
            || strlen($result['encryptedKey']) !== self::paddedLength(intdiv($keyBits, 8)) || strlen($result['encryptedHmacKey']) !== self::paddedLength($hashSize)
            || strlen($result['encryptedHmacValue']) !== self::paddedLength($hashSize)
        ) {
            throw new Exception('Malformed XLSX encryption information.');
        }

        return $result;
    }

    /** @param array{keyDataSalt: string, passwordSalt: string, encryptedVerifier: string, encryptedVerifierHash: string, encryptedKey: string, encryptedHmacKey: string, encryptedHmacValue: string, spinCount: int, keyBits: int, hashAlgorithm: string, hashSize: int} $info */
    public static function decrypt(array $info, string $encryptedPackage, string $password): string
    {
        if ($password === '') {
            throw new Exception('XLSX encryption password required.');
        }
        if (strlen($encryptedPackage) < 8) {
            throw new Exception('Malformed encrypted XLSX package.');
        }
        $hash = self::passwordHash($password, $info['passwordSalt'], $info['spinCount'], $info['hashAlgorithm']);
        $verifierKey = self::deriveKey($hash, self::BLOCK_KEY_VERIFIER, $info['hashAlgorithm'], $info['keyBits']);
        $verifierHashKey = self::deriveKey($hash, self::BLOCK_KEY_VERIFIER_HASH, $info['hashAlgorithm'], $info['keyBits']);
        $verifier = self::aesDecrypt($info['encryptedVerifier'], $verifierKey, $info['passwordSalt'], $info['keyBits']);
        $expectedHash = self::aesDecrypt($info['encryptedVerifierHash'], $verifierHashKey, $info['passwordSalt'], $info['keyBits']);
        if (!hash_equals(self::hash($info['hashAlgorithm'], $verifier), substr($expectedHash, 0, $info['hashSize']))) {
            throw new Exception('XLSX encryption password is incorrect.');
        }
        $secretKey = self::aesDecrypt($info['encryptedKey'], self::deriveKey($hash, self::BLOCK_KEY_ENCRYPTED_KEY, $info['hashAlgorithm'], $info['keyBits']), $info['passwordSalt'], $info['keyBits']);
        $secretKey = substr($secretKey, 0, intdiv($info['keyBits'], 8));
        if (strlen($secretKey) !== intdiv($info['keyBits'], 8)) {
            throw new Exception('Malformed XLSX encryption information.');
        }
        self::verifyIntegrity($info, $secretKey, $encryptedPackage);
        $size = self::unpackSize(substr($encryptedPackage, 0, 8));
        $offset = 8;
        $plain = '';
        for ($block = 0; $size > 0; ++$block) {
            $length = min(self::SEGMENT_SIZE, $size);
            $cipherLength = (int) (ceil($length / self::BLOCK_SIZE) * self::BLOCK_SIZE);
            $cipher = substr($encryptedPackage, $offset, $cipherLength);
            if (strlen($cipher) !== $cipherLength) {
                throw new Exception('Malformed encrypted XLSX package.');
            }
            $iv = self::iv($info['keyDataSalt'], pack('V', $block), $info['hashAlgorithm']);
            $plain .= substr(self::aesDecrypt($cipher, $secretKey, $iv, $info['keyBits']), 0, $length);
            $offset += $cipherLength;
            $size -= $length;
        }
        if ($offset !== strlen($encryptedPackage)) {
            throw new Exception('Malformed encrypted XLSX package.');
        }

        return $plain;
    }

    /**
     * Create the EncryptionInfo and EncryptedPackage streams for an Agile-encrypted XLSX.
     *
     * @return array{encryptionInfo: string, encryptedPackage: string}
     */
    public static function encrypt(string $plainPackage, string $password, int $keyBits = 256, string $hashAlgorithm = 'SHA512', int $spinCount = 100000): array
    {
        if ($password === '') {
            throw new Exception('XLSX encryption password required.');
        }
        if (!self::isSupportedProfile($keyBits, $hashAlgorithm, self::HASH_ALGORITHMS[$hashAlgorithm][1] ?? 0) || $spinCount < 0 || $spinCount > 10000000) {
            throw new Exception('Unsupported XLSX encryption profile.');
        }

        $passwordSalt = random_bytes(self::BLOCK_SIZE);
        $keyDataSalt = random_bytes(self::BLOCK_SIZE);
        $hashSize = self::hashSize($hashAlgorithm);
        $passwordHash = self::passwordHash($password, $passwordSalt, $spinCount, $hashAlgorithm);
        $verifier = random_bytes(self::BLOCK_SIZE);
        $secretKey = random_bytes(self::keyLength($keyBits));
        $encryptedVerifier = self::aesEncrypt($verifier, self::deriveKey($passwordHash, self::BLOCK_KEY_VERIFIER, $hashAlgorithm, $keyBits), $passwordSalt, $keyBits);
        $encryptedVerifierHash = self::aesEncrypt(str_pad(self::hash($hashAlgorithm, $verifier), self::paddedLength($hashSize), "\x00"), self::deriveKey($passwordHash, self::BLOCK_KEY_VERIFIER_HASH, $hashAlgorithm, $keyBits), $passwordSalt, $keyBits);
        $encryptedKey = self::aesEncrypt(str_pad($secretKey, self::paddedLength(strlen($secretKey)), "\x00"), self::deriveKey($passwordHash, self::BLOCK_KEY_ENCRYPTED_KEY, $hashAlgorithm, $keyBits), $passwordSalt, $keyBits);

        $encryptedPackage = self::packSize(strlen($plainPackage));
        for ($block = 0, $offset = 0; $offset < strlen($plainPackage); ++$block, $offset += self::SEGMENT_SIZE) {
            $segment = substr($plainPackage, $offset, self::SEGMENT_SIZE);
            $iv = self::iv($keyDataSalt, pack('V', $block), $hashAlgorithm);
            $encryptedPackage .= self::aesEncrypt(str_pad($segment, self::paddedLength(strlen($segment)), "\x00"), $secretKey, $iv, $keyBits);
        }

        $hmacKey = random_bytes(self::hashSize($hashAlgorithm));
        $hmacKeyIv = self::iv($keyDataSalt, self::BLOCK_KEY_HMAC_KEY, $hashAlgorithm);
        $hmacValueIv = self::iv($keyDataSalt, self::BLOCK_KEY_HMAC_VALUE, $hashAlgorithm);
        $encryptedHmacKey = self::aesEncrypt(str_pad($hmacKey, self::paddedLength($hashSize), "\x00"), $secretKey, $hmacKeyIv, $keyBits);
        $encryptedHmacValue = self::aesEncrypt(str_pad(hash_hmac(self::HASH_ALGORITHMS[$hashAlgorithm][0], $encryptedPackage, $hmacKey, true), self::paddedLength($hashSize), "\x00"), $secretKey, $hmacValueIv, $keyBits);

        $encryptionInfo = "\x04\x00\x04\x00\x40\x00\x00\x00" . self::encryptionInfoXml(
            $keyDataSalt,
            $passwordSalt,
            $encryptedVerifier,
            $encryptedVerifierHash,
            $encryptedKey,
            $encryptedHmacKey,
            $encryptedHmacValue,
            $keyBits,
            $hashAlgorithm,
            $hashSize,
            $spinCount
        );

        return ['encryptionInfo' => $encryptionInfo, 'encryptedPackage' => $encryptedPackage];
    }

    /**
     * Encrypt a ZIP package without loading either package into memory.
     *
     * @return array{encryptionInfo: string, encryptedPackageFilename: string}
     */
    public static function encryptFile(string $plainPackageFilename, string $password, int $keyBits = 256, string $hashAlgorithm = 'SHA512', int $spinCount = 100000): array
    {
        if ($password === '') {
            throw new Exception('XLSX encryption password required.');
        }
        if (!self::isSupportedProfile($keyBits, $hashAlgorithm, self::HASH_ALGORITHMS[$hashAlgorithm][1] ?? 0) || $spinCount < 0 || $spinCount > 10000000) {
            throw new Exception('Unsupported XLSX encryption profile.');
        }
        $size = filesize($plainPackageFilename);
        if ($size === false) {
            throw new Exception('Could not determine XLSX package size.');
        }
        $input = fopen($plainPackageFilename, 'rb');
        if ($input === false) {
            throw new Exception('Could not open XLSX package for encryption.');
        }
        $encryptedPackageFilename = SharedFile::temporaryFilename();
        $output = fopen($encryptedPackageFilename, 'wb');
        if ($output === false) {
            fclose($input);
            @unlink($encryptedPackageFilename);

            throw new Exception('Could not create encrypted XLSX package.');
        }

        try {
            $passwordSalt = random_bytes(self::BLOCK_SIZE);
            $keyDataSalt = random_bytes(self::BLOCK_SIZE);
            $hashSize = self::hashSize($hashAlgorithm);
            $passwordHash = self::passwordHash($password, $passwordSalt, $spinCount, $hashAlgorithm);
            $verifier = random_bytes(self::BLOCK_SIZE);
            $secretKey = random_bytes(self::keyLength($keyBits));
            $encryptedVerifier = self::aesEncrypt($verifier, self::deriveKey($passwordHash, self::BLOCK_KEY_VERIFIER, $hashAlgorithm, $keyBits), $passwordSalt, $keyBits);
            $encryptedVerifierHash = self::aesEncrypt(str_pad(self::hash($hashAlgorithm, $verifier), self::paddedLength($hashSize), "\x00"), self::deriveKey($passwordHash, self::BLOCK_KEY_VERIFIER_HASH, $hashAlgorithm, $keyBits), $passwordSalt, $keyBits);
            $encryptedKey = self::aesEncrypt(str_pad($secretKey, self::paddedLength(strlen($secretKey)), "\x00"), self::deriveKey($passwordHash, self::BLOCK_KEY_ENCRYPTED_KEY, $hashAlgorithm, $keyBits), $passwordSalt, $keyBits);
            $hmacKey = random_bytes(self::hashSize($hashAlgorithm));
            $hmac = hash_init(self::HASH_ALGORITHMS[$hashAlgorithm][0], HASH_HMAC, $hmacKey);
            $header = self::packSize($size);
            self::write($output, $header);
            hash_update($hmac, $header);
            for ($block = 0; !feof($input); ++$block) {
                $segment = fread($input, self::SEGMENT_SIZE);
                if ($segment === false) {
                    throw new Exception('Could not read XLSX package for encryption.');
                }
                if ($segment === '') {
                    break;
                }
                $iv = self::iv($keyDataSalt, pack('V', $block), $hashAlgorithm);
                $cipher = self::aesEncrypt(str_pad($segment, self::paddedLength(strlen($segment)), "\x00"), $secretKey, $iv, $keyBits);
                self::write($output, $cipher);
                hash_update($hmac, $cipher);
            }
            $hmacKeyIv = self::iv($keyDataSalt, self::BLOCK_KEY_HMAC_KEY, $hashAlgorithm);
            $hmacValueIv = self::iv($keyDataSalt, self::BLOCK_KEY_HMAC_VALUE, $hashAlgorithm);
            $encryptedHmacKey = self::aesEncrypt(str_pad($hmacKey, self::paddedLength($hashSize), "\x00"), $secretKey, $hmacKeyIv, $keyBits);
            $encryptedHmacValue = self::aesEncrypt(str_pad(hash_final($hmac, true), self::paddedLength($hashSize), "\x00"), $secretKey, $hmacValueIv, $keyBits);
            $encryptionInfo = "\x04\x00\x04\x00\x40\x00\x00\x00" . self::encryptionInfoXml($keyDataSalt, $passwordSalt, $encryptedVerifier, $encryptedVerifierHash, $encryptedKey, $encryptedHmacKey, $encryptedHmacValue, $keyBits, $hashAlgorithm, $hashSize, $spinCount);
        } catch (Throwable $e) {
            @unlink($encryptedPackageFilename);

            throw $e;
        } finally {
            fclose($input);
            fclose($output);
        }

        return ['encryptionInfo' => $encryptionInfo, 'encryptedPackageFilename' => $encryptedPackageFilename];
    }

    /**
     * Write an Office encrypted package CFB container using a file-backed EncryptedPackage stream.
     *
     * @param resource $fileHandle
     */
    public static function writeContainerFromFile($fileHandle, string $encryptionInfo, string $encryptedPackageFilename): void
    {
        $containerFilename = SharedFile::temporaryFilename();
        $container = fopen($containerFilename, 'w+b');
        if ($container === false) {
            @unlink($containerFilename);

            throw new Exception('Could not create encrypted XLSX container.');
        }

        try {
            self::writeEcma376Container($container, $encryptionInfo, $encryptedPackageFilename);
            rewind($container);
            while (!feof($container)) {
                $data = fread($container, 8192);
                if ($data === false) {
                    throw new Exception('Could not read encrypted XLSX container.');
                }
                if ($data !== '' && fwrite($fileHandle, $data) !== strlen($data)) {
                    throw new Exception('Could not write encrypted XLSX container.');
                }
            }
        } finally {
            fclose($container);
            @unlink($containerFilename);
        }
    }

    /**
     * Serialize the ECMA-376 encrypted-package CFB layout. The directory tree
     * is prescribed by the Data Spaces structure; sector allocation is dynamic
     * so normal workbook size is not artificially constrained.
     *
     * @param resource $fileHandle
     */
    private static function writeEcma376Container($fileHandle, string $encryptionInfo, string $encryptedPackageFilename): void
    {
        $packageSize = filesize($encryptedPackageFilename);
        if ($packageSize === false || $packageSize < 4096 || $packageSize > 0xFFFFFFFF) {
            throw new Exception('Malformed encrypted XLSX package.');
        }
        $smallStreams = [
            3 => ['Version', self::dataSpacesVersion()],
            4 => ['DataSpaceMap', self::dataSpaceMap()],
            6 => ['StrongEncryptionDataSpace', self::strongEncryptionDataSpace()],
            9 => ["\x06Primary", self::primaryTransform()],
            10 => ['EncryptionInfo', $encryptionInfo],
        ];
        $miniStarts = [3 => 0, 4 => 0, 6 => 0, 9 => 0, 10 => 0];
        $miniFat = array_fill(0, 128, 0xFFFFFFFF);
        $miniCount = 0;
        foreach ($smallStreams as $id => [, $content]) {
            $count = (int) ceil(strlen($content) / 64);
            $miniStarts[$id] = $miniCount;
            for ($i = 0; $i < $count - 1; ++$i) {
                $miniFat[$miniCount + $i] = $miniCount + $i + 1;
            }
            $miniFat[$miniCount + $count - 1] = 0xFFFFFFFE;
            $miniCount += $count;
        }
        $miniDataSectors = (int) ceil($miniCount / 8);
        $directorySectors = 3;
        $packageSectors = (int) ceil($packageSize / 512);
        $contentSectors = 1 + $directorySectors + $miniDataSectors + $packageSectors;
        $fatSectors = 1;
        $difatSectors = 0;
        do {
            $requiredFatSectors = (int) ceil(($contentSectors + $fatSectors + $difatSectors) / 128);
            $requiredDifatSectors = $requiredFatSectors <= 109 ? 0 : (int) ceil(($requiredFatSectors - 109) / 127);
            $changed = $requiredFatSectors !== $fatSectors || $requiredDifatSectors !== $difatSectors;
            $fatSectors = $requiredFatSectors;
            $difatSectors = $requiredDifatSectors;
        } while ($changed);
        $miniFatSector = $fatSectors;
        $directorySector = $miniFatSector + 1;
        $miniDataSector = $directorySector + $directorySectors;
        $packageSector = $miniDataSector + $miniDataSectors;
        $difatSector = $packageSector + $packageSectors;

        self::write($fileHandle, self::cfbHeader($fatSectors, $directorySector, $miniFatSector, $difatSector, $difatSectors));
        $fat = array_fill(0, $fatSectors * 128, 0xFFFFFFFF);
        for ($sector = 0; $sector < $fatSectors; ++$sector) {
            $fat[$sector] = 0xFFFFFFFD;
        }
        self::chainFat($fat, $miniFatSector, 1);
        self::chainFat($fat, $directorySector, $directorySectors);
        self::chainFat($fat, $miniDataSector, $miniDataSectors);
        self::chainFat($fat, $packageSector, $packageSectors);
        for ($sector = 0; $sector < $difatSectors; ++$sector) {
            $fat[$difatSector + $sector] = 0xFFFFFFFC;
        }
        self::write($fileHandle, self::packSectors($fat));
        self::write($fileHandle, self::packSectors($miniFat));

        $entries = [
            self::directoryEntry('Root Entry', 5, 1, 0xFFFFFFFF, 0xFFFFFFFF, 1, $miniDataSector, $miniCount * 64),
            self::directoryEntry('EncryptedPackage', 2, 1, 2, 10, 0xFFFFFFFF, $packageSector, $packageSize),
            self::directoryEntry("\x06DataSpaces", 1, 0, 0xFFFFFFFF, 0xFFFFFFFF, 4, 0, 0),
            self::directoryEntry('Version', 2, 0, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, $miniStarts[3], strlen($smallStreams[3][1])),
            self::directoryEntry('DataSpaceMap', 2, 1, 5, 3, 0xFFFFFFFF, $miniStarts[4], strlen($smallStreams[4][1])),
            self::directoryEntry('DataSpaceInfo', 1, 0, 0xFFFFFFFF, 7, 6, 0, 0),
            self::directoryEntry('StrongEncryptionDataSpace', 2, 1, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, $miniStarts[6], strlen($smallStreams[6][1])),
            self::directoryEntry('TransformInfo', 1, 0, 0xFFFFFFFF, 0xFFFFFFFF, 8, 0, 0),
            self::directoryEntry('StrongEncryptionTransform', 1, 1, 0xFFFFFFFF, 0xFFFFFFFF, 9, 0, 0),
            self::directoryEntry("\x06Primary", 2, 1, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, $miniStarts[9], strlen($smallStreams[9][1])),
            self::directoryEntry('EncryptionInfo', 2, 0, 0xFFFFFFFF, 0xFFFFFFFF, 0xFFFFFFFF, $miniStarts[10], strlen($smallStreams[10][1])),
        ];
        self::write($fileHandle, str_pad(implode('', $entries), $directorySectors * 512, "\x00"));
        $miniData = str_repeat("\x00", $miniDataSectors * 512);
        foreach ($smallStreams as $id => [, $content]) {
            $miniData = substr_replace($miniData, $content, $miniStarts[$id] * 64, strlen($content));
        }
        self::write($fileHandle, $miniData);
        $package = fopen($encryptedPackageFilename, 'rb');
        if ($package === false) {
            throw new Exception('Could not open encrypted XLSX package.');
        }

        try {
            while (!feof($package)) {
                $data = fread($package, 8192);
                if ($data === false) {
                    throw new Exception('Could not read encrypted XLSX package.');
                }
                if ($data !== '') {
                    self::write($fileHandle, $data);
                }
            }
        } finally {
            fclose($package);
        }
        $padding = $packageSectors * 512 - $packageSize;
        if ($padding > 0) {
            self::write($fileHandle, str_repeat("\x00", $padding));
        }
        for ($sector = 0; $sector < $difatSectors; ++$sector) {
            $firstFatSector = 109 + $sector * 127;
            $fatReferences = [];
            for ($i = 0; $i < 127; ++$i) {
                $fatReferences[] = $firstFatSector + $i < $fatSectors ? $firstFatSector + $i : 0xFFFFFFFF;
            }
            $fatReferences[] = $sector + 1 < $difatSectors ? $difatSector + $sector + 1 : 0xFFFFFFFE;
            self::write($fileHandle, self::packSectors($fatReferences));
        }
    }

    /** @param int[] $fat */
    private static function chainFat(array &$fat, int $start, int $count): void
    {
        for ($i = 0; $i < $count - 1; ++$i) {
            $fat[$start + $i] = $start + $i + 1;
        }
        $fat[$start + $count - 1] = 0xFFFFFFFE;
    }

    /** @param int[] $sectors */
    private static function packSectors(array $sectors): string
    {
        return pack('V*', ...$sectors);
    }

    private static function cfbHeader(int $fatSectors, int $directorySector, int $miniFatSector, int $difatSector, int $difatSectors): string
    {
        $headerDifat = [];
        for ($sector = 0; $sector < 109; ++$sector) {
            $headerDifat[] = $sector < $fatSectors ? $sector : 0xFFFFFFFF;
        }

        return "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1" . str_repeat("\x00", 16)
            . pack('v8', 0x3E, 3, 0xFFFE, 9, 6, 0, 0, 0)
            . pack('V8', 0, $fatSectors, $directorySector, 0, 0x1000, $miniFatSector, 1, $difatSectors === 0 ? 0xFFFFFFFE : $difatSector)
            . pack('V', $difatSectors) . self::packSectors($headerDifat);
    }

    private static function directoryEntry(string $name, int $type, int $color, int $left, int $right, int $child, int $start, int $size): string
    {
        $name = OLE::ascToUcs($name);

        return str_pad($name, 64, "\x00") . pack('vCCVVV', strlen($name) + 2, $type, $color, $left, $right, $child)
            . str_repeat("\x00", 16) . pack('V', 0) . str_repeat("\x00", 16)
            . pack('V2', $start, $size % 4294967296) . pack('V', intdiv($size, 4294967296));
    }

    private static function decode(mixed $value): string
    {
        if (!is_string($value) && !$value instanceof Stringable) {
            throw new Exception('Malformed XLSX encryption information.');
        }
        $result = base64_decode((string) $value, true);
        if ($result === false) {
            throw new Exception('Malformed XLSX encryption information.');
        }

        return $result;
    }

    private static function passwordHash(string $password, string $salt, int $spinCount, string $algorithm = 'SHA512'): string
    {
        $hash = self::hash($algorithm, $salt . mb_convert_encoding($password, 'UTF-16LE', 'UTF-8'));
        for ($i = 0; $i < $spinCount; ++$i) {
            $hash = self::hash($algorithm, pack('V', $i) . $hash);
        }

        return $hash;
    }

    private static function deriveKey(string $hash, string $blockKey, string $algorithm = 'SHA512', int $keyBits = 256): string
    {
        return substr(str_pad(self::hash($algorithm, $hash . $blockKey), intdiv($keyBits, 8), "\x36"), 0, intdiv($keyBits, 8));
    }

    private static function aesDecrypt(string $data, string $key, string $iv, int $keyBits = 256): string
    {
        self::assertOpenSslAvailable();
        $result = openssl_decrypt($data, 'aes-' . $keyBits . '-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        if ($result === false) {
            throw new Exception('Malformed XLSX encrypted data.');
        }

        return $result;
    }

    private static function aesEncrypt(string $data, string $key, string $iv, int $keyBits = 256): string
    {
        self::assertOpenSslAvailable();
        $result = openssl_encrypt($data, 'aes-' . $keyBits . '-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        if ($result === false) {
            throw new Exception('Could not encrypt XLSX data.');
        }

        return $result;
    }

    private static function isSupportedProfile(int $keyBits, string $hashAlgorithm, int $hashSize): bool
    {
        return in_array($keyBits, [128, 192, 256], true)
            && isset(self::HASH_ALGORITHMS[$hashAlgorithm])
            && self::HASH_ALGORITHMS[$hashAlgorithm][1] === $hashSize;
    }

    private static function hash(string $algorithm, string $data): string
    {
        return hash(self::HASH_ALGORITHMS[$algorithm][0], $data, true);
    }

    /** @return positive-int */
    private static function hashSize(string $algorithm): int
    {
        return match ($algorithm) {
            'SHA-1' => 20,
            'SHA256' => 32,
            'SHA384' => 48,
            'SHA512' => 64,
            default => throw new Exception('Unsupported XLSX encryption profile.'),
        };
    }

    /** @return positive-int */
    private static function keyLength(int $keyBits): int
    {
        return match ($keyBits) {
            128 => 16,
            192 => 24,
            256 => 32,
            default => throw new Exception('Unsupported XLSX encryption profile.'),
        };
    }

    private static function iv(string $salt, string $blockKey, string $algorithm): string
    {
        return substr(str_pad(self::hash($algorithm, $salt . $blockKey), self::BLOCK_SIZE, "\x36"), 0, self::BLOCK_SIZE);
    }

    private static function paddedLength(int $length): int
    {
        return (int) (ceil($length / self::BLOCK_SIZE) * self::BLOCK_SIZE);
    }

    private static function assertOpenSslAvailable(): void
    {
        if (!function_exists('openssl_encrypt') || !function_exists('openssl_decrypt')) {
            throw new Exception('XLSX encryption requires the OpenSSL extension.');
        }
    }

    private static function packSize(int $size): string
    {
        if ($size < 0) {
            throw new Exception('XLSX package is too large to encrypt.');
        }

        return pack('V2', $size % 4294967296, intdiv($size, 4294967296));
    }

    private static function unpackSize(string $data): int
    {
        $unpackedSize = unpack('Vlow/Vhigh', $data);
        if ($unpackedSize === false || !isset($unpackedSize['low'], $unpackedSize['high']) || !is_int($unpackedSize['low']) || !is_int($unpackedSize['high'])) {
            throw new Exception('Malformed encrypted XLSX package.');
        }
        $size = $unpackedSize['low'] + $unpackedSize['high'] * 4294967296;
        if ($size > PHP_INT_MAX) {
            throw new Exception('Encrypted XLSX package is too large for this platform.');
        }

        return $size;
    }

    /** @param resource $fileHandle */
    private static function write($fileHandle, string $data): void
    {
        if (fwrite($fileHandle, $data) !== strlen($data)) {
            throw new Exception('Could not write encrypted XLSX package.');
        }
    }

    private static function encryptionInfoXml(string $keyDataSalt, string $passwordSalt, string $encryptedVerifier, string $encryptedVerifierHash, string $encryptedKey, string $encryptedHmacKey, string $encryptedHmacValue, int $keyBits = 256, string $hashAlgorithm = 'SHA512', int $hashSize = 64, int $spinCount = 100000): string
    {
        $base64 = static fn (string $value): string => base64_encode($value);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<encryption xmlns="http://schemas.microsoft.com/office/2006/encryption" xmlns:p="http://schemas.microsoft.com/office/2006/keyEncryptor/password">'
            . '<keyData saltSize="16" blockSize="16" keyBits="' . $keyBits . '" hashSize="' . $hashSize . '" cipherAlgorithm="AES" cipherChaining="ChainingModeCBC" hashAlgorithm="' . $hashAlgorithm . '" saltValue="' . $base64($keyDataSalt) . '"/>'
            . '<dataIntegrity encryptedHmacKey="' . $base64($encryptedHmacKey) . '" encryptedHmacValue="' . $base64($encryptedHmacValue) . '"/>'
            . '<keyEncryptors><keyEncryptor uri="http://schemas.microsoft.com/office/2006/keyEncryptor/password"><p:encryptedKey spinCount="' . $spinCount . '" saltSize="16" blockSize="16" keyBits="' . $keyBits . '" hashSize="' . $hashSize . '" cipherAlgorithm="AES" cipherChaining="ChainingModeCBC" hashAlgorithm="' . $hashAlgorithm . '" saltValue="' . $base64($passwordSalt) . '" encryptedVerifierHashInput="' . $base64($encryptedVerifier) . '" encryptedVerifierHashValue="' . $base64($encryptedVerifierHash) . '" encryptedKeyValue="' . $base64($encryptedKey) . '"/></keyEncryptor></keyEncryptors></encryption>';
    }

    private static function dataSpacesVersion(): string
    {
        return hex2bin('3c0000004d006900630072006f0073006f00660074002e0043006f006e007400610069006e00650072002e004400610074006100530070006100630065007300010000000100000001000000') ?: throw new Exception('Could not generate XLSX encryption DataSpaces stream.');
    }

    private static function primaryTransform(): string
    {
        return hex2bin('58000000010000004c0000007b00460046003900410033004600300033002d0035003600450046002d0034003600310033002d0042004400440035002d003500410034003100430031004400300037003200340036007d004e0000004d006900630072006f0073006f00660074002e0043006f006e007400610069006e00650072002e0045006e006300720079007000740069006f006e005400720061006e00730066006f0072006d00000001000000010000000100000000000000000000000000000004000000') ?: throw new Exception('Could not generate XLSX encryption DataSpaces stream.');
    }

    private static function dataSpaceMap(): string
    {
        return hex2bin('08000000010000006800000001000000000000002000000045006e0063007200790070007400650064005000610063006b00610067006500320000005300740072006f006e00670045006e006300720079007000740069006f006e004400610074006100530070006100630065000000') ?: throw new Exception('Could not generate XLSX encryption DataSpaces stream.');
    }

    private static function strongEncryptionDataSpace(): string
    {
        return hex2bin('0800000001000000320000005300740072006f006e00670045006e006300720079007000740069006f006e005400720061006e00730066006f0072006d000000') ?: throw new Exception('Could not generate XLSX encryption DataSpaces stream.');
    }

    /** @param array{keyDataSalt: string, passwordSalt: string, encryptedVerifier: string, encryptedVerifierHash: string, encryptedKey: string, encryptedHmacKey: string, encryptedHmacValue: string, spinCount: int, keyBits: int, hashAlgorithm: string, hashSize: int} $info */
    private static function verifyIntegrity(array $info, string $secretKey, string $encryptedPackage): void
    {
        $hmacKeyIv = self::iv($info['keyDataSalt'], self::BLOCK_KEY_HMAC_KEY, $info['hashAlgorithm']);
        $hmacValueIv = self::iv($info['keyDataSalt'], self::BLOCK_KEY_HMAC_VALUE, $info['hashAlgorithm']);
        $hmacKey = substr(self::aesDecrypt($info['encryptedHmacKey'], $secretKey, $hmacKeyIv, $info['keyBits']), 0, $info['hashSize']);
        $expected = self::aesDecrypt($info['encryptedHmacValue'], $secretKey, $hmacValueIv, $info['keyBits']);
        if (!hash_equals(hash_hmac(self::HASH_ALGORITHMS[$info['hashAlgorithm']][0], $encryptedPackage, $hmacKey, true), substr($expected, 0, $info['hashSize']))) {
            throw new Exception('Encrypted XLSX package integrity check failed.');
        }
    }
}
