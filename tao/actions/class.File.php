<?php
/**
 * 
 * Controller use for the file upload components
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 */
class tao_actions_File extends tao_actions_CommonModule{
	
	/**
	 * @var string $rootFolder root folder of the copyed files
	 */
	protected $rootFolder = '';

	/**
	 * constructor. Initialize the context
	 */
	public function __construct(){
		parent::__construct();
		$this->rootFolder = sys_get_temp_dir();
	}
	
	
	/**
	 * Upload a file using http and copy it from the tmp dir to the target folder
	 * @return void
	 */
	public function upload(){
		$response = array('uploaded' => false);

		if (!empty($_FILES)) {
			if(isset($_FILES['Filedata'])){
				$response = array_merge($response, $this->uploadFile($_FILES['Filedata'], $_REQUEST['folder'] . '/'));
			}
		}
		$response = json_encode($response);
		print $response; 
		return;
	}
	
	/**
	 * Produce a simple view use to display a file upload form in a popup
	 */
	public function htmlUpload(){
		
		
		if($this->hasRequestParameter('sizeLimit')){
			$this->setData('sizeLimit', (int)($this->getRequestParameter('sizeLimit')));
		}
		else{
			$this->setData('sizeLimit', (int)(ini_get('upload_max_filesize')));
		}
		if($this->hasRequestParameter('target')){
			$this->setData('target', $this->getRequestParameter('target'));
		}
		else{
			$this->setData('target', "#source");
		}
		
		
		$this->setData('accept', '*');
		if($this->hasRequestParameter('fileExt')){
			$accept = '';
			foreach(explode(',', $this->getRequestParameter('fileExt')) as $fileExt){
				$accept .= tao_helpers_File::getMimeType(str_replace('*', 'file', $fileExt));
			}
			$this->setData('accept', $accept);
		}
		
		$response = array(
			'uploaded' 		=> false,
			'data'			=> '',
			'name'			=> '',
			'uploaded_file' => ''
		);
		if (isset($_FILES) && isset($_POST['upload_sent'])) {
			if(isset($_FILES['Filedata'])){
				$response = array_merge($response, $this->uploadFile($_FILES['Filedata'], '/'));
			}
		}
		
		$setLinear = true;
		if($this->hasRequestParameter('format')){
			if($this->getRequestParameter('format') != 'linear'){
				$setLinear = false;
			}
		}
		
		$this->setData('setLinear', $setLinear);
		$this->setData('uploaded', ($response['uploaded'] === true));
		$this->setData('uploadData', $response['data']);
		$this->setData('uploadFile', $response['name']);
		$this->setData('uploadFilePath', $response['uploaded_file']);
		$this->setView('form/html_upload.tpl');	
	}
	
	
	/**
	 * Get, check and move the file uploaded (described in the posetedFile parameter)
	 * 
	 * @param array $postedFile
	 * @param string $folder
	 * @return array $data
	 */
	protected function uploadFile($postedFile, $folder){
		$returnValue = array();
		
		if(isset($postedFile['tmp_name']) && isset($postedFile['name'])){
			$tempFile = $postedFile['tmp_name'];
			$targetPath = tao_helpers_File::concat(array($this->rootFolder,$folder));
			if(tao_helpers_File::securityCheck($targetPath)){
				if(!file_exists($targetPath)){
					mkdir($targetPath);
				}
				$targetFile =  tao_helpers_File::concat(array($targetPath, uniqid().'_'.$postedFile['name']));
				if(move_uploaded_file($tempFile, $targetFile)){
					$returnValue['uploaded'] = true;
					$data = $postedFile;
					$data['type'] =  tao_helpers_File::getMimeType($targetFile);
					$data['uploaded_file'] = $targetFile;
					$returnValue['name'] = $postedFile['name'];
					$returnValue['uploaded_file'] = $targetFile;
					$returnValue['data'] = serialize($data);
				}
			}
		}
		return $returnValue;
	}
}
?>