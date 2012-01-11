<?php

error_reporting(E_ALL);

/**
 * An implementation of TranslationFileWriter aiming at writing PO files.
 *
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A Writing class for TranslationFiles. Must be implemented by a concrete class
 * a given Translation Format such as XLIFF, PO, ... The write method must be
 * by subclasses.
 *
 * @author Jerome Bogaerts
 * @since 2.2
 * @version 1.0
 */
require_once('tao/helpers/translation/class.TranslationFileWriter.php');

/* user defined includes */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-includes begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-includes end

/* user defined constants */
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-constants begin
// section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E1-constants end

/**
 * An implementation of TranslationFileWriter aiming at writing PO files.
 *
 * @access public
 * @author Jerome Bogaerts
 * @package tao
 * @since 2.2
 * @subpackage helpers_translation
 * @version 1.0
 */
class tao_helpers_translation_POFileWriter
    extends tao_helpers_translation_TranslationFileWriter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method write
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function write()
    {
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E3 begin
        $buffer = '';
        
        // Add PO Headers.
        $buffer .= 'msgid ""' . "\n";
        $buffer .= 'msgstr ""' . "\n";
        $buffer .= '"Project-Id-Version: ' . PRODUCT_NAME . ' ' . TAO_VERSION_NAME . '\n"' . "\n";
        $buffer .= '"PO-Revision-Date: ' . date('Y-m-d') . 'T' . date('H:i:s') . '\n"' . "\n";
        $buffer .= '"Last-Translator: TAO Translation Team <translation@tao.lu>\n"' . "\n";
        $buffer .= '"MIME-Version: 1.0\n"' . "\n";
        $buffer .= '"Language: ' . $this->getTranslationFile()->getTargetLanguage() . '\n"' . "\n";
        $buffer .= '"Content-Type: text/plain; charset=utf-8\n"' . "\n";
        $buffer .= '"Content-Transfer-Encoding: 8bit\n"' . "\n\n";
        
        // Write all Translation Units.
		foreach($this->getTranslationFile()->getTranslationUnits() as $tu) {
			$s = tao_helpers_translation_POUtils::sanitize($tu->getSource(), true);
			$t = tao_helpers_translation_POUtils::sanitize($tu->getTarget(), true);
			$buffer .=  "msgid \"{$s}\"\n";
			$buffer .=  "msgstr \"{$t}\"\n";
			$buffer .=  "\n";
		}
		return file_put_contents($this->getFilePath(), $buffer);
        // section 10-13-1-85-72d0ca97:1331b62f595:-8000:00000000000034E3 end
    }

} /* end of class tao_helpers_translation_POFileWriter */

?>