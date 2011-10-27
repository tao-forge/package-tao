<?php
/**
 * The ConfigWriter class enables you to create config file from samples
 * and to write the constants inside. 
 * 
 * @author Bertrand CHEVRIER <bertrand.chevrier@tudor.lu>
 *
 */
class tao_install_utils_ConfigWriter{
	
	/**
	 * @var string the path to the sample file
	 */
	protected $sample;
	
	/**
	 * @var string the path to the real config file
	 */
	protected $file; 
	
	/**
	 * instantiate by config file
	 * @param string $sample
	 * @param string $file
	 * @throws tao_install_utils_Exception
	 */
	public function __construct($sample, $file)
	{
		if(!file_exists($sample)){
			throw new tao_install_utils_Exception('Unable to find sample config '.$sample);
		}
		$this->sample 	= $sample;
		$this->file 	= $file;
	}
	
	/**
	 * Create the config file from the sample
	 * @throws tao_install_utils_Exception
	 */
	public function createConfig()
	{
		
		//common checks
		if(!is_writable(dirname($this->file))){
			throw new tao_install_utils_Exception('Unable to create configuration file. Please set write permission to : '.dirname($this->file));
		}
		if(file_exists($this->file) && !is_writable($this->file)){
			throw new tao_install_utils_Exception('Unable to create the configuration file. Please set the write permissions to : '.$this->file);
		}
		if(!is_readable($this->sample)){
			throw new tao_install_utils_Exception('Unable to read the sample configuration. Please set the read permissions to : '.$this->sample);
		}
		
		if(!copy($this->sample, $this->file)){
			throw new tao_install_utils_Exception('Unable to copy the sample configuration to : '.$this->file);
		}
	}
	
	/**
	 * Write the constants into the config file
	 * @param array $constants the list of constants to write (the key is the name of the constant)
	 * @throws tao_install_utils_Exception
	 */
	public function writeConstants(array $constants)
	{
		
		//common checks
		if(!file_exists($this->file)){
			throw new tao_install_utils_Exception("Unable to write constants: $this->file don't exists!");
		}
		if(!is_readable($this->file) || !is_writable($this->file)){
			throw new tao_install_utils_Exception("Unable to write constants: $this->file must have read and write permissions!");
		}
		
		$content = file_get_contents($this->file);
		if(!empty($content)){
			foreach($constants as $name => $val){
				
				if(is_string($val)){
					$val = addslashes((string)$val);
					$content = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1,\''.$val.'\');',$content);
				}
				else if(is_bool($val)){
					($val === true) ? $val = 'true' : $val = 'false';
					$content = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1, '.$val.');',$content);
				}
				else if(is_numeric($val)){
					$content = preg_replace('/(\''.$name.'\')(.*?)$/ms','$1, '.$val.');',$content);
				}
			}
			file_put_contents($this->file, $content);
		}
	}
	
	/**
	 * Write the constants into a Javascript config file
	 * @param array $variables the list of variables to write (the key is the name of the var)
	 * @throws tao_install_utils_Exception
	 */
	public function writeJsVariable(array $variables, $lineBeginWith = "var")
	{
		//common checks
		if(!file_exists($this->file)){
			throw new tao_install_utils_Exception("Unable to write variables: $this->file don't exists!");
		}
		if(!is_readable($this->file) || !is_writable($this->file)){
			throw new tao_install_utils_Exception("Unable to write variables: $this->file must have read and write permissions!");
		}
		
		$lines 	= file($this->file);
		if($lines !== false){
			$data = file_get_contents($this->file);
			$changes = 0;
			foreach($lines as $line){
				foreach($variables as $key => $value){
					if(is_string($value)){
						$value = "'$value'";
					}
					else if(is_bool($value)){
						($value === true) ? $value = 'true' : $value = 'false';
					}
					
					if(preg_match("/^\s?$lineBeginWith\s?$key\s?=\s?/i", trim($line))){
						$data = str_replace(trim($line), "$lineBeginWith $key = $value;", $data);
						$changes++;
					}
				}
			}
			if($changes > 0){
				file_put_contents($this->file, $data);
			}
		}
	}
	
}
?>