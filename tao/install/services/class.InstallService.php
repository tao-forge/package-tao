<?php
/**
 * A Service implementation aiming at installing the software.
 * 
 * Please refer to tao/install/api.php for more information about how to call this service.
 */
class tao_install_services_InstallService extends tao_install_services_Service{
    
    /**
     * Creates a new instance of the service.
     * @param tao_install_services_Data $data The input data to be handled by the service.
     * @throws InvalidArgumentException If the input data structured is malformed or is missing data.
     */
    public function __construct(tao_install_services_Data $data){
        parent::__construct($data);
        
    	$content = json_decode($this->getData()->getContent(), true);
        if (!isset($content['type']) || empty($content['type']) || $content['type'] != 'Install'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'Install'.");
        }
        else if (!isset($content['value']) || empty($content['value'])){
            throw new InvalidArgumentException("Missing data: 'value' must be provided.");
        }
        else if (!isset($content['value']['db_host']) || empty($content['value']['db_host'])){
        	throw new InvalidArgumentException("Missing data: 'db_host' must be provided.");
        }
    	else if (!isset($content['value']['db_user']) || empty($content['value']['db_user'])){
        	throw new InvalidArgumentException("Missing data: 'db_user' must be provided.");
        }
    	else if (!isset($content['value']['db_host']) || empty($content['value']['db_host'])){
        	throw new InvalidArgumentException("Missing data: 'db_host' must be provided.");
        }
        else if (!isset($content['value']['db_pass'])){
        	throw new InvalidArgumentException("Missing data: 'db_pass' must be provided.");
        }
    	else if (!isset($content['value']['db_driver']) || empty($content['value']['db_driver'])){
        	throw new InvalidArgumentException("Missing data: 'db_driver' must be provided.");
        }
    	else if (!isset($content['value']['db_name']) || empty($content['value']['db_name'])){
        	throw new InvalidArgumentException("Missing data: 'db_name' must be provided.");
        }
    	else if (!isset($content['value']['module_namespace']) || empty($content['value']['module_namespace'])){
        	throw new InvalidArgumentException("Missing data: 'db_host' must be provided.");
        }
    	else if (!isset($content['value']['module_url']) || empty($content['value']['module_url'])){
        	throw new InvalidArgumentException("Missing data: 'module_url' must be provided.");
        }
    	else if (!isset($content['value']['module_lang']) || empty($content['value']['module_lang'])){
        	throw new InvalidArgumentException("Missing data: 'module_lang' must be provided.");
        }
    	else if (!isset($content['value']['module_mode']) || empty($content['value']['module_mode'])){
        	throw new InvalidArgumentException("Missing data: 'module_mode' must be provided.");
        }
    	else if (!isset($content['value']['import_local']) || ($content['value']['import_local'] !== false && $content['value']['import_local'] !== true)){
        	throw new InvalidArgumentException("Missing data: 'import_local' must be provided.");
        }
    	else if (!isset($content['value']['user_login']) || empty($content['value']['user_login'])){
        	throw new InvalidArgumentException("Missing data: 'user_login' must be provided.");
        }
    	else if (!isset($content['value']['user_pass1']) || empty($content['value']['user_pass1'])){
        	throw new InvalidArgumentException("Missing data: 'user_pass1' must be provided.");
        }
    }
    
    /**
     * Executes the main logic of the service.
     * @return tao_install_services_Data The result of the service execution.
     */
    public function execute(){
        $content = json_decode($this->getData()->getContent(), true);
		
		//instantiate the installator
		try{
			$installer = new tao_install_Installator(array(
				'root_path' 	=> TAO_INSTALL_PATH,
				'install_path'	=> dirname(__FILE__) . '/../../install'
			));
			
			$installer->install($content['value']);
			
			$report = array('type' => 'InstallReport',
							'value' => array('status' => 'valid',
											 'message' => "Installation successful."));
			$this->setResult(new tao_install_services_Data(json_encode($report)));
		}
		catch(Exception $e){
			$report = array('type' => 'InstallReport',
							'value' => array('status' => 'valid',
											 'message' => $e->getMessage()));
			$this->setResult(new tao_install_services_Data(json_encode($report)));
		}
    }
}
?>