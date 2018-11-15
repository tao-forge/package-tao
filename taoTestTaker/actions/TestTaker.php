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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2014 (update and modification) Open Assessment Technologies SA
 */
namespace oat\taoTestTaker\actions;

use core_kernel_classes_Class;
use oat\generis\Helper\UserHashForEncryption;
use oat\generis\model\GenerisRdf;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\tao\model\resources\ResourceWatcher;
use oat\taoTestTaker\actions\form\Search;
use oat\taoTestTaker\actions\form\TestTaker as TestTakerForm;
use oat\taoGroups\helpers\TestTakerForm as GroupForm;
use oat\taoTestTaker\models\events\TestTakerUpdatedEvent;
use oat\taoTestTaker\models\TestTakerService;
use tao_actions_SaSModule;

/**
 * Subjects Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoTestTaker
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class TestTaker extends tao_actions_SaSModule
{
    use EventManagerAwareTrait;

    /**
     * constructor: initialize the service and the default data
     *
     * @return Subjects
     */
    public function __construct()
    {
        parent::__construct();

        // the service is initialized by default
        $this->service = TestTakerService::singleton();
        $this->defaultData();
    }

    /**
     * get the main class
     *
     * @return core_kernel_classes_Classes
     */
    protected function getClassService()
    {
        return $this->service;
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param core_kernel_classes_Class $clazz
     * @return tao_actions_form_Search
     */
    protected function getSearchForm($clazz)
    {
        return new Search($clazz, null, array(
            'recursive' => true
        ));
    }

    /**
     * edit an subject instance
     *
     */
    public function editSubject()
    {
        $clazz = $this->getCurrentClass();

        // get the subject to edit
        $subject = $this->getCurrentInstance();

        $addMode = false;
        $login = (string) $subject->getOnePropertyValue(new \core_kernel_classes_Property(GenerisRdf::PROPERTY_USER_LOGIN));
        if (empty($login)) {
            $addMode = true;
            $this->setData('loginUri', \tao_helpers_Uri::encode(GenerisRdf::PROPERTY_USER_LOGIN));
        }

        if ($this->hasRequestParameter('reload')) {
            $this->setData('reload', true);
        }

        $myFormContainer = new TestTakerForm($clazz, $subject, $addMode, false);
        $myForm = $myFormContainer->getForm();

        if ($myForm->isSubmited()) {
            if ($myForm->isValid()) {
                $this->setData('reload', false);

                $values = $myForm->getValues();

                if ($addMode) {
                    $plainPassword = $values['password1'];
                    $values[GenerisRdf::PROPERTY_USER_PASSWORD] = \core_kernel_users_Service::getPasswordHash()->encrypt($values['password1']);
                    unset($values['password1']);
                    unset($values['password2']);
                } else {
                    if (! empty($values['password2'])) {
                        $plainPassword = $values['password2'];
                        $values[GenerisRdf::PROPERTY_USER_PASSWORD] = \core_kernel_users_Service::getPasswordHash()->encrypt($values['password2']);
                    }
                    unset($values['password2']);
                    unset($values['password3']);
                }

                $binder = new \tao_models_classes_dataBinding_GenerisFormDataBinder($subject);
                $subject = $binder->bind($values);

                $data = [];
                if (isset($plainPassword)){
                    $data = ['hashForKey' => UserHashForEncryption::hash($plainPassword)];
                }

                $this->getEventManager()->trigger(new TestTakerUpdatedEvent($subject->getUri(),
                    array_merge($values, $data)
                ));

                if ($addMode) {
                    // force default subject roles to be the Delivery Role:
                    $this->service->setTestTakerRole($subject);
                }

                // force the data language to be the same as the gui language
                $userService = \tao_models_classes_UserService::singleton();
                $lang = new \core_kernel_classes_Resource($values[GenerisRdf::PROPERTY_USER_UILG]);
                $userService->bindProperties($subject, array(
                    GenerisRdf::PROPERTY_USER_DEFLG => $lang->getUri()
                ));

                $message = __('Test taker saved');

                if ($addMode) {
                    $params = array(
                        'uri' => \tao_helpers_Uri::encode($subject->getUri()),
                        'classUri' => \tao_helpers_Uri::encode($clazz->getUri()),
                        'reload' => true,
                        'message' => $message
                    );
                    $this->redirect(_url('editSubject', null, null, $params));
                }

                $this->setData("selectNode", \tao_helpers_Uri::encode($subject->getUri()));
                $this->setData('message', $message);
                $this->setData('reload', true);
            }
        }

        if (\common_ext_ExtensionsManager::singleton()->isEnabled('taoGroups')) {
            $this->setData('groupForm', GroupForm::renderGroupTreeForm($subject));
        }
        $updatedAt = $this->getServiceManager()->get(ResourceWatcher::SERVICE_ID)->getUpdatedAt($subject);
        $this->setData('updatedAt', $updatedAt);
        $this->setData('checkLogin', $addMode);
        $this->setData('formTitle', __('Edit subject'));
        $this->setData('myForm', $myForm->render());
        $this->setView('form_subjects.tpl');
    }

    /**
     * overwrite the parent moveAllInstances to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveResource()
     * @requiresRight uri WRITE
     */
    public function moveResource()
    {
        return parent::moveResource();
    }
    /**
     * overwrite the parent moveAllInstances to add the requiresRight only in Items
     * @see tao_actions_TaoModule::moveAll()
     * @requiresRight ids WRITE
     */
    public function moveAll()
    {
        return parent::moveAll();
    }
}
