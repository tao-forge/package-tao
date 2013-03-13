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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * default action
 * must be in the actions folder
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package action
 */
class tao_actions_ExtensionsManager extends tao_actions_CommonModule {

	/**
	 * Index page
	 */
	public function index() {

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$extensionManager->reset();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		$availlableExtArray = $extensionManager->getAvailableExtensions();
		$this->setData('installedExtArray',$installedExtArray);
		$this->setData('availableExtArray',$availlableExtArray);
		$this->setView('extensionManager/view.tpl');

	}

	protected function getCurrentExtension() {
		if ($this->hasRequestParameter('id')) {
			$extensionManager = common_ext_ExtensionsManager::singleton();
			return common_ext_ExtensionsManager::singleton()->getExtensionById($this->getRequestParameter('id'));
		} else {
			return null;
		}
	}

	public function add( $id , $package_zip ){

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$fileUnzip = new fileUnzip(urldecode($package_zip));
		$fileUnzip->unzipAll(EXTENSION_PATH);
		$newExt = $extensionManager->getExtensionById($id);
		$extInstaller = new tao_install_ExtensionInstaller($newExt);
		try {
			$extInstaller->install();
			$message =   __('Extension ') . $newExt->name . __(' has been installed');
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}

		$this->setData('message',$message);
		$this->index();

	}

	public function install(){
		$success = false;
		try {
			$extInstaller = new tao_install_ExtensionInstaller($this->getCurrentExtension());
			$extInstaller->install();
			$message =   __('Extension ') . $this->getCurrentExtension()->getID() . __(' has been installed');
			$success = true;
			
			// @todo solve this differently.
			$userService = core_kernel_users_Service::singleton();
			$session = core_kernel_classes_Session::singleton();
			$userUri = $session->getUserUri();
			$user = new core_kernel_classes_Resource($userUri);
			$userLogin = $session->getUserLogin();
			$userRoles = $userService->getUserRoles($user);
			$session->setUser($userLogin, $userUri, $userRoles);
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}

		echo json_encode(array('success' => $success, 'message' => $message));
	}


	public function modify($loaded,$loadAtStartUp){

		$extensionManager = common_ext_ExtensionsManager::singleton();
		$installedExtArray = $extensionManager->getInstalledExtensions();
		$configurationArray = array();
		foreach($installedExtArray as $k=>$ext){
			$configuration = new common_ext_ExtensionConfiguration(isset($loaded[$k]),isset($loadAtStartUp[$k]));
			$configurationArray[$k]=$configuration;
		}
		try {
			$extensionManager->modifyConfigurations($configurationArray);
			$message = __('Extensions\' configurations updated ');
		}
		catch(common_ext_ExtensionException $e) {
			$message = $e->getMessage();
		}
		$this->setData('message', $message);
		$this->index();

	}

}
?>