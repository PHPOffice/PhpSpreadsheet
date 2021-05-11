<?php

namespace PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class Namespaces
{
    const SCHEMAS = 'http://schemas.openxmlformats.org';

    const RELATIONSHIPS = self::SCHEMAS . '/package/2006/relationships';

    // This one used in Reader\Xlsx
    const CORE_PROPERTIES = self::RELATIONSHIPS . '/metadata/core-properties';

    // This one used in Reader\Xlsx\Properties
    const CORE_PROPERTIES2 = self::SCHEMAS . '/package/2006/metadata/core-properties';

    const THEME = self::RELATIONSHIPS . '/theme';

    const COMPATIBILITY = self::SCHEMAS . '/markup-compatibility/2006';

    const MAIN = self::SCHEMAS . '/spreadsheetml/2006/main';

    const DRAWINGML = self::SCHEMAS . '/drawingml/2006/main';

    const CHART = self::SCHEMAS . '/drawingml/2006/chart';

    const SPREADSHEET_DRAWING = self::SCHEMAS . '/drawingml/2006/spreadsheetDrawing';

    const SCHEMA_OFFICE_DOCUMENT = self::SCHEMAS . '/officeDocument/2006/relationships';

    const COMMENTS = self::SCHEMA_OFFICE_DOCUMENT . '/comments';

    //const CUSTOM_PROPERTIES = self::SCHEMA_OFFICE_DOCUMENT . '/custom-properties';

    //const EXTENDED_PROPERTIES = self::SCHEMA_OFFICE_DOCUMENT . '/extended-properties';

    const HYPERLINK = self::SCHEMA_OFFICE_DOCUMENT . '/hyperlink';

    const OFFICE_DOCUMENT = self::SCHEMA_OFFICE_DOCUMENT . '/officeDocument';

    const SHARED_STRINGS = self::SCHEMA_OFFICE_DOCUMENT . '/sharedStrings';

    const STYLES = self::SCHEMA_OFFICE_DOCUMENT . '/styles';

    const IMAGE = self::SCHEMA_OFFICE_DOCUMENT . '/image';

    const VML = self::SCHEMA_OFFICE_DOCUMENT . '/vmlDrawing';

    const WORKSHEET = self::SCHEMA_OFFICE_DOCUMENT . '/worksheet';

    const SCHEMA_MICROSOFT = 'http://schemas.microsoft.com/office/2006/relationships';

    const EXTENSIBILITY = self::SCHEMA_MICROSOFT . '/ui/extensibility';

    const VBA = self::SCHEMA_MICROSOFT . '/vbaProject';

    const DC_ELEMENTS = 'http://purl.org/dc/elements/1.1/';

    const DC_TERMS = 'http://purl.org/dc/terms';

    const URN_MSOFFICE = 'urn:schemas-microsoft-com:office:office';

    const URN_VML = 'urn:schemas-microsoft-com:vml';

    const SCHEMA_PURL = 'http://purl.oclc.org/ooxml';

    const PURL_OFFICE_DOCUMENT = self::SCHEMA_PURL . '/officeDocument/relationships/officeDocument';

    const PURL_RELATIONSHIPS = self::SCHEMA_PURL . '/officeDocument/relationships';

    const PURL_MAIN = self::SCHEMA_PURL . '/spreadsheetml/main';

    const PURL_DRAWING = self::SCHEMA_PURL . '/drawingml/main';

    const PURL_WORKSHEET = self::PURL_RELATIONSHIPS . '/worksheet';
}
