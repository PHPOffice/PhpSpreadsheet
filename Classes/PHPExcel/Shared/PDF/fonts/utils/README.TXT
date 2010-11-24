TCPDF Fonts

TCPDF supports TrueTypeUnicode (UTF-8 Unicode), OpenTypeUnicode, TrueType, OpenType, Type1, CID-0 and Core (standard) fonts.

There are two ways to use a new font: embedding it in the PDF (with or without subsetting) or not. When a font is not embedded, it is searched in the system. The advantage is that the PDF file is lighter; on the other hand, if it is not available, a substitution font is used. So it is preferable to ensure that the needed font is installed on the client systems. If the file is to be viewed by a large audience, it is recommended to embed.

TCPDF support font subsetting to reduce the size of documents using large unicode font files.
If you embed the whole font in the PDF, the person on the other end can make changes to it even if he didn't have your font.
If you subset the font, file size of the PDF will be smaller but the person who receives your PDF would need to have your same font in order to make changes to your PDF.
The option for enabling/disabling the font subsetting are explained on the source code documentation for methods SetFont() and AddFont(). 

The fonts that could be not embedded are only the standard core fonts and CID-0 fonts.

The PDF Core (standard) fonts are:

    * courier : Courier
    * courierb : Courier Bold
    * courierbi : Courier Bold Italic
    * courieri : Courier Italic
    * helvetica : Helvetica
    * helveticab : Helvetica Bold
    * helveticabi : Helvetica Bold Italic
    * helveticai : Helvetica Italic
    * symbol : Symbol
    * times : Times New Roman
    * timesb : Times New Roman Bold
    * timesbi : Times New Roman Bold Italic
    * timesi : Times New Roman Italic
    * zapfdingbats : Zapf Dingbats

Setting up a font for usage with TCPDF requires the following steps:

   1. Convert all font filenames to lowercase and rename using the following schema:
          * [basic-font-name-in-lowercase].ttf for regular font
          * [basic-font-name-in-lowercase]b.ttf for bold variation
          * [basic-font-name-in-lowercase]i.ttf for oblique variation
          * [basic-font-name-in-lowercase]bi.ttf for bold oblique variation

   2. Generate the font's metrics file.
          * For Type1 font files this first step is not necessary because the AFM file is usually shipped with the font. In case you have only a metric file in PFM format, use the pfm2afm utility (fonts/utils/pfm2afm) to get the AFM file. If you own a Type1 font in ASCII format (.pfa), you can convert it to binary format with Type 1 utilities.
          * For TrueTypeUnicode or TrueType font files, use the the provided ttf2ufm utility (fonts/utils/ttf2ufm):

            $ ttf2ufm -a -F myfont.ttf

          * For OpenTypeUnicode or OpenType font files, use the the provided ttf2ufm utility (fonts/utils/ttf2ufm):

            $ ttf2ufm -a -F myfont.otf

   3. Run makefont.php script.
          * For TrueTypeUnicode:

            $ php -q makefont.php myfont.ttf myfont.ufm

          * For OpenTypeUnicode:

            $ php -q makefont.php myfont.otf myfont.ufm

          * For TrueType:

            $ php -q makefont.php myfont.ttf myfont.afm

          * For OpenType:

            $ php -q makefont.php myfont.otf myfont.afm

          * For Type1:

            $ php -q makefont.php myfont.pfb myfont.afm

      You may also specify additional parameters:

      MakeFont(string $fontfile, string $fmfile [, boolean $embedded [, $enc="cp1252" [, $patch=array()]]])

          * $fontfile : Path to the .ttf or .pfb file.
          * $fmfile : Path to the .afm file for Type1 and TrueType or .ufm for TrueTypeUnicode.
          * $embedded : Set to false to not embed the font, true otherwise (default).
          * $enc : Name of the encoding table to use. Default value: cp1252. Omit this parameter for TrueType Unicode, OpenType Unicode and symbolic fonts like Symbol or ZapfDingBats. The encoding defines the association between a code (from 0 to 255) and a character. The first 128 are fixed and correspond to ASCII. The encodings are stored in .map files. Those available are:
                o cp1250 (Central Europe)
                o cp1251 (Cyrillic)
                o cp1252 (Western Europe)
                o cp1253 (Greek)
                o cp1254 (Turkish)
                o cp1255 (Hebrew)
                o cp1257 (Baltic)
                o cp1258 (Vietnamese)
                o cp874 (Thai)
                o iso-8859-1 (Western Europe)
                o iso-8859-2 (Central Europe)
                o iso-8859-4 (Baltic)
                o iso-8859-5 (Cyrillic)
                o iso-8859-7 (Greek)
                o iso-8859-9 (Turkish)
                o iso-8859-11 (Thai)
                o iso-8859-15 (Western Europe)
                o iso-8859-16 (Central Europe)
                o koi8-r (Russian)
                o koi8-u (Ukrainian)
            Of course, the font must contain the characters corresponding to the chosen encoding. The encodings which begin with cp are those used by Windows; Linux systems usually use ISO.
          * $patch : Optional modification of the encoding. Empty by default. This parameter gives the possibility to alter the encoding. Sometimes you may want to add some characters. For instance, ISO-8859-1 does not contain the euro symbol. To add it at position 164, pass array(164=>'Euro').

   4. Edit and copy resulting files by case:
          * For embedded fonts: copy the resulting .php, .z and .ctg.z (if available) files to the TCPDF fonts directory.
          * For not-embedding the font, edit the .php file and comment the $file entry.
          * For CID-0 fonts (not embeddeed) you have to edit the .php file:
                o change the font type to: $type='cidfont0';
                o set the default font width by adding the line: $dw=1000;
                o remove the $enc, $file and $ctg variables definitions
                o add one of the following blocks of text at the end of the file (depends by the language you are using - see the arialunicid0.php file for a working example):
                      + // Chinese Simplified
                        $enc='UniCNS-UTF16-H';
                        $cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'CNS1','Supplement'=>0);
                        include(dirname(__FILE__).'/uni2cid_ac15.php');

                      + // Chinese Traditional
                        $enc='UniGB-UTF16-H';
                        $cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'GB1','Supplement'=>2);
                        include(dirname(__FILE__).'/uni2cid_ag15.php');

                      + // Korean
                        $enc='UniKS-UTF16-H';
                        $cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'Korea1','Supplement'=>0);
                        include(dirname(__FILE__).'/uni2cid_ak12.php');

                      + // Japanese
                        $enc='UniJIS-UTF16-H';
                        $cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'Japan1','Supplement'=>5);
                        include(dirname(__FILE__).'/uni2cid_aj16.php');

                o copy the .php file to the TCPDF fonts directory.
   5. Rename php font files variations using the following schema:
          * [basic-font-name-in-lowercase].php for regular font
          * [basic-font-name-in-lowercase]b.php for bold variation
          * [basic-font-name-in-lowercase]i.php for oblique variation
          * [basic-font-name-in-lowercase]bi.php for bold oblique variation

