<?php
//============================================================+
// File name   : tcpdf.php
// Begin       : 2002-08-03
// Last Update : 2009-09-30
// Author      : Nicola Asuni - info@tecnick.com - http://www.tcpdf.org
// Version     : 4.8.009
// License     : GNU LGPL (http://www.gnu.org/copyleft/lesser.html)
// 	----------------------------------------------------------------------------
//  Copyright (C) 2002-2009  Nicola Asuni - Tecnick.com S.r.l.
// 	
// 	This program is free software: you can redistribute it and/or modify
// 	it under the terms of the GNU Lesser General Public License as published by
// 	the Free Software Foundation, either version 2.1 of the License, or
// 	(at your option) any later version.
// 	
// 	This program is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU Lesser General Public License for more details.
// 	
// 	You should have received a copy of the GNU Lesser General Public License
// 	along with this program.  If not, see <http://www.gnu.org/licenses/>.
// 	
// 	See LICENSE.TXT file for more information.
//  ----------------------------------------------------------------------------
//
// Description : This is a PHP class for generating PDF documents without 
//               requiring external extensions.
//
// NOTE:
// This class was originally derived in 2002 from the Public 
// Domain FPDF class by Olivier Plathey (http://www.fpdf.org), 
// but now is almost entirely rewritten.
//
// Main features:
//  * no external libraries are required for the basic functions;
// 	* supports all ISO page formats;
// 	* supports custom page formats, margins and units of measure;
// 	* supports UTF-8 Unicode and Right-To-Left languages;
// 	* supports TrueTypeUnicode, OpenTypeUnicode, TrueType, OpenType, Type1 and CID-0 fonts;
// 	* supports document encryption;
// 	* includes methods to publish some XHTML code, including forms;
// 	* includes graphic (geometric) and transformation methods;
// 	* includes Javascript and Forms support;
// 	* includes a method to print various barcode formats: CODE 39, ANSI MH10.8M-1983, USD-3, 3 of 9, CODE 93, USS-93, Standard 2 of 5, Interleaved 2 of 5, CODE 128 A/B/C, 2 and 5 Digits UPC-Based Extention, EAN 8, EAN 13, UPC-A, UPC-E, MSI, POSTNET, PLANET, RMS4CC (Royal Mail 4-state Customer Code), CBC (Customer Bar Code), KIX (Klant index - Customer index), Intelligent Mail Barcode, Onecode, USPS-B-3200, CODABAR, CODE 11, PHARMACODE, PHARMACODE TWO-TRACKS;
// 	* includes methods to set Bookmarks and print a Table of Content;
// 	* includes methods to move and delete pages;
// 	* includes methods for automatic page header and footer management;
// 	* supports automatic page break;
// 	* supports automatic page numbering and page groups;
// 	* supports automatic line break and text justification;
// 	* supports JPEG and PNG images natively, all images supported by GD (GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM) and all images supported via ImagMagick (http://www.imagemagick.org/www/formats.html)
// 	* supports stroke and clipping mode for text;
// 	* supports clipping masks;
// 	* supports Grayscale, RGB, CMYK, Spot Colors and Transparencies;
// 	* supports several annotations, including links, text and file attachments;
// 	* supports page compression (requires zlib extension);
//  * supports text hyphenation.
//  * supports transactions to UNDO commands.
//  * supports signature certifications.
//
// -----------------------------------------------------------
// THANKS TO:
// 
// Olivier Plathey (http://www.fpdf.org) for original FPDF.
// Efthimios Mavrogeorgiadis (emavro@yahoo.com) for suggestions on RTL language support.
// Klemen Vodopivec (http://www.fpdf.de/downloads/addons/37/) for Encryption algorithm.
// Warren Sherliker (wsherliker@gmail.com) for better image handling.
// dullus for text Justification.
// Bob Vincent (pillarsdotnet@users.sourceforge.net) for <li> value attribute.
// Patrick Benny for text stretch suggestion on Cell().
// Johannes Güntert for JavaScript support.
// Denis Van Nuffelen for Dynamic Form.
// Jacek Czekaj for multibyte justification
// Anthony Ferrara for the reintroduction of legacy image methods.
// Sourceforge user 1707880 (hucste) for line-trough mode.
// Larry Stanbery for page groups.
// Martin Hall-May for transparency.
// Aaron C. Spike for Polycurve method.
// Mohamad Ali Golkar, Saleh AlMatrafe, Charles Abbott for Arabic and Persian support.
// Moritz Wagner and Andreas Wurmser for graphic functions.
// Andrew Whitehead for core fonts support.
// Esteban Joël Marín for OpenType font conversion.
// Teus Hagen for several suggestions and fixes.
// Yukihiro Nakadaira for CID-0 CJK fonts fixes.
// Kosmas Papachristos for some CSS improvements.
// Marcel Partap for some fixes.
// Won Kyu Park for several suggestions, fixes and patches.
// Anyone that has reported a bug or sent a suggestion.
//============================================================+

/**
 * This is a PHP class for generating PDF documents without requiring external extensions.<br>
 * TCPDF project (http://www.tcpdf.org) was originally derived in 2002 from the Public Domain FPDF class by Olivier Plathey (http://www.fpdf.org), but now is almost entirely rewritten.<br>
 * <h3>TCPDF main features are:</h3>
 * <ul>
* <li>no external libraries are required for the basic functions;</li>
* <li>supports all ISO page formats;</li>
* <li>supports custom page formats, margins and units of measure;</li>
* <li>supports UTF-8 Unicode and Right-To-Left languages;</li>
* <li>supports TrueTypeUnicode, OpenTypeUnicode, TrueType, OpenType, Type1 and CID-0 fonts;</li>
* <li>supports document encryption;</li>
* <li>includes methods to publish some XHTML code, including forms;</li>
* <li>includes graphic (geometric) and transformation methods;</li>
* <li>includes Javascript and Forms support;</li>
* <li>includes a method to print various barcode formats: CODE 39, ANSI MH10.8M-1983, USD-3, 3 of 9, CODE 93, USS-93, Standard 2 of 5, Interleaved 2 of 5, CODE 128 A/B/C, 2 and 5 Digits UPC-Based Extention, EAN 8, EAN 13, UPC-A, UPC-E, MSI, POSTNET, PLANET, RMS4CC (Royal Mail 4-state Customer Code), CBC (Customer Bar Code), KIX (Klant index - Customer index), Intelligent Mail Barcode, Onecode, USPS-B-3200, CODABAR, CODE 11, PHARMACODE, PHARMACODE TWO-TRACKS;</li>
* <li>includes methods to set Bookmarks and print a Table of Content;</li>
* <li>includes methods to move and delete pages;</li>
* <li>includes methods for automatic page header and footer management;</li>
* <li>supports automatic page break;</li>
* <li>supports automatic page numbering and page groups;</li>
* <li>supports automatic line break and text justification;</li>
* <li>supports JPEG and PNG images natively, all images supported by GD (GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM) and all images supported via ImagMagick (http://www.imagemagick.org/www/formats.html)</li>
* <li>supports stroke and clipping mode for text;</li>
* <li>supports clipping masks;</li>
* <li>supports Grayscale, RGB, CMYK, Spot Colors and Transparencies;</li>
* <li>supports several annotations, including links, text and file attachments;</li>
* <li>supports page compression (requires zlib extension);</li>
* <li>supports text hyphenation.</li>
* <li>supports transactions to UNDO commands.</li>
* <li>supports signature certifications.</li>
 * </ul>
 * Tools to encode your unicode fonts are on fonts/utils directory.</p>
 * @package com.tecnick.tcpdf
 * @abstract Class for generating PDF files on-the-fly without requiring external extensions.
 * @author Nicola Asuni
 * @copyright 2002-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 4.8.009
 */

/**
 * main configuration file
 */
require_once(dirname(__FILE__).'/config/tcpdf_config.php');

// includes some support files

/**
 * unicode data
 */
require_once(dirname(__FILE__).'/unicode_data.php');

/**
 * html colors table
 */
require_once(dirname(__FILE__).'/htmlcolors.php');

if (!class_exists('TCPDF', false)) {
	/**
	 * define default PDF document producer
	 */ 
	define('PDF_PRODUCER', 'TCPDF 4.8.009 (http://www.tcpdf.org)');
	
	/**
	* This is a PHP class for generating PDF documents without requiring external extensions.<br>
	* TCPDF project (http://www.tcpdf.org) has been originally derived in 2002 from the Public Domain FPDF class by Olivier Plathey (http://www.fpdf.org), but now is almost entirely rewritten.<br>
	* @name TCPDF
	* @package com.tecnick.tcpdf
	* @version 4.8.009
	* @author Nicola Asuni - info@tecnick.com
	* @link http://www.tcpdf.org
	* @license http://www.gnu.org/copyleft/lesser.html LGPL
	*/
	class TCPDF {
		
		// protected or Protected properties

		/**
		* @var current page number
		* @access protected
		*/
		protected $page;
		
		/**
		* @var current object number
		* @access protected
		*/
		protected $n;

		/**
		* @var array of object offsets
		* @access protected
		*/
		protected $offsets;

		/**
		* @var buffer holding in-memory PDF
		* @access protected
		*/
		protected $buffer;

		/**
		* @var array containing pages
		* @access protected
		*/
		protected $pages = array();

		/**
		* @var current document state
		* @access protected
		*/
		protected $state;

		/**
		* @var compression flag
		* @access protected
		*/
		protected $compress;
		
		/**
		* @var current page orientation (P = Portrait, L = Landscape)
		* @access protected
		*/
		protected $CurOrientation;

		/**
		* @var array that stores page dimensions and graphic status.<ul><li>$this->pagedim[$this->page]['w'] => page_width_in_points</li><li>$this->pagedim[$this->page]['h'] => height in points</li><li>$this->pagedim[$this->page]['wk'] => page_width_in_points</li><li>$this->pagedim[$this->page]['hk'] => height</li><li>$this->pagedim[$this->page]['tm'] => top_margin</li><li>$this->pagedim[$this->page]['bm'] => bottom_margin</li><li>$this->pagedim[$this->page]['lm'] => left_margin</li><li>$this->pagedim[$this->page]['rm'] => right_margin</li><li>$this->pagedim[$this->page]['pb'] => auto_page_break</li><li>$this->pagedim[$this->page]['or'] => page_orientation</li><li>$this->pagedim[$this->page]['olm'] => original_left_margin</li><li>$this->pagedim[$this->page]['orm'] => original_right_margin</li></ul>
		* @access protected
		*/
		protected $pagedim = array();

		/**
		* @var scale factor (number of points in user unit)
		* @access protected
		*/
		protected $k;

		/**
		* @var width of page format in points
		* @access protected
		*/
		protected $fwPt;

		/**
		* @var height of page format in points
		* @access protected
		*/
		protected $fhPt;

		/**
		* @var current width of page in points
		* @access protected
		*/
		protected $wPt;

		/**
		* @var current height of page in points
		* @access protected
		*/
		protected $hPt;

		/**
		* @var current width of page in user unit
		* @access protected
		*/
		protected $w;

		/**
		* @var current height of page in user unit
		* @access protected
		*/
		protected $h;

		/**
		* @var left margin
		* @access protected
		*/
		protected $lMargin;

		/**
		* @var top margin
		* @access protected
		*/
		protected $tMargin;

		/**
		* @var right margin
		* @access protected
		*/
		protected $rMargin;

		/**
		* @var page break margin
		* @access protected
		*/
		protected $bMargin;

		/**
		* @var cell internal padding
		* @access protected
		*/
		//protected
		public $cMargin;
		
		/**
		* @var cell internal padding (previous value)
		* @access protected
		*/
		protected $oldcMargin;

		/**
		* @var current horizontal position in user unit for cell positioning
		* @access protected
		*/
		protected $x;

		/**
		* @var current vertical position in user unit for cell positioning
		* @access protected
		*/
		protected $y;

		/**
		* @var height of last cell printed
		* @access protected
		*/
		protected $lasth;

		/**
		* @var line width in user unit
		* @access protected
		*/
		protected $LineWidth;

		/**
		* @var array of standard font names
		* @access protected
		*/
		protected $CoreFonts;

		/**
		* @var array of used fonts
		* @access protected
		*/
		protected $fonts = array();

		/**
		* @var array of font files
		* @access protected
		*/
		protected $FontFiles = array();

		/**
		* @var array of encoding differences
		* @access protected
		*/
		protected $diffs = array();

		/**
		* @var array of used images
		* @access protected
		*/
		protected $images = array();

		/**
		* @var array of Annotations in pages
		* @access protected
		*/
		protected $PageAnnots = array();

		/**
		* @var array of internal links
		* @access protected
		*/
		protected $links = array();

		/**
		* @var current font family
		* @access protected
		*/
		protected $FontFamily;

		/**
		* @var current font style
		* @access protected
		*/
		protected $FontStyle;
		
		/**
		* @var current font ascent (distance between font top and baseline)
		* @access protected
		* @since 2.8.000 (2007-03-29)
		*/
		protected $FontAscent;
		
		/**
		* @var current font descent (distance between font bottom and baseline)
		* @access protected
		* @since 2.8.000 (2007-03-29)
		*/
		protected $FontDescent;

		/**
		* @var underlining flag
		* @access protected
		*/
		protected $underline;

		/**
		* @var current font info
		* @access protected
		*/
		protected $CurrentFont;

		/**
		* @var current font size in points
		* @access protected
		*/
		protected $FontSizePt;

		/**
		* @var current font size in user unit
		* @access protected
		*/
		protected $FontSize;

		/**
		* @var commands for drawing color
		* @access protected
		*/
		protected $DrawColor;

		/**
		* @var commands for filling color
		* @access protected
		*/
		protected $FillColor;

		/**
		* @var commands for text color
		* @access protected
		*/
		protected $TextColor;

		/**
		* @var indicates whether fill and text colors are different
		* @access protected
		*/
		protected $ColorFlag;

		/**
		* @var automatic page breaking
		* @access protected
		*/
		protected $AutoPageBreak;

		/**
		* @var threshold used to trigger page breaks
		* @access protected
		*/
		protected $PageBreakTrigger;

		/**
		* @var flag set when processing footer
		* @access protected
		*/
		protected $InFooter = false;

		/**
		* @var zoom display mode
		* @access protected
		*/
		protected $ZoomMode;

		/**
		* @var layout display mode
		* @access protected
		*/
		protected $LayoutMode;

		/**
		* @var title
		* @access protected
		*/
		protected $title = '';

		/**
		* @var subject
		* @access protected
		*/
		protected $subject = '';

		/**
		* @var author
		* @access protected
		*/
		protected $author = '';

		/**
		* @var keywords
		* @access protected
		*/
		protected $keywords = '';

		/**
		* @var creator
		* @access protected
		*/
		protected $creator = '';

		/**
		* @var alias for total number of pages
		* @access protected
		*/
		protected $AliasNbPages = '{nb}';
		
		/**
		* @var alias for page number
		* @access protected
		*/
		protected $AliasNumPage = '{pnb}';
		
		/**
		* @var right-bottom corner X coordinate of inserted image
		* @since 2002-07-31
		* @author Nicola Asuni
		* @access protected
		*/
		protected $img_rb_x;

		/**
		* @var right-bottom corner Y coordinate of inserted image
		* @since 2002-07-31
		* @author Nicola Asuni
		* @access protected
		*/
		protected $img_rb_y;

		/**
		* @var adjusting factor to convert pixels to user units.
		* @since 2004-06-14
		* @author Nicola Asuni
		* @access protected
		*/
		protected $imgscale = 1;

		/**
		* @var boolean set to true when the input text is unicode (require unicode fonts)
		* @since 2005-01-02
		* @author Nicola Asuni
		* @access protected
		*/
		protected $isunicode = false;

		/**
		* @var PDF version
		* @since 1.5.3
		* @access protected
		*/
		protected $PDFVersion = '1.7';
		
		
		// ----------------------
		
		/**
		 * @var Minimum distance between header and top page margin.
		 * @access protected
		 */
		protected $header_margin;
		
		/**
		 * @var Minimum distance between footer and bottom page margin.
		 * @access protected
		 */
		protected $footer_margin;
		
		/**
		 * @var original left margin value
		 * @access protected
		 * @since 1.53.0.TC013
		 */
		protected $original_lMargin;
		
		/**
		 * @var original right margin value
		 * @access protected
		 * @since 1.53.0.TC013
		 */
		protected $original_rMargin;
			
		/**
		 * @var Header font.
		 * @access protected
		 */
		protected $header_font;
		
		/**
		 * @var Footer font.
		 * @access protected
		 */
		protected $footer_font;
		
		/**
		 * @var Language templates.
		 * @access protected
		 */
		protected $l;
		
		/**
		 * @var Barcode to print on page footer (only if set).
		 * @access protected
		 */
		protected $barcode = false;
		
		/**
		 * @var If true prints header
		 * @access protected
		 */
		protected $print_header = true;
		
		/**
		 * @var If true prints footer.
		 * @access protected
		 */
		protected $print_footer = true;
			
		/**
		 * @var Header image logo.
		 * @access protected
		 */
		protected $header_logo = '';
		
		/**
		 * @var Header image logo width in mm.
		 * @access protected
		 */
		protected $header_logo_width = 30;
		
		/**
		 * @var String to print as title on document header.
		 * @access protected
		 */
		protected $header_title = '';
		
		/**
		 * @var String to print on document header.
		 * @access protected
		 */
		protected $header_string = '';
		
		/**
		 * @var Default number of columns for html table.
		 * @access protected
		 */
		protected $default_table_columns = 4;
		
		
		// variables for html parser
		
		/**
		 * @var HTML PARSER: array to store current link and rendering styles.
		 * @access protected
		 */
		protected $HREF = array();
		
		/**
		 * @var store a list of available fonts on filesystem.
		 * @access protected
		 */
		protected $fontlist = array();
		
		/**
		 * @var current foreground color
		 * @access protected
		 */
		protected $fgcolor;
						
		/**
		 * @var HTML PARSER: array of boolean values, true in case of ordered list (OL), false otherwise.
		 * @access protected
		 */
		protected $listordered = array();
		
		/**
		 * @var HTML PARSER: array count list items on nested lists.
		 * @access protected
		 */
		protected $listcount = array();
		
		/**
		 * @var HTML PARSER: current list nesting level.
		 * @access protected
		 */
		protected $listnum = 0;
		
		/**
		 * @var HTML PARSER: indent amount for lists.
		 * @access protected
		 */
		protected $listindent;
		
		/**
		 * @var current background color
		 * @access protected
		 */
		protected $bgcolor;
		
		/**
		 * @var Store temporary font size in points.
		 * @access protected
		 */
		protected $tempfontsize = 10;
		
		/**
		 * @var spacer for LI tags.
		 * @access protected
		 */
		protected $lispacer = '';
		
		/**
		 * @var default encoding
		 * @access protected
		 * @since 1.53.0.TC010
		 */
		protected $encoding = 'UTF-8';
		
		/**
		 * @var PHP internal encoding
		 * @access protected
		 * @since 1.53.0.TC016
		 */
		protected $internal_encoding;
		
		/**
		 * @var indicates if the document language is Right-To-Left
		 * @access protected
		 * @since 2.0.000
		 */
		protected $rtl = false;
		
		/**
		 * @var used to force RTL or LTR string inversion
		 * @access protected
		 * @since 2.0.000
		 */
		protected $tmprtl = false;
		
		// --- Variables used for document encryption:
		
		/**
		 * Indicates whether document is protected
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $encrypted;
		
		/**
		 * U entry in pdf document
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $Uvalue;
		
		/**
		 * O entry in pdf document
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $Ovalue;
		
		/**
		 * P entry in pdf document
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $Pvalue;
		
		/**
		 * encryption object id
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $enc_obj_id;
		
		/**
		 * last RC4 key encrypted (cached for optimisation)
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $last_rc4_key;
		
		/**
		 * last RC4 computed key
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected $last_rc4_key_c;
		
		/**
		 * RC4 padding
		 * @access protected
		 */
		protected $padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
		
		/**
		 * RC4 encryption key
		 * @access protected
		 */
		protected $encryption_key;
		
		// --- bookmark ---
		
		/**
		 * Outlines for bookmark
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $outlines = array();
		
		/**
		 * Outline root for bookmark
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $OutlineRoot;
		
		
		// --- javascript and form ---
		
		/**
		 * javascript code
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $javascript = '';
		
		/**
		 * javascript counter
		 * @access protected
		 * @since 2.1.002 (2008-02-12)
		 */
		protected $n_js;

		/**
		 * line trough state
		 * @access protected
		 * @since 2.8.000 (2008-03-19)
		 */
		protected $linethrough;

		// --- Variables used for User's Rights ---
		// See PDF reference chapter 8.7 Digital Signatures

		/**
		 * If true enables user's rights on PDF reader
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur;

		/**
		 * Names specifying additional document-wide usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_document;

		/**
		 * Names specifying additional annotation-related usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_annots;

		/**
		 * Names specifying additional form-field-related usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_form;

		/**
		 * Names specifying additional signature-related usage rights for the document.
		 * @access protected
		 * @since 2.9.000 (2008-03-26)
		 */
		protected $ur_signature;

		/**
		 * Dot Per Inch Document Resolution (do not change)
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $dpi = 72;
		
		/**
		 * Array of page numbers were a new page group was started
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $newpagegroup = array();
		
		/**
		 * Contains the number of pages of the groups
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $pagegroups;
		
		/**
		 * Contains the alias of the current page group
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $currpagegroup; 
		
		/**
		 * Restrict the rendering of some elements to screen or printout.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $visibility = 'all';
		
		/**
		 * Print visibility.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $n_ocg_print;
		
		/**
		 * View visibility.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $n_ocg_view;
		
		/**
		 * Array of transparency objects and parameters.
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $extgstates;
		
		/**
		 * Set the default JPEG compression quality (1-100)
		 * @access protected
		 * @since 3.0.000 (2008-03-27)
		 */
		protected $jpeg_quality;
		
		/**
		 * Default cell height ratio.
		 * @access protected
		 * @since 3.0.014 (2008-05-23)
		 */
		protected $cell_height_ratio = K_CELL_HEIGHT_RATIO;
		
		/**
		 * PDF viewer preferences.
		 * @access protected
		 * @since 3.1.000 (2008-06-09)
		 */
		protected $viewer_preferences;
		
		/**
		 * A name object specifying how the document should be displayed when opened.
		 * @access protected
		 * @since 3.1.000 (2008-06-09)
		 */
		protected $PageMode;
		
		/**
		 * Array for storing gradient information.
		 * @access protected
		 * @since 3.1.000 (2008-06-09)
		 */
		protected $gradients = array();
		
		/**
		 * Array used to store positions inside the pages buffer.
		 * keys are the page numbers
		 * @access protected
		 * @since 3.2.000 (2008-06-26)
		 */
		protected $intmrk = array();
		
		/**
		 * Array used to store content positions inside the pages buffer.
		 * keys are the page numbers
		 * @access protected
		 * @since 4.6.021 (2009-07-20)
		 */
		protected $cntmrk = array();
		
		/**
		 * Array used to store footer positions of each page.
		 * @access protected
		 * @since 3.2.000 (2008-07-01)
		 */
		protected $footerpos = array();
		
		
		/**
		 * Array used to store footer lenght of each page.
		 * @access protected
		 * @since 4.0.014 (2008-07-29)
		 */
		protected $footerlen = array();
		
		/**
		 * True if a newline is created.
		 * @access protected
		 * @since 3.2.000 (2008-07-01)
		 */
		protected $newline = true;
		
		/**
		 * End position of the latest inserted line
		 * @access protected
		 * @since 3.2.000 (2008-07-01)
		 */
		protected $endlinex = 0;
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleWidth = '';
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleCap = '0 J';
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleJoin = '0 j';
		
		/**
		 * PDF string for last line width
		 * @access protected
		 * @since 4.0.006 (2008-07-16)
		 */
		protected $linestyleDash = '[] 0 d';
		
		/**
		 * True if marked-content sequence is open
		 * @access protected
		 * @since 4.0.013 (2008-07-28)
		 */
		protected $openMarkedContent = false;
		
		/**
		 * Count the latest inserted vertical spaces on HTML
		 * @access protected
		 * @since 4.0.021 (2008-08-24)
		 */
		protected $htmlvspace = 0;
		
		/**
		 * Array of Spot colors
		 * @access protected
		 * @since 4.0.024 (2008-09-12)
		 */
		protected $spot_colors = array();
		
		/**
		 * Symbol used for HTML unordered list items
		 * @access protected
		 * @since 4.0.028 (2008-09-26)
		 */
		protected $lisymbol = '';
		
		/**
		 * String used to mark the beginning and end of EPS image blocks
		 * @access protected
		 * @since 4.1.000 (2008-10-18)
		 */
		protected $epsmarker = 'x#!#EPS#!#x';
		
		/**
		 * Array of transformation matrix
		 * @access protected
		 * @since 4.2.000 (2008-10-29)
		 */
		protected $transfmatrix = array();

		/**
		 * Current key for transformation matrix
		 * @access protected
		 * @since 4.8.005 (2009-09-17)
		 */
		protected $transfmatrix_key = 0;

		/**
		 * Booklet mode for double-sided pages
		 * @access protected
		 * @since 4.2.000 (2008-10-29)
		 */
		protected $booklet = false;
		
		/**
		 * Epsilon value used for float calculations
		 * @access protected
		 * @since 4.2.000 (2008-10-29)
		 */
		protected $feps = 0.005;
		
		/**
		 * Array used for custom vertical spaces for HTML tags
		 * @access protected
		 * @since 4.2.001 (2008-10-30)
		 */
		protected $tagvspaces = array();
		
		/**
		 * @var HTML PARSER: custom indent amount for lists.
		 * Negative value means disabled.
		 * @access protected
		 * @since 4.2.007 (2008-11-12)
		 */
		protected $customlistindent = -1;
		
		/**
		 * @var if true keeps the border open for the cell sides that cross the page.
		 * @access protected
		 * @since 4.2.010 (2008-11-14)
		 */
		protected $opencell = true;

		/**
		 * @var array of files to embedd
		 * @access protected
		 * @since 4.4.000 (2008-12-07)
		 */
		protected $embeddedfiles = array();

		/**
		 * @var boolean true when inside html pre tag
		 * @access protected
		 * @since 4.4.001 (2008-12-08)
		 */
		protected $premode = false;

		/**
		 * Array used to store positions of graphics transformation blocks inside the page buffer.
		 * keys are the page numbers
		 * @access protected
		 * @since 4.4.002 (2008-12-09)
		 */
		protected $transfmrk = array();

		/**
		 * Default color for html links
		 * @access protected
		 * @since 4.4.003 (2008-12-09)
		 */
		protected $htmlLinkColorArray = array(0, 0, 255);

		/**
		 * Default font style to add to html links
		 * @access protected
		 * @since 4.4.003 (2008-12-09)
		 */
		protected $htmlLinkFontStyle = 'U';

		/**
		 * Counts the number of pages.
		 * @access protected
		 * @since 4.5.000 (2008-12-31)
		 */
		protected $numpages = 0;

		/**
		 * Array containing page lenghts in bytes.
		 * @access protected
		 * @since 4.5.000 (2008-12-31)
		 */
		protected $pagelen = array();

		/**
		 * Counts the number of pages.
		 * @access protected
		 * @since 4.5.000 (2008-12-31)
		 */
		protected $numimages = 0;

		/**
		 * Store the image keys.
		 * @access protected
		 * @since 4.5.000 (2008-12-31)
		 */
		protected $imagekeys = array();

		/**
		 * Lenght of the buffer in bytes.
		 * @access protected
		 * @since 4.5.000 (2008-12-31)
		 */
		protected $bufferlen = 0;

		/**
		 * If true enables disk caching.
		 * @access protected
		 * @since 4.5.000 (2008-12-31)
		 */
		protected $diskcache = false;

		/**
		 * Counts the number of fonts.
		 * @access protected
		 * @since 4.5.000 (2009-01-02)
		 */
		protected $numfonts = 0;

		/**
		 * Store the font keys.
		 * @access protected
		 * @since 4.5.000 (2009-01-02)
		 */
		protected $fontkeys = array();
		
		/**
		 * Store the font object IDs.
		 * @access protected
		 * @since 4.8.001 (2009-09-09)
		 */
		protected $font_obj_ids = array();

		/**
		 * Store the fage status (true when opened, false when closed).
		 * @access protected
		 * @since 4.5.000 (2009-01-02)
		 */
		protected $pageopen = array();
		
		/**
		 * Default monospaced font
		 * @access protected
		 * @since 4.5.025 (2009-03-10)
		 */
		protected $default_monospaced_font = 'courier';

		/**
		 * Used to store a cloned copy of the current class object
		 * @access protected
		 * @since 4.5.029 (2009-03-19)
		 */
		protected $objcopy;

		/**
		 * Array used to store the lenghts of cache files
		 * @access protected
		 * @since 4.5.029 (2009-03-19)
		 */
		protected $cache_file_lenght = array();

		/**
		 * Table header content to be repeated on each new page
		 * @access protected
		 * @since 4.5.030 (2009-03-20)
		 */
		protected $thead = '';

		/**
		 * Margins used for table header.
		 * @access protected
		 * @since 4.5.030 (2009-03-20)
		 */
		protected $theadMargins = array();

		/**
		 * Cache array for UTF8StringToArray() method.
		 * @access protected
		 * @since 4.5.037 (2009-04-07)
		 */
		protected $cache_UTF8StringToArray = array();

		/**
		 * Maximum size of cache array used for UTF8StringToArray() method.
		 * @access protected
		 * @since 4.5.037 (2009-04-07)
		 */
		protected $cache_maxsize_UTF8StringToArray = 8;

		/**
		 * Current size of cache array used for UTF8StringToArray() method.
		 * @access protected
		 * @since 4.5.037 (2009-04-07)
		 */
		protected $cache_size_UTF8StringToArray = 0;

		/**
		 * If true enables document signing
		 * @access protected
		 * @since 4.6.005 (2009-04-24)
		 */
		protected $sign = false;

		/**
		 * Signature data
		 * @access protected
		 * @since 4.6.005 (2009-04-24)
		 */
		protected $signature_data = array();

		/**
		 * Signature max lenght
		 * @access protected
		 * @since 4.6.005 (2009-04-24)
		 */
		protected $signature_max_lenght = 11742;

		/**
		 * Regular expression used to find blank characters used for word-wrapping.
		 * @access protected
		 * @since 4.6.006 (2009-04-28)
		 */
		protected $re_spaces = '/[\s]/';

		/**
		 * Signature object ID
		 * @access protected
		 * @since 4.6.022 (2009-06-23)
		 */
		protected $sig_obj_id = 0;

		/**
		 * ByteRange placemark used during signature process.
		 * @access protected
		 * @since 4.6.028 (2009-08-25)
		 */
		protected $byterange_string = '/ByteRange[0 ********** ********** **********]';

		/**
		 * Placemark used during signature process.
		 * @access protected
		 * @since 4.6.028 (2009-08-25)
		 */
		protected $sig_annot_ref = '***SIGANNREF*** 0 R';

		/**
		 * ID of page objects
		 * @access protected
		 * @since 4.7.000 (2009-08-29)
		 */
		protected $page_obj_id = array();

		/**
		 * Start ID for embedded file objects
		 * @access protected
		 * @since 4.7.000 (2009-08-29)
		 */
		protected $embedded_start_obj_id = 100000;

		/**
		 * Start ID for annotation objects
		 * @access protected
		 * @since 4.7.000 (2009-08-29)
		 */
		protected $annots_start_obj_id = 200000;
		
		/**
		 * Max ID of annotation object
		 * @access protected
		 * @since 4.7.000 (2009-08-29)
		 */
		protected $annot_obj_id = 200000;
		
		/**
		 * Current ID of annotation object
		 * @access protected
		 * @since 4.8.003 (2009-09-15)
		 */
		protected $curr_annot_obj_id = 200000;
		
		/**
		 * List of form annotations IDs
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $form_obj_id = array();
		
		/*
		 * Deafult Javascript field properties. Possible values are described on official Javascript for Acrobat API reference. Annotation options can be directly specified using the 'aopt' entry.
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $default_form_prop = array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 255), 'strokeColor'=>array(128, 128, 128));

		/**
		 * Javascript objects array
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $js_objects = array();

		/**
		 * Start ID for javascript objects
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $js_start_obj_id = 300000;
		
		/**
		 * Current ID of javascript object
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $js_obj_id = 300000;
		
		/**
		 * Current form action (used during XHTML rendering)
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $form_action = '';

		/**
		 * Current form encryption type (used during XHTML rendering)
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $form_enctype = 'application/x-www-form-urlencoded';

		/**
		 * Current method to submit forms.
		 * @access protected
		 * @since 4.8.000 (2009-09-07)
		 */
		protected $form_mode = 'post';

		/**
		 * Start ID for appearance streams XObjects
		 * @access protected
		 * @since 4.8.001 (2009-09-09)
		 */
		protected $apxo_start_obj_id = 400000;
		
		/**
		 * Current ID of appearance streams XObjects
		 * @access protected
		 * @since 4.8.001 (2009-09-09)
		 */
		protected $apxo_obj_id = 400000;
		
		/**
		 * List of fonts used on form fields (fontname => fontkey).
		 * @access protected
		 * @since 4.8.001 (2009-09-09)
		 */
		protected $annotation_fonts = array();
		
		/**
		 * List of radio buttons parent objects.
		 * @access protected
		 * @since 4.8.001 (2009-09-09)
		 */
		protected $radiobutton_groups = array();
		
		/**
		 * List of radio group objects IDs
		 * @access protected
		 * @since 4.8.001 (2009-09-09)
		 */
		protected $radio_groups = array();
		
		/**
		 * Text indentation value (used for text-indent CSS attribute) 
		 * @access protected
		 * @since 4.8.006 (2009-09-23)
		 */
		protected $textindent = 0;
		
		/**
		 * Store page number when startTransaction() is called.
		 * @access protected
		 * @since 4.8.006 (2009-09-23)
		 */
		protected $start_transaction_page = 0;
		
		//------------------------------------------------------------
		// METHODS
		//------------------------------------------------------------

		/**
		 * This is the class constructor. 
		 * It allows to set up the page format, the orientation and 
		 * the measure unit used in all the methods (except for the font sizes).
		 * @since 1.0
		 * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li></ul>
		 * @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
		 * @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		 * @param boolean $unicode TRUE means that the input text is unicode (default = true)
		 * @param boolean $diskcache if TRUE reduce the RAM memory usage by caching temporary data on filesystem (slower).
		 * @param String $encoding charset encoding; default is UTF-8
		 * @access public
		 */
		public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false) {
			/* Set internal character encoding to ASCII */
			if (function_exists('mb_internal_encoding') AND mb_internal_encoding()) {
				$this->internal_encoding = mb_internal_encoding();
				mb_internal_encoding('ASCII');
			}
			// set disk caching
			$this->diskcache = $diskcache ? true : false;
			// set language direction
			$this->rtl = false;
			$this->tmprtl = false;
			//Some checks
			$this->_dochecks();
			//Initialization of properties
			$this->isunicode = $unicode;
			$this->page = 0;
			$this->transfmrk[0] = array();
			$this->pagedim = array();
			$this->n = 2;
			$this->buffer = '';
			$this->pages = array();
			$this->state = 0;
			$this->fonts = array();
			$this->FontFiles = array();
			$this->diffs = array();
			$this->images = array();
			$this->links = array();
			$this->gradients = array();
			$this->InFooter = false;
			$this->lasth = 0;
			$this->FontFamily = 'helvetica';
			$this->FontStyle = '';
			$this->FontSizePt = 12;
			$this->underline = false;
			$this->linethrough = false;
			$this->DrawColor = '0 G';
			$this->FillColor = '0 g';
			$this->TextColor = '0 g';
			$this->ColorFlag = false;
			// encryption values
			$this->encrypted = false;
			$this->last_rc4_key = '';
			$this->padding = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
			//Standard Unicode fonts
			$this->CoreFonts = array(
				'courier'=>'Courier',
				'courierB'=>'Courier-Bold',
				'courierI'=>'Courier-Oblique',
				'courierBI'=>'Courier-BoldOblique',
				'helvetica'=>'Helvetica',
				'helveticaB'=>'Helvetica-Bold',
				'helveticaI'=>'Helvetica-Oblique',
				'helveticaBI'=>'Helvetica-BoldOblique',
				'times'=>'Times-Roman',
				'timesB'=>'Times-Bold',
				'timesI'=>'Times-Italic',
				'timesBI'=>'Times-BoldItalic',
				'symbol'=>'Symbol',
				'zapfdingbats'=>'ZapfDingbats'
			);
			//Set scale factor
			$this->setPageUnit($unit);
			// set page format and orientation
			$this->setPageFormat($format, $orientation);
			//Page margins (1 cm)
			$margin = 28.35 / $this->k;
			$this->SetMargins($margin, $margin);
			//Interior cell margin
			$this->cMargin = $margin / 10;
			//Line width (0.2 mm)
			$this->LineWidth = 0.57 / $this->k;
			$this->linestyleWidth = sprintf('%.2F w', ($this->LineWidth * $this->k));
			$this->linestyleCap = '0 J';
			$this->linestyleJoin = '0 j';
			$this->linestyleDash = '[] 0 d';
			//Automatic page break
			$this->SetAutoPageBreak(true, (2 * $margin));
			//Full width display mode
			$this->SetDisplayMode('fullwidth');
			//Compression
			$this->SetCompression(true);
			//Set default PDF version number
			$this->PDFVersion = '1.7';
			$this->encoding = $encoding;
			$this->HREF = array();
			$this->getFontsList();
			$this->fgcolor = array('R' => 0, 'G' => 0, 'B' => 0);
			$this->bgcolor = array('R' => 255, 'G' => 255, 'B' => 255);
			$this->extgstates = array();
			// user's rights
			$this->sign = false;
			$this->ur = false;
			$this->ur_document = '/FullSave';
			$this->ur_annots = '/Create/Delete/Modify/Copy/Import/Export';
			$this->ur_form = '/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate';
			$this->ur_signature = '/Modify';			
			// set default JPEG quality
			$this->jpeg_quality = 75;
			// initialize some settings
			$this->utf8Bidi(array(''), '');
			// set default font
			$this->SetFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
			// check if PCRE Unicode support is enabled
			if ($this->isunicode AND (@preg_match('/\pL/u', 'a') == 1)) {
				// PCRE unicode support is turned ON
				// \p{Z} or \p{Separator}: any kind of Unicode whitespace or invisible separator.
				// \p{Lo} or \p{Other_Letter}: a Unicode letter or ideograph that does not have lowercase and uppercase variants.
				// \p{Lo} is needed because Chinese characters are packed next to each other without spaces in between.
				//$this->re_spaces = '/[\s\p{Z}\p{Lo}]/u';
				$this->re_spaces = '/[\s\p{Z}]/u';
			} else {
				// PCRE unicode support is turned OFF
				$this->re_spaces = '/[\s]/';
			}
			$this->annot_obj_id = $this->annots_start_obj_id;
			$this->curr_annot_obj_id = $this->annots_start_obj_id;
			$this->apxo_obj_id = $this->apxo_start_obj_id;
			$this->js_obj_id = $this->js_start_obj_id;
			$this->default_form_prop = array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 255), 'strokeColor'=>array(128, 128, 128));
		}
		
		/**
		 * Default destructor.
		 * @access public
		 * @since 1.53.0.TC016
		 */
		public function __destruct() {
			// restore internal encoding
			if (isset($this->internal_encoding) AND !empty($this->internal_encoding)) {
				mb_internal_encoding($this->internal_encoding);
			}
			// unset all class variables
			$this->_destroy(true);
		}
		
		/**
		 * Set the units of measure for the document.
		 * @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
		 * @access public
		 * @since 3.0.015 (2008-06-06)
		 */
		public function setPageUnit($unit) {
		//Set scale factor
			switch (strtolower($unit)) {
				// points
				case 'px':
				case 'pt': {
					$this->k = 1;
					break;
				}
				// millimeters
				case 'mm': {
					$this->k = $this->dpi / 25.4;
					break;
				}
				// centimeters
				case 'cm': {
					$this->k = $this->dpi / 2.54;
					break;
				}
				// inches
				case 'in': {
					$this->k = $this->dpi;
					break;
				}
				// unsupported unit
				default : {
					$this->Error('Incorrect unit: '.$unit);
					break;
				}
			}
			if (isset($this->CurOrientation)) {
					$this->setPageOrientation($this->CurOrientation);
			}
		}
		
		/**
		* Set the page format
		* @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @access public
		* @since 3.0.015 (2008-06-06)
		*/
		public function setPageFormat($format, $orientation='P') {
			//Page format
			if (is_string($format)) {
				// Page formats (45 standard ISO paper formats and 4 american common formats).
				// Paper cordinates are calculated in this way: (inches * 72) where (1 inch = 2.54 cm)
				switch (strtoupper($format)) {
					case '4A0': {$format = array(4767.87,6740.79); break;}
					case '2A0': {$format = array(3370.39,4767.87); break;}
					case 'A0': {$format = array(2383.94,3370.39); break;}
					case 'A1': {$format = array(1683.78,2383.94); break;}
					case 'A2': {$format = array(1190.55,1683.78); break;}
					case 'A3': {$format = array(841.89,1190.55); break;}
					case 'A4': default: {$format = array(595.28,841.89); break;}
					case 'A5': {$format = array(419.53,595.28); break;}
					case 'A6': {$format = array(297.64,419.53); break;}
					case 'A7': {$format = array(209.76,297.64); break;}
					case 'A8': {$format = array(147.40,209.76); break;}
					case 'A9': {$format = array(104.88,147.40); break;}
					case 'A10': {$format = array(73.70,104.88); break;}
					case 'B0': {$format = array(2834.65,4008.19); break;}
					case 'B1': {$format = array(2004.09,2834.65); break;}
					case 'B2': {$format = array(1417.32,2004.09); break;}
					case 'B3': {$format = array(1000.63,1417.32); break;}
					case 'B4': {$format = array(708.66,1000.63); break;}
					case 'B5': {$format = array(498.90,708.66); break;}
					case 'B6': {$format = array(354.33,498.90); break;}
					case 'B7': {$format = array(249.45,354.33); break;}
					case 'B8': {$format = array(175.75,249.45); break;}
					case 'B9': {$format = array(124.72,175.75); break;}
					case 'B10': {$format = array(87.87,124.72); break;}
					case 'C0': {$format = array(2599.37,3676.54); break;}
					case 'C1': {$format = array(1836.85,2599.37); break;}
					case 'C2': {$format = array(1298.27,1836.85); break;}
					case 'C3': {$format = array(918.43,1298.27); break;}
					case 'C4': {$format = array(649.13,918.43); break;}
					case 'C5': {$format = array(459.21,649.13); break;}
					case 'C6': {$format = array(323.15,459.21); break;}
					case 'C7': {$format = array(229.61,323.15); break;}
					case 'C8': {$format = array(161.57,229.61); break;}
					case 'C9': {$format = array(113.39,161.57); break;}
					case 'C10': {$format = array(79.37,113.39); break;}
					case 'RA0': {$format = array(2437.80,3458.27); break;}
					case 'RA1': {$format = array(1729.13,2437.80); break;}
					case 'RA2': {$format = array(1218.90,1729.13); break;}
					case 'RA3': {$format = array(864.57,1218.90); break;}
					case 'RA4': {$format = array(609.45,864.57); break;}
					case 'SRA0': {$format = array(2551.18,3628.35); break;}
					case 'SRA1': {$format = array(1814.17,2551.18); break;}
					case 'SRA2': {$format = array(1275.59,1814.17); break;}
					case 'SRA3': {$format = array(907.09,1275.59); break;}
					case 'SRA4': {$format = array(637.80,907.09); break;}
					case 'LETTER': {$format = array(612.00,792.00); break;}
					case 'LEGAL': {$format = array(612.00,1008.00); break;}
					case 'EXECUTIVE': {$format = array(521.86,756.00); break;}
					case 'FOLIO': {$format = array(612.00,936.00); break;}
				}
				$this->fwPt = $format[0];
				$this->fhPt = $format[1];
			} else {
				$this->fwPt = $format[0] * $this->k;
				$this->fhPt = $format[1] * $this->k;
			}
			$this->setPageOrientation($orientation);
		}
		
		/**
		* Set page orientation.
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @param boolean $autopagebreak Boolean indicating if auto-page-break mode should be on or off.
		* @param float $bottommargin bottom margin of the page.
		* @access public
		* @since 3.0.015 (2008-06-06)
		*/
		public function setPageOrientation($orientation, $autopagebreak='', $bottommargin='') {
			$orientation = strtoupper($orientation);
			if (($orientation == 'P') OR ($orientation == 'PORTRAIT')) {
				$this->CurOrientation = 'P';
				$this->wPt = $this->fwPt;
				$this->hPt = $this->fhPt;
			} elseif (($orientation == 'L') OR ($orientation == 'LANDSCAPE')) {
				$this->CurOrientation = 'L';
				$this->wPt = $this->fhPt;
				$this->hPt = $this->fwPt;
			} else {
				$this->Error('Incorrect orientation: '.$orientation);
			}
			$this->w = $this->wPt / $this->k;
			$this->h = $this->hPt / $this->k;
			if ($this->empty_string($autopagebreak)) {
				if (isset($this->AutoPageBreak)) {
					$autopagebreak = $this->AutoPageBreak;
				} else {
					$autopagebreak = true;
				}
			}
			if ($this->empty_string($bottommargin)) {
				if (isset($this->bMargin)) {
					$bottommargin = $this->bMargin;
				} else {
					// default value = 2 cm
					$bottommargin = 2 * 28.35 / $this->k;
				}
			}
			$this->SetAutoPageBreak($autopagebreak, $bottommargin);
			// store page dimensions
			$this->pagedim[$this->page] = array('w' => $this->wPt, 'h' => $this->hPt, 'wk' => $this->w, 'hk' => $this->h, 'tm' => $this->tMargin, 'bm' => $bottommargin, 'lm' => $this->lMargin, 'rm' => $this->rMargin, 'pb' => $autopagebreak, 'or' => $this->CurOrientation, 'olm' => $this->original_lMargin, 'orm' => $this->original_rMargin);
		}
		
		/**
		 * Set regular expression to detect withespaces or word separators.
		 * @param string $re regular expression (leave empty for default).
		 * @access public
		 * @since 4.6.016 (2009-06-15)
		 */
		public function setSpacesRE($re='/[\s]/') {
			// if PCRE unicode support is turned ON:
			// 	\p{Z} or \p{Separator}: any kind of Unicode whitespace or invisible separator.
			// 	\p{Lo} or \p{Other_Letter}: a Unicode letter or ideograph that does not have lowercase and uppercase variants.
			// 	\p{Lo} is needed because Chinese characters are packed next to each other without spaces in between.
			$this->re_spaces = $re;
		}
			
		/**
		 * Enable or disable Right-To-Left language mode
		 * @param Boolean $enable if true enable Right-To-Left language mode.
		 * @param Boolean $resetx if true reset the X position on direction change.
		 * @access public
		* @since 2.0.000 (2008-01-03)
		 */
		public function setRTL($enable, $resetx=true) {
			$enable = $enable ? true : false;
			$resetx = ($resetx AND ($enable != $this->rtl));
			$this->rtl = $enable;
			$this->tmprtl = false;
			if ($resetx) {
				$this->Ln(0);
			}
		}
		
		/**
		 * Return the RTL status
		 * @return boolean
		 * @access public
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getRTL() {
			return $this->rtl;
		}
		
		/**
		* Force temporary RTL language direction
		* @param mixed $mode can be false, 'L' for LTR or 'R' for RTL
		* @access public
		* @since 2.1.000 (2008-01-09)
		*/
		public function setTempRTL($mode) {
			switch ($mode) {
				case false:
				case 'L':
				case 'R': {
					$this->tmprtl = $mode;
				}
			}
		}
		
		/**
		* Set the last cell height.
		* @param float $h cell height.
		* @author Nicola Asuni
		* @access public
		* @since 1.53.0.TC034
		*/
		public function setLastH($h) {
			$this->lasth = $h;
		}
		
		/**
		* Get the last cell height.
		* @return last cell height
		* @access public
		* @since 4.0.017 (2008-08-05)
		*/
		public function getLastH() {
			return $this->lasth;
		}
		
		/**
		* Set the adjusting factor to convert pixels to user units.
		* @param float $scale adjusting factor to convert pixels to user units.
		* @author Nicola Asuni
		* @access public
		* @since 1.5.2
		*/
		public function setImageScale($scale) {
			$this->imgscale = $scale;
		}

		/**
		* Returns the adjusting factor to convert pixels to user units.
		* @return float adjusting factor to convert pixels to user units.
		* @author Nicola Asuni
		* @access public
		* @since 1.5.2
		*/
		public function getImageScale() {
			return $this->imgscale;
		}
				
		/**
		* Returns an array of page dimensions:
		* <ul><li>$this->pagedim[$this->page]['w'] => page_width_in_points</li><li>$this->pagedim[$this->page]['h'] => height in points</li><li>$this->pagedim[$this->page]['wk'] => page_width_in_points</li><li>$this->pagedim[$this->page]['hk'] => height</li><li>$this->pagedim[$this->page]['tm'] => top_margin</li><li>$this->pagedim[$this->page]['bm'] => bottom_margin</li><li>$this->pagedim[$this->page]['lm'] => left_margin</li><li>$this->pagedim[$this->page]['rm'] => right_margin</li><li>$this->pagedim[$this->page]['pb'] => auto_page_break</li><li>$this->pagedim[$this->page]['or'] => page_orientation</li><li>$this->pagedim[$this->page]['olm'] => original_left_margin</li><li>$this->pagedim[$this->page]['orm'] => original_right_margin</li></ul>
		* @param int $pagenum page number (empty = current page)
		* @return array of page dimensions.
		* @author Nicola Asuni
		* @access public
		* @since 4.5.027 (2009-03-16)
		*/
		public function getPageDimensions($pagenum='') {
			if (empty($pagenum)) {
				$pagenum = $this->page;
			}
			return $this->pagedim[$pagenum];
		}
		
		/**
		* Returns the page width in units.
		* @param int $pagenum page number (empty = current page)
		* @return int page width.
		* @author Nicola Asuni
		* @access public
		* @since 1.5.2
		* @see getPageDimensions()
		*/
		public function getPageWidth($pagenum='') {
			if (empty($pagenum)) {
				return $this->w;
			}
			return $this->pagedim[$pagenum]['w'];
		}

		/**
		* Returns the page height in units.
		* @param int $pagenum page number (empty = current page)
		* @return int page height.
		* @author Nicola Asuni
		* @access public
		* @since 1.5.2
		* @see getPageDimensions()
		*/
		public function getPageHeight($pagenum='') {
			if (empty($pagenum)) {
				return $this->h;
			}
			return $this->pagedim[$pagenum]['h'];
		}

		/**
		* Returns the page break margin.
		* @param int $pagenum page number (empty = current page)
		* @return int page break margin.
		* @author Nicola Asuni
		* @access public
		* @since 1.5.2
		* @see getPageDimensions()
		*/
		public function getBreakMargin($pagenum='') {
			if (empty($pagenum)) {
				return $this->bMargin;
			}
			return $this->pagedim[$pagenum]['bm'];
		}

		/**
		* Returns the scale factor (number of points in user unit).
		* @return int scale factor.
		* @author Nicola Asuni
		* @access public
		* @since 1.5.2
		*/
		public function getScaleFactor() {
			return $this->k;
		}

		/**
		* Defines the left, top and right margins. By default, they equal 1 cm. Call this method to change them.
		* @param float $left Left margin.
		* @param float $top Top margin.
		* @param float $right Right margin. Default value is the left one.
		* @access public
		* @since 1.0
		* @see SetLeftMargin(), SetTopMargin(), SetRightMargin(), SetAutoPageBreak()
		*/
		public function SetMargins($left, $top, $right=-1) {
			//Set left, top and right margins
			$this->lMargin = $left;
			$this->tMargin = $top;
			if ($right == -1) {
				$right = $left;
			}
			$this->rMargin = $right;
		}

		/**
		* Defines the left margin. The method can be called before creating the first page. If the current abscissa gets out of page, it is brought back to the margin.
		* @param float $margin The margin.
		* @access public
		* @since 1.4
		* @see SetTopMargin(), SetRightMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetLeftMargin($margin) {
			//Set left margin
			$this->lMargin=$margin;
			if (($this->page > 0) AND ($this->x < $margin)) {
				$this->x = $margin;
			}
		}

		/**
		* Defines the top margin. The method can be called before creating the first page.
		* @param float $margin The margin.
		* @access public
		* @since 1.5
		* @see SetLeftMargin(), SetRightMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetTopMargin($margin) {
			//Set top margin
			$this->tMargin=$margin;
			if (($this->page > 0) AND ($this->y < $margin)) {
				$this->y = $margin;
			}
		}

		/**
		* Defines the right margin. The method can be called before creating the first page.
		* @param float $margin The margin.
		* @access public
		* @since 1.5
		* @see SetLeftMargin(), SetTopMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetRightMargin($margin) {
			$this->rMargin=$margin;
			if (($this->page > 0) AND ($this->x > ($this->w - $margin))) {
				$this->x = $this->w - $margin;
			}
		}

		/**
		* Set the internal Cell padding.
		* @param float $pad internal padding.
		* @access public
		* @since 2.1.000 (2008-01-09)
		* @see Cell(), SetLeftMargin(), SetTopMargin(), SetAutoPageBreak(), SetMargins()
		*/
		public function SetCellPadding($pad) {
			$this->cMargin = $pad;
		}

		/**
		* Enables or disables the automatic page breaking mode. When enabling, the second parameter is the distance from the bottom of the page that defines the triggering limit. By default, the mode is on and the margin is 2 cm.
		* @param boolean $auto Boolean indicating if mode should be on or off.
		* @param float $margin Distance from the bottom of the page.
		* @access public
		* @since 1.0
		* @see Cell(), MultiCell(), AcceptPageBreak()
		*/
		public function SetAutoPageBreak($auto, $margin=0) {
			//Set auto page break mode and triggering margin
			$this->AutoPageBreak = $auto;
			$this->bMargin = $margin;
			$this->PageBreakTrigger = $this->h - $margin;
		}

		/**
		* Defines the way the document is to be displayed by the viewer.
		* @param mixed $zoom The zoom to use. It can be one of the following string values or a number indicating the zooming factor to use. <ul><li>fullpage: displays the entire page on screen </li><li>fullwidth: uses maximum width of window</li><li>real: uses real size (equivalent to 100% zoom)</li><li>default: uses viewer default mode</li></ul>
		* @param string $layout The page layout. Possible values are:<ul><li>SinglePage Display one page at a time</li><li>OneColumn Display the pages in one column</li><li>TwoColumnLeft Display the pages in two columns, with odd-numbered pages on the left</li><li>TwoColumnRight Display the pages in two columns, with odd-numbered pages on the right</li><li>TwoPageLeft (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the left</li><li>TwoPageRight (PDF 1.5) Display the pages two at a time, with odd-numbered pages on the right</li></ul>
		* @param string $mode A name object specifying how the document should be displayed when opened:<ul><li>UseNone Neither document outline nor thumbnail images visible</li><li>UseOutlines Document outline visible</li><li>UseThumbs Thumbnail images visible</li><li>FullScreen Full-screen mode, with no menu bar, window controls, or any other window visible</li><li>UseOC (PDF 1.5) Optional content group panel visible</li><li>UseAttachments (PDF 1.6) Attachments panel visible</li></ul>
		* @access public
		* @since 1.2
		*/
		public function SetDisplayMode($zoom, $layout='SinglePage', $mode='UseNone') {
			//Set display mode in viewer
			if (($zoom == 'fullpage') OR ($zoom == 'fullwidth') OR ($zoom == 'real') OR ($zoom == 'default') OR (!is_string($zoom))) {
				$this->ZoomMode = $zoom;
			} else {
				$this->Error('Incorrect zoom display mode: '.$zoom);
			}
			switch ($layout) {
				case 'default':
				case 'single':
				case 'SinglePage': {
					$this->LayoutMode = 'SinglePage';
					break;
				}
				case 'continuous':
				case 'OneColumn': {
					$this->LayoutMode = 'OneColumn';
					break;
				}
				case 'two':
				case 'TwoColumnLeft': {
					$this->LayoutMode = 'TwoColumnLeft';
					break;
				}
				case 'TwoColumnRight': {
					$this->LayoutMode = 'TwoColumnRight';
					break;
				}
				case 'TwoPageLeft': {
					$this->LayoutMode = 'TwoPageLeft';
					break;
				}
				case 'TwoPageRight': {
					$this->LayoutMode = 'TwoPageRight';
					break;
				}
				default: {
					$this->LayoutMode = 'SinglePage';
				}
			}
			switch ($mode) {
				case 'UseNone': {
					$this->PageMode = 'UseNone';
					break;
				}
				case 'UseOutlines': {
					$this->PageMode = 'UseOutlines';
					break;
				}
				case 'UseThumbs': {
					$this->PageMode = 'UseThumbs';
					break;
				}
				case 'FullScreen': {
					$this->PageMode = 'FullScreen';
					break;
				}
				case 'UseOC': {
					$this->PageMode = 'UseOC';
					break;
				}
				case '': {
					$this->PageMode = 'UseAttachments';
					break;
				}
				default: {
					$this->PageMode = 'UseNone';
				}
			}
		}

		/**
		* Activates or deactivates page compression. When activated, the internal representation of each page is compressed, which leads to a compression ratio of about 2 for the resulting document. Compression is on by default.
		* Note: the Zlib extension is required for this feature. If not present, compression will be turned off.
		* @param boolean $compress Boolean indicating if compression must be enabled.
		* @access public
		* @since 1.4
		*/
		public function SetCompression($compress) {
			//Set page compression
			if (function_exists('gzcompress')) {
				$this->compress = $compress;
			} else {
				$this->compress = false;
			}
		}

		/**
		* Defines the title of the document.
		* @param string $title The title.
		* @access public
		* @since 1.2
		* @see SetAuthor(), SetCreator(), SetKeywords(), SetSubject()
		*/
		public function SetTitle($title) {
			//Title of document
			$this->title = $title;
		}

		/**
		* Defines the subject of the document.
		* @param string $subject The subject.
		* @access public
		* @since 1.2
		* @see SetAuthor(), SetCreator(), SetKeywords(), SetTitle()
		*/
		public function SetSubject($subject) {
			//Subject of document
			$this->subject = $subject;
		}

		/**
		* Defines the author of the document.
		* @param string $author The name of the author.
		* @access public
		* @since 1.2
		* @see SetCreator(), SetKeywords(), SetSubject(), SetTitle()
		*/
		public function SetAuthor($author) {
			//Author of document
			$this->author = $author;
		}

		/**
		* Associates keywords with the document, generally in the form 'keyword1 keyword2 ...'.
		* @param string $keywords The list of keywords.
		* @access public
		* @since 1.2
		* @see SetAuthor(), SetCreator(), SetSubject(), SetTitle()
		*/
		public function SetKeywords($keywords) {
			//Keywords of document
			$this->keywords = $keywords;
		}

		/**
		* Defines the creator of the document. This is typically the name of the application that generates the PDF.
		* @param string $creator The name of the creator.
		* @access public
		* @since 1.2
		* @see SetAuthor(), SetKeywords(), SetSubject(), SetTitle()
		*/
		public function SetCreator($creator) {
			//Creator of document
			$this->creator = $creator;
		}
		
		/**
		* This method is automatically called in case of fatal error; it simply outputs the message and halts the execution. An inherited class may override it to customize the error handling but should always halt the script, or the resulting document would probably be invalid.
		* 2004-06-11 :: Nicola Asuni : changed bold tag with strong
		* @param string $msg The error message
		* @access public
		* @since 1.0
		*/
		public function Error($msg) {
			// unset all class variables
			$this->_destroy(true);
			// exit program and print error
			die('<strong>TCPDF ERROR: </strong>'.$msg);
		}

		/**
		* This method begins the generation of the PDF document.
		* It is not necessary to call it explicitly because AddPage() does it automatically.
		* Note: no page is created by this method
		* @access public
		* @since 1.0
		* @see AddPage(), Close()
		*/
		public function Open() {
			//Begin document
			$this->state = 1;
		}

		/**
		* Terminates the PDF document.
		* It is not necessary to call this method explicitly because Output() does it automatically.
		* If the document contains no page, AddPage() is called to prevent from getting an invalid document.
		* @access public
		* @since 1.0
		* @see Open(), Output()
		*/
		public function Close() {
			if ($this->state == 3) {
				return;
			}
			if ($this->page == 0) {
				$this->AddPage();
			}
			// close page
			$this->endPage();
			// close document
			$this->_enddoc();
			// unset all class variables (except critical ones)
			$this->_destroy(false);
		}
		
		/**
		* Move pointer at the specified document page and update page dimensions.
		* @param int $pnum page number
		* @param boolean $resetmargins if true reset left, right, top margins and Y position.
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see getPage(), lastpage(), getNumPages()
		*/
		public function setPage($pnum, $resetmargins=false) {
			if ($pnum == $this->page) {
				return;
			}
			if (($pnum > 0) AND ($pnum <= $this->numpages)) {
				$this->state = 2;
				// save current graphic settings
				//$gvars = $this->getGraphicVars();
				$oldpage = $this->page;
				$this->page = $pnum;
				$this->wPt = $this->pagedim[$this->page]['w'];
				$this->hPt = $this->pagedim[$this->page]['h'];
				$this->w = $this->wPt / $this->k;
				$this->h = $this->hPt / $this->k;
				$this->tMargin = $this->pagedim[$this->page]['tm'];
				$this->bMargin = $this->pagedim[$this->page]['bm'];
				$this->original_lMargin = $this->pagedim[$this->page]['olm'];
				$this->original_rMargin = $this->pagedim[$this->page]['orm'];
				$this->AutoPageBreak = $this->pagedim[$this->page]['pb'];
				$this->CurOrientation = $this->pagedim[$this->page]['or'];
				$this->SetAutoPageBreak($this->AutoPageBreak, $this->bMargin);
				// restore graphic settings
				//$this->setGraphicVars($gvars);
				if ($resetmargins) {
					$this->lMargin = $this->pagedim[$this->page]['olm'];
					$this->rMargin = $this->pagedim[$this->page]['orm'];
					$this->SetY($this->tMargin);
				} else {
					// account for booklet mode
					if ($this->pagedim[$this->page]['olm'] != $this->pagedim[$oldpage]['olm']) {
						$deltam = $this->pagedim[$this->page]['olm'] - $this->pagedim[$this->page]['orm'];
						$this->lMargin += $deltam;
						$this->rMargin -= $deltam;
					}
				}
			} else {
				$this->Error('Wrong page number on setPage() function.');
			}
		}
		
		/**
		* Reset pointer to the last document page.
		* @param boolean $resetmargins if true reset left, right, top margins and Y position.
		* @access public
		* @since 2.0.000 (2008-01-04)
		* @see setPage(), getPage(), getNumPages()
		*/
		public function lastPage($resetmargins=false) {
			$this->setPage($this->getNumPages(), $resetmargins);
		}
		
		/**
		* Get current document page number.
		* @return int page number
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see setPage(), lastpage(), getNumPages()
		*/
		public function getPage() {
			return $this->page;
		}
		
		
		/**
		* Get the total number of insered pages.
		* @return int number of pages
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see setPage(), getPage(), lastpage()
		*/
		public function getNumPages() {
			return $this->numpages;
		}

		/**
		* Adds a new page to the document. If a page is already present, the Footer() method is called first to output the footer (if enabled). Then the page is added, the current position set to the top-left corner according to the left and top margins (or top-right if in RTL mode), and Header() is called to display the header (if enabled).
		* The origin of the coordinate system is at the top-left corner (or top-right for RTL) and increasing ordinates go downwards.
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		* @access public
		* @since 1.0
		* @see startPage(), endPage()
		*/
		public function AddPage($orientation='', $format='') {
			if (!isset($this->original_lMargin)) {
				$this->original_lMargin = $this->lMargin;
			}
			if (!isset($this->original_rMargin)) {
				$this->original_rMargin = $this->rMargin;
			}
			// terminate previous page
			$this->endPage();
			// start new page
			$this->startPage($orientation, $format);
		}

		/**
		* Terminate the current page
		* @access protected
		* @since 4.2.010 (2008-11-14)
		* @see startPage(), AddPage()
		*/
		protected function endPage() {
			// check if page is already closed
			if (($this->page == 0) OR ($this->numpages > $this->page) OR (!$this->pageopen[$this->page])) {
				return;
			}
			$this->InFooter = true;
			// print page footer
			$this->setFooter();
			// close page
			$this->_endpage();
			// mark page as closed
			$this->pageopen[$this->page] = false;
			$this->InFooter = false;
		}

		/**
		* Starts a new page to the document. The page must be closed using the endPage() function.
		* The origin of the coordinate system is at the top-left corner and increasing ordinates go downwards.
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		* @access protected
		* @since 4.2.010 (2008-11-14)
		* @see endPage(), AddPage()
		*/
		protected function startPage($orientation='', $format='') {
			if ($this->numpages > $this->page) {
				// this page has been already added
				$this->setPage($this->page + 1);
				$this->SetY($this->tMargin);
				return;
			}
			// start a new page
			if ($this->state == 0) {
				$this->Open();
			}
			++$this->numpages;
			$this->swapMargins($this->booklet);
			// save current graphic settings
			$gvars = $this->getGraphicVars();
			// start new page
			$this->_beginpage($orientation, $format);
			// mark page as open
			$this->pageopen[$this->page] = true;
			// restore graphic settings
			$this->setGraphicVars($gvars);
			// mark this point
			$this->setPageMark();
			// print page header
			$this->setHeader();
			// restore graphic settings
			$this->setGraphicVars($gvars);
			// mark this point
			$this->setPageMark();
			// print table header (if any)
			$this->setTableHeader();
		}
			
		/**
	 	 * Set start-writing mark on current page for multicell borders and fills.
	 	 * This function must be called after calling Image() function for a background image.
	 	 * Background images must be always inserted before calling Multicell() or WriteHTMLCell() or WriteHTML() functions.
	 	 * @access public
	 	 * @since 4.0.016 (2008-07-30)
		 */
		public function setPageMark() {
			$this->intmrk[$this->page] = $this->pagelen[$this->page];
			$this->setContentMark();
		}
		
		/**
	 	 * Set start-writing mark on selected page.
	 	 * @param int $page page number (default is the current page)
	 	 * @access protected
	 	 * @since 4.6.021 (2009-07-20)
		 */
		protected function setContentMark($page=0) {
			if ($page <= 0) {
				$page = $this->page;
			}
			if (isset($this->footerlen[$page])) {
				$this->cntmrk[$page] = $this->pagelen[$page] - $this->footerlen[$page];
			} else {
				$this->cntmrk[$page] = $this->pagelen[$page];
			}
		}
		
		/**
	 	 * Set header data.
		 * @param string $ln header image logo
		 * @param string $lw header image logo width in mm
		 * @param string $ht string to print as title on document header
		 * @param string $hs string to print on document header
		 * @access public
		 */
		public function setHeaderData($ln='', $lw=0, $ht='', $hs='') {
			$this->header_logo = $ln;
			$this->header_logo_width = $lw;
			$this->header_title = $ht;
			$this->header_string = $hs;
		}
		
		/**
	 	 * Returns header data:
	 	 * <ul><li>$ret['logo'] = logo image</li><li>$ret['logo_width'] = width of the image logo in user units</li><li>$ret['title'] = header title</li><li>$ret['string'] = header description string</li></ul>
		 * @return array()
		 * @access public
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getHeaderData() {
			$ret = array();
			$ret['logo'] = $this->header_logo;
			$ret['logo_width'] = $this->header_logo_width;
			$ret['title'] = $this->header_title;
			$ret['string'] = $this->header_string;
			return $ret;
		}
		
		/**
	 	 * Set header margin.
		 * (minimum distance between header and top page margin)
		 * @param int $hm distance in user units
		 * @access public
		 */
		public function setHeaderMargin($hm=10) {
			$this->header_margin = $hm;
		}
		
		/**
	 	 * Returns header margin in user units.
		 * @return float
		 * @since 4.0.012 (2008-07-24)
		 * @access public
		 */
		public function getHeaderMargin() {
			return $this->header_margin;
		}
		
		/**
	 	 * Set footer margin.
		 * (minimum distance between footer and bottom page margin)
		 * @param int $fm distance in user units
		 * @access public
		 */
		public function setFooterMargin($fm=10) {
			$this->footer_margin = $fm;
		}
		
		/**
	 	 * Returns footer margin in user units.
		 * @return float
		 * @since 4.0.012 (2008-07-24)
		 * @access public
		 */
		public function getFooterMargin() {
			return $this->footer_margin;
		}
		/**
	 	 * Set a flag to print page header.
		 * @param boolean $val set to true to print the page header (default), false otherwise. 
		 * @access public
		 */
		public function setPrintHeader($val=true) {
			$this->print_header = $val;
		}
		
		/**
	 	 * Set a flag to print page footer.
		 * @param boolean $value set to true to print the page footer (default), false otherwise. 
		 * @access public
		 */
		public function setPrintFooter($val=true) {
			$this->print_footer = $val;
		}
		
		/**
	 	 * Return the right-bottom (or left-bottom for RTL) corner X coordinate of last inserted image
		 * @return float 
		 * @access public
		 */
		public function getImageRBX() {
			return $this->img_rb_x;
		}
		
		/**
	 	 * Return the right-bottom (or left-bottom for RTL) corner Y coordinate of last inserted image
		 * @return float 
		 * @access public
		 */
		public function getImageRBY() {
			return $this->img_rb_y;
		}
		
		/**
	 	 * This method is used to render the page header.
	 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
		 * @access public
		 */
		public function Header() {
			$ormargins = $this->getOriginalMargins();
			$headerfont = $this->getHeaderFont();
			$headerdata = $this->getHeaderData();
			if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
				$this->Image(K_PATH_IMAGES.$headerdata['logo'], $this->GetX(), $this->getHeaderMargin(), $headerdata['logo_width']);
				$imgy = $this->getImageRBY();
			} else {
				$imgy = $this->GetY();
			}
			$cell_height = round(($this->getCellHeightRatio() * $headerfont[2]) / $this->getScaleFactor(), 2);
			// set starting margin for text data cell
			if ($this->getRTL()) {
				$header_x = $ormargins['right'] + ($headerdata['logo_width'] * 1.1);
			} else {
				$header_x = $ormargins['left'] + ($headerdata['logo_width'] * 1.1);
			}
			$this->SetTextColor(0, 0, 0);
			// header title
			$this->SetFont($headerfont[0], 'B', $headerfont[2] + 1);
			$this->SetX($header_x);			
			$this->Cell(0, $cell_height, $headerdata['title'], 0, 1, '', 0, '', 0);
			// header string
			$this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
			$this->SetX($header_x);
			$this->MultiCell(0, $cell_height, $headerdata['string'], 0, '', 0, 1, '', '', true, 0, false);
			// print an ending header line
			$this->SetLineStyle(array('width' => 0.85 / $this->getScaleFactor(), 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
			$this->SetY((2.835 / $this->getScaleFactor()) + max($imgy, $this->GetY()));
			if ($this->getRTL()) {
				$this->SetX($ormargins['right']);
			} else {
				$this->SetX($ormargins['left']);
			}
			$this->Cell(0, 0, '', 'T', 0, 'C');
		}
		
		/**
	 	 * This method is used to render the page footer. 
	 	 * It is automatically called by AddPage() and could be overwritten in your own inherited class.
		 * @access public
		 */
		public function Footer() {				
			$cur_y = $this->GetY();
			$ormargins = $this->getOriginalMargins();
			$this->SetTextColor(0, 0, 0);			
			//set style for cell border
			$line_width = 0.85 / $this->getScaleFactor();
			$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
			//print document barcode
			$barcode = $this->getBarcode();
			if (!empty($barcode)) {
				$this->Ln($line_width);
				$barcode_width = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right'])/3);
				$this->write1DBarcode($barcode, 'C128B', $this->GetX(), $cur_y + $line_width, $barcode_width, (($this->getFooterMargin() / 3) - $line_width), 0.3, '', '');	
			}
			if (empty($this->pagegroups)) {
				$pagenumtxt = $this->l['w_page'].' '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
			} else {
				$pagenumtxt = $this->l['w_page'].' '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
			}		
			$this->SetY($cur_y);
			//Print page number
			if ($this->getRTL()) {
				$this->SetX($ormargins['right']);
				$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
			} else {
				$this->SetX($ormargins['left']);
				$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'R');
			}
		}
		
		/**
	 	 * This method is used to render the page header. 
	 	 * @access protected
	 	 * @since 4.0.012 (2008-07-24)
		 */
		protected function setHeader() {
			if ($this->print_header) {
				$lasth = $this->lasth;
				$this->_out('q');
				$this->rMargin = $this->original_rMargin;
				$this->lMargin = $this->original_lMargin;
				$this->cMargin = 0;
				//set current position
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $this->header_margin);
				} else {
					$this->SetXY($this->original_lMargin, $this->header_margin);
				}
				$this->SetFont($this->header_font[0], $this->header_font[1], $this->header_font[2]);
				$this->Header();
				//restore position
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $this->tMargin);
				} else {
					$this->SetXY($this->original_lMargin, $this->tMargin);
				}
				$this->_out('Q');
				$this->lasth = $lasth;
			}
		}
		
		/**
	 	 * This method is used to render the page footer. 
	 	 * @access protected
	 	 * @since 4.0.012 (2008-07-24)
		 */
		protected function setFooter() {
			//Page footer
			// save current graphic settings
			$gvars = $this->getGraphicVars();
			// mark this point
			$this->footerpos[$this->page] = $this->pagelen[$this->page];
			$this->_out("\n");
			if ($this->print_footer) {
				$lasth = $this->lasth;
				$this->_out('q');
				$this->rMargin = $this->original_rMargin;
				$this->lMargin = $this->original_lMargin;
				$this->cMargin = 0;
				//set current position
				$footer_y = $this->h - $this->footer_margin;
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $footer_y);
				} else {
					$this->SetXY($this->original_lMargin, $footer_y);
				}
				$this->SetFont($this->footer_font[0], $this->footer_font[1], $this->footer_font[2]);
				$this->Footer();
				//restore position
				if ($this->rtl) {
					$this->SetXY($this->original_rMargin, $this->tMargin);
				} else {
					$this->SetXY($this->original_lMargin, $this->tMargin);
				}
				$this->_out('Q');
				$this->lasth = $lasth;
			}
			// restore graphic settings
			$this->setGraphicVars($gvars);
			// calculate footer lenght
			$this->footerlen[$this->page] = $this->pagelen[$this->page] - $this->footerpos[$this->page] + 1;
		}

		/**
	 	 * This method is used to render the table header on new page (if any). 
	 	 * @access protected
	 	 * @since 4.5.030 (2009-03-25)
		 */
		protected function setTableHeader() {
			if (isset($this->theadMargins['top'])) {
				// restore the original top-margin
				$this->tMargin = $this->theadMargins['top'];
				$this->pagedim[$this->page]['tm'] = $this->tMargin;
				$this->y = $this->tMargin;
			}
			if (!$this->empty_string($this->thead)) {
				// set margins
				$prev_lMargin = $this->lMargin;
				$prev_rMargin = $this->rMargin;
				$this->lMargin = $this->pagedim[$this->page]['olm'];
				$this->rMargin = $this->pagedim[$this->page]['orm'];
				$this->cMargin = $this->theadMargins['cmargin'];
				// print table header
				$this->writeHTML($this->thead, false, false, false, false, '');
				// set new top margin to skip the table headers
				if (!isset($this->theadMargins['top'])) {
					$this->theadMargins['top'] = $this->tMargin;
				}
				$this->tMargin = $this->y;
				$this->pagedim[$this->page]['tm'] = $this->tMargin;
				$this->lasth = 0;
				$this->lMargin = $prev_lMargin;
				$this->rMargin = $prev_rMargin;
			}
		}
		
		/**
		* Returns the current page number.
		* @return int page number
		* @access public
		* @since 1.0
		* @see AliasNbPages(), getAliasNbPages()
		*/
		public function PageNo() {
			return $this->page;
		}

		/**
		* Defines a new spot color. 
		* It can be expressed in RGB components or gray scale. 
		* The method can be called before the first page is created and the value is retained from page to page.
		* @param int $c Cyan color for CMYK. Value between 0 and 255
		* @param int $m Magenta color for CMYK. Value between 0 and 255
		* @param int $y Yellow color for CMYK. Value between 0 and 255
		* @param int $k Key (Black) color for CMYK. Value between 0 and 255
		* @access public
		* @since 4.0.024 (2008-09-12)
		* @see SetDrawSpotColor(), SetFillSpotColor(), SetTextSpotColor()
		*/
		public function AddSpotColor($name, $c, $m, $y, $k) {
			if (!isset($this->spot_colors[$name])) {
				$i = 1 + count($this->spot_colors);
				$this->spot_colors[$name] = array('i' => $i, 'c' => $c, 'm' => $m, 'y' => $y, 'k' => $k);
			}
		}

		/**
		* Defines the color used for all drawing operations (lines, rectangles and cell borders). 
		* It can be expressed in RGB components or gray scale. 
		* The method can be called before the first page is created and the value is retained from page to page.
		* @param array $color array of colors
		* @access public
		* @since 3.1.000 (2008-06-11)
		* @see SetDrawColor()
		*/
		public function SetDrawColorArray($color) {
			if (isset($color)) {
				$color = array_values($color);
				$r = isset($color[0]) ? $color[0] : -1;
				$g = isset($color[1]) ? $color[1] : -1;
				$b = isset($color[2]) ? $color[2] : -1;
				$k = isset($color[3]) ? $color[3] : -1;
				if ($r >= 0) {
					$this->SetDrawColor($r, $g, $b, $k);
				}
			}
		}

		/**
		* Defines the color used for all drawing operations (lines, rectangles and cell borders). It can be expressed in RGB components or gray scale. The method can be called before the first page is created and the value is retained from page to page.
		* @param int $col1 Gray level for single color, or Red color for RGB, or Cyan color for CMYK. Value between 0 and 255
		* @param int $col2 Green color for RGB, or Magenta color for CMYK. Value between 0 and 255
		* @param int $col3 Blue color for RGB, or Yellow color for CMYK. Value between 0 and 255
		* @param int $col4 Key (Black) color for CMYK. Value between 0 and 255
		* @access public
		* @since 1.3
		* @see SetDrawColorArray(), SetFillColor(), SetTextColor(), Line(), Rect(), Cell(), MultiCell()
		*/
		public function SetDrawColor($col1=0, $col2=-1, $col3=-1, $col4=-1) {
			// set default values
			if (!is_numeric($col1)) {
				$col1 = 0;
			}
			if (!is_numeric($col2)) {
				$col2 = -1;
			}
			if (!is_numeric($col3)) {
				$col3 = -1;
			}
			if (!is_numeric($col4)) {
				$col4 = -1;
			}
			//Set color for all stroking operations
			if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
				// Grey scale
				$this->DrawColor = sprintf('%.3F G', $col1/255);
			} elseif ($col4 == -1) {
				// RGB
				$this->DrawColor = sprintf('%.3F %.3F %.3F RG', $col1/255, $col2/255, $col3/255);
			} else {
				// CMYK
				$this->DrawColor = sprintf('%.3F %.3F %.3F %.3F K', $col1/100, $col2/100, $col3/100, $col4/100);
			}
			if ($this->page > 0) {
				$this->_out($this->DrawColor);
			}
		}
		
		/**
		* Defines the spot color used for all drawing operations (lines, rectangles and cell borders).
		* @param string $name name of the spot color
		* @param int $tint the intensity of the color (from 0 to 100 ; 100 = full intensity by default).
		* @access public
		* @since 4.0.024 (2008-09-12)
		* @see AddSpotColor(), SetFillSpotColor(), SetTextSpotColor()
		*/
		public function SetDrawSpotColor($name, $tint=100) {
			if (!isset($this->spot_colors[$name])) {
				$this->Error('Undefined spot color: '.$name);
			}
			$this->DrawColor = sprintf('/CS%d CS %.3F SCN', $this->spot_colors[$name]['i'], $tint/100);
			if ($this->page > 0) {
				$this->_out($this->DrawColor);
			}
		}
		
		/**
		* Defines the color used for all filling operations (filled rectangles and cell backgrounds). 
		* It can be expressed in RGB components or gray scale. 
		* The method can be called before the first page is created and the value is retained from page to page.
		* @param array $color array of colors
		* @access public
		* @since 3.1.000 (2008-6-11)
		* @see SetFillColor()
		*/
		public function SetFillColorArray($color) {
			if (isset($color)) {
				$color = array_values($color);
				$r = isset($color[0]) ? $color[0] : -1;
				$g = isset($color[1]) ? $color[1] : -1;
				$b = isset($color[2]) ? $color[2] : -1;
				$k = isset($color[3]) ? $color[3] : -1;
				if ($r >= 0) {
					$this->SetFillColor($r, $g, $b, $k);
				}
			}
		}
		
		/**
		* Defines the color used for all filling operations (filled rectangles and cell backgrounds). It can be expressed in RGB components or gray scale. The method can be called before the first page is created and the value is retained from page to page.
		* @param int $col1 Gray level for single color, or Red color for RGB, or Cyan color for CMYK. Value between 0 and 255
		* @param int $col2 Green color for RGB, or Magenta color for CMYK. Value between 0 and 255
		* @param int $col3 Blue color for RGB, or Yellow color for CMYK. Value between 0 and 255
		* @param int $col4 Key (Black) color for CMYK. Value between 0 and 255
		* @access public
		* @since 1.3
		* @see SetFillColorArray(), SetDrawColor(), SetTextColor(), Rect(), Cell(), MultiCell()
		*/
		public function SetFillColor($col1=0, $col2=-1, $col3=-1, $col4=-1) {
			// set default values
			if (!is_numeric($col1)) {
				$col1 = 0;
			}
			if (!is_numeric($col2)) {
				$col2 = -1;
			}
			if (!is_numeric($col3)) {
				$col3 = -1;
			}
			if (!is_numeric($col4)) {
				$col4 = -1;
			}
			//Set color for all filling operations
			if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
				// Grey scale
				$this->FillColor = sprintf('%.3F g', $col1/255);
				$this->bgcolor = array('G' => $col1);
			} elseif ($col4 == -1) {
				// RGB
				$this->FillColor = sprintf('%.3F %.3F %.3F rg', $col1/255, $col2/255, $col3/255);
				$this->bgcolor = array('R' => $col1, 'G' => $col2, 'B' => $col3);
			} else {
				// CMYK
				$this->FillColor = sprintf('%.3F %.3F %.3F %.3F k', $col1/100, $col2/100, $col3/100, $col4/100);
				$this->bgcolor = array('C' => $col1, 'M' => $col2, 'Y' => $col3, 'K' => $col4);
			}
			$this->ColorFlag = ($this->FillColor != $this->TextColor);
			if ($this->page > 0) {
				$this->_out($this->FillColor);
			}
		}
		
		/**
		* Defines the spot color used for all filling operations (filled rectangles and cell backgrounds).
		* @param string $name name of the spot color
		* @param int $tint the intensity of the color (from 0 to 100 ; 100 = full intensity by default).
		* @access public
		* @since 4.0.024 (2008-09-12)
		* @see AddSpotColor(), SetDrawSpotColor(), SetTextSpotColor()
		*/
		public function SetFillSpotColor($name, $tint=100) {
			if (!isset($this->spot_colors[$name])) {
				$this->Error('Undefined spot color: '.$name);
			}
			$this->FillColor = sprintf('/CS%d cs %.3F scn', $this->spot_colors[$name]['i'], $tint/100);
			$this->ColorFlag = ($this->FillColor != $this->TextColor);
			if ($this->page > 0) {
				$this->_out($this->FillColor);
			}
		}
		
		/**
		* Defines the color used for text. It can be expressed in RGB components or gray scale. 
		* The method can be called before the first page is created and the value is retained from page to page.
		* @param array $color array of colors
		* @access public
		* @since 3.1.000 (2008-6-11)
		* @see SetFillColor()
		*/
		public function SetTextColorArray($color) {
			if (isset($color)) {
				$color = array_values($color);
				$r = isset($color[0]) ? $color[0] : -1;
				$g = isset($color[1]) ? $color[1] : -1;
				$b = isset($color[2]) ? $color[2] : -1;
				$k = isset($color[3]) ? $color[3] : -1;
				if ($r >= 0) {
					$this->SetTextColor($r, $g, $b, $k);
				}
			}
		}

		/**
		* Defines the color used for text. It can be expressed in RGB components or gray scale. The method can be called before the first page is created and the value is retained from page to page.
		* @param int $col1 Gray level for single color, or Red color for RGB, or Cyan color for CMYK. Value between 0 and 255
		* @param int $col2 Green color for RGB, or Magenta color for CMYK. Value between 0 and 255
		* @param int $col3 Blue color for RGB, or Yellow color for CMYK. Value between 0 and 255
		* @param int $col4 Key (Black) color for CMYK. Value between 0 and 255
		* @access public
		* @since 1.3
		* @see SetTextColorArray(), SetDrawColor(), SetFillColor(), Text(), Cell(), MultiCell()
		*/
		public function SetTextColor($col1=0, $col2=-1, $col3=-1, $col4=-1) {
			// set default values
			if (!is_numeric($col1)) {
				$col1 = 0;
			}
			if (!is_numeric($col2)) {
				$col2 = -1;
			}
			if (!is_numeric($col3)) {
				$col3 = -1;
			}
			if (!is_numeric($col4)) {
				$col4 = -1;
			}
			//Set color for text
			if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
				// Grey scale
				$this->TextColor = sprintf('%.3F g', $col1/255);
				$this->fgcolor = array('G' => $col1);
			} elseif ($col4 == -1) {
				// RGB
				$this->TextColor = sprintf('%.3F %.3F %.3F rg', $col1/255, $col2/255, $col3/255);
				$this->fgcolor = array('R' => $col1, 'G' => $col2, 'B' => $col3);
			} else {
				// CMYK
				$this->TextColor = sprintf('%.3F %.3F %.3F %.3F k', $col1/100, $col2/100, $col3/100, $col4/100);
				$this->fgcolor = array('C' => $col1, 'M' => $col2, 'Y' => $col3, 'K' => $col4);
			}
			$this->ColorFlag = ($this->FillColor != $this->TextColor);
		}
		
		/**
		* Defines the spot color used for text.
		* @param string $name name of the spot color
		* @param int $tint the intensity of the color (from 0 to 100 ; 100 = full intensity by default).
		* @access public
		* @since 4.0.024 (2008-09-12)
		* @see AddSpotColor(), SetDrawSpotColor(), SetFillSpotColor()
		*/
		public function SetTextSpotColor($name, $tint=100) {
			if (!isset($this->spot_colors[$name])) {
				$this->Error('Undefined spot color: '.$name);
			}
			$this->TextColor = sprintf('/CS%d cs %.3F scn', $this->spot_colors[$name]['i'], $tint/100);
			$this->ColorFlag = ($this->FillColor != $this->TextColor);
			if ($this->page > 0) {
				$this->_out($this->TextColor);
			}
		}

		/**
		* Returns the length of a string in user unit. A font must be selected.<br>
		* @param string $s The string whose length is to be computed
		* @param string $fontname Family font. It can be either a name defined by AddFont() or one of the standard families. It is also possible to pass an empty string, in that case, the current family is retained.
		* @param string $fontstyle Font style. Possible values are (case insensitive):<ul><li>empty string: regular</li><li>B: bold</li><li>I: italic</li><li>U: underline</li><li>D: line trough</li></ul> or any combination. The default value is regular.
		* @param float $fontsize Font size in points. The default value is the current size.
		* @return int string length
		* @author Nicola Asuni
		* @access public
		* @since 1.2
		*/
		public function GetStringWidth($s, $fontname='', $fontstyle='', $fontsize=0) {
			return $this->GetArrStringWidth($this->utf8Bidi($this->UTF8StringToArray($s), $s, $this->tmprtl), $fontname, $fontstyle, $fontsize);
		}
		
		/**
		* Returns the string length of an array of chars in user unit. A font must be selected.<br>
		* @param string $sa The array of chars whose total length is to be computed
		* @param string $fontname Family font. It can be either a name defined by AddFont() or one of the standard families. It is also possible to pass an empty string, in that case, the current family is retained.
		* @param string $fontstyle Font style. Possible values are (case insensitive):<ul><li>empty string: regular</li><li>B: bold</li><li>I: italic</li><li>U: underline</li><li>D: line trough</li></ul> or any combination. The default value is regular.
		* @param float $fontsize Font size in points. The default value is the current size.
		* @return int string length
		* @author Nicola Asuni
		* @access public
		* @since 2.4.000 (2008-03-06)
		*/
		public function GetArrStringWidth($sa, $fontname='', $fontstyle='', $fontsize=0) {
			// store current values
			if (!$this->empty_string($fontname)) {
				$prev_FontFamily = $this->FontFamily;
				$prev_FontStyle = $this->FontStyle;
				$prev_FontSizePt = $this->FontSizePt;
				$this->SetFont($fontname, $fontstyle, $fontsize);
			}
			$w = 0;
			foreach ($sa as $char) {
				$w += $this->GetCharWidth($char);
			}
			// restore previous values
			if (!$this->empty_string($fontname)) {
				$this->SetFont($prev_FontFamily, $prev_FontStyle, $prev_FontSizePt);
			}
			return $w;
		}
		
		/**
		* Returns the length of the char in user unit for the current font.
		* @param int $char The char code whose length is to be returned
		* @return int char width
		* @author Nicola Asuni
		* @access public
		* @since 2.4.000 (2008-03-06)
		*/
		public function GetCharWidth($char) {
			if ($char == 173) {
				// SHY character will not be printed
				return (0);
			}
			$cw = &$this->CurrentFont['cw'];
			if (isset($cw[$char])) {
				$w = $cw[$char];
			} elseif (isset($this->CurrentFont['dw'])) {
				// default width
				$w = $this->CurrentFont['dw'];
			} elseif (isset($cw[32])) {
				// default width
				$dw = $cw[32];
			} else {
				$w = 600;
			}
			return ($w * $this->FontSize / 1000);
		}
		
		/**
		* Returns the numbero of characters in a string.
		* @param string $s The input string.
		* @return int number of characters
		* @access public
		* @since 2.0.0001 (2008-01-07)
		*/
		public function GetNumChars($s) {
			if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
				return count($this->UTF8StringToArray($s));
			} 
			return strlen($s);
		}
			
		/**
		* Fill the list of available fonts ($this->fontlist).
		* @access protected
		* @since 4.0.013 (2008-07-28)
		*/
		protected function getFontsList() {
			$fontsdir = opendir($this->_getfontpath());
			while (($file = readdir($fontsdir)) !== false) {
				if (substr($file, -4) == '.php') {
					array_push($this->fontlist, strtolower(basename($file, '.php')));
				}
			}
			closedir($fontsdir);
		}
		
		/**
		* Imports a TrueType, Type1, core, or CID0 font and makes it available.
		* It is necessary to generate a font definition file first (read /fonts/utils/README.TXT). 
		* The definition file (and the font file itself when embedding) must be present either in the current directory or in the one indicated by K_PATH_FONTS if the constant is defined. If it could not be found, the error "Could not include font definition file" is generated.
		* @param string $family Font family. The name can be chosen arbitrarily. If it is a standard family name, it will override the corresponding font.
		* @param string $style Font style. Possible values are (case insensitive):<ul><li>empty string: regular (default)</li><li>B: bold</li><li>I: italic</li><li>BI or IB: bold italic</li></ul>
		* @param string $fontfile The font definition file. By default, the name is built from the family and style, in lower case with no spaces.
		* @return array containing the font data, or false in case of error.
		* @access public
		* @since 1.5
		* @see SetFont()
		*/
		public function AddFont($family, $style='', $fontfile='') {
			if ($this->empty_string($family)) {
				if (!$this->empty_string($this->FontFamily)) {
					$family = $this->FontFamily;
				} else {
					$this->Error('Empty font family');
				}
			}
			$family = strtolower($family);
			if ((!$this->isunicode) AND ($family == 'arial')) {
				$family = 'helvetica';
			}
			if (($family == 'symbol') OR ($family == 'zapfdingbats')) {
				$style = '';
			}
			$tempstyle = strtoupper($style);
			$style = '';
			// underline
			if (strpos($tempstyle, 'U') !== false) {
				$this->underline = true;
			} else {
				$this->underline = false;
			}
			// line through (deleted)
			if (strpos($tempstyle, 'D') !== false) {
				$this->linethrough = true;
			} else {
				$this->linethrough = false;
			}
			// bold
			if (strpos($tempstyle, 'B') !== false) {
				$style .= 'B';
			}
			// oblique
			if (strpos($tempstyle, 'I') !== false) {
				$style .= 'I';
			}
			$bistyle = $style;
			$fontkey = $family.$style;
			$font_style = $style.($this->underline ? 'U' : '').($this->linethrough ? 'D' : '');
			$fontdata = array('fontkey' => $fontkey, 'family' => $family, 'style' => $font_style);
			// check if the font has been already added
			if ($this->getFontBuffer($fontkey) !== false) {
				return $fontdata;
			}
			if (isset($type)) {
				unset($type); 
			}
			if (isset($cw)) {
				unset($cw); 
			}
			// get specified font directory (if any)
			$fontdir = '';
			if (!$this->empty_string($fontfile)) {
				$fontdir = dirname($fontfile);
				if ($this->empty_string($fontdir) OR ($fontdir == '.')) {
					$fontdir = '';
				} else {
					$fontdir .= '/';
				}
			}
			// search and include font file
			if ($this->empty_string($fontfile) OR (!file_exists($fontfile))) {
				// build a standard filenames for specified font
				$fontfile1 = str_replace(' ', '', $family).strtolower($style).'.php';
				$fontfile2 = str_replace(' ', '', $family).'.php';
				// search files on various directories
				if (file_exists($fontdir.$fontfile1)) {
					$fontfile = $fontdir.$fontfile1;
				} elseif (file_exists($this->_getfontpath().$fontfile1)) {
					$fontfile = $this->_getfontpath().$fontfile1;
				} elseif (file_exists($fontfile1)) {
					$fontfile = $fontfile1;
				} elseif (file_exists($fontdir.$fontfile2)) {
					$fontfile = $fontdir.$fontfile2;
				} elseif (file_exists($this->_getfontpath().$fontfile2)) {
					$fontfile = $this->_getfontpath().$fontfile2;
				} else {
					$fontfile = $fontfile2;
				}
			}
			// include font file
			if (file_exists($fontfile)) {
				include($fontfile);
			} else {
				$this->Error('Could not include font definition file: '.$family.'');
			}
			// check font parameters
			if ((!isset($type)) OR (!isset($cw))) {
				$this->Error('The font definition file has a bad format: '.$fontfile.'');
			}
			// SET default parameters
			if (!isset($file) OR $this->empty_string($file)) {
				$file = '';
			}
			if (!isset($enc) OR $this->empty_string($enc)) {
				$enc = '';
			}
			if (!isset($cidinfo) OR $this->empty_string($cidinfo)) {
				$cidinfo = array('Registry'=>'Adobe','Ordering'=>'Identity','Supplement'=>0);
				$cidinfo['uni2cid'] = array();
			}
			if (!isset($ctg) OR $this->empty_string($ctg)) {
				$ctg = '';
			}
			if (!isset($desc) OR $this->empty_string($desc)) {
				$desc = array();
			}
			if (!isset($up) OR $this->empty_string($up)) {
				$up = -100;
			}
			if (!isset($ut) OR $this->empty_string($ut)) {
				$ut = 50;
			}
			if (!isset($cw) OR $this->empty_string($cw)) {
				$cw = array();
			}
			if (!isset($dw) OR $this->empty_string($dw)) {
				// set default width
				if (isset($desc['MissingWidth']) AND ($desc['MissingWidth'] > 0)) {
					$dw = $desc['MissingWidth'];
				} elseif (isset($cw[32])) {
					$dw = $cw[32];
				} else {
					$dw = 600;
				}
			}
			++$this->numfonts;			
			if ($type == 'cidfont0') {
				// register CID font (all styles at once)
				$styles = array('' => '', 'B' => ',Bold', 'I' => ',Italic', 'BI' => ',BoldItalic');
				$sname = $name.$styles[$bistyle];
				if ((strpos($bistyle, 'B') !== false) AND (isset($desc['StemV'])) AND ($desc['StemV'] == 70)) {
					$desc['StemV'] = 120;
				}
			} elseif ($type == 'core') {
				$name = $this->CoreFonts[$fontkey];
			} elseif (($type == 'TrueType') OR ($type == 'Type1')) {
				// ...
			} elseif ($type == 'TrueTypeUnicode') {
				$enc = 'Identity-H';
			} else {
				$this->Error('Unknow font type: '.$type.'');
			}
			$this->setFontBuffer($fontkey, array('i' => $this->numfonts, 'type' => $type, 'name' => $name, 'desc' => $desc, 'up' => $up, 'ut' => $ut, 'cw' => $cw, 'dw' => $dw, 'enc' => $enc, 'cidinfo' => $cidinfo, 'file' => $file, 'ctg' => $ctg));
			if (isset($diff) AND (!empty($diff))) {
				//Search existing encodings
				$d = 0;
				$nb = count($this->diffs);
				for ($i=1; $i <= $nb; ++$i) {
					if ($this->diffs[$i] == $diff) {
						$d = $i;
						break;
					}
				}
				if ($d == 0) {
					$d = $nb + 1;
					$this->diffs[$d] = $diff;
				}
				$this->setFontSubBuffer($fontkey, 'diff', $d);
			}
			if (!$this->empty_string($file)) {
				if ((strcasecmp($type,'TrueType') == 0) OR (strcasecmp($type, 'TrueTypeUnicode') == 0)) {
					$this->FontFiles[$file] = array('length1' => $originalsize, 'fontdir' => $fontdir);
				} elseif ($type != 'core') {
					$this->FontFiles[$file] = array('length1' => $size1, 'length2' => $size2, 'fontdir' => $fontdir);
				}
			}
			return $fontdata;
		}

		/**
		* Sets the font used to print character strings. 
		* The font can be either a standard one or a font added via the AddFont() method. Standard fonts use Windows encoding cp1252 (Western Europe).
		* The method can be called before the first page is created and the font is retained from page to page. 
		* If you just wish to change the current font size, it is simpler to call SetFontSize().
		* Note: for the standard fonts, the font metric files must be accessible. There are three possibilities for this:<ul><li>They are in the current directory (the one where the running script lies)</li><li>They are in one of the directories defined by the include_path parameter</li><li>They are in the directory defined by the K_PATH_FONTS constant</li></ul><br />
		* @param string $family Family font. It can be either a name defined by AddFont() or one of the standard Type1 families (case insensitive):<ul><li>times (Times-Roman)</li><li>timesb (Times-Bold)</li><li>timesi (Times-Italic)</li><li>timesbi (Times-BoldItalic)</li><li>helvetica (Helvetica)</li><li>helveticab (Helvetica-Bold)</li><li>helveticai (Helvetica-Oblique)</li><li>helveticabi (Helvetica-BoldOblique)</li><li>courier (Courier)</li><li>courierb (Courier-Bold)</li><li>courieri (Courier-Oblique)</li><li>courierbi (Courier-BoldOblique)</li><li>symbol (Symbol)</li><li>zapfdingbats (ZapfDingbats)</li></ul> It is also possible to pass an empty string. In that case, the current family is retained.
		* @param string $style Font style. Possible values are (case insensitive):<ul><li>empty string: regular</li><li>B: bold</li><li>I: italic</li><li>U: underline</li><li>D: line trough</li></ul> or any combination. The default value is regular. Bold and italic styles do not apply to Symbol and ZapfDingbats basic fonts or other fonts when not defined.
		* @param float $size Font size in points. The default value is the current size. If no size has been specified since the beginning of the document, the value taken is 12
		* @param string $fontfile The font definition file. By default, the name is built from the family and style, in lower case with no spaces.
		* @access public
		* @since 1.0
		* @see AddFont(), SetFontSize()
		*/
		public function SetFont($family, $style='', $size=0, $fontfile='') {
			//Select a font; size given in points
			if ($size == 0) {
				$size = $this->FontSizePt;
			}
			// try to add font (if not already added)
			$fontdata = $this->AddFont($family, $style, $fontfile);
			$this->FontFamily = $fontdata['family'];
			$this->FontStyle = $fontdata['style'];
			$this->CurrentFont = $this->getFontBuffer($fontdata['fontkey']);
			$this->SetFontSize($size);
		}

		/**
		* Defines the size of the current font.
		* @param float $size The size (in points)
		* @access public
		* @since 1.0
		* @see SetFont()
		*/
		public function SetFontSize($size) {
			//Set font size in points
			$this->FontSizePt = $size;
			$this->FontSize = $size / $this->k;
			if (isset($this->CurrentFont['desc']['Ascent']) AND ($this->CurrentFont['desc']['Ascent'] > 0)) {
				$this->FontAscent = $this->CurrentFont['desc']['Ascent'] * $this->FontSize / 1000;
			} else {
				$this->FontAscent = 0.8 * $this->FontSize;
			}
			if (isset($this->CurrentFont['desc']['Descent']) AND ($this->CurrentFont['desc']['Descent'] > 0)) {
				$this->FontDescent = - $this->CurrentFont['desc']['Descent'] * $this->FontSize / 1000;
			} else {
				$this->FontDescent = 0.2 * $this->FontSize;
			}
			if (($this->page > 0) AND (isset($this->CurrentFont['i']))) {
				$this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
			}
		}

		/**
		* Defines the default monospaced font.
		* @param string $font Font name.
		* @access public
		* @since 4.5.025
		*/
		public function SetDefaultMonospacedFont($font) {
			$this->default_monospaced_font = $font;
		}
		
		/**
		* Creates a new internal link and returns its identifier. An internal link is a clickable area which directs to another place within the document.<br />
		* The identifier can then be passed to Cell(), Write(), Image() or Link(). The destination is defined with SetLink().
		* @access public
		* @since 1.5
		* @see Cell(), Write(), Image(), Link(), SetLink()
		*/
		public function AddLink() {
			//Create a new internal link
			$n = count($this->links) + 1;
			$this->links[$n] = array(0, 0);
			return $n;
		}

		/**
		* Defines the page and position a link points to.
		* @param int $link The link identifier returned by AddLink()
		* @param float $y Ordinate of target position; -1 indicates the current position. The default value is 0 (top of page)
		* @param int $page Number of target page; -1 indicates the current page. This is the default value
		* @access public
		* @since 1.5
		* @see AddLink()
		*/
		public function SetLink($link, $y=0, $page=-1) {
			if ($y == -1) {
				$y = $this->y;
			}
			if ($page == -1) {
				$page = $this->page;
			}
			$this->links[$link] = array($page, $y);
		}

		/**
		* Puts a link on a rectangular area of the page.
		* Text or image links are generally put via Cell(), Write() or Image(), but this method can be useful for instance to define a clickable area inside an image.
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param float $w Width of the rectangle
		* @param float $h Height of the rectangle
		* @param mixed $link URL or identifier returned by AddLink()
		* @param int $spaces number of spaces on the text to link
		* @access public
		* @since 1.5
		* @see AddLink(), Annotation(), Cell(), Write(), Image()
		*/
		public function Link($x, $y, $w, $h, $link, $spaces=0) {
			$this->Annotation($x, $y, $w, $h, $link, array('Subtype'=>'Link'), $spaces);
		}
		
		/**
		* Puts a markup annotation on a rectangular area of the page.
		* !!!!THE ANNOTATION SUPPORT IS NOT YET FULLY IMPLEMENTED !!!!
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param float $w Width of the rectangle
		* @param float $h Height of the rectangle
		* @param string $text annotation text or alternate content
		* @param array $opt array of options (see section 8.4 of PDF reference 1.7).
		* @param int $spaces number of spaces on the text to link
		* @access public
		* @since 4.0.018 (2008-08-06)
		*/
		public function Annotation($x='', $y='', $w, $h, $text, $opt=array('Subtype'=>'Text'), $spaces=0) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			// recalculate coordinates to account for graphic transformations
			if (isset($this->transfmatrix)) {
				for ($i=$this->transfmatrix_key; $i > 0; --$i) {
					$maxid = count($this->transfmatrix[$i]) - 1;
					for ($j=$maxid; $j >= 0; --$j) {
						$ctm = $this->transfmatrix[$i][$j];
						if (isset($ctm['a'])) {
							$x = $x * $this->k;
							$y = ($this->h - $y) * $this->k;
							$w = $w * $this->k;
							$h = $h * $this->k;
							// top left
							$xt = $x;
							$yt = $y;
							$x1 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
							$y1 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
							// top right
							$xt = $x + $w;
							$yt = $y;
							$x2 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
							$y2 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
							// bottom left
							$xt = $x;
							$yt = $y - $h;
							$x3 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
							$y3 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
							// bottom right
							$xt = $x + $w;
							$yt = $y - $h;
							$x4 = ($ctm['a'] * $xt) + ($ctm['c'] * $yt) + $ctm['e'];
							$y4 = ($ctm['b'] * $xt) + ($ctm['d'] * $yt) + $ctm['f'];
							// new coordinates (rectangle area)
							$x = min($x1, $x2, $x3, $x4);
							$y = max($y1, $y2, $y3, $y4);
							$w = (max($x1, $x2, $x3, $x4) - $x) / $this->k;
							$h = ($y - min($y1, $y2, $y3, $y4)) / $this->k;
							$x = $x / $this->k;
							$y = $this->h - ($y / $this->k);
						}
					}
				}
			}
			if ($this->page <= 0) {
				$page = 1;
			} else {
				$page = $this->page;
			}
			if (!isset($this->PageAnnots[$page])) {
				$this->PageAnnots[$page] = array();
			}
			$this->PageAnnots[$page][] = array('x' => $x, 'y' => $y, 'w' => $w, 'h' => $h, 'txt' => $text, 'opt' => $opt, 'numspaces' => $spaces);
			if (($opt['Subtype'] == 'FileAttachment') AND (!$this->empty_string($opt['FS'])) AND file_exists($opt['FS']) AND (!isset($this->embeddedfiles[basename($opt['FS'])]))) {
				$this->embeddedfiles[basename($opt['FS'])] = array('file' => $opt['FS'], 'n' => (count($this->embeddedfiles) + $this->embedded_start_obj_id));
			}
			// Add widgets annotation's icons
			if (isset($opt['mk']['i']) AND file_exists($opt['mk']['i'])) {
				$this->Image($opt['mk']['i'], '', '', 10, 10, '', '', '', false, 300, '', false, false, 0, false, true);
			}
			if (isset($opt['mk']['ri']) AND file_exists($opt['mk']['ri'])) {
				$this->Image($opt['mk']['ri'], '', '', 0, 0, '', '', '', false, 300, '', false, false, 0, false, true);
			}
			if (isset($opt['mk']['ix']) AND file_exists($opt['mk']['ix'])) {
				$this->Image($opt['mk']['ix'], '', '', 0, 0, '', '', '', false, 300, '', false, false, 0, false, true);
			}
			++$this->annot_obj_id;
		}

		/**
		* Embedd the attached files.
		* @since 4.4.000 (2008-12-07)
		* @access protected
		* @see Annotation()
		*/
		protected function _putEmbeddedFiles() {
			reset($this->embeddedfiles);
			foreach ($this->embeddedfiles as $filename => $filedata) {
				$data = file_get_contents($filedata['file']);
				$filter = '';
				if ($this->compress) {
					$data = gzcompress($data);
					$filter = ' /Filter /FlateDecode';
				}
				$this->offsets[$filedata['n']] = $this->bufferlen;
				$this->_out($filedata['n'].' 0 obj');
				$this->_out('<</Type /EmbeddedFile'.$filter.' /Length '.strlen($data).' >>');
				$this->_putstream($data);
				$this->_out('endobj');
			}
		}
		
		/**
		* Prints a character string.
		* The origin is on the left of the first charcter, on the baseline.
		* This method allows to place a string precisely on the page.
		* @param float $x Abscissa of the origin
		* @param float $y Ordinate of the origin
		* @param string $txt String to print
		* @param int $stroke outline size in points (0 = disable)
		* @param boolean $clip if true activate clipping mode (you must call StartTransform() before this function and StopTransform() to stop the clipping tranformation).
		* @access public
		* @since 1.0
		* @deprecated deprecated since version 4.3.005 (2008-11-25)
		* @see Cell(), Write(), MultiCell(), WriteHTML(), WriteHTMLCell()
		*/
		public function Text($x, $y, $txt, $stroke=0, $clip=false) {
			//Output a string
			if ($this->rtl) {
				// bidirectional algorithm (some chars may be changed affecting the line length)
				$s = $this->utf8Bidi($this->UTF8StringToArray($txt), $txt, $this->tmprtl);
				$l = $this->GetArrStringWidth($s);
				$xr = $this->w - $x - $l;
			} else {
				$xr = $x;
			}
			$opt = '';
			if (($stroke > 0) AND (!$clip)) {
				$opt .= '1 Tr '.intval($stroke).' w ';
			} elseif (($stroke > 0) AND $clip) {
				$opt .= '5 Tr '.intval($stroke).' w ';
			} elseif ($clip) {
				$opt .= '7 Tr ';
			}
			$s = sprintf('BT %.2F %.2F Td %s(%s) Tj ET 0 Tr', $xr * $this->k, ($this->h-$y) * $this->k, $opt, $this->_escapetext($txt));
			if ($this->underline AND ($txt!='')) {
				$s .= ' '.$this->_dounderline($xr, $y, $txt);
			}
			if ($this->linethrough AND ($txt!='')) { 
				$s .= ' '.$this->_dolinethrough($xr, $y, $txt); 
			}
			if ($this->ColorFlag AND (!$clip)) {
				$s='q '.$this->TextColor.' '.$s.' Q';
			}
			$this->_out($s);
		}

		/**
		* Whenever a page break condition is met, the method is called, and the break is issued or not depending on the returned value. 
		* The default implementation returns a value according to the mode selected by SetAutoPageBreak().<br />
		* This method is called automatically and should not be called directly by the application.
		* @return boolean
		* @access public
		* @since 1.4
		* @see SetAutoPageBreak()
		*/
		public function AcceptPageBreak() {
			return $this->AutoPageBreak;
		}
		
		/**
		* Add page if needed.
		* @param float $h Cell height. Default value: 0.
		* @param mixed $y starting y position, leave empty for current position.
		* @param boolean $addpage if true add a page, otherwise only return the true/false state
		* @return boolean true in case of page break, false otherwise.
		* @since 3.2.000 (2008-07-01)
		* @access protected
		*/
		protected function checkPageBreak($h=0, $y='', $addpage=true) {
			if ($this->empty_string($y)) {
				$y = $this->y;
			}
			if ((($y + $h) > $this->PageBreakTrigger) AND (!$this->InFooter) AND ($this->AcceptPageBreak())) {
				if ($addpage) {
					//Automatic page break
					$x = $this->x;
					$this->AddPage($this->CurOrientation);
					$this->y = $this->tMargin;
					$oldpage = $this->page - 1;
					if ($this->rtl) {
						if ($this->pagedim[$this->page]['orm'] != $this->pagedim[$oldpage]['orm']) {
							$this->x = $x - ($this->pagedim[$this->page]['orm'] - $this->pagedim[$oldpage]['orm']);
						} else {
							$this->x = $x;
						}
					} else {
						if ($this->pagedim[$this->page]['olm'] != $this->pagedim[$oldpage]['olm']) {
							$this->x = $x + ($this->pagedim[$this->page]['olm'] - $this->pagedim[$oldpage]['olm']);
						} else {
							$this->x = $x;
						}
					}
				}
				return true;
			}
			return false;
		}

		/**
		* Prints a cell (rectangular area) with optional borders, background color and character string. The upper-left corner of the cell corresponds to the current position. The text can be aligned or centered. After the call, the current position moves to the right or to the next line. It is possible to put a link on the text.<br />
		* If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
		* @param float $w Cell width. If 0, the cell extends up to the right margin.
		* @param float $h Cell height. Default value: 0.
		* @param string $txt String to print. Default value: empty string.
		* @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL languages)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>
		Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
		* @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @param boolean $ignore_min_height if true ignore automatic minimum height value.
		* @access public
		* @since 1.0
		* @see SetFont(), SetDrawColor(), SetFillColor(), SetTextColor(), SetLineWidth(), AddLink(), Ln(), MultiCell(), Write(), SetAutoPageBreak()
		*/
		public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false) {
			//$min_cell_height = $this->FontAscent + $this->FontDescent;
			$min_cell_height = $this->FontSize * $this->cell_height_ratio;
			if ($h < $min_cell_height) {
				$h = $min_cell_height;
			}
			$this->checkPageBreak($h);
			$this->_out($this->getCellCode($w, $h, $txt, $border, $ln, $align, $fill, $link, $stretch, $ignore_min_height));
		}

		/**
		* Removes SHY characters from text.
		* @param string $txt input string
		* @return string without SHY characters.
		* @access public
		* @since (4.5.019) 2009-02-28
		*/
		public function removeSHY($txt='') {
			/*
			* Unicode Data
			* Name : SOFT HYPHEN, commonly abbreviated as SHY
			* HTML Entity (decimal): &#173;
			* HTML Entity (hex): &#xad;
			* HTML Entity (named): &shy;
			* How to type in Microsoft Windows: [Alt +00AD] or [Alt 0173]
			* UTF-8 (hex): 0xC2 0xAD (c2ad)
			* UTF-8 character: chr(194).chr(173)
			*/
			$txt = preg_replace('/([\\xc2]{1}[\\xad]{1})/', '', $txt);
			if (!$this->isunicode) {
				$txt = preg_replace('/([\\xad]{1})/', '', $txt);
			}
			return $txt;
		}
		
		/**
		* Returns the PDF string code to print a cell (rectangular area) with optional borders, background color and character string. The upper-left corner of the cell corresponds to the current position. The text can be aligned or centered. After the call, the current position moves to the right or to the next line. It is possible to put a link on the text.<br />
		* If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
		* @param float $w Cell width. If 0, the cell extends up to the right margin.
		* @param float $h Cell height. Default value: 0.
		* @param string $txt String to print. Default value: empty string.
		* @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL languages)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
		* @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @param boolean $ignore_min_height if true ignore automatic minimum height value.
		* @access protected
		* @since 1.0
		* @see Cell()
		*/
		protected function getCellCode($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false) {
			$txt = $this->removeSHY($txt);
			$rs = ''; //string to be returned
			if (!$ignore_min_height) {
				$min_cell_height = $this->FontSize * $this->cell_height_ratio;
				if ($h < $min_cell_height) {
					$h = $min_cell_height;
				}
			}
			$k = $this->k;
			if ($this->empty_string($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $this->x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $this->x;
				}
			}
			$s = '';			
			if (($fill == 1) OR ($border == 1)) {
				if ($fill == 1) {
					$op = ($border == 1) ? 'B' : 'f';
				} else {
					$op = 'S';
				}
				if ($this->rtl) {
					$xk = (($this->x  - $w) * $k);
				} else {
					$xk = ($this->x * $k);
				}
				$s .= sprintf('%.2F %.2F %.2F %.2F re %s ', $xk, (($this->h - $this->y) * $k), ($w * $k), (-$h * $k), $op);
			}
			if (is_string($border)) {
				$lm = ($this->LineWidth / 2);
				$x = $this->x;
				$y = $this->y;
				if (strpos($border,'L') !== false) {
					if ($this->rtl) {
						$xk = ($x - $w) * $k;
					} else {
						$xk = $x * $k;
					}
					$s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $xk, (($this->h - $y + $lm) * $k), $xk, (($this->h - ($y + $h + $lm)) * $k));
				}
				if (strpos($border,'T') !== false) {
					if ($this->rtl) {
						$xk = ($x - $w + $lm) * $k;
						$xwk = ($x - $lm) * $k;
					} else {
						$xk = ($x - $lm) * $k;
						$xwk = ($x + $w + $lm) * $k;
					}
					$s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $xk, (($this->h - $y) * $k), $xwk, (($this->h - $y) * $k));
				}
				if (strpos($border,'R') !== false) {
					if ($this->rtl) {
						$xk = $x * $k;
					} else {
						$xk = ($x + $w) * $k;
					}
					$s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $xk, (($this->h - $y + $lm) * $k), $xk, (($this->h - ($y + $h + $lm))* $k));
				}
				if (strpos($border,'B') !== false) {
					if ($this->rtl) {
						$xk = ($x - $w + $lm) * $k;
						$xwk = ($x - $lm) * $k;
					} else {
						$xk = ($x - $lm) * $k;
						$xwk = ($x + $w + $lm) * $k;
					}
					$s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $xk, (($this->h - ($y + $h)) * $k), $xwk, (($this->h - ($y + $h)) * $k));
				}
			}
			if ($txt != '') {
				// text lenght
				$width = $this->GetStringWidth($txt);
				// ratio between cell lenght and text lenght
				if ($width <= 0) {
					$ratio = 1;
				} else {
					$ratio = ($w - (2 * $this->cMargin)) / $width;
				}
				// stretch text if required
				if (($stretch > 0) AND (($ratio < 1) OR (($ratio > 1) AND (($stretch % 2) == 0)))) {
					if ($stretch > 2) {
						// spacing
						//Calculate character spacing in points
						$char_space = (($w - $width - (2 * $this->cMargin)) * $this->k) / max($this->GetNumChars($txt)-1,1);
						//Set character spacing
						$rs .= sprintf('BT %.2F Tc ET ', $char_space);
					} else {
						// scaling
						//Calculate horizontal scaling
						$horiz_scale = $ratio * 100.0;
						//Set horizontal scaling
						$rs .= sprintf('BT %.2F Tz ET ', $horiz_scale);
					}
					$align = '';
					$width = $w - (2 * $this->cMargin);
				} else {
					$stretch == 0;
				}
				if ($align == 'L') {
					if ($this->rtl) {
						$dx = $w - $width - $this->cMargin;
					} else {
						$dx = $this->cMargin;
					}
				} elseif ($align == 'R') {
					if ($this->rtl) {
						$dx = $this->cMargin;
					} else {
						$dx = $w - $width - $this->cMargin;
					}
				} elseif ($align == 'C') {
					$dx = ($w - $width) / 2;
				} elseif ($align == 'J') {
					if ($this->rtl) {
						$dx = $w - $width - $this->cMargin;
					} else {
						$dx = $this->cMargin;
					}
				} else {
					$dx = $this->cMargin;
				}
				if ($this->ColorFlag) {
					$s .= 'q '.$this->TextColor.' ';
				}
				$txt2 = $this->_escapetext($txt);
				if ($this->rtl) {
					$xdk = ($this->x - $dx - $width) * $k;
				} else {
					$xdk = ($this->x + $dx) * $k;
				}
				// Justification
				if ($align == 'J') {
					// count number of spaces
					$ns = substr_count($txt, ' ');
					if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
						// get string width without spaces
						$width = $this->GetStringWidth(str_replace(' ', '', $txt));
						// calculate average space width
						$spacewidth = -1000 * ($w - $width - (2 * $this->cMargin)) / ($ns?$ns:1) / $this->FontSize;
						// set word position to be used with TJ operator
						$txt2 = str_replace(chr(0).' ', ') '.($spacewidth).' (', $txt2);
					} else {
						// get string width
						$width = $this->GetStringWidth($txt);
						$spacewidth = (($w - $width - (2 * $this->cMargin)) / ($ns?$ns:1)) * $this->k;
						$rs .= sprintf('BT %.3F Tw ET ', $spacewidth);
					}
				}
				// calculate approximate position of the font base line
				//$basefonty = $this->y + (($h + $this->FontAscent - $this->FontDescent)/2);
				$basefonty = $this->y + ($h/2) + ($this->FontSize/3);
				// print text
				$s .= sprintf('BT %.2F %.2F Td [(%s)] TJ ET', $xdk, (($this->h - $basefonty) * $k), $txt2);
				if ($this->rtl) {
					$xdx = $this->x - $dx - $width;
				} else {
					$xdx = $this->x + $dx;
				}
				if ($this->underline)  {
					$s .= ' '.$this->_dounderlinew($xdx, $basefonty, $width);
				}
				if ($this->linethrough) { 
					$s .= ' '.$this->_dolinethroughw($xdx, $basefonty, $width);
				}
				if ($this->ColorFlag) {
					$s .= ' Q';
				}
				if ($link) {
					$this->Link($xdx, $this->y + (($h - $this->FontSize)/2), $width, $this->FontSize, $link, substr_count($txt, chr(32)));
				}
			}
			// output cell
			if ($s) {
				// output cell
				$rs .= $s;
				// reset text stretching
				if ($stretch > 2) {
					//Reset character horizontal spacing
					$rs .= ' BT 0 Tc ET';
				} elseif ($stretch > 0) {
					//Reset character horizontal scaling
					$rs .= ' BT 100 Tz ET';
				}
			}
			// reset word spacing
			if (!(($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) AND ($align == 'J')) {
				$rs .= ' BT 0 Tw ET';
			}
			$this->lasth = $h;
			if ($ln > 0) {
				//Go to the beginning of the next line
				$this->y += $h;
				if ($ln == 1) {
					if ($this->rtl) {
						$this->x = $this->w - $this->rMargin;
					} else {
						$this->x = $this->lMargin;
					}
				}
			} else {
				// go left or right by case
				if ($this->rtl) {
					$this->x -= $w;
				} else {
					$this->x += $w;
				}
			}
			$gstyles = ''.$this->linestyleWidth.' '.$this->linestyleCap.' '.$this->linestyleJoin.' '.$this->linestyleDash.' '.$this->DrawColor.' '.$this->FillColor."\n";
			$rs = $gstyles.$rs;
			return $rs;
		}

		/**
		* This method allows printing text with line breaks. 
		* They can be automatic (as soon as the text reaches the right border of the cell) or explicit (via the \n character). As many cells as necessary are output, one below the other.<br />
		* Text can be aligned, centered or justified. The cell block can be framed and the background painted.
		* @param float $w Width of cells. If 0, they extend up to the right margin of the page.
		* @param float $h Cell minimum height. The cell extends automatically if needed.
		* @param string $txt String to print
		* @param mixed $border Indicates if borders must be drawn around the cell block. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align</li><li>C: center</li><li>R: right align</li><li>J: justification (default value when $ishtml=false)</li></ul>
		* @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		* @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right</li><li>1: to the beginning of the next line [DEFAULT]</li><li>2: below</li></ul>
		* @param float $x x position in user units
		* @param float $y y position in user units
		* @param boolean $reseth if true reset the last cell height (default true).
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @param boolean $ishtml set to true if $txt is HTML content (default = false).
		* @param boolean $autopadding if true, uses internal padding and automatically adjust it to account for line width.
		* @param float $maxh maximum height. It should be >= $h and less then remaining space to the bottom of the page, or 0 for disable this feature. This feature works only when $ishtml=false.
		* @return int Return the number of cells or 1 for html mode.
		* @access public
		* @since 1.3
		* @see SetFont(), SetDrawColor(), SetFillColor(), SetTextColor(), SetLineWidth(), Cell(), Write(), SetAutoPageBreak()
		*/
		public function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0) {	
			if ($this->empty_string($this->lasth) OR $reseth) {
				//set row height
				$this->lasth = $this->FontSize * $this->cell_height_ratio;
			}
			if (!$this->empty_string($y)) {
				$this->SetY($y);
			} else {
				$y = $this->GetY();
			}
			// check for page break
			$this->checkPageBreak($h);
			$y = $this->GetY();
			// get current page number
			$startpage = $this->page;
			if (!$this->empty_string($x)) {
				$this->SetX($x);
			} else {
				$x = $this->GetX();
			}
			if ($this->empty_string($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $this->x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $this->x;
				}
			}
			// store original margin values
			$lMargin = $this->lMargin;
			$rMargin = $this->rMargin;
			if ($this->rtl) {
				$this->SetRightMargin($this->w - $this->x);
				$this->SetLeftMargin($this->x - $w);
			} else {
				$this->SetLeftMargin($this->x);
				$this->SetRightMargin($this->w - $this->x - $w);
			}
			$starty = $this->y;
			if ($autopadding) {
				// Adjust internal padding
				if ($this->cMargin < ($this->LineWidth / 2)) {
					$this->cMargin = ($this->LineWidth / 2);
				}
				// Add top space if needed
				if (($this->lasth - $this->FontSize) < $this->LineWidth) {
					$this->y += $this->LineWidth / 2;
				}
				// add top padding
				$this->y += $this->cMargin;
			}
			if ($ishtml) {
				// ******* Write HTML text
				$this->writeHTML($txt, true, 0, $reseth, true, $align);
				$nl = 1;
			} else {
				// ******* Write text
				$nl = $this->Write($this->lasth, $txt, '', 0, $align, true, $stretch, false, false, $maxh);
			}
			if ($autopadding) {
				// add bottom padding
				$this->y += $this->cMargin;
				// Add bottom space if needed
				if (($this->lasth - $this->FontSize) < $this->LineWidth) {
					$this->y += $this->LineWidth / 2;
				}
			}
			// Get end-of-text Y position
			$currentY = $this->y;
			// get latest page number
			$endpage = $this->page;
			// check if a new page has been created
			if ($endpage > $startpage) {
				// design borders around HTML cells.
				for ($page=$startpage; $page <= $endpage; ++$page) {
					$this->setPage($page);
					if ($page == $startpage) {
						$this->y = $starty; // put cursor at the beginning of cell on the first page
						$h = $this->getPageHeight() - $starty - $this->getBreakMargin();
						$cborder = $this->getBorderMode($border, $position='start');
					} elseif ($page == $endpage) {
						$this->y = $this->tMargin; // put cursor at the beginning of last page
						$h = $currentY - $this->tMargin;
						$cborder = $this->getBorderMode($border, $position='end');
					} else {
						$this->y = $this->tMargin; // put cursor at the beginning of the current page
						$h = $this->getPageHeight() - $this->tMargin - $this->getBreakMargin();
						$cborder = $this->getBorderMode($border, $position='middle');
					}
					$nx = $x;
					// account for margin changes
					if ($page > $startpage) {
						if (($this->rtl) AND ($this->pagedim[$page]['orm'] != $this->pagedim[$startpage]['orm'])) {
							$nx = $x + ($this->pagedim[$page]['orm'] - $this->pagedim[$startpage]['orm']);
						} elseif ((!$this->rtl) AND ($this->pagedim[$page]['olm'] != $this->pagedim[$startpage]['olm'])) {
							$nx = $x + ($this->pagedim[$page]['olm'] - $this->pagedim[$startpage]['olm']);
						}
					}
					$this->SetX($nx);
					$ccode = $this->getCellCode($w, $h, '', $cborder, 1, '', $fill, '', 0, false);
					if ($cborder OR $fill) {
						$pagebuff = $this->getPageBuffer($this->page);
						$pstart = substr($pagebuff, 0, $this->intmrk[$this->page]);
						$pend = substr($pagebuff, $this->intmrk[$this->page]);
						$this->setPageBuffer($this->page, $pstart.$ccode."\n".$pend);
						$this->intmrk[$this->page] += strlen($ccode."\n");
					}
				}
			} else {
				$h = max($h, ($currentY - $y));
				// put cursor at the beginning of text
				$this->SetY($y); 
				$this->SetX($x);
				// design a cell around the text
				$ccode = $this->getCellCode($w, $h, '', $border, 1, '', $fill, '', 0, true);
				if ($border OR $fill) {
					if (end($this->transfmrk[$this->page]) !== false) {
						$pagemarkkey = key($this->transfmrk[$this->page]);
						$pagemark = &$this->transfmrk[$this->page][$pagemarkkey];
					} elseif ($this->InFooter) {
						$pagemark = &$this->footerpos[$this->page];
					} else {
						$pagemark = &$this->intmrk[$this->page];
					}
					$pagebuff = $this->getPageBuffer($this->page);
					$pstart = substr($pagebuff, 0, $pagemark);
					$pend = substr($pagebuff, $pagemark);
					$this->setPageBuffer($this->page, $pstart.$ccode."\n".$pend);
					$pagemark += strlen($ccode."\n");
				}
			}
			// Get end-of-cell Y position
			$currentY = $this->GetY();
			// restore original margin values
			$this->SetLeftMargin($lMargin);
			$this->SetRightMargin($rMargin);
			if ($ln > 0) {
				//Go to the beginning of the next line
				$this->SetY($currentY);
				if ($ln == 2) {
					$this->SetX($x + $w);
				}
			} else {
				// go left or right by case
				$this->setPage($startpage);
				$this->y = $y;
				$this->SetX($x + $w);
			}
			$this->setContentMark();
			return $nl;
		}

		/**
		* Get the border mode accounting for multicell position (opens bottom side of multicell crossing pages)
		* @param mixed $border Indicates if borders must be drawn around the cell block. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param string multicell position: 'start', 'middle', 'end'
		* @return border mode
		* @access protected
		* @since 4.4.002 (2008-12-09)
		*/
		protected function getBorderMode($border, $position='start') {
			if ((!$this->opencell) AND ($border == 1)) {
				return 1;
			}
			$cborder = '';
			switch ($position) {
				case 'start': {
					if ($border == 1) {
						$cborder = 'LTR';
					} else {
						if (!(false === strpos($border, 'L'))) {
							$cborder .= 'L';
						}
						if (!(false === strpos($border, 'T'))) {
							$cborder .= 'T';
						}
						if (!(false === strpos($border, 'R'))) {
							$cborder .= 'R';
						}
						if ((!$this->opencell) AND (!(false === strpos($border, 'B')))) {
							$cborder .= 'B';
						}
					}
					break;
				}
				case 'middle': {
					if ($border == 1) {
						$cborder = 'LR';
					} else {
						if (!(false === strpos($border, 'L'))) {
							$cborder .= 'L';
						}
						if ((!$this->opencell) AND (!(false === strpos($border, 'T')))) {
							$cborder .= 'T';
						}
						if (!(false === strpos($border, 'R'))) {
							$cborder .= 'R';
						}
						if ((!$this->opencell) AND (!(false === strpos($border, 'B')))) {
							$cborder .= 'B';
						}
					}
					break;
				}
				case 'end': {
					if ($border == 1) {
						$cborder = 'LRB';
					} else {
						if (!(false === strpos($border, 'L'))) {
							$cborder .= 'L';
						}
						if ((!$this->opencell) AND (!(false === strpos($border, 'T')))) {
							$cborder .= 'T';
						}
						if (!(false === strpos($border, 'R'))) {
							$cborder .= 'R';
						}
						if (!(false === strpos($border, 'B'))) {
							$cborder .= 'B';
						}
					}
					break;
				}
				default: {
					$cborder = $border;
					break;
				}
			}
			return $cborder;
		}

		/**
		* This method returns the estimated number of lines required to print the text.
		* @param string $txt text to print
		* @param float $w width of cell. If 0, they extend up to the right margin of the page.
		* @return int Return the estimated number of lines.
		* @access public
		* @since 4.5.011
		*/
		public function getNumLines($txt, $w=0) {
			$lines = 0;
			if ($this->empty_string($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $this->x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $this->x;
				}
			}
			// max column width
			$wmax = $w - (2 * $this->cMargin);
			// remove carriage returns
			$txt = str_replace("\r", '', $txt);
			// remove last newline (if any)
			if (substr($txt,-1) == "\n") {
				$txt = substr($txt, 0, -1);
			}
			// divide text in blocks
			$txtblocks = explode("\n", $txt);
			// for each block;
			foreach ($txtblocks as $block) {
				// estimate the number of lines
				$lines += $this->empty_string($block) ? 1 : (ceil($this->GetStringWidth($block) / $wmax));
			}
			return $lines;
		}
			
		/**
		* This method prints text from the current position.<br />
		* @param float $h Line height
		* @param string $txt String to print
		* @param mixed $link URL or identifier returned by AddLink()
		* @param int $fill Indicates if the background must be painted (1) or transparent (0). Default value: 0.
		* @param string $align Allows to center or align the text. Possible values are:<ul><li>L or empty string: left align (default value)</li><li>C: center</li><li>R: right align</li><li>J: justify</li></ul>
		* @param boolean $ln if true set cursor at the bottom of the line, otherwise set cursor at the top of the line.
		* @param int $stretch stretch carachter mode: <ul><li>0 = disabled</li><li>1 = horizontal scaling only if necessary</li><li>2 = forced horizontal scaling</li><li>3 = character spacing only if necessary</li><li>4 = forced character spacing</li></ul>
		* @param boolean $firstline if true prints only the first line and return the remaining string.
		* @param boolean $firstblock if true the string is the starting of a line.
		* @param float $maxh maximum height. The remaining unprinted text will be returned. It should be >= $h and less then remaining space to the bottom of the page, or 0 for disable this feature.
		* @return mixed Return the number of cells or the remaining string if $firstline = true.
		* @access public
		* @since 1.5
		*/
		public function Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0, $firstline=false, $firstblock=false, $maxh=0) {
			if (strlen($txt) == 0) {
				$txt = ' ';
			}
			// remove carriage returns
			$s = str_replace("\r", '', $txt);
			// check if string contains arabic text
			if (preg_match(K_RE_PATTERN_ARABIC, $s)) {
				$arabic = true;
			} else {
				$arabic = false;
			}
			// check if string contains RTL text
			if ($arabic OR $this->tmprtl OR preg_match(K_RE_PATTERN_RTL, $txt)) {
				$rtlmode = true;
			} else {
				$rtlmode = false;
			}
			// get a char width
			$chrwidth = $this->GetCharWidth('.');
			// get array of unicode values
			$chars = $this->UTF8StringToArray($s);
			// get array of chars
			$uchars = $this->UTF8ArrayToUniArray($chars);
			// get the number of characters
			$nb = count($chars);
			// replacement for SHY character (minus symbol)
			$shy_replacement = 45;
			$shy_replacement_char = $this->unichr($shy_replacement);
			// widht for SHY replacement
			$shy_replacement_width = $this->GetCharWidth($shy_replacement);
			// store current position
			$prevx = $this->x;
			$prevy = $this->y;
			// max Y
			$maxy = $this->y + $maxh - $h - (2 * $this->cMargin);
			// calculate remaining line width ($w)
			if ($this->rtl) {
				$w = $this->x - $this->lMargin;
			} else {
				$w = $this->w - $this->rMargin - $this->x;
			}
			// max column width
			$wmax = $w - (2 * $this->cMargin);
			if ((!$firstline) AND (($chrwidth > $wmax) OR ($this->GetCharWidth($chars[0]) > $wmax))) {
				// a single character do not fit on column
				return '';
			}
			$i = 0; // character position
			$j = 0; // current starting position
			$sep = -1; // position of the last blank space
			$shy = false; // true if the last blank is a soft hypen (SHY)
			$l = 0; // current string lenght
			$nl = 0; //number of lines
			$linebreak = false;
			// for each character
			while ($i < $nb) {
				if (($maxh > 0) AND ($this->y >= $maxy) ) {
					$firstline = true;
				}
				//Get the current character
				$c = $chars[$i];
				if ($c == 10) { // 10 = "\n" = new line
					//Explicit line break
					if ($align == 'J') {
						if ($this->rtl) {
							$talign = 'R';
						} else {
							$talign = 'L';
						}
					} else {
						$talign = $align;
					}
					$tmpstr = $this->UniArrSubString($uchars, $j, $i);
					if ($firstline) {
						$startx = $this->x;
						$tmparr = array_slice($chars, $j, $i);
						if ($rtlmode) {
							$tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
						}
						$linew = $this->GetArrStringWidth($tmparr);
						unset($tmparr);
						if ($this->rtl) {
							$this->endlinex = $startx - $linew;
						} else {
							$this->endlinex = $startx + $linew;
						}
						$w = $linew;
						$tmpcmargin = $this->cMargin;
						if ($maxh == 0) {
							$this->cMargin = 0;
						}
					}
					$this->Cell($w, $h, $tmpstr, 0, 1, $talign, $fill, $link, $stretch);
					unset($tmpstr);
					if ($firstline) {
						$this->cMargin = $tmpcmargin;
						return ($this->UniArrSubString($uchars, $i));
					}
					++$nl;
					$j = $i + 1;
					$l = 0;
					$sep = -1;
					$shy = false;
					// account for margin changes
					if ((($this->y + $this->lasth) > $this->PageBreakTrigger) AND (!$this->InFooter)) {
						// AcceptPageBreak() may be overriden on extended classed to include margin changes
						$this->AcceptPageBreak();
					}
					$w = $this->getRemainingWidth();
					$wmax = $w - (2 * $this->cMargin);
				} else {
					// 160 is the non-breaking space.
					// 173 is SHY (Soft Hypen).
					// \p{Z} or \p{Separator}: any kind of Unicode whitespace or invisible separator.
					// \p{Lo} or \p{Other_Letter}: a Unicode letter or ideograph that does not have lowercase and uppercase variants.
					// \p{Lo} is needed because Chinese characters are packed next to each other without spaces in between.
					if (($c != 160) AND (($c == 173) OR preg_match($this->re_spaces, $this->unichr($c)))) {
						// update last blank space position
						$sep = $i;
						// check if is a SHY
						if ($c == 173) {
							$shy = true;
						} else {
							$shy = false;
						}
					}
					// update string length
					if ((($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) AND ($arabic)) {
						// with bidirectional algorithm some chars may be changed affecting the line length
						// *** very slow ***
						$l = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, $i-$j+1), '', $this->tmprtl));
					} else {
						$l += $this->GetCharWidth($c);
					}
					if (($l > $wmax) OR ($shy AND (($l + $shy_replacement_width) > $wmax)) ) {
						// we have reached the end of column
						if ($sep == -1) {
							// check if the line was already started
							if (($this->rtl AND ($this->x <= ($this->w - $this->rMargin - $chrwidth)))
								OR ((!$this->rtl) AND ($this->x >= ($this->lMargin + $chrwidth)))) {
								// print a void cell and go to next line
								$this->Cell($w, $h, '', 0, 1);
								$linebreak = true;
								if ($firstline) {
									return ($this->UniArrSubString($uchars, $j));
								}
							} else {
								// truncate the word because do not fit on column
								$tmpstr = $this->UniArrSubString($uchars, $j, $i);
								if ($firstline) {
									$startx = $this->x;
									$tmparr = array_slice($chars, $j, $i);
									if ($rtlmode) {
										$tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
									}
									$linew = $this->GetArrStringWidth($tmparr);
									unset($tmparr);
									if ($this->rtl) {
										$this->endlinex = $startx - $linew;
									} else {
										$this->endlinex = $startx + $linew;
									}
									$w = $linew;
									$tmpcmargin = $this->cMargin;
									if ($maxh == 0) {
										$this->cMargin = 0;
									}
								}
								$this->Cell($w, $h, $tmpstr, 0, 1, $align, $fill, $link, $stretch);
								unset($tmpstr);
								if ($firstline) {
									$this->cMargin = $tmpcmargin;
									return ($this->UniArrSubString($uchars, $i));
								}
								$j = $i;
								--$i;
							}	
						} else {
							// word wrapping
							if ($this->rtl AND (!$firstblock)) {
								$endspace = 1;
							} else {
								$endspace = 0;
							}
							if ($shy) {
								// add hypen (minus symbol) at the end of the line
								$shy_width = $shy_replacement_width;
								if ($this->rtl) {
									$shy_char_left = $shy_replacement_char;
									$shy_char_right = '';
								} else {
									$shy_char_left = '';
									$shy_char_right = $shy_replacement_char;
								}
							} else {
								$shy_width = 0;
								$shy_char_left = '';
								$shy_char_right = '';
							}
							$tmpstr = $this->UniArrSubString($uchars, $j, ($sep + $endspace));
							if ($firstline) {
								$startx = $this->x;
								$tmparr = array_slice($chars, $j, ($sep + $endspace));
								if ($rtlmode) {
									$tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
								}
								$linew = $this->GetArrStringWidth($tmparr);
								unset($tmparr);
								if ($this->rtl) {
									$this->endlinex = $startx - $linew - $shy_width;
								} else {
									$this->endlinex = $startx + $linew + $shy_width;
								}
								$w = $linew;
								$tmpcmargin = $this->cMargin;
								if ($maxh == 0) {
									$this->cMargin = 0;
								}
							}
							// print the line
							$this->Cell($w, $h, $shy_char_left.$tmpstr.$shy_char_right, 0, 1, $align, $fill, $link, $stretch);
							unset($tmpstr);
							if ($firstline) {
								// return the remaining text
								$this->cMargin = $tmpcmargin;
								return ($this->UniArrSubString($uchars, ($sep + $endspace)));
							}
							$i = $sep;
							$sep = -1;
							$shy = false;
							$j = ($i+1);
						}
						// account for margin changes
						if ((($this->y + $this->lasth) > $this->PageBreakTrigger) AND (!$this->InFooter)) {
							// AcceptPageBreak() may be overriden on extended classed to include margin changes
							$this->AcceptPageBreak();
						}
						$w = $this->getRemainingWidth();
						$wmax = $w - (2 * $this->cMargin);
						if ($linebreak) {
							$linebreak = false;
						} else {
							++$nl;
							$l = 0;
						}
					}
				}
				++$i;
			} // end while i < nb
			// print last substring (if any)
			if ($l > 0) {
				switch ($align) {
					case 'J':
					case 'C': {
						$w = $w;
						break;
					}
					case 'L': {
						if ($this->rtl) {
							$w = $w;
						} else {
							$w = $l;
						}
						break;
					}
					case 'R': {
						if ($this->rtl) {
							$w = $l;
						} else {
							$w = $w;
						}
						break;
					}
					default: {
						$w = $l;
						break;
					}
				}
				$tmpstr = $this->UniArrSubString($uchars, $j, $nb);
				if ($firstline) {
					$startx = $this->x;
					$tmparr = array_slice($chars, $j, $nb);
					if ($rtlmode) {
						$tmparr = $this->utf8Bidi($tmparr, $tmpstr, $this->tmprtl);
					}
					$linew = $this->GetArrStringWidth($tmparr);
					unset($tmparr);
					if ($this->rtl) {
						$this->endlinex = $startx - $linew;
					} else {
						$this->endlinex = $startx + $linew;
					}
					$w = $linew;
					$tmpcmargin = $this->cMargin;
					if ($maxh == 0) {
						$this->cMargin = 0;
					}
				}
				$this->Cell($w, $h, $tmpstr, 0, $ln, $align, $fill, $link, $stretch);
				unset($tmpstr);
				if ($firstline) {
					$this->cMargin = $tmpcmargin;
					return ($this->UniArrSubString($uchars, $nb));
				}
				++$nl;
			}
			if ($firstline) {
				return '';
			}
			return $nl;
		}
				
		/**
		* Returns the remaining width between the current position and margins.
		* @return int Return the remaining width
		* @access protected
		*/
		protected function getRemainingWidth() {
			if ($this->rtl) {
				return ($this->x - $this->lMargin);
			} else {
				return ($this->w - $this->rMargin - $this->x);
			}
		}

	 	/**
		* Extract a slice of the $strarr array and return it as string.
		* @param string $strarr The input array of characters.
		* @param int $start the starting element of $strarr.
		* @param int $end first element that will not be returned.
		* @return Return part of a string
		* @access public
		*/
		public function UTF8ArrSubString($strarr, $start='', $end='') {
			if (strlen($start) == 0) {
				$start = 0;
			}
			if (strlen($end) == 0) {
				$end = count($strarr);
			}
			$string = '';
			for ($i=$start; $i < $end; ++$i) {
				$string .= $this->unichr($strarr[$i]);
			}
			return $string;
		}

	 	/**
		* Extract a slice of the $uniarr array and return it as string.
		* @param string $uniarr The input array of characters.
		* @param int $start the starting element of $strarr.
		* @param int $end first element that will not be returned.
		* @return Return part of a string
		* @access public
		* @since 4.5.037 (2009-04-07)
		*/
		public function UniArrSubString($uniarr, $start='', $end='') {
			if (strlen($start) == 0) {
				$start = 0;
			}
			if (strlen($end) == 0) {
				$end = count($uniarr);
			}
			$string = '';
			for ($i=$start; $i < $end; ++$i) {
				$string .= $uniarr[$i];
			}
			return $string;
		}

	 	/**
		* Convert an array of UTF8 values to array of unicode characters
		* @param string $ta The input array of UTF8 values.
		* @return Return array of unicode characters
		* @access public
		* @since 4.5.037 (2009-04-07)
		*/
		public function UTF8ArrayToUniArray($ta) {
			return array_map(array($this, 'unichr'), $ta);
		}
		
		/**
		* Returns the unicode caracter specified by UTF-8 code
		* @param int $c UTF-8 code
		* @return Returns the specified character.
		* @author Miguel Perez, Nicola Asuni
		* @access public
		* @since 2.3.000 (2008-03-05)
		*/
		public function unichr($c) {
			if (!$this->isunicode) {
				return chr($c);
			} elseif ($c <= 0x7F) {
				// one byte
				return chr($c);
			} elseif ($c <= 0x7FF) {
				// two bytes
				return chr(0xC0 | $c >> 6).chr(0x80 | $c & 0x3F);
			} elseif ($c <= 0xFFFF) {
				// three bytes
				return chr(0xE0 | $c >> 12).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
			} elseif ($c <= 0x10FFFF) {
				// four bytes
				return chr(0xF0 | $c >> 18).chr(0x80 | $c >> 12 & 0x3F).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
			} else {
				return '';
			}
		}
		
		/**
		* Puts an image in the page. 
		* The upper-left corner must be given. 
		* The dimensions can be specified in different ways:<ul>
		* <li>explicit width and height (expressed in user unit)</li>
		* <li>one explicit dimension, the other being calculated automatically in order to keep the original proportions</li>
		* <li>no explicit dimension, in which case the image is put at 72 dpi</li></ul>
		* Supported formats are JPEG and PNG images whitout GD library and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;
		* The format can be specified explicitly or inferred from the file extension.<br />
		* It is possible to put a link on the image.<br />
		* Remark: if an image is used several times, only one copy will be embedded in the file.<br />
		* @param string $file Name of the file containing the image.
		* @param float $x Abscissa of the upper-left corner.
		* @param float $y Ordinate of the upper-left corner.
		* @param float $w Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param float $h Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param string $type Image format. Possible values are (case insensitive): JPEG and PNG (whitout GD library) and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;. If not specified, the type is inferred from the file extension.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param string $align Indicates the alignment of the pointer next to image insertion relative to image height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		* @param boolean $resize If true resize (reduce) the image to fit $w and $h (requires GD library).
		* @param int $dpi dot-per-inch resolution used on resize
		* @param string $palign Allows to center or align the image on the current line. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		* @param boolean $ismask true if this image is a mask, false otherwise
		* @param mixed $imgmask image object returned by this function or false
		* @param mixed $border Indicates if borders must be drawn around the image. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @param boolean $fitbox If true scale image dimensions proportionally to fit within the ($w, $h) box.
		* @param boolean $hidden if true do not display the image.
		* @return image information
		* @access public
		* @since 1.1
		*/
		public function Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			// get image dimensions
			$imsize = @getimagesize($file);
			if ($imsize === FALSE) {
				// encode spaces on filename
				$file = str_replace(' ', '%20', $file);
				$imsize = @getimagesize($file);
				if ($imsize === FALSE) {
					$this->Error('[Image] No such file or directory in '.$file);
				}
			}
			// get original image width and height in pixels
			list($pixw, $pixh) = $imsize;
			// calculate image width and height on document
			if (($w <= 0) AND ($h <= 0)) {
				// convert image size to document unit
				$w = $this->pixelsToUnits($pixw);
				$h = $this->pixelsToUnits($pixh);
			} elseif ($w <= 0) {
				$w = $h * $pixw / $pixh;
			} elseif ($h <= 0) {
				$h = $w * $pixh / $pixw;
			} elseif ($fitbox AND ($w > 0) AND ($h > 0)) {
				// scale image dimensions proportionally to fit within the ($w, $h) box
				if ((($w * $pixh) / ($h * $pixw)) < 1) {
					$h = $w * $pixh / $pixw;
				} else {
					$w = $h * $pixw / $pixh;
				}
			}
			// calculate new minimum dimensions in pixels
			$neww = round($w * $this->k * $dpi / $this->dpi);
			$newh = round($h * $this->k * $dpi / $this->dpi);
			// check if resize is necessary (resize is used only to reduce the image)
			if (($neww * $newh) >= ($pixw * $pixh)) {
				$resize = false;
			}
			// check if image has been already added on document
			if (!in_array($file, $this->imagekeys)) {
				//First use of image, get info
				if ($type == '') {
					$fileinfo = pathinfo($file);
					if (isset($fileinfo['extension']) AND (!$this->empty_string($fileinfo['extension']))) {
						$type = $fileinfo['extension'];
					} else {
						$this->Error('Image file has no extension and no type was specified: '.$file);
					}
				}
				$type = strtolower($type);
				if ($type == 'jpg') {
					$type = 'jpeg';
				}
				$mqr = $this->get_mqr();
				$this->set_mqr(false);
				// Specific image handlers
				$mtd = '_parse'.$type;
				// GD image handler function
				$gdfunction = 'imagecreatefrom'.$type;
				$info = false;
				if ((method_exists($this, $mtd)) AND (!($resize AND function_exists($gdfunction)))) {
					// TCPDF image functions
					$info = $this->$mtd($file);
					if ($info == 'pngalpha') {
						return $this->ImagePngAlpha($file, $x, $y, $w, $h, 'PNG', $link, $align, $resize, $dpi, $palign);
					}
				} 
				if (!$info) {
					if (function_exists($gdfunction)) {
						// GD library
						$img = $gdfunction($file);
						if ($resize) {
							$imgr = imagecreatetruecolor($neww, $newh);
							imagecopyresampled($imgr, $img, 0, 0, 0, 0, $neww, $newh, $pixw, $pixh); 
							$info = $this->_toJPEG($imgr);
						} else {
							$info = $this->_toJPEG($img);
						}
					} elseif (extension_loaded('imagick')) {
						// ImageMagick library
						$img = new Imagick();
						$img->readImage($file);
						if ($resize) {
							$img->resizeImage($neww, $newh, 10, 1, false);
						}
						$img->setCompressionQuality($this->jpeg_quality);
						$img->setImageFormat('jpeg');
						$tempname = tempnam(K_PATH_CACHE, 'jpg_');
						$img->writeImage($tempname);
						$info = $this->_parsejpeg($tempname);
						unlink($tempname);
						$img->destroy();
					} else {
						return;
					}
				}
				if ($info === false) {
					//If false, we cannot process image
					return;
				}
				$this->set_mqr($mqr);
				if ($ismask) {
					// force grayscale
					$info['cs'] = 'DeviceGray';
				}
				$info['i'] = $this->numimages + 1;
				if ($imgmask !== false) {
					$info['masked'] = $imgmask;
				}
				// add image to document
				$this->setImageBuffer($file, $info);
			} else {
				$info = $this->getImageBuffer($file);
			}
			// Check whether we need a new page first as this does not fit
			if ($this->checkPageBreak($h, $y)) {
				$y = $this->GetY() + $this->cMargin;
			}
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			// set alignment
			if ($this->rtl) {
				if ($palign == 'L') {
					$ximg = $this->lMargin;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} elseif ($palign == 'C') {
					$ximg = ($this->w - $x - $w) / 2;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} else {
					$ximg = $this->w - $x - $w;
					// set left side coordinate
					$this->img_rb_x = $ximg;
				}
			} else {
				if ($palign == 'R') {
					$ximg = $this->w - $this->rMargin - $w;
					// set left side coordinate
					$this->img_rb_x = $ximg;
				} elseif ($palign == 'C') {
					$ximg = ($this->w - $x - $w) / 2;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} else {
					$ximg = $x;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				}
			}
			if ($ismask OR $hidden) {
				// image is not displayed
				return $info['i'];
			}
			$xkimg = $ximg * $this->k;
			$this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', ($w * $this->k), ($h * $this->k), $xkimg, (($this->h - ($y + $h)) * $this->k), $info['i']));
			if (!empty($border)) {
				$bx = $x;
				$by = $y;
				$this->x = $ximg;
				$this->y = $y;
				$this->Cell($w, $h, '', $border, 0, '', 0, '', 0);
				$this->x = $bx;
				$this->y = $by;
			}
			if ($link) {
				$this->Link($ximg, $y, $w, $h, $link, 0);
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T': {
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M': {
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B': {
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N': {
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
			$this->endlinex = $this->img_rb_x;
			return $info['i'];
		}
		
		/**
		 * Sets the current active configuration setting of magic_quotes_runtime (if the set_magic_quotes_runtime function exist)
		 * @param boolean $mqr FALSE for off, TRUE for on.
		 * @since 4.6.025 (2009-08-17)
		 */
		public function set_mqr($mqr) {
			if(!defined('PHP_VERSION_ID')) {
				$version = PHP_VERSION;
				define('PHP_VERSION_ID', (($version{0} * 10000) + ($version{2} * 100) + $version{4}));
			}
			if (PHP_VERSION_ID < 50300) {
				@set_magic_quotes_runtime($mqr);
			}
		}
		
		/**
		 * Gets the current active configuration setting of magic_quotes_runtime (if the get_magic_quotes_runtime function exist)
		 * @return Returns 0 if magic quotes runtime is off or get_magic_quotes_runtime doesn't exist, 1 otherwise. 
		 * @since 4.6.025 (2009-08-17)
		 */
		public function get_mqr() {
			if(!defined('PHP_VERSION_ID')) {
				$version = PHP_VERSION;
				define('PHP_VERSION_ID', (($version{0} * 10000) + ($version{2} * 100) + $version{4}));
			}
			if (PHP_VERSION_ID < 50300) {
				return @get_magic_quotes_runtime();
			}
			return 0;
		}
						
		/**
		* Convert the loaded php image to a JPEG and then return a structure for the PDF creator.
		* This function requires GD library and write access to the directory defined on K_PATH_CACHE constant.
		* @param string $file Image file name.
		* @param image $image Image object.
		* return image JPEG image object.
		* @access protected
		*/
		protected function _toJPEG($image) {
			$tempname = tempnam(K_PATH_CACHE, 'jpg_');
			imagejpeg($image, $tempname, $this->jpeg_quality);
			imagedestroy($image);
			$retvars = $this->_parsejpeg($tempname);
			// tidy up by removing temporary image
			unlink($tempname);
			return $retvars;
		}
		
		/**
		* Extract info from a JPEG file without using the GD library.
		* @param string $file image file to parse
		* @return array structure containing the image data
		* @access protected
		*/
		protected function _parsejpeg($file) {
			$a = getimagesize($file);
			if (empty($a)) {
				$this->Error('Missing or incorrect image file: '.$file);
			}
			if ($a[2] != 2) {
				$this->Error('Not a JPEG file: '.$file);
			}
			if ((!isset($a['channels'])) OR ($a['channels'] == 3)) {
				$colspace = 'DeviceRGB';
			} elseif ($a['channels'] == 4) {
				$colspace = 'DeviceCMYK';
			} else {
				$colspace = 'DeviceGray';
			}
			$bpc = isset($a['bits']) ? $a['bits'] : 8;
			$data = file_get_contents($file);
			return array('w' => $a[0], 'h' => $a[1], 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'DCTDecode', 'data' => $data);
		}

		/**
		* Extract info from a PNG file without using the GD library.
		* @param string $file image file to parse
		* @return array structure containing the image data
		* @access protected
		*/
		protected function _parsepng($file) {
			$f = fopen($file, 'rb');
			if ($f === false) {
				$this->Error('Can\'t open image file: '.$file);
			}
			//Check signature
			if (fread($f, 8) != chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
				$this->Error('Not a PNG file: '.$file);
			}
			//Read header chunk
			fread($f, 4);
			if (fread($f, 4) != 'IHDR') {
				$this->Error('Incorrect PNG file: '.$file);
			}
			$w = $this->_freadint($f);
			$h = $this->_freadint($f);
			$bpc = ord(fread($f, 1));
			if ($bpc > 8) {
				//$this->Error('16-bit depth not supported: '.$file);
				fclose($f);
				return false;
			}
			$ct = ord(fread($f, 1));
			if ($ct == 0) {
				$colspace = 'DeviceGray';
			} elseif ($ct == 2) {
				$colspace = 'DeviceRGB';
			} elseif ($ct == 3) {
				$colspace = 'Indexed';
			} else {
				// alpha channel
				fclose($f);
				return 'pngalpha';
			}
			if (ord(fread($f, 1)) != 0) {
				//$this->Error('Unknown compression method: '.$file);
				fclose($f);
				return false;
			}
			if (ord(fread($f, 1)) != 0) {
				//$this->Error('Unknown filter method: '.$file);
				fclose($f);
				return false;
			}
			if (ord(fread($f, 1)) != 0) {
				//$this->Error('Interlacing not supported: '.$file);
				fclose($f);
				return false;
			}
			fread($f, 4);
			$parms = '/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
			//Scan chunks looking for palette, transparency and image data
			$pal = '';
			$trns = '';
			$data = '';
			do {
				$n = $this->_freadint($f);
				$type = fread($f, 4);
				if ($type == 'PLTE') {
					//Read palette
					$pal = $this->rfread($f, $n);
					fread($f, 4);
				} elseif ($type == 'tRNS') {
					//Read transparency info
					$t = $this->rfread($f, $n);
					if ($ct == 0) {
						$trns = array(ord(substr($t, 1, 1)));
					} elseif ($ct == 2) {
						$trns = array(ord(substr($t, 1, 1)), ord(substr($t, 3, 1)), ord(substr($t, 5, 1)));
					} else {
						$pos = strpos($t, chr(0));
						if ($pos !== false) {
							$trns = array($pos);
						}
					}
					fread($f, 4);
				} elseif ($type == 'IDAT') {
					//Read image data block
					$data .= $this->rfread($f, $n);
					fread($f, 4);
				} elseif ($type == 'IEND') {
					break;
				} else {
					$this->rfread($f, $n + 4);
				}
			} while ($n);
			if (($colspace == 'Indexed') AND (empty($pal))) {
				//$this->Error('Missing palette in '.$file);
				fclose($f);
				return false;
			}
			fclose($f);
			return array('w' => $w, 'h' => $h, 'cs' => $colspace, 'bpc' => $bpc, 'f' => 'FlateDecode', 'parms' => $parms, 'pal' => $pal, 'trns' => $trns, 'data' => $data);
		}

		/**
		* Binary-safe and URL-safe file read.
		* Reads up to length  bytes from the file pointer referenced by handle. Reading stops as soon as one of the following conditions is met: length bytes have been read; EOF (end of file) is reached.
		* @param resource $handle
		* @param int $length
		* @return Returns the read string or FALSE in case of error.
		* @author Nicola Asuni
		* @access protected
		* @since 4.5.027 (2009-03-16)
		*/
		protected function rfread($handle, $length) {
			$data = fread($handle, $length);
			if ($data === false) {
				return false;
			}
			$rest = $length - strlen($data);
			if ($rest > 0) {
				$data .= $this->rfread($handle, $rest);
			}
			return $data;
		}

		/**
		* Extract info from a PNG image with alpha channel using the GD library.
		* @param string $file Name of the file containing the image.
		* @param float $x Abscissa of the upper-left corner.
		* @param float $y Ordinate of the upper-left corner.
		* @param float $w Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param float $h Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param string $type Image format. Possible values are (case insensitive): JPEG and PNG (whitout GD library) and all images supported by GD: GD, GD2, GD2PART, GIF, JPEG, PNG, BMP, XBM, XPM;. If not specified, the type is inferred from the file extension.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param string $align Indicates the alignment of the pointer next to image insertion relative to image height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		* @param boolean $resize If true resize (reduce) the image to fit $w and $h (requires GD library).
		* @param int $dpi dot-per-inch resolution used on resize
		* @param string $palign Allows to center or align the image on the current line. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		* @author Valentin Schmidt, Nicola Asuni
		* @access protected
		* @since 4.3.007 (2008-12-04)
		* @see Image()
		*/
		protected function ImagePngAlpha($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='') {
			// get image size
			list($wpx, $hpx) = getimagesize($file);
			// generate images
			$img = imagecreatefrompng($file);
			$imgalpha = imagecreate($wpx, $hpx);
			// generate gray scale pallete
			for ($c = 0; $c < 256; ++$c) {
				ImageColorAllocate($imgalpha, $c, $c, $c);
			}
			// extract alpha channel
			for ($xpx = 0; $xpx < $wpx; ++$xpx) {
				for ($ypx = 0; $ypx < $hpx; ++$ypx) {
					$colorindex = imagecolorat($img, $xpx, $ypx);
					$col = imagecolorsforindex($img, $colorindex);
					imagesetpixel($imgalpha, $xpx, $ypx, $this->getGDgamma((127 - $col['alpha']) * 255 / 127));
				}
			}
			// create temp alpha file
			$tempfile_alpha = tempnam(K_PATH_CACHE, 'mska_');
			imagepng($imgalpha, $tempfile_alpha);
			imagedestroy($imgalpha);
			// extract image without alpha channel
			$imgplain = imagecreatetruecolor($wpx, $hpx);
			imagecopy($imgplain, $img, 0, 0, 0, 0, $wpx, $hpx);
			// create temp image file
			$tempfile_plain = tempnam(K_PATH_CACHE, 'mskp_');
			imagepng($imgplain, $tempfile_plain);
			imagedestroy($imgplain);
			// embed mask image
			$imgmask = $this->Image($tempfile_alpha, $x, $y, $w, $h, 'PNG', '', '', $resize, $dpi, '', true, false);
			// embed image, masked with previously embedded mask
			$this->Image($tempfile_plain, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, false, $imgmask);
			// remove temp files
			unlink($tempfile_alpha);
			unlink($tempfile_plain);
		}

		/**
		* Correct the gamma value to be used with GD library
		* @param float $v the gamma value to be corrected
		* @access protected
		* @since 4.3.007 (2008-12-04)
		*/
		protected function getGDgamma($v) {
			return (pow(($v / 255), 2.2) * 255);
		} 
		
		/**
		* Performs a line break. 
		* The current abscissa goes back to the left margin and the ordinate increases by the amount passed in parameter.
		* @param float $h The height of the break. By default, the value equals the height of the last printed cell.
		* @param boolean $cell if true add a cMargin to the x coordinate
		* @access public
		* @since 1.0
		* @see Cell()
		*/
		public function Ln($h='', $cell=false) {
			//Line feed; default value is last cell height
			if ($cell) {
				$cellmargin = $this->cMargin;
			} else {
				$cellmargin = 0;
			}
			if ($this->rtl) {
				$this->x = $this->w - $this->rMargin - $cellmargin;
			} else {
				$this->x = $this->lMargin + $cellmargin;
			}
			if (is_string($h)) {
				$this->y += $this->lasth;
			} else {
				$this->y += $h;
			}
			$this->newline = true;
		}

		/**
		* Returns the relative X value of current position.
		* The value is relative to the left border for LTR languages and to the right border for RTL languages.
		* @return float
		* @access public
		* @since 1.2
		* @see SetX(), GetY(), SetY()
		*/
		public function GetX() {
			//Get x position
			if ($this->rtl) {
				return ($this->w - $this->x);
			} else {
				return $this->x;
			}
		}
		
		/**
		* Returns the absolute X value of current position.
		* @return float
		* @access public
		* @since 1.2
		* @see SetX(), GetY(), SetY()
		*/
		public function GetAbsX() {
			return $this->x;
		}
		
		/**
		* Returns the ordinate of the current position.
		* @return float
		* @access public
		* @since 1.0
		* @see SetY(), GetX(), SetX()
		*/
		public function GetY() {
			//Get y position
			return $this->y;
		}
		
		/**
		* Defines the abscissa of the current position. 
		* If the passed value is negative, it is relative to the right of the page (or left if language is RTL).
		* @param float $x The value of the abscissa.
		* @access public
		* @since 1.2
		* @see GetX(), GetY(), SetY(), SetXY()
		*/
		public function SetX($x) {
			//Set x position
			if ($this->rtl) {
				if ($x >= 0) {
					$this->x = $this->w - $x;
				} else {
					$this->x = abs($x);
				}
			} else {
				if ($x >= 0) {
					$this->x = $x;
				} else {
					$this->x = $this->w + $x;
				}
			}
			if ($this->x < 0) {
				$this->x = 0;
			}
			if ($this->x > $this->w) {
				$this->x = $this->w;
			}
		}
		
		/**
		* Moves the current abscissa back to the left margin and sets the ordinate.
		* If the passed value is negative, it is relative to the bottom of the page.
		* @param float $y The value of the ordinate.
		* @param bool $resetx if true (default) reset the X position.
		* @access public
		* @since 1.0
		* @see GetX(), GetY(), SetY(), SetXY()
		*/
		public function SetY($y, $resetx=true) {
			if ($resetx) {
				//reset x
				if ($this->rtl) {
					$this->x = $this->w - $this->rMargin;
				} else {
					$this->x = $this->lMargin;
				}
			}
			if ($y >= 0) {
				$this->y = $y;
			} else {
				$this->y = $this->h + $y;
			}
			if ($this->y < 0) {
				$this->y = 0;
			}
			if ($this->y > $this->h) {
				$this->y = $this->h;
			}
		}
		
		/**
		* Defines the abscissa and ordinate of the current position. 
		* If the passed values are negative, they are relative respectively to the right and bottom of the page.
		* @param float $x The value of the abscissa
		* @param float $y The value of the ordinate
		* @access public
		* @since 1.2
		* @see SetX(), SetY()
		*/
		public function SetXY($x, $y) {
			//Set x and y positions
			$this->SetY($y);
			$this->SetX($x);
		}

		/**
		* Send the document to a given destination: string, local file or browser. 
		* In the last case, the plug-in may be used (if present) or a download ("Save as" dialog box) may be forced.<br />
		* The method first calls Close() if necessary to terminate the document.
		* @param string $name The name of the file when saved. Note that special characters are removed and blanks characters are replaced with the underscore character.
		* @param string $dest Destination where to send the document. It can take one of the following values:<ul><li>I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.</li><li>D: send to the browser and force a file download with the name given by name.</li><li>F: save to a local file with the name given by name.</li><li>S: return the document as a string. name is ignored.</li></ul>
		* @access public
		* @since 1.0
		* @see Close()
		*/
		public function Output($name='doc.pdf', $dest='I') {
			//Output PDF to some destination
			//Finish document if necessary
			if ($this->state < 3) {
				$this->Close();
			}
			//Normalize parameters
			if (is_bool($dest)) {
				$dest = $dest ? 'D' : 'F';
			}
			$dest = strtoupper($dest);
			if ($dest != 'F') {
				$name = preg_replace('/[\s]+/', '_', $name);
				$name = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $name);
			}
			if ($this->sign) {
				// *** apply digital signature to the document ***
				// get the document content
				$pdfdoc = $this->getBuffer();
				// remove last newline
				$pdfdoc = substr($pdfdoc, 0, -1);
				// Remove the original buffer
				if (isset($this->diskcache) AND $this->diskcache) {
					// remove buffer file from cache
					unlink($this->buffer);
				}
				unset($this->buffer);
				// remove filler space
				$byterange_string_len = strlen($this->byterange_string);
				// define the ByteRange
				$byte_range = array();
				$byte_range[0] = 0;
				$byte_range[1] = strpos($pdfdoc, $this->byterange_string) + $byterange_string_len + 10;
				$byte_range[2] = $byte_range[1] + $this->signature_max_lenght + 2;
				$byte_range[3] = strlen($pdfdoc) - $byte_range[2];
				$pdfdoc = substr($pdfdoc, 0, $byte_range[1]).substr($pdfdoc, $byte_range[2]);
				// replace the ByteRange
				$byterange = sprintf('/ByteRange[0 %u %u %u]', $byte_range[1], $byte_range[2], $byte_range[3]);
				$byterange .= str_repeat(' ', ($byterange_string_len - strlen($byterange)));
				$pdfdoc = str_replace($this->byterange_string, $byterange, $pdfdoc);
				// write the document to a temporary folder
				$tempdoc = tempnam(K_PATH_CACHE, 'tmppdf_');
				$f = fopen($tempdoc, 'wb');
				if (!$f) {
					$this->Error('Unable to create temporary file: '.$tempdoc);
				}
				$pdfdoc_lenght = strlen($pdfdoc);
				fwrite($f, $pdfdoc, $pdfdoc_lenght);
				fclose($f);
				// get digital signature via openssl library
				$tempsign = tempnam(K_PATH_CACHE, 'tmpsig_');
				if (empty($this->signature_data['extracerts'])) {
					openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array($this->signature_data['privkey'], $this->signature_data['password']), array(), PKCS7_BINARY | PKCS7_DETACHED);
				} else {
					openssl_pkcs7_sign($tempdoc, $tempsign, $this->signature_data['signcert'], array($this->signature_data['privkey'], $this->signature_data['password']), array(), PKCS7_BINARY | PKCS7_DETACHED, $this->signature_data['extracerts']);
				}	
				unlink($tempdoc);
				// read signature
				$signature = file_get_contents($tempsign, false, null, $pdfdoc_lenght);
				unlink($tempsign);
				// extract signature
				$signature = substr($signature, (strpos($signature, "%%EOF\n\n------") + 13));
				$tmparr = explode("\n\n", $signature);
				$signature = $tmparr[1];
				unset($tmparr);
				// decode signature
				$signature = base64_decode(trim($signature));
				// convert signature to hex
				$signature = current(unpack('H*', $signature));
				$signature = str_pad($signature, $this->signature_max_lenght, '0');
				// Add signature to the document
				$pdfdoc = substr($pdfdoc, 0, $byte_range[1]).'<'.$signature.'>'.substr($pdfdoc, ($byte_range[1]));
				$this->diskcache = false;
				$this->buffer = &$pdfdoc;
				$this->bufferlen = strlen($pdfdoc);
			}
			switch($dest) {
				case 'I': {
					// Send PDF to the standard output
					if (ob_get_contents()) {
						$this->Error('Some data has already been output, can\'t send PDF file');
					}
					if (php_sapi_name() != 'cli') {
						//We send to a browser
						header('Content-Type: application/pdf');
						if (headers_sent()) {
							$this->Error('Some data has already been output to browser, can\'t send PDF file');
						}
						header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
						header('Pragma: public');
						header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
						header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');	
						header('Content-Length: '.$this->bufferlen);
						header('Content-Disposition: inline; filename="'.basename($name).'";');
					}
					echo $this->getBuffer();
					break;
				}
				case 'D': {
					// Download PDF as file
					if (ob_get_contents()) {
						$this->Error('Some data has already been output, can\'t send PDF file');
					}
					header('Content-Description: File Transfer');
					if (headers_sent()) {
						$this->Error('Some data has already been output to browser, can\'t send PDF file');
					}
					header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
					header('Pragma: public');
					header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
					header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
					// force download dialog
					header('Content-Type: application/force-download');
					header('Content-Type: application/octet-stream', false);
					header('Content-Type: application/download', false);
					header('Content-Type: application/pdf', false);
					// use the Content-Disposition header to supply a recommended filename
					header('Content-Disposition: attachment; filename="'.basename($name).'";');
					header('Content-Transfer-Encoding: binary');
					header('Content-Length: '.$this->bufferlen);
					echo $this->getBuffer();
					break;
				}
				case 'F': {
					// Save PDF to a local file
					if ($this->diskcache) {
						copy($this->buffer, $name);
					} else {
						$f = fopen($name, 'wb');
						if (!$f) {
							$this->Error('Unable to create output file: '.$name);
						}
						fwrite($f, $this->getBuffer(), $this->bufferlen);
						fclose($f);
					}
					break;
				}
				case 'S': {
					// Returns PDF as a string
					return $this->getBuffer();
				}
				default: {
					$this->Error('Incorrect output destination: '.$dest);
				}
			}
			return '';
		}

		/**
		 * Unset all class variables except the following critical variables: internal_encoding, state, bufferlen, buffer and diskcache.
		 * @param boolean $destroyall if true destroys all class variables, otherwise preserves critical variables.
		 * @param boolean $preserve_objcopy if true preserves the objcopy variable
		 * @access public
		 * @since 4.5.016 (2009-02-24)
		 */
		public function _destroy($destroyall=false, $preserve_objcopy=false) {
			if ($destroyall AND isset($this->diskcache) AND $this->diskcache AND (!$preserve_objcopy) AND (!$this->empty_string($this->buffer))) {
				// remove buffer file from cache
				unlink($this->buffer);
			}
			foreach (array_keys(get_object_vars($this)) as $val) {
				if ($destroyall OR (
					($val != 'internal_encoding') 
					AND ($val != 'state') 
					AND ($val != 'bufferlen') 
					AND ($val != 'buffer') 
					AND ($val != 'diskcache')
					AND ($val != 'sign')
					AND ($val != 'signature_data')
					AND ($val != 'signature_max_lenght')
					AND ($val != 'byterange_string')
					)) {
					if (!$preserve_objcopy OR ($val != 'objcopy')) {
						unset($this->$val);
					}
				}
			}
		}
		
		/**
		* Check for locale-related bug
		* @access protected
		*/
		protected function _dochecks() {
			//Check for locale-related bug
			if (1.1 == 1) {
				$this->Error('Don\'t alter the locale before including class file');
			}
			//Check for decimal separator
			if (sprintf('%.1F', 1.0) != '1.0') {
				setlocale(LC_NUMERIC, 'C');
			}
		}

		/**
		* Return fonts path
		* @return string
		* @access protected
		*/
		protected function _getfontpath() {
			if (!defined('K_PATH_FONTS') AND is_dir(dirname(__FILE__).'/fonts')) {
				define('K_PATH_FONTS', dirname(__FILE__).'/fonts/');
			}
			return defined('K_PATH_FONTS') ? K_PATH_FONTS : '';
		}
		
		/**
		* Output pages.
		* @access protected
		*/
		protected function _putpages() {
			$nb = $this->numpages;
			if (!empty($this->AliasNbPages)) {
				$nbs = $this->formatPageNumber($nb);
				$nbu = $this->UTF8ToUTF16BE($nbs, false); // replacement for unicode font
				$alias_a = $this->_escape($this->AliasNbPages);
				$alias_au = $this->_escape('{'.$this->AliasNbPages.'}');
				if ($this->isunicode) {
					$alias_b = $this->_escape($this->UTF8ToLatin1($this->AliasNbPages));
					$alias_bu = $this->_escape($this->UTF8ToLatin1('{'.$this->AliasNbPages.'}'));
					$alias_c = $this->_escape($this->utf8StrRev($this->AliasNbPages, false, $this->tmprtl));
					$alias_cu = $this->_escape($this->utf8StrRev('{'.$this->AliasNbPages.'}', false, $this->tmprtl));
				}
			}
			if (!empty($this->AliasNumPage)) {
				$alias_pa = $this->_escape($this->AliasNumPage);
				$alias_pau = $this->_escape('{'.$this->AliasNumPage.'}');
				if ($this->isunicode) {
					$alias_pb = $this->_escape($this->UTF8ToLatin1($this->AliasNumPage));
					$alias_pbu = $this->_escape($this->UTF8ToLatin1('{'.$this->AliasNumPage.'}'));
					$alias_pc = $this->_escape($this->utf8StrRev($this->AliasNumPage, false, $this->tmprtl));
					$alias_pcu = $this->_escape($this->utf8StrRev('{'.$this->AliasNumPage.'}', false, $this->tmprtl));
				}
			}
			$pagegroupnum = 0;
			$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
			for ($n=1; $n <= $nb; ++$n) {
				$temppage = $this->getPageBuffer($n);
				if (!empty($this->pagegroups)) {
					if(isset($this->newpagegroup[$n])) {
						$pagegroupnum = 0;
					}
					++$pagegroupnum;
					foreach ($this->pagegroups as $k => $v) {
						// replace total pages group numbers
						$vs = $this->formatPageNumber($v);
						$vu = $this->UTF8ToUTF16BE($vs, false);
						$alias_ga = $this->_escape($k);
						$alias_gau = $this->_escape('{'.$k.'}');
						if ($this->isunicode) {
							$alias_gb = $this->_escape($this->UTF8ToLatin1($k));
							$alias_gbu = $this->_escape($this->UTF8ToLatin1('{'.$k.'}'));
							$alias_gc = $this->_escape($this->utf8StrRev($k, false, $this->tmprtl));
							$alias_gcu = $this->_escape($this->utf8StrRev('{'.$k.'}', false, $this->tmprtl));
						}
						$temppage = str_replace($alias_gau, $vu, $temppage);
						if ($this->isunicode) {
							$temppage = str_replace($alias_gbu, $vu, $temppage);
							$temppage = str_replace($alias_gcu, $vu, $temppage);
							$temppage = str_replace($alias_gb, $vs, $temppage);
							$temppage = str_replace($alias_gc, $vs, $temppage);
						}
						$temppage = str_replace($alias_ga, $vs, $temppage);
						// replace page group numbers
						$pvs = $this->formatPageNumber($pagegroupnum);
						$pvu = $this->UTF8ToUTF16BE($pvs, false);
						$pk = str_replace('{nb', '{pnb', $k);
						$alias_pga = $this->_escape($pk);
						$alias_pgau = $this->_escape('{'.$pk.'}');
						if ($this->isunicode) {
							$alias_pgb = $this->_escape($this->UTF8ToLatin1($pk));
							$alias_pgbu = $this->_escape($this->UTF8ToLatin1('{'.$pk.'}'));
							$alias_pgc = $this->_escape($this->utf8StrRev($pk, false, $this->tmprtl));
							$alias_pgcu = $this->_escape($this->utf8StrRev('{'.$pk.'}', false, $this->tmprtl));
						}
						$temppage = str_replace($alias_pgau, $pvu, $temppage);
						if ($this->isunicode) {
							$temppage = str_replace($alias_pgbu, $pvu, $temppage);
							$temppage = str_replace($alias_pgcu, $pvu, $temppage);
							$temppage = str_replace($alias_pgb, $pvs, $temppage);
							$temppage = str_replace($alias_pgc, $pvs, $temppage);
						}
						$temppage = str_replace($alias_pga, $pvs, $temppage);
					}
				}
				if (!empty($this->AliasNbPages)) {
					// replace total pages number
					$temppage = str_replace($alias_au, $nbu, $temppage);
					if ($this->isunicode) {
						$temppage = str_replace($alias_bu, $nbu, $temppage);
						$temppage = str_replace($alias_cu, $nbu, $temppage);
						$temppage = str_replace($alias_b, $nbs, $temppage);
						$temppage = str_replace($alias_c, $nbs, $temppage);
					}
					$temppage = str_replace($alias_a, $nbs, $temppage);
				}
				if (!empty($this->AliasNumPage)) {
					// replace page number
					$pnbs = $this->formatPageNumber($n);
					$pnbu = $this->UTF8ToUTF16BE($pnbs, false); // replacement for unicode font
					$temppage = str_replace($alias_pau, $pnbu, $temppage);
					if ($this->isunicode) {
						$temppage = str_replace($alias_pbu, $pnbu, $temppage);
						$temppage = str_replace($alias_pcu, $pnbu, $temppage);
						$temppage = str_replace($alias_pb, $pnbs, $temppage);
						$temppage = str_replace($alias_pc, $pnbs, $temppage);
					}
					$temppage = str_replace($alias_pa, $pnbs, $temppage);
				}
				$temppage = str_replace($this->epsmarker, '', $temppage);
				//Page
				$this->page_obj_id[$n] = $this->_newobj();
				$this->_out('<</Type /Page');
				$this->_out('/Parent 1 0 R');
				$this->_out(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->pagedim[$n]['w'], $this->pagedim[$n]['h']));
				$this->_out('/Resources 2 0 R');
				$this->_putannotsrefs($n);
				$this->_out('/Contents '.($this->n + 1).' 0 R>>');
				$this->_out('endobj');
				//Page content
				$p = ($this->compress) ? gzcompress($temppage) : $temppage;
				$this->_newobj();
				$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
				$this->_putstream($p);
				$this->_out('endobj');
				if ($this->diskcache) {
					// remove temporary files
					unlink($this->pages[$n]);
				}
			}
			//Pages root
			$this->offsets[1] = $this->bufferlen;
			$this->_out('1 0 obj');
			$this->_out('<</Type /Pages');
			$this->_out('/Kids [');
			foreach($this->page_obj_id as $page_obj) {
				$this->_out($page_obj.' 0 R');
			}
			$this->_out(']');
			$this->_out('/Count '.$nb);
			$this->_out('>>');
			$this->_out('endobj');
		}

		/**
		* Output referencees to page annotations
		* @param int $n page number
		* @access protected
		* @author Nicola Asuni
		* @since 4.7.000 (2008-08-29)
		*/
		protected function _putannotsrefs($n) {
			if (!(isset($this->PageAnnots[$n]) OR ($this->sign AND isset($this->signature_data['cert_type'])))) {
				return;
			}
			$this->_out('/Annots [');
			if (isset($this->PageAnnots[$n])) {
				$num_annots = count($this->PageAnnots[$n]);
				for ($i = 0; $i < $num_annots; ++$i) {
					++$this->curr_annot_obj_id;
					if (!in_array($this->curr_annot_obj_id, $this->radio_groups)) {
						$this->_out($this->curr_annot_obj_id.' 0 R');
					} else {
						++$num_annots;
					}
				}
			}
			if (($n==1) AND $this->sign AND isset($this->signature_data['cert_type'])) {
				// set reference for signature object
				$this->_out($this->sig_annot_ref);
			}
			$this->_out(']');
		}

		/**
		* Output annotations objects for all pages.
		* !!! THIS FUNCTION IS NOT YET COMPLETED !!!
		* See section 12.5 of PDF 32000_2008 reference.
		* @access protected
		* @author Nicola Asuni
		* @since 4.0.018 (2008-08-06)
		*/
		protected function _putannotsobjs() {
			// reset object counter
			$this->annot_obj_id = $this->annots_start_obj_id;
			for ($n=1; $n <= $this->numpages; ++$n) {
				if (isset($this->PageAnnots[$n])) {
					// set page annotations
					foreach ($this->PageAnnots[$n] as $key => $pl) {
						// create annotation object for grouping radiobuttons
						if (isset($this->radiobutton_groups[$n][$pl['txt']]) AND is_array($this->radiobutton_groups[$n][$pl['txt']])) {
							$annots = '<<';
							$annots .= ' /Type /Annot';
							$annots .= ' /Subtype /Widget';
							$annots .= ' /T '.$this->_datastring($pl['txt']);
							$annots .= ' /FT /Btn';
							$annots .= ' /Ff 49152';
							$annots .= ' /Kids [';
							foreach ($this->radiobutton_groups[$n][$pl['txt']] as $data) {
								$annots .= ' '.$data['kid'].' 0 R';
								if ($data['def'] !== 'Off') {
									$defval = $data['def'];
								}
							}
							$annots .= ' ]';
							if (isset($defval)) {
								$annots .= ' /V /'.$defval;
							}
							$annots .= ' >>';
							++$this->annot_obj_id;
							$this->offsets[$this->annot_obj_id] = $this->bufferlen;
							$this->_out($this->annot_obj_id.' 0 obj');
							$this->_out($annots);
							$this->_out('endobj');
							$this->form_obj_id[] = $this->annot_obj_id;
							// store object id to be used on Parent entry of Kids
							$this->radiobutton_groups[$n][$pl['txt']] = $this->annot_obj_id;
						}
						$formfield = false;
						$pl['opt'] = array_change_key_case($pl['opt'], CASE_LOWER);
						$a = $pl['x'] * $this->k;
						$b = $this->pagedim[$n]['h'] - (($pl['y'] + $pl['h'])  * $this->k);
						$c = $pl['w'] * $this->k;
						$d = $pl['h'] * $this->k;
						$rect = sprintf('%.2F %.2F %.2F %.2F', $a, $b, $a+$c, $b+$d);
						// create new annotation object
						$annots = '<</Type /Annot';
						$annots .= ' /Subtype /'.$pl['opt']['subtype'];
						$annots .= ' /Rect ['.$rect.']';
						$ft = array('Btn', 'Tx', 'Ch', 'Sig');
						if (isset($pl['opt']['ft']) AND in_array($pl['opt']['ft'], $ft)) {
							$annots .= ' /FT /'.$pl['opt']['ft'];
							$formfield = true;
						}
						$annots .= ' /Contents '.$this->_textstring($pl['txt']);
						$annots .= ' /P '.$this->page_obj_id[$n].' 0 R';
						$annots .= ' /NM '.$this->_datastring(sprintf('%04u-%04u', $n, $key));
						$annots .= ' /M '.$this->_datestring();
						if (isset($pl['opt']['f'])) {
							$val = 0;
							if (is_array($pl['opt']['f'])) {
								foreach ($pl['opt']['f'] as $f) {
									switch (strtolower($f)) {
										case 'invisible': {
											$val += 1 << 0;
											break;
										}
										case 'hidden': {
											$val += 1 << 1;
											break;
										}
										case 'print': {
											$val += 1 << 2;
											break;
										}
										case 'nozoom': {
											$val += 1 << 3;
											break;
										}
										case 'norotate': {
											$val += 1 << 4;
											break;
										}
										case 'noview': {
											$val += 1 << 5;
											break;
										}
										case 'readonly': {
											$val += 1 << 6;
											break;
										}
										case 'locked': {
											$val += 1 << 8;
											break;
										}
										case 'togglenoview': {
											$val += 1 << 9;
											break;
										}
										case 'lockedcontents': {
											$val += 1 << 10;
											break;
										}
										default: {
											break;
										}
									}
								}
							} else {
								$val = intval($pl['opt']['f']);
							}
							$annots .= ' /F '.intval($val);
						}
						if (isset($pl['opt']['as']) AND is_string($pl['opt']['as'])) {
							$annots .= ' /AS /'.$pl['opt']['as'];
						}
						if (isset($pl['opt']['ap'])) {
							// appearance stream
							$annots .= ' /AP <<';
							if (is_array($pl['opt']['ap'])) {
								foreach ($pl['opt']['ap'] as $apmode => $apdef) {
									// $apmode can be: n = normal; r = rollover; d = down;
									$annots .= ' /'.strtoupper($apmode);
									if (is_array($apdef)) {
										$annots .= ' <<';
										foreach ($apdef as $apstate => $stream) {
											// reference to XObject that define the appearance for this mode-state
											$apsobjid = $this->_putAPXObject($c, $d, $stream);
											$annots .= ' /'.$apstate.' '.$apsobjid.' 0 R';
										}
										$annots .= ' >>';
									} else {
										// reference to XObject that define the appearance for this mode
										$apsobjid = $this->_putAPXObject($c, $d, $apdef);
										$annots .= ' '.$apsobjid.' 0 R';
									}
								}
							} else {
								$annots .= $pl['opt']['ap'];
							}
							$annots .= ' >>';
						}
						if (isset($pl['opt']['bs']) AND (is_array($pl['opt']['bs']))) {
							$annots .= ' /BS <<';
							$annots .= ' /Type /Border';
							if (isset($pl['opt']['bs']['w'])) {
								$annots .= ' /W '.intval($pl['opt']['bs']['w']);
							}
							$bstyles = array('S', 'D', 'B', 'I', 'U');
							if (isset($pl['opt']['bs']['s']) AND in_array($pl['opt']['bs']['s'], $bstyles)) {
								$annots .= ' /S /'.$pl['opt']['bs']['s'];
							}
							if (isset($pl['opt']['bs']['d']) AND (is_array($pl['opt']['bs']['d']))) {
								$annots .= ' /D [';
								foreach ($pl['opt']['bs']['d'] as $cord) {
									$annots .= ' '.intval($cord);
								}
								$annots .= ']';
							}
							$annots .= ' >>';
						} else {
							$annots .= ' /Border [';
							if (isset($pl['opt']['border']) AND (count($pl['opt']['border']) >= 3)) {
								$annots .= intval($pl['opt']['border'][0]).' ';
								$annots .= intval($pl['opt']['border'][1]).' ';
								$annots .= intval($pl['opt']['border'][2]);
								if (isset($pl['opt']['border'][3]) AND is_array($pl['opt']['border'][3])) {
									$annots .= ' [';
									foreach ($pl['opt']['border'][3] as $dash) {
										$annots .= intval($dash).' ';
									}
									$annots .= ']';
								}
							} else {
								$annots .= '0 0 0';
							}
							$annots .= ']';
						}
						if (isset($pl['opt']['be']) AND (is_array($pl['opt']['be']))) {
							$annots .= ' /BE <<';
							$bstyles = array('S', 'C');
							if (isset($pl['opt']['be']['s']) AND in_array($pl['opt']['be']['s'], $markups)) {
								$annots .= ' /S /'.$pl['opt']['bs']['s'];
							} else {
								$annots .= ' /S /S';
							}
							if (isset($pl['opt']['be']['i']) AND ($pl['opt']['be']['i'] >= 0) AND ($pl['opt']['be']['i'] <= 2)) {
								$annots .= ' /I '.sprintf(" %.4F", $pl['opt']['be']['i']);
							}
							$annots .= '>>';
						}
						if (isset($pl['opt']['c']) AND (is_array($pl['opt']['c'])) AND !empty($pl['opt']['c'])) {
							$annots .= ' /C [';
							foreach ($pl['opt']['c'] as $col) {
								$col = intval($col);
								$color = $col <= 0 ? 0 : ($col >= 255 ? 1 : $col / 255);
								$annots .= sprintf(" %.4F", $color);
							}
							$annots .= ']';
						}
						//$annots .= ' /StructParent ';
						//$annots .= ' /OC ';
						$markups = array('text', 'freetext', 'line', 'square', 'circle', 'polygon', 'polyline', 'highlight',  'underline', 'squiggly', 'strikeout', 'stamp', 'caret', 'ink', 'fileattachment', 'sound');
						if (in_array(strtolower($pl['opt']['subtype']), $markups)) {
							// this is a markup type
							if (isset($pl['opt']['t']) AND is_string($pl['opt']['t'])) {
								$annots .= ' /T '.$this->_textstring($pl['opt']['t']);
							}
							//$annots .= ' /Popup ';
							if (isset($pl['opt']['ca'])) {
								$annots .= ' /CA '.sprintf("%.4F", floatval($pl['opt']['ca']));
							}
							if (isset($pl['opt']['rc'])) {
								$annots .= ' /RC '.$this->_textstring($pl['opt']['rc']);
							}
							$annots .= ' /CreationDate '.$this->_datestring();
							//$annots .= ' /IRT ';
							if (isset($pl['opt']['subj'])) {
								$annots .= ' /Subj '.$this->_textstring($pl['opt']['subj']);
							}
							//$annots .= ' /RT ';
							//$annots .= ' /IT ';
							//$annots .= ' /ExData ';
						}
						$lineendings = array('Square', 'Circle', 'Diamond', 'OpenArrow', 'ClosedArrow', 'None', 'Butt', 'ROpenArrow', 'RClosedArrow', 'Slash');
						switch (strtolower($pl['opt']['subtype'])) {
							case 'text': {
								if (isset($pl['opt']['open'])) {
									$annots .= ' /Open '. (strtolower($pl['opt']['open']) == 'true' ? 'true' : 'false');
								}
								$iconsapp = array('Comment', 'Help', 'Insert', 'Key', 'NewParagraph', 'Note', 'Paragraph');
								if (isset($pl['opt']['name']) AND in_array($pl['opt']['name'], $iconsapp)) {
									$annots .= ' /Name /'.$pl['opt']['name'];
								} else {
									$annots .= ' /Name /Note';
								}
								$statemodels = array('Marked', 'Review');
								if (isset($pl['opt']['statemodel']) AND in_array($pl['opt']['statemodel'], $statemodels)) {
									$annots .= ' /StateModel /'.$pl['opt']['statemodel'];
								} else {
									$pl['opt']['statemodel'] = 'Marked';
									$annots .= ' /StateModel /'.$pl['opt']['statemodel'];
								}
								if ($pl['opt']['statemodel'] == 'Marked') {
									$states = array('Accepted', 'Unmarked');
								} else {
									$states = array('Accepted', 'Rejected', 'Cancelled', 'Completed', 'None');
								}
								if (isset($pl['opt']['state']) AND in_array($pl['opt']['state'], $states)) {
									$annots .= ' /State /'.$pl['opt']['state'];
								} else {
									if ($pl['opt']['statemodel'] == 'Marked') {
										$annots .= ' /State /Unmarked';
									} else {
										$annots .= ' /State /None';
									}
								}
								break;
							}
							case 'link': {
								if(is_string($pl['txt'])) {
									// external URI link
									$annots .= ' /A <</S /URI /URI '.$this->_datastring($this->unhtmlentities($pl['txt'])).'>>';
								} else {
									// internal link
									$l = $this->links[$pl['txt']];
									$annots .= sprintf(' /Dest [%d 0 R /XYZ 0 %.2F null]', (1 + (2 * $l[0])), ($this->pagedim[$l[0]]['h'] - ($l[1] * $this->k)));
								}
								$hmodes = array('N', 'I', 'O', 'P');
								if (isset($pl['opt']['h']) AND in_array($pl['opt']['h'], $hmodes)) {
									$annots .= ' /H /'.$pl['opt']['h'];
								} else {
									$annots .= ' /H /I';
								}
								//$annots .= ' /PA ';
								//$annots .= ' /Quadpoints ';
								break;
							}
							case 'freetext': {
								if (isset($pl['opt']['da']) AND !empty($pl['opt']['da'])) {
									$annots .= ' /DA ('.$pl['opt']['da'].')';
								}
								if (isset($pl['opt']['q']) AND ($pl['opt']['q'] >= 0) AND ($pl['opt']['q'] <= 2)) {
									$annots .= ' /Q '.intval($pl['opt']['q']);
								}
								if (isset($pl['opt']['rc'])) {
									$annots .= ' /RC '.$this->_textstring($pl['opt']['rc']);
								}
								if (isset($pl['opt']['ds'])) {
									$annots .= ' /DS '.$this->_textstring($pl['opt']['ds']);
								}
								if (isset($pl['opt']['cl']) AND is_array($pl['opt']['cl'])) {
									$annots .= ' /CL [';
									foreach ($pl['opt']['cl'] as $cl) {
										$annots .= sprintf("%.4F ", $cl * $this->k);
									}
									$annots .= ']';
								}
								$tfit = array('FreeText', 'FreeTextCallout', 'FreeTextTypeWriter');
								if (isset($pl['opt']['it']) AND in_array($pl['opt']['it'], $tfit)) {
									$annots .= ' /IT '.$pl['opt']['it'];
								}
								if (isset($pl['opt']['rd']) AND is_array($pl['opt']['rd'])) {
									$l = $pl['opt']['rd'][0] * $this->k;
									$r = $pl['opt']['rd'][1] * $this->k;
									$t = $pl['opt']['rd'][2] * $this->k;
									$b = $pl['opt']['rd'][3] * $this->k;
									$annots .= ' /RD ['.sprintf('%.2F %.2F %.2F %.2F', $l, $r, $t, $b).']';
								}
								if (isset($pl['opt']['le']) AND in_array($pl['opt']['le'], $lineendings)) {
									$annots .= ' /LE /'.$pl['opt']['le'];
								}
								break;
							}
							case 'line': {
								break;
							}
							case 'square': {
								break;
							}
							case 'circle': {
								break;
							}
							case 'polygon': {
								break;
							}
							case 'polyline': {
								break;
							}
							case 'highlight': {
								break;
							}
							case 'underline': {
								break;
							}
							case 'squiggly': {
								break;
							}
							case 'strikeout': {
								break;
							}
							case 'stamp': {
								break;
							}
							case 'caret': {
								break;
							}
							case 'ink': {
								break;
							}
							case 'popup': {
								break;
							}
							case 'fileattachment': {
								if (!isset($pl['opt']['fs'])) {
									break;
								}
								$filename = basename($pl['opt']['fs']);
								if (isset($this->embeddedfiles[$filename]['n'])) {
									$annots .= ' /FS <</Type /Filespec /F '.$this->_datastring($filename).' /EF <</F '.$this->embeddedfiles[$filename]['n'].' 0 R>> >>';
									$iconsapp = array('Graph', 'Paperclip', 'PushPin', 'Tag');
									if (isset($pl['opt']['name']) AND in_array($pl['opt']['name'], $iconsapp)) {
										$annots .= ' /Name /'.$pl['opt']['name'];
									} else {
										$annots .= ' /Name /PushPin';
									}
								}
								break;
							}
							case 'sound': {
								if (!isset($pl['opt']['sound'])) {
									break;
								}
								$filename = basename($pl['opt']['sound']);
								if (isset($this->embeddedfiles[$filename]['n'])) {
									$annots .= ' /Sound <</Type /Sound';
									// ... TO BE COMPLETED ...
									// /R /C /B /E /CO /CP
									// $annots .= ' /F '.$this->_datastring($filename).' /EF <</F '.$this->embeddedfiles[$filename]['n'].' 0 R>> >>';
									$iconsapp = array('Speaker', 'Mic');
									if (isset($pl['opt']['name']) AND in_array($pl['opt']['name'], $iconsapp)) {
										$annots .= ' /Name /'.$pl['opt']['name'];
									} else {
										$annots .= ' /Name /Speaker';
									}
								}
								break;
							}
							case 'movie': {
								break;
							}
							case 'widget': {
								$hmode = array('N', 'I', 'O', 'P', 'T');
								if (isset($pl['opt']['h']) AND in_array($pl['opt']['h'], $hmode)) {
									$annots .= ' /H /'.$pl['opt']['h'];
								}
							 	if (isset($pl['opt']['mk']) AND (is_array($pl['opt']['mk'])) AND !empty($pl['opt']['mk'])) {
							 		$annots .= ' /MK <<';
							 		if (isset($pl['opt']['mk']['r'])) {
							 			$annots .= ' /R '.$pl['opt']['mk']['r'];
							 		}
							 		if (isset($pl['opt']['mk']['bc']) AND (is_array($pl['opt']['mk']['bc']))) {
							 			$annots .= ' /BC [';
							 			foreach($pl['opt']['mk']['bc'] AS $col) {
							 				$col = intval($col);
											$color = $col <= 0 ? 0 : ($col >= 255 ? 1 : $col / 255);
							 				$annots .= ' '.$color;
							 			}
							 			$annots .= ']';
							 		}
							 		if (isset($pl['opt']['mk']['bg']) AND (is_array($pl['opt']['mk']['bg']))) {
							 			$annots .= ' /BG [';
							 			foreach($pl['opt']['mk']['bg'] AS $col) {
							 				$col = intval($col);
											$color = $col <= 0 ? 0 : ($col >= 255 ? 1 : $col / 255);
							 				$annots .= ' '.$color;
							 			}
							 			$annots .= ']';
							 		}
							 		if (isset($pl['opt']['mk']['ca'])) {
							 			$annots .= ' /CA '.$pl['opt']['mk']['ca'].'';
							 		}
							 		if (isset($pl['opt']['mk']['rc'])) {
							 			$annots .= ' /RC '.$pl['opt']['mk']['ca'].'';
							 		}
							 		if (isset($pl['opt']['mk']['ac'])) {
							 			$annots .= ' /AC '.$pl['opt']['mk']['ca'].'';
							 		}							 								 		
							 		if (isset($pl['opt']['mk']['i'])) {
							 			$info = $this->getImageBuffer($pl['opt']['mk']['i']);
							 			if ($info !== false) {
							 				$annots .= ' /I '.$info['n'].' 0 R';
							 			}
							 		}
							 		if (isset($pl['opt']['mk']['ri'])) {
							 			$info = $this->getImageBuffer($pl['opt']['mk']['ri']);
							 			if ($info !== false) {
							 				$annots .= ' /RI '.$info['n'].' 0 R';
							 			}
							 		}
							 		if (isset($pl['opt']['mk']['ix'])) {
							 			$info = $this->getImageBuffer($pl['opt']['mk']['ix']);
							 			if ($info !== false) {
							 				$annots .= ' /IX '.$info['n'].' 0 R';
							 			}
							 		}							 		
							 		if (isset($pl['opt']['mk']['if']) AND (is_array($pl['opt']['mk']['if'])) AND !empty($pl['opt']['mk']['if'])) {
							 			$annots .= ' /IF <<';
							 			$if_sw = array('A', 'B', 'S', 'N');
										if (isset($pl['opt']['mk']['if']['sw']) AND in_array($pl['opt']['mk']['if']['sw'], $if_sw)) {
											$annots .= ' /SW /'.$pl['opt']['mk']['if']['sw'];
										}
							 			$if_s = array('A', 'P');
										if (isset($pl['opt']['mk']['if']['s']) AND in_array($pl['opt']['mk']['if']['s'], $if_s)) {
											$annots .= ' /S /'.$pl['opt']['mk']['if']['s'];
										}
										if (isset($pl['opt']['mk']['if']['a']) AND (is_array($pl['opt']['mk']['if']['a'])) AND !empty($pl['opt']['mk']['if']['a'])) {
											$annots .= ' /A ['.$pl['opt']['mk']['if']['a'][0].' '.$pl['opt']['mk']['if']['a'][1].']';
										}
										if (isset($pl['opt']['mk']['if']['fb']) AND ($pl['opt']['mk']['if']['fb'])) {
											$annots .= ' /FB true';
										}
							 			$annots .= '>>';
							 		}
							 		if (isset($pl['opt']['mk']['tp']) AND ($pl['opt']['mk']['tp'] >= 0) AND ($pl['opt']['mk']['tp'] <= 6)) {
							 			$annots .= ' /TP '.$pl['opt']['mk']['tp'];
							 		} else {
							 			$annots .= ' /TP 0';
							 		}
							 		$annots .= '>>';
							 	} // end MK
							 	// --- Entries for field dictionaries ---
							 	if (isset($this->radiobutton_groups[$n][$pl['txt']])) {
							 		// set parent
							 		$annots .= ' /Parent '.$this->radiobutton_groups[$n][$pl['txt']].' 0 R';
							 	}
							 	if (isset($pl['opt']['t']) AND is_string($pl['opt']['t'])) {
									$annots .= ' /T '.$this->_datastring($pl['opt']['t']);
								}
								if (isset($pl['opt']['tu']) AND is_string($pl['opt']['tu'])) {
									$annots .= ' /TU '.$this->_datastring($pl['opt']['tu']);
								}
								if (isset($pl['opt']['tm']) AND is_string($pl['opt']['tm'])) {
									$annots .= ' /TM '.$this->_datastring($pl['opt']['tm']);
								}
								if (isset($pl['opt']['ff'])) {
									if (is_array($pl['opt']['ff'])) {
										// array of bit settings
										$flag = 0;
										foreach($pl['opt']['ff'] as $val) {
											$flag += 1 << ($val - 1);
										}
									} else {
										$flag = intval($pl['opt']['ff']);
									}
									$annots .= ' /Ff '.$flag;
								}
								if (isset($pl['opt']['maxlen'])) {
									$annots .= ' /MaxLen '.intval($pl['opt']['maxlen']);
								}
								if (isset($pl['opt']['v'])) {
									$annots .= ' /V';
									if (is_array($pl['opt']['v'])) {
										foreach ($pl['opt']['v'] AS $optval) {
											$annots .= ' '.$optval;
										}
									} else {
										$annots .= ' '.$this->_textstring($pl['opt']['v']);
									}
								}
								if (isset($pl['opt']['dv']) AND is_string($pl['opt']['dv'])) {
									$annots .= ' /DV';
									if (is_array($pl['opt']['dv'])) {
										foreach ($pl['opt']['dv'] AS $optval) {
											$annots .= ' '.$optval;
										}
									} else {
										$annots .= ' '.$this->_textstring($pl['opt']['dv']);
									}
								}
								if (isset($pl['opt']['rv']) AND is_string($pl['opt']['rv'])) {
									$annots .= ' /RV';
									if (is_array($pl['opt']['rv'])) {
										foreach ($pl['opt']['rv'] AS $optval) {
											$annots .= ' '.$optval;
										}
									} else {
										$annots .= ' '.$this->_textstring($pl['opt']['rv']);
									}
								}
								if (isset($pl['opt']['a']) AND !empty($pl['opt']['a'])) {
									$annots .= ' /A << '.$pl['opt']['a'].' >>';
								}
								if (isset($pl['opt']['aa']) AND !empty($pl['opt']['aa'])) {
									$annots .= ' /AA << '.$pl['opt']['aa'].' >>';
								}
								if (isset($pl['opt']['da']) AND !empty($pl['opt']['da'])) {
									$annots .= ' /DA ('.$pl['opt']['da'].')';
								}
								if (isset($pl['opt']['q']) AND ($pl['opt']['q'] >= 0) AND ($pl['opt']['q'] <= 2)) {
									$annots .= ' /Q '.intval($pl['opt']['q']);
								}
								if (isset($pl['opt']['opt']) AND (is_array($pl['opt']['opt'])) AND !empty($pl['opt']['opt'])) {
						 			$annots .= ' /Opt [';
						 			foreach($pl['opt']['opt'] AS $copt) {
						 				if (is_array($copt)) {
						 					$annots .= ' ['.$this->_textstring($copt[0]).' '.$this->_textstring($copt[1]).']';
						 				} else {
						 					$annots .= ' '.$this->_textstring($copt);
						 				}
						 			}
						 			$annots .= ']';
						 		}
						 		if (isset($pl['opt']['ti'])) {
						 			$annots .= ' /TI '.intval($pl['opt']['ti']);
						 		}
						 		if (isset($pl['opt']['i']) AND (is_array($pl['opt']['i'])) AND !empty($pl['opt']['i'])) {
						 			$annots .= ' /I [';
						 			foreach($pl['opt']['i'] AS $copt) {
						 				$annots .= intval($copt).' ';
						 			}
						 			$annots .= ']';
						 		}
								break;
							}
							case 'screen': {
								break;
							}
							case 'printermark': {
								break;
							}
							case 'trapnet': {
								break;
							}
							case 'watermark': {
								break;
							}
							case '3d': {
								break;
							}
							default: {
								break;
							}
						}
						$annots .= '>>';
						// create new annotation object
						++$this->annot_obj_id;
						$this->offsets[$this->annot_obj_id] = $this->bufferlen;
						$this->_out($this->annot_obj_id.' 0 obj');
						$this->_out($annots);
						$this->_out('endobj');
						if ($formfield AND ! isset($this->radiobutton_groups[$n][$pl['txt']])) {
							// store reference of form object
							$this->form_obj_id[] = $this->annot_obj_id;
						}
					}
				}
			} // end for each page
		}

		/**
		* Put appearance streams XObject used to define annotation's appearance states
		* @param int $w annotation width
		* @param int $h annotation height
		* @param string $stream appearance stream
		* @return int object ID
		* @access protected
		* @since 4.8.001 (2009-09-09)
		*/
		protected function _putAPXObject($w=0, $h=0, $stream='') {
			$stream = trim($stream);
			++$this->apxo_obj_id;
			$this->offsets[$this->apxo_obj_id] = $this->bufferlen;
			$this->_out($this->apxo_obj_id.' 0 obj');
			$this->_out('<<');
			$this->_out('/Type /XObject');
			$this->_out('/Subtype /Form');
			$this->_out('/FormType 1');
			if ($this->compress) {
				$stream = gzcompress($stream);
				$this->_out('/Filter /FlateDecode');
			}
			$rect = sprintf('%.2F %.2F', $w, $h);
			$this->_out('/BBox [0 0 '.$rect.']');
			$this->_out('/Matrix [1 0 0 1 0 0]');
			$this->_out('/Resources <</ProcSet [/PDF]>>');
			$this->_out('/Length '.strlen($stream));
			$this->_out('>>');
			$this->_putstream($stream);
			$this->_out('endobj');
			return $this->apxo_obj_id;
		}

		/**
		* Output fonts.
		* @access protected
		*/
		protected function _putfonts() {
			$nf = $this->n;
			foreach ($this->diffs as $diff) {
				//Encodings
				$this->_newobj();
				$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
				$this->_out('endobj');
			}
			$mqr = $this->get_mqr();
			$this->set_mqr(false);
			foreach ($this->FontFiles as $file => $info) {
				// search and get font file to embedd
				$fontdir = $info['fontdir'];
				$file = strtolower($file);
				$fontfile = '';
				// search files on various directories
				if (file_exists($fontdir.$file)) {
					$fontfile = $fontdir.$file;
				} elseif (file_exists($this->_getfontpath().$file)) {
					$fontfile = $this->_getfontpath().$file;
				} elseif (file_exists($file)) {
					$fontfile = $file;
				}
				if (!$this->empty_string($fontfile)) {
					$font = file_get_contents($fontfile);
					$compressed = (substr($file, -2) == '.z');
					if ((!$compressed) AND (isset($info['length2']))) {
						$header = (ord($font{0}) == 128);
						if ($header) {
							//Strip first binary header
							$font = substr($font, 6);
						}
						if ($header AND (ord($font{$info['length1']}) == 128)) {
							//Strip second binary header
							$font = substr($font, 0, $info['length1']).substr($font, ($info['length1'] + 6));
						}
					}
					$this->_newobj();
					$this->FontFiles[$file]['n'] = $this->n;
					$this->_out('<</Length '.strlen($font));
					if ($compressed) {
						$this->_out('/Filter /FlateDecode');
					}
					$this->_out('/Length1 '.$info['length1']);
					if (isset($info['length2'])) {
						$this->_out('/Length2 '.$info['length2'].' /Length3 0');
					}
					$this->_out('>>');
					$this->_putstream($font);
					$this->_out('endobj');
				}
			}
			$this->set_mqr($mqr);
			foreach ($this->fontkeys as $k) {
				//Font objects
				$this->setFontSubBuffer($k, 'n', $this->n + 1);
				$font = $this->getFontBuffer($k);
				$type = $font['type'];
				$name = $font['name'];
				if ($type == 'core') {
					//Standard font
					$obj_id = $this->_newobj();
					$this->_out('<</Type /Font');
					$this->_out('/Subtype /Type1');
					$this->_out('/BaseFont /'.$name);
					$this->_out('/Name /F'.$font['i']);
					if ((strtolower($name) != 'symbol') AND (strtolower($name) != 'zapfdingbats')) {
						$this->_out('/Encoding /WinAnsiEncoding');
					}
					if (strtolower($name) == 'helvetica') {
						// add default font for annotations
						$this->annotation_fonts['helvetica'] = $k;
					}
					$this->_out('>>');
					$this->_out('endobj');
				} elseif (($type == 'Type1') OR ($type == 'TrueType')) {
					//Additional Type1 or TrueType font
					$obj_id = $this->_newobj();
					$this->_out('<</Type /Font');
					$this->_out('/Subtype /'.$type);
					$this->_out('/BaseFont /'.$name);
					$this->_out('/Name /F'.$font['i']);
					$this->_out('/FirstChar 32 /LastChar 255');
					$this->_out('/Widths '.($this->n + 1).' 0 R');
					$this->_out('/FontDescriptor '.($this->n + 2).' 0 R');
					if ($font['enc']) {
						if (isset($font['diff'])) {
							$this->_out('/Encoding '.($nf + $font['diff']).' 0 R');
						} else {
							$this->_out('/Encoding /WinAnsiEncoding');
						}
					}
					$this->_out('>>');
					$this->_out('endobj');
					// Widths
					$this->_newobj();
					$cw = &$font['cw'];
					$s = '[';
					for ($i = 32; $i < 256; ++$i) {
						$s .= $cw[$i].' ';
					}
					$this->_out($s.']');
					$this->_out('endobj');
					//Descriptor
					$this->_newobj();
					$s = '<</Type /FontDescriptor /FontName /'.$name;
					foreach ($font['desc'] as $fdk => $fdv) {
						$s .= ' /'.$fdk.' '.$fdv.'';
					}
					if (!$this->empty_string($font['file'])) {
						$s .= ' /FontFile'.($type == 'Type1' ? '' : '2').' '.$this->FontFiles[$font['file']]['n'].' 0 R';
					}
					$this->_out($s.'>>');
					$this->_out('endobj');
				} else {
					//Allow for additional types
					$mtd = '_put'.strtolower($type);
					if (!method_exists($this, $mtd)) {
						$this->Error('Unsupported font type: '.$type);
					}
					$obj_id = $this->$mtd($font);
				}
				// store object ID for current font
				$this->font_obj_ids[$k] = $obj_id;
			}
		}
		
		/**
		* Outputs font widths
		* @parameter array $font font data
		* @parameter int $cidoffset offset for CID values
		* @author Nicola Asuni
		* @access protected
		* @since 4.4.000 (2008-12-07)
		*/
		protected function _putfontwidths($font, $cidoffset=0) {
			ksort($font['cw']);
			$rangeid = 0;
			$range = array();
			$prevcid = -2;
			$prevwidth = -1;
			$interval = false;
			// for each character
			foreach ($font['cw'] as $cid => $width) {
				$cid -= $cidoffset;
				if ($width != $font['dw']) {
					if ($cid == ($prevcid + 1)) {
						// consecutive CID
						if ($width == $prevwidth) {
							if ($width == $range[$rangeid][0]) {
								$range[$rangeid][] = $width;
							} else {
								array_pop($range[$rangeid]);
								// new range
								$rangeid = $prevcid;
								$range[$rangeid] = array();
								$range[$rangeid][] = $prevwidth;
								$range[$rangeid][] = $width;
							}
							$interval = true;
							$range[$rangeid]['interval'] = true;
						} else {
							if ($interval) {
								// new range
								$rangeid = $cid;
								$range[$rangeid] = array();
								$range[$rangeid][] = $width;
							} else {
								$range[$rangeid][] = $width;
							}
							$interval = false;
						}
					} else {
						// new range
						$rangeid = $cid;
						$range[$rangeid] = array();
						$range[$rangeid][] = $width;
						$interval = false;
					}
					$prevcid = $cid;
					$prevwidth = $width;
				}
			}
			// optimize ranges
			$prevk = -1;
			$nextk = -1;
			$prevint = false;
			foreach ($range as $k => $ws) {
				$cws = count($ws);
				if (($k == $nextk) AND (!$prevint) AND ((!isset($ws['interval'])) OR ($cws < 4))) {
					if (isset($range[$k]['interval'])) {
						unset($range[$k]['interval']);
					}
					$range[$prevk] = array_merge($range[$prevk], $range[$k]);
					unset($range[$k]);
				} else {
					$prevk = $k;
				}
				$nextk = $k + $cws;
				if (isset($ws['interval'])) {
					if ($cws > 3) {
						$prevint = true;
					} else {
						$prevint = false;
					}
					unset($range[$k]['interval']);
					--$nextk;
				} else {
					$prevint = false;
				}
			}
			// output data
			$w = '';
			foreach ($range as $k => $ws) {
				if (count(array_count_values($ws)) == 1) {
					// interval mode is more compact
					$w .= ' '.$k.' '.($k + count($ws) - 1).' '.$ws[0];
				} else {
					// range mode
					$w .= ' '.$k.' [ '.implode(' ', $ws).' ]';
				}
			}
			$this->_out('/W ['.$w.' ]');
		}
		
		/**
		* Adds unicode fonts.<br>
		* Based on PDF Reference 1.3 (section 5)
		* @parameter array $font font data
		* @return int font object ID
		* @access protected
		* @author Nicola Asuni
		* @since 1.52.0.TC005 (2005-01-05)
		*/
		protected function _puttruetypeunicode($font) {
			// Type0 Font
			// A composite font composed of other fonts, organized hierarchically
			$obj_id = $this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /Type0');
			$this->_out('/BaseFont /'.$font['name'].'');
			$this->_out('/Name /F'.$font['i']);
			$this->_out('/Encoding /'.$font['enc']);
			$this->_out('/ToUnicode /Identity-H');
			$this->_out('/DescendantFonts ['.($this->n + 1).' 0 R]');
			$this->_out('>>');
			$this->_out('endobj');
			// CIDFontType2
			// A CIDFont whose glyph descriptions are based on TrueType font technology
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /CIDFontType2');
			$this->_out('/BaseFont /'.$font['name'].'');
			// A dictionary containing entries that define the character collection of the CIDFont.
			$cidinfo = '/Registry '.$this->_datastring($font['cidinfo']['Registry']);
			$cidinfo .= ' /Ordering '.$this->_datastring($font['cidinfo']['Ordering']);
			$cidinfo .= ' /Supplement '.$font['cidinfo']['Supplement'];
			$this->_out('/CIDSystemInfo <<'.$cidinfo.'>>');
			$this->_out('/FontDescriptor '.($this->n + 1).' 0 R');
			$this->_out('/DW '.$font['dw'].''); // default width
			$this->_putfontwidths($font, 0);
			$this->_out('/CIDToGIDMap '.($this->n + 2).' 0 R');
			$this->_out('>>');
			$this->_out('endobj');			
			// Font descriptor
			// A font descriptor describing the CIDFont default metrics other than its glyph widths
			$this->_newobj();
			$this->_out('<</Type /FontDescriptor');
			$this->_out('/FontName /'.$font['name']);
			foreach ($font['desc'] as $key => $value) {
				$this->_out('/'.$key.' '.$value);
			}
			$fontdir = '';
			if (!$this->empty_string($font['file'])) {
				// A stream containing a TrueType font
				$this->_out('/FontFile2 '.$this->FontFiles[$font['file']]['n'].' 0 R');
				$fontdir = $this->FontFiles[$font['file']]['fontdir'];
			}
			$this->_out('>>');
			$this->_out('endobj');
			$this->_newobj();
			if (isset($font['ctg']) AND (!$this->empty_string($font['ctg']))) {
				// Embed CIDToGIDMap
				// A specification of the mapping from CIDs to glyph indices
				// search and get CTG font file to embedd
				$ctgfile = strtolower($font['ctg']);
				// search and get ctg font file to embedd
				$fontfile = '';
				// search files on various directories
				if (file_exists($fontdir.$ctgfile)) {
					$fontfile = $fontdir.$ctgfile;
				} elseif (file_exists($this->_getfontpath().$ctgfile)) {
					$fontfile = $this->_getfontpath().$ctgfile;
				} elseif (file_exists($ctgfile)) {
					$fontfile = $ctgfile;
				}
				if ($this->empty_string($fontfile)) {
					$this->Error('Font file not found: '.$ctgfile);
				}
				$size = filesize($fontfile);
				$this->_out('<</Length '.$size.'');
				if (substr($fontfile, -2) == '.z') { // check file extension
					// Decompresses data encoded using the public-domain 
					// zlib/deflate compression method, reproducing the 
					// original text or binary data
					$this->_out('/Filter /FlateDecode');
				}
				$this->_out('>>');
				$this->_putstream(file_get_contents($fontfile));
			}
			$this->_out('endobj');
			return $obj_id;
		}
		
		/**
		 * Output CID-0 fonts.
		 * A Type 0 CIDFont contains glyph descriptions based on the Adobe Type 1 font format
		 * @param array $font font data
		 * @return int font object ID
		 * @access protected
		 * @author Andrew Whitehead, Nicola Asuni, Yukihiro Nakadaira
		 * @since 3.2.000 (2008-06-23)
		 */
		protected function _putcidfont0($font) {
			$cidoffset = 0;
			if (!isset($font['cw'][1])) {
				$cidoffset = 31;
			}
			if (isset($font['cidinfo']['uni2cid'])) {
				// convert unicode to cid.
				$uni2cid = $font['cidinfo']['uni2cid'];
				$cw = array();
				foreach ($font['cw'] as $uni => $width) {
					if (isset($uni2cid[$uni])) {
						$cw[($uni2cid[$uni] + $cidoffset)] = $width;
					} elseif ($uni < 256) {
						$cw[$uni] = $width;
					} // else unknown character
				}
				$font = array_merge($font, array('cw' => $cw));
			}
			$name = $font['name'];
			$enc = $font['enc'];
			if ($enc) {
				$longname = $name.'-'.$enc;
			} else {
				$longname = $name;
			}
			$obj_id = $this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /Type0');
			$this->_out('/BaseFont /'.$longname);
			$this->_out('/Name /F'.$font['i']);
			if ($enc) {
				$this->_out('/Encoding /'.$enc);
			}
			$this->_out('/DescendantFonts ['.($this->n + 1).' 0 R]');
			$this->_out('>>');
			$this->_out('endobj');
			$this->_newobj();
			$this->_out('<</Type /Font');
			$this->_out('/Subtype /CIDFontType0');
			$this->_out('/BaseFont /'.$name);
			$cidinfo = '/Registry '.$this->_datastring($font['cidinfo']['Registry']);
			$cidinfo .= ' /Ordering '.$this->_datastring($font['cidinfo']['Ordering']);
			$cidinfo .= ' /Supplement '.$font['cidinfo']['Supplement'];
			$this->_out('/CIDSystemInfo <<'.$cidinfo.'>>');
			$this->_out('/FontDescriptor '.($this->n + 1).' 0 R');
			$this->_out('/DW '.$font['dw']);
			$this->_putfontwidths($font, $cidoffset);
			$this->_out('>>');
			$this->_out('endobj');
			$this->_newobj();
			$s = '<</Type /FontDescriptor /FontName /'.$name;
			foreach ($font['desc'] as $k => $v) {
				if ($k != 'Style') {
					$s .= ' /'.$k.' '.$v.'';
				}
			}
			$this->_out($s.'>>');
			$this->_out('endobj');
			return $obj_id;
		}

		/**
		 * Output images.
		 * @access protected
		 */
		protected function _putimages() {
			$filter = ($this->compress) ? '/Filter /FlateDecode ' : '';
			foreach ($this->imagekeys as $file) {
				$info = $this->getImageBuffer($file);
				$this->_newobj();
				$this->setImageSubBuffer($file, 'n', $this->n);
				$this->_out('<</Type /XObject');
				$this->_out('/Subtype /Image');
				$this->_out('/Width '.$info['w']);
				$this->_out('/Height '.$info['h']);
				if (isset($info['masked'])) {
					$this->_out('/SMask '.($this->n - 1).' 0 R');
				}
				if ($info['cs'] == 'Indexed') {
					$this->_out('/ColorSpace [/Indexed /DeviceRGB '.((strlen($info['pal']) / 3) - 1).' '.($this->n + 1).' 0 R]');
				} else {
					$this->_out('/ColorSpace /'.$info['cs']);
					if ($info['cs'] == 'DeviceCMYK') {
						$this->_out('/Decode [1 0 1 0 1 0 1 0]');
					}
				}
				$this->_out('/BitsPerComponent '.$info['bpc']);
				if (isset($info['f'])) {
					$this->_out('/Filter /'.$info['f']);
				}
				if (isset($info['parms'])) {
					$this->_out($info['parms']);
				}
				if (isset($info['trns']) AND is_array($info['trns'])) {
					$trns='';
					$count_info = count($info['trns']);
					for ($i=0; $i < $count_info; ++$i) {
						$trns .= $info['trns'][$i].' '.$info['trns'][$i].' ';
					}
					$this->_out('/Mask ['.$trns.']');
				}
				$this->_out('/Length '.strlen($info['data']).'>>');
				$this->_putstream($info['data']);
				$this->_out('endobj');
				//Palette
				if ($info['cs'] == 'Indexed') {
					$this->_newobj();
					$pal = ($this->compress) ? gzcompress($info['pal']) : $info['pal'];
					$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
					$this->_putstream($pal);
					$this->_out('endobj');
				}
			}
		}

		/**
		* Output Spot Colors Resources.
		* @access protected
		* @since 4.0.024 (2008-09-12)
		*/
		protected function _putspotcolors() {
			foreach ($this->spot_colors as $name => $color) {
				$this->_newobj();
				$this->spot_colors[$name]['n'] = $this->n;
				$this->_out('[/Separation /'.str_replace(' ', '#20', $name));
				$this->_out('/DeviceCMYK <<');
				$this->_out('/Range [0 1 0 1 0 1 0 1] /C0 [0 0 0 0] ');
				$this->_out(sprintf('/C1 [%.4F %.4F %.4F %.4F] ', $color['c']/100, $color['m']/100, $color['y']/100, $color['k']/100));
				$this->_out('/FunctionType 2 /Domain [0 1] /N 1>>]');
				$this->_out('endobj');
			}
		}

		/**
		* Output object dictionary for images.
		* @access protected
		*/
		protected function _putxobjectdict() {
			foreach ($this->imagekeys as $file) {
				$info = $this->getImageBuffer($file);
				$this->_out('/I'.$info['i'].' '.$info['n'].' 0 R');
			}
		}

		/**
		* Output Resources Dictionary.
		* @access protected
		*/
		protected function _putresourcedict() {
			$this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
			$this->_out('/Font <<');
			foreach ($this->fontkeys as $fontkey) {
				$font = $this->getFontBuffer($fontkey);
				$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
			}
			$this->_out('>>');
			$this->_out('/XObject <<');
			$this->_putxobjectdict();
			$this->_out('>>');
			// visibility
			$this->_out('/Properties <</OC1 '.$this->n_ocg_print.' 0 R /OC2 '.$this->n_ocg_view.' 0 R>>');
			// transparency
			$this->_out('/ExtGState <<');
			foreach ($this->extgstates as $k => $extgstate) {
				$this->_out('/GS'.$k.' '.$extgstate['n'].' 0 R');
			}
			$this->_out('>>');
			// gradients
			if (isset($this->gradients) AND (count($this->gradients) > 0)) {
				$this->_out('/Shading <<');
				foreach ($this->gradients as $id => $grad) {
					$this->_out('/Sh'.$id.' '.$grad['id'].' 0 R');
				}
				$this->_out('>>');
			}
			// spot colors
			if (isset($this->spot_colors) AND (count($this->spot_colors) > 0)) {
				$this->_out('/ColorSpace <<');
				foreach ($this->spot_colors as $color) {
					$this->_out('/CS'.$color['i'].' '.$color['n'].' 0 R');
				}
				$this->_out('>>');
			}
		}
		
		/**
		* Output Resources.
		* @access protected
		*/
		protected function _putresources() {
			$this->_putextgstates();
			$this->_putocg();
			$this->_putfonts();
			$this->_putimages();
			$this->_putspotcolors();
			$this->_putshaders();
			//Resource dictionary
			$this->offsets[2] = $this->bufferlen;
			$this->_out('2 0 obj');
			$this->_out('<<');
			$this->_putresourcedict();
			$this->_out('>>');
			$this->_out('endobj');
			$this->_putbookmarks();
			$this->_putEmbeddedFiles();
			$this->_putannotsobjs();
			$this->_putjavascript();
			// encryption
			if ($this->encrypted) {
				$this->_newobj();
				$this->enc_obj_id = $this->n;
				$this->_out('<<');
				$this->_putencryption();
				$this->_out('>>');
				$this->_out('endobj');
			}
		}
		
		/**
		* Adds some Metadata information
		* (see Chapter 10.2 of PDF Reference)
		* @access protected
		*/
		protected function _putinfo() {
			if ($this->empty_string($this->title)) {
				$this->title = '?';
			}
			$this->_out('/Title '.$this->_textstring($this->title));
			if ($this->empty_string($this->author)) {
				$this->author = '?';
			}
			$this->_out('/Author '.$this->_textstring($this->author));
			if ($this->empty_string($this->subject)) {
				$this->subject = '?';
			}
			$this->_out('/Subject '.$this->_textstring($this->subject));
			if ($this->empty_string($this->keywords)) {
				$this->keywords = '?';
			}
			$this->_out('/Keywords '.$this->_textstring($this->keywords));
			if ($this->empty_string($this->creator)) {
				$this->creator = '?';
			}
			$this->_out('/Creator '.$this->_textstring($this->creator));
			if (defined('PDF_PRODUCER')) {
				$this->_out('/Producer '.$this->_textstring(PDF_PRODUCER));
			} else {
				$this->_out('/Producer '.$this->_textstring('TCPDF'));
			}
			$this->_out('/CreationDate '.$this->_datestring());
			$this->_out('/ModDate '.$this->_datestring());	
		}
		
		/**
		* Output Catalog.
		* @access protected
		*/
		protected function _putcatalog() {
			$this->_out('/Type /Catalog');
			$this->_out('/Pages 1 0 R');
			if ($this->ZoomMode == 'fullpage') {
				$this->_out('/OpenAction [3 0 R /Fit]');
			} elseif ($this->ZoomMode == 'fullwidth') {
				$this->_out('/OpenAction [3 0 R /FitH null]');
			} elseif ($this->ZoomMode == 'real') {
				$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
			} elseif (!is_string($this->ZoomMode)) {
				$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode / 100).']');
			}			
			if (isset($this->LayoutMode) AND (!$this->empty_string($this->LayoutMode))) {
				$this->_out('/PageLayout /'.$this->LayoutMode.'');
			}
			if (isset($this->PageMode) AND (!$this->empty_string($this->PageMode))) {
				$this->_out('/PageMode /'.$this->PageMode);
			}
			if (isset($this->l['a_meta_language'])) {
				$this->_out('/Lang /'.$this->l['a_meta_language']);
			}
			$this->_out('/Names <<');
			if ((!empty($this->javascript)) OR (!empty($this->js_objects))) {
				$this->_out('/JavaScript '.($this->n_js).' 0 R');
			}
			$this->_out('>>');			
			if (count($this->outlines) > 0) {
				$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
				$this->_out('/PageMode /UseOutlines');
			}
			$this->_putviewerpreferences();
			$p = $this->n_ocg_print.' 0 R';
			$v = $this->n_ocg_view.' 0 R';
			$as = '<</Event /Print /OCGs ['.$p.' '.$v.'] /Category [/Print]>> <</Event /View /OCGs ['.$p.' '.$v.'] /Category [/View]>>';
			$this->_out('/OCProperties <</OCGs ['.$p.' '.$v.'] /D <</ON ['.$p.'] /OFF ['.$v.'] /AS ['.$as.']>>>>');
			// AcroForm
			if (!empty($this->form_obj_id) OR ($this->sign AND isset($this->signature_data['cert_type']))) {
				$this->_out('/AcroForm<<');
				$objrefs = '';
				if ($this->sign AND isset($this->signature_data['cert_type'])) {
					$objrefs .= $this->sig_obj_id.' 0 R';
				}
				if (!empty($this->form_obj_id)) {
					foreach($this->form_obj_id as $objid) {
						$objrefs .= ' '.$objid.' 0 R';
					}
				}
				$this->_out('/Fields ['.$objrefs.']');
				$this->_out('/NeedAppearances '.(empty($this->form_obj_id)?'false':'true'));
				if ($this->sign AND isset($this->signature_data['cert_type'])) {
					$this->_out('/SigFlags 3');
				}
				//$this->_out('/CO ');
				if (isset($this->annotation_fonts) AND !empty($this->annotation_fonts)) {
					$this->_out('/DR <<');
					$this->_out('/Font <<');
					foreach ($this->annotation_fonts as $font => $fontkey) {
						$this->_out('/F'.($fontkey + 1).' '.$this->font_obj_ids[$font].' 0 R');
					}
					$this->_out('>>');
					$this->_out('>>');
				}
				$this->_out('/DA (/F'.(array_search('helvetica', $this->fontkeys) + 1).' 0 Tf 0 g)');
				$this->_out('/Q '.(($this->rtl)?'2':'0'));
				//$this->_out('/XFA ');
				$this->_out('>>');
				// signatures
				if ($this->sign AND isset($this->signature_data['cert_type'])) {
					if ($this->signature_data['cert_type'] > 0) {
						$this->_out('/Perms<</DocMDP '.($this->sig_obj_id + 1).' 0 R>>');
					} else {
						$this->_out('/Perms<</UR3 '.($this->sig_obj_id + 1).' 0 R>>');
					}
				}
			}
		}
		
		/**
		* Output viewer preferences.
		* @author Nicola asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function _putviewerpreferences() {
			$this->_out('/ViewerPreferences<<');
			if ($this->rtl) {
				$this->_out('/Direction /R2L');
			} else {
				$this->_out('/Direction /L2R');
			}
			if (isset($this->viewer_preferences['HideToolbar']) AND ($this->viewer_preferences['HideToolbar'])) {
				$this->_out('/HideToolbar true');
			}
			if (isset($this->viewer_preferences['HideMenubar']) AND ($this->viewer_preferences['HideMenubar'])) {
				$this->_out('/HideMenubar true');
			}
			if (isset($this->viewer_preferences['HideWindowUI']) AND ($this->viewer_preferences['HideWindowUI'])) {
				$this->_out('/HideWindowUI true');
			}
			if (isset($this->viewer_preferences['FitWindow']) AND ($this->viewer_preferences['FitWindow'])) {
				$this->_out('/FitWindow true');
			}
			if (isset($this->viewer_preferences['CenterWindow']) AND ($this->viewer_preferences['CenterWindow'])) {
				$this->_out('/CenterWindow true');
			}
			if (isset($this->viewer_preferences['DisplayDocTitle']) AND ($this->viewer_preferences['DisplayDocTitle'])) {
				$this->_out('/DisplayDocTitle true');
			}
			if (isset($this->viewer_preferences['NonFullScreenPageMode'])) {
				$this->_out('/NonFullScreenPageMode /'.$this->viewer_preferences['NonFullScreenPageMode'].'');
			}
			if (isset($this->viewer_preferences['ViewArea'])) {
				$this->_out('/ViewArea /'.$this->viewer_preferences['ViewArea']);
			}
			if (isset($this->viewer_preferences['ViewClip'])) {
				$this->_out('/ViewClip /'.$this->viewer_preferences['ViewClip']);
			}
			if (isset($this->viewer_preferences['PrintArea'])) {
				$this->_out('/PrintArea /'.$this->viewer_preferences['PrintArea']);
			}
			if (isset($this->viewer_preferences['PrintClip'])) {
				$this->_out('/PrintClip /'.$this->viewer_preferences['PrintClip']);
			}
			if (isset($this->viewer_preferences['PrintScaling'])) {
				$this->_out('/PrintScaling /'.$this->viewer_preferences['PrintScaling']);
			}
			if (isset($this->viewer_preferences['Duplex']) AND (!$this->empty_string($this->viewer_preferences['Duplex']))) {
				$this->_out('/Duplex /'.$this->viewer_preferences['Duplex']);
			}
			if (isset($this->viewer_preferences['PickTrayByPDFSize'])) {
				if ($this->viewer_preferences['PickTrayByPDFSize']) {
					$this->_out('/PickTrayByPDFSize true');
				} else {
					$this->_out('/PickTrayByPDFSize false');
				}
			}
			if (isset($this->viewer_preferences['PrintPageRange'])) {
				$PrintPageRangeNum = '';
				foreach ($this->viewer_preferences['PrintPageRange'] as $k => $v) {
					$PrintPageRangeNum .= ' '.($v - 1).'';
				}
				$this->_out('/PrintPageRange ['.substr($PrintPageRangeNum,1).']');
			}
			if (isset($this->viewer_preferences['NumCopies'])) {
				$this->_out('/NumCopies '.intval($this->viewer_preferences['NumCopies']));
			}
			$this->_out('>>');
		}
		
		/**
		* Output trailer.
		* @access protected
		*/
		protected function _puttrailer() {
			$this->_out('/Size '.($this->n + 1));
			$this->_out('/Root '.$this->n.' 0 R');
			$this->_out('/Info '.($this->n - 1).' 0 R');
			if ($this->encrypted) {
				$this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
				$this->_out('/ID [()()]');
			}
		}

		/**
		* Output PDF header.
		* @access protected
		*/
		protected function _putheader() {
			$this->_out('%PDF-'.$this->PDFVersion);
		}

		/**
		* Output end of document (EOF).
		* @access protected
		*/
		protected function _enddoc() {
			$this->state = 1;
			$this->_putheader();			
			$this->_putpages();
			$this->_putresources();
			// Signature
			if ($this->sign AND isset($this->signature_data['cert_type'])) {
				// widget annotation for signature
				$this->sig_obj_id = $this->_newobj();
				// --- replace signature ID on the first page ---
				// get the document content
				$pdfdoc = $this->getBuffer();
				// Remove the original buffer
				if (isset($this->diskcache) AND $this->diskcache) {
					// remove buffer file from cache
					unlink($this->buffer);
				}
				unset($this->buffer);
				$signature_widget_ref = sprintf('%u 0 R', $this->sig_obj_id);
				$signature_widget_ref .= str_repeat(' ', (strlen($this->sig_annot_ref) - strlen($signature_widget_ref)));
				$pdfdoc = str_replace($this->sig_annot_ref, $signature_widget_ref, $pdfdoc);
				$this->diskcache = false;
				$this->buffer = &$pdfdoc;
				$this->bufferlen = strlen($pdfdoc);
				// ---
				$this->_out('<<');
				$this->_out('/Type /Annot /Subtype /Widget /Rect [0 0 0 0]');
				$this->_out('/P 3 0 R'); // link to first page object
				$this->_out('/FT /Sig');
				$this->_out('/T '.$this->_textstring('Signature'));
				$this->_out('/Ff 0');
				$this->_out('/V '.($this->sig_obj_id + 1).' 0 R');
				$this->_out('>>');
				$this->_out('endobj');
				// signature		
				$this->_newobj();
				$this->_out('<<');
				$this->_putsignature();
				$this->_out('>>');
				$this->_out('endobj');
			}
			// Info
			$this->_newobj();
			$this->_out('<<');
			$this->_putinfo();
			$this->_out('>>');
			$this->_out('endobj');
			// Catalog
			$this->_newobj();
			$this->_out('<<');
			$this->_putcatalog();
			$this->_out('>>');
			$this->_out('endobj');
			// Cross-ref
			$o = $this->bufferlen;
			$this->_out('xref');
			$this->_out('0 '.($this->n + 1));
			$this->_out('0000000000 65535 f ');
			for ($i=1; $i <= $this->n; ++$i) {
				$this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
			}
			// Embedded Files
			if (isset($this->embeddedfiles) AND count($this->embeddedfiles) > 0) {
				$this->_out($this->embedded_start_obj_id.' '.count($this->embeddedfiles));
				foreach ($this->embeddedfiles as $filename => $filedata) {
					$this->_out(sprintf('%010d 00000 n ', $this->offsets[$filedata['n']]));
				}
			}
			// Annotation Objects
			if ($this->annot_obj_id > $this->annots_start_obj_id) {
				$this->_out(($this->annots_start_obj_id + 1).' '.($this->annot_obj_id - $this->annots_start_obj_id));
				for ($i = ($this->annots_start_obj_id + 1); $i <= $this->annot_obj_id; ++$i) {
					$this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
				}
			}
			// Javascript Objects
			if ($this->js_obj_id > $this->js_start_obj_id) {
				$this->_out(($this->js_start_obj_id + 1).' '.($this->js_obj_id - $this->js_start_obj_id));
				for ($i = ($this->js_start_obj_id + 1); $i <= $this->js_obj_id; ++$i) {
					$this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
				}
			}
			// Appearance streams XObjects
			if ($this->apxo_obj_id > $this->apxo_start_obj_id) {
				$this->_out(($this->apxo_start_obj_id + 1).' '.($this->apxo_obj_id - $this->apxo_start_obj_id));
				for ($i = ($this->apxo_start_obj_id + 1); $i <= $this->apxo_obj_id; ++$i) {
					$this->_out(sprintf('%010d 00000 n ', $this->offsets[$i]));
				}
			}
			//Trailer
			$this->_out('trailer');
			$this->_out('<<');
			$this->_puttrailer();
			$this->_out('>>');
			$this->_out('startxref');
			$this->_out($o);
			$this->_out('%%EOF');
			$this->state = 3; // end-of-doc
			if ($this->diskcache) {
				// remove temporary files used for images
				foreach ($this->imagekeys as $key) {
					// remove temporary files
					unlink($this->images[$key]);
				}
				foreach ($this->fontkeys as $key) {
					// remove temporary files
					unlink($this->fonts[$key]);
				}
			}
		}

		/**
		* Initialize a new page.
		* @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or PORTRAIT (default)</li><li>L or LANDSCAPE</li></ul>
		* @param mixed $format The format used for pages. It can be either one of the following values (case insensitive) or a custom format in the form of a two-element array containing the width and the height (expressed in the unit given by unit).<ul><li>4A0</li><li>2A0</li><li>A0</li><li>A1</li><li>A2</li><li>A3</li><li>A4 (default)</li><li>A5</li><li>A6</li><li>A7</li><li>A8</li><li>A9</li><li>A10</li><li>B0</li><li>B1</li><li>B2</li><li>B3</li><li>B4</li><li>B5</li><li>B6</li><li>B7</li><li>B8</li><li>B9</li><li>B10</li><li>C0</li><li>C1</li><li>C2</li><li>C3</li><li>C4</li><li>C5</li><li>C6</li><li>C7</li><li>C8</li><li>C9</li><li>C10</li><li>RA0</li><li>RA1</li><li>RA2</li><li>RA3</li><li>RA4</li><li>SRA0</li><li>SRA1</li><li>SRA2</li><li>SRA3</li><li>SRA4</li><li>LETTER</li><li>LEGAL</li><li>EXECUTIVE</li><li>FOLIO</li></ul>
		* @access protected
		*/
		protected function _beginpage($orientation='', $format='') {
			++$this->page;
			$this->setPageBuffer($this->page, '');
			// initialize array for graphics tranformation positions inside a page buffer
			$this->transfmrk[$this->page] = array();
			$this->state = 2;
			if ($this->empty_string($orientation)) {
				if (isset($this->CurOrientation)) {
					$orientation = $this->CurOrientation;
				} else {
					$orientation = 'P';
				}
			}
			if ($this->empty_string($format)) {
				$this->setPageOrientation($orientation);
			} else {
				$this->setPageFormat($format, $orientation);
			}
			if ($this->rtl) {
				$this->x = $this->w - $this->rMargin;
			} else {
				$this->x = $this->lMargin;
			}
			$this->y = $this->tMargin;
			if (isset($this->newpagegroup[$this->page])) {
				// start a new group
				$n = sizeof($this->pagegroups) + 1;
				$alias = '{nb'.$n.'}';
				$this->pagegroups[$alias] = 1;
				$this->currpagegroup = $alias;
			} elseif ($this->currpagegroup) {
				++$this->pagegroups[$this->currpagegroup];
			}
		}

		/**
		* Mark end of page.
		* @access protected
		*/
		protected function _endpage() {
			$this->setVisibility('all');
			$this->state = 1;
		}

		/**
		* Begin a new object and return the object number.
		* @return int object number
		* @access protected
		*/
		protected function _newobj() {
			++$this->n;
			$this->offsets[$this->n] = $this->bufferlen;
			$this->_out($this->n.' 0 obj');
			return $this->n;
		}

		/**
		* Underline text.
		* @param int $x X coordinate
		* @param int $y Y coordinate
		* @param string $txt text to underline
		* @access protected
		*/
		protected function _dounderline($x, $y, $txt) {
			$w = $this->GetStringWidth($txt);
			return $this->_dounderlinew($x, $y, $w);
		}
		
		/**
		* Line through text.
		* @param int $x X coordinate
		* @param int $y Y coordinate
		* @param string $txt text to linethrough
		* @access protected
		*/
		protected function _dolinethrough($x, $y, $txt) {
			$w = $this->GetStringWidth($txt);
			return $this->_dolinethroughw($x, $y, $w);
		}

		/**
		* Underline for rectangular text area.
		* @param int $x X coordinate
		* @param int $y Y coordinate
		* @param int $w width to underline
		* @access protected
		* @since 4.8.008 (2009-09-29)
		*/
		protected function _dounderlinew($x, $y, $w) {
			$up = $this->CurrentFont['up'];
			$ut = $this->CurrentFont['ut'];
			return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, ($this->h - ($y - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
		}
		
		/**
		* Line through for rectangular text area.
		* @param int $x X coordinate
		* @param int $y Y coordinate
		* @param string $txt text to linethrough
		* @access protected
		* @since 4.8.008 (2009-09-29)
		*/
		protected function _dolinethroughw($x, $y, $w) {
			$up = $this->CurrentFont['up'];
			$ut = $this->CurrentFont['ut'];
			return sprintf('%.2F %.2F %.2F %.2F re f', $x * $this->k, ($this->h - ($y - ($this->FontSize/2) - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
		}
		
		/**
		* Read a 4-byte integer from file.
		* @param string $f file name.
		* @return 4-byte integer
		* @access protected
		*/
		protected function _freadint($f) {
			$a = unpack('Ni', fread($f, 4));
			return $a['i'];
		}
		
		/**
		* Add "\" before "\", "(" and ")"
		* @param string $s string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _escape($s) {
			// the chr(13) substitution fixes the Bugs item #1421290.
			return strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r'));
		}
		
		/**
		* Format a data string for meta information
		* @param string $s date string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _datastring($s) {
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			return '('. $this->_escape($s).')';
		}

		/**
		* Returns a formatted date for meta information
		* @return string escaped date string.
		* @access protected
		* @since 4.6.028 (2009-08-25)
		*/
		protected function _datestring() {
			$current_time = substr_replace(date('YmdHisO'), '\'', (0 - 2), 0).'\'';
			return $this->_datastring('D:'.$current_time);
		}

		/**
		* Format a text string for meta information
		* @param string $s string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _textstring($s) {
			if ($this->isunicode) {
				//Convert string to UTF-16BE
				$s = $this->UTF8ToUTF16BE($s, true);
			}
			return $this->_datastring($s);
		}
				
		/**
		* Format a text string
		* @param string $s string to escape.
		* @return string escaped string.
		* @access protected
		*/
		protected function _escapetext($s) {
			if ($this->isunicode) {
				if (($this->CurrentFont['type'] == 'core') OR ($this->CurrentFont['type'] == 'TrueType') OR ($this->CurrentFont['type'] == 'Type1')) {
					$s = $this->UTF8ToLatin1($s);
				} else {
					//Convert string to UTF-16BE and reverse RTL language
					$s = $this->utf8StrRev($s, false, $this->tmprtl);
				}
			}
			return $this->_escape($s);
		}
		
		/**
		* Output a stream.
		* @param string $s string to output.
		* @access protected
		*/
		protected function _putstream($s) {
			if ($this->encrypted) {
				$s = $this->_RC4($this->_objectkey($this->n), $s);
			}
			$this->_out('stream');
			$this->_out($s);
			$this->_out('endstream');
		}
		
		/**
		* Output a string to the document.
		* @param string $s string to output.
		* @access protected
		*/
		protected function _out($s) {
			if ($this->state == 2) {
				if ((!$this->InFooter) AND isset($this->footerlen[$this->page]) AND ($this->footerlen[$this->page] > 0)) {
					// puts data before page footer
					$pagebuff = $this->getPageBuffer($this->page);
					$page = substr($pagebuff, 0, -$this->footerlen[$this->page]);
					$footer = substr($pagebuff, -$this->footerlen[$this->page]);
					$this->setPageBuffer($this->page, $page.$s."\n".$footer);
					// update footer position
					$this->footerpos[$this->page] += strlen($s."\n");	
				} else {
					$this->setPageBuffer($this->page, $s."\n", true);
				}
			} else {
				$this->setBuffer($s."\n");
			}
		}
		
		 /**
		 * Converts UTF-8 strings to codepoints array.<br>
		 * Invalid byte sequences will be replaced with 0xFFFD (replacement character)<br>
		 * Based on: http://www.faqs.org/rfcs/rfc3629.html
		 * <pre>
		 * 	  Char. number range  |        UTF-8 octet sequence
		 *       (hexadecimal)    |              (binary)
		 *    --------------------+-----------------------------------------------
		 *    0000 0000-0000 007F | 0xxxxxxx
		 *    0000 0080-0000 07FF | 110xxxxx 10xxxxxx
		 *    0000 0800-0000 FFFF | 1110xxxx 10xxxxxx 10xxxxxx
		 *    0001 0000-0010 FFFF | 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
		 *    ---------------------------------------------------------------------
		 *
		 *   ABFN notation:
		 *   ---------------------------------------------------------------------
		 *   UTF8-octets = *( UTF8-char )
		 *   UTF8-char   = UTF8-1 / UTF8-2 / UTF8-3 / UTF8-4
		 *   UTF8-1      = %x00-7F
		 *   UTF8-2      = %xC2-DF UTF8-tail
		 *
		 *   UTF8-3      = %xE0 %xA0-BF UTF8-tail / %xE1-EC 2( UTF8-tail ) /
		 *                 %xED %x80-9F UTF8-tail / %xEE-EF 2( UTF8-tail )
		 *   UTF8-4      = %xF0 %x90-BF 2( UTF8-tail ) / %xF1-F3 3( UTF8-tail ) /
		 *                 %xF4 %x80-8F 2( UTF8-tail )
		 *   UTF8-tail   = %x80-BF
		 *   ---------------------------------------------------------------------
		 * </pre>
		 * @param string $str string to process.
		 * @return array containing codepoints (UTF-8 characters values)
		 * @access protected
		 * @author Nicola Asuni
		 * @since 1.53.0.TC005 (2005-01-05)
		 */
		protected function UTF8StringToArray($str) {
			if (isset($this->cache_UTF8StringToArray['_'.$str])) {
				// return cached value
				return($this->cache_UTF8StringToArray['_'.$str]);
			}
			// check cache size
			if ($this->cache_size_UTF8StringToArray >= $this->cache_maxsize_UTF8StringToArray) {
				// remove first element
				array_shift($this->cache_UTF8StringToArray);
			}
			++$this->cache_size_UTF8StringToArray;
			if (!$this->isunicode) {
				// split string into array of equivalent codes
				$strarr = array();
				$strlen = strlen($str);
				for ($i=0; $i < $strlen; ++$i) {
					$strarr[] = ord($str{$i});
				}
				// insert new value on cache
				$this->cache_UTF8StringToArray['_'.$str] = $strarr;
				return $strarr;
			}
			$unicode = array(); // array containing unicode values
			$bytes  = array(); // array containing single character byte sequences
			$numbytes  = 1; // number of octetc needed to represent the UTF-8 character
			$str .= ''; // force $str to be a string
			$length = strlen($str);
			for ($i = 0; $i < $length; ++$i) {
				$char = ord($str{$i}); // get one string character at time
				if (count($bytes) == 0) { // get starting octect
					if ($char <= 0x7F) {
						$unicode[] = $char; // use the character "as is" because is ASCII
						$numbytes = 1;
					} elseif (($char >> 0x05) == 0x06) { // 2 bytes character (0x06 = 110 BIN)
						$bytes[] = ($char - 0xC0) << 0x06; 
						$numbytes = 2;
					} elseif (($char >> 0x04) == 0x0E) { // 3 bytes character (0x0E = 1110 BIN)
						$bytes[] = ($char - 0xE0) << 0x0C; 
						$numbytes = 3;
					} elseif (($char >> 0x03) == 0x1E) { // 4 bytes character (0x1E = 11110 BIN)
						$bytes[] = ($char - 0xF0) << 0x12; 
						$numbytes = 4;
					} else {
						// use replacement character for other invalid sequences
						$unicode[] = 0xFFFD;
						$bytes = array();
						$numbytes = 1;
					}
				} elseif (($char >> 0x06) == 0x02) { // bytes 2, 3 and 4 must start with 0x02 = 10 BIN
					$bytes[] = $char - 0x80;
					if (count($bytes) == $numbytes) {
						// compose UTF-8 bytes to a single unicode value
						$char = $bytes[0];
						for ($j = 1; $j < $numbytes; ++$j) {
							$char += ($bytes[$j] << (($numbytes - $j - 1) * 0x06));
						}
						if ((($char >= 0xD800) AND ($char <= 0xDFFF)) OR ($char >= 0x10FFFF)) {
							/* The definition of UTF-8 prohibits encoding character numbers between
							U+D800 and U+DFFF, which are reserved for use with the UTF-16
							encoding form (as surrogate pairs) and do not directly represent
							characters. */
							$unicode[] = 0xFFFD; // use replacement character
						} else {
							$unicode[] = $char; // add char to array
						}
						// reset data for next char
						$bytes = array(); 
						$numbytes = 1;
					}
				} else {
					// use replacement character for other invalid sequences
					$unicode[] = 0xFFFD;
					$bytes = array();
					$numbytes = 1;
				}
			}
			// insert new value on cache
			$this->cache_UTF8StringToArray['_'.$str] = $unicode;
			return $unicode;
		}
		
		/**
		 * Converts UTF-8 strings to UTF16-BE.<br>
		 * @param string $str string to process.
		 * @param boolean $setbom if true set the Byte Order Mark (BOM = 0xFEFF)
		 * @return string
		 * @access protected
		 * @author Nicola Asuni
		 * @since 1.53.0.TC005 (2005-01-05)
		 * @uses UTF8StringToArray(), arrUTF8ToUTF16BE()
		 */
		protected function UTF8ToUTF16BE($str, $setbom=true) {
			if (!$this->isunicode) {
				return $str; // string is not in unicode
			}
			$unicode = $this->UTF8StringToArray($str); // array containing UTF-8 unicode values
			return $this->arrUTF8ToUTF16BE($unicode, $setbom);
		}
		
		/**
		 * Converts UTF-8 strings to Latin1 when using the standard 14 core fonts.<br>
		 * @param string $str string to process.
		 * @return string
		 * @author Andrew Whitehead, Nicola Asuni
		 * @access protected
		 * @since 3.2.000 (2008-06-23)
		 */
		protected function UTF8ToLatin1($str) {
			global $utf8tolatin;
			if (!$this->isunicode) {
				return $str; // string is not in unicode
			}
			$outstr = ''; // string to be returned
			$unicode = $this->UTF8StringToArray($str); // array containing UTF-8 unicode values
			foreach ($unicode as $char) {
				if ($char < 256) {
					$outstr .= chr($char);
				} elseif (array_key_exists($char, $utf8tolatin)) {
					// map from UTF-8
					$outstr .= chr($utf8tolatin[$char]);
				} elseif ($char == 0xFFFD) {
					// skip
				} else {
					$outstr .= '?';
				}
			}
			return $outstr;
		}

		/**
		 * Converts array of UTF-8 characters to UTF16-BE string.<br>
		 * Based on: http://www.faqs.org/rfcs/rfc2781.html
	 	 * <pre>
		 *   Encoding UTF-16:
		 * 
 		 *   Encoding of a single character from an ISO 10646 character value to
		 *    UTF-16 proceeds as follows. Let U be the character number, no greater
		 *    than 0x10FFFF.
		 * 
		 *    1) If U < 0x10000, encode U as a 16-bit unsigned integer and
		 *       terminate.
		 * 
		 *    2) Let U' = U - 0x10000. Because U is less than or equal to 0x10FFFF,
		 *       U' must be less than or equal to 0xFFFFF. That is, U' can be
		 *       represented in 20 bits.
		 * 
		 *    3) Initialize two 16-bit unsigned integers, W1 and W2, to 0xD800 and
		 *       0xDC00, respectively. These integers each have 10 bits free to
		 *       encode the character value, for a total of 20 bits.
		 * 
		 *    4) Assign the 10 high-order bits of the 20-bit U' to the 10 low-order
		 *       bits of W1 and the 10 low-order bits of U' to the 10 low-order
		 *       bits of W2. Terminate.
		 * 
		 *    Graphically, steps 2 through 4 look like:
		 *    U' = yyyyyyyyyyxxxxxxxxxx
		 *    W1 = 110110yyyyyyyyyy
		 *    W2 = 110111xxxxxxxxxx
		 * </pre>
		 * @param array $unicode array containing UTF-8 unicode values
		 * @param boolean $setbom if true set the Byte Order Mark (BOM = 0xFEFF)
		 * @return string
		 * @access protected
		 * @author Nicola Asuni
		 * @since 2.1.000 (2008-01-08)
		 * @see UTF8ToUTF16BE()
		 */
		protected function arrUTF8ToUTF16BE($unicode, $setbom=true) {
			$outstr = ''; // string to be returned
			if ($setbom) {
				$outstr .= "\xFE\xFF"; // Byte Order Mark (BOM)
			}
			foreach ($unicode as $char) {
				if ($char == 0xFFFD) {
					$outstr .= "\xFF\xFD"; // replacement character
				} elseif ($char < 0x10000) {
					$outstr .= chr($char >> 0x08);
					$outstr .= chr($char & 0xFF);
				} else {
					$char -= 0x10000;
					$w1 = 0xD800 | ($char >> 0x10);
					$w2 = 0xDC00 | ($char & 0x3FF);	
					$outstr .= chr($w1 >> 0x08);
					$outstr .= chr($w1 & 0xFF);
					$outstr .= chr($w2 >> 0x08);
					$outstr .= chr($w2 & 0xFF);
				}
			}
			return $outstr;
		}
		// ====================================================
		
		/**
	 	 * Set header font.
		 * @param array $font font
		 * @access public
		 * @since 1.1
		 */
		public function setHeaderFont($font) {
			$this->header_font = $font;
		}
		
		/**
	 	 * Get header font.
	 	 * @return array()
		 * @access public
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getHeaderFont() {
			return $this->header_font;
		}
		
		/**
	 	 * Set footer font.
		 * @param array $font font
		 * @access public
		 * @since 1.1
		 */
		public function setFooterFont($font) {
			$this->footer_font = $font;
		}
		
		/**
	 	 * Get Footer font.
	 	 * @return array()
		 * @access public
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getFooterFont() {
			return $this->footer_font;
		}
		
		/**
	 	 * Set language array.
		 * @param array $language
		 * @access public
		 * @since 1.1
		 */
		public function setLanguageArray($language) {
			$this->l = $language;
			if (isset($this->l['a_meta_dir'])) {
				$this->rtl = $this->l['a_meta_dir']=='rtl' ? true : false;
			} else {
				$this->rtl = false;
			}
		}
		
		/**
		 * Returns the PDF data.
		 * @access public
		 */
		public function getPDFData() {
			if ($this->state < 3) {
				$this->Close();
			}
			return $this->buffer;
		}
				
		/**
		 * Output anchor link.
		 * @param string $url link URL or internal link (i.e.: &lt;a href="#23"&gt;link to page 23&lt;/a&gt;)
		 * @param string $name link name
		 * @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		 * @param boolean $firstline if true prints only the first line and return the remaining string.
		 * @param array $color array of RGB text color
		 * @param string $style font style (U, D, B, I)
		 * @return the number of cells used or the remaining text if $firstline = true;
		 * @access public
		 */
		public function addHtmlLink($url, $name, $fill=0, $firstline=false, $color='', $style=-1) {
			if (!$this->empty_string($url) AND ($url{0} == '#')) {
				// convert url to internal link
				$page = intval(substr($url, 1));
				$url = $this->AddLink();
				$this->SetLink($url, 0, $page);
			}
			// store current settings
			$prevcolor = $this->fgcolor;
			$prevstyle = $this->FontStyle;
			if (empty($color)) {
				$this->SetTextColorArray($this->htmlLinkColorArray);
			} else {
				$this->SetTextColorArray($color);
			}
			if ($style == -1) {
				$this->SetFont('', $this->FontStyle.$this->htmlLinkFontStyle);
			} else {
				$this->SetFont('', $this->FontStyle.$style);
			}
			$ret = $this->Write($this->lasth, $name, $url, $fill, '', false, 0, $firstline);
			// restore settings
			$this->SetFont('', $prevstyle);
			$this->SetTextColorArray($prevcolor);
			return $ret;
		}
		
		/**
		 * Returns an associative array (keys: R,G,B) from an html color name or a six-digit or three-digit hexadecimal color representation (i.e. #3FE5AA or #7FF).
		 * @param string $color html color 
		 * @return array RGB color or false in case of error.
		 * @access public
		 */		
		public function convertHTMLColorToDec($color='#FFFFFF') {
			global $webcolor;
			$returncolor = false;
			$color = preg_replace('/[\s]*/', '', $color); // remove extra spaces
			$color = strtolower($color);
			if (($dotpos = strpos($color, '.')) !== false) {
				// remove class parent (i.e.: color.red)
				$color = substr($color, ($dotpos + 1));
			}
			if (strlen($color) == 0) {
				return false;
			}
			if (substr($color, 0, 3) == 'rgb') {
				$codes = substr($color, 4);
				$codes = str_replace(')', '', $codes);
				$returncolor = explode(',', $codes, 3);
				return $returncolor;
			}
			if (substr($color, 0, 1) != '#') {
				// decode color name
				if (isset($webcolor[$color])) {
					$color_code = $webcolor[$color];
				} else {
					return false;
				}
			} else {
				$color_code = substr($color, 1);
			}
			switch (strlen($color_code)) {
				case 3: {
					// three-digit hexadecimal representation
					$r = substr($color_code, 0, 1);
					$g = substr($color_code, 1, 1);
					$b = substr($color_code, 2, 1);
					$returncolor['R'] = hexdec($r.$r);
					$returncolor['G'] = hexdec($g.$g);
					$returncolor['B'] = hexdec($b.$b);
					break;
				}
				case 6: {
					// six-digit hexadecimal representation
					$returncolor['R'] = hexdec(substr($color_code, 0, 2));
					$returncolor['G'] = hexdec(substr($color_code, 2, 2));
					$returncolor['B'] = hexdec(substr($color_code, 4, 2));
					break;
				}
			}
			return $returncolor;
		}
		
		/**
		 * Converts pixels to User's Units.
		 * @param int $px pixels
		 * @return float value in user's unit
		 * @access public
		 * @see setImageScale(), getImageScale()
		 */
		public function pixelsToUnits($px) {
			return ($px / ($this->imgscale * $this->k));
		}
			
		/**
		 * Reverse function for htmlentities. 
		 * Convert entities in UTF-8.
		 * @param $text_to_convert Text to convert.
		 * @return string converted
		 * @access public
		*/
		public function unhtmlentities($text_to_convert) {
			return html_entity_decode($text_to_convert, ENT_QUOTES, $this->encoding);
		}
		
		// ENCRYPTION METHODS ----------------------------------
		// SINCE 2.0.000 (2008-01-02)
		
		/**
		* Compute encryption key depending on object number where the encrypted data is stored
		* @param int $n object number
		* @access protected
		* @since 2.0.000 (2008-01-02)
		*/
		protected function _objectkey($n) {
			return substr($this->_md5_16($this->encryption_key.pack('VXxx', $n)), 0, 10);
		}
		
		/**
		 * Put encryption on PDF document.
		 * @access protected
		 * @since 2.0.000 (2008-01-02)
		 */
		protected function _putencryption() {
			$this->_out('/Filter /Standard');
			$this->_out('/V 1');
			$this->_out('/R 2');
			$this->_out('/O ('.$this->_escape($this->Ovalue).')');
			$this->_out('/U ('.$this->_escape($this->Uvalue).')');
			$this->_out('/P '.$this->Pvalue);
		}
		
		/**
		* Returns the input text exrypted using RC4 algorithm and the specified key.
		* RC4 is the standard encryption algorithm used in PDF format
		* @param string $key encryption key
		* @param String $text input text to be encrypted
		* @return String encrypted text
		* @access protected
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _RC4($key, $text) {
			if ($this->last_rc4_key != $key) {
				$k = str_repeat($key, ((256 / strlen($key)) + 1));
				$rc4 = range(0, 255);
				$j = 0;
				for ($i = 0; $i < 256; ++$i) {
					$t = $rc4[$i];
					$j = ($j + $t + ord($k{$i})) % 256;
					$rc4[$i] = $rc4[$j];
					$rc4[$j] = $t;
				}
				$this->last_rc4_key = $key;
				$this->last_rc4_key_c = $rc4;
			} else {
				$rc4 = $this->last_rc4_key_c;
			}
			$len = strlen($text);
			$a = 0;
			$b = 0;
			$out = '';
			for ($i = 0; $i < $len; ++$i) {
				$a = ($a + 1) % 256;
				$t = $rc4[$a];
				$b = ($b + $t) % 256;
				$rc4[$a] = $rc4[$b];
				$rc4[$b] = $t;
				$k = $rc4[($rc4[$a] + $rc4[$b]) % 256];
				$out .= chr(ord($text{$i}) ^ $k);
			}
			return $out;
		}
		
		/**
		* Encrypts a string using MD5 and returns it's value as a binary string.
		* @param string $str input string
		* @return String MD5 encrypted binary string
		* @access protected
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _md5_16($str) {
			return pack('H*', md5($str));
		}
		
		/**
		* Compute O value (used for RC4 encryption)
		* @param String $user_pass user password
		* @param String $owner_pass user password
		* @return String O value
		* @access protected
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _Ovalue($user_pass, $owner_pass) {
			$tmp = $this->_md5_16($owner_pass);
			$owner_RC4_key = substr($tmp, 0, 5);
			return $this->_RC4($owner_RC4_key, $user_pass);
		}
		
		/**
		* Compute U value (used for RC4 encryption)
		* @return String U value
		* @access protected
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _Uvalue() {
			return $this->_RC4($this->encryption_key, $this->padding);
		}
		
		/**
		* Compute encryption key
		* @param String $user_pass user password
		* @param String $owner_pass user password
		* @param String $protection protection type
		* @access protected
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		protected function _generateencryptionkey($user_pass, $owner_pass, $protection) {
			// Pad passwords
			$user_pass = substr($user_pass.$this->padding, 0, 32);
			$owner_pass = substr($owner_pass.$this->padding, 0, 32);
			// Compute O value
			$this->Ovalue = $this->_Ovalue($user_pass, $owner_pass);
			// Compute encyption key
			$tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
			$this->encryption_key = substr($tmp, 0, 5);
			// Compute U value
			$this->Uvalue = $this->_Uvalue();
			// Compute P value
			$this->Pvalue = -(($protection^255) + 1);
		}
		
		/**
		* Set document protection
		* The permission array is composed of values taken from the following ones:
		* - copy: copy text and images to the clipboard
		* - print: print the document
		* - modify: modify it (except for annotations and forms)
		* - annot-forms: add annotations and forms 
		* Remark: the protection against modification is for people who have the full Acrobat product.
		* If you don't set any password, the document will open as usual. If you set a user password, the PDF viewer will ask for it before displaying the document. The master password, if different from the user one, can be used to get full access.
		* Note: protecting a document requires to encrypt it, which increases the processing time a lot. This can cause a PHP time-out in some cases, especially if the document contains images or fonts.
		* @param Array $permissions the set of permissions. Empty by default (only viewing is allowed). (print, modify, copy, annot-forms)
		* @param String $user_pass user password. Empty by default.
		* @param String $owner_pass owner password. If not specified, a random value is used.
		* @access public
		* @since 2.0.000 (2008-01-02)
		* @author Klemen Vodopivec
		*/
		public function SetProtection($permissions=array(), $user_pass='', $owner_pass=null) {
			$options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32);
			$protection = 192;
			foreach ($permissions as $permission) {
				if (!isset($options[$permission])) {
					$this->Error('Incorrect permission: '.$permission);
				}
				$protection += $options[$permission];
			}
			if ($owner_pass === null) {
				$owner_pass = uniqid(rand());
			}
			$this->encrypted = true;
			$this->_generateencryptionkey($user_pass, $owner_pass, $protection);
		}
		
		// END OF ENCRYPTION FUNCTIONS -------------------------
		
		// START TRANSFORMATIONS SECTION -----------------------
		
		/**
		* Starts a 2D tranformation saving current graphic state.
		* This function must be called before scaling, mirroring, translation, rotation and skewing.
		* Use StartTransform() before, and StopTransform() after the transformations to restore the normal behavior.
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function StartTransform() {
			$this->_out('q');
			$this->transfmrk[$this->page][] = $this->pagelen[$this->page];
			++$this->transfmatrix_key;
			$this->transfmatrix[$this->transfmatrix_key] = array();
		}
		
		/**
		* Stops a 2D tranformation restoring previous graphic state.
		* This function must be called after scaling, mirroring, translation, rotation and skewing.
		* Use StartTransform() before, and StopTransform() after the transformations to restore the normal behavior.
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function StopTransform() {
			$this->_out('Q');
			if (isset($this->transfmatrix[$this->transfmatrix_key])) {
				array_pop($this->transfmatrix[$this->transfmatrix_key]);
				--$this->transfmatrix_key;
			}
			array_pop($this->transfmrk[$this->page]);
		}
		/**
		* Horizontal Scaling.
		* @param float $s_x scaling factor for width as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function ScaleX($s_x, $x='', $y='') {
			$this->Scale($s_x, 100, $x, $y);
		}
		
		/**
		* Vertical Scaling.
		* @param float $s_y scaling factor for height as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function ScaleY($s_y, $x='', $y='') {
			$this->Scale(100, $s_y, $x, $y);
		}
		
		/**
		* Vertical and horizontal proportional Scaling.
		* @param float $s scaling factor for width and height as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function ScaleXY($s, $x='', $y='') {
			$this->Scale($s, $s, $x, $y);
		}
		
		/**
		* Vertical and horizontal non-proportional Scaling.
		* @param float $s_x scaling factor for width as percent. 0 is not allowed.
		* @param float $s_y scaling factor for height as percent. 0 is not allowed.
		* @param int $x abscissa of the scaling center. Default is current x position
		* @param int $y ordinate of the scaling center. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Scale($s_x, $s_y, $x='', $y='') {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			if (($s_x == 0) OR ($s_y == 0)) {
				$this->Error('Please do not use values equal to zero for scaling');
			}
			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;
			//calculate elements of transformation matrix
			$s_x /= 100;
			$s_y /= 100;
			$tm[0] = $s_x;
			$tm[1] = 0;
			$tm[2] = 0;
			$tm[3] = $s_y;
			$tm[4] = $x * (1 - $s_x);
			$tm[5] = $y * (1 - $s_y);
			//scale the coordinate system
			$this->Transform($tm);
		}
		
		/**
		* Horizontal Mirroring.
		* @param int $x abscissa of the point. Default is current x position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorH($x='') {
			$this->Scale(-100, 100, $x);
		}
		
		/**
		* Verical Mirroring.
		* @param int $y ordinate of the point. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorV($y='') {
			$this->Scale(100, -100, '', $y);
		}
		
		/**
		* Point reflection mirroring.
		* @param int $x abscissa of the point. Default is current x position
		* @param int $y ordinate of the point. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorP($x='',$y='') {
			$this->Scale(-100, -100, $x, $y);
		}
		
		/**
		* Reflection against a straight line through point (x, y) with the gradient angle (angle).
		* @param float $angle gradient angle of the straight line. Default is 0 (horizontal line).
		* @param int $x abscissa of the point. Default is current x position
		* @param int $y ordinate of the point. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function MirrorL($angle=0, $x='',$y='') {
			$this->Scale(-100, 100, $x, $y);
			$this->Rotate(-2*($angle-90), $x, $y);
		}
		
		/**
		* Translate graphic object horizontally.
		* @param int $t_x movement to the right (or left for RTL)
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function TranslateX($t_x) {
			$this->Translate($t_x, 0);
		}
		
		/**
		* Translate graphic object vertically.
		* @param int $t_y movement to the bottom
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function TranslateY($t_y) {
			$this->Translate(0, $t_y);
		}
		
		/**
		* Translate graphic object horizontally and vertically.
		* @param int $t_x movement to the right
		* @param int $t_y movement to the bottom
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Translate($t_x, $t_y) {
			if ($this->rtl) {
				$t_x = -$t_x;
			}
			//calculate elements of transformation matrix
			$tm[0] = 1;
			$tm[1] = 0;
			$tm[2] = 0;
			$tm[3] = 1;
			$tm[4] = $t_x * $this->k;
			$tm[5] = -$t_y * $this->k;
			//translate the coordinate system
			$this->Transform($tm);
		}
		
		/**
		* Rotate object.
		* @param float $angle angle in degrees for counter-clockwise rotation
		* @param int $x abscissa of the rotation center. Default is current x position
		* @param int $y ordinate of the rotation center. Default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Rotate($angle, $x='', $y='') {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($this->rtl) {
				$x = $this->w - $x;
				$angle = -$angle;
			}
			$y = ($this->h - $y) * $this->k;
			$x *= $this->k;
			//calculate elements of transformation matrix
			$tm[0] = cos(deg2rad($angle));
			$tm[1] = sin(deg2rad($angle));
			$tm[2] = -$tm[1];
			$tm[3] = $tm[0];
			$tm[4] = $x + ($tm[1] * $y) - ($tm[0] * $x);
			$tm[5] = $y - ($tm[0] * $y) - ($tm[1] * $x);
			//rotate the coordinate system around ($x,$y)
			$this->Transform($tm);
		}
		
		/**
		* Skew horizontally.
		* @param float $angle_x angle in degrees between -90 (skew to the left) and 90 (skew to the right)
		* @param int $x abscissa of the skewing center. default is current x position
		* @param int $y ordinate of the skewing center. default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function SkewX($angle_x, $x='', $y='') {
			$this->Skew($angle_x, 0, $x, $y);
		}
		
		/**
		* Skew vertically.
		* @param float $angle_y angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
		* @param int $x abscissa of the skewing center. default is current x position
		* @param int $y ordinate of the skewing center. default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function SkewY($angle_y, $x='', $y='') {
			$this->Skew(0, $angle_y, $x, $y);
		}
		
		/**
		* Skew.
		* @param float $angle_x angle in degrees between -90 (skew to the left) and 90 (skew to the right)
		* @param float $angle_y angle in degrees between -90 (skew to the bottom) and 90 (skew to the top)
		* @param int $x abscissa of the skewing center. default is current x position
		* @param int $y ordinate of the skewing center. default is current y position
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		public function Skew($angle_x, $angle_y, $x='', $y='') {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($this->rtl) {
				$x = $this->w - $x;
				$angle_x = -$angle_x;
			}
			if (($angle_x <= -90) OR ($angle_x >= 90) OR ($angle_y <= -90) OR ($angle_y >= 90)) {
				$this->Error('Please use values between -90 and +90 degrees for Skewing.');
			}
			$x *= $this->k;
			$y = ($this->h - $y) * $this->k;
			//calculate elements of transformation matrix
			$tm[0] = 1;
			$tm[1] = tan(deg2rad($angle_y));
			$tm[2] = tan(deg2rad($angle_x));
			$tm[3] = 1;
			$tm[4] = -$tm[2] * $y;
			$tm[5] = -$tm[1] * $x;
			//skew the coordinate system
			$this->Transform($tm);
		}
		
		/**
		* Apply graphic transformations.
		* @access protected
		* @since 2.1.000 (2008-01-07)
		* @see StartTransform(), StopTransform()
		*/
		protected function Transform($tm) {
			$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $tm[0], $tm[1], $tm[2], $tm[3], $tm[4], $tm[5]));
			// add tranformation matrix
			$this->transfmatrix[$this->transfmatrix_key][] = array('a' => $tm[0], 'b' => $tm[1], 'c' => $tm[2], 'd' => $tm[3], 'e' => $tm[4], 'f' => $tm[5]);
			// update tranformation mark
			if (end($this->transfmrk[$this->page]) !== false) {
				$key = key($this->transfmrk[$this->page]);
				$this->transfmrk[$this->page][$key] = $this->pagelen[$this->page];
			}
		}
		
		// END TRANSFORMATIONS SECTION -------------------------
		
		
		// START GRAPHIC FUNCTIONS SECTION ---------------------
		// The following section is based on the code provided by David Hernandez Sanz
		
		/**
		* Defines the line width. By default, the value equals 0.2 mm. The method can be called before the first page is created and the value is retained from page to page.
		* @param float $width The width.
		* @access public
		* @since 1.0
		* @see Line(), Rect(), Cell(), MultiCell()
		*/
		public function SetLineWidth($width) {
			//Set line width
			$this->LineWidth = $width;
			$this->linestyleWidth = sprintf('%.2F w', ($width * $this->k));
			if ($this->page > 0) {
				$this->_out($this->linestyleWidth);
			}
		}
		
		/**
		* Returns the current the line width.
		* @return int Line width 
		* @access public
		* @since 2.1.000 (2008-01-07)
		* @see Line(), SetLineWidth()
		*/
		public function GetLineWidth() {
			return $this->LineWidth;
		}
		
		/**
		* Set line style.
		* @param array $style Line style. Array with keys among the following:
		* <ul>
		*	 <li>width (float): Width of the line in user units.</li>
		*	 <li>cap (string): Type of cap to put on the line. Possible values are:
		* butt, round, square. The difference between "square" and "butt" is that
		* "square" projects a flat end past the end of the line.</li>
		*	 <li>join (string): Type of join. Possible values are: miter, round,
		* bevel.</li>
		*	 <li>dash (mixed): Dash pattern. Is 0 (without dash) or string with
		* series of length values, which are the lengths of the on and off dashes.
		* For example: "2" represents 2 on, 2 off, 2 on, 2 off, ...; "2,1" is 2 on,
		* 1 off, 2 on, 1 off, ...</li>
		*	 <li>phase (integer): Modifier on the dash pattern which is used to shift
		* the point at which the pattern starts.</li>
		*	 <li>color (array): Draw color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K).</li>
		* </ul>
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function SetLineStyle($style) {
			if (!is_array($style)) {
				return;
			}
			extract($style);
			if (isset($width)) {
				$width_prev = $this->LineWidth;
				$this->SetLineWidth($width);
				$this->LineWidth = $width_prev;
			}
			if (isset($cap)) {
				$ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
				if (isset($ca[$cap])) {
					$this->linestyleCap = $ca[$cap].' J';
					$this->_out($this->linestyleCap);
				}
			}
			if (isset($join)) {
				$ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
				if (isset($ja[$join])) {
					$this->linestyleJoin = $ja[$join].' j';
					$this->_out($this->linestyleJoin);
				}
			}
			if (isset($dash)) {
				$dash_string = '';
				if ($dash) {
					if (preg_match('/^.+,/', $dash) > 0) {
						$tab = explode(',', $dash);
					} else {
						$tab = array($dash);
					}
					$dash_string = '';
					foreach ($tab as $i => $v) {
						if ($i) {
							$dash_string .= ' ';
						}
						$dash_string .= sprintf("%.2F", $v);
					}
				}
				if (!isset($phase) OR !$dash) {
					$phase = 0;
				}
				$this->linestyleDash = sprintf("[%s] %.2F d", $dash_string, $phase);
				$this->_out($this->linestyleDash);
			}
			if (isset($color)) {
				$this->SetDrawColorArray($color);
			}
		}
		
		/*
		* Set a draw point.
		* @param float $x Abscissa of point.
		* @param float $y Ordinate of point.
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outPoint($x, $y) {
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			$this->_out(sprintf("%.2F %.2F m", $x * $this->k, ($this->h - $y) * $this->k));
		}
		
		/*
		* Draws a line from last draw point.
		* @param float $x Abscissa of end point.
		* @param float $y Ordinate of end point.
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outLine($x, $y) {
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			$this->_out(sprintf("%.2F %.2F l", $x * $this->k, ($this->h - $y) * $this->k));
		}
		
		/**
		* Draws a rectangle.
		* @param float $x Abscissa of upper-left corner (or upper-right corner for RTL language).
		* @param float $y Ordinate of upper-left corner (or upper-right corner for RTL language).
		* @param float $w Width.
		* @param float $h Height.
		* @param string $op options
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outRect($x, $y, $w, $h, $op) {
			if ($this->rtl) {
				$x = $this->w - $x - $w;
			}
			$this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k, $op));
		}
		
		/*
		* Draws a Bezier curve from last draw point.
		* The Bezier curve is a tangent to the line between the control points at either end of the curve.
		* @param float $x1 Abscissa of control point 1.
		* @param float $y1 Ordinate of control point 1.
		* @param float $x2 Abscissa of control point 2.
		* @param float $y2 Ordinate of control point 2.
		* @param float $x3 Abscissa of end point.
		* @param float $y3 Ordinate of end point.
		* @access protected
		* @since 2.1.000 (2008-01-08)
		*/
		protected function _outCurve($x1, $y1, $x2, $y2, $x3, $y3) {
			if ($this->rtl) {
				$x1 = $this->w - $x1;
				$x2 = $this->w - $x2;
				$x3 = $this->w - $x3;
			}
			$this->_out(sprintf("%.2F %.2F %.2F %.2F %.2F %.2F c", $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
		}
		
		/**
		* Draws a line between two points.
		* @param float $x1 Abscissa of first point.
		* @param float $y1 Ordinate of first point.
		* @param float $x2 Abscissa of second point.
		* @param float $y2 Ordinate of second point.
		* @param array $style Line style. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @access public
		* @since 1.0
		* @see SetLineWidth(), SetDrawColor(), SetLineStyle()
		*/
		public function Line($x1, $y1, $x2, $y2, $style=array()) {
			if (is_array($style)) {
				$this->SetLineStyle($style);
			}
			$this->_outPoint($x1, $y1);
			$this->_outLine($x2, $y2);
			$this->_out(' S');
		}
		
		/**
		* Draws a rectangle.
		* @param float $x Abscissa of upper-left corner (or upper-right corner for RTL language).
		* @param float $y Ordinate of upper-left corner (or upper-right corner for RTL language).
		* @param float $w Width.
		* @param float $h Height.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $border_style Border style of rectangle. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all borders. Array like for {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>L, T, R, B or combinations: Line style of left, top, right or bottom border. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the border. Default value: default line style (empty array).
		* @param array $border_style Border style of rectangle. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @since 1.0
		* @see SetLineStyle()
		*/
		public function Rect($x, $y, $w, $h, $style='', $border_style=array(), $fill_color=array()) {
			if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case 'F': {
					$op = 'f';
					$border_style = array();
					$this->_outRect($x, $y, $w, $h, $op);
					break;
				}
				case 'DF':
				case 'FD': {
					if ((!$border_style) OR (isset($border_style['all']))) {
						$op = 'B';
						if (isset($border_style['all'])) {
							$this->SetLineStyle($border_style['all']);
							$border_style = array();
						}
					} else {
						$op = 'f';
					}
					$this->_outRect($x, $y, $w, $h, $op);
					break;
				}
				case 'CNZ': {
					$op = 'W n';
					$this->_outRect($x, $y, $w, $h, $op);
					break;
				}
				case 'CEO': {
					$op = 'W* n';
					$this->_outRect($x, $y, $w, $h, $op);
					break;
				}
				default: {
					$op = 'S';
					if ((!$border_style) OR (isset($border_style['all']))) {
						if (isset($border_style['all']) AND $border_style['all']) {
							$this->SetLineStyle($border_style['all']);
							$border_style = array();
						}
						$this->_outRect($x, $y, $w, $h, $op);
					}
					break;
				}
			}
			if ($border_style) {
				$border_style2 = array();
				foreach ($border_style as $line => $value) {
					$lenght = strlen($line);
					for ($i = 0; $i < $lenght; ++$i) {
						$border_style2[$line[$i]] = $value;
					}
				}
				$border_style = $border_style2;
				if (isset($border_style['L']) AND $border_style['L']) {
					$this->Line($x, $y, $x, $y + $h, $border_style['L']);
				}
				if (isset($border_style['T']) AND $border_style['T']) {
					$this->Line($x, $y, $x + $w, $y, $border_style['T']);
				}
				if (isset($border_style['R']) AND $border_style['R']) {
					$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
				}
				if (isset($border_style['B']) AND $border_style['B']) {
					$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
				}
			}
		}
		
		
		/**
		* Draws a Bezier curve.
		* The Bezier curve is a tangent to the line between the control points at
		* either end of the curve.
		* @param float $x0 Abscissa of start point.
		* @param float $y0 Ordinate of start point.
		* @param float $x1 Abscissa of control point 1.
		* @param float $y1 Ordinate of control point 1.
		* @param float $x2 Abscissa of control point 2.
		* @param float $y2 Ordinate of control point 2.
		* @param float $x3 Abscissa of end point.
		* @param float $y3 Ordinate of end point.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of curve. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @see SetLineStyle()
		* @since 2.1.000 (2008-01-08)
		*/
		public function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style='', $line_style=array(), $fill_color=array()) {
			if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case 'F': {
					$op = 'f';
					$line_style = array();
					break;
				}
				case 'FD': 
				case 'DF': {
					$op = 'B';
					break;
				}
				case 'CNZ': {
					$op = 'W n';
					break;
				}
				case 'CEO': {
					$op = 'W* n';
					break;
				}
				default: {
					$op = 'S';
					break;
				}
			}
			if ($line_style) {
				$this->SetLineStyle($line_style);
			}
			$this->_outPoint($x0, $y0);
			$this->_outCurve($x1, $y1, $x2, $y2, $x3, $y3);
			$this->_out($op);
		}
		
		/**
		* Draws a poly-Bezier curve.
		* Each Bezier curve segment is a tangent to the line between the control points at
		* either end of the curve.
		* @param float $x0 Abscissa of start point.
		* @param float $y0 Ordinate of start point.
		* @param float $segments An array of bezier descriptions. Format: array(x1, y1, x2, y2, x3, y3).
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of curve. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @see SetLineStyle()
		* @since 3.0008 (2008-05-12)
		*/
		public function Polycurve($x0, $y0, $segments, $style='', $line_style=array(), $fill_color=array()) {
			if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case 'F': {
					$op = 'f';
					$line_style = array();
					break;
				}
				case 'FD':
				case 'DF': {
					$op = 'B';
					break;
				}
				case 'CNZ': {
					$op = 'W n';
					break;
				}
				case 'CEO': {
					$op = 'W* n';
					break;
				}
				default: {
					$op = 'S';
					break;
				}
			}
			if ($line_style) {
				$this->SetLineStyle($line_style);
			}
			$this->_outPoint($x0, $y0);
			foreach ($segments as $segment) {
				list($x1, $y1, $x2, $y2, $x3, $y3) = $segment;
				$this->_outCurve($x1, $y1, $x2, $y2, $x3, $y3);
			}	
			$this->_out($op);
		}
		
		/**
		* Draws an ellipse.
		* An ellipse is formed from n Bezier curves.
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $rx Horizontal radius.
		* @param float $ry Vertical radius (if ry = 0 then is a circle, see {@link Circle Circle}). Default value: 0.
		* @param float $angle: Angle oriented (anti-clockwise). Default value: 0.
		* @param float $astart: Angle start of draw line. Default value: 0.
		* @param float $afinish: Angle finish of draw line. Default value: 360.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>C: Draw close.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of ellipse. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @param integer $nc Number of curves used in ellipse. Default value: 8.
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function Ellipse($x0, $y0, $rx, $ry=0, $angle=0, $astart=0, $afinish=360, $style='', $line_style=array(), $fill_color=array(), $nc=8) {
			if ($angle) {
				$this->StartTransform();
				$this->Rotate($angle, $x0, $y0);
				$this->Ellipse($x0, $y0, $rx, $ry, 0, $astart, $afinish, $style, $line_style, $fill_color, $nc);
				$this->StopTransform();
				return;
			}
			if ($rx) {
				if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
					$this->SetFillColorArray($fill_color);
				}
				switch ($style) {
					case 'F': {
						$op = 'f';
						$line_style = array();
						break;
					}
					case 'FD': 
					case 'DF': {
						$op = 'B';
						break;
					}
					case 'C': {
						$op = 's'; // Small 's' signifies closing the path as well
						break;
					}
					case 'CNZ': {
						$op = 'W n';
						break;
					}
					case 'CEO': {
						$op = 'W* n';
						break;
					}
					default: {
						$op = 'S';
						break;
					}
				}
				if ($line_style) {
					$this->SetLineStyle($line_style);
				}
				if (!$ry) {
					$ry = $rx;
				}
				$rx *= $this->k;
				$ry *= $this->k;
				if ($nc < 2) {
					$nc = 2;
				}
				$astart = deg2rad((float) $astart);
				$afinish = deg2rad((float) $afinish);
				$total_angle = $afinish - $astart;
				$dt = $total_angle / $nc;
				$dtm = $dt / 3;
				$x0 *= $this->k;
				$y0 = ($this->h - $y0) * $this->k;
				$t1 = $astart;
				$a0 = $x0 + ($rx * cos($t1));
				$b0 = $y0 + ($ry * sin($t1));
				$c0 = -$rx * sin($t1);
				$d0 = $ry * cos($t1);
				$this->_outPoint($a0 / $this->k, $this->h - ($b0 / $this->k));
				for ($i = 1; $i <= $nc; ++$i) {
					// Draw this bit of the total curve
					$t1 = ($i * $dt) + $astart;
					$a1 = $x0 + ($rx * cos($t1));
					$b1 = $y0 + ($ry * sin($t1));
					$c1 = -$rx * sin($t1);
					$d1 = $ry * cos($t1);
					$this->_outCurve(($a0 + ($c0 * $dtm)) / $this->k, $this->h - (($b0 + ($d0 * $dtm)) / $this->k), ($a1 - ($c1 * $dtm)) / $this->k, $this->h - (($b1 - ($d1 * $dtm)) / $this->k), $a1 / $this->k, $this->h - ($b1 / $this->k));
					$a0 = $a1;
					$b0 = $b1;
					$c0 = $c1;
					$d0 = $d1;
				}
				$this->_out($op);
			}
		}
		
		/**
		* Draws a circle.
		* A circle is formed from n Bezier curves.
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $r Radius.
		* @param float $astart: Angle start of draw line. Default value: 0.
		* @param float $afinish: Angle finish of draw line. Default value: 360.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>C: Draw close.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of circle. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(red, green, blue). Default value: default color (empty array).
		* @param integer $nc Number of curves used in circle. Default value: 8.
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function Circle($x0, $y0, $r, $astart=0, $afinish=360, $style='', $line_style=array(), $fill_color=array(), $nc=8) {
			$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nc);
		}

		/**
		* Draws a polygonal line
		* @param array $p Points 0 to ($np - 1). Array with values (x0, y0, x1, y1,..., x(np-1), y(np - 1))
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of polygon. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all lines. Array like for {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>0 to ($np - 1): Line style of each line. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the line. Default value is default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @param boolean $closed if true the polygon is closes, otherwise will remain open
		* @access public
		* @since 4.8.003 (2009-09-15)
		*/
		public function PolyLine($p, $style='', $line_style=array(), $fill_color=array()) {
			$this->Polygon($p, $style, $line_style, $fill_color, false);
		}

		/**
		* Draws a polygon.
		* @param array $p Points 0 to ($np - 1). Array with values (x0, y0, x1, y1,..., x(np-1), y(np - 1))
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of polygon. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all lines. Array like for {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>0 to ($np - 1): Line style of each line. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the line. Default value is default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @param boolean $closed if true the polygon is closes, otherwise will remain open
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function Polygon($p, $style='', $line_style=array(), $fill_color=array(), $closed=true) {
			$nc = count($p); // number of coordinates
			$np = $nc / 2; // number of points
			if ($closed) {
				// close polygon by adding the first 2 points at the end (one line)
				for ($i = 0; $i < 4; ++$i) {
					$p[$nc + $i] = $p[$i];
				}
				// copy style for the last added line
				if (isset($line_style[0])) {
					$line_style[$np] = $line_style[0];
				}			
				$nc += 4;
			}
			if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
				$this->SetFillColorArray($fill_color);
			}
			switch ($style) {
				case 'F': {
					$line_style = array();
					$op = 'f';
					break;
				}
				case 'FD': 
				case 'DF': {
					$op = 'B';
					break;
				}
				case 'CNZ': {
					$op = 'W n';
					break;
				}
				case 'CEO': {
					$op = 'W* n';
					break;
				}				
				default: {
					$op = 'S';
					break;
				}
			}
			$draw = true;
			if ($line_style) {
				if (isset($line_style['all'])) {
					$this->SetLineStyle($line_style['all']);
				} else {
					$draw = false;
					if ($op == 'B') {
						// draw fill
						$op = 'f';
						$this->_outPoint($p[0], $p[1]);
						for ($i = 2; $i < $nc; $i = $i + 2) {
							$this->_outLine($p[$i], $p[$i + 1]);
						}
						$this->_out($op);
					}
					// draw outline
					$this->_outPoint($p[0], $p[1]);
					for ($i = 2; $i < $nc; $i = $i + 2) {
						$line_num = ($i / 2) - 1;
						if (isset($line_style[$line_num])) {
							if ($line_style[$line_num] != 0) {
								if (is_array($line_style[$line_num])) {
									$this->_out('S');
									$this->SetLineStyle($line_style[$line_num]);
									$this->_outPoint($p[$i - 2], $p[$i - 1]);
									$this->_outLine($p[$i], $p[$i + 1]);
									$this->_out('S');
									$this->_outPoint($p[$i], $p[$i + 1]);
								} else {
									$this->_outLine($p[$i], $p[$i + 1]);
								}
							}
						} else {
							$this->_outLine($p[$i], $p[$i + 1]);
						}
					}
					$this->_out($op);
				}
			}
			if ($draw) {
				$this->_outPoint($p[0], $p[1]);
				for ($i = 2; $i < $nc; $i = $i + 2) {
					$this->_outLine($p[$i], $p[$i + 1]);
				}
				$this->_out($op);
			}
		}
		
		/**
		* Draws a regular polygon.
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $r: Radius of inscribed circle.
		* @param integer $ns Number of sides.
		* @param float $angle Angle oriented (anti-clockwise). Default value: 0.
		* @param boolean $draw_circle Draw inscribed circle or not. Default value: false.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of polygon sides. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all sides. Array like for {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>0 to ($ns - 1): Line style of each side. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the side. Default value is default line style (empty array).
		* @param array $fill_color Fill color. Format: array(red, green, blue). Default value: default color (empty array).
		* @param string $circle_style Style of rendering of inscribed circle (if draws). Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $circle_outLine_style Line style of inscribed circle (if draws). Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $circle_fill_color Fill color of inscribed circle (if draws). Format: array(red, green, blue). Default value: default color (empty array).
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function RegularPolygon($x0, $y0, $r, $ns, $angle=0, $draw_circle=false, $style='', $line_style=array(), $fill_color=array(), $circle_style='', $circle_outLine_style=array(), $circle_fill_color=array()) {
			if (3 > $ns) {
				$ns = 3;
			}
			if ($draw_circle) {
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
			}
			$p = array();
			for ($i = 0; $i < $ns; ++$i) {
				$a = $angle + ($i * 360 / $ns);
				$a_rad = deg2rad((float) $a);
				$p[] = $x0 + ($r * sin($a_rad));
				$p[] = $y0 + ($r * cos($a_rad));
			}
			$this->Polygon($p, $style, $line_style, $fill_color);
		}
		
		/**
		* Draws a star polygon
		* @param float $x0 Abscissa of center point.
		* @param float $y0 Ordinate of center point.
		* @param float $r Radius of inscribed circle.
		* @param integer $nv Number of vertices.
		* @param integer $ng Number of gap (if ($ng % $nv = 1) then is a regular polygon).
		* @param float $angle: Angle oriented (anti-clockwise). Default value: 0.
		* @param boolean $draw_circle: Draw inscribed circle or not. Default value is false.
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $line_style Line style of polygon sides. Array with keys among the following:
		* <ul>
		*	 <li>all: Line style of all sides. Array like for
		* {@link SetLineStyle SetLineStyle}.</li>
		*	 <li>0 to (n - 1): Line style of each side. Array like for {@link SetLineStyle SetLineStyle}.</li>
		* </ul>
		* If a key is not present or is null, not draws the side. Default value is default line style (empty array).
		* @param array $fill_color Fill color. Format: array(red, green, blue). Default value: default color (empty array).
		* @param string $circle_style Style of rendering of inscribed circle (if draws). Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $circle_outLine_style Line style of inscribed circle (if draws). Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $circle_fill_color Fill color of inscribed circle (if draws). Format: array(red, green, blue). Default value: default color (empty array).
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function StarPolygon($x0, $y0, $r, $nv, $ng, $angle=0, $draw_circle=false, $style='', $line_style=array(), $fill_color=array(), $circle_style='', $circle_outLine_style=array(), $circle_fill_color=array()) {
			if ($nv < 2) {
				$nv = 2;
			}
			if ($draw_circle) {
				$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
			}
			$p2 = array();
			$visited = array();
			for ($i = 0; $i < $nv; ++$i) {
				$a = $angle + ($i * 360 / $nv);
				$a_rad = deg2rad((float) $a);
				$p2[] = $x0 + ($r * sin($a_rad));
				$p2[] = $y0 + ($r * cos($a_rad));
				$visited[] = false;
			}
			$p = array();
			$i = 0;
			do {
				$p[] = $p2[$i * 2];
				$p[] = $p2[($i * 2) + 1];
				$visited[$i] = true;
				$i += $ng;
				$i %= $nv;
			} while (!$visited[$i]);
			$this->Polygon($p, $style, $line_style, $fill_color);
		}
		
		/**
		* Draws a rounded rectangle.
		* @param float $x Abscissa of upper-left corner.
		* @param float $y Ordinate of upper-left corner.
		* @param float $w Width.
		* @param float $h Height.
		* @param float $r Radius of the rounded corners.
		* @param string $round_corner Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left. Default value: all rounded corner ("1111").
		* @param string $style Style of rendering. Possible values are:
		* <ul>
		*	 <li>D or empty string: Draw (default).</li>
		*	 <li>F: Fill.</li>
		*	 <li>DF or FD: Draw and fill.</li>
		*	 <li>CNZ: Clipping mode (using the even-odd rule to determine which regions lie inside the clipping path).</li>
		*	 <li>CEO: Clipping mode (using the nonzero winding number rule to determine which regions lie inside the clipping path).</li>
		* </ul>
		* @param array $border_style Border style of rectangle. Array like for {@link SetLineStyle SetLineStyle}. Default value: default line style (empty array).
		* @param array $fill_color Fill color. Format: array(GREY) or array(R,G,B) or array(C,M,Y,K). Default value: default color (empty array).
		* @access public
		* @since 2.1.000 (2008-01-08)
		*/
		public function RoundedRect($x, $y, $w, $h, $r, $round_corner='1111', $style='', $border_style=array(), $fill_color=array()) {
			if ('0000' == $round_corner) { // Not rounded
				$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
			} else { // Rounded
				if (!(false === strpos($style, 'F')) AND isset($fill_color)) {
					$this->SetFillColorArray($fill_color);
				}
				switch ($style) {
					case 'F': {
						$border_style = array();
						$op = 'f';
						break;
					}
					case 'FD': 
					case 'DF': {
						$op = 'B';
						break;
					}
					case 'CNZ': {
						$op = 'W n';
						break;
					}
					case 'CEO': {
						$op = 'W* n';
						break;
					}
					default: {
						$op = 'S';
						break;
					}
				}
				if ($border_style) {
					$this->SetLineStyle($border_style);
				}
				$MyArc = 4 / 3 * (sqrt(2) - 1);
				$this->_outPoint($x + $r, $y);
				$xc = $x + $w - $r;
				$yc = $y + $r;
				$this->_outLine($xc, $y);
				if ($round_corner[0]) {
					$this->_outCurve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
				} else {
					$this->_outLine($x + $w, $y);
				}
				$xc = $x + $w - $r;
				$yc = $y + $h - $r;
				$this->_outLine($x + $w, $yc);
				if ($round_corner[1]) {
					$this->_outCurve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
				} else {
					$this->_outLine($x + $w, $y + $h);
				}
				$xc = $x + $r;
				$yc = $y + $h - $r;
				$this->_outLine($xc, $y + $h);
				if ($round_corner[2]) {
					$this->_outCurve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
				} else {
					$this->_outLine($x, $y + $h);
				}
				$xc = $x + $r;
				$yc = $y + $r;
				$this->_outLine($x, $yc);
				if ($round_corner[3]) {
					$this->_outCurve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
				} else {
					$this->_outLine($x, $y);
					$this->_outLine($x + $r, $y);
				}
				$this->_out($op);
			}
		}
		
		/**
		* Draws a grahic arrow.
		* @parameter float $x0 Abscissa of first point.
		* @parameter float $y0 Ordinate of first point.
		* @parameter float $x0 Abscissa of second point.
		* @parameter float $y1 Ordinate of second point.
		* @parameter int $head_style (0 = draw only arrowhead arms, 1 = draw closed arrowhead, but no fill, 2 = closed and filled arrowhead, 3 = filled arrowhead)
		* @parameter float $arm_size length of arrowhead arms
		* @parameter int $arm_angle angle between an arm and the shaft
		* @author Piotr Galecki, Nicola Asuni, Andy Meier
		* @since 4.6.018 (2009-07-10)
		*/
		public function Arrow($x0, $y0, $x1, $y1, $head_style=0, $arm_size=5, $arm_angle=15) {
			// getting arrow direction angle
			// 0 deg angle is when both arms go along X axis. angle grows clockwise.
			$dir_angle = rad2deg(atan2(($y0 - $y1), ($x0 - $x1)));
			$sx1 = $x1;
			$sy1 = $y1;
			if ($head_style > 0) {
				// calculate the stopping point for the arrow shaft
				$sx1 = $x1 + (($arm_size - $this->LineWidth) * cos(deg2rad($dir_angle)));
				$sy1 = $y1 + (($arm_size - $this->LineWidth) * sin(deg2rad($dir_angle)));
			} 
			// main arrow line / shaft
			$this->Line($x0, $y0, $sx1, $sy1);
			// left arrowhead arm tip
			$x2L = $x1 + ($arm_size * cos(deg2rad($dir_angle + $arm_angle)));
			$y2L = $y1 + ($arm_size * sin(deg2rad($dir_angle + $arm_angle)));
			// right arrowhead arm tip
			$x2R = $x1 + ($arm_size * cos(deg2rad($dir_angle - $arm_angle)));
			$y2R = $y1 + ($arm_size * sin(deg2rad($dir_angle - $arm_angle)));
			$mode = 'D';
			$style = array();
			switch ($head_style) {
				case 0: {
					// draw only arrowhead arms
					$mode = 'D';
					$style = array(1, 1, 0);
					break;
				}
				case 1: {
					// draw closed arrowhead, but no fill
					$mode = 'D';
					break;
				}
				case 2: {
					// closed and filled arrowhead
					$mode = 'DF';
					break;
				}
				case 3: {
					// filled arrowhead
					$mode = 'F';
					break;
				}
			}
			$this->Polygon(array($x2L, $y2L, $x1, $y1, $x2R, $y2R), $mode, $style, array());
		}
		
		// END GRAPHIC FUNCTIONS SECTION -----------------------
		
		// BIDIRECTIONAL TEXT SECTION --------------------------
		/**
		 * Reverse the RLT substrings using the Bidirectional Algorithm (http://unicode.org/reports/tr9/).
		 * @param string $str string to manipulate.
		 * @param bool $forcertl if 'R' forces RTL, if 'L' forces LTR
		 * @return string
		 * @access protected
		 * @author Nicola Asuni
		 * @since 2.1.000 (2008-01-08)
		*/
		protected function utf8StrRev($str, $setbom=false, $forcertl=false) {
			return $this->arrUTF8ToUTF16BE($this->utf8Bidi($this->UTF8StringToArray($str), $str, $forcertl), $setbom);
		}
		
		/**
		 * Reverse the RLT substrings using the Bidirectional Algorithm (http://unicode.org/reports/tr9/).
		 * @param array $ta array of characters composing the string.
		 * @param string $str string to process
		 * @param bool $forcertl if 'R' forces RTL, if 'L' forces LTR
		 * @return string
		 * @author Nicola Asuni
		 * @access protected
		 * @since 2.4.000 (2008-03-06)
		*/
		protected function utf8Bidi($ta, $str='', $forcertl=false) {
			global $unicode, $unicode_mirror, $unicode_arlet, $laa_array, $diacritics;
			// paragraph embedding level
			$pel = 0;
			// max level
			$maxlevel = 0;
			if ($this->empty_string($str)) {
				// create string from array
				$str = $this->UTF8ArrSubString($ta);
			}
			// check if string contains arabic text
			if (preg_match(K_RE_PATTERN_ARABIC, $str)) {
				$arabic = true;
			} else {
				$arabic = false;
			}
			// check if string contains RTL text
			if (!($forcertl OR $arabic OR preg_match(K_RE_PATTERN_RTL, $str))) {
				return $ta;
			}
			
			// get number of chars
			$numchars = count($ta);
			
			if ($forcertl == 'R') {
					$pel = 1;
			} elseif ($forcertl == 'L') {
					$pel = 0;
			} else {
				// P2. In each paragraph, find the first character of type L, AL, or R.
				// P3. If a character is found in P2 and it is of type AL or R, then set the paragraph embedding level to one; otherwise, set it to zero.
				for ($i=0; $i < $numchars; ++$i) {
					$type = $unicode[$ta[$i]];
					if ($type == 'L') {
						$pel = 0;
						break;
					} elseif (($type == 'AL') OR ($type == 'R')) {
						$pel = 1;
						break;
					}
				}
			}
			
			// Current Embedding Level
			$cel = $pel;
			// directional override status
			$dos = 'N';
			$remember = array();
			// start-of-level-run
			$sor = $pel % 2 ? 'R' : 'L';
			$eor = $sor;
			
			// Array of characters data
			$chardata = Array();
			
			// X1. Begin by setting the current embedding level to the paragraph embedding level. Set the directional override status to neutral. Process each character iteratively, applying rules X2 through X9. Only embedding levels from 0 to 61 are valid in this phase.
			// 	In the resolution of levels in rules I1 and I2, the maximum embedding level of 62 can be reached.
			for ($i=0; $i < $numchars; ++$i) {
				if ($ta[$i] == K_RLE) {
					// X2. With each RLE, compute the least greater odd embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to neutral.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + ($cel % 2) + 1;
					if ($next_level < 62) {
						$remember[] = array('num' => K_RLE, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'N';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_LRE) {
					// X3. With each LRE, compute the least greater even embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to neutral.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + 2 - ($cel % 2);
					if ( $next_level < 62 ) {
						$remember[] = array('num' => K_LRE, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'N';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_RLO) {
					// X4. With each RLO, compute the least greater odd embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to right-to-left.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + ($cel % 2) + 1;
					if ($next_level < 62) {
						$remember[] = array('num' => K_RLO, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'R';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_LRO) {
					// X5. With each LRO, compute the least greater even embedding level.
					//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to left-to-right.
					//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
					$next_level = $cel + 2 - ($cel % 2);
					if ( $next_level < 62 ) {
						$remember[] = array('num' => K_LRO, 'cel' => $cel, 'dos' => $dos);
						$cel = $next_level;
						$dos = 'L';
						$sor = $eor;
						$eor = $cel % 2 ? 'R' : 'L';
					}
				} elseif ($ta[$i] == K_PDF) {
					// X7. With each PDF, determine the matching embedding or override code. If there was a valid matching code, restore (pop) the last remembered (pushed) embedding level and directional override.
					if (count($remember)) {
						$last = count($remember ) - 1;
						if (($remember[$last]['num'] == K_RLE) OR 
							  ($remember[$last]['num'] == K_LRE) OR 
							  ($remember[$last]['num'] == K_RLO) OR 
							  ($remember[$last]['num'] == K_LRO)) {
							$match = array_pop($remember);
							$cel = $match['cel'];
							$dos = $match['dos'];
							$sor = $eor;
							$eor = ($cel > $match['cel'] ? $cel : $match['cel']) % 2 ? 'R' : 'L';
						}
					}
				} elseif (($ta[$i] != K_RLE) AND
								 ($ta[$i] != K_LRE) AND
								 ($ta[$i] != K_RLO) AND
								 ($ta[$i] != K_LRO) AND
								 ($ta[$i] != K_PDF)) {
					// X6. For all types besides RLE, LRE, RLO, LRO, and PDF:
					//	a. Set the level of the current character to the current embedding level.
					//	b. Whenever the directional override status is not neutral, reset the current character type to the directional override status.
					if ($dos != 'N') {
						$chardir = $dos;
					} else {
						if (isset($unicode[$ta[$i]])) {
							$chardir = $unicode[$ta[$i]];
						} else {
							$chardir = 'L';
						}
					}
					// stores string characters and other information
					$chardata[] = array('char' => $ta[$i], 'level' => $cel, 'type' => $chardir, 'sor' => $sor, 'eor' => $eor);
				}
			} // end for each char
			
			// X8. All explicit directional embeddings and overrides are completely terminated at the end of each paragraph. Paragraph separators are not included in the embedding.
			// X9. Remove all RLE, LRE, RLO, LRO, PDF, and BN codes.
			// X10. The remaining rules are applied to each run of characters at the same level. For each run, determine the start-of-level-run (sor) and end-of-level-run (eor) type, either L or R. This depends on the higher of the two levels on either side of the boundary (at the start or end of the paragraph, the level of the 'other' run is the base embedding level). If the higher level is odd, the type is R; otherwise, it is L.
			
			// 3.3.3 Resolving Weak Types
			// Weak types are now resolved one level run at a time. At level run boundaries where the type of the character on the other side of the boundary is required, the type assigned to sor or eor is used.
			// Nonspacing marks are now resolved based on the previous characters.
			$numchars = count($chardata);
			
			// W1. Examine each nonspacing mark (NSM) in the level run, and change the type of the NSM to the type of the previous character. If the NSM is at the start of the level run, it will get the type of sor.
			$prevlevel = -1; // track level changes
			$levcount = 0; // counts consecutive chars at the same level
			for ($i=0; $i < $numchars; ++$i) {
				if ($chardata[$i]['type'] == 'NSM') {
					if ($levcount) {
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					} elseif ($i > 0) {
						$chardata[$i]['type'] = $chardata[($i-1)]['type'];
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					++$levcount;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W2. Search backward from each instance of a European number until the first strong type (R, L, AL, or sor) is found. If an AL is found, change the type of the European number to Arabic number.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; ++$i) {
				if ($chardata[$i]['char'] == 'EN') {
					for ($j=$levcount; $j >= 0; $j--) {
						if ($chardata[$j]['type'] == 'AL') {
							$chardata[$i]['type'] = 'AN';
						} elseif (($chardata[$j]['type'] == 'L') OR ($chardata[$j]['type'] == 'R')) {
							break;
						}
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					++$levcount;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W3. Change all ALs to R.
			for ($i=0; $i < $numchars; ++$i) {
				if ($chardata[$i]['type'] == 'AL') {
					$chardata[$i]['type'] = 'R';
				} 
			}
			
			// W4. A single European separator between two European numbers changes to a European number. A single common separator between two numbers of the same type changes to that type.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; ++$i) {
				if (($levcount > 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
					if (($chardata[$i]['type'] == 'ES') AND ($chardata[($i-1)]['type'] == 'EN') AND ($chardata[($i+1)]['type'] == 'EN')) {
						$chardata[$i]['type'] = 'EN';
					} elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i-1)]['type'] == 'EN') AND ($chardata[($i+1)]['type'] == 'EN')) {
						$chardata[$i]['type'] = 'EN';
					} elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i-1)]['type'] == 'AN') AND ($chardata[($i+1)]['type'] == 'AN')) {
						$chardata[$i]['type'] = 'AN';
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					++$levcount;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W5. A sequence of European terminators adjacent to European numbers changes to all European numbers.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; ++$i) {
				if ($chardata[$i]['type'] == 'ET') {
					if (($levcount > 0) AND ($chardata[($i-1)]['type'] == 'EN')) {
						$chardata[$i]['type'] = 'EN';
					} else {
						$j = $i+1;
						while (($j < $numchars) AND ($chardata[$j]['level'] == $prevlevel)) {
							if ($chardata[$j]['type'] == 'EN') {
								$chardata[$i]['type'] = 'EN';
								break;
							} elseif ($chardata[$j]['type'] != 'ET') {
								break;
							}
							++$j;
						}
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					++$levcount;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// W6. Otherwise, separators and terminators change to Other Neutral.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; ++$i) {
				if (($chardata[$i]['type'] == 'ET') OR ($chardata[$i]['type'] == 'ES') OR ($chardata[$i]['type'] == 'CS')) {
					$chardata[$i]['type'] = 'ON';
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					++$levcount;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			//W7. Search backward from each instance of a European number until the first strong type (R, L, or sor) is found. If an L is found, then change the type of the European number to L.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; ++$i) {
				if ($chardata[$i]['char'] == 'EN') {
					for ($j=$levcount; $j >= 0; $j--) {
						if ($chardata[$j]['type'] == 'L') {
							$chardata[$i]['type'] = 'L';
						} elseif ($chardata[$j]['type'] == 'R') {
							break;
						}
					}
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					++$levcount;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// N1. A sequence of neutrals takes the direction of the surrounding strong text if the text on both sides has the same direction. European and Arabic numbers act as if they were R in terms of their influence on neutrals. Start-of-level-run (sor) and end-of-level-run (eor) are used at level run boundaries.
			$prevlevel = -1;
			$levcount = 0;
			for ($i=0; $i < $numchars; ++$i) {
				if (($levcount > 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
					if (($chardata[$i]['type'] == 'N') AND ($chardata[($i-1)]['type'] == 'L') AND ($chardata[($i+1)]['type'] == 'L')) {
						$chardata[$i]['type'] = 'L';
					} elseif (($chardata[$i]['type'] == 'N') AND
					 (($chardata[($i-1)]['type'] == 'R') OR ($chardata[($i-1)]['type'] == 'EN') OR ($chardata[($i-1)]['type'] == 'AN')) AND
					 (($chardata[($i+1)]['type'] == 'R') OR ($chardata[($i+1)]['type'] == 'EN') OR ($chardata[($i+1)]['type'] == 'AN'))) {
						$chardata[$i]['type'] = 'R';
					} elseif ($chardata[$i]['type'] == 'N') {
						// N2. Any remaining neutrals take the embedding direction
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					}
				} elseif (($levcount == 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
					// first char
					if (($chardata[$i]['type'] == 'N') AND ($chardata[$i]['sor'] == 'L') AND ($chardata[($i+1)]['type'] == 'L')) {
						$chardata[$i]['type'] = 'L';
					} elseif (($chardata[$i]['type'] == 'N') AND
					 (($chardata[$i]['sor'] == 'R') OR ($chardata[$i]['sor'] == 'EN') OR ($chardata[$i]['sor'] == 'AN')) AND
					 (($chardata[($i+1)]['type'] == 'R') OR ($chardata[($i+1)]['type'] == 'EN') OR ($chardata[($i+1)]['type'] == 'AN'))) {
						$chardata[$i]['type'] = 'R';
					} elseif ($chardata[$i]['type'] == 'N') {
						// N2. Any remaining neutrals take the embedding direction
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					}
				} elseif (($levcount > 0) AND ((($i+1) == $numchars) OR (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] != $prevlevel))) {
					//last char
					if (($chardata[$i]['type'] == 'N') AND ($chardata[($i-1)]['type'] == 'L') AND ($chardata[$i]['eor'] == 'L')) {
						$chardata[$i]['type'] = 'L';
					} elseif (($chardata[$i]['type'] == 'N') AND
					 (($chardata[($i-1)]['type'] == 'R') OR ($chardata[($i-1)]['type'] == 'EN') OR ($chardata[($i-1)]['type'] == 'AN')) AND
					 (($chardata[$i]['eor'] == 'R') OR ($chardata[$i]['eor'] == 'EN') OR ($chardata[$i]['eor'] == 'AN'))) {
						$chardata[$i]['type'] = 'R';
					} elseif ($chardata[$i]['type'] == 'N') {
						// N2. Any remaining neutrals take the embedding direction
						$chardata[$i]['type'] = $chardata[$i]['sor'];
					}
				} elseif ($chardata[$i]['type'] == 'N') {
					// N2. Any remaining neutrals take the embedding direction
					$chardata[$i]['type'] = $chardata[$i]['sor'];
				}
				if ($chardata[$i]['level'] != $prevlevel) {
					$levcount = 0;
				} else {
					++$levcount;
				}
				$prevlevel = $chardata[$i]['level'];
			}
			
			// I1. For all characters with an even (left-to-right) embedding direction, those of type R go up one level and those of type AN or EN go up two levels.
			// I2. For all characters with an odd (right-to-left) embedding direction, those of type L, EN or AN go up one level.
			for ($i=0; $i < $numchars; ++$i) {
				$odd = $chardata[$i]['level'] % 2;
				if ($odd) {
					if (($chardata[$i]['type'] == 'L') OR ($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')) {
						$chardata[$i]['level'] += 1;
					}
				} else {
					if ($chardata[$i]['type'] == 'R') {
						$chardata[$i]['level'] += 1;
					} elseif (($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')) {
						$chardata[$i]['level'] += 2;
					}
				}
				$maxlevel = max($chardata[$i]['level'],$maxlevel);
			}
			
			// L1. On each line, reset the embedding level of the following characters to the paragraph embedding level:
			//	1. Segment separators,
			//	2. Paragraph separators,
			//	3. Any sequence of whitespace characters preceding a segment separator or paragraph separator, and
			//	4. Any sequence of white space characters at the end of the line.
			for ($i=0; $i < $numchars; ++$i) {
				if (($chardata[$i]['type'] == 'B') OR ($chardata[$i]['type'] == 'S')) {
					$chardata[$i]['level'] = $pel;
				} elseif ($chardata[$i]['type'] == 'WS') {
					$j = $i+1;
					while ($j < $numchars) {
						if ((($chardata[$j]['type'] == 'B') OR ($chardata[$j]['type'] == 'S')) OR
							(($j == ($numchars-1)) AND ($chardata[$j]['type'] == 'WS'))) {
							$chardata[$i]['level'] = $pel;
							break;
						} elseif ($chardata[$j]['type'] != 'WS') {
							break;
						}
						++$j;
					}
				}
			}
			
			// Arabic Shaping
			// Cursively connected scripts, such as Arabic or Syriac, require the selection of positional character shapes that depend on adjacent characters. Shaping is logically applied after the Bidirectional Algorithm is used and is limited to characters within the same directional run. 
			if ($arabic) {
				$endedletter = array(1569,1570,1571,1572,1573,1575,1577,1583,1584,1585,1586,1608,1688);
				$alfletter = array(1570,1571,1573,1575);
				$chardata2 = $chardata;
				$laaletter = false;
				$charAL = array();
				$x = 0;
				for ($i=0; $i < $numchars; ++$i) {
					if (($unicode[$chardata[$i]['char']] == 'AL') OR ($chardata[$i]['char'] == 32) OR ($chardata[$i]['char'] == 8204)) {
						$charAL[$x] = $chardata[$i];
						$charAL[$x]['i'] = $i;
						$chardata[$i]['x'] = $x;
						++$x;
					}
				}
				$numAL = $x;
				for ($i=0; $i < $numchars; ++$i) {
					$thischar = $chardata[$i];
					if ($i > 0) {
						$prevchar = $chardata[($i-1)];
					} else {
						$prevchar = false;
					}
					if (($i+1) < $numchars) {
						$nextchar = $chardata[($i+1)];
					} else {
						$nextchar = false;
					}
					if ($unicode[$thischar['char']] == 'AL') {
						$x = $thischar['x'];
						if ($x > 0) {
							$prevchar = $charAL[($x-1)];
						} else {
							$prevchar = false;
						}
						if (($x+1) < $numAL) {
							$nextchar = $charAL[($x+1)];
						} else {
							$nextchar = false;
						}
						// if laa letter
						if (($prevchar !== false) AND ($prevchar['char'] == 1604) AND (in_array($thischar['char'], $alfletter))) {
							$arabicarr = $laa_array;
							$laaletter = true;
							if ($x > 1) {
								$prevchar = $charAL[($x-2)];
							} else {
								$prevchar = false;
							}
						} else {
							$arabicarr = $unicode_arlet;
							$laaletter = false;
						}
						if (($prevchar !== false) AND ($nextchar !== false) AND
							(($unicode[$prevchar['char']] == 'AL') OR ($unicode[$prevchar['char']] == 'NSM')) AND
							(($unicode[$nextchar['char']] == 'AL') OR ($unicode[$nextchar['char']] == 'NSM')) AND
							($prevchar['type'] == $thischar['type']) AND
							($nextchar['type'] == $thischar['type']) AND
							($nextchar['char'] != 1567)) {
							if (in_array($prevchar['char'], $endedletter)) {
								if (isset($arabicarr[$thischar['char']][2])) {
									// initial
									$chardata2[$i]['char'] = $arabicarr[$thischar['char']][2];
								}
							} else {
								if (isset($arabicarr[$thischar['char']][3])) {
									// medial
									$chardata2[$i]['char'] = $arabicarr[$thischar['char']][3];
								}
							}
						} elseif (($nextchar !== false) AND
							(($unicode[$nextchar['char']] == 'AL') OR ($unicode[$nextchar['char']] == 'NSM')) AND
							($nextchar['type'] == $thischar['type']) AND
							($nextchar['char'] != 1567)) {
							if (isset($arabicarr[$chardata[$i]['char']][2])) {
								// initial
								$chardata2[$i]['char'] = $arabicarr[$thischar['char']][2];
							}
						} elseif ((($prevchar !== false) AND
							(($unicode[$prevchar['char']] == 'AL') OR ($unicode[$prevchar['char']] == 'NSM')) AND
							($prevchar['type'] == $thischar['type'])) OR
							(($nextchar !== false) AND ($nextchar['char'] == 1567))) {
							// final
							if (($i > 1) AND ($thischar['char'] == 1607) AND
								($chardata[$i-1]['char'] == 1604) AND
								($chardata[$i-2]['char'] == 1604)) {
								//Allah Word
								// mark characters to delete with false
								$chardata2[$i-2]['char'] = false;
								$chardata2[$i-1]['char'] = false; 
								$chardata2[$i]['char'] = 65010;
							} else {
								if (($prevchar !== false) AND in_array($prevchar['char'], $endedletter)) {
									if (isset($arabicarr[$thischar['char']][0])) {
										// isolated
										$chardata2[$i]['char'] = $arabicarr[$thischar['char']][0];
									}
								} else {
									if (isset($arabicarr[$thischar['char']][1])) {
										// final
										$chardata2[$i]['char'] = $arabicarr[$thischar['char']][1];
									}
								}
							}
						} elseif (isset($arabicarr[$thischar['char']][0])) {
							// isolated
							$chardata2[$i]['char'] = $arabicarr[$thischar['char']][0];
						}
						// if laa letter
						if ($laaletter) {
							// mark characters to delete with false
							$chardata2[($charAL[($x-1)]['i'])]['char'] = false;
						}
					} // end if AL (Arabic Letter)
				} // end for each char
				/* 
				 * Combining characters that can occur with Shadda (0651 HEX, 1617 DEC) are placed in UE586-UE594. 
				 * Putting the combining mark and shadda in the same glyph allows us to avoid the two marks overlapping each other in an illegible manner.
				 */
				$cw = &$this->CurrentFont['cw'];
				for ($i = 0; $i < ($numchars-1); ++$i) {
					if (($chardata2[$i]['char'] == 1617) AND (isset($diacritics[($chardata2[$i+1]['char'])]))) {
						// check if the subtitution font is defined on current font
						if (isset($cw[($diacritics[($chardata2[$i+1]['char'])])])) {
							$chardata2[$i]['char'] = false;
							$chardata2[$i+1]['char'] = $diacritics[($chardata2[$i+1]['char'])];
						}
					}
				}
				// remove marked characters
				foreach ($chardata2 as $key => $value) {
					if ($value['char'] === false) {
						unset($chardata2[$key]);
					}
				}
				$chardata = array_values($chardata2);
				$numchars = count($chardata);
				unset($chardata2);
				unset($arabicarr);
				unset($laaletter);
				unset($charAL);
			}
			
			// L2. From the highest level found in the text to the lowest odd level on each line, including intermediate levels not actually present in the text, reverse any contiguous sequence of characters that are at that level or higher.
			for ($j=$maxlevel; $j > 0; $j--) {
				$ordarray = Array();
				$revarr = Array();
				$onlevel = false;
				for ($i=0; $i < $numchars; ++$i) {
					if ($chardata[$i]['level'] >= $j) {
						$onlevel = true;
						if (isset($unicode_mirror[$chardata[$i]['char']])) {
							// L4. A character is depicted by a mirrored glyph if and only if (a) the resolved directionality of that character is R, and (b) the Bidi_Mirrored property value of that character is true.
							$chardata[$i]['char'] = $unicode_mirror[$chardata[$i]['char']];
						}
						$revarr[] = $chardata[$i];
					} else {
						if ($onlevel) {
							$revarr = array_reverse($revarr);
							$ordarray = array_merge($ordarray, $revarr);
							$revarr = Array();
							$onlevel = false;
						}
						$ordarray[] = $chardata[$i];
					}
				}
				if ($onlevel) {
					$revarr = array_reverse($revarr);
					$ordarray = array_merge($ordarray, $revarr);
				}
				$chardata = $ordarray;
			}
			
			$ordarray = array();
			for ($i=0; $i < $numchars; ++$i) {
				$ordarray[] = $chardata[$i]['char'];
			}
			
			return $ordarray;
		}
		
		// END OF BIDIRECTIONAL TEXT SECTION -------------------
		
		/*
		* Adds a bookmark.
		* @param string $txt bookmark description.
		* @param int $level bookmark level (minimum value is 0).
		* @param float $y Ordinate of the boorkmark position (default = -1 = current position).
		* @param int $page target page number (leave empty for current page).
		* @access public
		* @author Olivier Plathey, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function Bookmark($txt, $level=0, $y=-1, $page='') {
			if ($level < 0) {
				$level = 0;
			}
			if (isset($this->outlines[0])) {
				$lastoutline = end($this->outlines);
				$maxlevel = $lastoutline['l'] + 1;
			} else {
				$maxlevel = 0;
			}
			if ($level > $maxlevel) {
				$level = $maxlevel;
			}
			if ($y == -1) {
				$y = $this->GetY();
			}
			if (empty($page)) {
				$page = $this->PageNo();
			}
			$this->outlines[] = array('t' => $txt, 'l' => $level, 'y' => $y, 'p' => $page);
		}
		
		/*
		* Create a bookmark PDF string.
		* @access protected
		* @author Olivier Plathey, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _putbookmarks() {
			$nb = count($this->outlines);
			if ($nb == 0) {
				return;
			}
			$lru = array();
			$level = 0;
			foreach ($this->outlines as $i => $o) {
				if ($o['l'] > 0) {
					$parent = $lru[($o['l'] - 1)];
					//Set parent and last pointers
					$this->outlines[$i]['parent'] = $parent;
					$this->outlines[$parent]['last'] = $i;
					if ($o['l'] > $level) {
						//Level increasing: set first pointer
						$this->outlines[$parent]['first'] = $i;
					}
				} else {
					$this->outlines[$i]['parent'] = $nb;
				}
				if (($o['l'] <= $level) AND ($i > 0)) {
					//Set prev and next pointers
					$prev = $lru[$o['l']];
					$this->outlines[$prev]['next'] = $i;
					$this->outlines[$i]['prev'] = $prev;
				}
				$lru[$o['l']] = $i;
				$level = $o['l'];
			}
			//Outline items
			$n = $this->n + 1;
			foreach ($this->outlines as $i => $o) {
				$this->_newobj();
				$this->_out('<</Title '.$this->_textstring($o['t']));
				$this->_out('/Parent '.($n + $o['parent']).' 0 R');
				if (isset($o['prev']))
				$this->_out('/Prev '.($n + $o['prev']).' 0 R');
				if (isset($o['next']))
				$this->_out('/Next '.($n + $o['next']).' 0 R');
				if (isset($o['first']))
				$this->_out('/First '.($n + $o['first']).' 0 R');
				if (isset($o['last']))
				$this->_out('/Last '.($n + $o['last']).' 0 R');
				$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]', (1 + (2 * $o['p'])), ($this->pagedim[$o['p']]['h'] - ($o['y'] * $this->k))));
				$this->_out('/Count 0>>');
				$this->_out('endobj');
			}
			//Outline root
			$this->_newobj();
			$this->OutlineRoot = $this->n;
			$this->_out('<</Type /Outlines /First '.$n.' 0 R');
			$this->_out('/Last '.($n + $lru[0]).' 0 R>>');
			$this->_out('endobj');
		}
		
		
		// --- JAVASCRIPT ------------------------------------------------------
		
		/*
		* Adds a javascript
		* @param string $script Javascript code
		* @access public
		* @author Johannes Güntert, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		public function IncludeJS($script) {
			$this->javascript .= $script;
		}

		/*
		* Adds a javascript object and return object ID
		* @param string $script Javascript code
		* @param boolean $onload if true executes this object when opening the document
		* @return int internal object ID
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-07)
		*/
		public function addJavascriptObject($script, $onload=false) {
			++$this->js_obj_id;
			$this->js_objects[$this->js_obj_id] = array('js' => $script, 'onload' => $onload);
			return $this->js_obj_id;
		}

		/*
		* Create a javascript PDF string.
		* @access protected
		* @author Johannes Güntert, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _putjavascript() {
			if (empty($this->javascript) AND empty($this->js_objects)) {
				return;
			}
			if (strpos($this->javascript, 'this.addField') > 0) {
				if (!$this->ur) {
					//$this->setUserRights();
				}
				// the following two lines are used to avoid form fields duplication after saving
				// The addField method only works on Acrobat Writer, unless the document is signed with Adobe private key (UR3)
				$jsa = sprintf("ftcpdfdocsaved=this.addField('%s','%s',%d,[%.2F,%.2F,%.2F,%.2F]);", 'tcpdfdocsaved', 'text', 0, 0, 1, 0, 1);
				$jsb = "getField('tcpdfdocsaved').value='saved';";
				$this->javascript = $jsa."\n".$this->javascript."\n".$jsb;
			}
			$this->n_js = $this->_newobj();
			$this->_out('<<');
			$this->_out('/Names [');
			if (!empty($this->javascript)) {
				$this->_out('(EmbeddedJS) '.($this->n + 1).' 0 R');
			}
			if (!empty($this->js_objects)) {
				foreach ($this->js_objects as $key => $val) {
					if ($val['onload']) {
						$this->_out('(JS'.$key.') '.$key.' 0 R');
					}
				}
			}
			$this->_out(']');
			$this->_out('>>');
			$this->_out('endobj');
			// default Javascript object
			if (!empty($this->javascript)) {
				$this->_newobj();
				$this->_out('<<');
				$this->_out('/S /JavaScript');
				$this->_out('/JS '.$this->_textstring($this->javascript));
				$this->_out('>>');
				$this->_out('endobj');
			}
			// additional Javascript objects
			if (!empty($this->js_objects)) {
				foreach ($this->js_objects as $key => $val) {
					$this->offsets[$key] = $this->bufferlen;
					$this->_out($key.' 0 obj');
					$this->_out('<<');
					$this->_out('/S /JavaScript');
					$this->_out('/JS '.$this->_textstring($val['js']));
					$this->_out('>>');
					$this->_out('endobj');
				}
			}			
		}
		
		/*
		* Convert color to javascript color.
		* @param string $color color name or #RRGGBB
		* @access protected
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _JScolor($color) {
			static $aColors = array('transparent', 'black', 'white', 'red', 'green', 'blue', 'cyan', 'magenta', 'yellow', 'dkGray', 'gray', 'ltGray');
			if (substr($color,0,1) == '#') {
				return sprintf("['RGB',%.3F,%.3F,%.3F]", hexdec(substr($color,1,2))/255, hexdec(substr($color,3,2))/255, hexdec(substr($color,5,2))/255);
			}
			if (!in_array($color,$aColors)) {
				$this->Error('Invalid color: '.$color);
			}
			return 'color.'.$color;
		}
		
		/*
		* Adds a javascript form field.
		* @param string $type field type
		* @param string $name field name
		* @param int $x horizontal position
		* @param int $y vertical position
		* @param int $w width
		* @param int $h height
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @access protected
		* @author Denis Van Nuffelen, Nicola Asuni
		* @since 2.1.002 (2008-02-12)
		*/
		protected function _addfield($type, $name, $x, $y, $w, $h, $prop) {
			if ($this->rtl) {
				$x = $x - $w;
			}
			// the followind avoid fields duplication after saving the document
			$this->javascript .= "if(getField('tcpdfdocsaved').value != 'saved') {";
			$k = $this->k;
			$this->javascript .= sprintf("f".$name."=this.addField('%s','%s',%d,[%.2F,%.2F,%.2F,%.2F]);", $name, $type, $this->PageNo()-1, $x*$k, ($this->h-$y)*$k+1, ($x+$w)*$k, ($this->h-$y-$h)*$k+1)."\n";
			$this->javascript .= 'f'.$name.'.textSize='.$this->FontSizePt.";\n";
			while (list($key, $val) = each($prop)) {
				if (strcmp(substr($key, -5), 'Color') == 0) {
					$val = $this->_JScolor($val);
				} else {
					$val = "'".$val."'";
				}
				$this->javascript .= 'f'.$name.'.'.$key.'='.$val.";\n";
			}
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
			$this->javascript .= '}';
		}

		// --- FORM FIELDS -----------------------------------------------------

		/*
		* Convert JavaScript form fields properties array to Annotation Properties array.
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @return array of annotation properties
		* @access protected
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-06)
		*/
		protected function getAnnotOptFromJSProp($prop) {
			if (isset($prop['aopt']) AND is_array($prop['aopt'])) {
				// the annotation options area lready defined
				return $prop['aopt'];
			}
			$opt = array(); // value to be returned
			// alignment: Controls how the text is laid out within the text field.
			if (isset($prop['alignment'])) {
				switch ($prop['alignment']) {
					case 'left': {
						$opt['q'] = 0;
						break;
					}
					case 'center': {
						$opt['q'] = 1;
						break;
					}
					case 'right': {
						$opt['q'] = 2;
						break;
					}
					default: {
						$opt['q'] = ($this->rtl)?2:0;
						break;
					}
				}
			}
			// lineWidth: Specifies the thickness of the border when stroking the perimeter of a field's rectangle.
			if (isset($prop['lineWidth'])) {
				$linewidth = intval($prop['lineWidth']);
			} else {
				$linewidth = 1;
			}
			// borderStyle: The border style for a field.
			if (isset($prop['borderStyle'])) {
				switch ($prop['borderStyle']) {
					case 'border.d':
					case 'dashed': {
						$opt['border'] = array(0, 0, $linewidth, array(3, 2));
						$opt['bs'] = array('w'=>$linewidth, 's'=>'D', 'd'=>array(3, 2));
						break;
					}
					case 'border.b':
					case 'beveled': {
						$opt['border'] = array(0, 0, $linewidth);
						$opt['bs'] = array('w'=>$linewidth, 's'=>'B');
						break;
					}
					case 'border.i':
					case 'inset': {
						$opt['border'] = array(0, 0, $linewidth);
						$opt['bs'] = array('w'=>$linewidth, 's'=>'I');
						break;
					}
					case 'border.u':
					case 'underline': {
						$opt['border'] = array(0, 0, $linewidth);
						$opt['bs'] = array('w'=>$linewidth, 's'=>'U');
						break;
					}
					default:
					case 'border.s':
					case 'solid': {
						$opt['border'] = array(0, 0, $linewidth);
						$opt['bs'] = array('w'=>$linewidth, 's'=>'S');
						break;
					}
				}
			}
			if (isset($prop['border']) AND is_array($prop['border'])) {
				$opt['border'] = $prop['border'];
			}
			if (!isset($opt['mk'])) {
				$opt['mk'] = array();
			}
			if (!isset($opt['mk']['if'])) {
				$opt['mk']['if'] = array();
			}
			$opt['mk']['if']['a'] = array(0.5, 0.5);
			// buttonAlignX: Controls how space is distributed from the left of the button face with respect to the icon.
			if (isset($prop['buttonAlignX'])) {
				$opt['mk']['if']['a'][0] = $prop['buttonAlignX'];
			}
			// buttonAlignY: Controls how unused space is distributed from the bottom of the button face with respect to the icon.
			if (isset($prop['buttonAlignY'])) {
				$opt['mk']['if']['a'][1] = $prop['buttonAlignY'];
			}
			// buttonFitBounds: If true, the extent to which the icon may be scaled is set to the bounds of the button field.
			if (isset($prop['buttonFitBounds']) AND ($prop['buttonFitBounds'] == 'true')) {
				$opt['mk']['if']['fb'] = true;
			}			
			// buttonScaleHow: Controls how the icon is scaled (if necessary) to fit inside the button face.
			if (isset($prop['buttonScaleHow'])) {
				switch ($prop['buttonScaleHow']) {
					case 'scaleHow.proportional': {
						$opt['mk']['if']['s'] = 'P';
						break;
					}
					case 'scaleHow.anamorphic': {
						$opt['mk']['if']['s'] = 'A';
						break;
					}
				}
			}
			// buttonScaleWhen: Controls when an icon is scaled to fit inside the button face.
			if (isset($prop['buttonScaleWhen'])) {
				switch ($prop['buttonScaleWhen']) {
					case 'scaleWhen.always': {
						$opt['mk']['if']['sw'] = 'A';
						break;
					}
					case 'scaleWhen.never': {
						$opt['mk']['if']['sw'] = 'N';
						break;
					}
					case 'scaleWhen.tooBig': {
						$opt['mk']['if']['sw'] = 'B';
						break;
					}
					case 'scaleWhen.tooSmall': {
						$opt['mk']['if']['sw'] = 'S';
						break;
					}
				}
			}
			// buttonPosition: Controls how the text and the icon of the button are positioned with respect to each other within the button face.
			if (isset($prop['buttonPosition'])) {
				switch ($prop['buttonPosition']) {
					case 0:
					case 'position.textOnly': {
						$opt['mk']['tp'] = 0;
						break;
					}
					case 1:
					case 'position.iconOnly': {
						$opt['mk']['tp'] = 1;
						break;
					}
					case 2:
					case 'position.iconTextV': {
						$opt['mk']['tp'] = 2;
						break;
					}
					case 3:
					case 'position.textIconV': {
						$opt['mk']['tp'] = 3;
						break;
					}
					case 4:
					case 'position.iconTextH': {
						$opt['mk']['tp'] = 4;
						break;
					}
					case 5:
					case 'position.textIconH': {
						$opt['mk']['tp'] = 5;
						break;
					}
					case 6:
					case 'position.overlay': {
						$opt['mk']['tp'] = 6;
						break;
					}
				}				
			}
			// fillColor: Specifies the background color for a field.
			if (isset($prop['fillColor'])) {
				if (is_array($prop['fillColor'])) {
					$opt['mk']['bg'] = $prop['fillColor'];
				} else {
					$opt['mk']['bg'] = $this->convertHTMLColorToDec($prop['fillColor']);
				}
			}
			// strokeColor: Specifies the stroke color for a field that is used to stroke the rectangle of the field with a line as large as the line width.
			if (isset($prop['strokeColor'])) {
				if (is_array($prop['strokeColor'])) {
					$opt['mk']['bc'] = $prop['strokeColor'];
				} else {
					$opt['mk']['bc'] = $this->convertHTMLColorToDec($prop['strokeColor']);
				}
			}
			// rotation: The rotation of a widget in counterclockwise increments.
			if (isset($prop['rotation'])) {
				$opt['mk']['r'] = $prop['rotation'];
			}
			// charLimit: Limits the number of characters that a user can type into a text field.
			if (isset($prop['charLimit'])) {
				$opt['maxlen'] = intval($prop['charLimit']);
			}
			if (!isset($ff)) {
				$ff = 0;
			}
			// readonly: The read-only characteristic of a field. If a field is read-only, the user can see the field but cannot change it.
			if (isset($prop['readonly']) AND ($prop['readonly'] == 'true')) {
				$ff += 1 << 0;
			}
			// required: Specifies whether a field requires a value.
			if (isset($prop['required']) AND ($prop['required'] == 'true')) {
				$ff += 1 << 1;
			}
			// multiline: Controls how text is wrapped within the field.
			if (isset($prop['multiline']) AND ($prop['multiline'] == 'true')) {
				$ff += 1 << 12;
			}
			// password: Specifies whether the field should display asterisks when data is entered in the field.
			if (isset($prop['password']) AND ($prop['password'] == 'true')) {
				$ff += 1 << 13;
			}
			// NoToggleToOff: If set, exactly one radio button shall be selected at all times; selecting the currently selected button has no effect.
			if (isset($prop['NoToggleToOff']) AND ($prop['NoToggleToOff'] == 'true')) {
				$ff += 1 << 14;
			}
			// Radio: If set, the field is a set of radio buttons.
			if (isset($prop['Radio']) AND ($prop['Radio'] == 'true')) {
				$ff += 1 << 15;
			}
			// Pushbutton: If set, the field is a pushbutton that does not retain a permanent value.
			if (isset($prop['Pushbutton']) AND ($prop['Pushbutton'] == 'true')) {
				$ff += 1 << 16;
			}
			// Combo: If set, the field is a combo box; if clear, the field is a list box.
			if (isset($prop['Combo']) AND ($prop['Combo'] == 'true')) {
				$ff += 1 << 17;
			}
			// editable: Controls whether a combo box is editable.
			if (isset($prop['editable']) AND ($prop['editable'] == 'true')) {
				$ff += 1 << 18;
			}
			// Sort: If set, the field's option items shall be sorted alphabetically.
			if (isset($prop['Sort']) AND ($prop['Sort'] == 'true')) {
				$ff += 1 << 19;
			}
			// fileSelect: If true, sets the file-select flag in the Options tab of the text field (Field is Used for File Selection).
			if (isset($prop['fileSelect']) AND ($prop['fileSelect'] == 'true')) {
				$ff += 1 << 20;
			}
			// multipleSelection: If true, indicates that a list box allows a multiple selection of items.
			if (isset($prop['multipleSelection']) AND ($prop['multipleSelection'] == 'true')) {
				$ff += 1 << 21;
			}
			// doNotSpellCheck: If true, spell checking is not performed on this editable text field.
			if (isset($prop['doNotSpellCheck']) AND ($prop['doNotSpellCheck'] == 'true')) {
				$ff += 1 << 22;
			}
			// doNotScroll: If true, the text field does not scroll and the user, therefore, is limited by the rectangular region designed for the field.
			if (isset($prop['doNotScroll']) AND ($prop['doNotScroll'] == 'true')) {
				$ff += 1 << 23;
			}
			// comb: If set to true, the field background is drawn as series of boxes (one for each character in the value of the field) and each character of the content is drawn within those boxes. The number of boxes drawn is determined from the charLimit property. It applies only to text fields. The setter will also raise if any of the following field properties are also set multiline, password, and fileSelect. A side-effect of setting this property is that the doNotScroll property is also set.
			if (isset($prop['comb']) AND ($prop['comb'] == 'true')) {
				$ff += 1 << 24;
			}
			// radiosInUnison: If false, even if a group of radio buttons have the same name and export value, they behave in a mutually exclusive fashion, like HTML radio buttons.
			if (isset($prop['radiosInUnison']) AND ($prop['radiosInUnison'] == 'true')) {
				$ff += 1 << 25;
			}
			// richText: If true, the field allows rich text formatting.
			if (isset($prop['richText']) AND ($prop['richText'] == 'true')) {
				$ff += 1 << 25;
			}
			// commitOnSelChange: Controls whether a field value is committed after a selection change.
			if (isset($prop['commitOnSelChange']) AND ($prop['commitOnSelChange'] == 'true')) {
				$ff += 1 << 26;
			}
			$opt['ff'] = $ff;
			// defaultValue: The default value of a field - that is, the value that the field is set to when the form is reset.
			if (isset($prop['defaultValue'])) {
				$opt['dv'] = $prop['defaultValue'];
			}
			$f = 4; // default value for annotation flags
			// readonly: The read-only characteristic of a field. If a field is read-only, the user can see the field but cannot change it.
			if (isset($prop['readonly']) AND ($prop['readonly'] == 'true')) {
				$f += 1 << 6;
			}
			// display: Controls whether the field is hidden or visible on screen and in print.
			if (isset($prop['display'])) {
				if ($prop['display'] == 'display.visible') {
					//
				} elseif ($prop['display'] == 'display.hidden') {
					$f += 1 << 1;
				} elseif ($prop['display'] == 'display.noPrint') {
					$f -= 1 << 2;
				} elseif ($prop['display'] == 'display.noView') {
					$f += 1 << 5;
				}
			}
			$opt['f'] = $f;
			// currentValueIndices: Reads and writes single or multiple values of a list box or combo box.
			if (isset($prop['currentValueIndices']) AND is_array($prop['currentValueIndices'])) {
				$opt['i'] = $prop['currentValueIndices'];
			}
			// value: The value of the field data that the user has entered.
			if (isset($prop['value'])) {
				if (is_array($prop['value'])) {
					$opt['opt'] = array();
					foreach ($prop['value'] AS $key => $optval) {
						// exportValues: An array of strings representing the export values for the field.
						if (isset($prop['exportValues'][$key])) {
							$opt['opt'][$key] = array($prop['exportValues'][$key], $prop['value'][$key]);
						} else {
							$opt['opt'][$key] = $prop['value'][$key];
						}
					}
				} else {
					$opt['v'] = $prop['value'];
				}
			}
			// richValue: This property specifies the text contents and formatting of a rich text field.
			if (isset($prop['richValue'])) {
				$opt['rv'] = $prop['richValue'];
			}
			// submitName: If nonempty, used during form submission instead of name. Only applicable if submitting in HTML format (that is, URL-encoded).
			if (isset($prop['submitName'])) {
				$opt['tm'] = $prop['submitName'];
			}
			// name: Fully qualified field name.
			if (isset($prop['name'])) {
				$opt['t'] = $prop['name'];
			}
			// userName: The user name (short description string) of the field.
			if (isset($prop['userName'])) {
				$opt['tu'] = $prop['userName'];
			}
			// highlight: Defines how a button reacts when a user clicks it.
			if (isset($prop['highlight'])) {
				switch ($prop['highlight']) {
					case 'none':
					case 'highlight.n': {
						$opt['h'] = 'N';
						break;
					}
					case 'invert':
					case 'highlight.i': {
						$opt['h'] = 'i';
						break;
					}
					case 'push':
					case 'highlight.p': {
						$opt['h'] = 'P';
						break;
					}
					case 'outline':
					case 'highlight.o': {
						$opt['h'] = 'O';
						break;
					}
				}				
			}
			// Unsupported options:
			// - calcOrderIndex: Changes the calculation order of fields in the document.
			// - delay: Delays the redrawing of a field's appearance.
			// - defaultStyle: This property defines the default style attributes for the form field.
			// - style: Allows the user to set the glyph style of a check box or radio button.
			// - textColor, textFont, textSize
			return $opt;
		}
		
		/*
		* Set default properties for form fields.
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-06)
		*/
		public function setFormDefaultProp($prop=array()) {
			$this->default_form_prop = $prop;
		}
		
		/*
		* Return the default properties for form fields.
		* @return array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-06)
		*/
		public function getFormDefaultProp() {
			return $this->default_form_prop;
		}
		
		/*
		* Creates a text field
		* @param string $name field name
		* @param float $w Width of the rectangle
		* @param float $h Height of the rectangle
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @param array $opt annotation parameters. Possible values are described on official PDF32000_2008 reference.
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param boolean $js if true put the field using JavaScript (requires Acrobat Writer to be rendered).
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-07)
		*/
		public function TextField($name, $w, $h, $prop=array(), $opt=array(), $x='', $y='', $js=false) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($js) {
				$this->_addfield('text', $name, $x, $y, $w, $h, $prop);
				return;
			}
			// get default style
			$prop = array_merge($this->getFormDefaultProp(), $prop);
			// get annotation data
			$popt = $this->getAnnotOptFromJSProp($prop);
			// set default appearance stream
			$font = $this->FontFamily;
			$fontkey = array_search($font, $this->fontkeys);
			if (!in_array($fontkey, $this->annotation_fonts)) {
				$this->annotation_fonts[$font] = $fontkey;
			}
			$fontstyle = sprintf('/F%d %.2F Tf %s', ($fontkey + 1), $this->FontSizePt, $this->TextColor);
			$popt['da'] = $fontstyle;
			$popt['ap'] = array();
			$popt['ap']['n'] = 'q BT '.$fontstyle.' ET Q';
			// merge options
			$opt = array_merge($popt, $opt);
			// remove some conflicting options
			unset($opt['bs']);
			// set remaining annotation data
			$opt['Subtype'] = 'Widget';
			$opt['ft'] = 'Tx';
			$opt['t'] = $name;
			/*
			Additional annotation's parameters (check _putannotsobj() method):
			//$opt['f']
			//$opt['ap']
			//$opt['as']
			//$opt['bs']
			//$opt['be']
			//$opt['c']
			//$opt['border']
			//$opt['h']
			//$opt['mk']
			//$opt['mk']['r']
			//$opt['mk']['bc']
			//$opt['mk']['bg']
			//$opt['mk']['ca']
			//$opt['mk']['rc']
			//$opt['mk']['ac']
			//$opt['mk']['i']
			//$opt['mk']['ri']
			//$opt['mk']['ix']
			//$opt['mk']['if']
			//$opt['mk']['if']['sw']
			//$opt['mk']['if']['s']
			//$opt['mk']['if']['a']
			//$opt['mk']['if']['fb']
			//$opt['mk']['tp']
			//$opt['tu']
			//$opt['tm']
			//$opt['ff']
			//$opt['v']
			//$opt['dv']
			//$opt['a']
			//$opt['aa']
			//$opt['q']
			*/
			$this->Annotation($x, $y, $w, $h, $name, $opt, 0);
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
		}

		/*
		* Creates a RadioButton field
		* @param string $name field name
		* @param int $w width
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @param array $opt annotation parameters. Possible values are described on official PDF32000_2008 reference.
		* @param string $onvalue value to be returned if selected.
		* @param boolean $checked define the initial state.
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param boolean $js if true put the field using JavaScript (requires Acrobat Writer to be rendered).
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-07)
		*/
		public function RadioButton($name, $w, $prop=array(), $opt=array(), $onvalue='On', $checked=false, $x='', $y='', $js=false) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($js) {
				$this->_addfield('radiobutton', $name, $x, $y, $w, $w, $prop);
				return;
			}
			if ($this->empty_string($onvalue)) {
				$onvalue = 'On';
			}
			if ($checked) {
				$defval = $onvalue;
			} else {
				$defval = 'Off';
			}
			// set data for parent group
			if (!isset($this->radiobutton_groups[$this->page])) {
				$this->radiobutton_groups[$this->page] = array();
			}
			if (!isset($this->radiobutton_groups[$this->page][$name])) {
				$this->radiobutton_groups[$this->page][$name] = array();
				++$this->annot_obj_id;
				$this->radio_groups[] = $this->annot_obj_id;
			}
			// save object ID to be added on Kids entry on parent object
			$this->radiobutton_groups[$this->page][$name][] = array('kid' => ($this->annot_obj_id + 1), 'def' => $defval);
			// get default style
			$prop = array_merge($this->getFormDefaultProp(), $prop);
			$prop['NoToggleToOff'] = 'true';
			$prop['Radio'] = 'true';
			$prop['borderStyle'] = 'inset';
			// get annotation data
			$popt = $this->getAnnotOptFromJSProp($prop);
			// set additional default values
			$font = 'zapfdingbats';
			$this->AddFont($font);
			$fontkey = array_search($font, $this->fontkeys);
			if (!in_array($fontkey, $this->annotation_fonts)) {
				$this->annotation_fonts[$font] = $fontkey;
			}
			$fontstyle = sprintf('/F%d %.2F Tf %s', ($fontkey + 1), $this->FontSizePt, $this->TextColor);
			$popt['da'] = $fontstyle;
			$popt['ap'] = array();
			$popt['ap']['n'] = array();
			$popt['ap']['n'][$onvalue] = 'q BT '.$fontstyle.' 0 0 Td (8) Tj ET Q';
			$popt['ap']['n']['Off'] = 'q BT '.$fontstyle.' 0 0 Td (8) Tj ET Q';
			if (!isset($popt['mk'])) {
				$popt['mk'] = array();
			}
			$popt['mk']['ca'] = '(l)';
			// merge options
			$opt = array_merge($popt, $opt);
			// set remaining annotation data
			$opt['Subtype'] = 'Widget';
			$opt['ft'] = 'Btn';
			if ($checked) {
				$opt['v'] = array('/'.$onvalue);
				$opt['as'] = $onvalue;
			} else {
				$opt['as'] = 'Off';
			}
			$this->Annotation($x, $y, $w, $w, $name, $opt, 0);
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
		}
		
		/*
		* Creates a List-box field
		* @param string $name field name
		* @param int $w width
		* @param int $h height
		* @param array $values array containing the list of values.
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @param array $opt annotation parameters. Possible values are described on official PDF32000_2008 reference.
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param boolean $js if true put the field using JavaScript (requires Acrobat Writer to be rendered).
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-07)
		*/
		public function ListBox($name, $w, $h, $values, $prop=array(), $opt=array(), $x='', $y='', $js=false) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($js) {
				$this->_addfield('listbox', $name, $x, $y, $w, $h, $prop);
				$s = '';
				foreach ($values as $value) {
					$s .= "'".addslashes($value)."',";
				}
				$this->javascript .= 'f'.$name.'.setItems(['.substr($s, 0, -1)."]);\n";
				return;
			}
			// get default style
			$prop = array_merge($this->getFormDefaultProp(), $prop);
			// get annotation data
			$popt = $this->getAnnotOptFromJSProp($prop);
			// set additional default values
			$font = $this->FontFamily;
			$fontkey = array_search($font, $this->fontkeys);
			if (!in_array($fontkey, $this->annotation_fonts)) {
				$this->annotation_fonts[$font] = $fontkey;
			}
			$fontstyle = sprintf('/F%d %.2F Tf %s', ($fontkey + 1), $this->FontSizePt, $this->TextColor);
			$popt['da'] = $fontstyle;
			$popt['ap'] = array();
			$popt['ap']['n'] = 'q BT '.$fontstyle.' ET Q';
			// merge options
			$opt = array_merge($popt, $opt);
			// set remaining annotation data
			$opt['Subtype'] = 'Widget';
			$opt['ft'] = 'Ch';
			$opt['t'] = $name;
			$opt['opt'] = $values;
			$this->Annotation($x, $y, $w, $h, $name, $opt, 0);
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
		}
		
		/*
		* Creates a Combo-box field
		* @param string $name field name
		* @param int $w width
		* @param int $h height
		* @param array $values array containing the list of values.
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @param array $opt annotation parameters. Possible values are described on official PDF32000_2008 reference.
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param boolean $js if true put the field using JavaScript (requires Acrobat Writer to be rendered).
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-07)
		*/
		public function ComboBox($name, $w, $h, $values, $prop=array(), $opt=array(), $x='', $y='', $js=false) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($js) {
				$this->_addfield('combobox', $name, $x, $y, $w, $h, $prop);
				$s = '';
				foreach ($values as $value) {
					$s .= "'".addslashes($value)."',";
				}
				$this->javascript .= 'f'.$name.'.setItems(['.substr($s, 0, -1)."]);\n";
				return;
			}
			// get default style
			$prop = array_merge($this->getFormDefaultProp(), $prop);
			$prop['Combo'] = true;
			// get annotation data
			$popt = $this->getAnnotOptFromJSProp($prop);
			// set additional default options
			$font = $this->FontFamily;
			$fontkey = array_search($font, $this->fontkeys);
			if (!in_array($fontkey, $this->annotation_fonts)) {
				$this->annotation_fonts[$font] = $fontkey;
			}
			$fontstyle = sprintf('/F%d %.2F Tf %s', ($fontkey + 1), $this->FontSizePt, $this->TextColor);
			$popt['da'] = $fontstyle;
			$popt['ap'] = array();
			$popt['ap']['n'] = 'q BT '.$fontstyle.' ET Q';
			// merge options
			$opt = array_merge($popt, $opt);
			// set remaining annotation data
			$opt['Subtype'] = 'Widget';
			$opt['ft'] = 'Ch';
			$opt['t'] = $name;
			$opt['opt'] = $values;
			$this->Annotation($x, $y, $w, $h, $name, $opt, 0);
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
		}
		
		/*
		* Creates a CheckBox field
		* @param string $name field name
		* @param int $w width
		* @param boolean $checked define the initial state.
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @param array $opt annotation parameters. Possible values are described on official PDF32000_2008 reference.
		* @param string $onvalue value to be returned if selected.
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param boolean $js if true put the field using JavaScript (requires Acrobat Writer to be rendered).
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-07)
		*/
		public function CheckBox($name, $w, $checked=false, $prop=array(), $opt=array(), $onvalue='Yes', $x='', $y='', $js=false) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($js) {
				$this->_addfield('checkbox', $name, $x, $y, $w, $w, $prop);
				return;
			}
			if (!isset($prop['value'])) {
				$prop['value'] = array('Yes');
			}
			// get default style
			$prop = array_merge($this->getFormDefaultProp(), $prop);
			$prop['borderStyle'] = 'inset';
			// get annotation data
			$popt = $this->getAnnotOptFromJSProp($prop);
			// set additional default options
			$font = 'zapfdingbats';
			$this->AddFont($font);
			$fontkey = array_search($font, $this->fontkeys);
			if (!in_array($fontkey, $this->annotation_fonts)) {
				$this->annotation_fonts[$font] = $fontkey;
			}
			$fontstyle = sprintf('/F%d %.2F Tf %s', ($fontkey + 1), $this->FontSizePt, $this->TextColor);
			$popt['da'] = $fontstyle;
			$popt['ap'] = array();
			$popt['ap']['n'] = array();
			$popt['ap']['n']['Yes'] = 'q BT '.$fontstyle.' 0 0 Td (8) Tj ET Q';
			$popt['ap']['n']['Off'] = 'q BT '.$fontstyle.' 0 0 Td (8) Tj ET Q';
			// merge options
			$opt = array_merge($popt, $opt);
			// set remaining annotation data
			$opt['Subtype'] = 'Widget';
			$opt['ft'] = 'Btn';
			$opt['t'] = $name;
			$opt['opt'] = array($onvalue);
			if ($checked) {
				$opt['v'] = array('/0');
				$opt['as'] = 'Yes';
			} else {
				$opt['v'] = array('/Off');
				$opt['as'] = 'Off';
			}
			$this->Annotation($x, $y, $w, $w, $name, $opt, 0);
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
		}
		
		/*
		* Creates a button field
		* @param string $name field name
		* @param int $w width
		* @param int $h height
		* @param string $caption caption.
		* @param mixed $action action triggered by pressing the button. Use a string to specify a javascript action. Use an array to specify a form action options as on section 12.7.5 of PDF32000_2008.
		* @param array $prop javascript field properties. Possible values are described on official Javascript for Acrobat API reference.
		* @param array $opt annotation parameters. Possible values are described on official PDF32000_2008 reference.
		* @param float $x Abscissa of the upper-left corner of the rectangle
		* @param float $y Ordinate of the upper-left corner of the rectangle
		* @param boolean $js if true put the field using JavaScript (requires Acrobat Writer to be rendered).
		* @access public
		* @author Nicola Asuni
		* @since 4.8.000 (2009-09-07)
		*/
		public function Button($name, $w, $h, $caption, $action, $prop=array(), $opt=array(), $x='', $y='', $js=false) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			if ($js) {
				$this->_addfield('button', $name, $this->x, $this->y, $w, $h, $prop);
				$this->javascript .= 'f'.$name.".buttonSetCaption('".addslashes($caption)."');\n";
				$this->javascript .= 'f'.$name.".setAction('MouseUp','".addslashes($action)."');\n";
				$this->javascript .= 'f'.$name.".highlight='push';\n";
				$this->javascript .= 'f'.$name.".print=false;\n";
				return;
			}
			// get default style
			$prop = array_merge($this->getFormDefaultProp(), $prop);
			$prop['Pushbutton'] = 'true';
			$prop['highlight'] = 'push';
			$prop['display'] = 'display.noPrint';
			// get annotation data
			$popt = $this->getAnnotOptFromJSProp($prop);
			// set additional default options
			if (!isset($popt['mk'])) {
				$popt['mk'] = array();
			}
			$popt['mk']['ca'] = $this->_textstring($caption);
			$popt['mk']['rc'] = $this->_textstring($caption);
			$popt['mk']['ac'] = $this->_textstring($caption);
			$font = $this->FontFamily;
			$fontkey = array_search($font, $this->fontkeys);
			if (!in_array($fontkey, $this->annotation_fonts)) {
				$this->annotation_fonts[$font] = $fontkey;
			}
			$fontstyle = sprintf('/F%d %.2F Tf %s', ($fontkey + 1), $this->FontSizePt, $this->TextColor);
			$popt['da'] = $fontstyle;
			$popt['ap'] = array();
			$popt['ap']['n'] = 'q BT '.$fontstyle.' ET Q';
			// merge options
			$opt = array_merge($popt, $opt);
			// set remaining annotation data
			$opt['Subtype'] = 'Widget';
			$opt['ft'] = 'Btn';
			$opt['t'] = $caption;
			$opt['v'] = $name;
			if (!empty($action)) {
				if (is_array($action)) {
					// form action options as on section 12.7.5 of PDF32000_2008.
					$opt['aa'] = '/D <<';
					$bmode = array('SubmitForm', 'ResetForm', 'ImportData');
					foreach ($action AS $key => $val) {
						if (($key == 'S') AND in_array($val, $bmode)) {
							$opt['aa'] .= ' /S /'.$val;
						} elseif (($key == 'F') AND (!empty($val))) {
							$opt['aa'] .= ' /F '.$this->_datastring($val);
						} elseif (($key == 'Fields') AND is_array($val) AND !empty($val)) {
							$opt['aa'] .= ' /Fields [';
							foreach ($val AS $field) {
								$opt['aa'] .= ' '.$this->_textstring($field);
							}
							$opt['aa'] .= ']';
						} elseif (($key == 'Flags')) {
							$ff = 0;
							if (is_array($val)) {
								foreach ($val AS $flag) {
									switch ($flag) {
										case 'Include/Exclude': {
											$ff += 1 << 0;
											break;
										}
										case 'IncludeNoValueFields': {
											$ff += 1 << 1;
											break;
										}
										case 'ExportFormat': {
											$ff += 1 << 2;
											break;
										}
										case 'GetMethod': {
											$ff += 1 << 3;
											break;
										}
										case 'SubmitCoordinates': {
											$ff += 1 << 4;
											break;
										}
										case 'XFDF': {
											$ff += 1 << 5;
											break;
										}
										case 'IncludeAppendSaves': {
											$ff += 1 << 6;
											break;
										}
										case 'IncludeAnnotations': {
											$ff += 1 << 7;
											break;
										}
										case 'SubmitPDF': {
											$ff += 1 << 8;
											break;
										}
										case 'CanonicalFormat': {
											$ff += 1 << 9;
											break;
										}
										case 'ExclNonUserAnnots': {
											$ff += 1 << 10;
											break;
										}
										case 'ExclFKey': {
											$ff += 1 << 11;
											break;
										}
										case 'EmbedForm': {
											$ff += 1 << 13;
											break;
										}
									}
								}
							} else {
								$ff = intval($val);
							}
							$opt['aa'] .= ' /Flags '.$ff;
						}
					}
					$opt['aa'] .= ' >>';
				} else {
					// Javascript action or raw action command
					$js_obj_id = $this->addJavascriptObject($action);
					$opt['aa'] = '/D '.$js_obj_id.' 0 R';
				}
			}
			$this->Annotation($x, $y, $w, $h, $name, $opt, 0);
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
		}
		
		// --- END FORMS FIELDS ------------------------------------------------
		
		/*
		* Add certification signature (DocMDP or UR3)
		* You can set only one signature type
		* @access protected
		* @author Nicola Asuni
		* @since 4.6.008 (2009-05-07)
		*/
		protected function _putsignature() {
			if ((!$this->sign) OR (!isset($this->signature_data['cert_type']))) {
				return;
			}
			$this->_out('/Type /Sig');
			$this->_out('/Filter /Adobe.PPKLite');
			$this->_out('/SubFilter /adbe.pkcs7.detached');
			$this->_out($this->byterange_string);
			$this->_out('/Contents<>'.str_repeat(' ', $this->signature_max_lenght));
			$this->_out('/Reference');
			$this->_out('[');
			$this->_out('<<');
			$this->_out('/Type /SigRef');
			if ($this->signature_data['cert_type'] > 0) {
				$this->_out('/TransformMethod /DocMDP');
				$this->_out('/TransformParams');
				$this->_out('<<');
				$this->_out('/Type /TransformParams');
				$this->_out('/V /1.2');
				$this->_out('/P '.$this->signature_data['cert_type'].'');
			} else {
				$this->_out('/TransformMethod /UR3');
				$this->_out('/TransformParams');
				$this->_out('<<');
				$this->_out('/Type /TransformParams');
				$this->_out('/V /2.2');
				if (!$this->empty_string($this->ur_document)) {
					$this->_out('/Document['.$this->ur_document.']');
				}
				if (!$this->empty_string($this->ur_annots)) {
					$this->_out('/Annots['.$this->ur_annots.']');
				}
				if (!$this->empty_string($this->ur_form)) {
					$this->_out('/Form['.$this->ur_form.']');
				}
				if (!$this->empty_string($this->ur_signature)) {
					$this->_out('/Signature['.$this->ur_signature.']');
				}
			}
			$this->_out('>>');
			$this->_out('>>');
			$this->_out(']');
			if (isset($this->signature_data['info']['Name']) AND !$this->empty_string($this->signature_data['info']['Name'])) {
				$this->_out('/Name '.$this->_textstring($this->signature_data['info']['Name']).'');
			}
			if (isset($this->signature_data['info']['Location']) AND !$this->empty_string($this->signature_data['info']['Location'])) {
				$this->_out('/Location '.$this->_textstring($this->signature_data['info']['Location']).'');
			}
			if (isset($this->signature_data['info']['Reason']) AND !$this->empty_string($this->signature_data['info']['Reason'])) {
				$this->_out('/Reason '.$this->_textstring($this->signature_data['info']['Reason']).'');
			}
			if (isset($this->signature_data['info']['ContactInfo']) AND !$this->empty_string($this->signature_data['info']['ContactInfo'])) {
				$this->_out('/ContactInfo '.$this->_textstring($this->signature_data['info']['ContactInfo']).'');
			}
			$this->_out('/M '.$this->_datestring());
		}
		
		/*
		* Set User's Rights for PDF Reader
		* WARNING: This works only using the Adobe private key with the setSignature() method!.
		* Check the PDF Reference 8.7.1 Transform Methods, 
		* Table 8.105 Entries in the UR transform parameters dictionary
		* @param boolean $enable if true enable user's rights on PDF reader
		* @param string $document Names specifying additional document-wide usage rights for the document. The only defined value is "/FullSave", which permits a user to save the document along with modified form and/or annotation data.
		* @param string $annots Names specifying additional annotation-related usage rights for the document. Valid names in PDF 1.5 and later are /Create/Delete/Modify/Copy/Import/Export, which permit the user to perform the named operation on annotations.
		* @param string $form Names specifying additional form-field-related usage rights for the document. Valid names are: /Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate 
		* @param string $signature Names specifying additional signature-related usage rights for the document. The only defined value is /Modify, which permits a user to apply a digital signature to an existing signature form field or clear a signed signature form field.
		* @access public
		* @author Nicola Asuni
		* @since 2.9.000 (2008-03-26)
		*/
		public function setUserRights(
				$enable=true, 
				$document='/FullSave',
				$annots='/Create/Delete/Modify/Copy/Import/Export',
				$form='/Add/Delete/FillIn/Import/Export/SubmitStandalone/SpawnTemplate',
				$signature='/Modify') {
			$this->ur = $enable;
			$this->ur_document = $document;
			$this->ur_annots = $annots;
			$this->ur_form = $form;
			$this->ur_signature = $signature;
			if (!$this->sign) {
				// This signature only works using the Adobe Private key that is unavailable!
				$this->setSignature('', '', '', '', 0, array());
			}
		}
		
		/*
		* Enable document signature (requires the OpenSSL Library).
		* The digital signature improve document authenticity and integrity and allows o enable extra features on Acrobat Reader.
		* @param mixed $signing_cert signing certificate (string or filename prefixed with 'file://')
		* @param mixed $private_key private key (string or filename prefixed with 'file://')
		* @param string $private_key_password password
		* @param string $extracerts specifies the name of a file containing a bunch of extra certificates to include in the signature which can for example be used to help the recipient to verify the certificate that you used.
		* @param int $cert_type The access permissions granted for this document. Valid values shall be: 1 = No changes to the document shall be permitted; any change to the document shall invalidate the signature; 2 = Permitted changes shall be filling in forms, instantiating page templates, and signing; other changes shall invalidate the signature; 3 = Permitted changes shall be the same as for 2, as well as annotation creation, deletion, and modification; other changes shall invalidate the signature.
		* @parm array $info array of option information: Name, Location, Reason, ContactInfo.
		* @access public
		* @author Nicola Asuni
		* @since 4.6.005 (2009-04-24)
		*/
		public function setSignature($signing_cert='', $private_key='', $private_key_password='', $extracerts='', $cert_type=2, $info=array()) {
			// to create self-signed signature: openssl req -x509 -nodes -days 365000 -newkey rsa:1024 -keyout tcpdf.crt -out tcpdf.crt
			// to convert pfx certificate to pem: openssl
			//     OpenSSL> pkcs12 -in <cert.pfx> -out <cert.crt> -nodes
			$this->sign = true;
			$this->signature_data = array();
			if (strlen($signing_cert) == 0) {
				$signing_cert = 'file://'.dirname(__FILE__).'/tcpdf.crt';
				$private_key_password = 'tcpdfdemo';
			}
			if (strlen($private_key) == 0) {
				$private_key = $signing_cert;
			}
			$this->signature_data['signcert'] = $signing_cert;
			$this->signature_data['privkey'] = $private_key;
			$this->signature_data['password'] = $private_key_password;
			$this->signature_data['extracerts'] = $extracerts;
			$this->signature_data['cert_type'] = $cert_type;
			$this->signature_data['info'] = $info;
		}
		
		/*
		* Create a new page group.
		* NOTE: call this function before calling AddPage()
		* @param int $page starting group page (leave empty for next page).
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function startPageGroup($page='') {
			if (empty($page)) {
				$page = $this->page + 1;
			}
			$this->newpagegroup[$page] = true;
		}

		/**
		* Defines an alias for the total number of pages.
		* It will be substituted as the document is closed.
		* @param string $alias The alias.
		* @access public
		* @since 1.4
		* @see getAliasNbPages(), PageNo(), Footer()
		*/
		public function AliasNbPages($alias='{nb}') {
			$this->AliasNbPages = $alias;
		}
		
		/**
		 * Returns the string alias used for the total number of pages.
         * If the current font is unicode type, the returned string is surrounded by additional curly braces.
		 * @return string
		 * @access public
		 * @since 4.0.018 (2008-08-08)
		 * @see AliasNbPages(), PageNo(), Footer()
		*/
		public function getAliasNbPages() {
			if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
				return '{'.$this->AliasNbPages.'}';
            }
			return $this->AliasNbPages;
		}

		/**
		* Defines an alias for the page number.
		* It will be substituted as the document is closed.
		* @param string $alias The alias.
		* @access public
		* @since 4.5.000 (2009-01-02)
		* @see getAliasNbPages(), PageNo(), Footer()
		*/
		public function AliasNumPage($alias='{pnb}') {
			//Define an alias for total number of pages
			$this->AliasNumPage = $alias;
		}
		
		/**
		 * Returns the string alias used for the page number.
         * If the current font is unicode type, the returned string is surrounded by additional curly braces.
		 * @return string
		 * @access public
		 * @since 4.5.000 (2009-01-02)
		 * @see AliasNbPages(), PageNo(), Footer()
		*/
		public function getAliasNumPage() {
			if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
				return '{'.$this->AliasNumPage.'}';
            }
			return $this->AliasNumPage;
		}
		
		/*
		* Return the current page in the group.
		* @return current page in the group
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function getGroupPageNo() {
			return $this->pagegroups[$this->currpagegroup];
		}

		/**
		* Returns the current group page number formatted as a string.
		* @access public
		* @since 4.3.003 (2008-11-18)
		* @see PaneNo(), formatPageNumber()
		*/
		public function getGroupPageNoFormatted() {
			return $this->formatPageNumber($this->getGroupPageNo());
        }
		
		/*
		 * Return the alias of the current page group
         * If the current font is unicode type, the returned string is surrounded by additional curly braces.
		 * (will be replaced by the total number of pages in this group).
		 * @return alias of the current page group
		 * @access public
		 * @since 3.0.000 (2008-03-27)
		*/
		public function getPageGroupAlias() {
			if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
				return '{'.$this->currpagegroup.'}';
            }
			return $this->currpagegroup;
		}
		
		/*
		 * Return the alias for the page number on the current page group
         * If the current font is unicode type, the returned string is surrounded by additional curly braces.
		 * (will be replaced by the total number of pages in this group).
		 * @return alias of the current page group
		 * @access public
		 * @since 4.5.000 (2009-01-02)
		*/
		public function getPageNumGroupAlias() {
			if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
				return '{'.str_replace('{nb', '{pnb', $this->currpagegroup).'}';
            }
			return str_replace('{nb', '{pnb', $this->currpagegroup);
		}

		/**
		* Format the page numbers.
		* This method can be overriden for custom formats.
		* @param int $num page number
		* @access protected
		* @since 4.2.005 (2008-11-06)
		*/
		protected function formatPageNumber($num) {
			return number_format((float)$num, 0, '', '.');
		}

		/**
		* Format the page numbers on the Table Of Content.
		* This method can be overriden for custom formats.
		* @param int $num page number
		* @access protected
		* @since 4.5.001 (2009-01-04)
		* @see addTOC()
		*/
		protected function formatTOCPageNumber($num) {
			return number_format((float)$num, 0, '', '.');
		}

        /**
		* Returns the current page number formatted as a string.
		* @access public
		* @since 4.2.005 (2008-11-06)
		* @see PaneNo(), formatPageNumber()
		*/
		public function PageNoFormatted() {
			return $this->formatPageNumber($this->PageNo());
        }

        /*
		* Put visibility settings.
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function _putocg() {
			$this->_newobj();
			$this->n_ocg_print = $this->n;
			$this->_out('<</Type /OCG /Name '.$this->_textstring('print'));
			$this->_out('/Usage <</Print <</PrintState /ON>> /View <</ViewState /OFF>>>>>>');
			$this->_out('endobj');
			$this->_newobj();
			$this->n_ocg_view = $this->n;
			$this->_out('<</Type /OCG /Name '.$this->_textstring('view'));
			$this->_out('/Usage <</Print <</PrintState /OFF>> /View <</ViewState /ON>>>>>>');
			$this->_out('endobj');
		}
		
		/*
		* Set the visibility of the successive elements.
		* This can be useful, for instance, to put a background 
		* image or color that will show on screen but won't print.
		* @param string $v visibility mode. Legal values are: all, print, screen.
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function setVisibility($v) {
			if ($this->openMarkedContent) {
				// close existing open marked-content
				$this->_out('EMC');
				$this->openMarkedContent = false;
			}
			switch($v) {
				case 'print': {
					$this->_out('/OC /OC1 BDC');
					$this->openMarkedContent = true;
					break;
				}
				case 'screen': {
					$this->_out('/OC /OC2 BDC');
					$this->openMarkedContent = true;
					break;
				}
				case 'all': {
					$this->_out('');
					break;
				}
				default: {
					$this->Error('Incorrect visibility: '.$v);
					break;
				}
			}
			$this->visibility = $v;
		}
		
		/*
		* Add transparency parameters to the current extgstate
		* @param array $params parameters
		* @return the number of extgstates
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function addExtGState($parms) {
			$n = count($this->extgstates) + 1;
			$this->extgstates[$n]['parms'] = $parms;
			return $n;
		}
		
		/*
		* Add an extgstate
		* @param array $gs extgstate
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function setExtGState($gs) {
			$this->_out(sprintf('/GS%d gs', $gs));
		}
		
		/*
		* Put extgstates for object transparency
		* @param array $gs extgstate
		* @access protected
		* @since 3.0.000 (2008-03-27)
		*/
		protected function _putextgstates() {
			$ne = count($this->extgstates);
			for ($i = 1; $i <= $ne; ++$i) {
				$this->_newobj();
				$this->extgstates[$i]['n'] = $this->n;
				$this->_out('<</Type /ExtGState');
				foreach ($this->extgstates[$i]['parms'] as $k => $v) {
					$this->_out('/'.$k.' '.$v);
				}
				$this->_out('>>');
				$this->_out('endobj');
			}
		}
		
		/*
		* Set alpha for stroking (CA) and non-stroking (ca) operations.
		* @param float $alpha real value from 0 (transparent) to 1 (opaque)
		* @param string $bm blend mode, one of the following: Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn, HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function setAlpha($alpha, $bm='Normal') {
			$gs = $this->addExtGState(array('ca' => $alpha, 'CA' => $alpha, 'BM' => '/'.$bm));
			$this->setExtGState($gs);
		}

		/*
		* Set the default JPEG compression quality (1-100)
		* @param int $quality JPEG quality, integer between 1 and 100
		* @access public
		* @since 3.0.000 (2008-03-27)
		*/
		public function setJPEGQuality($quality) {
			if (($quality < 1) OR ($quality > 100)) {
				$quality = 75;
			}
			$this->jpeg_quality = intval($quality);
		}
		
		/*
		* Set the default number of columns in a row for HTML tables.
		* @param int $cols number of columns
		* @access public
		* @since 3.0.014 (2008-06-04)
		*/
		public function setDefaultTableColumns($cols=4) { 
			$this->default_table_columns = intval($cols); 
		}
		
		/*
		* Set the height of the cell (line height) respect the font height.
		* @param int $h cell proportion respect font height (typical value = 1.25).
		* @access public
		* @since 3.0.014 (2008-06-04)
		*/
		public function setCellHeightRatio($h) { 
			$this->cell_height_ratio = $h; 
		}
		
		/*
		* return the height of cell repect font height.
		* @access public
		* @since 4.0.012 (2008-07-24)
		*/
		public function getCellHeightRatio() { 
			return $this->cell_height_ratio; 
		}
		
		/*
		* Set the PDF version (check PDF reference for valid values).
		* Default value is 1.t
		* @access public
		* @since 3.1.000 (2008-06-09)
		*/
		public function setPDFVersion($version='1.7') { 
			$this->PDFVersion = $version;
		}
		
		/*
		* Set the viewer preferences dictionary controlling the way the document is to be presented on the screen or in print.
		* (see Section 8.1 of PDF reference, "Viewer Preferences").
		* <ul>
		* <li>HideToolbar boolean (Optional) A flag specifying whether to hide the viewer application's tool bars when the document is active. Default value: false.</li>
		* <li>HideMenubar boolean (Optional) A flag specifying whether to hide the viewer application's menu bar when the document is active. Default value: false.</li>
		* <li>HideWindowUI boolean (Optional) A flag specifying whether to hide user interface elements in the document's window (such as scroll bars and navigation controls), leaving only the document's contents displayed. Default value: false.</li>
		* <li>FitWindow boolean (Optional) A flag specifying whether to resize the document's window to fit the size of the first displayed page. Default value: false.</li>
		* <li>CenterWindow boolean (Optional) A flag specifying whether to position the document's window in the center of the screen. Default value: false.</li>
		* <li>DisplayDocTitle boolean (Optional; PDF 1.4) A flag specifying whether the window's title bar should display the document title taken from the Title entry of the document information dictionary (see Section 10.2.1, "Document Information Dictionary"). If false, the title bar should instead display the name of the PDF file containing the document. Default value: false.</li>
		* <li>NonFullScreenPageMode name (Optional) The document's page mode, specifying how to display the document on exiting full-screen mode:<ul><li>UseNone Neither document outline nor thumbnail images visible</li><li>UseOutlines Document outline visible</li><li>UseThumbs Thumbnail images visible</li><li>UseOC Optional content group panel visible</li><ul>This entry is meaningful only if the value of the PageMode entry in the catalog dictionary (see Section 3.6.1, "Document Catalog") is FullScreen; it is ignored otherwise. Default value: UseNone.</li>
		* <li>ViewArea name (Optional; PDF 1.4) The name of the page boundary representing the area of a page to be displayed when viewing the document on the screen. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>ViewClip name (Optional; PDF 1.4) The name of the page boundary to which the contents of a page are to be clipped when viewing the document on the screen. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>PrintArea name (Optional; PDF 1.4) The name of the page boundary representing the area of a page to be rendered when printing the document. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>PrintClip name (Optional; PDF 1.4) The name of the page boundary to which the contents of a page are to be clipped when printing the document. Valid values are (see Section 10.10.1, "Page Boundaries").:<ul><li>MediaBox</li><li>CropBox (default)</li><li>BleedBox</li><li>TrimBox</li><li>ArtBox</li></ul></li>
		* <li>PrintScaling name (Optional; PDF 1.6) The page scaling option to be selected when a print dialog is displayed for this document. Valid values are: <ul><li>None, which indicates that the print dialog should reflect no page scaling</li><li>AppDefault (default), which indicates that applications should use the current print scaling</li><ul></li>
		* <li>Duplex name (Optional; PDF 1.7) The paper handling option to use when printing the file from the print dialog. The following values are valid:<ul><li>Simplex - Print single-sided</li><li>DuplexFlipShortEdge - Duplex and flip on the short edge of the sheet</li><li>DuplexFlipLongEdge - Duplex and flip on the long edge of the sheet</li></ul>Default value: none</li>
		* <li>PickTrayByPDFSize boolean (Optional; PDF 1.7) A flag specifying whether the PDF page size is used to select the input paper tray. This setting influences only the preset values used to populate the print dialog presented by a PDF viewer application. If PickTrayByPDFSize is true, the check box in the print dialog associated with input paper tray is checked. Note: This setting has no effect on Mac OS systems, which do not provide the ability to pick the input tray by size.</li>
		* <li>PrintPageRange array (Optional; PDF 1.7) The page numbers used to initialize the print dialog box when the file is printed. The first page of the PDF file is denoted by 1. Each pair consists of the first and last pages in the sub-range. An odd number of integers causes this entry to be ignored. Negative numbers cause the entire array to be ignored. Default value: as defined by PDF viewer application</li>
		* <li>NumCopies integer (Optional; PDF 1.7) The number of copies to be printed when the print dialog is opened for this file. Supported values are the integers 2 through 5. Values outside this range are ignored. Default value: as defined by PDF viewer application, but typically 1</li>
		* </ul>
		* @param array $preferences array of options.
		* @author Nicola Asuni
		* @access public
		* @since 3.1.000 (2008-06-09)
		*/
		public function setViewerPreferences($preferences) { 
			$this->viewer_preferences = $preferences;
		}
		
		/**
		* Paints a linear colour gradient.
		* @param float $x abscissa of the top left corner of the rectangle.
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @param array $col1 first color (RGB components).
		* @param array $col2 second color (RGB components).
		* @param array $coords array of the form (x1, y1, x2, y2) which defines the gradient vector (see linear_gradient_coords.jpg). The default value is from left to right (x1=0, y1=0, x2=1, y2=0).
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function LinearGradient($x, $y, $w, $h, $col1=array(), $col2=array(), $coords=array(0,0,1,0)) {
			$this->Clip($x, $y, $w, $h);
			$this->Gradient(2, $col1, $col2, $coords);
		}
		
		/**
		* Paints a radial colour gradient.
		* @param float $x abscissa of the top left corner of the rectangle.
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @param array $col1 first color (RGB components).
		* @param array $col2 second color (RGB components).
		* @param array $coords array of the form (fx, fy, cx, cy, r) where (fx, fy) is the starting point of the gradient with color1, (cx, cy) is the center of the circle with color2, and r is the radius of the circle (see radial_gradient_coords.jpg). (fx, fy) should be inside the circle, otherwise some areas will not be defined.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function RadialGradient($x, $y, $w, $h, $col1=array(), $col2=array(), $coords=array(0.5,0.5,0.5,0.5,1)) {
			$this->Clip($x, $y, $w, $h);
			$this->Gradient(3, $col1, $col2, $coords);
		}
		
		/**
		* Paints a coons patch mesh.
		* @param float $x abscissa of the top left corner of the rectangle.
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @param array $col1 first color (lower left corner) (RGB components).
		* @param array $col2 second color (lower right corner) (RGB components).
		* @param array $col3 third color (upper right corner) (RGB components).
		* @param array $col4 fourth color (upper left corner) (RGB components).
		* @param array $coords <ul><li>for one patch mesh: array(float x1, float y1, .... float x12, float y12): 12 pairs of coordinates (normally from 0 to 1) which specify the Bezier control points that define the patch. First pair is the lower left edge point, next is its right control point (control point 2). Then the other points are defined in the order: control point 1, edge point, control point 2 going counter-clockwise around the patch. Last (x12, y12) is the first edge point's left control point (control point 1).</li><li>for two or more patch meshes: array[number of patches]: arrays with the following keys for each patch: f: where to put that patch (0 = first patch, 1, 2, 3 = right, top and left of precedent patch - I didn't figure this out completely - just try and error ;-) points: 12 pairs of coordinates of the Bezier control points as above for the first patch, 8 pairs of coordinates for the following patches, ignoring the coordinates already defined by the precedent patch (I also didn't figure out the order of these - also: try and see what's happening) colors: must be 4 colors for the first patch, 2 colors for the following patches</li></ul>
		* @param array $coords_min minimum value used by the coordinates. If a coordinate's value is smaller than this it will be cut to coords_min. default: 0
		* @param array $coords_max maximum value used by the coordinates. If a coordinate's value is greater than this it will be cut to coords_max. default: 1
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function CoonsPatchMesh($x, $y, $w, $h, $col1=array(), $col2=array(), $col3=array(), $col4=array(), $coords=array(0.00,0.0,0.33,0.00,0.67,0.00,1.00,0.00,1.00,0.33,1.00,0.67,1.00,1.00,0.67,1.00,0.33,1.00,0.00,1.00,0.00,0.67,0.00,0.33), $coords_min=0, $coords_max=1) {
			$this->Clip($x, $y, $w, $h);        
			$n = count($this->gradients) + 1;
			$this->gradients[$n]['type'] = 6; //coons patch mesh
			//check the coords array if it is the simple array or the multi patch array
			if (!isset($coords[0]['f'])) {
				//simple array -> convert to multi patch array
				if (!isset($col1[1])) {
					$col1[1] = $col1[2] = $col1[0];
				}
				if (!isset($col2[1])) {
					$col2[1] = $col2[2] = $col2[0];
				}
				if (!isset($col3[1])) {
					$col3[1] = $col3[2] = $col3[0];
				}
				if (!isset($col4[1])) {
					$col4[1] = $col4[2] = $col4[0];
				}
				$patch_array[0]['f'] = 0;
				$patch_array[0]['points'] = $coords;
				$patch_array[0]['colors'][0]['r'] = $col1[0];
				$patch_array[0]['colors'][0]['g'] = $col1[1];
				$patch_array[0]['colors'][0]['b'] = $col1[2];
				$patch_array[0]['colors'][1]['r'] = $col2[0];
				$patch_array[0]['colors'][1]['g'] = $col2[1];
				$patch_array[0]['colors'][1]['b'] = $col2[2];
				$patch_array[0]['colors'][2]['r'] = $col3[0];
				$patch_array[0]['colors'][2]['g'] = $col3[1];
				$patch_array[0]['colors'][2]['b'] = $col3[2];
				$patch_array[0]['colors'][3]['r'] = $col4[0];
				$patch_array[0]['colors'][3]['g'] = $col4[1];
				$patch_array[0]['colors'][3]['b'] = $col4[2];
			} else {
				//multi patch array
				$patch_array = $coords;
			}
			$bpcd = 65535; //16 BitsPerCoordinate
			//build the data stream
			$this->gradients[$n]['stream'] = '';
			$count_patch = count($patch_array);
			for ($i=0; $i < $count_patch; ++$i) {
				$this->gradients[$n]['stream'] .= chr($patch_array[$i]['f']); //start with the edge flag as 8 bit
				$count_points = count($patch_array[$i]['points']);
				for ($j=0; $j < $count_points; ++$j) {
					//each point as 16 bit
					$patch_array[$i]['points'][$j] = (($patch_array[$i]['points'][$j] - $coords_min) / ($coords_max - $coords_min)) * $bpcd;
					if ($patch_array[$i]['points'][$j] < 0) {
						$patch_array[$i]['points'][$j] = 0;
					}
					if ($patch_array[$i]['points'][$j] > $bpcd) {
						$patch_array[$i]['points'][$j] = $bpcd;
					}
					$this->gradients[$n]['stream'] .= chr(floor($patch_array[$i]['points'][$j] / 256));
					$this->gradients[$n]['stream'] .= chr(floor($patch_array[$i]['points'][$j] % 256));
				}
				$count_cols = count($patch_array[$i]['colors']);
				for ($j=0; $j < $count_cols; ++$j) {
					//each color component as 8 bit
					$this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['r']);
					$this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['g']);
					$this->gradients[$n]['stream'] .= chr($patch_array[$i]['colors'][$j]['b']);
				}
			}
			//paint the gradient
			$this->_out('/Sh'.$n.' sh');
			//restore previous Graphic State
			$this->_out('Q');
		}
		
		/**
		* Set a rectangular clipping area.
		* @param float $x abscissa of the top left corner of the rectangle (or top right corner for RTL mode).
		* @param float $y ordinate of the top left corner of the rectangle.
		* @param float $w width of the rectangle.
		* @param float $h height of the rectangle.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function Clip($x, $y, $w, $h) {
			if ($this->rtl) {
				$x = $this->w - $x - $w;
			}
			//save current Graphic State
			$s = 'q';
			//set clipping area
			$s .= sprintf(' %.2F %.2F %.2F %.2F re W n', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k);
			//set up transformation matrix for gradient
			$s .= sprintf(' %.3F 0 0 %.3F %.3F %.3F cm', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k);
			$this->_out($s);
		}
				
		/**
		* Output gradient.
		* @param int $type type of gradient.
		* @param array $col1 first color (RGB components).
		* @param array $col2 second color (RGB components).
		* @param array $coords array of coordinates.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function Gradient($type, $col1, $col2, $coords) {
			$n = count($this->gradients) + 1;
			$this->gradients[$n]['type'] = $type;
			if (!isset($col1[1])) {
				$col1[1]=$col1[2]=$col1[0];
			}
			$this->gradients[$n]['col1'] = sprintf('%.3F %.3F %.3F', ($col1[0]/255), ($col1[1]/255), ($col1[2]/255));
			if (!isset($col2[1])) {
				$col2[1] = $col2[2] = $col2[0];
			}
			$this->gradients[$n]['col2'] = sprintf('%.3F %.3F %.3F', ($col2[0]/255), ($col2[1]/255), ($col2[2]/255));
			$this->gradients[$n]['coords'] = $coords;
			//paint the gradient
			$this->_out('/Sh'.$n.' sh');
			//restore previous Graphic State
			$this->_out('Q');
		}
		
		/**
		* Output shaders.
		* @author Andreas Würmser, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		function _putshaders() {
			foreach ($this->gradients as $id => $grad) {  
				if (($grad['type'] == 2) OR ($grad['type'] == 3)) {
					$this->_newobj();
					$this->_out('<<');
					$this->_out('/FunctionType 2');
					$this->_out('/Domain [0.0 1.0]');
					$this->_out('/C0 ['.$grad['col1'].']');
					$this->_out('/C1 ['.$grad['col2'].']');
					$this->_out('/N 1');
					$this->_out('>>');
					$this->_out('endobj');
					$f1 = $this->n;
				}
				$this->_newobj();
				$this->_out('<<');
				$this->_out('/ShadingType '.$grad['type']);
				$this->_out('/ColorSpace /DeviceRGB');
				if ($grad['type'] == 2) {
					$this->_out(sprintf('/Coords [%.3F %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3]));
					$this->_out('/Function '.$f1.' 0 R');
					$this->_out('/Extend [true true] ');
					$this->_out('>>');
				} elseif ($grad['type'] == 3) {
					//x0, y0, r0, x1, y1, r1
					//at this this time radius of inner circle is 0
					$this->_out(sprintf('/Coords [%.3F %.3F 0 %.3F %.3F %.3F]', $grad['coords'][0], $grad['coords'][1], $grad['coords'][2], $grad['coords'][3], $grad['coords'][4]));
					$this->_out('/Function '.$f1.' 0 R');
					$this->_out('/Extend [true true] ');
					$this->_out('>>');
				} elseif ($grad['type'] == 6) {
					$this->_out('/BitsPerCoordinate 16');
					$this->_out('/BitsPerComponent 8');
					$this->_out('/Decode[0 1 0 1 0 1 0 1 0 1]');
					$this->_out('/BitsPerFlag 8');
					$this->_out('/Length '.strlen($grad['stream']));
					$this->_out('>>');
					$this->_putstream($grad['stream']);
				}
				$this->_out('endobj');
				$this->gradients[$id]['id'] = $this->n;
			}
		}

		/**
		* Output an arc
		* @author Maxime Delorme, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access protected
		*/
		protected function _outarc($x1, $y1, $x2, $y2, $x3, $y3 ) {
			$h = $this->h;
			$this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1*$this->k, ($h-$y1)*$this->k, $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
		}
		
		/**
		* Draw the sector of a circle.
		* It can be used for instance to render pie charts.
		* @param float $xc abscissa of the center.
		* @param float $yc ordinate of the center.
		* @param float $r radius.
		* @param float $a start angle (in degrees).
		* @param float $b end angle (in degrees).
		* @param string $style: D, F, FD or DF (draw, fill, fill and draw). Default: FD.
		* @param float $cw: indicates whether to go clockwise (default: true).
		* @param float $o: origin of angles (0 for 3 o'clock, 90 for noon, 180 for 9 o'clock, 270 for 6 o'clock). Default: 90.
		* @author Maxime Delorme, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function PieSector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90) {
			if ($this->rtl) {
				$xc = $this->w - $xc;
			}
			if ($cw) {
				$d = $b;
				$b = $o - $a;
				$a = $o - $d;
			} else {
				$b += $o;
				$a += $o;
			}
			$a = ($a % 360) + 360;
			$b = ($b % 360) + 360;
			if ($a > $b) {
				$b +=360;
			}
			$b = $b / 360 * 2 * M_PI;
			$a = $a / 360 * 2 * M_PI;
			$d = $b - $a;
			if ($d == 0 ) {
				$d = 2 * M_PI;
			}
			$k = $this->k;
			$hp = $this->h;
			if ($style=='F') {
				$op = 'f';
			} elseif ($style=='FD' or $style=='DF') {
				$op = 'b';
			} else {
				$op = 's';
			}
			if (sin($d/2)) {
				$MyArc = 4/3 * (1 - cos($d/2)) / sin($d/2) * $r;
			}
			//first put the center
			$this->_out(sprintf('%.2F %.2F m', ($xc)*$k, ($hp-$yc)*$k));
			//put the first point
			$this->_out(sprintf('%.2F %.2F l', ($xc+$r*cos($a))*$k, (($hp-($yc-$r*sin($a)))*$k)));
			//draw the arc
			if ($d < (M_PI/2)) {
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
			} else {
				$b = $a + $d/4;
				$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
				$a = $b;
				$b = $a + $d/4;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
				$a = $b;
				$b = $a + $d/4;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b) );
				$a = $b;
				$b = $a + $d/4;
				$this->_outarc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a), $yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a), $xc+$r*cos($b)+$MyArc*cos($b-M_PI/2), $yc-$r*sin($b)-$MyArc*sin($b-M_PI/2), $xc+$r*cos($b), $yc-$r*sin($b));
			}
			//terminate drawing
			$this->_out($op);
		}
		
		/**
		* Embed vector-based Adobe Illustrator (AI) or AI-compatible EPS files.
		* Only vector drawing is supported, not text or bitmap. 
		* Although the script was successfully tested with various AI format versions, best results are probably achieved with files that were exported in the AI3 format (tested with Illustrator CS2, Freehand MX and Photoshop CS2).
		* @param string $file Name of the file containing the image.
		* @param float $x Abscissa of the upper-left corner.
		* @param float $y Ordinate of the upper-left corner.
		* @param float $w Width of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param float $h Height of the image in the page. If not specified or equal to zero, it is automatically calculated.
		* @param mixed $link URL or identifier returned by AddLink().
		* @param boolean useBoundingBox specifies whether to position the bounding box (true) or the complete canvas (false) at location (x,y). Default value is true.
		* @param string $align Indicates the alignment of the pointer next to image insertion relative to image height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		* @param string $palign Allows to center or align the image on the current line. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		* @param mixed $border Indicates if borders must be drawn around the image. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		* @author Valentin Schmidt, Nicola Asuni
		* @since 3.1.000 (2008-06-09)
		* @access public
		*/
		public function ImageEps($file, $x='', $y='', $w=0, $h=0, $link='', $useBoundingBox=true, $align='', $palign='', $border=0) {
			if ($x === '') {
				$x = $this->x;
			}
			if ($y === '') {
				$y = $this->y;
			}
			$k = $this->k;
			$data = file_get_contents($file);
			if ($data === false) {
				$this->Error('EPS file not found: '.$file);
			}
			$regs = array();
			// EPS/AI compatibility check (only checks files created by Adobe Illustrator!)
			preg_match("/%%Creator:([^\r\n]+)/", $data, $regs); # find Creator
			if (count($regs) > 1) {
				$version_str = trim($regs[1]); # e.g. "Adobe Illustrator(R) 8.0"
				if (strpos($version_str, 'Adobe Illustrator') !== false) {
					$versexp = explode(' ', $version_str);
					$version = (float)array_pop($versexp);
					if ($version >= 9) {
						$this->Error('This version of Adobe Illustrator file is not supported: '.$file);
					}
				}
			}
			// strip binary bytes in front of PS-header
			$start = strpos($data, '%!PS-Adobe');
			if ($start > 0) {
				$data = substr($data, $start);
			}
			// find BoundingBox params
			preg_match("/%%BoundingBox:([^\r\n]+)/", $data, $regs);
			if (count($regs) > 1) {
				list($x1, $y1, $x2, $y2) = explode(' ', trim($regs[1]));
			} else {
				$this->Error('No BoundingBox found in EPS file: '.$file);
			}
			$start = strpos($data, '%%EndSetup');
			if ($start === false) {
				$start = strpos($data, '%%EndProlog');
			}
			if ($start === false) {
				$start = strpos($data, '%%BoundingBox');
			}
			$data = substr($data, $start);
			$end = strpos($data, '%%PageTrailer');
			if ($end===false) {
				$end = strpos($data, 'showpage');
			}
			if ($end) {
				$data = substr($data, 0, $end);
			}
			if ($w > 0) {
				$scale_x = $w / (($x2 - $x1) / $k);
				if ($h > 0) {
					$scale_y = $h / (($y2 - $y1) / $k);
				} else {
					$scale_y = $scale_x;
					$h = ($y2 - $y1) / $k * $scale_y;
				}
			} else {
				if ($h > 0) {
					$scale_y = $h / (($y2 - $y1) / $k);
					$scale_x = $scale_y;
					$w = ($x2-$x1) / $k * $scale_x;
				} else {
					$w = ($x2 - $x1) / $k;
					$h = ($y2 - $y1) / $k;
				}
			}
			// Check whether we need a new page first as this does not fit
			if ($this->checkPageBreak($h, $y)) {
				$y = $this->GetY() + $this->cMargin;
			}
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			// set alignment
			if ($this->rtl) {
				if ($palign == 'L') {
					$ximg = $this->lMargin;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} elseif ($palign == 'C') {
					$ximg = ($this->w - $x - $w) / 2;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} else {
					$ximg = $this->w - $x - $w;
					// set left side coordinate
					$this->img_rb_x = $ximg;
				}
			} else {
				if ($palign == 'R') {
					$ximg = $this->w - $this->rMargin - $w;
					// set left side coordinate
					$this->img_rb_x = $ximg;
				} elseif ($palign == 'C') {
					$ximg = ($this->w - $x - $w) / 2;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				} else {
					$ximg = $x;
					// set right side coordinate
					$this->img_rb_x = $ximg + $w;
				}
			}
			if ($useBoundingBox) {
				$dx = $ximg * $k - $x1;
				$dy = $y * $k - $y1;
			} else {
				$dx = $ximg * $k;
				$dy = $y * $k;
			}
			// save the current graphic state
			$this->_out('q'.$this->epsmarker);
			// translate
			$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', 1, 0, 0, 1, $dx, $dy + ($this->hPt - (2 * $y * $k) - ($y2 - $y1))));
			// scale
			if (isset($scale_x)) {
				$this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F cm', $scale_x, 0, 0, $scale_y, $x1 * (1 - $scale_x), $y2 * (1 - $scale_y)));
			}
			// handle pc/unix/mac line endings
			preg_match('/[\r\n]+/s', $data, $regs);
			$lines = explode($regs[0], $data);
			$u=0;
			$cnt = count($lines);
			for ($i=0; $i < $cnt; ++$i) {
				$line = $lines[$i];
				if (($line == '') OR ($line{0} == '%')) {
					continue;
				}
				$len = strlen($line);
				$chunks = explode(' ', $line);
				$cmd = array_pop($chunks);
				// RGB
				if (($cmd == 'Xa') OR ($cmd == 'XA')) {
					$b = array_pop($chunks); 
					$g = array_pop($chunks); 
					$r = array_pop($chunks);
					$this->_out(''.$r.' '.$g.' '.$b.' '.($cmd=='Xa'?'rg':'RG')); //substr($line, 0, -2).'rg' -> in EPS (AI8): c m y k r g b rg!
					continue;
				}
				switch ($cmd) {
					case 'm':
					case 'l':
					case 'v':
					case 'y':
					case 'c':
					case 'k':
					case 'K':
					case 'g':
					case 'G':
					case 's':
					case 'S':
					case 'J':
					case 'j':
					case 'w':
					case 'M':
					case 'd':
					case 'n':
					case 'v': {
						$this->_out($line);
						break;
					}
					case 'x': {// custom fill color
						list($c,$m,$y,$k) = $chunks;
						$this->_out(''.$c.' '.$m.' '.$y.' '.$k.' k');
						break;
					}
					case 'X': { // custom stroke color
						list($c,$m,$y,$k) = $chunks;
						$this->_out(''.$c.' '.$m.' '.$y.' '.$k.' K');
						break;
					}
					case 'Y':
					case 'N':
					case 'V':
					case 'L':
					case 'C': {
						$line{$len-1} = strtolower($cmd);
						$this->_out($line);
						break;
					}
					case 'b':
					case 'B': {
						$this->_out($cmd . '*');
						break;
					}
					case 'f':
					case 'F': {
						if ($u > 0) {
							$isU = false;
							$max = min($i+5, $cnt);
							for ($j=$i+1; $j < $max; ++$j)
							  $isU = ($isU OR (($lines[$j] == 'U') OR ($lines[$j] == '*U')));
							if ($isU) {
								$this->_out('f*');
							}
						} else {
							$this->_out('f*');
						}
						break;
					}
					case '*u': {
						++$u;
						break;
					}
					case '*U': {
						--$u;
						break;
					}
				}
			}
			// restore previous graphic state
			$this->_out($this->epsmarker.'Q');
			if (!empty($border)) {
				$bx = $x;
				$by = $y;
				$this->x = $x;
				$this->y = $y;
				$this->Cell($w, $h, '', $border, 0, '', 0, '', 0);
				$this->x = $bx;
				$this->y = $by;
			}
			if ($link) {
				$this->Link($ximg, $y, $w, $h, $link, 0);
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T':{
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M':{
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B':{
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N':{
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
			$this->endlinex = $this->img_rb_x;
		}
		
		/**
	 	 * Set document barcode.
		 * @param string $bc barcode
		 * @access public
		 */
		public function setBarcode($bc='') {
			$this->barcode = $bc;
		}
		
		/**
	 	 * Get current barcode.
		 * @return string
		 * @access public
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getBarcode() {
			return $this->barcode;
		}
		
		/**
	 	 * Print a Linear Barcode.
	 	 * @param string $code code to print
	 	 * @param string $type type of barcode.
		 * @param int $x x position in user units
		 * @param int $y y position in user units
		 * @param int $w width in user units
		 * @param int $h height in user units
		 * @param float $xres width of the smallest bar in user units
		 * @param array $style array of options:<ul><li>string $style['position'] barcode position inside the specified width: L = left (default for LTR); C = center; R = right (default for RTL); S = stretch</li><li>boolean $style['border'] if true prints a border around the barcode</li><li>int $style['padding'] padding to leave around the barcode in user units</li><li>array $style['fgcolor'] color array for bars and text</li><li>mixed $style['bgcolor'] color array for background or false for transparent</li><li>boolean $style["text"] boolean if true prints text below the barcode</li><li>string $style['font'] font name for text</li><li>int $style['fontsize'] font size for text</li><li>int $style['stretchtext']: 0 = disabled; 1 = horizontal scaling only if necessary; 2 = forced horizontal scaling; 3 = character spacing only if necessary; 4 = forced character spacing</li></ul>
		 * @param string $align Indicates the alignment of the pointer next to barcode insertion relative to barcode height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		 * @author Nicola Asuni
		 * @since 3.1.000 (2008-06-09)
		 * @access public
		 */
		public function write1DBarcode($code, $type, $x='', $y='', $w='', $h='', $xres=0.4, $style='', $align='') {
			if ($this->empty_string($code)) {
				return;
			}
			require_once(dirname(__FILE__).'/barcodes.php');
			// save current graphic settings
			$gvars = $this->getGraphicVars();
			// create new barcode object
			$barcodeobj = new TCPDFBarcode($code, $type);
			$arrcode = $barcodeobj->getBarcodeArray();
			if ($arrcode === false) {
				$this->Error('Error in 1D barcode string');
			}
			// set default values
			if (!isset($style['position'])) {
				if ($this->rtl) {
					$style['position'] = 'R';
				} else {
					$style['position'] = 'L';
				}
			}
			if (!isset($style['padding'])) {
				$style['padding'] = 0;
			}
			if (!isset($style['fgcolor'])) {
				$style['fgcolor'] = array(0,0,0); // default black
			}
			if (!isset($style['bgcolor'])) {
				$style['bgcolor'] = false; // default transparent
			}
			if (!isset($style['border'])) {
				$style['border'] = false;
			}
			$fontsize = 0;
			if (!isset($style['text'])) {
				$style['text'] = false;
			}
			if ($style['text'] AND isset($style['font'])) {
				if (isset($style['fontsize'])) {
					$fontsize = $style['fontsize'];
				}
				$this->SetFont($style['font'], '', $fontsize);
			}
			if (!isset($style['stretchtext'])) {
				$style['stretchtext'] = 4;
			}
			// set foreground color
			$this->SetDrawColorArray($style['fgcolor']);
			$this->SetTextColorArray($style['fgcolor']);
			if ($this->empty_string($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $this->x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $this->x;
				}
			}
			if ($this->empty_string($x)) {
				$x = $this->GetX();
			}
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			if ($this->empty_string($y)) {
				$y = $this->GetY();
			}
			if ($this->empty_string($xres)) {
				$xres = 0.4;
			}
			$fbw = ($arrcode['maxw'] * $xres) + (2 * $style['padding']);
			$extraspace = ($this->cell_height_ratio * $fontsize / $this->k) + (2 * $style['padding']);
			if ($this->empty_string($h) OR ($h <= 0)) {
				$h = 10 + $extraspace;
			}
			if ($this->checkPageBreak($h)) {
				$y = $this->y;
			}
			// maximum bar heigth
			$barh = $h - $extraspace;
			switch ($style['position']) {
				case 'L': { // left
					if ($this->rtl) {
						$xpos = $x - $w;
					} else {
						$xpos = $x;
					}
					break;
				}
				case 'C': { // center
					$xdiff = (($w - $fbw) / 2);
					if ($this->rtl) {
						$xpos = $x - $w + $xdiff;
					} else {
						$xpos = $x + $xdiff;
					}
					break;
				}
				case 'R': { // right
					if ($this->rtl) {
						$xpos = $x - $fbw;
					} else {
						$xpos = $x + $w - $fbw;
					}
					break;
				}
				case 'S': { // stretch
					$fbw = $w;
					$xres = ($w - (2 * $style['padding'])) / $arrcode['maxw'];
					if ($this->rtl) {
						$xpos = $x - $w;
					} else {
						$xpos = $x;
					}
					break;
				}
			}
			$xpos_rect = $xpos;
			$xpos = $xpos_rect + $style['padding'];
			$xpos_text = $xpos;
			// barcode is always printed in LTR direction
			$tempRTL = $this->rtl;
			$this->rtl = false;
			// print background color
			if ($style['bgcolor']) {
				$this->Rect($xpos_rect, $y, $fbw, $h, $style['border'] ? 'DF' : 'F', '', $style['bgcolor']);
			} elseif ($style['border']) {
				$this->Rect($xpos_rect, $y, $fbw, $h, 'D');
			}
			// print bars
			if ($arrcode !== false) {
				foreach ($arrcode['bcode'] as $k => $v) {
					$bw = ($v['w'] * $xres);
					if ($v['t']) {
						// draw a vertical bar
						$ypos = $y + $style['padding'] + ($v['p'] * $barh / $arrcode['maxh']);
						$this->Rect($xpos, $ypos, $bw, ($v['h'] * $barh  / $arrcode['maxh']), 'F', array(), $style['fgcolor']);
					}
					$xpos += $bw;
				}
			}
			// print text
			if ($style['text']) {
				// print text
				$this->x = $xpos_text;
				$this->y = $y + $style['padding'] + $barh; 
				$this->Cell(($arrcode['maxw'] * $xres), ($this->cell_height_ratio * $fontsize / $this->k), $code, 0, 0, 'C', 0, '', $style['stretchtext']);
			}
			// restore original direction
			$this->rtl = $tempRTL;
			// restore previous settings
			$this->setGraphicVars($gvars);
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			if ($this->rtl) {
				// set left side coordinate
				$this->img_rb_x = ($this->w - $x - $w);
			} else {
				// set right side coordinate
				$this->img_rb_x = $x + $w;
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T':{
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M':{
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B':{
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N':{
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
		}
		
		/**
	 	 * This function is DEPRECATED, please use the new write1DBarcode() function.
		 * @param int $x x position in user units
		 * @param int $y y position in user units
		 * @param int $w width in user units
		 * @param int $h height position in user units
		 * @param string $type type of barcode (I25, C128A, C128B, C128C, C39)
		 * @param string $style barcode style
		 * @param string $font font for text
		 * @param int $xres x resolution
		 * @param string $code code to print
		 * @deprecated deprecated since version 3.1.000 (2008-06-10)
		 * @access public
		 * @see write1DBarcode()
		 */
		public function writeBarcode($x, $y, $w, $h, $type, $style, $font, $xres, $code) {
			// convert old settings for the new write1DBarcode() function.
			$xres = 1 / $xres;
			$newstyle = array(
				'position' => 'L',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => true,
				'font' => $font,
				'fontsize' => 8,
				'stretchtext' => 4
			);
			if ($style & 1) {
				$newstyle['border'] = true;
			}
			if ($style & 2) {
				$newstyle['bgcolor'] = false;
			}
			if ($style & 4) {
				$newstyle['position'] = 'C';
			} elseif ($style & 8) {
				$newstyle['position'] = 'L';
			} elseif ($style & 16) {
				$newstyle['position'] = 'R';
			}
			if ($style & 128) {
				$newstyle['text'] = true;
			}
			if ($style & 256) {
				$newstyle['stretchtext'] = 4;
			}
			$this->write1DBarcode($code, $type, $x, $y, $w, $h, $xres, $newstyle, '');
		}
		
		/**
	 	 * Print 2D Barcode.
	 	 * @param string $code code to print
	 	 * @param string $type type of barcode.
		 * @param int $x x position in user units
		 * @param int $y y position in user units
		 * @param int $w width in user units
		 * @param int $h height in user units
		 * @param array $style array of options:<ul><li>boolean $style['border'] if true prints a border around the barcode</li><li>int $style['padding'] padding to leave around the barcode in user units</li><li>array $style['fgcolor'] color array for bars and text</li><li>mixed $style['bgcolor'] color array for background or false for transparent</li></ul>
		 * @param string $align Indicates the alignment of the pointer next to barcode insertion relative to barcode height. The value can be:<ul><li>T: top-right for LTR or top-left for RTL</li><li>M: middle-right for LTR or middle-left for RTL</li><li>B: bottom-right for LTR or bottom-left for RTL</li><li>N: next line</li></ul>
		 * @author Nicola Asuni
		 * @since 4.5.037 (2009-04-07)
		 * @access public
		 */
		public function write2DBarcode($code, $type, $x='', $y='', $w='', $h='', $style='', $align='') {
			if ($this->empty_string($code)) {
				return;
			}
			require_once(dirname(__FILE__).'/2dbarcodes.php');
			// save current graphic settings
			$gvars = $this->getGraphicVars();
			// create new barcode object
			$barcodeobj = new TCPDF2DBarcode($code, $type);
			$arrcode = $barcodeobj->getBarcodeArray();
			if ($arrcode === false) {
				$this->Error('Error in 2D barcode string');
			}
			// set default values
			if (!isset($style['padding'])) {
				$style['padding'] = 0;
			}
			if (!isset($style['fgcolor'])) {
				$style['fgcolor'] = array(0,0,0); // default black
			}
			if (!isset($style['bgcolor'])) {
				$style['bgcolor'] = false; // default transparent
			}
			if (!isset($style['border'])) {
				$style['border'] = false;
			}
			// set foreground color
			$this->SetDrawColorArray($style['fgcolor']);
			if ($this->empty_string($x)) {
				$x = $this->GetX();
			}
			if ($this->rtl) {
				$x = $this->w - $x;
			}
			if ($this->empty_string($y)) {
				$y = $this->GetY();
			}
			if ($this->empty_string($w) OR ($w <= 0)) {
				if ($this->rtl) {
					$w = $x - $this->lMargin;
				} else {
					$w = $this->w - $this->rMargin - $x;
				}
			}
			if ($this->empty_string($h) OR ($h <= 0)) {
				// 2d barcodes are square by default
				$h = $w;
			}
			if ($this->checkPageBreak($h)) {
				$y = $this->y;
			}
			// calculate barcode size (excluding padding)
			$bw = $w - (2 * $style['padding']);
			$bh = $h - (2 * $style['padding']);
			// calculate starting coordinates
			if ($this->rtl) {
				$xpos = $x - $w;
			} else {
				$xpos = $x;
			}
			$xpos += $style['padding'];
			$ypos = $y + $style['padding'];
			// barcode is always printed in LTR direction
			$tempRTL = $this->rtl;
			$this->rtl = false;
			// print background color
			if ($style['bgcolor']) {
				$this->Rect($x, $y, $w, $h, $style['border'] ? 'DF' : 'F', '', $style['bgcolor']);
			} elseif ($style['border']) {
				$this->Rect($x, $y, $w, $h, 'D');
			}
			// print barcode cells
			if ($arrcode !== false) {
				$rows = $arrcode['num_rows'];
				$cols = $arrcode['num_cols'];
				// calculate dimension of single barcode cell
				$cw = $bw / $cols;
				$ch = $bh / $rows;
				// for each row
				for ($r = 0; $r < $rows; ++$r) {
					$xr = $xpos;
					// for each column
					for ($c = 0; $c < $cols; ++$c) {
						if ($arrcode['bcode'][$r][$c] == 1) {
							// draw a single barcode cell
							$this->Rect($xr, $ypos, $cw, $ch, 'F', array(), $style['fgcolor']);
						}
						$xr += $cw;
					}
					$ypos += $ch;
				}
			}
			// restore original direction
			$this->rtl = $tempRTL;
			// restore previous settings
			$this->setGraphicVars($gvars);
			// set bottomcoordinates
			$this->img_rb_y = $y + $h;
			if ($this->rtl) {
				// set left side coordinate
				$this->img_rb_x = ($this->w - $x - $w);
			} else {
				// set right side coordinate
				$this->img_rb_x = $x + $w;
			}
			// set pointer to align the successive text/objects
			switch($align) {
				case 'T':{
					$this->y = $y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'M':{
					$this->y = $y + round($h/2);
					$this->x = $this->img_rb_x;
					break;
				}
				case 'B':{
					$this->y = $this->img_rb_y;
					$this->x = $this->img_rb_x;
					break;
				}
				case 'N':{
					$this->SetY($this->img_rb_y);
					break;
				}
				default:{
					break;
				}
			}
		}
		
		/**
		 * Returns an array containing current margins:
		 * <ul>
				<li>$ret['left'] = left  margin</li>
				<li>$ret['right'] = right margin</li>
				<li>$ret['top'] = top margin</li>
				<li>$ret['bottom'] = bottom margin</li>
				<li>$ret['header'] = header margin</li>
				<li>$ret['footer'] = footer margin</li>
				<li>$ret['cell'] = cell margin</li>
		 * </ul>
		 * @return array containing all margins measures 
		 * @access public
		 * @since 3.2.000 (2008-06-23)
		 */
		public function getMargins() {
			$ret = array(
				'left' => $this->lMargin,
				'right' => $this->rMargin,
				'top' => $this->tMargin,
				'bottom' => $this->bMargin,
				'header' => $this->header_margin,
				'footer' => $this->footer_margin,
				'cell' => $this->cMargin,
			);
			return $ret;
		}
		
		/**
		 * Returns an array containing original margins:
		 * <ul>
				<li>$ret['left'] = left  margin</li>
				<li>$ret['right'] = right margin</li>
		 * </ul>
		 * @return array containing all margins measures 
		 * @access public
		 * @since 4.0.012 (2008-07-24)
		 */
		public function getOriginalMargins() {
			$ret = array(
				'left' => $this->original_lMargin,
				'right' => $this->original_rMargin
			);
			return $ret;
		}
		
		/**
		 * Returns the current font size.
		 * @return current font size
		 * @access public
		 * @since 3.2.000 (2008-06-23)
		 */
		public function getFontSize() {
			return $this->FontSize;
		}
		
		/**
		 * Returns the current font size in points unit.
		 * @return current font size in points unit
		 * @access public
		 * @since 3.2.000 (2008-06-23)
		 */
		public function getFontSizePt() {
			return $this->FontSizePt;
		}

		/**
		 * Returns the current font family name.
		 * @return string current font family name
		 * @access public
		 * @since 4.3.008 (2008-12-05)
		 */
		public function getFontFamily() {
			return $this->FontFamily;
		}

		/**
		 * Returns the current font style.
		 * @return string current font style
		 * @access public
		 * @since 4.3.008 (2008-12-05)
		 */
		public function getFontStyle() {
			return $this->FontStyle;
		}
		
		/**
		 * Prints a cell (rectangular area) with optional borders, background color and html text string. 
		 * The upper-left corner of the cell corresponds to the current position. After the call, the current position moves to the right or to the next line.<br />
		 * If automatic page breaking is enabled and the cell goes beyond the limit, a page break is done before outputting.
		 * @param float $w Cell width. If 0, the cell extends up to the right margin.
		 * @param float $h Cell minimum height. The cell extends automatically if needed.
		 * @param float $x upper-left corner X coordinate
		 * @param float $y upper-left corner Y coordinate
		 * @param string $html html text to print. Default value: empty string.
		 * @param mixed $border Indicates if borders must be drawn around the cell. The value can be either a number:<ul><li>0: no border (default)</li><li>1: frame</li></ul>or a string containing some or all of the following characters (in any order):<ul><li>L: left</li><li>T: top</li><li>R: right</li><li>B: bottom</li></ul>
		 * @param int $ln Indicates where the current position should go after the call. Possible values are:<ul><li>0: to the right (or left for RTL language)</li><li>1: to the beginning of the next line</li><li>2: below</li></ul>
	Putting 1 is equivalent to putting 0 and calling Ln() just after. Default value: 0.
		 * @param int $fill Indicates if the cell background must be painted (1) or transparent (0). Default value: 0.
		 * @param boolean $reseth if true reset the last cell height (default true).
		 * @param string $align Allows to center or align the text. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		 * @param boolean $autopadding if true, uses internal padding and automatically adjust it to account for line width.
		 * @access public
		 * @uses MultiCell()
		 * @see Multicell(), writeHTML()
		 */
		public function writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true) {
			return $this->MultiCell($w, $h, $html, $border, $align, $fill, $ln, $x, $y, $reseth, 0, true, $autopadding, 0);
		}
		
		/**
	 	 * Returns the HTML DOM array.
	 	 * <ul><li>$dom[$key]['tag'] = true if tag, false otherwise;</li><li>$dom[$key]['value'] = tag name or text;</li><li>$dom[$key]['opening'] = true if opening tag, false otherwise;</li><li>$dom[$key]['attribute'] = array of attributes (attribute name is the key);</li><li>$dom[$key]['style'] = array of style attributes (attribute name is the key);</li><li>$dom[$key]['parent'] = id of parent element;</li><li>$dom[$key]['fontname'] = font family name;</li><li>$dom[$key]['fontstyle'] = font style;</li><li>$dom[$key]['fontsize'] = font size in points;</li><li>$dom[$key]['bgcolor'] = RGB array of background color;</li><li>$dom[$key]['fgcolor'] = RGB array of foreground color;</li><li>$dom[$key]['width'] = width in pixels;</li><li>$dom[$key]['height'] = height in pixels;</li><li>$dom[$key]['align'] = text alignment;</li><li>$dom[$key]['cols'] = number of colums in table;</li><li>$dom[$key]['rows'] = number of rows in table;</li></ul>
		 * @param string $html html code
		 * @return array
		 * @access protected
		 * @since 3.2.000 (2008-06-20)
		 */
		protected function getHtmlDomArray($html) {
			// remove all unsupported tags (the line below lists all supported tags)
			$html = strip_tags($html, '<marker/><a><b><blockquote><br><br/><dd><del><div><dl><dt><em><font><form><h1><h2><h3><h4><h5><h6><hr><i><img><input><label><li><ol><option><p><pre><select><small><span><strong><sub><sup><table><tablehead><tcpdf><td><textarea><th><thead><tr><tt><u><ul>');
			//replace some blank characters
			$html = preg_replace('/<pre/', '<xre', $html); // preserve pre tag
			$html = preg_replace('/<(table|tr|td|th|tcpdf|blockquote|dd|div|dt|form|h1|h2|h3|h4|h5|h6|br|hr|li|ol|ul|p)([^\>]*)>[\n\r\t]+/', '<\\1\\2>', $html);
			$html = preg_replace('@(\r\n|\r)@', "\n", $html);
			$repTable = array("\t" => ' ', "\0" => ' ', "\x0B" => ' ', "\\" => "\\\\");
			$html = strtr($html, $repTable);
			$offset = 0;
			while (($offset < strlen($html)) AND ($pos = strpos($html, '</pre>', $offset)) !== false) {
				$html_a = substr($html, 0, $offset);
				$html_b = substr($html, $offset, ($pos - $offset + 6));
				while (preg_match("'<xre([^\>]*)>(.*?)\n(.*?)</pre>'si", $html_b)) {
					// preserve newlines on <pre> tag
					$html_b = preg_replace("'<xre([^\>]*)>(.*?)\n(.*?)</pre>'si", "<xre\\1>\\2<br />\\3</pre>", $html_b);
				}
				$html = $html_a.$html_b.substr($html, $pos + 6);
				$offset = strlen($html_a.$html_b);
			}
			$offset = 0;
			while (($offset < strlen($html)) AND ($pos = strpos($html, '</textarea>', $offset)) !== false) {
				$html_a = substr($html, 0, $offset);
				$html_b = substr($html, $offset, ($pos - $offset + 11));
				while (preg_match("'<textarea([^\>]*)>(.*?)\n(.*?)</textarea>'si", $html_b)) {
					// preserve newlines on <textarea> tag
					$html_b = preg_replace("'<textarea([^\>]*)>(.*?)\n(.*?)</textarea>'si", "<textarea\\1>\\2<TBR>\\3</textarea>", $html_b);
					$html_b = preg_replace("'<textarea([^\>]*)>(.*?)[\"](.*?)</textarea>'si", "<textarea\\1>\\2''\\3</textarea>", $html_b);
				}
				$html = $html_a.$html_b.substr($html, $pos + 11);
				$offset = strlen($html_a.$html_b);
			}
			$html = preg_replace("'([\s]*)<option'si", "<option", $html);
			$html = preg_replace("'</option>([\s]*)'si", "</option>", $html);
			$offset = 0;
			while (($offset < strlen($html)) AND ($pos = strpos($html, '</option>', $offset)) !== false) {
				$html_a = substr($html, 0, $offset);
				$html_b = substr($html, $offset, ($pos - $offset + 9));
				while (preg_match("'<option([^\>]*)>(.*?)</option>'si", $html_b)) {
					$html_b = preg_replace("'<option([\s]+)value=\"([^\"]*)\"([^\>]*)>(.*?)</option>'si", "\\2\t\\4\r", $html_b);
					$html_b = preg_replace("'<option([^\>]*)>(.*?)</option>'si", "\\2\r", $html_b);
				}
				$html = $html_a.$html_b.substr($html, $pos + 9);
				$offset = strlen($html_a.$html_b);
			}
			$html = preg_replace("'<select([^\>]*)>'si", "<select\\1 opt=\"", $html);
			$html = preg_replace("'([\s]+)</select>'si", "\" />", $html);
			$html = str_replace("\n", ' ', $html);
			// restore textarea newlines
			$html = str_replace('<TBR>', "\n", $html);
			// remove extra spaces from code
			$html = preg_replace('/[\s]+<\/(table|tr|td|th|ul|ol|li)>/', '</\\1>', $html);
			$html = preg_replace('/[\s]+<(tr|td|th|ul|ol|li|br)/', '<\\1', $html);
			$html = preg_replace('/<\/(table|tr|td|th|blockquote|dd|div|dt|h1|h2|h3|h4|h5|h6|hr|li|ol|ul|p)>[\s]+</', '</\\1><', $html);
			$html = preg_replace('/<\/(td|th)>/', '<marker style="font-size:0"/></\\1>', $html);
			$html = preg_replace('/<\/table>([\s]*)<marker style="font-size:0"\/>/', '</table>', $html);
			$html = preg_replace('/<img/', ' <img', $html);
			$html = preg_replace('/<img([^\>]*)>/xi', '<img\\1><span></span>', $html);
			$html = preg_replace('/<xre/', '<pre', $html); // restore pre tag
			$html = preg_replace('/<textarea([^\>]*)>/xi', '<textarea\\1 value="', $html);
			$html = preg_replace('/<\/textarea>/', '" />', $html);
			// trim string
			$html = preg_replace('/^[\s]+/', '', $html);
			$html = preg_replace('/[\s]+$/', '', $html);
			// pattern for generic tag
			$tagpattern = '/(<[^>]+>)/';
			// explodes the string
			$a = preg_split($tagpattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			// count elements
			$maxel = count($a);
			$elkey = 0;
			$key = 0;
			// create an array of elements
			$dom = array();
			$dom[$key] = array();
			// set first void element
			$dom[$key]['tag'] = false;
			$dom[$key]['value'] = '';
			$dom[$key]['parent'] = 0;
			$dom[$key]['fontname'] = $this->FontFamily;
			$dom[$key]['fontstyle'] = $this->FontStyle;
			$dom[$key]['fontsize'] = $this->FontSizePt;
			$dom[$key]['bgcolor'] = false;
			$dom[$key]['fgcolor'] = $this->fgcolor;
			$dom[$key]['align'] = '';
			$dom[$key]['listtype'] = '';
			$dom[$key]['text-indent'] = 0;
			$thead = false; // true when we are inside the THEAD tag
			++$key;
			$level = array();
			array_push($level, 0); // root
			while ($elkey < $maxel) {
				$dom[$key] = array();
				$element = $a[$elkey];
				$dom[$key]['elkey'] = $elkey;
				if (preg_match($tagpattern, $element)) {
					// html tag
					$element = substr($element, 1, -1);
					// get tag name
					preg_match('/[\/]?([a-zA-Z0-9]*)/', $element, $tag);
					$tagname = strtolower($tag[1]);
					// check if we are inside a table header
					if ($tagname == 'thead') {
						if ($element{0} == '/') {
							$thead = false;
						} else {
							$thead = true;
						}
						++$elkey;
						continue;
					}
					$dom[$key]['tag'] = true;
					$dom[$key]['value'] = $tagname;
					if ($element{0} == '/') {
						// closing html tag
						$dom[$key]['opening'] = false;
						$dom[$key]['parent'] = end($level);
						array_pop($level);
						$dom[$key]['fontname'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontname'];
						$dom[$key]['fontstyle'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontstyle'];
						$dom[$key]['fontsize'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fontsize'];
						$dom[$key]['bgcolor'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['bgcolor'];
						$dom[$key]['fgcolor'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['fgcolor'];
						$dom[$key]['align'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['align'];
						if (isset($dom[($dom[($dom[$key]['parent'])]['parent'])]['listtype'])) {
							$dom[$key]['listtype'] = $dom[($dom[($dom[$key]['parent'])]['parent'])]['listtype'];
						}
						// set the number of columns in table tag
						if (($dom[$key]['value'] == 'tr') AND (!isset($dom[($dom[($dom[$key]['parent'])]['parent'])]['cols']))) {
							$dom[($dom[($dom[$key]['parent'])]['parent'])]['cols'] = $dom[($dom[$key]['parent'])]['cols'];
						}
						if (($dom[$key]['value'] == 'td') OR ($dom[$key]['value'] == 'th')) {
							$dom[($dom[$key]['parent'])]['content'] = '';
							for ($i = ($dom[$key]['parent'] + 1); $i < $key; ++$i) {
								$dom[($dom[$key]['parent'])]['content'] .= $a[$dom[$i]['elkey']];
							}
							$key = $i;
						}
						// store header rows on a new table
						if (($dom[$key]['value'] == 'tr') AND ($dom[($dom[$key]['parent'])]['thead'] === true)) {
							if ($this->empty_string($dom[($dom[($dom[$key]['parent'])]['parent'])]['thead'])) {
								$dom[($dom[($dom[$key]['parent'])]['parent'])]['thead'] = $a[$dom[($dom[($dom[$key]['parent'])]['parent'])]['elkey']];
							}
							for ($i = $dom[$key]['parent']; $i <= $key; ++$i) {
								$dom[($dom[($dom[$key]['parent'])]['parent'])]['thead'] .= $a[$dom[$i]['elkey']];
							}
						}
						if (($dom[$key]['value'] == 'table') AND (!$this->empty_string($dom[($dom[$key]['parent'])]['thead']))) {
							$dom[($dom[$key]['parent'])]['thead'] .= '</tablehead>';
						}
					} else {
						// opening html tag
						$dom[$key]['opening'] = true;
						$dom[$key]['parent'] = end($level);
						if (substr($element, -1, 1) != '/') {
							// not self-closing tag
							array_push($level, $key);
							$dom[$key]['self'] = false;
						} else {
							$dom[$key]['self'] = true;
						}
						// copy some values from parent
						$parentkey = 0;
						if ($key > 0) {
							$parentkey = $dom[$key]['parent'];
							$dom[$key]['fontname'] = $dom[$parentkey]['fontname'];
							$dom[$key]['fontstyle'] = $dom[$parentkey]['fontstyle'];
							$dom[$key]['fontsize'] = $dom[$parentkey]['fontsize'];
							$dom[$key]['bgcolor'] = $dom[$parentkey]['bgcolor'];
							$dom[$key]['fgcolor'] = $dom[$parentkey]['fgcolor'];
							$dom[$key]['align'] = $dom[$parentkey]['align'];
							$dom[$key]['listtype'] = $dom[$parentkey]['listtype'];
							$dom[$key]['text-indent'] = $dom[$parentkey]['text-indent'];
						}
						// get attributes
						preg_match_all('/([^=\s]*)=["]?([^"]*)["]?/', $element, $attr_array, PREG_PATTERN_ORDER);
						$dom[$key]['attribute'] = array(); // reset attribute array
						while (list($id, $name) = each($attr_array[1])) {
							$dom[$key]['attribute'][strtolower($name)] = $attr_array[2][$id];
						}
						// split style attributes
						if (isset($dom[$key]['attribute']['style'])) {
							// get style attributes
							preg_match_all('/([^;:\s]*):([^;]*)/', $dom[$key]['attribute']['style'], $style_array, PREG_PATTERN_ORDER);
							$dom[$key]['style'] = array(); // reset style attribute array
							while (list($id, $name) = each($style_array[1])) {
								$dom[$key]['style'][strtolower($name)] = trim($style_array[2][$id]);
							}
							// --- get some style attributes ---
							if (isset($dom[$key]['style']['font-family'])) {
								// font family
								if (isset($dom[$key]['style']['font-family'])) {
									$fontslist = preg_split('/[,]/', strtolower($dom[$key]['style']['font-family']));
									foreach ($fontslist as $font) {
										$font = trim(strtolower($font));
										if (in_array($font, $this->fontlist) OR in_array($font, $this->fontkeys)) {
											$dom[$key]['fontname'] = $font;
											break;
										}
									}
								}
							}
							// list-style-type
							if (isset($dom[$key]['style']['list-style-type'])) {
								$dom[$key]['listtype'] = trim(strtolower($dom[$key]['style']['list-style-type']));
								if ($dom[$key]['listtype'] == 'inherit') {
									$dom[$key]['listtype'] = $dom[$parentkey]['listtype'];
								}
							}
							// text-indent
							if (isset($dom[$key]['style']['text-indent'])) {
								$dom[$key]['text-indent'] = $this->getHTMLUnitToUnits($dom[$key]['style']['text-indent']);
								if ($dom[$key]['text-indent'] == 'inherit') {
									$dom[$key]['text-indent'] = $dom[$parentkey]['text-indent'];
								}
							}
							// font size
							if (isset($dom[$key]['style']['font-size'])) {
								$fsize = trim($dom[$key]['style']['font-size']);
								switch ($fsize) {
									// absolute-size
									case 'xx-small': {
										$dom[$key]['fontsize'] = $dom[0]['fontsize'] - 4;
										break;
									}
									case 'x-small': {
										$dom[$key]['fontsize'] = $dom[0]['fontsize'] - 3;
										break;
									}
									case 'small': {
										$dom[$key]['fontsize'] = $dom[0]['fontsize'] - 2;
										break;
									}
									case 'medium': {
										$dom[$key]['fontsize'] = $dom[0]['fontsize'];
										break;
									}
									case 'large': {
										$dom[$key]['fontsize'] = $dom[0]['fontsize'] + 2;
										break;
									}
									case 'x-large': {
										$dom[$key]['fontsize'] = $dom[0]['fontsize'] + 4;
										break;
									}
									case 'xx-large': {
										$dom[$key]['fontsize'] = $dom[0]['fontsize'] + 6;
										break;
									}
									// relative-size
									case 'smaller': {
										$dom[$key]['fontsize'] = $dom[$parentkey]['fontsize'] - 3;
										break;
									}
									case 'larger': {
										$dom[$key]['fontsize'] = $dom[$parentkey]['fontsize'] + 3;
										break;
									}
									default: {
										$dom[$key]['fontsize'] = $this->getHTMLUnitToUnits($fsize, $dom[$parentkey]['fontsize'], 'pt', true);
									}
								}
							}
							// font style
							if (isset($dom[$key]['style']['font-weight']) AND (strtolower($dom[$key]['style']['font-weight']{0}) == 'b')) {
								$dom[$key]['fontstyle'] .= 'B';
							}
							if (isset($dom[$key]['style']['font-style']) AND (strtolower($dom[$key]['style']['font-style']{0}) == 'i')) {
								$dom[$key]['fontstyle'] .= '"I';
							}
							// font color
							if (isset($dom[$key]['style']['color']) AND (!$this->empty_string($dom[$key]['style']['color']))) {
								$dom[$key]['fgcolor'] = $this->convertHTMLColorToDec($dom[$key]['style']['color']);
							}
							// background color
							if (isset($dom[$key]['style']['background-color']) AND (!$this->empty_string($dom[$key]['style']['background-color']))) {
								$dom[$key]['bgcolor'] = $this->convertHTMLColorToDec($dom[$key]['style']['background-color']);
							}
							// text-decoration
							if (isset($dom[$key]['style']['text-decoration'])) {
								$decors = explode(' ', strtolower($dom[$key]['style']['text-decoration']));
								foreach ($decors as $dec) {
									$dec = trim($dec);
									if (!$this->empty_string($dec)) {
										if ($dec{0} == 'u') {
											$dom[$key]['fontstyle'] .= 'U';
										} elseif ($dec{0} == 'l') {
											$dom[$key]['fontstyle'] .= 'D';
										}
									}
								}
							}
							// check for width attribute
							if (isset($dom[$key]['style']['width'])) {
								$dom[$key]['width'] = $dom[$key]['style']['width'];
							}
							// check for height attribute
							if (isset($dom[$key]['style']['height'])) {
								$dom[$key]['height'] = $dom[$key]['style']['height'];
							}
							// check for text alignment
							if (isset($dom[$key]['style']['text-align'])) {
								$dom[$key]['align'] = strtoupper($dom[$key]['style']['text-align']{0});
							}
							// check for border attribute
							if (isset($dom[$key]['style']['border'])) {
								$dom[$key]['attribute']['border'] = $dom[$key]['style']['border'];
							}
						}
						// check for font tag
						if ($dom[$key]['value'] == 'font') {
							// font family
							if (isset($dom[$key]['attribute']['face'])) {
								$fontslist = preg_split('/[,]/', strtolower($dom[$key]['attribute']['face']));
								foreach ($fontslist as $font) {
									$font = trim(strtolower($font));
									if (in_array($font, $this->fontlist) OR in_array($font, $this->fontkeys)) {
										$dom[$key]['fontname'] = $font;
										break;
									}
								}
							}
							// font size
							if (isset($dom[$key]['attribute']['size'])) {
								if ($key > 0) {
									if ($dom[$key]['attribute']['size']{0} == '+') {
										$dom[$key]['fontsize'] = $dom[($dom[$key]['parent'])]['fontsize'] + intval(substr($dom[$key]['attribute']['size'], 1));
									} elseif ($dom[$key]['attribute']['size']{0} == '-') {
										$dom[$key]['fontsize'] = $dom[($dom[$key]['parent'])]['fontsize'] - intval(substr($dom[$key]['attribute']['size'], 1));
									} else {
										$dom[$key]['fontsize'] = intval($dom[$key]['attribute']['size']);
									}
								} else {
									$dom[$key]['fontsize'] = intval($dom[$key]['attribute']['size']);
								}
							}
						}
						// force natural alignment for lists
						if ((($dom[$key]['value'] == 'ul') OR ($dom[$key]['value'] == 'ol') OR ($dom[$key]['value'] == 'dl'))
							AND (!isset($dom[$key]['align']) OR $this->empty_string($dom[$key]['align']) OR ($dom[$key]['align'] != 'J'))) {
							if ($this->rtl) {
								$dom[$key]['align'] = 'R';
							} else {
								$dom[$key]['align'] = 'L';
							}
						}
						if (($dom[$key]['value'] == 'small') OR ($dom[$key]['value'] == 'sup') OR ($dom[$key]['value'] == 'sub')) {
							$dom[$key]['fontsize'] = $dom[$key]['fontsize'] * K_SMALL_RATIO;
						}
						if (($dom[$key]['value'] == 'strong') OR ($dom[$key]['value'] == 'b')) {
							$dom[$key]['fontstyle'] .= 'B';
						}
						if (($dom[$key]['value'] == 'em') OR ($dom[$key]['value'] == 'i')) {
							$dom[$key]['fontstyle'] .= 'I';
						}
						if ($dom[$key]['value'] == 'u') {
							$dom[$key]['fontstyle'] .= 'U';
						}
						if ($dom[$key]['value'] == 'del') {
							$dom[$key]['fontstyle'] .= 'D';
						}
						if (($dom[$key]['value'] == 'pre') OR ($dom[$key]['value'] == 'tt')) {
							$dom[$key]['fontname'] = $this->default_monospaced_font;
						}
						if (($dom[$key]['value']{0} == 'h') AND (intval($dom[$key]['value']{1}) > 0) AND (intval($dom[$key]['value']{1}) < 7)) {
							$headsize = (4 - intval($dom[$key]['value']{1})) * 2;
							$dom[$key]['fontsize'] = $dom[0]['fontsize'] + $headsize;
							$dom[$key]['fontstyle'] .= 'B';
						}
						if (($dom[$key]['value'] == 'table')) {
							$dom[$key]['rows'] = 0; // number of rows
							$dom[$key]['trids'] = array(); // IDs of TR elements
							$dom[$key]['thead'] = ''; // table header rows
						}
						if (($dom[$key]['value'] == 'tr')) {
							$dom[$key]['cols'] = 0;
							// store the number of rows on table element
							++$dom[($dom[$key]['parent'])]['rows'];
							// store the TR elements IDs on table element
							array_push($dom[($dom[$key]['parent'])]['trids'], $key);
							if ($thead) {
								$dom[$key]['thead'] = true;
							} else {
								$dom[$key]['thead'] = false;
							}
						}
						if (($dom[$key]['value'] == 'th') OR ($dom[$key]['value'] == 'td')) {
							if (isset($dom[$key]['attribute']['colspan'])) {
								$colspan = intval($dom[$key]['attribute']['colspan']);
							} else {
								$colspan = 1;
							}
							$dom[$key]['attribute']['colspan'] = $colspan;
							$dom[($dom[$key]['parent'])]['cols'] += $colspan;
						}
						// set foreground color attribute
						if (isset($dom[$key]['attribute']['color']) AND (!$this->empty_string($dom[$key]['attribute']['color']))) {
							$dom[$key]['fgcolor'] = $this->convertHTMLColorToDec($dom[$key]['attribute']['color']);
						}
						// set background color attribute
						if (isset($dom[$key]['attribute']['bgcolor']) AND (!$this->empty_string($dom[$key]['attribute']['bgcolor']))) {
							$dom[$key]['bgcolor'] = $this->convertHTMLColorToDec($dom[$key]['attribute']['bgcolor']);
						}
						// check for width attribute
						if (isset($dom[$key]['attribute']['width'])) {
							$dom[$key]['width'] = $dom[$key]['attribute']['width'];
						}
						// check for height attribute
						if (isset($dom[$key]['attribute']['height'])) {
							$dom[$key]['height'] = $dom[$key]['attribute']['height'];
						}
						// check for text alignment
						if (isset($dom[$key]['attribute']['align']) AND (!$this->empty_string($dom[$key]['attribute']['align'])) AND ($dom[$key]['value'] !== 'img')) {
							$dom[$key]['align'] = strtoupper($dom[$key]['attribute']['align']{0});
						}
					} // end opening tag
				} else {
					// text
					$dom[$key]['tag'] = false;
					$dom[$key]['value'] = stripslashes($this->unhtmlentities($element));
					$dom[$key]['parent'] = end($level);
				}
				++$elkey;
				++$key;
			}
			return $dom;
		}
		
		/**
		 * Allows to preserve some HTML formatting (limited support).<br />
		 * IMPORTANT: The HTML must be well formatted - try to clean-up it using an application like HTML-Tidy before submitting.
		 * Supported tags are: a, b, blockquote, br, dd, del, div, dl, dt, em, font, h1, h2, h3, h4, h5, h6, hr, i, img, li, ol, p, pre, small, span, strong, sub, sup, table, tcpdf, td, th, thead, tr, tt, u, ul
		 * @param string $html text to display
		 * @param boolean $ln if true add a new line after text (default = true)
		 * @param int $fill Indicates if the background must be painted (true) or transparent (false).
		 * @param boolean $reseth if true reset the last cell height (default false).
		 * @param boolean $cell if true add the default cMargin space to each Write (default false).
		 * @param string $align Allows to center or align the text. Possible values are:<ul><li>L : left align</li><li>C : center</li><li>R : right align</li><li>'' : empty string : left for LTR or right for RTL</li></ul>
		 * @access public
		 */
		public function writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='') {
			$gvars = $this->getGraphicVars();
			// store current values
			$prevPage = $this->page;
			$prevlMargin = $this->lMargin;
			$prevrMargin = $this->rMargin;
			$curfontname = $this->FontFamily;
			$curfontstyle = $this->FontStyle;
			$curfontsize = $this->FontSizePt;	
			$this->newline = true;
			$startlinepage = $this->page;
			$minstartliney = $this->y;
			$startlinex = $this->x;
			$startliney = $this->y;
			$yshift = 0;
			$newline = true;
			$loop = 0;
			$curpos = 0;
			$this_method_vars = array();
			$undo = false;
			$blocktags = array('blockquote','br','dd','div','dt','h1','h2','h3','h4','h5','h6','hr','li','ol','p','ul','tcpdf');
			$this->premode = false;
			if (isset($this->PageAnnots[$this->page])) {
				$pask = count($this->PageAnnots[$this->page]);
			} else {
				$pask = 0;
			}
			if (isset($this->footerlen[$this->page])) {
				$this->footerpos[$this->page] = $this->pagelen[$this->page] - $this->footerlen[$this->page];
			} else {
				$this->footerpos[$this->page] = $this->pagelen[$this->page];
			}
			$startlinepos = $this->footerpos[$this->page];
			$lalign = $align;
			$plalign = $align;
			if ($this->rtl) {
				$w = $this->x - $this->lMargin;
			} else {
				$w = $this->w - $this->rMargin - $this->x;
			}
			$w -= (2 * $this->cMargin);
			if ($cell) {
				if ($this->rtl) {
					$this->x -= $this->cMargin;
				} else {
					$this->x += $this->cMargin;
				}
			}
			if ($this->customlistindent >= 0) {
				$this->listindent = $this->customlistindent;
			} else {
				$this->listindent = $this->GetStringWidth('0000');
			}
			// save previous states
			$prev_listnum = $this->listnum;
			$prev_listordered = $this->listordered;
			$prev_listcount = $this->listcount;
			$prev_lispacer = $this->lispacer;
			$this->listnum = 0;
			$this->listordered = array();
			$this->listcount = array();
			$this->lispacer = '';
			if (($this->empty_string($this->lasth)) OR ($reseth)) {
				//set row height
				$this->lasth = $this->FontSize * $this->cell_height_ratio; 
			}
			$dom = $this->getHtmlDomArray($html);
			$maxel = count($dom);
			$key = 0;
			while ($key < $maxel) {
				if ($dom[$key]['tag'] AND $dom[$key]['opening'] AND isset($dom[$key]['attribute']['nobr']) AND ($dom[$key]['attribute']['nobr'] == 'true')) {
					if (isset($dom[($dom[$key]['parent'])]['attribute']['nobr']) AND ($dom[($dom[$key]['parent'])]['attribute']['nobr'] == 'true')) {
						$dom[$key]['attribute']['nobr'] = false;
					} else {
						// store current object
						$this->startTransaction();
						// save this method vars
						$this_method_vars['html'] = $html;
						$this_method_vars['ln'] = $ln;
						$this_method_vars['fill'] = $fill;
						$this_method_vars['reseth'] = $reseth;
						$this_method_vars['cell'] = $cell;
						$this_method_vars['align'] = $align;
						$this_method_vars['gvars'] = $gvars;
						$this_method_vars['prevPage'] = $prevPage;
						$this_method_vars['prevlMargin'] = $prevlMargin;
						$this_method_vars['prevrMargin'] = $prevrMargin;
						$this_method_vars['curfontname'] = $curfontname;
						$this_method_vars['curfontstyle'] = $curfontstyle;
						$this_method_vars['curfontsize'] = $curfontsize;
						$this_method_vars['minstartliney'] = $minstartliney;
						$this_method_vars['yshift'] = $yshift;
						$this_method_vars['startlinepage'] = $startlinepage;
						$this_method_vars['startlinepos'] = $startlinepos;
						$this_method_vars['startlinex'] = $startlinex;
						$this_method_vars['startliney'] = $startliney;
						$this_method_vars['newline'] = $newline;
						$this_method_vars['loop'] = $loop;
						$this_method_vars['curpos'] = $curpos;
						$this_method_vars['pask'] = $pask;
						$this_method_vars['lalign'] = $lalign;
						$this_method_vars['plalign'] = $plalign;
						$this_method_vars['w'] = $w;
						$this_method_vars['prev_listnum'] = $prev_listnum;
						$this_method_vars['prev_listordered'] = $prev_listordered;
						$this_method_vars['prev_listcount'] = $prev_listcount;
						$this_method_vars['prev_lispacer'] = $prev_lispacer;
						$this_method_vars['key'] = $key;
						$this_method_vars['dom'] = $dom;
					}
				}
				if ($dom[$key]['tag'] OR ($key == 0)) {
					if ((($dom[$key]['value'] == 'table') OR ($dom[$key]['value'] == 'tr')) AND (isset($dom[$key]['align']))) {
						$dom[$key]['align'] = ($this->rtl) ? 'R' : 'L';
					}
					// vertically align image in line
					if ((!$this->newline)
						AND ($dom[$key]['value'] == 'img')
						AND (isset($dom[$key]['attribute']['height']))
						AND ($dom[$key]['attribute']['height'] > 0)) {
						
						// get image height
						$imgh = $this->getHTMLUnitToUnits($dom[$key]['attribute']['height'], $this->lasth, 'px');
						if (!$this->InFooter) {
							// check for page break
							$this->checkPageBreak($imgh);
						}
						if ($this->page > $startlinepage) {
							// fix line splitted over two pages
							if (isset($this->footerlen[$startlinepage])) {
								$curpos = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
							}
							// line to be moved one page forward
							$pagebuff = $this->getPageBuffer($startlinepage);
							$linebeg = substr($pagebuff, $startlinepos, ($curpos - $startlinepos));
							$tstart = substr($pagebuff, 0, $startlinepos);
							$tend = substr($this->getPageBuffer($startlinepage), $curpos);
							// remove line from previous page
							$this->setPageBuffer($startlinepage, $tstart.''.$tend);
							$pagebuff = $this->getPageBuffer($this->page);
							$tstart = substr($pagebuff, 0, $this->cntmrk[$this->page]);
							$tend = substr($pagebuff, $this->cntmrk[$this->page]);
							// add line start to current page
							$yshift = $minstartliney - $this->y;
							$try = sprintf('1 0 0 1 0 %.3F cm', ($yshift * $this->k));
							$this->setPageBuffer($this->page, $tstart."\nq\n".$try."\n".$linebeg."\nQ\n".$tend);
							// shift the annotations and links
							if (isset($this->PageAnnots[$this->page])) {
								$next_pask = count($this->PageAnnots[$this->page]);
							} else {
								$next_pask = 0;
							}
							if (isset($this->PageAnnots[$startlinepage])) {
								foreach ($this->PageAnnots[$startlinepage] as $pak => $pac) {
									if ($pak >= $pask) {
										$this->PageAnnots[$this->page][] = $pac;
										unset($this->PageAnnots[$startlinepage][$pak]);
										$npak = count($this->PageAnnots[$this->page]) - 1;
										$this->PageAnnots[$this->page][$npak]['y'] -= $yshift;										
									}
								}
								
							}
							$pask = $next_pask;
							$startlinepos = $this->cntmrk[$this->page];
							$startlinepage = $this->page;
							$startliney = $this->y;
						}
						$this->y += (($curfontsize / $this->k) - $imgh);
						$minstartliney = min($this->y, $minstartliney);	
					} elseif (isset($dom[$key]['fontname']) OR isset($dom[$key]['fontstyle']) OR isset($dom[$key]['fontsize'])) {
						// account for different font size
						$pfontname = $curfontname;
						$pfontstyle = $curfontstyle;
						$pfontsize = $curfontsize;
						$fontname = isset($dom[$key]['fontname']) ? $dom[$key]['fontname'] : $curfontname;
						$fontstyle = isset($dom[$key]['fontstyle']) ? $dom[$key]['fontstyle'] : $curfontstyle;
						$fontsize = isset($dom[$key]['fontsize']) ? $dom[$key]['fontsize'] : $curfontsize;
						if (($fontname != $curfontname) OR ($fontstyle != $curfontstyle) OR ($fontsize != $curfontsize)) {
							$this->SetFont($fontname, $fontstyle, $fontsize);
							$this->lasth = $this->FontSize * $this->cell_height_ratio;
							if (is_numeric($fontsize) AND ($fontsize > 0)
								AND is_numeric($curfontsize) AND ($curfontsize > 0)
								AND ($fontsize != $curfontsize) AND (!$this->newline)
								AND ($key < ($maxel - 1))
								) {
								if ((!$this->newline) AND ($this->page > $startlinepage)) {
									// fix lines splitted over two pages
									if (isset($this->footerlen[$startlinepage])) {
										$curpos = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
									}
									// line to be moved one page forward
									$pagebuff = $this->getPageBuffer($startlinepage);
									$linebeg = substr($pagebuff, $startlinepos, ($curpos - $startlinepos));
									$tstart = substr($pagebuff, 0, $startlinepos);
									$tend = substr($this->getPageBuffer($startlinepage), $curpos);
									// remove line start from previous page
									$this->setPageBuffer($startlinepage, $tstart.''.$tend);
									$pagebuff = $this->getPageBuffer($this->page);
									$tstart = substr($pagebuff, 0, $this->cntmrk[$this->page]);
									$tend = substr($pagebuff, $this->cntmrk[$this->page]);
									// add line start to current page
									$yshift = $minstartliney - $this->y;
									$try = sprintf('1 0 0 1 0 %.3F cm', ($yshift * $this->k));
									$this->setPageBuffer($this->page, $tstart."\nq\n".$try."\n".$linebeg."\nQ\n".$tend);
									// shift the annotations and links
									if (isset($this->PageAnnots[$this->page])) {
										$next_pask = count($this->PageAnnots[$this->page]);
									} else {
										$next_pask = 0;
									}
									if (isset($this->PageAnnots[$startlinepage])) {
										foreach ($this->PageAnnots[$startlinepage] as $pak => $pac) {
											if ($pak >= $pask) {
												$this->PageAnnots[$this->page][] = $pac;
												unset($this->PageAnnots[$startlinepage][$pak]);
												$npak = count($this->PageAnnots[$this->page]) - 1;
												$this->PageAnnots[$this->page][$npak]['y'] -= $yshift;
											}
										}
									}
									$pask = $next_pask;
								}
								if (($dom[$key]['value'] != 'td') AND ($dom[$key]['value'] != 'th')) {
									$this->y += (($curfontsize - $fontsize) / $this->k);
								}
								$minstartliney = min($this->y, $minstartliney);
							}
							$curfontname = $fontname;
							$curfontstyle = $fontstyle;
							$curfontsize = $fontsize;
						}
					}
					if (($plalign == 'J') AND (in_array($dom[$key]['value'], $blocktags))) {
						$plalign = '';
					}
					// get current position on page buffer
					$curpos = $this->pagelen[$startlinepage];
					if (isset($dom[$key]['bgcolor']) AND ($dom[$key]['bgcolor'] !== false)) {
						$this->SetFillColorArray($dom[$key]['bgcolor']);
						$wfill = true;
					} else {
						$wfill = $fill | false;
					}
					if (isset($dom[$key]['fgcolor']) AND ($dom[$key]['fgcolor'] !== false)) {
						$this->SetTextColorArray($dom[$key]['fgcolor']);
					}
					if (isset($dom[$key]['align'])) {
						$lalign = $dom[$key]['align'];
					}
					if ($this->empty_string($lalign)) {
						$lalign = $align;
					}
				}
				// align lines
				if ($this->newline AND (strlen($dom[$key]['value']) > 0) AND ($dom[$key]['value'] != 'td') AND ($dom[$key]['value'] != 'th')) {
					$newline = true;
					// we are at the beginning of a new line
					if (isset($startlinex)) {
						$yshift = $minstartliney - $startliney;
						if (($yshift > 0) OR ($this->page > $startlinepage)) {
							$yshift = 0;
						}
						if ((isset($plalign) AND ((($plalign == 'C') OR ($plalign == 'J') OR (($plalign == 'R') AND (!$this->rtl)) OR (($plalign == 'L') AND ($this->rtl))))) OR ($yshift < 0)) {
							// the last line must be shifted to be aligned as requested
							$linew = abs($this->endlinex - $startlinex);
							$pstart = substr($this->getPageBuffer($startlinepage), 0, $startlinepos);
							if (isset($opentagpos) AND isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
								$this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
								$midpos = min($opentagpos, $this->footerpos[$startlinepage]);
							} elseif (isset($opentagpos)) {
								$midpos = $opentagpos;
							} elseif (isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
								$this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
								$midpos = $this->footerpos[$startlinepage];
							} else {
								$midpos = 0;
							}
							if ($midpos > 0) {
								$pmid = substr($this->getPageBuffer($startlinepage), $startlinepos, ($midpos - $startlinepos));
								$pend = substr($this->getPageBuffer($startlinepage), $midpos);
							} else {
								$pmid = substr($this->getPageBuffer($startlinepage), $startlinepos);
								$pend = '';
							}
							// calculate shifting amount
							$tw = $w;
							if ($this->lMargin != $prevlMargin) {
								$tw += ($prevlMargin - $this->lMargin);
							}
							if ($this->rMargin != $prevrMargin) {
								$tw += ($prevrMargin - $this->rMargin);
							}
							$mdiff = abs($tw - $linew);
							$t_x = 0;
							if ($plalign == 'C') {
								if ($this->rtl) {
									$t_x = -($mdiff / 2);
								} else {
									$t_x = ($mdiff / 2);
								}
							} elseif (($plalign == 'R') AND (!$this->rtl)) {
								// right alignment on LTR document
								$t_x = $mdiff;	
							} elseif (($plalign == 'L') AND ($this->rtl)) {
								// left alignment on RTL document
								$t_x = -$mdiff;
							} elseif (($plalign == 'J') AND ($plalign == $lalign)) {
								// Justification
								if ($this->rtl OR $this->tmprtl) {
									$t_x = $this->lMargin - $this->endlinex;
								}
								$no = 0;
								$ns = 0;
								$pmidtemp = $pmid;
								// escape special characters
								$pmidtemp = preg_replace('/[\\\][\(]/x', '\\#!#OP#!#', $pmidtemp);
								$pmidtemp = preg_replace('/[\\\][\)]/x', '\\#!#CP#!#', $pmidtemp);
								// search spaces
								if (preg_match_all('/\[\(([^\)]*)\)\]/x', $pmidtemp, $lnstring, PREG_PATTERN_ORDER)) {
									$maxkk = count($lnstring[1]) - 1;
									for ($kk=0; $kk <= $maxkk; ++$kk) {
										// restore special characters
										$lnstring[1][$kk] = str_replace('#!#OP#!#', '(', $lnstring[1][$kk]);
										$lnstring[1][$kk] = str_replace('#!#CP#!#', ')', $lnstring[1][$kk]);
										if ($kk == $maxkk) {
											if ($this->rtl OR $this->tmprtl) {
												$tvalue = ltrim($lnstring[1][$kk]);
											} else {
												$tvalue = rtrim($lnstring[1][$kk]);
											}
										} else {
											$tvalue = $lnstring[1][$kk];
										}
										// count spaces on line
										$no += substr_count($lnstring[1][$kk], chr(32));
										$ns += substr_count($tvalue, chr(32));
									}
									if ($this->rtl OR $this->tmprtl) {
										$t_x = $this->lMargin - $this->endlinex - (($no - $ns - 1) * $this->GetStringWidth(chr(32)));
									}
									// calculate additional space to add to each space
									$spacelen = $this->GetStringWidth(chr(32));
									$spacewidth = (($tw - $linew + (($no - $ns) * $spacelen)) / ($ns?$ns:1)) * $this->k;
									$spacewidthu = -1000 * ($tw - $linew + ($no * $spacelen)) / ($ns?$ns:1) / $this->FontSize;
									$nsmax = $ns;
									$ns = 0;
									reset($lnstring);
									$offset = 0;
									$strcount = 0;
									$prev_epsposbeg = 0;
									global $spacew;
									while (preg_match('/([0-9\.\+\-]*)[\s](Td|cm|m|l|c|re)[\s]/x', $pmid, $strpiece, PREG_OFFSET_CAPTURE, $offset) == 1) {
										// check if we are inside a string section '[( ... )]'
										$stroffset = strpos($pmid, '[(', $offset);
										if (($stroffset !== false) AND ($stroffset <= $strpiece[2][1])) {
											// set offset to the end of string section 
											$offset = strpos($pmid, ')]', $stroffset);
											while (($offset !== false) AND ($pmid{($offset - 1)} == '\\')) {
												$offset = strpos($pmid, ')]', ($offset + 1));
											}
											if ($offset === false) {
												$this->Error('HTML Justification: malformed PDF code.');
											}
											continue;
										}
										if ($this->rtl OR $this->tmprtl) {
											$spacew = ($spacewidth * ($nsmax - $ns));
										} else {
											$spacew = ($spacewidth * $ns);
										}
										$offset = $strpiece[2][1] + strlen($strpiece[2][0]);
										$epsposbeg = strpos($pmid, 'q'.$this->epsmarker, $offset);
										$epsposend = strpos($pmid, $this->epsmarker.'Q', $offset) + strlen($this->epsmarker.'Q');
										if ((($epsposbeg > 0) AND ($epsposend > 0) AND ($offset > $epsposbeg) AND ($offset < $epsposend))
											OR (($epsposbeg === false) AND ($epsposend > 0) AND ($offset < $epsposend))) {
											// shift EPS images
											$trx = sprintf('1 0 0 1 %.3F 0 cm', $spacew);
											$epsposbeg = strpos($pmid, 'q'.$this->epsmarker, ($prev_epsposbeg - 6));
											$pmid_b = substr($pmid, 0, $epsposbeg);
											$pmid_m = substr($pmid, $epsposbeg, ($epsposend - $epsposbeg));
											$pmid_e = substr($pmid, $epsposend);
											$pmid = $pmid_b."\nq\n".$trx."\n".$pmid_m."\nQ\n".$pmid_e;
											$offset = $epsposend;
											continue;
										}
										$prev_epsposbeg = $epsposbeg;
										$currentxpos = 0;
										// shift blocks of code
										switch ($strpiece[2][0]) {
											case 'Td':
											case 'cm':
											case 'm':
											case 'l': {
												// get current X position
												preg_match('/([0-9\.\+\-]*)[\s]('.$strpiece[1][0].')[\s]('.$strpiece[2][0].')([\s]*)/x', $pmid, $xmatches);
												$currentxpos = $xmatches[1];
												if (($strcount <= $maxkk) AND ($strpiece[2][0] == 'Td')) {
													if ($strcount == $maxkk) {
														if ($this->rtl OR $this->tmprtl) {
															$tvalue = $lnstring[1][$strcount];
														} else {
															$tvalue = rtrim($lnstring[1][$strcount]);
														}
													} else {
														$tvalue = $lnstring[1][$strcount];
													}
													$ns += substr_count($tvalue, chr(32));
													++$strcount;
												}
												if ($this->rtl OR $this->tmprtl) {
													$spacew = ($spacewidth * ($nsmax - $ns));
												}
												// justify block
												$pmid = preg_replace_callback('/([0-9\.\+\-]*)[\s]('.$strpiece[1][0].')[\s]('.$strpiece[2][0].')([\s]*)/x',
													create_function('$matches', 'global $spacew;
													$newx = sprintf("%.2F",(floatval($matches[1]) + $spacew));
													return "".$newx." ".$matches[2]." x*#!#*x".$matches[3].$matches[4];'), $pmid, 1);
												break;
											}
											case 're': {
												// get current X position
												preg_match('/([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]('.$strpiece[1][0].')[\s]('.$strpiece[2][0].')([\s]*)/x', $pmid, $xmatches);
												$currentxpos = $xmatches[1];
												// justify block
												$pmid = preg_replace_callback('/([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]('.$strpiece[1][0].')[\s]('.$strpiece[2][0].')([\s]*)/x',
													create_function('$matches', 'global $spacew;
													$newx = sprintf("%.2F",(floatval($matches[1]) + $spacew));
													return "".$newx." ".$matches[2]." ".$matches[3]." ".$matches[4]." x*#!#*x".$matches[5].$matches[6];'), $pmid, 1);
												break;
											}
											case 'c': {
												// get current X position
												preg_match('/([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]('.$strpiece[1][0].')[\s]('.$strpiece[2][0].')([\s]*)/x', $pmid, $xmatches);
												$currentxpos = $xmatches[1];
												// justify block
												$pmid = preg_replace_callback('/([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]([0-9\.\+\-]*)[\s]('.$strpiece[1][0].')[\s]('.$strpiece[2][0].')([\s]*)/x',
													create_function('$matches', 'global $spacew;
													$newx1 = sprintf("%.3F",(floatval($matches[1]) + $spacew));
													$newx2 = sprintf("%.3F",(floatval($matches[3]) + $spacew));
													$newx3 = sprintf("%.3F",(floatval($matches[5]) + $spacew));
													return "".$newx1." ".$matches[2]." ".$newx2." ".$matches[4]." ".$newx3." ".$matches[6]." x*#!#*x".$matches[7].$matches[8];'), $pmid, 1);
												break;
											}
										}
										// shift the annotations and links
										if (isset($this->PageAnnots[$this->page])) {
											foreach ($this->PageAnnots[$this->page] as $pak => $pac) {
												if (($pac['y'] >= $minstartliney) AND (($pac['x'] * $this->k) >= ($currentxpos - $this->feps)) AND (($pac['x'] * $this->k) <= ($currentxpos + $this->feps))) {
													$this->PageAnnots[$this->page][$pak]['x'] += ($spacew / $this->k);
													$this->PageAnnots[$this->page][$pak]['w'] += (($spacewidth * $pac['numspaces']) / $this->k);
													break;
												}
											}
										}
									} // end of while
									// remove markers
									$pmid = str_replace('x*#!#*x', '', $pmid);
									if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
										// multibyte characters
										$spacew = $spacewidthu;
										$pmidtemp = $pmid;
										// escape special characters
										$pmidtemp = preg_replace('/[\\\][\(]/x', '\\#!#OP#!#', $pmidtemp);
										$pmidtemp = preg_replace('/[\\\][\)]/x', '\\#!#CP#!#', $pmidtemp);
										$pmid = preg_replace_callback("/\[\(([^\)]*)\)\]/x",
													create_function('$matches', 'global $spacew;
													$matches[1] = str_replace("#!#OP#!#", "(", $matches[1]);
													$matches[1] = str_replace("#!#CP#!#", ")", $matches[1]);
													return "[(".str_replace(chr(0).chr(32), ") ".($spacew)." (", $matches[1]).")]";'), $pmidtemp);
										$this->setPageBuffer($startlinepage, $pstart."\n".$pmid."\n".$pend);
										$endlinepos = strlen($pstart."\n".$pmid."\n");
									} else {
										// non-unicode (single-byte characters)
										$rs = sprintf("%.3F Tw", $spacewidth);
										$pmid = preg_replace("/\[\(/x", $rs.' [(', $pmid);
										$this->setPageBuffer($startlinepage, $pstart."\n".$pmid."\nBT 0 Tw ET\n".$pend);
										$endlinepos = strlen($pstart."\n".$pmid."\nBT 0 Tw ET\n");
									}
								}
							} // end of J
							if (($t_x != 0) OR ($yshift < 0)) {
								// shift the line
								$trx = sprintf('1 0 0 1 %.3F %.3F cm', ($t_x * $this->k), ($yshift * $this->k));
								$this->setPageBuffer($startlinepage, $pstart."\nq\n".$trx."\n".$pmid."\nQ\n".$pend);
								$endlinepos = strlen($pstart."\nq\n".$trx."\n".$pmid."\nQ\n");
								// shift the annotations and links
								if (isset($this->PageAnnots[$this->page])) {
									foreach ($this->PageAnnots[$this->page] as $pak => $pac) {
										if ($pak >= $pask) {
											$this->PageAnnots[$this->page][$pak]['x'] += $t_x;
											$this->PageAnnots[$this->page][$pak]['y'] -= $yshift;
										}
									}
								}
								$this->y -= $yshift;
							}
						}
					}
					$this->newline = false;
					$pbrk = $this->checkPageBreak($this->lasth);
					$this->SetFont($fontname, $fontstyle, $fontsize);
					if ($wfill) {
						$this->SetFillColorArray($this->bgcolor);
					}
					$startlinex = $this->x;
					$startliney = $this->y;
					$minstartliney = $this->y;
					$startlinepage = $this->page;
					if (isset($endlinepos) AND (!$pbrk)) {
						$startlinepos = $endlinepos;
						unset($endlinepos);
					} else {
						if (isset($this->footerlen[$this->page])) {
							$this->footerpos[$this->page] = $this->pagelen[$this->page] - $this->footerlen[$this->page];
						} else {
							$this->footerpos[$this->page] = $this->pagelen[$this->page];
						}
						$startlinepos = $this->footerpos[$this->page];
					}
					$plalign = $lalign;
					if (isset($this->PageAnnots[$this->page])) {
						$pask = count($this->PageAnnots[$this->page]);
					} else {
						$pask = 0;
					}
				}
				if (isset($opentagpos)) {
					unset($opentagpos);
				}
				if ($dom[$key]['tag']) {
					if ($dom[$key]['opening']) {
						// get text indentation (if any)
						if (isset($dom[$key]['text-indent']) AND in_array($dom[$key]['value'], array('blockquote','dd','div','dt','h1','h2','h3','h4','h5','h6','li','ol','p','ul','table','tr','td'))) {
							$this->textindent = $dom[$key]['text-indent'];
						}
						if ($dom[$key]['value'] == 'table') {
							if ($this->rtl) {
								$wtmp = $this->x - $this->lMargin;
							} else {
								$wtmp = $this->w - $this->rMargin - $this->x;
							}
							$wtmp -= (2 * $this->cMargin);
							// calculate cell width
							if (isset($dom[$key]['width'])) {
								$table_width = $this->getHTMLUnitToUnits($dom[$key]['width'], $wtmp, 'px');
							} else {
								$table_width = $wtmp;
							}
						}
						// table content is handled in a special way
						if (($dom[$key]['value'] == 'td') OR ($dom[$key]['value'] == 'th')) {
							$trid = $dom[$key]['parent'];
							$table_el = $dom[$trid]['parent'];
							if (!isset($dom[$table_el]['cols'])) {
								$dom[$table_el]['cols'] = $trid['cols'];
							}
							$oldmargin = $this->cMargin;
							if (isset($dom[($dom[$trid]['parent'])]['attribute']['cellpadding'])) {
								$currentcmargin = $this->getHTMLUnitToUnits($dom[($dom[$trid]['parent'])]['attribute']['cellpadding'], 1, 'px');
							} else {
								$currentcmargin = 0;		
							}
							$this->cMargin = $currentcmargin;
							if (isset($dom[($dom[$trid]['parent'])]['attribute']['cellspacing'])) {
								$cellspacing = $this->getHTMLUnitToUnits($dom[($dom[$trid]['parent'])]['attribute']['cellspacing'], 1, 'px');
							} else {
								$cellspacing = 0;
							}
							if ($this->rtl) {
								$cellspacingx = -$cellspacing;
							} else {
								$cellspacingx = $cellspacing;
							}
							$colspan = $dom[$key]['attribute']['colspan'];
							$wtmp = ($colspan * ($table_width / $dom[$table_el]['cols']));
							if (isset($dom[$key]['width'])) {
								$cellw = $this->getHTMLUnitToUnits($dom[$key]['width'], $table_width, 'px');
							} else {
								$cellw = $wtmp;
							}
							if (isset($dom[$key]['height'])) {
								// minimum cell height
								$cellh = $this->getHTMLUnitToUnits($dom[$key]['height'], 0, 'px');
							} else {
								$cellh = 0;
							}
							$cellw -= $cellspacing;
							if (isset($dom[$key]['content'])) {
								$cell_content = $dom[$key]['content'];
							} else {
								$cell_content = '&nbsp;';
							}
							$tagtype = $dom[$key]['value'];
							$parentid = $key;
							while (($key < $maxel) AND (!(($dom[$key]['tag']) AND (!$dom[$key]['opening']) AND ($dom[$key]['value'] == $tagtype) AND ($dom[$key]['parent'] == $parentid)))) {
								// move $key index forward
								++$key;
							}
							if (!isset($dom[$trid]['startpage'])) {
								$dom[$trid]['startpage'] = $this->page;
							} else {
								$this->setPage($dom[$trid]['startpage']);
							}
							if (!isset($dom[$trid]['starty'])) {
								$dom[$trid]['starty'] = $this->y;
							} else {
								$this->y = $dom[$trid]['starty'];
							}
							if (!isset($dom[$trid]['startx'])) {
								$dom[$trid]['startx'] = $this->x;
							}
							$this->x += ($cellspacingx / 2);						
							if (isset($dom[$parentid]['attribute']['rowspan'])) {
								$rowspan = intval($dom[$parentid]['attribute']['rowspan']);
							} else {
								$rowspan = 1;
							}
							// skip row-spanned cells started on the previous rows
							if (isset($dom[$table_el]['rowspans'])) {
								$rsk = 0;
								$rskmax = count($dom[$table_el]['rowspans']);
								while ($rsk < $rskmax) {
									$trwsp = $dom[$table_el]['rowspans'][$rsk];
									$rsstartx = $trwsp['startx'];
									$rsendx = $trwsp['endx'];
									// account for margin changes
									if ($trwsp['startpage'] < $this->page) {
										if (($this->rtl) AND ($this->pagedim[$this->page]['orm'] != $this->pagedim[$trwsp['startpage']]['orm'])) {
											$dl = ($this->pagedim[$this->page]['orm'] - $this->pagedim[$trwsp['startpage']]['orm']);
											$rsstartx -= $dl;
											$rsendx -= $dl;
										} elseif ((!$this->rtl) AND ($this->pagedim[$this->page]['olm'] != $this->pagedim[$trwsp['startpage']]['olm'])) {
											$dl = ($this->pagedim[$this->page]['olm'] - $this->pagedim[$trwsp['startpage']]['olm']);
											$rsstartx += $dl;
											$rsendx += $dl;
										}
									}
									if  (($trwsp['rowspan'] > 0)
										AND ($rsstartx > ($this->x - $cellspacing - $currentcmargin - $this->feps))
										AND ($rsstartx < ($this->x + $cellspacing + $currentcmargin + $this->feps))
										AND (($trwsp['starty'] < ($this->y - $this->feps)) OR ($trwsp['startpage'] < $this->page))) {
										// set the starting X position of the current cell
										$this->x = $rsendx + $cellspacingx;
										if (($trwsp['rowspan'] == 1)
											AND (isset($dom[$trid]['endy']))
											AND (isset($dom[$trid]['endpage']))
											AND ($trwsp['endpage'] == $dom[$trid]['endpage'])) {
											// set ending Y position for row
											$dom[$table_el]['rowspans'][$rsk]['endy'] = max($dom[$trid]['endy'], $trwsp['endy']);
											$dom[$trid]['endy'] = $dom[$table_el]['rowspans'][$rsk]['endy'];
										}
										$rsk = 0;
									} else {
										++$rsk;
									}
								}
							}
							// add rowspan information to table element
							if ($rowspan > 1) {
								if (isset($this->footerlen[$this->page])) {
									$this->footerpos[$this->page] = $this->pagelen[$this->page] - $this->footerlen[$this->page];
								} else {
									$this->footerpos[$this->page] = $this->pagelen[$this->page];
								}
								$trintmrkpos = $this->footerpos[$this->page];
								$trsid = array_push($dom[$table_el]['rowspans'], array('trid' => $trid, 'rowspan' => $rowspan, 'mrowspan' => $rowspan, 'colspan' => $colspan, 'startpage' => $this->page, 'startx' => $this->x, 'starty' => $this->y, 'intmrkpos' => $trintmrkpos));
							}
							$cellid = array_push($dom[$trid]['cellpos'], array('startx' => $this->x));
							if ($rowspan > 1) {
								$dom[$trid]['cellpos'][($cellid - 1)]['rowspanid'] = ($trsid - 1);
							}
							// push background colors
							if (isset($dom[$parentid]['bgcolor']) AND ($dom[$parentid]['bgcolor'] !== false)) {
								$dom[$trid]['cellpos'][($cellid - 1)]['bgcolor'] = $dom[$parentid]['bgcolor'];
							}
							$prevLastH = $this->lasth;
							// ****** write the cell content ******
							$this->MultiCell($cellw, $cellh, $cell_content, false, $lalign, false, 2, '', '', true, 0, true);
							$this->lasth = $prevLastH;
							$this->cMargin = $oldmargin;
							$dom[$trid]['cellpos'][($cellid - 1)]['endx'] = $this->x;
							// update the end of row position
							if ($rowspan <= 1) {
								if (isset($dom[$trid]['endy'])) {
									if ($this->page == $dom[$trid]['endpage']) {
										$dom[$trid]['endy'] = max($this->y, $dom[$trid]['endy']);
									} elseif ($this->page > $dom[$trid]['endpage']) {
										$dom[$trid]['endy'] = $this->y;
									}
								} else {
									$dom[$trid]['endy'] = $this->y;
								}
								if (isset($dom[$trid]['endpage'])) {
									$dom[$trid]['endpage'] = max($this->page, $dom[$trid]['endpage']);
								} else {
									$dom[$trid]['endpage'] = $this->page;
								}								
							} else {
								// account for row-spanned cells
								$dom[$table_el]['rowspans'][($trsid - 1)]['endx'] = $this->x;
								$dom[$table_el]['rowspans'][($trsid - 1)]['endy'] = $this->y;
								$dom[$table_el]['rowspans'][($trsid - 1)]['endpage'] = $this->page;
							}
							if (isset($dom[$table_el]['rowspans'])) {
								// update endy and endpage on rowspanned cells
								foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
									if ($trwsp['rowspan'] > 0) {
										if (isset($dom[$trid]['endpage'])) {
											if ($trwsp['endpage'] == $dom[$trid]['endpage']) {
												$dom[$table_el]['rowspans'][$k]['endy'] = max($dom[$trid]['endy'], $trwsp['endy']);
											} elseif ($trwsp['endpage'] < $dom[$trid]['endpage']) {
												$dom[$table_el]['rowspans'][$k]['endy'] = $dom[$trid]['endy'];
												$dom[$table_el]['rowspans'][$k]['endpage'] = $dom[$trid]['endpage'];
											} else {
												$dom[$trid]['endy'] = $this->pagedim[$dom[$trid]['endpage']]['hk'] - $this->pagedim[$dom[$trid]['endpage']]['bm'];
											}
										}
									}
								}
							}
							$this->x += ($cellspacingx / 2);
						} else {
							// opening tag (or self-closing tag)
							if (!isset($opentagpos)) {
								if (!$this->InFooter) {
									if (isset($this->footerlen[$this->page])) {
										$this->footerpos[$this->page] = $this->pagelen[$this->page] - $this->footerlen[$this->page];
									} else {
										$this->footerpos[$this->page] = $this->pagelen[$this->page];
									}
									$opentagpos = $this->footerpos[$this->page];
								}
							}
							$this->openHTMLTagHandler($dom, $key, $cell);
						}
					} else {
						// closing tag
						$this->closeHTMLTagHandler($dom, $key, $cell);
					}
				} elseif (strlen($dom[$key]['value']) > 0) {
					// print list-item
					if (!$this->empty_string($this->lispacer)) {
						$this->SetFont($pfontname, $pfontstyle, $pfontsize);
						$this->lasth = $this->FontSize * $this->cell_height_ratio;
						$minstartliney = $this->y;
						$this->putHtmlListBullet($this->listnum, $this->lispacer, $pfontsize);
						$this->SetFont($curfontname, $curfontstyle, $curfontsize);
						$this->lasth = $this->FontSize * $this->cell_height_ratio;
						if (is_numeric($pfontsize) AND ($pfontsize > 0) AND is_numeric($curfontsize) AND ($curfontsize > 0) AND ($pfontsize != $curfontsize)) {
							$this->y += (($pfontsize - $curfontsize) / $this->k);
							$minstartliney = min($this->y, $minstartliney);
						}
					}
					// text
					$this->htmlvspace = 0;
					if ((!$this->premode) AND ($this->rtl OR $this->tmprtl)) {
						// reverse spaces order
						$len1 = strlen($dom[$key]['value']);
						$lsp = $len1 - strlen(ltrim($dom[$key]['value']));
						$rsp = $len1 - strlen(rtrim($dom[$key]['value']));
						$tmpstr = '';
						if ($rsp > 0) {
							$tmpstr .= substr($dom[$key]['value'], -$rsp);
						}
						$tmpstr .= trim($dom[$key]['value']);
						if ($lsp > 0) {
							$tmpstr .= substr($dom[$key]['value'], 0, $lsp);
						}
						$dom[$key]['value'] = $tmpstr;
					}
					if ($newline) {
						if (!$this->premode) {
							if (($this->rtl OR $this->tmprtl)) {
								$dom[$key]['value'] = rtrim($dom[$key]['value']);
							} else {
								$dom[$key]['value'] = ltrim($dom[$key]['value']);
							}
						}
						$newline = false;
						$firstblock = true;
					} else {
						$firstblock = false;
					}
					$strrest = '';
					if (!empty($this->HREF) AND (isset($this->HREF['url']))) {
						// HTML <a> Link
						$strrest = $this->addHtmlLink($this->HREF['url'], $dom[$key]['value'], $wfill, true, $this->HREF['color'], $this->HREF['style']);
					} else {
						$ctmpmargin = $this->cMargin;
						$this->cMargin = 0;
						if ($this->rtl) {
							$this->x -= $this->textindent;
						} else {
							$this->x += $this->textindent;
						}
						// ****** write only until the end of the line and get the rest ******
						$strrest = $this->Write($this->lasth, $dom[$key]['value'], '', $wfill, '', false, 0, true, $firstblock);
						$this->textindent = 0;
						$this->cMargin = $ctmpmargin;
					}
					if (strlen($strrest) > 0) {
						// store the remaining string on the previous $key position
						$this->newline = true;
						if ($cell) {
							if ($this->rtl) {
								$this->x -= $this->cMargin;
							} else {
								$this->x += $this->cMargin;
							}
						}
						if ($strrest == $dom[$key]['value']) {
							// used to avoid infinite loop
							++$loop;
						} else {
							$loop = 0;
						}
						$dom[$key]['value'] = ltrim($strrest);
						if ($loop < 3) {
							--$key;
						}
					} else {
						$loop = 0;
					}
				}
				++$key;
				if (isset($dom[$key]['tag']) AND $dom[$key]['tag'] AND (!isset($dom[$key]['opening']) OR !$dom[$key]['opening']) AND isset($dom[($dom[$key]['parent'])]['attribute']['nobr']) AND ($dom[($dom[$key]['parent'])]['attribute']['nobr'] == 'true')) {
					if ((!$undo) AND ($this->start_transaction_page == ($this->numpages - 1))) {
						// restore previous object
						$this->rollbackTransaction(true);
						// restore previous values
						foreach ($this_method_vars as $vkey => $vval) {
							$$vkey = $vval;
						}
						// add a page
						$this->AddPage();
						$undo = true; // avoid infinite loop
					} else {
						$undo = false;
					}
				}
			} // end for each $key
			// align the last line
			if (isset($startlinex)) {
				$yshift = $minstartliney - $startliney;
				if (($yshift > 0) OR ($this->page > $startlinepage)) {
					$yshift = 0;
				}
				if ((isset($plalign) AND ((($plalign == 'C') OR ($plalign == 'J') OR (($plalign == 'R') AND (!$this->rtl)) OR (($plalign == 'L') AND ($this->rtl))))) OR ($yshift < 0)) {
					// the last line must be shifted to be aligned as requested
					$linew = abs($this->endlinex - $startlinex);
					$pstart = substr($this->getPageBuffer($startlinepage), 0, $startlinepos);
					if (isset($opentagpos) AND isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
						$this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
						$midpos = min($opentagpos, $this->footerpos[$startlinepage]);
					} elseif (isset($opentagpos)) {
						$midpos = $opentagpos;
					} elseif (isset($this->footerlen[$startlinepage]) AND (!$this->InFooter)) {
						$this->footerpos[$startlinepage] = $this->pagelen[$startlinepage] - $this->footerlen[$startlinepage];
						$midpos = $this->footerpos[$startlinepage];
					} else {
						$midpos = 0;
					}
					if ($midpos > 0) {
						$pmid = substr($this->getPageBuffer($startlinepage), $startlinepos, ($midpos - $startlinepos));
						$pend = substr($this->getPageBuffer($startlinepage), $midpos);
					} else {
						$pmid = substr($this->getPageBuffer($startlinepage), $startlinepos);
						$pend = '';
					}	
					// calculate shifting amount
					$tw = $w;
					if ($this->lMargin != $prevlMargin) {
						$tw += ($prevlMargin - $this->lMargin);
					}
					if ($this->rMargin != $prevrMargin) {
						$tw += ($prevrMargin - $this->rMargin);
					}
					$mdiff = abs($tw - $linew);
					if ($plalign == 'C') {
						if ($this->rtl) {
							$t_x = -($mdiff / 2);
						} else {
							$t_x = ($mdiff / 2);
						}
					} elseif (($plalign == 'R') AND (!$this->rtl)) {
						// right alignment on LTR document
						$t_x = $mdiff;
					} elseif (($plalign == 'L') AND ($this->rtl)) {
						// left alignment on RTL document
						$t_x = -$mdiff;
					} else {
						$t_x = 0;
					}
					if (($t_x != 0) OR ($yshift < 0)) {
						// shift the line
						$trx = sprintf('1 0 0 1 %.3F %.3F cm', ($t_x * $this->k), ($yshift * $this->k));
						$this->setPageBuffer($startlinepage, $pstart."\nq\n".$trx."\n".$pmid."\nQ\n".$pend);
						$endlinepos = strlen($pstart."\nq\n".$trx."\n".$pmid."\nQ\n");
						// shift the annotations and links
						if (isset($this->PageAnnots[$this->page])) {
							foreach ($this->PageAnnots[$this->page] as $pak => $pac) {
								if ($pak >= $pask) {
									$this->PageAnnots[$this->page][$pak]['x'] += $t_x;
									$this->PageAnnots[$this->page][$pak]['y'] -= $yshift;
								}
							}
						}
						$this->y -= $yshift;
					}
				}
			}
			if ($ln AND (!($cell AND ($dom[$key-1]['value'] == 'table')))) {
				$this->Ln($this->lasth);
			}
			// restore previous values
			$this->setGraphicVars($gvars);
			if ($this->page > $prevPage) {
				$this->lMargin = $this->pagedim[$this->page]['olm'];
				$this->rMargin = $this->pagedim[$this->page]['orm'];
			}
			// restore previous list state
			$this->listnum = $prev_listnum;
			$this->listordered = $prev_listordered;
			$this->listcount = $prev_listcount;
			$this->lispacer = $prev_lispacer;
			unset($dom);
		}
		
		/**
		 * Process opening tags.
		 * @param array $dom html dom array 
		 * @param int $key current element id
		 * @param boolean $cell if true add the default cMargin space to each new line (default false).
		 * @access protected
		 */
		protected function openHTMLTagHandler(&$dom, $key, $cell=false) {
			$tag = $dom[$key];
			$parent = $dom[($dom[$key]['parent'])];
			$firstorlast = ($key == 1);
			// check for text direction attribute
			if (isset($tag['attribute']['dir'])) {
				$this->tmprtl = $tag['attribute']['dir'] == 'rtl' ? 'R' : 'L';
			} else {
				$this->tmprtl = false;
			}
			//Opening tag
			switch($tag['value']) {
				case 'table': {
					$cp = 0;
					$cs = 0;
					$dom[$key]['rowspans'] = array();
					if (!$this->empty_string($dom[$key]['thead'])) {
						// set table header
						$this->thead = $dom[$key]['thead'];
						if (!isset($this->theadMargins) OR (empty($this->theadMargins))) {
							$this->theadMargins = array();
							$this->theadMargins['cmargin'] = $this->cMargin;
						}
					}
					if (isset($tag['attribute']['cellpadding'])) {
						$cp = $this->getHTMLUnitToUnits($tag['attribute']['cellpadding'], 1, 'px');
						$this->oldcMargin = $this->cMargin;
						$this->cMargin = $cp;
					}
					if (isset($tag['attribute']['cellspacing'])) {
						$cs = $this->getHTMLUnitToUnits($tag['attribute']['cellspacing'], 1, 'px');
					}
					$this->checkPageBreak((2 * $cp) + (2 * $cs) + $this->lasth);
					break;
				}
				case 'tr': {
					// array of columns positions
					$dom[$key]['cellpos'] = array();
					break;
				}
				case 'hr': {
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], false);
					$this->htmlvspace = 0;
					$wtmp = $this->w - $this->lMargin - $this->rMargin;
					if ((isset($tag['attribute']['width'])) AND ($tag['attribute']['width'] != '')) {
						$hrWidth = $this->getHTMLUnitToUnits($tag['attribute']['width'], $wtmp, 'px');
					} else {
						$hrWidth = $wtmp;
					}
					$x = $this->GetX();
					$y = $this->GetY();
					$prevlinewidth = $this->GetLineWidth();
					$this->Line($x, $y, $x + $hrWidth, $y);
					$this->SetLineWidth($prevlinewidth);
					$this->addHTMLVertSpace(1, $cell, '', !isset($dom[($key + 1)]), $tag['value'], false);
					break;
				}
				case 'a': {
					if (array_key_exists('href', $tag['attribute'])) {
						$this->HREF['url'] = $tag['attribute']['href'];
					}
					$this->HREF['color'] = $this->htmlLinkColorArray;
					$this->HREF['style'] = $this->htmlLinkFontStyle;
					if (array_key_exists('style', $tag['attribute'])) {
						// get style attributes
						preg_match_all('/([^;:\s]*):([^;]*)/', $tag['attribute']['style'], $style_array, PREG_PATTERN_ORDER);
						$astyle = array();
						while (list($id, $name) = each($style_array[1])) {
							$name = strtolower($name);
							$astyle[$name] = trim($style_array[2][$id]);
						}
						if (isset($astyle['color'])) {
							$this->HREF['color'] = $this->convertHTMLColorToDec($astyle['color']);
						}
						if (isset($astyle['text-decoration'])) {
							$this->HREF['style'] = '';
							$decors = explode(' ', strtolower($astyle['text-decoration']));
							foreach ($decors as $dec) {
								$dec = trim($dec);
								if (!$this->empty_string($dec)) {
									if ($dec{0} == 'u') {
										$this->HREF['style'] .= 'U';
									} elseif ($dec{0} == 'l') {
										$this->HREF['style'] .= 'D';
									}
								}
							}
						}
					}		
					break;
				}
				case 'img': {
					if (isset($tag['attribute']['src'])) {
						// replace relative path with real server path
						if (($tag['attribute']['src'][0] == '/') AND ($_SERVER['DOCUMENT_ROOT'] != '/')) {
							$tag['attribute']['src'] = $_SERVER['DOCUMENT_ROOT'].$tag['attribute']['src'];
						}
						$tag['attribute']['src'] = urldecode($tag['attribute']['src']);
						$tag['attribute']['src'] = str_replace(K_PATH_URL, K_PATH_MAIN, $tag['attribute']['src']);
						if (!isset($tag['attribute']['width'])) {
							$tag['attribute']['width'] = 0;
						}
						if (!isset($tag['attribute']['height'])) {
							$tag['attribute']['height'] = 0;
						}
						//if (!isset($tag['attribute']['align'])) {
							// the only alignment supported is "bottom"
							// further development is required for other modes.
							$tag['attribute']['align'] = 'bottom';
						//} 
						switch($tag['attribute']['align']) {
							case 'top': {
								$align = 'T';
								break;
							}
							case 'middle': {
								$align = 'M';
								break;
							}
							case 'bottom': {
								$align = 'B';
								break;
							}
							default: {
								$align = 'B';
								break;
							}
						}
						$fileinfo = pathinfo($tag['attribute']['src']);
						if (isset($fileinfo['extension']) AND (!$this->empty_string($fileinfo['extension']))) {
							$type = strtolower($fileinfo['extension']);
						}
						$prevy = $this->y;
						$xpos = $this->GetX();
						if (isset($dom[($key - 1)]) AND ($dom[($key - 1)]['value'] == ' ')) {
							if ($this->rtl) {
								$xpos += $this->GetStringWidth(' ');
							} else {
								$xpos -= $this->GetStringWidth(' ');
							}
						}
						$imglink = '';
						if (isset($this->HREF['url']) AND !$this->empty_string($this->HREF['url'])) {
							$imglink = $this->HREF['url'];
							if ($imglink{0} == '#') {
								// convert url to internal link
								$page = intval(substr($imglink, 1));
								$imglink = $this->AddLink();
								$this->SetLink($imglink, 0, $page);
							}
						}
						$border = 0;
						if (isset($tag['attribute']['border']) AND !empty($tag['attribute']['border'])) {
							// currently only support 1 (frame) or a combination of 'LTRB'
							$border = $tag['attribute']['border'];
						}
						$iw = '';
						if (isset($tag['attribute']['width'])) {
							$iw = $this->getHTMLUnitToUnits($tag['attribute']['width'], 1, 'px', false);
						}
						$ih = '';
						if (isset($tag['attribute']['height'])) {
							$ih = $this->getHTMLUnitToUnits($tag['attribute']['height'], 1, 'px', false);
						}
						if (($type == 'eps') OR ($type == 'ai')) {
							$this->ImageEps($tag['attribute']['src'], $xpos, $this->GetY(), $iw, $ih, $imglink, true, $align, '', $border);
						} else {
							$this->Image($tag['attribute']['src'], $xpos, $this->GetY(), $iw, $ih, '', $imglink, $align, false, 300, '', false, false, $border);
						}
						switch($align) {
							case 'T': {
								$this->y = $prevy;
								break;
							}
							case 'M': {
								$this->y = (($this->img_rb_y + $prevy - ($tag['fontsize'] / $this->k)) / 2) ;
								break;
							}
							case 'B': {
								$this->y = $this->img_rb_y - ($tag['fontsize'] / $this->k);
								break;
							}
						}
					}
					break;
				}
				case 'dl': {
					++$this->listnum;
					$this->addHTMLVertSpace(0, $cell, '', $firstorlast, $tag['value'], false);
					break;
				}
				case 'dt': {
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], false);
					break;
				}
				case 'dd': {
					if ($this->rtl) {
						$this->rMargin += $this->listindent;
					} else {
						$this->lMargin += $this->listindent;
					}
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], false);
					break;
				}
				case 'ul':
				case 'ol': {
					$this->addHTMLVertSpace(0, $cell, '', $firstorlast, $tag['value'], false);
					$this->htmlvspace = 0;
					++$this->listnum;
					if ($tag['value'] == 'ol') {
						$this->listordered[$this->listnum] = true;
					} else {
						$this->listordered[$this->listnum] = false;
					}
					if (isset($tag['attribute']['start'])) {
						$this->listcount[$this->listnum] = intval($tag['attribute']['start']) - 1;
					} else {
						$this->listcount[$this->listnum] = 0;
					}
					if ($this->rtl) {
						$this->rMargin += $this->listindent;
					} else {
						$this->lMargin += $this->listindent;
					}
					$this->addHTMLVertSpace(0, $cell, '', $firstorlast, $tag['value'], false);
					$this->htmlvspace = 0;
					break;
				}
				case 'li': {
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], false);
					if ($this->listordered[$this->listnum]) {
						// ordered item
						if (isset($parent['attribute']['type']) AND !$this->empty_string($parent['attribute']['type'])) {
							$this->lispacer = $parent['attribute']['type'];
						} elseif (isset($parent['listtype']) AND !$this->empty_string($parent['listtype'])) {
							$this->lispacer = $parent['listtype'];
						} elseif (isset($this->lisymbol) AND !$this->empty_string($this->lisymbol)) {
							$this->lispacer = $this->lisymbol;
						} else {
							$this->lispacer = '#';
						}
						++$this->listcount[$this->listnum];
						if (isset($tag['attribute']['value'])) {
							$this->listcount[$this->listnum] = intval($tag['attribute']['value']);
						}
					} else {
						// unordered item
						if (isset($parent['attribute']['type']) AND !$this->empty_string($parent['attribute']['type'])) {
							$this->lispacer = $parent['attribute']['type'];
						} elseif (isset($parent['listtype']) AND !$this->empty_string($parent['listtype'])) {
							$this->lispacer = $parent['listtype'];
						} elseif (isset($this->lisymbol) AND !$this->empty_string($this->lisymbol)) {
							$this->lispacer = $this->lisymbol;
						} else {
							$this->lispacer = '!';
						}
					}
					break;
				}
				case 'blockquote': {
					if ($this->rtl) {
						$this->rMargin += $this->listindent;
					} else {
						$this->lMargin += $this->listindent;
					}
					$this->addHTMLVertSpace(2, $cell, '', $firstorlast, $tag['value'], false);
					break;
				}
				case 'br': {
					$this->Ln('', $cell);
					break;
				}
				case 'div': {
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], false);
					break;
				}
				case 'p': {
					$this->addHTMLVertSpace(2, $cell, '', $firstorlast, $tag['value'], false);
					break;
				}
				case 'pre': {
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], false);
					$this->premode = true;
					break;
				}
				case 'sup': {
					$this->SetXY($this->GetX(), $this->GetY() - ((0.7 * $this->FontSizePt) / $this->k));
					break;
				}
				case 'sub': {
					$this->SetXY($this->GetX(), $this->GetY() + ((0.3 * $this->FontSizePt) / $this->k));
					break;
				}
				case 'h1': 
				case 'h2': 
				case 'h3': 
				case 'h4': 
				case 'h5': 
				case 'h6': {
					$this->addHTMLVertSpace(1, $cell, ($tag['fontsize'] * 1.5) / $this->k, $firstorlast, $tag['value'], false);
					break;
				}
				// Form fields (since 4.8.000 - 2009-09-07)
				case 'form': {
					if (isset($tag['attribute']['action'])) {
						$this->form_action = $tag['attribute']['action'];
					} else {
						$this->form_action = K_PATH_URL.$_SERVER['SCRIPT_NAME'];
					}
					if (isset($tag['attribute']['enctype'])) {
						$this->form_enctype = $tag['attribute']['enctype'];
					} else {
						$this->form_enctype = 'application/x-www-form-urlencoded';
					}
					if (isset($tag['attribute']['method'])) {
						$this->form_mode = $tag['attribute']['method'];
					} else {
						$this->form_mode = 'post';
					}
					break;
				}
				case 'input': {
					if (isset($tag['attribute']['name']) AND !$this->empty_string($tag['attribute']['name'])) {
						$name = $tag['attribute']['name'];
					} else {
						break;
					}
					$prop = array();
					$opt = array();
					if (isset($tag['attribute']['value']) AND !$this->empty_string($tag['attribute']['value'])) {
						$value = $tag['attribute']['value'];
					}
					if (isset($tag['attribute']['maxlength']) AND !$this->empty_string($tag['attribute']['maxlength'])) {
						$opt['maxlen'] = intval($tag['attribute']['value']);
					}
					$h = $this->FontSize * $this->cell_height_ratio;
					if (isset($tag['attribute']['size']) AND !$this->empty_string($tag['attribute']['size'])) {
						$w = intval($tag['attribute']['size']) * $this->GetStringWidth(chr(32)) * 2;
					} else {
						$w = $h;
					}
					if (isset($tag['attribute']['checked']) AND (($tag['attribute']['checked'] == 'checked') OR ($tag['attribute']['checked'] == 'true'))) {
						$checked = true;
					} else {
						$checked = false;
					}
					switch ($tag['attribute']['type']) {
						case 'text': {
							if (isset($value)) {
								$opt['v'] = $value;
							}
							$this->TextField($name, $w, $h, $prop, $opt, '', '', false);
							break;
						}
						case 'password': {
							if (isset($value)) {
								$opt['v'] = $value;
							}
							$prop['password'] = 'true';
							$this->TextField($name, $w, $h, $prop, $opt, '', '', false);
							break;
						}
						case 'checkbox': {
							$this->CheckBox($name, $w, $checked, $prop, $opt, $value, '', '', false);
							break;
						}
						case 'radio': {
							$this->RadioButton($name, $w, $prop, $opt, $value, $checked, '', '', false);
							break;
						}
						case 'submit': {
							$w = $this->GetStringWidth($value) * 1.5;
							$h *= 1.6;
							$prop = array('lineWidth'=>1, 'borderStyle'=>'beveled', 'fillColor'=>array(196, 196, 196), 'strokeColor'=>array(255, 255, 255));
							$action = array();
							$action['S'] = 'SubmitForm';
							$action['F'] = $this->form_action;
							if ($this->form_enctype != 'FDF') {
								$action['Flags'] = array('ExportFormat');
							}
							if ($this->form_mode == 'get') {
								$action['Flags'] = array('GetMethod');
							}
							$this->Button($name, $w, $h, $value, $action, $prop, $opt, '', '', false);
							break;
						}
						case 'reset': {
							$w = $this->GetStringWidth($value) * 1.5;
							$h *= 1.6;
							$prop = array('lineWidth'=>1, 'borderStyle'=>'beveled', 'fillColor'=>array(196, 196, 196), 'strokeColor'=>array(255, 255, 255));
							$this->Button($name, $w, $h, $value, array('S'=>'ResetForm'), $prop, $opt, '', '', false);
							break;
						}
						case 'file': {
							$prop['fileSelect'] = 'true';
							$this->TextField($name, $w, $h, $prop, $opt, '', '', false);
							if (!isset($value)) {
								$value = '*';
							}
							$w = $this->GetStringWidth($value) * 2;
							$h *= 1.2;
							$prop = array('lineWidth'=>1, 'borderStyle'=>'beveled', 'fillColor'=>array(196, 196, 196), 'strokeColor'=>array(255, 255, 255));
							$jsaction = 'var f=this.getField(\''.$name.'\'); f.browseForFileToSubmit();';
							$this->Button('FB_'.$name, $w, $h, $value, $jsaction, $prop, $opt, '', '', false);
							break;
						}
						case 'hidden': {
							if (isset($value)) {
								$opt['v'] = $value;
							}
							$opt['f'] = array('invisible', 'hidden');
							$this->TextField($name, 0, 0, $prop, $opt, '', '', false);
							break;
						}
						case 'image': {
							// THIS TYPE MUST BE FIXED
							if (isset($tag['attribute']['src']) AND !$this->empty_string($tag['attribute']['src'])) {
								$img = $tag['attribute']['src'];
							} else {
								break;
							}
							$value = 'img';
							//$opt['mk'] = array('i'=>$img, 'tp'=>1, 'if'=>array('sw'=>'A', 's'=>'A', 'fb'=>false));
							if (isset($tag['attribute']['onclick']) AND !empty($tag['attribute']['onclick'])) {
								$jsaction = $tag['attribute']['onclick'];
							} else {
								$jsaction = '';
							}
							$this->Button($name, $w, $h, $value, $jsaction, $prop, $opt, '', '', false);
							break;
						}
						case 'button': {
							$w = $this->GetStringWidth($value) * 1.5;
							$h *= 1.6;
							$prop = array('lineWidth'=>1, 'borderStyle'=>'beveled', 'fillColor'=>array(196, 196, 196), 'strokeColor'=>array(255, 255, 255));
							if (isset($tag['attribute']['onclick']) AND !empty($tag['attribute']['onclick'])) {
								$jsaction = $tag['attribute']['onclick'];
							} else {
								$jsaction = '';
							}
							$this->Button($name, $w, $h, $value, $jsaction, $prop, $opt, '', '', false);
							break;
						}
					}
					break;
				}
				case 'textarea': {
					$prop = array();
					$opt = array();
					if (isset($tag['attribute']['name']) AND !$this->empty_string($tag['attribute']['name'])) {
						$name = $tag['attribute']['name'];
					} else {
						break;
					}
					if (isset($tag['attribute']['value']) AND !$this->empty_string($tag['attribute']['value'])) {
						$opt['v'] = $tag['attribute']['value'];
					}
					if (isset($tag['attribute']['cols']) AND !$this->empty_string($tag['attribute']['cols'])) {
						$w = intval($tag['attribute']['cols']) * $this->GetStringWidth(chr(32)) * 2;
					} else {
						$w = 40;
					}
					if (isset($tag['attribute']['rows']) AND !$this->empty_string($tag['attribute']['rows'])) {
						$h = intval($tag['attribute']['rows']) * $this->FontSize * $this->cell_height_ratio;
					} else {
						$h = 10;
					}
					$prop['multiline'] = 'true';
					$this->TextField($name, $w, $h, $prop, $opt, '', '', false);
					break;
				}
				case 'select': {
					$h = $this->FontSize * $this->cell_height_ratio;
					if (isset($tag['attribute']['size']) AND !$this->empty_string($tag['attribute']['size'])) {
						$h *= ($tag['attribute']['size'] + 1);
					}
					$prop = array();
					$opt = array();
					if (isset($tag['attribute']['name']) AND !$this->empty_string($tag['attribute']['name'])) {
						$name = $tag['attribute']['name'];
					} else {
						break;
					}
					$w = 0;
					if (isset($tag['attribute']['opt']) AND !$this->empty_string($tag['attribute']['opt'])) {
						$options = explode ("\r", $tag['attribute']['opt']);
						$values = array();
						foreach ($options as $val) {
							if (strpos($val, "\t") !== false) {
								$opts = explode("\t", $val);
								$values[] = $opts;
								$w = max($w, $this->GetStringWidth($opts[1]));
							} else {
								$values[] = $val;
								$w = max($w, $this->GetStringWidth($val));
							}
						}
					} else {
						break;
					}
					$w *= 2;
					if (isset($tag['attribute']['multiple']) AND ($tag['attribute']['multiple']='multiple')) {
						$prop['multipleSelection'] = 'true';
						$this->ListBox($name, $w, $h, $values, $prop, $opt, '', '', false);
					} else {
						$this->ComboBox($name, $w, $h, $values, $prop, $opt, '', '', false);
					}
					break;
				}
				case 'tcpdf': {
					// NOT HTML: used to call TCPDF methods
					if (isset($tag['attribute']['method'])) {
						$tcpdf_method = $tag['attribute']['method'];
						if (method_exists($this, $tcpdf_method)) {
							if (isset($tag['attribute']['params']) AND (!empty($tag['attribute']['params']))) {
								eval('$params = array('.$this->unhtmlentities($tag['attribute']['params']).');');
								call_user_func_array(array($this, $tcpdf_method), $params);
							} else {
								$this->$tcpdf_method();
							}
							$this->newline = true;
						}
					}
				}
				default: {
					break;
				}
			}
		}
		
		/**
		 * Process closing tags.
		 * @param array $dom html dom array 
		 * @param int $key current element id
		 * @param boolean $cell if true add the default cMargin space to each new line (default false).
		 * @access protected
		 */
		protected function closeHTMLTagHandler(&$dom, $key, $cell=false) {
			$tag = $dom[$key];
			$parent = $dom[($dom[$key]['parent'])];
			$firstorlast = ((!isset($dom[($key + 1)])) OR ((!isset($dom[($key + 2)])) AND ($dom[($key + 1)]['value'] == 'marker')));
			$in_table_head = false;
			//Closing tag
			switch($tag['value']) {
				case 'tr': {
					$table_el = $dom[($dom[$key]['parent'])]['parent'];
					if(!isset($parent['endy'])) {
						$dom[($dom[$key]['parent'])]['endy'] = $this->y;
						$parent['endy'] = $this->y;
					}
					if(!isset($parent['endpage'])) {
						$dom[($dom[$key]['parent'])]['endpage'] = $this->page;
						$parent['endpage'] = $this->page;
					}
					// update row-spanned cells
					if (isset($dom[$table_el]['rowspans'])) {
						foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
							$dom[$table_el]['rowspans'][$k]['rowspan'] -= 1;
							if ($dom[$table_el]['rowspans'][$k]['rowspan'] == 0) {
								if ($dom[$table_el]['rowspans'][$k]['endpage'] == $parent['endpage']) {
									$dom[($dom[$key]['parent'])]['endy'] = max($dom[$table_el]['rowspans'][$k]['endy'], $parent['endy']);
								} elseif ($dom[$table_el]['rowspans'][$k]['endpage'] > $parent['endpage']) {
									$dom[($dom[$key]['parent'])]['endy'] = $dom[$table_el]['rowspans'][$k]['endy'];
									$dom[($dom[$key]['parent'])]['endpage'] = $dom[$table_el]['rowspans'][$k]['endpage'];
								}
							}
						}
						// report new endy and endpage to the rowspanned cells
						foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
							if ($dom[$table_el]['rowspans'][$k]['rowspan'] == 0) {
								$dom[$table_el]['rowspans'][$k]['endpage'] = max($dom[$table_el]['rowspans'][$k]['endpage'], $dom[($dom[$key]['parent'])]['endpage']);
								$dom[($dom[$key]['parent'])]['endpage'] = $dom[$table_el]['rowspans'][$k]['endpage'];
								$dom[$table_el]['rowspans'][$k]['endy'] = max($dom[$table_el]['rowspans'][$k]['endy'], $dom[($dom[$key]['parent'])]['endy']);
								$dom[($dom[$key]['parent'])]['endy'] = $dom[$table_el]['rowspans'][$k]['endy'];
							}
						}
						// update remaining rowspanned cells
						foreach ($dom[$table_el]['rowspans'] as $k => $trwsp) {
							if ($dom[$table_el]['rowspans'][$k]['rowspan'] == 0) {
								$dom[$table_el]['rowspans'][$k]['endpage'] = $dom[($dom[$key]['parent'])]['endpage'];
								$dom[$table_el]['rowspans'][$k]['endy'] = $dom[($dom[$key]['parent'])]['endy'];
							}
						}
					}
					$this->setPage($dom[($dom[$key]['parent'])]['endpage']);
					$this->y = $dom[($dom[$key]['parent'])]['endy'];
					if (isset($dom[$table_el]['attribute']['cellspacing'])) {
						$cellspacing = $this->getHTMLUnitToUnits($dom[$table_el]['attribute']['cellspacing'], 1, 'px');
						$this->y += $cellspacing;
					}				
					$this->Ln(0, $cell);
					$this->x = $parent['startx'];
					// account for booklet mode
					if ($this->page > $parent['startpage']) {
						if (($this->rtl) AND ($this->pagedim[$this->page]['orm'] != $this->pagedim[$parent['startpage']]['orm'])) {
							$this->x += ($this->pagedim[$this->page]['orm'] - $this->pagedim[$parent['startpage']]['orm']);
						} elseif ((!$this->rtl) AND ($this->pagedim[$this->page]['olm'] != $this->pagedim[$parent['startpage']]['olm'])) {
							$this->x += ($this->pagedim[$this->page]['olm'] - $this->pagedim[$parent['startpage']]['olm']);
						}
					}
					break;
				}
				case 'tablehead':
					// closing tag used for the thead part
					$in_table_head = true;
				case 'table': {
					// draw borders
					$table_el = $parent;
					if ((isset($table_el['attribute']['border']) AND ($table_el['attribute']['border'] > 0)) 
						OR (isset($table_el['style']['border']) AND ($table_el['style']['border'] > 0))) {
							$border = 1;
					} else {
						$border = 0;
					}
					// fix bottom line alignment of last line before page break
					foreach ($dom[($dom[$key]['parent'])]['trids'] as $j => $trkey) {
						// update row-spanned cells
						if (isset($dom[($dom[$key]['parent'])]['rowspans'])) {
							foreach ($dom[($dom[$key]['parent'])]['rowspans'] as $k => $trwsp) {
								if ($trwsp['trid'] == $trkey) {
									$dom[($dom[$key]['parent'])]['rowspans'][$k]['mrowspan'] -= 1;
								}
								if (isset($prevtrkey) AND ($trwsp['trid'] == $prevtrkey) AND ($trwsp['mrowspan'] >= 0)) {
									$dom[($dom[$key]['parent'])]['rowspans'][$k]['trid'] = $trkey;
								}
							}
						}
						if (isset($prevtrkey) AND ($dom[$trkey]['startpage'] > $dom[$prevtrkey]['endpage'])) {
							$pgendy = $this->pagedim[$dom[$prevtrkey]['endpage']]['hk'] - $this->pagedim[$dom[$prevtrkey]['endpage']]['bm'];
							$dom[$prevtrkey]['endy'] = $pgendy;
							// update row-spanned cells
							if (isset($dom[($dom[$key]['parent'])]['rowspans'])) {
								foreach ($dom[($dom[$key]['parent'])]['rowspans'] as $k => $trwsp) {
									if (($trwsp['trid'] == $trkey) AND ($trwsp['mrowspan'] == 1) AND ($trwsp['endpage'] == $dom[$prevtrkey]['endpage'])) {
										$dom[($dom[$key]['parent'])]['rowspans'][$k]['endy'] = $pgendy;
										$dom[($dom[$key]['parent'])]['rowspans'][$k]['mrowspan'] = -1;
									}
								}
							}
						}
						$prevtrkey = $trkey;
						$table_el = $dom[($dom[$key]['parent'])];
					}
					// for each row
					foreach ($table_el['trids'] as $j => $trkey) {
						$parent = $dom[$trkey];
						// for each cell on the row
						foreach ($parent['cellpos'] as $k => $cellpos) {
							if (isset($cellpos['rowspanid']) AND ($cellpos['rowspanid'] >= 0)) {
								$cellpos['startx'] = $table_el['rowspans'][($cellpos['rowspanid'])]['startx'];
								$cellpos['endx'] = $table_el['rowspans'][($cellpos['rowspanid'])]['endx'];
								$endy = $table_el['rowspans'][($cellpos['rowspanid'])]['endy'];
								$startpage = $table_el['rowspans'][($cellpos['rowspanid'])]['startpage'];
								$endpage = $table_el['rowspans'][($cellpos['rowspanid'])]['endpage'];
							} else {
								$endy = $parent['endy'];
								$startpage = $parent['startpage'];
								$endpage = $parent['endpage'];
							}
							if ($endpage > $startpage) {
								// design borders around HTML cells.
								for ($page=$startpage; $page <= $endpage; ++$page) {
									$this->setPage($page);
									if ($page == $startpage) {
										$this->y = $parent['starty']; // put cursor at the beginning of row on the first page
										$ch = $this->getPageHeight() - $parent['starty'] - $this->getBreakMargin();
										$cborder = $this->getBorderMode($border, $position='start');
									} elseif ($page == $endpage) {
										$this->y = $this->tMargin; // put cursor at the beginning of last page
										$ch = $endy - $this->tMargin;
										$cborder = $this->getBorderMode($border, $position='end');
									} else {
										$this->y = $this->tMargin; // put cursor at the beginning of the current page
										$ch = $this->getPageHeight() - $this->tMargin - $this->getBreakMargin();
										$cborder = $this->getBorderMode($border, $position='middle');
									}
									if (isset($cellpos['bgcolor']) AND ($cellpos['bgcolor']) !== false) {
										$this->SetFillColorArray($cellpos['bgcolor']);
										$fill = true;
									} else {
										$fill = false;
									}
									$cw = abs($cellpos['endx'] - $cellpos['startx']);
									$this->x = $cellpos['startx'];
									// account for margin changes
									if ($page > $startpage) {
										if (($this->rtl) AND ($this->pagedim[$page]['orm'] != $this->pagedim[$startpage]['orm'])) {
											$this->x -= ($this->pagedim[$page]['orm'] - $this->pagedim[$startpage]['orm']);
										} elseif ((!$this->rtl) AND ($this->pagedim[$page]['lm'] != $this->pagedim[$startpage]['olm'])) {
											$this->x += ($this->pagedim[$page]['olm'] - $this->pagedim[$startpage]['olm']);
										}
									}
									// design a cell around the text
									$ccode = $this->FillColor."\n".$this->getCellCode($cw, $ch, '', $cborder, 1, '', $fill, '', 0, true);
									if ($cborder OR $fill) {
										$pagebuff = $this->getPageBuffer($this->page);
										$pstart = substr($pagebuff, 0, $this->intmrk[$this->page]);
										$pend = substr($pagebuff, $this->intmrk[$this->page]);
										$this->setPageBuffer($this->page, $pstart.$ccode."\n".$pend);
										$this->intmrk[$this->page] += strlen($ccode."\n");
									}
								}
							} else {
								$this->setPage($startpage);
								if (isset($cellpos['bgcolor']) AND ($cellpos['bgcolor']) !== false) {
									$this->SetFillColorArray($cellpos['bgcolor']);
									$fill = true;
								} else {
									$fill = false;
								}
								$this->x = $cellpos['startx'];
								$this->y = $parent['starty'];
								$cw = abs($cellpos['endx'] - $cellpos['startx']);
								$ch = $endy - $parent['starty'];
								// design a cell around the text
								$ccode = $this->FillColor."\n".$this->getCellCode($cw, $ch, '', $border, 1, '', $fill, '', 0, true);
								if ($border OR $fill) {
									if (end($this->transfmrk[$this->page]) !== false) {
										$pagemarkkey = key($this->transfmrk[$this->page]);
										$pagemark = &$this->transfmrk[$this->page][$pagemarkkey];
									} elseif ($this->InFooter) {
										$pagemark = &$this->footerpos[$this->page];
									} else {
										$pagemark = &$this->intmrk[$this->page];
									}
									$pagebuff = $this->getPageBuffer($this->page);
									$pstart = substr($pagebuff, 0, $pagemark);
									$pend = substr($pagebuff, $pagemark);
									$this->setPageBuffer($this->page, $pstart.$ccode."\n".$pend);
									$pagemark += strlen($ccode."\n");
								}					
							}
						}					
						if (isset($table_el['attribute']['cellspacing'])) {
							$cellspacing = $this->getHTMLUnitToUnits($table_el['attribute']['cellspacing'], 1, 'px');
							$this->y += $cellspacing;
						}				
						$this->Ln(0, $cell);
						$this->x = $parent['startx'];
						if ($endpage > $startpage) {
							if (($this->rtl) AND ($this->pagedim[$endpage]['orm'] != $this->pagedim[$startpage]['orm'])) {
								$this->x += ($this->pagedim[$endpage]['orm'] - $this->pagedim[$startpage]['orm']);
							} elseif ((!$this->rtl) AND ($this->pagedim[$endpage]['olm'] != $this->pagedim[$startpage]['olm'])) {
								$this->x += ($this->pagedim[$endpage]['olm'] - $this->pagedim[$startpage]['olm']);
							}
						}
					}
					if (!$in_table_head) {
						// we are not inside a thead section
						if (isset($parent['cellpadding'])) {
							$this->cMargin = $this->oldcMargin;
						}
						$this->lasth = $this->FontSize * $this->cell_height_ratio;
						if (isset($this->theadMargins['top'])) {
							// restore top margin
							$this->tMargin = $this->theadMargins['top'];
							$this->pagedim[$this->page]['tm'] = $this->tMargin;
						}
						// reset table header
						$this->thead = '';
						$this->theadMargins = array();
					}
					break;
				}
				case 'a': {
					$this->HREF = '';
					break;
				}
				case 'sup': {
					$this->SetXY($this->GetX(), $this->GetY() + ((0.7 * $parent['fontsize']) / $this->k));
					break;
				}
				case 'sub': {
					$this->SetXY($this->GetX(), $this->GetY() - ((0.3 * $parent['fontsize'])/$this->k));
					break;
				}
				case 'div': {
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], true);
					break;
				}
				case 'blockquote': {
					if ($this->rtl) {
						$this->rMargin -= $this->listindent;
					} else {
						$this->lMargin -= $this->listindent;
					}
					$this->addHTMLVertSpace(2, $cell, '', $firstorlast, $tag['value'], true);
					break;
				}
				case 'p': {
					$this->addHTMLVertSpace(2, $cell, '', $firstorlast, $tag['value'], true);
					break;
				}
				case 'pre': {
					$this->addHTMLVertSpace(1, $cell, '', $firstorlast, $tag['value'], true);
					$this->premode = false;
					break;
				}
				case 'dl': {
					--$this->listnum;
					if ($this->listnum <= 0) {
						$this->listnum = 0;
						$this->addHTMLVertSpace(2, $cell, '', $firstorlast, $tag['value'], true);
					}
					break;
				}
				case 'dt': {
					$this->lispacer = '';
					$this->addHTMLVertSpace(0, $cell, '', $firstorlast, $tag['value'], true);
					break;
				}
				case 'dd': {
					$this->lispacer = '';
					if ($this->rtl) {
						$this->rMargin -= $this->listindent;
					} else {
						$this->lMargin -= $this->listindent;
					}
					$this->addHTMLVertSpace(0, $cell, '', $firstorlast, $tag['value'], true);
					break;
				}
				case 'ul':
				case 'ol': {
					--$this->listnum;
					$this->lispacer = '';
					if ($this->rtl) {
						$this->rMargin -= $this->listindent;
					} else {
						$this->lMargin -= $this->listindent;
					}
					if ($this->listnum <= 0) {
						$this->listnum = 0;
						$this->addHTMLVertSpace(2, $cell, '', $firstorlast, $tag['value'], true);
					}
					$this->lasth = $this->FontSize * $this->cell_height_ratio;
					break;
				}
				case 'li': {
					$this->lispacer = '';
					$this->addHTMLVertSpace(0, $cell, '', $firstorlast, $tag['value'], true);
					break;
				}
				case 'h1': 
				case 'h2': 
				case 'h3': 
				case 'h4': 
				case 'h5': 
				case 'h6': {
					$this->addHTMLVertSpace(1, $cell, ($parent['fontsize'] * 1.5) / $this->k, $firstorlast, $tag['value'], true);
					break;
				}
				// Form fields (since 4.8.000 - 2009-09-07)
				case 'form': {
					$this->form_action = '';
					$this->form_enctype = 'application/x-www-form-urlencoded';
					break;
				}
				default : {
					break;
				}
			}
			$this->tmprtl = false;
		}
		
		/**
		 * Add vertical spaces if needed.
		 * @param int $n number of spaces to add
		 * @param boolean $cell if true add the default cMargin space to each new line (default false).
		 * @param string $h The height of the break. By default, the value equals the height of the last printed cell.
		 * @param boolean $firstorlast if true do not print additional empty lines.
		 * @param string $tag HTML tag to which this space will be applied
		 * @param boolean $closing true if this space will be applied to a closing tag, false otherwise
		 * @access protected
		 */
		protected function addHTMLVertSpace($n, $cell=false, $h='', $firstorlast=false, $tag='', $closing=false) {
			if ($firstorlast) {
				$this->Ln(0, $cell);
				$this->htmlvspace = 0;
				return;
			}
			if (isset($this->tagvspaces[$tag][intval($closing)]['n'])) {
				$n = $this->tagvspaces[$tag][intval($closing)]['n'];
			}
			if (isset($this->tagvspaces[$tag][intval($closing)]['h'])) {
				$h = $this->tagvspaces[$tag][intval($closing)]['h'];
			}
			if (is_string($h)) {
				$vsize = $n * $this->lasth;
			} else {
				$vsize = $n * $h;
			}
			if ($vsize > $this->htmlvspace) {
				$this->Ln(($vsize - $this->htmlvspace), $cell);
				$this->htmlvspace = $vsize;
			}
		}
		
		/**
		 * Set the default bullet to be used as LI bullet symbol
		 * @param string $symbol character or string to be used (legal values are: '' = automatic, '!' = auto bullet, '#' = auto numbering, 'disc', 'disc', 'circle', 'square', '1', 'decimal', 'decimal-leading-zero', 'i', 'lower-roman', 'I', 'upper-roman', 'a', 'lower-alpha', 'lower-latin', 'A', 'upper-alpha', 'upper-latin', 'lower-greek')
		 * @access public
		 * @since 4.0.028 (2008-09-26)
		 */
		public function setLIsymbol($symbol='!') {
			$symbol = strtolower($symbol);
			switch ($symbol) {
				case '!' :
				case '#' :
				case 'disc' :
				case 'disc' :
				case 'circle' :
				case 'square' :
				case '1':
				case 'decimal':
				case 'decimal-leading-zero':
				case 'i':
				case 'lower-roman':
				case 'I':
				case 'upper-roman':
				case 'a':
				case 'lower-alpha':
				case 'lower-latin':
				case 'A':
				case 'upper-alpha':
				case 'upper-latin':
				case 'lower-greek': {
					$this->lisymbol = $symbol;
					break;
				}
				default : {
					$this->lisymbol = '';
				}
			}
		}
		
		/**
		* Set the booklet mode for double-sided pages.
		* @param boolean $booklet true set the booklet mode on, fals eotherwise.
		* @param float $inner Inner page margin.
		* @param float $outer Outer page margin.
		* @access public
		* @since 4.2.000 (2008-10-29)
		*/
		public function SetBooklet($booklet=true, $inner=-1, $outer=-1) {
			$this->booklet = $booklet;
			if ($inner >= 0) {
				$this->lMargin = $inner;
			}
			if ($outer >= 0) {
				$this->rMargin = $outer;
			}
		}
		
		/**
		* Swap the left and right margins.
		* @param boolean $reverse if true swap left and right margins.
		* @access protected
		* @since 4.2.000 (2008-10-29)
		*/
		protected function swapMargins($reverse=true) {
			if ($reverse) {
				// swap left and right margins
				$mtemp = $this->original_lMargin;
				$this->original_lMargin = $this->original_rMargin;
				$this->original_rMargin = $mtemp;
				$deltam = $this->original_lMargin - $this->original_rMargin;
				$this->lMargin += $deltam;
				$this->rMargin -= $deltam;
			}
		}

		/**
		* Set the vertical spaces for HTML tags.
		* The array must have the following structure (example):
		* $tagvs = array('h1' => array(0 => array('h' => '', 'n' => 2), 1 => array('h' => 1.3, 'n' => 1)));
		* The first array level contains the tag names,
		* the second level contains 0 for opening tags or 1 for closing tags,
		* the third level contains the vertical space unit (h) and the number spaces to add (n).
		* If the h parameter is not specified, default values are used.
		* @param array $tagvs array of tags and relative vertical spaces.
		* @access public
		* @since 4.2.001 (2008-10-30)
		*/
		public function setHtmlVSpace($tagvs) {
			$this->tagvspaces = $tagvs;
		}

        /**
		* Set custom width for list indentation.
		* @param float $width width of the indentation. Use negative value to disable it.
		* @access public
		* @since 4.2.007 (2008-11-12)
		*/
		public function setListIndentWidth($width) {
			return $this->customlistindent = floatval($width);
        }

        /**
		* Set the top/bottom cell sides to be open or closed when the cell cross the page.
		* @param boolean $isopen if true keeps the top/bottom border open for the cell sides that cross the page.
		* @access public
		* @since 4.2.010 (2008-11-14)
		*/
		public function setOpenCell($isopen) {
			$this->opencell = $isopen;
        }

        /**
		* Set the color and font style for HTML links.
		* @param array $color RGB array of colors
		* @param string $fontstyle additional font styles to add
		* @access public
		* @since 4.4.003 (2008-12-09)
		*/
		public function setHtmlLinksStyle($color=array(0,0,255), $fontstyle='U') {
			$this->htmlLinkColorArray = $color;
			$this->htmlLinkFontStyle = $fontstyle;
        }

        /**
		* convert html string containing value and unit of measure to user's units or points.
		* @param string $htmlval string containing values and unit
		* @param string $refsize reference value in points
		* @param string $defaultunit default unit (can be one of the following: %, em, ex, px, in, mm, pc, pt).
		* @param boolean $point if true returns points, otherwise returns value in user's units
		* @return float value in user's unit or point if $points=true
		* @access public
		* @since 4.4.004 (2008-12-10)
		*/
        public function getHTMLUnitToUnits($htmlval, $refsize=1, $defaultunit='px', $points=false) {
			$supportedunits = array('%', 'em', 'ex', 'px', 'in', 'cm', 'mm', 'pc', 'pt');
			$retval = 0;
			$value = 0;
			$unit = 'px';
			$k = $this->k;
			if ($points) {
				$k = 1;
			}
			if (in_array($defaultunit, $supportedunits)) {
				$unit = $defaultunit;
			}
			if (is_numeric($htmlval)) {
				$value = floatval($htmlval);
			} elseif (preg_match('/([0-9\.]+)/', $htmlval, $mnum)) {
				$value = floatval($mnum[1]);
				if (preg_match('/([a-z%]+)/', $htmlval, $munit)) {
					if (in_array($munit[1], $supportedunits)) {
						$unit = $munit[1];
					}
				}
			}
			switch ($unit) {
				// percentage
				case '%': {
					$retval = (($value * $refsize) / 100);
					break;
				}
				// relative-size
				case 'em': {
					$retval = ($value * $refsize);
					break;
				}
				case 'ex': {
					$retval = $value * ($refsize / 2);
					break;
				}
				// absolute-size
				case 'in': {
					$retval = ($value * $this->dpi) / $k;
					break;
				}
				case 'cm': {
					$retval = ($value / 2.54 * $this->dpi) / $k;
					break;
				}
				case 'mm': {
					$retval = ($value / 25.4 * $this->dpi) / $k;
					break;
				}
				case 'pc': {
					// one pica is 12 points
					$retval = ($value * 12) / $k;
					break;
				}
				case 'pt': {
					$retval = $value / $k;
					break;
				}
				case 'px': {
					$retval = $this->pixelsToUnits($value);
					break;
				}
			}
			return $retval;
		}

		/**
		* Returns the Roman representation of an integer number
		* @param int number to convert
		* @return string roman representation of the specified number
		* @access public
		* @since 4.4.004 (2008-12-10)
		*/
		public function intToRoman($number) {
			$roman = '';
			while ($number >= 1000) {
				$roman .= 'M';
				$number -= 1000;
			}
			while ($number >= 900) {
				$roman .= 'CM';
				$number -= 900;
			}
			while ($number >= 500) {
				$roman .= 'D';
				$number -= 500;
			}
			while ($number >= 400) {
				$roman .= 'CD';
				$number -= 400;
			}
			while ($number >= 100) {
				$roman .= 'C';
				$number -= 100;
			}
			while ($number >= 90) {
			$roman .= 'XC';
			$number -= 90;
			}
			while ($number >= 50) {
				$roman .= 'L';
				$number -= 50;
			}
			while ($number >= 40) {
				$roman .= 'XL';
				$number -= 40;
			}
			while ($number >= 10) {
			$roman .= 'X';
			$number -= 10;
			}
			while ($number >= 9) {
				$roman .= 'IX';
				$number -= 9;
			}
			while ($number >= 5) {
				$roman .= 'V';
				$number -= 5;
			}
			while ($number >= 4) {
			$roman .= 'IV';
			$number -= 4;
			}
			while ($number >= 1) {
				$roman .= 'I';
				--$number;
			}
			return $roman;
		}

		/**
		* Output an HTML list bullet or ordered item symbol
		* @param int $listdepth list nesting level
		* @param string $listtype type of list
		* @param float $size current font size
		* @access protected
		* @since 4.4.004 (2008-12-10)
		*/
		protected function putHtmlListBullet($listdepth, $listtype='', $size=10) {
		    $size /= $this->k;
		    $fill = '';
		    $color = $this->fgcolor;
		    $width = 0;
		    $textitem = '';
		    $tmpx = $this->x;		
			$lspace = $this->GetStringWidth('  ');
			if ($listtype == '!') {
				// set default list type for unordered list
				$deftypes = array('disc', 'circle', 'square');
				$listtype = $deftypes[($listdepth - 1) % 3];
			} elseif ($listtype == '#') {
				// set default list type for ordered list
				$listtype = 'decimal';
			}
        	switch ($listtype) {
        		// unordered types
				case 'none': {
					break;
				}
				case 'disc': {
					$fill = 'F';
				}
				case 'circle': {
					$fill .= 'D';
					$r = $size / 6;
					$lspace += (2 * $r);
					if ($this->rtl) {
						$this->x = $this->w - $this->x - $lspace;
					} else {
						$this->x -= $lspace;
					}
					$this->Circle(($this->x + $r), ($this->y + ($this->lasth / 2)), $r, 0, 360, $fill, array('color'=>$color), $color, 8);
					break;
				}
				case 'square': {
					$l = $size / 3;
					$lspace += $l;
					if ($this->rtl) {
						$this->x = $this->w - $this->x - $lspace;
					} else {
						$this->x -= $lspace;
					}
					$this->Rect($this->x, ($this->y + (($this->lasth - $l)/ 2)), $l, $l, 'F', array(), $color);
					break;
				}
				// ordered types

				// $this->listcount[$this->listnum];
				// $textitem
				case '1':
				case 'decimal': {
					$textitem = $this->listcount[$this->listnum];
					break;
				}
				case 'decimal-leading-zero': {
					$textitem = sprintf("%02d", $this->listcount[$this->listnum]);
					break;
				}
				case 'i':
				case 'lower-roman': {
					$textitem = strtolower($this->intToRoman($this->listcount[$this->listnum]));
					break;
				}
				case 'I':
				case 'upper-roman': {
					$textitem = $this->intToRoman($this->listcount[$this->listnum]);
					break;
				}
				case 'a':
				case 'lower-alpha':
				case 'lower-latin': {
					$textitem = chr(97 + $this->listcount[$this->listnum] - 1);
					break;
				}
				case 'A':
				case 'upper-alpha':
				case 'upper-latin': {
					$textitem = chr(65 + $this->listcount[$this->listnum] - 1);
					break;
				}
				case 'lower-greek': {
					$textitem = $this->unichr(945 + $this->listcount[$this->listnum] - 1);
					break;
				}
				/*
				// Types to be implemented (special handling)
				case 'hebrew': {
					break;
				}
				case 'armenian': {
					break;
				}
				case 'georgian': {
					break;
				}
				case 'cjk-ideographic': {
					break;
				}
				case 'hiragana': {
					break;
				}
				case 'katakana': {
					break;
				}
				case 'hiragana-iroha': {
					break;
				}
				case 'katakana-iroha': {
					break;
				}
				*/
				default: {
					$textitem = $this->listcount[$this->listnum];
				}
			}
			if (!$this->empty_string($textitem)) {
				// print ordered item
				if ($this->rtl) {
					$textitem = '.'.$textitem;
				} else {
					$textitem = $textitem.'.';
				}
				$lspace += $this->GetStringWidth($textitem);
				if ($this->rtl) {
					$this->x += $lspace;
				} else {
					$this->x -= $lspace;
				}
				$this->Write($this->lasth, $textitem, '', false, '', false, 0, false);
			}
			$this->x = $tmpx;
			$this->lispacer = '';
		}

        /**
		* Returns current graphic variables as array.
		* @return array graphic variables
		* @access protected
		* @since 4.2.010 (2008-11-14)
		*/
		protected function getGraphicVars() {
			$grapvars = array(
				'FontFamily' => $this->FontFamily,
				'FontStyle' => $this->FontStyle,
				'FontSizePt' => $this->FontSizePt,
				'rMargin' => $this->rMargin,
				'lMargin' => $this->lMargin,
				'cMargin' => $this->cMargin,
				'LineWidth' => $this->LineWidth,
				'linestyleWidth' => $this->linestyleWidth,
				'linestyleCap' => $this->linestyleCap,
				'linestyleJoin' => $this->linestyleJoin,
				'linestyleDash' => $this->linestyleDash,
				'DrawColor' => $this->DrawColor,
				'FillColor' => $this->FillColor,
				'TextColor' => $this->TextColor,
				'ColorFlag' => $this->ColorFlag,
				'bgcolor' => $this->bgcolor,
				'fgcolor' => $this->fgcolor,
				'htmlvspace' => $this->htmlvspace,
				'lasth' => $this->lasth
				);
			return $grapvars;
		}

        /**
		* Set graphic variables.
		* @param $gvars array graphic variables
		* @access protected
		* @since 4.2.010 (2008-11-14)
		*/
		protected function setGraphicVars($gvars) {
			$this->FontFamily = $gvars['FontFamily'];
			$this->FontStyle = $gvars['FontStyle'];
			$this->FontSizePt = $gvars['FontSizePt'];
			$this->rMargin = $gvars['rMargin'];
			$this->lMargin = $gvars['lMargin'];
			$this->cMargin = $gvars['cMargin'];
			$this->LineWidth = $gvars['LineWidth'];
			$this->linestyleWidth = $gvars['linestyleWidth'];
			$this->linestyleCap = $gvars['linestyleCap'];
			$this->linestyleJoin = $gvars['linestyleJoin'];
			$this->linestyleDash = $gvars['linestyleDash'];
			$this->DrawColor = $gvars['DrawColor'];
			$this->FillColor = $gvars['FillColor'];
			$this->TextColor = $gvars['TextColor'];
			$this->ColorFlag = $gvars['ColorFlag'];
			$this->bgcolor = $gvars['bgcolor'];
			$this->fgcolor = $gvars['fgcolor'];
			$this->htmlvspace = $gvars['htmlvspace'];
			//$this->lasth = $gvars['lasth'];
			$this->_out(''.$this->linestyleWidth.' '.$this->linestyleCap.' '.$this->linestyleJoin.' '.$this->linestyleDash.' '.$this->DrawColor.' '.$this->FillColor.'');
			if (!$this->empty_string($this->FontFamily)) {
				$this->SetFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
			}
		}

		/**
		* Returns a temporary filename for caching object on filesystem.
		* @param string $prefix prefix to add to filename
		* return string filename.
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function getObjFilename($name) {
			return tempnam(K_PATH_CACHE, $name.'_');
		}

        /**
		* Writes data to a temporary file on filesystem.
		* @param string $file file name
		* @param mixed $data data to write on file
		* @param boolean $append if true append data, false replace.
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function writeDiskCache($filename, $data, $append=false) {
			if ($append) {
				$fmode = 'ab+';
			} else {
				$fmode = 'wb+';
			}
			$f = @fopen($filename, $fmode);
			if (!$f) {
				$this->Error('Unable to write cache file: '.$filename);
			} else {
				fwrite($f, $data);
				fclose($f);
			}
			// update file lenght (needed for transactions)
			if (!isset($this->cache_file_lenght['_'.$filename])) {
				$this->cache_file_lenght['_'.$filename] = strlen($data);
			} else {
				$this->cache_file_lenght['_'.$filename] += strlen($data);
			}
		}

        /**
		* Read data from a temporary file on filesystem.
		* @param string $file file name
		* @return mixed retrieved data
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function readDiskCache($filename) {
			return file_get_contents($filename);
		}

		/**
		* Set buffer content (always append data).
		* @param string $data data
		* @access protected
		* @since 4.5.000 (2009-01-02)
		*/
		protected function setBuffer($data) {
			$this->bufferlen += strlen($data);
			if ($this->diskcache) {
				if (!isset($this->buffer) OR $this->empty_string($this->buffer)) {
					$this->buffer = $this->getObjFilename('buffer');
				}
				$this->writeDiskCache($this->buffer, $data, true);
			} else {
				$this->buffer .= $data;
			}
		}

        /**
		* Get buffer content.
		* @return string buffer content
		* @access protected
		* @since 4.5.000 (2009-01-02)
		*/
		protected function getBuffer() {
			if ($this->diskcache) {
				return $this->readDiskCache($this->buffer);
			} else {
				return $this->buffer;
			}
		}

        /**
		* Set page buffer content.
		* @param int $page page number
		* @param string $data page data
		* @param boolean $append if true append data, false replace.
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function setPageBuffer($page, $data, $append=false) {
			if ($this->diskcache) {
				if (!isset($this->pages[$page])) {
					$this->pages[$page] = $this->getObjFilename('page'.$page);
				}
				$this->writeDiskCache($this->pages[$page], $data, $append);
			} else {
				if ($append) {
					$this->pages[$page] .= $data;
				} else {
					$this->pages[$page] = $data;
				}
			}
			if ($append AND isset($this->pagelen[$page])) {
				$this->pagelen[$page] += strlen($data);
			} else {
				$this->pagelen[$page] = strlen($data);
			}
		}

        /**
		* Get page buffer content.
		* @param int $page page number
		* @return string page buffer content or false in case of error
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function getPageBuffer($page) {
			if ($this->diskcache) {
				return $this->readDiskCache($this->pages[$page]);
			} elseif (isset($this->pages[$page])) {
				return $this->pages[$page];
			}
			return false;
		}

        /**
		* Set image buffer content.
		* @param string $image image key
		* @param array $data image data
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function setImageBuffer($image, $data) {
			if ($this->diskcache) {
				if (!isset($this->images[$image])) {
					$this->images[$image] = $this->getObjFilename('image'.$image);
				}
				$this->writeDiskCache($this->images[$image], serialize($data));
			} else {
				$this->images[$image] = $data;
			}
			if (!in_array($image, $this->imagekeys)) {
				$this->imagekeys[] = $image;
			}
			++$this->numimages;
		}

        /**
		* Set image buffer content.
		* @param string $image image key
		* @param string $key image sub-key
		* @param array $data image data
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function setImageSubBuffer($image, $key, $data) {
			if (!isset($this->images[$image])) {
				$this->setImageBuffer($image, array());
			}
			if ($this->diskcache) {
				$tmpimg = $this->getImageBuffer($image);
				$tmpimg[$key] = $data;
				$this->writeDiskCache($this->images[$image], serialize($tmpimg));
			} else {
				$this->images[$image][$key] = $data;
			}
		}

        /**
		* Get image buffer content.
		* @param string $image image key
		* @return string image buffer content or false in case of error
		* @access protected
		* @since 4.5.000 (2008-12-31)
		*/
		protected function getImageBuffer($image) {
			if ($this->diskcache AND isset($this->images[$image])) {
				return unserialize($this->readDiskCache($this->images[$image]));
			} elseif (isset($this->images[$image])) {
				return $this->images[$image];
			}
			return false;
		}

		/**
		* Set font buffer content.
		* @param string $font font key
		* @param array $data font data
		* @access protected
		* @since 4.5.000 (2009-01-02)
		*/
		protected function setFontBuffer($font, $data) {
			if ($this->diskcache) {
				if (!isset($this->fonts[$font])) {
					$this->fonts[$font] = $this->getObjFilename('font');
				}
				$this->writeDiskCache($this->fonts[$font], serialize($data));
			} else {
				$this->fonts[$font] = $data;
			}
			if (!in_array($font, $this->fontkeys)) {
				$this->fontkeys[] = $font;
			}
		}

        /**
		* Set font buffer content.
		* @param string $font font key
		* @param string $key font sub-key
		* @param array $data font data
		* @access protected
		* @since 4.5.000 (2009-01-02)
		*/
		protected function setFontSubBuffer($font, $key, $data) {
			if (!isset($this->fonts[$font])) {
				$this->setFontBuffer($font, array());
			}
			if ($this->diskcache) {
				$tmpfont = $this->getFontBuffer($font);
				$tmpfont[$key] = $data;
				$this->writeDiskCache($this->fonts[$font], serialize($tmpfont));
			} else {
				$this->fonts[$font][$key] = $data;
			}
		}

        /**
		* Get font buffer content.
		* @param string $font font key
		* @return string font buffer content or false in case of error
		* @access protected
		* @since 4.5.000 (2009-01-02)
		*/
		protected function getFontBuffer($font) {
			if ($this->diskcache AND isset($this->fonts[$font])) {
				return unserialize($this->readDiskCache($this->fonts[$font]));
			} elseif (isset($this->fonts[$font])) {
				return $this->fonts[$font];
			}
			return false;
		}

        /**
		* Move a page to a previous position.
		* @param int $frompage number of the source page
		* @param int $topage number of the destination page (must be less than $frompage)
		* @return true in case of success, false in case of error.
		* @access public
		* @since 4.5.000 (2009-01-02)
		*/
		public function movePage($frompage, $topage) {
			if (($frompage > $this->numpages) OR ($frompage <= $topage)) {
				return false;
			}
			if ($frompage == $this->page) {
				// close the page before moving it
				$this->endPage();
			}
			// move all page-related states
			$tmppage = $this->pages[$frompage];
			$tmppagedim = $this->pagedim[$frompage];
			$tmppagelen = $this->pagelen[$frompage];
			$tmpintmrk = $this->intmrk[$frompage];
			if (isset($this->footerpos[$frompage])) {
				$tmpfooterpos = $this->footerpos[$frompage];
			}
			if (isset($this->footerlen[$frompage])) {
				$tmpfooterlen = $this->footerlen[$frompage];
			}
			if (isset($this->transfmrk[$frompage])) {
				$tmptransfmrk = $this->transfmrk[$frompage];
			}
			if (isset($this->PageAnnots[$frompage])) {
				$tmpannots = $this->PageAnnots[$frompage];
			}
			if (isset($this->newpagegroup[$frompage])) {
				$tmpnewpagegroup = $this->newpagegroup[$frompage];
			}
			for ($i = $frompage; $i > $topage; --$i) {
				$j = $i - 1;
				// shift pages down
				$this->pages[$i] = $this->pages[$j];
				$this->pagedim[$i] = $this->pagedim[$j];
				$this->pagelen[$i] = $this->pagelen[$j];
				$this->intmrk[$i] = $this->intmrk[$j];
				if (isset($this->footerpos[$j])) {
					$this->footerpos[$i] = $this->footerpos[$j];
				} elseif (isset($this->footerpos[$i])) {
					unset($this->footerpos[$i]);
				}
				if (isset($this->footerlen[$j])) {
					$this->footerlen[$i] = $this->footerlen[$j];
				} elseif (isset($this->footerlen[$i])) {
					unset($this->footerlen[$i]);
				}
				if (isset($this->transfmrk[$j])) {
					$this->transfmrk[$i] = $this->transfmrk[$j];
				} elseif (isset($this->transfmrk[$i])) {
					unset($this->transfmrk[$i]);
				}
				if (isset($this->PageAnnots[$j])) {
					$this->PageAnnots[$i] = $this->PageAnnots[$j];
				} elseif (isset($this->PageAnnots[$i])) {
					unset($this->PageAnnots[$i]);
				}
				if (isset($this->newpagegroup[$j])) {
					$this->newpagegroup[$i] = $this->newpagegroup[$j];
				} elseif (isset($this->newpagegroup[$i])) {
					unset($this->newpagegroup[$i]);
				}
			}
			$this->pages[$topage] = $tmppage;
			$this->pagedim[$topage] = $tmppagedim;
			$this->pagelen[$topage] = $tmppagelen;
			$this->intmrk[$topage] = $tmpintmrk;
			if (isset($tmpfooterpos)) {
				$this->footerpos[$topage] = $tmpfooterpos;
			} elseif (isset($this->footerpos[$topage])) {
				unset($this->footerpos[$topage]);
			}
			if (isset($tmpfooterlen)) {
				$this->footerlen[$topage] = $tmpfooterlen;
			} elseif (isset($this->footerlen[$topage])) {
				unset($this->footerlen[$topage]);
			}
			if (isset($tmptransfmrk)) {
				$this->transfmrk[$topage] = $tmptransfmrk;
			} elseif (isset($this->transfmrk[$topage])) {
				unset($this->transfmrk[$topage]);
			}
			if (isset($tmpannots)) {
				$this->PageAnnots[$topage] = $tmpannots;
			} elseif (isset($this->PageAnnots[$topage])) {
				unset($this->PageAnnots[$topage]);
			}
			if (isset($tmpnewpagegroup)) {
				$this->newpagegroup[$topage] = $tmpnewpagegroup;
			} elseif (isset($this->newpagegroup[$topage])) {
				unset($this->newpagegroup[$topage]);
			}
			// adjust outlines
			$tmpoutlines = $this->outlines;
			foreach ($tmpoutlines as $key => $outline) {
				if (($outline['p'] >= $topage) AND ($outline['p'] < $frompage)) {
					$this->outlines[$key]['p'] = $outline['p'] + 1;
				} elseif ($outline['p'] == $frompage) {
					$this->outlines[$key]['p'] = $topage;
				}
			}
			// adjust links
			$tmplinks = $this->links;
			foreach ($tmplinks as $key => $link) {
				if (($link[0] >= $topage) AND ($link[0] < $frompage)) {
					$this->links[$key][0] = $link[0] + 1;
				} elseif ($link[0] == $frompage) {
					$this->links[$key][0] = $topage;
				}
			}
			// adjust javascript
			$tmpjavascript = $this->javascript;
			global $jfrompage, $jtopage;
			$jfrompage = $frompage;
			$jtopage = $topage;
			$this->javascript = preg_replace_callback('/this\.addField\(\'([^\']*)\',\'([^\']*)\',([0-9]+)/',
				create_function('$matches', 'global $jfrompage, $jtopage;
				$pagenum = intval($matches[3]) + 1;
				if (($pagenum >= $jtopage) AND ($pagenum < $jfrompage)) {
					$newpage = ($pagenum + 1);
				} elseif ($pagenum == $jfrompage) {
					$newpage = $jtopage;
				} else {
					$newpage = $pagenum;
				}
				--$newpage;
				return "this.addField(\'".$matches[1]."\',\'".$matches[2]."\',".$newpage."";'), $tmpjavascript);
			// return to last page
			$this->lastPage(true);
			return true;
		}

        /**
		* Remove the specified page.
		* @param int $page page to remove
		* @return true in case of success, false in case of error.
		* @access public
		* @since 4.6.004 (2009-04-23)
		*/
		public function deletePage($page) {
			if ($page > $this->numpages) {
				return false;
			}
			// delete current page
			unset($this->pages[$page]);
			unset($this->pagedim[$page]);
			unset($this->pagelen[$page]);
			unset($this->intmrk[$page]);
			if (isset($this->footerpos[$page])) {
				unset($this->footerpos[$page]);
			}
			if (isset($this->footerlen[$page])) {
				unset($this->footerlen[$page]);
			}
			if (isset($this->transfmrk[$page])) {
				unset($this->transfmrk[$page]);
			}
			if (isset($this->PageAnnots[$page])) {
				unset($this->PageAnnots[$page]);
			}
			if (isset($this->newpagegroup[$page])) {
				unset($this->newpagegroup[$page]);
			}
			if (isset($this->pageopen[$page])) {
				unset($this->pageopen[$page]);
			}
			// update remaining pages
			for ($i = $page; $i < $this->numpages; ++$i) {
				$j = $i + 1;
				// shift pages
				$this->pages[$i] = $this->pages[$j];
				$this->pagedim[$i] = $this->pagedim[$j];
				$this->pagelen[$i] = $this->pagelen[$j];
				$this->intmrk[$i] = $this->intmrk[$j];
				if (isset($this->footerpos[$j])) {
					$this->footerpos[$i] = $this->footerpos[$j];
				} elseif (isset($this->footerpos[$i])) {
					unset($this->footerpos[$i]);
				}
				if (isset($this->footerlen[$j])) {
					$this->footerlen[$i] = $this->footerlen[$j];
				} elseif (isset($this->footerlen[$i])) {
					unset($this->footerlen[$i]);
				}
				if (isset($this->transfmrk[$j])) {
					$this->transfmrk[$i] = $this->transfmrk[$j];
				} elseif (isset($this->transfmrk[$i])) {
					unset($this->transfmrk[$i]);
				}
				if (isset($this->PageAnnots[$j])) {
					$this->PageAnnots[$i] = $this->PageAnnots[$j];
				} elseif (isset($this->PageAnnots[$i])) {
					unset($this->PageAnnots[$i]);
				}
				if (isset($this->newpagegroup[$j])) {
					$this->newpagegroup[$i] = $this->newpagegroup[$j];
				} elseif (isset($this->newpagegroup[$i])) {
					unset($this->newpagegroup[$i]);
				}
				if (isset($this->pageopen[$j])) {
					$this->pageopen[$i] = $this->pageopen[$j];
				} elseif (isset($this->pageopen[$i])) {
					unset($this->pageopen[$i]);
				}
			}
			// remove last page
			unset($this->pages[$this->numpages]);
			unset($this->pagedim[$this->numpages]);
			unset($this->pagelen[$this->numpages]);
			unset($this->intmrk[$this->numpages]);
			if (isset($this->footerpos[$this->numpages])) {
				unset($this->footerpos[$this->numpages]);
			}
			if (isset($this->footerlen[$this->numpages])) {
				unset($this->footerlen[$this->numpages]);
			}
			if (isset($this->transfmrk[$this->numpages])) {
				unset($this->transfmrk[$this->numpages]);
			}
			if (isset($this->PageAnnots[$this->numpages])) {
				unset($this->PageAnnots[$this->numpages]);
			}
			if (isset($this->newpagegroup[$this->numpages])) {
				unset($this->newpagegroup[$this->numpages]);
			}
			if (isset($this->pageopen[$this->numpages])) {
				unset($this->pageopen[$this->numpages]);
			}
			--$this->numpages;
			$this->page = $this->numpages;
			// adjust outlines
			$tmpoutlines = $this->outlines;
			foreach ($tmpoutlines as $key => $outline) {
				if ($outline['p'] > $page) {
					$this->outlines[$key]['p'] = $outline['p'] - 1;
				} elseif ($outline['p'] == $page) {
					unset($this->outlines[$key]);
				}
			}
			// adjust links
			$tmplinks = $this->links;
			foreach ($tmplinks as $key => $link) {
				if ($link[0] > $page) {
					$this->links[$key][0] = $link[0] - 1;
				} elseif ($link[0] == $page) {
					unset($this->links[$key]);
				}
			}
			// adjust javascript
			$tmpjavascript = $this->javascript;
			global $jpage;
			$jpage = $page;
			$this->javascript = preg_replace_callback('/this\.addField\(\'([^\']*)\',\'([^\']*)\',([0-9]+)/',
				create_function('$matches', 'global $jpage;
				$pagenum = intval($matches[3]) + 1;
				if ($pagenum >= $jpage) {
					$newpage = ($pagenum - 1);
				} elseif ($pagenum == $jpage) {
					$newpage = 1;
				} else {
					$newpage = $pagenum;
				}
				--$newpage;
				return "this.addField(\'".$matches[1]."\',\'".$matches[2]."\',".$newpage."";'), $tmpjavascript);
			// return to last page
			$this->lastPage(true);
			return true;
		}

		/**
		* Output a Table of Content Index (TOC).
		* You can override this method to achieve different styles.
		* @param int $page page number where this TOC should be inserted (leave empty for current page).
		* @param string $numbersfont set the font for page numbers (please use monospaced font for better alignment).
		* @param string $filler string used to fill the space between text and page number.
		* @access public
		* @author Nicola Asuni
		* @since 4.5.000 (2009-01-02)
		*/
		public function addTOC($page='', $numbersfont='', $filler='.') {
			$fontsize = $this->FontSizePt;
			$fontfamily = $this->FontFamily;
			$fontstyle = $this->FontStyle;
			$w = $this->w - $this->lMargin - $this->rMargin;
			$spacer = $this->GetStringWidth(' ') * 4;
			$page_first = $this->getPage();
			$lmargin = $this->lMargin;
			$rmargin = $this->rMargin;
			$x_start = $this->GetX();
			if ($this->empty_string($numbersfont)) {
				$numbersfont = $this->default_monospaced_font;
			}
			if ($this->empty_string($filler)) {
				$filler = ' ';
			}
			if ($this->empty_string($page)) {
				$gap = ' ';
			} else {
				$gap = '';
			}
			foreach ($this->outlines as $key => $outline) {
				if ($this->rtl) {
					$aligntext = 'R';
					$alignnum = 'L';
				} else {
					$aligntext = 'L';
					$alignnum = 'R';
				}
				if ($outline['l'] == 0) {
					$this->SetFont($fontfamily, $fontstyle.'B', $fontsize);
				} else {
					$this->SetFont($fontfamily, $fontstyle, $fontsize - $outline['l']);
				}
				$indent = ($spacer * $outline['l']);
				if ($this->rtl) {
					$this->rMargin += $indent;
					$this->x -= $indent;
				} else {
					$this->lMargin += $indent;
					$this->x += $indent;
				}
				$link = $this->AddLink();
				$this->SetLink($link, 0, $outline['p']);
				// write the text
				$this->Write(0, $outline['t'], $link, 0, $aligntext, false, 0, false, false, 0);
				$this->SetFont($numbersfont, $fontstyle, $fontsize);
				if ($this->empty_string($page)) {
					$pagenum = $outline['p'];
				} else {
					// placemark to be replaced with the correct number
					$pagenum = '{#'.($outline['p']).'}';
					if (($this->CurrentFont['type'] == 'TrueTypeUnicode') OR ($this->CurrentFont['type'] == 'cidfont0')) {
						$pagenum = '{'.$pagenum.'}';
				    }
				}
				$numwidth = $this->GetStringWidth($pagenum);
				if ($this->rtl) {
					$tw = $this->x - $this->lMargin;
				} else {
					$tw = $this->w - $this->rMargin - $this->x;
				}
				$fw = $tw - $numwidth - $this->GetStringWidth(' ');
				$numfills = floor($fw / $this->GetStringWidth($filler));
				if ($numfills > 0) {
					$rowfill = str_repeat($filler, $numfills);
				} else {
					$rowfill = '';
				}
				if ($this->rtl) {
					$pagenum = $pagenum.$gap.$rowfill.' ';
				} else {
					$pagenum = ' '.$rowfill.$gap.$pagenum;
				}
				// write the number
				//$this->SetX($x_start);
				$this->Cell($tw, 0, $pagenum, 0, 1, $alignnum, 0, $link, 0);
				$this->SetX($x_start);
				$this->lMargin = $lmargin;
				$this->rMargin = $rmargin;
			}
			$page_last = $this->getPage();
			$numpages = $page_last - $page_first + 1;
			if (!$this->empty_string($page)) {
				for ($p = $page_first; $p <= $page_last; ++$p) {
					// get page data
					$temppage = $this->getPageBuffer($p);
					for ($n = 1; $n <= $this->numpages; ++$n) {
						// update page numbers
						$k = '{#'.$n.'}';
						$ku = '{'.$k.'}';
						$alias_a = $this->_escape($k);
						$alias_au = $this->_escape('{'.$k.'}');
						if ($this->isunicode) {
							$alias_b = $this->_escape($this->UTF8ToLatin1($k));
							$alias_bu = $this->_escape($this->UTF8ToLatin1($ku));
							$alias_c = $this->_escape($this->utf8StrRev($k, false, $this->tmprtl));
							$alias_cu = $this->_escape($this->utf8StrRev($ku, false, $this->tmprtl));
						}
						if ($n >= $page) {
							$np = $n + $numpages;
						} else {
							$np = $n;
						}
						$ns = $this->formatTOCPageNumber($np);
						$nu = $ns;
						$sdiff = strlen($k) - strlen($ns) - 1;
						$sdiffu = strlen($ku) - strlen($ns) - 1;
						$sfill = str_repeat($filler, $sdiff);
						$sfillu = str_repeat($filler, $sdiffu);
						if ($this->rtl) {
							$ns = $ns.' '.$sfill;
							$nu = $nu.' '.$sfillu;
						} else {
							$ns = $sfill.' '.$ns;
							$nu = $sfillu.' '.$nu;
						}
						$nu = $this->UTF8ToUTF16BE($nu, false);
						$temppage = str_replace($alias_au, $nu, $temppage);
						if ($this->isunicode) {
							$temppage = str_replace($alias_bu, $nu, $temppage);
							$temppage = str_replace($alias_cu, $nu, $temppage);
							$temppage = str_replace($alias_b, $ns, $temppage);
							$temppage = str_replace($alias_c, $ns, $temppage);
						}
						$temppage = str_replace($alias_a, $ns, $temppage);
					}
					// save changes
					$this->setPageBuffer($p, $temppage);
				}
				// move pages
				for ($i = 0; $i < $numpages; ++$i) {
					$this->movePage($page_last, $page);
				}
			}
			$this->SetFont($fontfamily, $fontstyle, $fontsize);
		}

		/**
		* Stores a copy of the current TCPDF object used for undo operation.
		* @access public
		* @since 4.5.029 (2009-03-19)
		*/
		public function startTransaction() {
			if (isset($this->objcopy)) {
				// remove previous copy
				$this->commitTransaction();
			}
			// record current page number
			$this->start_transaction_page = $this->page;
			// clone current object
			$this->objcopy = $this->objclone($this);
		}

		/**
		* Delete the copy of the current TCPDF object used for undo operation.
		* @access public
		* @since 4.5.029 (2009-03-19)
		*/
		public function commitTransaction() {
			if (isset($this->objcopy)) {
				$this->objcopy->_destroy(true, true);
				unset($this->objcopy);
			}
		}

		/**
		* This method allows to undo the latest transaction by returning the latest saved TCPDF object with startTransaction().
		* @param boolean $self if true restores current class object to previous state without the need of reassignment via the returned value.
		* @return TCPDF object.
		* @access public
		* @since 4.5.029 (2009-03-19)
		*/
		public function rollbackTransaction($self=false) {
			if (isset($this->objcopy)) {
				if (isset($this->objcopy->diskcache) AND $this->objcopy->diskcache) {
					// truncate files to previous values
					foreach ($this->objcopy->cache_file_lenght as $file => $lenght) {
						$file = substr($file, 1);
						$handle = fopen($file, 'r+');
						ftruncate($handle, $lenght);
					}
				}
				$this->_destroy(true, true);
				if ($self) {
					$objvars = get_object_vars($this->objcopy);
					foreach ($objvars as $key => $value) {
						$this->$key = $value;
					}
				}
				return $this->objcopy;
			}
			return $this;
		}

		/**
		* Creates a copy of a class object
		* @param object $object class object to be cloned
		* @return cloned object
		* @access public
		* @since 4.5.029 (2009-03-19)
		*/
		public function objclone($object) {
			return @clone($object);
		}

		/**
		* Determine whether a string is empty.
		* @param srting $str string to be checked
		* @return boolean true if string is empty
		* @access public
		* @since 4.5.044 (2009-04-16)
		*/
		public function empty_string($str) {
			return (is_null($str) OR (is_string($str) AND (strlen($str) == 0)));
		}
		
	} // END OF TCPDF CLASS
}
//============================================================+
// END OF FILE
//============================================================+
?>
