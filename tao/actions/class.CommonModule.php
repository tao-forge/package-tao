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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * Top level controller
 * All children extenions module should extends the CommonModule to access the shared data
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 */
abstract class tao_actions_CommonModule extends Module {

	/**
	 * The Modules access the models throught the service instance
	 * @var tao_models_classes_Service
	 */
	protected $service = null;
	
	/**
	 * constructor checks if a user is logged in
	 * If you don't want this check, please override the  _isAllowed method to return true
	 */
	public function __construct()
	{
		if(!$this->_isAllowed()){
			throw new tao_models_classes_UserException(__('Access denied. Please renew your authentication!'));
		}
	}
	
	/**
     * @see Module::setView()
     * @param string $identifier view identifier
     * @param string use the views in the specified extension instead of the current extension 
     */
    public function setView($identifier, $extensionID = null)
    {
		if ($extensionID === true) {
			$extensionID = 'tao';
			common_Logger::d('Deprecated use of setView() using a boolean');
		}
    	if(is_null($extensionID) || empty($extensionID)) {
    		$extensionID = Context::getInstance()->getExtensionName();
    	}
		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById($extensionID);
		parent::setView($ext->getConstant('DIR_VIEWS').'templates'.DIRECTORY_SEPARATOR.$identifier);
	}
	
	/**
	 * Retrieve the data from the url and make the base initialization
	 * @return void
	 */
	protected function defaultData()
	{
		$context = Context::getInstance();
		
		$this->setData('extension', context::getInstance()->getExtensionName());
		$this->setData('module', $context->getModuleName());
		$this->setData('action', $context->getActionName());
		
		if($this->hasRequestParameter('uri')) {
			
			// @todo stop using session to manage uri/classUri
			$this->setSessionAttribute('uri', $this->getRequestParameter('uri'));
			
			// inform the client of new classUri
			$this->setData('uri', $this->getRequestParameter('uri'));
		}
		if($this->hasRequestParameter('classUri')) {
		
			// @todo stop using session to manage uri/classUri
			$this->setSessionAttribute('classUri', $this->getRequestParameter('classUri'));
			if (!$this->hasRequestParameter('uri')) {
				$this->removeSessionAttribute('uri');
			}
			
			// inform the client of new classUri
			$this->setData('uri', $this->getRequestParameter('classUri'));
		}
		
		if($this->getRequestParameter('message')){
			$this->setData('message', $this->getRequestParameter('message'));
		}
		if($this->getRequestParameter('errorMessage')){
			$this->setData('errorMessage', $this->getRequestParameter('errorMessage'));
		}
	}
	
	/**
	 * Function to return an user readable error
	 * Does not work with ajax Requests yet
	 * 
	 * @param string $description error to show
	 * @param boolean $returnLink whenever or not to add a return link
	 */
	protected function returnError($description, $returnLink = true) {
		if (tao_helpers_Request::isAjax()) {
			common_Logger::w('Called '.__FUNCTION__.' in an unsupported AJAX context');
			throw new common_Exception($description); 
		} else {
			$this->setData('message', $description);
			$this->setData('returnLink', $returnLink);
			$this->setView('error/user_error.tpl', 'tao');
		}
	}

	/**
	 * Check if the current user is allowed to acces the request
	 * Override this method to allow/deny a request
	 * @return boolean
	 */
	protected function _isAllowed()
	{
		// cannot use $this since the module might be called from diffrent extension
		$context = Context::getInstance();
		$ext	= $context->getExtensionName();
		$module = $context->getModuleName();
		$action = $context->getActionName();
		
		return tao_helpers_funcACL_funcACL::hasAccess($ext, $module, $action);
	}
	
}
?>