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
 * Copyright (c) 2013 (original work) Open Assessment Technlogies SA (under the project TAO-PRODUCT);
 * 
 */
 
/**
 * This module runs the items
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 */
class taoItems_actions_ItemRunner extends tao_actions_ServiceModule {
	
	public function index(){

		$session = PHPSession::singleton();
		
		$userId = common_session_SessionManager::getSession()->getUserUri();
		if(is_null($userId)){
			throw new common_exception_Error('No user is logged in');
		}
		$lang = core_kernel_classes_Session::singleton()->getDataLanguage();

		
		if ($this->hasRequestParameter('serviceCallId')) {
    		$serviceCallId = $this->getRequestParameter('serviceCallId');
            $variableData = tao_models_classes_service_state_Service::singleton()->get($userId, $serviceCallId);
    		$this->setData('storageData', array(
    			'serial'	=> $serviceCallId,
    			'data'		=> is_null($variableData) ? array() : $variableData
    		));
		}
		
		$directoryResource = new core_kernel_file_File(tao_helpers_Uri::decode($this->getRequestParameter('itemPath')));

		$baseUrl = taoDelivery_models_classes_RuntimeAccess::getAccessProvider()->getAccessUrl($directoryResource);
		/*
		$itemPath = taoDelivery_models_classes_RuntimeAccess::getAccessProvider()->getAccessUrl($directoryResource);
		
        echo $itemPath;
        die();

		$this->setData('itemId', '12345');
		*/
		$this->setData('itemPath', $baseUrl.$lang.DIRECTORY_SEPARATOR.'index.html');
		
		$this->setView('runtime/item_runner.tpl');			
	}
	
	public function access() {
		$provider = new tao_models_classes_fsAccess_ActionAccessProvider();
		$filename = $provider->decodeUrl($_SERVER['REQUEST_URI']);
		if (file_exists($filename)) {
			$mimeType = tao_helpers_File::getMimeType($filename);
			header('Content-Type: '.$mimeType);
			$fp = fopen($filename, 'rb');
 			fpassthru($fp);
		} else {
			throw new tao_models_classes_FileNotFoundException($filename);
		}
	}
	
	public function saveVariables() {
	    /*
	    $user = new core_kernel_classes_Resource(core_kernel_classes_Session::singleton()->getUserUri());
        $success = tao_models_classes_service_state_VariableProxy::singleton()->set(
            $user,
            $this->getRequestParameter('id'),
            $this->getRequestParameter('data')
        );
        */
	    $success = false;
		echo json_encode($success);
	}
	
	public function getVariables() {
	    /*
	    $user = new core_kernel_classes_Resource(core_kernel_classes_Session::singleton()->getUserUri());
        $variables = tao_models_classes_service_state_VariableProxy::singleton()->get(
            $user,
            $this->getRequestParameter('id')
        );
        */
	    $variables = null;
		echo json_encode(array(
			'success' => $variables !== null,
			'data' => $variables
		));
	}
}
