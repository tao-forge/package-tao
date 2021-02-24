<?php

/**
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
 */

namespace oat\taoQtiTest\models\import;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use \Exception;
use \common_report_Report;
use \helpers_TimeOutHelper;
use oat\oatbox\PhpSerializable;
use oat\oatbox\PhpSerializeStateless;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\taoQtiTest\models\QtiTestService;
use oat\taoQtiTest\models\event\QtiTestImportEvent;
use oat\tao\model\import\ImportHandler;
use oat\tao\model\import\ImportHandlerHelperTrait;
use oat\tao\model\import\TaskParameterProviderInterface;

/**
 * Import handler for QTI packages
 *
 * @access  public
 * @author  Joel Bout, <joel@taotesting.com>
 * @package taoQTI
 */
class TestImport implements ImportHandler, PhpSerializable, ServiceLocatorAwareInterface, TaskParameterProviderInterface
{
    use PhpSerializeStateless;
    use EventManagerAwareTrait;
    use ImportHandlerHelperTrait;

    /**
     * (non-PHPdoc)
     * @see oat\tao\model\import\ImportHandler::getLabel()
     */
    public function getLabel()
    {
        return __('QTI/APIP Test Content Package');
    }

    /**
     * (non-PHPdoc)
     * @see oat\tao\model\import\ImportHandler::getForm()
     */
    public function getForm()
    {
        $form = new TestImportForm();

        return $form->getForm();
    }

    /**
     * @param core_kernel_classes_Class   $class
     * @param tao_helpers_form_Form|array $form
     * @param string|null $userId owner of the resource
     * @return common_report_Report
     */
    public function import($class, $form, $userId = null)
    {
        try {
            $uploadedFile = $this->fetchUploadedFile($form);

            // The zip extraction is a long process that can exceed the 30s timeout
            helpers_TimeOutHelper::setTimeOutLimit(helpers_TimeOutHelper::LONG);

            $report = QtiTestService::singleton()->importMultipleTests($class, $uploadedFile);

            helpers_TimeOutHelper::reset();

            $this->getUploadService()->remove($uploadedFile);

            if (common_report_Report::TYPE_SUCCESS == $report->getType()) {
                $this->getEventManager()->trigger(new QtiTestImportEvent($report));
            }

            return $report;
        } catch (Exception $e) {
            return common_report_Report::createFailure($e->getMessage());
        }
    }
}