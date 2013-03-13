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

/**
 * A Service implementation aiming at checking if the server side can talk 'JSON' and
 * receive information from the server to be 'synchronized' with it.
 * Information received are the TAO root URL, ...
 * 
 * Please refer to tao/install/api.php for more information about how to call this service.
 */
class tao_install_services_SyncService extends tao_install_services_Service{
    
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
    	$ext = common_configuration_ComponentFactory::buildPHPExtension('json');
        $report = $ext->check();
                                       
        // We fake JSON encoding for a gracefull response in any case.
        $json = $report->getStatus() == common_configuration_Report::VALID;
        if (!$json){
        	$data = '{"type": "SyncReport", "value": { "json": '. (($json) ? 'true' : 'false') . '}}';
        }
        else{
        	$localesDir = dirname(__FILE__) . '/../../locales';
        	$data = json_encode(array('type' => 'SyncReport', 'value' => array(
        		'json' => true,
        		'rootURL' => self::getRootUrl(),
        		'availableDrivers' => self::getAvailableDrivers(),
        		'availableLanguages' => self::getAvailableLanguages($localesDir)
        	)));
        }
                                   
        $this->setResult(new tao_install_services_Data($data));
    }
    
    /**
     * Computes the root URL of the platform based on the current
     * request.
     * 
     * @return mixed
     */
    private static function getRootUrl(){
    	// Returns TAO ROOT url based on a call to the API.
    	$isHTTPS = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']);
    	$host = $_SERVER['HTTP_HOST'];
    	$uri = $_SERVER['REQUEST_URI'];
    	$currentUrl = (true == $isHTTPS) ? 'https' : 'http' . '://' . $host . $uri;
    	$parsed = parse_url($currentUrl);
    	$port = (empty($parsed['port'])) ? '' : ':' . $parsed['port'];
    	$rootUrl = $parsed['scheme'] . '://' . $parsed['host'] . $parsed['path'] . $port;
    	return str_replace('/tao/install/api.php', '', $rootUrl);
    }

    private static function getAvailableDrivers(){
        $compatibleDrivers = array('pdo_mysql', 'pdo_pgsql', 'pdo_sqlsrv');
        $availableDrivers = array();

        foreach ($compatibleDrivers as $cD){
            $check = common_configuration_ComponentFactory::buildPHPDatabaseDriver($cD);
            $report = $check->check();

            if ($report->getStatus() == common_configuration_Report::VALID){
                $availableDrivers[] = $cD;
            }
        }

        return $availableDrivers;
    }
    
    /**
     * Get the list of available languages in terms of locales in the /tao meta-extension folder.
     * 
     * @param string $localesPath The path to the /locales directory to scan into.
     * @param boolean $sort Sort by alphetical order.
     * @return array an array of languages where keys are language tags and values are language labels in english (EN).
     */
    private static function getAvailableLanguages($localesPath, $sort = true){
    	$languages = array();
    	
    	try{
    		$languages = tao_install_utils_System::getAvailableLocales($localesPath);
    		if (true == $sort){
    			asort($languages);
    		}
    	}
    	catch (Exception $e){
    		// Do nothing and return gracefully.
    	}
    	
    	return $languages;
    }
    
    public static function checkData(tao_install_services_Data $data){
    	// Check data integrity.
        $content = $data->getContent();
        if (!isset($content['type']) || empty($content['type']) || $content['type'] !== 'Sync'){
            throw new InvalidArgumentException("Unexpected type: 'type' must be equal to 'Sync'.");
        }
    }
}
?>