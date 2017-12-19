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

use oat\generis\model\OntologyRdf;

/**
 * Crud services implements basic CRUD services, orginally intended for REST controllers/ HTTP exception handlers
 * Consequently the signatures and behaviors is closer to REST and throwing HTTP like exceptions
 *
 * @author "Patrick Plichart, <patrick@taotesting.com>"
 *        
 */
class taoResultServer_models_classes_CrudResultServerService extends tao_models_classes_CrudService
{

    /**
     * (non-PHPdoc)
     * 
     * @see tao_models_classes_CrudService::getClassService()
     */
    protected function getClassService()
    {
        return taoResultServer_models_classes_ResultServerAuthoringService::singleton();
    }

    /**
     * (non-PHPdoc) 
     * 
     * @see tao_models_classes_CrudService::delete()
     */
    public function delete($resource)
    {
        $this->getClassService()->deleteResultServer(new core_kernel_classes_Resource($resource));
        return true;
    }

    /**
     * 
     * @author Patrick Plichart, <patrick@taotesting.com>
     * @param array $propertiesValues
     * @return core_kernel_classes_Resource
     */
    public function createFromArray(array $propertiesValues)
    {
        if (! isset($propertiesValues[RDFS_LABEL])) {
            $propertiesValues[RDFS_LABEL] = "";
        }
        $type = isset($propertiesValues[OntologyRdf::RDF_TYPE]) ? $propertiesValues[OntologyRdf::RDF_TYPE] : $this->getRootClass();
        $label = $propertiesValues[RDFS_LABEL];
        // hmmm
        unset($propertiesValues[RDFS_LABEL]);
        unset($propertiesValues[OntologyRdf::RDF_TYPE]);
        $resource = parent::create($label, $type, $propertiesValues);
        return $resource;
    }
}

?>
