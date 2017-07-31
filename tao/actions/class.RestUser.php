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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Class tao_actions_RestUser
 *
 * Rest interface to manage forms to create and edit users.
 *
 * Request should contains following data:
 * [
 *       "http://www.tao.lu/Ontologies/generis.rdf#userFirstName" => "Bertrand",
 *       "http://www.tao.lu/Ontologies/generis.rdf#userLastName"  => "Chevrier",
 *       "http://www.tao.lu/Ontologies/generis.rdf#userMail" => "bertrand@taotesting.com",
 *       "http://www.tao.lu/Ontologies/generis.rdf#userDefLg" => "http://www.tao.lu/Ontologies/TAO.rdf#Langfr-FR",
 *       "http://www.tao.lu/Ontologies/generis.rdf#userUILg" => "http://www.tao.lu/Ontologies/TAO.rdf#Langfr-FR",
 *       "http://www.tao.lu/Ontologies/generis.rdf#login" => "berty",
 *       "http://www.w3.org/2000/01/rdf-schema#label" => "bertounet",
 *       "http://www.tao.lu/Ontologies/generis.rdf#userRoles"=> [
 *          'http://www.tao.lu/Ontologies/TAOProctor.rdf#ProctorRole',
 *          'http://www.tao.lu/Ontologies/TAO.rdf#SysAdminRole'
 *       ],
 *       'password1' => 'ctl789@CTL789@',
 *       'password2' => 'ctl789@CTL789@',
 * ]
 */
class tao_actions_RestUser extends tao_actions_RestResourceController
{
    /**
     * Return the form object to manage user edition or creation
     *
     * @param $instance
     * @return tao_actions_form_RestUserForm
     */
    protected function getForm($instance)
    {
        $form = new \tao_actions_form_RestUserForm($instance);
        $form->setServiceLocator($this->getServiceManager());
        return $form;
    }

    public function create()
    {
        //$data = $this->getForm($this->getClassParameter())->getData();
        //var_dump($data[tao_actions_form_RestUserForm::PROPERTIES]);

        try {
            $parameters = $this->getRequestParameters();
            $resource = $this->getForm($this->getClassParameter())
                ->bind($parameters)
                ->validate()
                ->save();
            var_dump(['uri' => $resource->getUri()]);
        } catch (common_Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function edit()
    {
        try {
            //$this->returnSuccess($this->getForm($this->getResourceParameter())->getData());
        } catch (common_Exception $e) {
            $this->returnFailure($e);
        }

        try {
            $parameters = $this->getRequestParameters();
            $resource = $this->getForm($this->getResourceParameter())
                ->bind($parameters)
                ->validate()
                ->save();
            var_dump(['uri' => $resource->getUri()]);
        } catch (common_Exception $e) {
            var_dump($e->getMessage());
        }
    }


    public function getRequestParameters()
    {
        return $parameters = [
            "http://www.tao.lu/Ontologies/generis.rdf#userFirstName" => "Bertrand",
            "http://www.tao.lu/Ontologies/generis.rdf#userLastName"  => "Chevrier",
            "http://www.tao.lu/Ontologies/generis.rdf#userMail" => "bertrand@taotesting.com",
            "http://www.tao.lu/Ontologies/generis.rdf#userDefLg" => "http://www.tao.lu/Ontologies/TAO.rdf#Langfr-FR",
            "http://www.tao.lu/Ontologies/generis.rdf#userUILg" => "http://www.tao.lu/Ontologies/TAO.rdf#Langfr-FR",
            "http://www.tao.lu/Ontologies/generis.rdf#login" => "berty",
            "http://www.w3.org/2000/01/rdf-schema#label" => "bertounet",
            "http://www.tao.lu/Ontologies/generis.rdf#userRoles"=> [
                'http://www.tao.lu/Ontologies/TAOProctor.rdf#ProctorRole',
                'http://www.tao.lu/Ontologies/TAO.rdf#SysAdminRole'
            ],
            'password1' => 'Atl789@Atl789@',
            'password2' => 'Atl789@Atl789@',
        ];
    }


}