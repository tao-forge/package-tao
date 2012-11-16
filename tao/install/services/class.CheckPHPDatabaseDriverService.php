<?php

/**
 * A Service implementation aiming at checking the existence and the availability of
 * a Database Driver on the host system.
 * 
 * Please refer to tao/install/api.php for more information about how to call this service.
 */
class tao_install_services_CheckPHPDatabaseDriverService 
	extends tao_install_services_Service
	implements tao_install_services_CheckService 
	{
    
    /**
     * Creates a new instance of the service.
     * @param tao_install_services_Data $data The input data to be handled by the service.
     * @throws InvalidArgumentException If the input data structured is malformed or is missing data.
     */
    public function __construct(tao_install_services_Data $data){
        parent::__construct($data);
    }
    
    /**
     * Executes the main logic of the service.
     * @return tao_install_services_Data The result of the service execution.
     */
    public function execute(){
        $ext = self::buildComponent($this->getData());
        $report = $ext->check();                       
        $this->setResult(self::buildResult($this->getData(), $report, $ext));
    }
    
    public static function checkData(tao_install_services_Data $data){
     	// Check data integrity.
        $content = json_decode($data->getContent(), true);
        if (!isset($content['type']) || empty($content['type']) || $content['type'] !== 'CheckPHPDatabaseDriver'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'CheckPHPDatanaseDriver'.");
        }
        else if (!isset($content['value']) || empty($content['value'])){
            throw new InvalidArgumentException("Missing data: 'value' must be provided.");
        }
        else if (!isset($content['value']['id']) || empty($content['value']['id'])){
        	throw new InvalidArgumentException("Missing data: 'id' must be provided.");
        }
        else if (!isset($content['value']['name'])){
            throw new InvalidArgumentException("Missing data: 'name' must be provided.");
        }
        else if (!isset($content['value']['optional'])){
            throw new InvalidArgumentException("Missing data: 'optional' must be provided.");
        }
    }
    
    public static function buildComponent(tao_install_services_Data $data){
    	$content = json_decode($data->getContent(), true);
        $extensionName = $content['value']['name'];
        $optional = ($content['value']['optional'] == 'true') ? true : false;
        $ext = new common_configuration_PHPDatabaseDriver(null, null, $extensionName, $optional);
        
        return $ext;
    }
    
    public static function buildResult(tao_install_services_Data $data,
									   common_configuration_Report $report,
									   common_configuration_Component $component){

		$content = json_decode($data->getContent(), true);
        $id = $content['value']['id'];
        
        $data = array('type' => 'PHPDatabaseDriverReport',
                      'value' => array('status' => $report->getStatusAsString(),
                                       'message' => $report->getMessage(),
                                       'optional' => $component->isOptional(),
                                       'name' => $component->getName(),
        							   'id' => $id));
        
        return new tao_install_services_Data(json_encode($data));
	}
}
?>