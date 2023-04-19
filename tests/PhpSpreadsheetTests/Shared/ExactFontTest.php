<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\RichText\Run;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Style\Font as StyleFont;
use PHPUnit\Framework\TestCase;

class ExactFontTest extends TestCase
{
    // Results from this test are not necessarily portable between
    //   systems and Php Releases.
    // See https://github.com/php/php-src/issues/9073
    // Extra tests are added to determine if test should
    //   be marked incomplete.
    const EXTRA_FONTS = [
        'DejaVu Sans' => [
            'x' => 'DejaVuSans.ttf',
            'xb' => 'DejaVuSans-Bold.ttf',
            'xi' => 'DejaVuSans-Oblique.ttf',
            'xbi' => 'DejaVuSans-BoldOblique.ttf',
        ],
        'DejaVu Sans Mono' => [
            'x' => 'DejaVuSansMono.ttf',
            'xb' => 'DejaVuSansMono-Bold.ttf',
            'xi' => 'DejaVuSansMono-Oblique.ttf',
            'xbi' => 'DejaVuSansMono-BoldOblique.ttf',
        ],
        'DejaVu Serif Condensed' => [
            'x' => 'DejaVuSerifCondensed.ttf',
            'xb' => 'DejaVuSerifCondensed-Bold.ttf',
            'xi' => 'DejaVuSerifCondensed-Italic.ttf',
            'xbi' => 'DejaVuSerifCondensed-BoldItalic.ttf',
        ],
    ];

    /** @var string */
    private $holdDirectory;

    /** @var string */
    private $holdAutoSizeMethod;

    /** @var string */
    private $directoryName = '';

    /** @var string */
    private $incompleteMessage = '';

    private const KNOWN_MD5 = [
        '6a15e0a7c0367ba77a959ea27ebf11cf',
        'b0e31de57cd5307954a3c54136ce68ae',
        'be189a7e2711cdf2a7f6275c60cbc7e2',
    ];

    protected function setUp(): void
    {
        $this->holdDirectory = Font::getTrueTypeFontPath();
        $this->holdAutoSizeMethod = Font::getAutoSizeMethod();
        $direc = realpath('vendor/mpdf/mpdf/ttfonts') . DIRECTORY_SEPARATOR;
        $fontFile = 'DejaVuSans.ttf';
        $fontPath = $direc . $fontFile;
        $this->incompleteMessage = '';
        if (@is_readable($fontPath)) {
            $hash = md5_file($fontPath);
            if (!in_array($hash, self::KNOWN_MD5, true)) {
                $this->incompleteMessage = "Unrecognized Font file MD5 hash $hash";
            }
        } else {
            $this->incompleteMessage = 'Unable to locate font file';
        }
        $this->directoryName = $direc;
    }

    protected function tearDown(): void
    {
        Font::setTrueTypeFontPath($this->holdDirectory);
        Font::setAutoSizeMethod($this->holdAutoSizeMethod);
        $this->directoryName = '';
    }

    /** @dataProvider providerFontData */
    public function testExact(string $fontName, float $excelWidth, float $xmlWidth, float $winWidth, float $ubuntuWidth): void
    {
        if ($this->incompleteMessage !== '') {
            self::markTestIncomplete($this->incompleteMessage);
        }
        $font = new StyleFont();
        $font->setName($fontName);
        $font->setSize(11);
        Font::setTrueTypeFontPath($this->directoryName);
        Font::setExtraFontArray(self::EXTRA_FONTS);
        Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_EXACT);
        $exactWidth = Font::calculateColumnWidth($font, "This is $fontName");
        Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_APPROX);
        $approxWidth = Font::calculateColumnWidth($font, "This is $fontName");
        if ($excelWidth > 0) {
            self::assertGreaterThanOrEqual(max($excelWidth, $xmlWidth), $exactWidth);
            // Give ourselves a little wiggle room on upper bound.
            self::assertLessThanOrEqual(1.05 * max($winWidth, $ubuntuWidth), $exactWidth);
            self::assertNotEquals($exactWidth, $approxWidth);
        } else {
            self::assertEquals($exactWidth, $approxWidth, 'Use approx when exact font file not found');
        }
    }

    public static function providerFontData(): array
    {
        return [
            ['DejaVu Sans', 19.82, 20.453125, 22.5659, 21.709],
            ['DejaVu Sans Mono', 29.18, 29.81640625, 31.9922, 31.8494],
            ['DejaVu Serif Condensed', 29.55, 30.1796875, 31.9922, 31.1353],
            ['Arial', -29.55, 30.1796875, 31.9922, 31.1353],
        ];
    }

    public function testRichText(): void
    {
        // RichText treated as text, using Cell font, not Run Font
        $courier = new StyleFont();
        $courier->setName('Courier New');
        $courier->setSize(11);
        Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_APPROX);
        $element1 = new Run('A');
        $element2 = new Run('B');
        $element3 = new Run('C');
        $element1->setFont($courier);
        $element2->setFont($courier);
        $element3->setFont($courier);
        $richText = new RichText();
        $richText->setRichTextElements([$element1, $element2, $element3]);
        $arial = new StyleFont();
        $arial->setName('Arial');
        $arial->setSize(9);
        $widthRich = Font::calculateColumnWidth($arial, $richText);
        $widthText = Font::calculateColumnWidth($arial, 'ABC');
        self::assertSame($widthRich, $widthText);
    }
}
