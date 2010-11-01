<?php
require_once ('tao/actions/Api.class.php');

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 *
 */
class Server extends Api {

	/**
	 * Initialize the item execution environment 
	 */
	public function initialize(){
		
		$executionEnvironment = array();
		
		if($this->hasRequestParameter('processUri') && 
				$this->hasRequestParameter('itemUri') && 
				$this->hasRequestParameter('testUri') &&
				$this->hasRequestParameter('deliveryUri') ){
			
			$user = $this->userService->subjectService->getCurrentUser();
			if(is_null($user)){
				throw new Exception(__('No user is logged in'));
			}
			
			$process	= new core_kernel_classes_Resource($this->getRequestParameter('processUri'));
			$item 		= new core_kernel_classes_Resource($this->getRequestParameter('itemUri'));
			$test 		= new core_kernel_classes_Resource($this->getRequestParameter('testUri'));
			$delivery 	= new core_kernel_classes_Resource($this->getRequestParameter('deliveryUri'));
			
			
			$executionEnvironment = $this->createExecutionEnvironment($process, $item, $test, $delivery, $user);
		
		}	
		echo json_encode($executionEnvironment);
	}
	
	/**
	 * save data pushed to the server
	 */
	public function save(){
		$saved = false;
		if($this->hasRequestParameter('token')){
			$token = $this->getRequestParameter('token');
			if($this->authenticate($token)){
				
				$executionEnvironment = $this->getExecutionEnvironment();
				
				
				if($this->hasRequestParameter('taoVars')){
					
					//here we save the TAO variables
					
				}
				if($this->hasRequestParameter('userVars')){
					
					//here we save the user variables
					
				}
				
				$saved = true;
			}
		}
		
		echo json_encode(array('saved' => $saved));
	} 
	
}
?>