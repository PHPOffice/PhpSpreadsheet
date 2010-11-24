To embed TrueType and OpenType font files, you need to extract the
font metrics from the font files and build the required tables using
the utility TTF2UFM.

TTF2UFM is a modified version of Mark Heath's TTF 2 PT1 converter 
(http://ttf2pt1.sourceforge.net/) by Steven Wittens <steven@acko.net> 
(http://www.acko.net/blog/ufpdf). That version has been further
modified by Ulrich Telle for use with the wxWidgets component
wxPdfDocument.

Following changes where made:

1) Generated AFM files contain the glyph number for each character.
2) Generated UFM files contain the bounding box for each character.
3) OpenType support has been activated for the Windows binary,
   and the generated AFM/UFM files contain the associated
   original Unicode codes for each character.
