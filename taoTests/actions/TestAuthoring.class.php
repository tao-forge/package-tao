<?php
/**
 * DeliveryAuthoring Controller provide actions to edit a delivery
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoDelivery
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class TestAuthoring extends ProcessAuthoring {
	
	public function __construct(){
		
		parent::__construct();
		
		//the service is initialized by default
		$this->service = tao_models_classes_ServiceFactory::get('taoTests_models_classes_TestAuthoringService');
		$this->defaultData();
	}
}	