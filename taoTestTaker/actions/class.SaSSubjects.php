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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2002-2008 (update and modification) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * SaSSubjects Controller provide process services on subjects
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class taoSubjects_actions_SaSSubjects extends taoSubjects_actions_Subjects {

    
    
    /**
     * @see Subjects::__construct()
     */
    public function __construct() {
    	tao_helpers_Context::load('STANDALONE_MODE');
        $this->setSessionAttribute('currentExtension', 'taoSubjects');
		parent::__construct();
    }
    
	/**
	 * @see TaoModule::setView()
	 * @param string $identifier the view name
	 * @param boolean $useMetaExtensionView use a view from the parent extention
	 * @return mixed 
	 */
    public function setView($identifier, $useMetaExtensionView = false) {
		if(tao_helpers_Request::isAjax()){
			return parent::setView($identifier, $useMetaExtensionView);
		}
    	if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', DIR_VIEWS . 'templates/' . $identifier);
		}
		return parent::setView('sas.tpl', true);
    }

	/**
     * overrided to prevent exception: 
     * if no class is selected, the root class is returned 
     * @see TaoModule::getCurrentClass()
     * @return core_kernel_class_Class
     */
    protected function getCurrentClass() {
        if($this->hasRequestParameter('classUri')){
        	return parent::getCurrentClass();
        }
		return $this->getRootClass();
    }

	/**
	 * Render the tree form to add a subject to groups
	 * @return void 
	 */
	public function addToGroup(){
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setData('classUri', $this->getRequestParameter('classUri'));
		$subjectGroups = tao_helpers_Uri::encodeArray($this->service->getSubjectGroups($this->getCurrentInstance()), tao_helpers_Uri::ENCODE_ARRAY_VALUES);
		$this->setData('subjectGroups', json_encode($subjectGroups));
		$this->setView('groups.tpl');
	}	
}
?>