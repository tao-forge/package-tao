<?php

error_reporting(E_ALL);

/**
 * TAO - tao/actions/form/class.VersionedFile.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 23.04.2012, 15:51:09 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F7C-includes begin
// section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F7C-includes end

/* user defined constants */
// section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F7C-constants begin
// section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F7C-constants end

/**
 * Short description of class tao_actions_form_VersionedFile
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class tao_actions_form_VersionedFile
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute ownerInstance
     *
     * @access protected
     * @var Resource
     */
    protected $ownerInstance = null;

    /**
     * Short description of attribute property
     *
     * @access protected
     * @var Property
     */
    protected $property = null;

    /**
     * Short description of attribute versionedFile
     *
     * @access public
     * @var File
     */
    public $versionedFile = null;

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F7E begin
        
		if(!isset($this->options['instanceUri'])){
    		throw new Exception(__('Option instanceUri is not an option !!'));
    	}
    	if(!isset($this->options['ownerUri'])){
    		throw new Exception(__('Option ownerUri is not an option !!'));
    	}
    	if(!isset($this->options['propertyUri'])){
    		throw new Exception(__('Option propertyUri is not an option !!'));
    	}
    	
    	$this->ownerInstance = new core_kernel_classes_Resource($this->options['ownerUri']);
    	$this->property = new core_kernel_classes_Property($this->options['propertyUri']);
    	$this->versionedFile = new core_kernel_versioning_File($this->options['instanceUri']);
		
    	$this->form = tao_helpers_form_FormFactory::getForm('versioned_file');
    	
		$actions = tao_helpers_form_FormFactory::getElement('save', 'Free');
		$value = '';
		$value .=  "<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/save.png' /> ".__('Save')."</a>";
		$value .=  "<a href='#' class='form-reverter'><img src='".TAOBASE_WWW."/img/revert.png' /> ".__('Return')."</a>";
		$actions->setValue($value);
		
    	$this->form->setActions(array($actions), 'top');
    	$this->form->setActions(array($actions), 'bottom');
    	
        // section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F7E end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F80 begin
    	
    	$versioned = $this->versionedFile->isVersioned();
		$freeFilePath = isset($this->options['freeFilePath'])?(bool)$this->options['freeFilePath']:false;
    	
		/*
		 * 1. BUILD FORM
		 */
    	
		// File Content
    	$contentGroup = array();
    	function return_bytes($val) {
			$val = trim($val);
			$last = strtolower($val[strlen($val)-1]);
			switch($last) {
				// Le modifieur 'G' est disponible depuis PHP 5.1.0
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}

			return $val;
		}
		$browseElt = tao_helpers_form_FormFactory::getElement("file_import", "AsyncFile");
		$browseElt->addValidator(tao_helpers_form_FormFactory::getValidator('FileSize', array('max' => return_bytes(ini_get('post_max_size')))));
    	//make the content compulsory if it does not exist already
		if(!$versioned){
			$browseElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
			$browseElt->setDescription(__("Upload the file to version"));
		}
		else{
			$browseElt->setDescription(__("Upload a new content"));
		}
		$this->form->addElement($browseElt);
		array_push($contentGroup, $browseElt->getName());
    	
		$commitMessageElt = tao_helpers_form_FormFactory::getElement("commit_message", "Textarea");
		$commitMessageElt->setDescription(__("Commit message : "));
		$this->form->addElement($commitMessageElt);
		array_push($contentGroup, $commitMessageElt->getName());
		
		// if the file is already versioned add a way to download it
		if($versioned){
			
			$downloadUrl = $this->getDownloadUrl($this->versionedFile);
			
			$downloadFileElt = tao_helpers_form_FormFactory::getElement("file_download", 'Free');
			$downloadFileElt->setValue("<a href='$downloadUrl' class='blink' target='_blank'><img src='".TAOBASE_WWW."img/document-save.png' alt='Download versioned file' class='icon'  /> ".__('Download content')."</a>");
			$this->form->addElement($downloadFileElt);
			array_push($contentGroup, $downloadFileElt->getName());
			
			$deleteFileElt0 = tao_helpers_form_FormFactory::getElement("file_delete0", 'Free');
			$deleteFileElt0->setValue("<a id='delete-versioned-file' href='#' class='blink' target='_blank'><img src='".TAOBASE_WWW."img/edit-delete.png' alt='Delete versioned file' class='icon'  /> ".__('Remove content')."</a>");
			$this->form->addElement($deleteFileElt0);
			array_push($contentGroup, $deleteFileElt0->getName());
			
			$deleteFileElt = tao_helpers_form_FormFactory::getElement("file_delete", 'Hidden');
			$deleteFileElt->setValue(0);
			$this->form->addElement($deleteFileElt);
			array_push($contentGroup, $deleteFileElt->getName());
		}
		
		$this->form->createGroup('file', 'Content', $contentGroup);
		
    	//File Meta
    	$fileNameElt = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILENAME), $versioned ? 'Label' : 'Textbox');
		$fileNameElt->setDescription(__("File name"));
		if(!$versioned){ 
			$fileNameElt->addValidator(tao_helpers_form_FormFactory::getValidator('FileName'));
		}
		$this->form->addElement($fileNameElt);
		
		//file path element to be added or not:
		$filePathElt = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_VERSIONEDFILE_FILEPATH), $freeFilePath?'Textbox':'Hidden');
		$filePathElt->setDescription(__("File path"));
		$this->form->addElement($filePathElt);
		
		$versionedRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
		$repositoryEltOptions = array();
		foreach($versionedRepositoryClass->getInstances() as $repository){
			$repositoryEltOptions[tao_helpers_Uri::encode($repository->uriResource)] = $repository->getLabel();
		}
		$fileRepositoryElt = tao_helpers_form_FormFactory::getElement(tao_helpers_Uri::encode(PROPERTY_VERSIONEDFILE_REPOSITORY), $versioned ? 'Label' : 'Radiobox');
		$fileRepositoryElt->setDescription(__("File repository"));
		if(!$versioned){
			$fileRepositoryElt->setOptions($repositoryEltOptions);
		}
		$fileRepositoryElt->addValidator(tao_helpers_form_FormFactory::getValidator('NotEmpty'));
		$this->form->addElement($fileRepositoryElt);
    	
    	$this->form->createGroup('meta', 'Description', array(
			$fileNameElt->getName(),
			$filePathElt->getName(),
			$fileRepositoryElt->getName()
		));
    	
		// File Revision
		if($versioned){
			$fileVersionOptions = array();
			$history = $this->versionedFile->gethistory();
			$countHistory = count($history);
			foreach($history as $i => $revision){
				$date = new DateTime($revision['date']);
				$fileVersionOptions[$countHistory-$i] = $countHistory-$i . '. ' . $revision['msg'] . ' [' . $revision['author'] .' / ' . $date->format('Y-m-d H:i:s') . '] ';
			}
			
			$fileRevisionElt = tao_helpers_form_FormFactory::getElement('file_version', 'Radiobox');
			$fileRevisionElt->setDescription(__("File revision"));
			$fileRevisionElt->setOptions($fileVersionOptions);
			$fileRevisionElt->setValue($countHistory);
			$this->form->addElement($fileRevisionElt);
			$this->form->createGroup('revision', 'Version', array($fileRevisionElt->getName()));
		}
		
		/*
		 * 2. HIDDEN FIELDS
		 */
		//add an hidden elt for the property uri (Property associated to the owner instance)
		$propertyUriElt = tao_helpers_form_FormFactory::getElement("propertyUri", "Hidden");
		$propertyUriElt->setValue(tao_helpers_Uri::encode($this->property->uriResource));
		$this->form->addElement($propertyUriElt);
		
		//add an hidden elt for the instance Uri
		//usefull to render the revert action
		$instanceUriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
		$instanceUriElt->setValue(tao_helpers_Uri::encode($this->ownerInstance->uriResource));
		$this->form->addElement($instanceUriElt);
		
		/*
		 * 3. FILL THE FORM
		 */
    	if($versioned){
    		
			$fileNameValue = $this->versionedFile->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_FILE_FILENAME));
			if(!empty($fileNameValue)){
				$fileNameElt->setValue((string) $fileNameValue);
			}
		
			$filePathValue = $this->versionedFile->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_FILEPATH));
			if(!empty($filePathValue)){
				$filePathElt->setValue((string) $filePathValue);
			}
		
			$repositoryValue = $this->versionedFile->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_REPOSITORY));
			if(!empty($repositoryValue)){
				$fileRepositoryElt->setValue($repositoryValue->uriResource);
			}
			
			$history = $this->versionedFile->gethistory();
			$versionElt = $this->form->getElement('file_version');
			$versionElt->setValue(count($history));
			
    	}else{
			
			if(!$freeFilePath){
				$filePathElt->setValue($this->getDefaultFilePath());
			}else{
				$filePathElt->setValue('/');
			}
			
			$defaultRepo = $this->getDefaultRepository();
			if(!is_null($defaultRepo)){
				$fileRepositoryElt->setValue($defaultRepo->uriResource);
			}
		}
    	
        // section 127-0-1-1-234d8e6a:13300ee5308:-8000:0000000000003F80 end
    }

    /**
     * Override the validate method of the form container to validate 
     * linked elements
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return boolean
     */
    public function validate()
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--485428cc:133267d2802:-8000:000000000000409D begin
        
    	if($this->form->isSubmited()){
			
    		if($this->versionedFile->isVersioned()){
    			return true;
    		}
			
	    	$fileNameElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_FILE_FILENAME));
	    	$fileName = !is_null($fileNameElt)?$fileNameElt->getValue():'';
	    	
	    	$filePathElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_VERSIONEDFILE_FILEPATH));
	    	$filePath = $filePathElt->getValue();
	    	
	    	$fileRepositoryElt = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_VERSIONEDFILE_REPOSITORY));
	    	$fileRepository = tao_helpers_Uri::decode($fileRepositoryElt->getValue());
	    	
	    	 //check if a resource with the same path exists yet in the repository
	        $clazz = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDFILE);
	        $options = array('like' => false, 'recursive' => true);
			$propertyFilter = array(
				PROPERTY_FILE_FILENAME => $fileName,
				PROPERTY_VERSIONEDFILE_FILEPATH => $filePath,
				PROPERTY_VERSIONEDFILE_REPOSITORY => $fileRepository
			);
	        $sameNameFiles = $clazz->searchInstances($propertyFilter, $options);
	        if(!empty($sameNameFiles)){
	        	$sameFileResource = array_pop($sameNameFiles);
	        	$sameFile = new core_kernel_versioning_File($sameFileResource->uriResource);
	        	
	        	$this->form->valid = false;
	        	$this->form->error = __('A similar resource has already been versioned').' ('.$sameFile->getAbsolutePath().')';
	        }
    	}
    	
        // section 127-0-1-1--485428cc:133267d2802:-8000:000000000000409D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getDownloadUrl
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getDownloadUrl()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AF2 begin
		
		if(!is_null($this->ownerInstance)){
			$returnValue = _url('downloadFile', 'File', 'tao', array('uri' => tao_helpers_Uri::encode($this->ownerInstance->uriResource)));
		}
		
        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AF2 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getDefaultFilePath
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return string
     */
    public function getDefaultFilePath()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AF4 begin
		
		$returnValue = tao_helpers_Uri::getUniqueId($this->ownerInstance->uriResource).'/'.tao_helpers_Uri::getUniqueId($this->property->uriResource);
			
        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AF4 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getDefaultRepository
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return core_kernel_versioning_Repository
     */
    public function getDefaultRepository()
    {
        $returnValue = null;

        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AF6 begin
        // section 127-0-1-1-34f65b5e:136df48a4e6:-8000:0000000000004AF6 end

        return $returnValue;
    }

} /* end of class tao_actions_form_VersionedFile */

?>