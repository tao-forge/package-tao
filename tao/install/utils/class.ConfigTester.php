<?php
define('REL_PATH', '../../');
require_once(REL_PATH . 'tao/helpers/class.File.php');
require_once('class.Exception.php');

/**
 * The ConfigTester tester class enables you to test the server configuration
 * 
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 */
class tao_install_utils_ConfigTester{
	
	/**
	 * @var string 
	 */
	private $message = "";
	
	/**
	 * @var int the test status
	 */
	private $status;
	
	const STATUS_UNKNOW  = 1;
	const STATUS_VALID	 = 2;
	const STATUS_INVALID = 3;
	
	
	private static $_types = array(
		'PHP_VERSION',
		'APACHE_MOD',
		'PHP_EXTENSION'
	);
	
	/**
	 * Constructor,
	 * do the test regarding the type in parameter
	 * @param string $type one of the _types to test
	 * @param array $options
	 */
	public function __construct($type, $options){
		if(!in_array(strtoupper($type),self::$_types)){
			throw new tao_install_utils_Exception('Unknow config type : '.$type);
		}
		switch($type){
			case 'PHP_VERSION'	: 
				isset($options['max']) ? $max = $options['max'] : $max = null;
				$this->checkPhpVersion($options['min'], $max); 
				break;
			case 'APACHE_MOD'	: $this->checkApacheMod($options['name']); 		break;
			case 'PHP_EXTENSION': $this->checkPhpExtension($options['name']); 	break;
		}
	}
	
	/**
	 * @return int the current status
	 */
	public function getStatus(){
		return $this->status;
	}
	
	/**
	 * @return string the current message
	 */
	public function getMessage(){
		return $this->message;
	}
	
	/**
	 * Check the PHP Version and update the status and the message
	 * @param string $min version
	 * @param string|null $max version
	 */
	protected function checkPhpVersion($min, $max = null){
		$this->status = self::STATUS_INVALID;
		if(is_null($max)){
			$this->message = "Required PHP version is greater than {$min}. Your version is ".PHP_VERSION.".";
			if(version_compare(PHP_VERSION, $min, 'gt')){
				$this->status = self::STATUS_VALID;
			}
		}
		else{
			$this->message = "Required PHP version is greater than $min and leater than $max. Your version is ".PHP_VERSION.".";
			if(version_compare(PHP_VERSION, $max, 'lt') && version_compare(PHP_VERSION, $min, 'gt')){
				$this->status = self::STATUS_VALID;
			}
		}
	}
	
	/**
	 * Check if a PHP extension is loaded and update the status and the message
	 * @param string $extensionName
	 */
	protected function checkPhpExtension($extensionName){
		switch(strtolower($extensionName)){
			case 'json':
			case 'dom':
				$this->message = 'PHP extension '.strtoupper($extensionName).' is required';
				(extension_loaded(strtolower($extensionName))) ? $this->status = self::STATUS_VALID : $this->status = self::STATUS_INVALID;
				break;
			case 'zip':
			case 'tidy': 
			case 'curl': 
				$this->message = 'PHP extension '.strtoupper($extensionName).' is strongly recomended';
				(extension_loaded(strtolower($extensionName))) ? $this->status = self::STATUS_VALID : $this->status = self::STATUS_INVALID;
				break;
			case 'gd':
				$this->message = 'PHP extension GD is optionnal';
				(extension_loaded('gd')) ? $this->status  = self::STATUS_VALID : $this->status  = self::STATUS_INVALID;
				break;
			case 'suhosin':
				$this->message 	= "Suhosin patch is optionnal. But if you use it, ".
								"set the directives suhosin.request.max_varname_length and suhosin.post.max_name_length to 128.";
				(extension_loaded('suhosin')) ? $this->status = self::STATUS_VALID : $this->status = self::STATUS_INVALID;
				break;
			case 'mysql':
			case 'mysqli':
			case 'pdo':
			case 'pdo_mysql':
				$this->message = 'PHP extension mysql, mysqli or pdo is required';
				(extension_loaded(strtolower($extensionName))) ? $this->status  = self::STATUS_VALID : $this->status  = self::STATUS_INVALID;
				break;
			default :
				$this->message 	= "Unable to determine the status of PHP extension {$extensionName}.";
				$this->status	= self::STATUS_UNKNOW;
				break;	
		}
	}
	
	/**
	 * Check if an Apache module is loaded and update the status and the message
	 * @param string $moduleName
	 */
	protected function checkApacheMod($moduleName){
		
		switch(strtolower($moduleName)){
			case 'rewrite' : 
				$infos = tao_install_utils_System::getInfos();
				$this->status  = self::STATUS_UNKNOW;
				$this->message = '';
				//check if the url rewriting is enabled by sending a cUrl request
				if(function_exists('curl_init')){
					$url = tao_helpers_File::concat(array($infos['host'],$infos['folder'], '/test'));
					
					$curlHandler = curl_init();
					curl_setopt($curlHandler, CURLOPT_URL, $url);
					curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
					curl_exec($curlHandler);
					if(curl_errno($curlHandler) == 0){
						$this->status =  self::STATUS_INVALID;
						$code = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
						if($code == 302){
							$this->status =  self::STATUS_VALID;
						}
						else{
							$this->message = "You need to activate mod_rewrite on your apache instance.";
						}
					}
					else{
						$this->message = "Unable to test the module, an error ocurred during the process.";
					}
					curl_close($curlHandler);
				}
				else{
					$this->message = "cUrl is required to test the mod rewrite.";
				}
				break;
			default :
				$this->message 	= "Unable to determine the status of apache module {$moduleName}.";
				$this->status	= self::STATUS_UNKNOW;
				break;
		}
	}
	
}
?>
