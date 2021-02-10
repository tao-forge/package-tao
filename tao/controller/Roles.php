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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013-2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\tao\controller;

use common_exception_BadRequest;
use core_kernel_users_Cache;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyAwareTrait;
use oat\tao\model\RoleService;
use oat\tao\model\TaoOntology;
use oat\tao\model\UserService;
use oat\tao\model\dataBinding\GenerisFormDataBinder;
use oat\tao\model\exceptions\UserErrorException;
use oat\tao\model\form\RoleForm;
use tao_helpers_Uri;
use tao_helpers_form_FormContainer;
use tao_helpers_form_FormContainer as FormContainer;
use tao_helpers_form_GenerisTreeForm;

/**
 * Role Controller provide actions performed from url resolution
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoGroups

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class Roles extends RdfController
{
    use OntologyAwareTrait;

    protected $authoringService = null;
    protected $forbidden = [];

    /**
     * index:
     */
    public function index()
    {
        $this->defaultData();

        $this->removeSessionAttribute('uri');
        $this->removeSessionAttribute('classUri');

        $this->setView('roles/index.tpl');
    }

    /**
     * Edit a group instance
     * @return void
     */
    public function editRole()
    {
        $this->defaultData();

        $clazz = $this->getCurrentClass();
        $role = $this->getCurrentInstance();

        $formContainer = new RoleForm($clazz, $role, [FormContainer::CSRF_PROTECTION_OPTION => true]);
        $myForm = $formContainer->getForm();
        if ($myForm->isSubmited() && $myForm->isValid()) {
            $formValues = $myForm->getValues();
            $roleService = RoleService::singleton();
            $includedRolesProperty = $this->getProperty(GenerisRdf::PROPERTY_ROLE_INCLUDESROLE);

            // We have to make the difference between the old list
            // of included roles and the new ones.
            $oldIncludedRolesUris = $role->getPropertyValues($includedRolesProperty);
            $newIncludedRolesUris = $formValues[GenerisRdf::PROPERTY_ROLE_INCLUDESROLE];
            $removeIncludedRolesUris = array_diff($oldIncludedRolesUris, $newIncludedRolesUris);
            $addIncludedRolesUris = array_diff($newIncludedRolesUris, $oldIncludedRolesUris);

            // Make the changes according to the detected differences.
            foreach ($removeIncludedRolesUris as $rU) {
                $r = $this->getResource($rU);
                $roleService->unincludeRole($role, $r);
            }

            foreach ($addIncludedRolesUris as $aU) {
                $r = $this->getResource($aU);
                $roleService->includeRole($role, $r);
            }

            // Let's deal with other properties the usual way.
            unset($formValues[$includedRolesProperty->getUri()]);

            $binder = new GenerisFormDataBinder($role);
            $role = $binder->bind($myForm->getValues());

            core_kernel_users_Cache::removeIncludedRoles($role); // flush cache for this role.

            $this->setData('selectNode', tao_helpers_Uri::encode($role->getUri()));
            $this->setData('message', __('Role saved'));
            $this->setData('reload', true);
        }

        $this->setData('uri', tao_helpers_Uri::encode($role->getUri()));
        $this->setData('classUri', tao_helpers_Uri::encode($clazz->getUri()));
        $this->setData('formTitle', 'Edit Role');
        $this->setData('myForm', $myForm->render());
        $this->setView('roles/form.tpl');
    }

    public function assignUsers()
    {
        $this->defaultData();

        $role = $this->getCurrentInstance();
        $prop = $this->getProperty(GenerisRdf::PROPERTY_USER_ROLES);
        $tree = tao_helpers_form_GenerisTreeForm::buildReverseTree($role, $prop);
        $tree->setData('title', __('Assign User to role'));
        $tree->setData('dataUrl', _url('getUsers'));
        $this->setData('userTree', $tree->render());
        $this->setView('roles/assignUsers.tpl');
    }

    /**
     * Delete a group or a group class
     * @throws UserErrorException
     * @throws common_exception_BadRequest
     * @throws common_exception_Error
     * @throws common_exception_MissingParameter
     * @return void
     */
    public function delete()
    {
        if (!$this->isXmlHttpRequest()) {
            throw new common_exception_BadRequest('wrong request mode');
        } else {
            $deleted = false;
            if ($this->getRequestParameter('uri')) {
                $role = $this->getCurrentInstance();

                if (!in_array($role->getUri(), $this->forbidden)) {
                    //check if no user is using this role:
                    $userClass = $this->getClass(GenerisRdf::CLASS_GENERIS_USER);
                    $options = ['recursive' => true, 'like' => false];
                    $filters = [GenerisRdf::PROPERTY_USER_ROLES => $role->getUri()];
                    $users = $userClass->searchInstances($filters, $options);
                    if (empty($users)) {
                        //delete role here:
                        $deleted = $this->getClassService()->removeRole($role);
                    } else {
                        //set message error
                        throw new UserErrorException(__('This role is still given to one or more users. Please remove the role to these users first.'));
                    }
                } else {
                    throw new UserErrorException($role->getLabel() . ' could not be deleted');
                }
            }

            $this->returnJson(['deleted' => $deleted, 'success' => $deleted]);
        }
    }

    /**
     * @throws common_exception_BadRequest
     * @throws common_exception_Error
     */
    public function getUsers()
    {
        if (!$this->isXmlHttpRequest()) {
            throw new common_exception_BadRequest('wrong request mode');
        } else {
            $this->returnJson($this->getUserService()->toTree($this->getClass(TaoOntology::CLASS_URI_TAO_USER), []));
        }
    }

    /**
     * @throws common_ext_ExtensionException
     */
    public function editRoleClass()
    {
        $this->defaultData();

        $this->removeSessionAttribute('uri');
        $this->index();
    }

    /**
     * get the main class
     * @return \core_kernel_classes_Class
     */
    protected function getRootClass()
    {
        return $this->getClassService()->getRoleClass();
    }

    /**
     * @return oat\tao\model\RoleService
     */
    protected function getClassService()
    {
        if (!$this->service) {
            $this->service = RoleService::singleton();
        }
        return $this->service;
    }

    /**
     * @return oat\tao\model\UserService
     */
    protected function getUserService()
    {
        return $this->getServiceLocator()->get(UserService::SERVICE_ID);
    }
}
