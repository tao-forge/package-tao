<?php
/*
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */

namespace oat\taoQtiItem\model\import;

use oat\oatbox\PhpSerializable;
use oat\oatbox\PhpSerializeStateless;
use oat\oatbox\service\ServiceManager;
use oat\tao\model\upload\UploadService;
use oat\taoQtiItem\model\qti\ImportService;
use oat\taoQtiItem\model\qti\exception\UnsupportedQtiElement;
use oat\taoQtiItem\model\qti\exception\ParsingException;
use oat\taoQtiItem\model\qti\exception\QtiModelException;
use oat\taoQtiItem\model\qti\parser\ValidationException;
use \tao_models_classes_import_ImportHandler;
use \common_report_Report;
use \common_Exception;
use \common_exception_Error;

/**
 * Importhandler for QTI XML files
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoQTIItem
 */
class QtiItemImport implements tao_models_classes_import_ImportHandler, PhpSerializable
{
    use PhpSerializeStateless;

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getLabel()
     */
    public function getLabel()
    {
        return __('QTI/APIP XML Item Document');
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::getForm()
     */
    public function getForm()
    {
        $form = new QtiItemImportForm();
        return $form->getForm();
    }

    /**
     * (non-PHPdoc)
     * @see tao_models_classes_import_ImportHandler::import()
     * @param \core_kernel_classes_Class $class
     * @param \tao_helpers_form_Form $form
     * @return common_report_Report
     * @throws \oat\oatbox\service\ServiceNotFoundException
     * @throws \common_Exception
     * @throws common_exception_Error
     */
    public function import($class, $form)
    {

        $fileInfo = $form->getValue('source');

        if (isset($fileInfo['uploaded_file'])) {

            /** @var  UploadService $uploadService */
            $uploadService = ServiceManager::getServiceManager()->get(UploadService::SERVICE_ID);
            $uploadedFile = $uploadService->getUploadedFile($fileInfo['uploaded_file']);

            try {
                $importService = ImportService::singleton();
                $report = $importService->importQTIFile($uploadedFile, $class, true);
            } catch (UnsupportedQtiElement $e) {
                $report = common_report_Report::createFailure(__("A QTI component is not supported. The system returned the following error: %s\n", $e->getUserMessage()));
            } catch (QtiModelException $e) {
                $report = common_report_Report::createFailure(__("One or more QTI components are not supported by the system. The system returned the following error: %s\n", $e->getUserMessage()));
            } catch (ParsingException $e) {
                $report = common_report_Report::createFailure(__("The validation of the imported QTI item failed. The system returned the following error:%s\n", $e->getMessage()));
            } catch(ValidationException $e) {
                $report = $e->getReport();
            } catch(common_Exception $e) {
                $report = common_report_Report::createFailure(__("An unexpected error occurred during the import of the QTI Item. The system returned the following error: %s\n", $e->getMessage()));
            }

            $uploadService->remove($uploadService->getUploadedFlyFile($fileInfo['uploaded_file']));
        } else {
            throw new common_exception_Error('No source file for import');
        }
        
        return $report;
    }
}
