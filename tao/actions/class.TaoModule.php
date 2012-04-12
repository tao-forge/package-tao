<?php

/**
 * The TaoModule is an abstract controller, 
 * the tao children extensions Modules should extends the TaoModule to beneficiate the shared methods.
 * It regroups the methods that can be applied on any extension (the rdf:Class managment for example)
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 */
abstract class tao_actions_TaoModule extends tao_actions_CommonModule {

	/**
	 * Check if the current user is allowed to acces the request
	 * Override this method to allow/deny a request
	 * @return boolean
	 */
	protected function _isAllowed()
	{ 
		//if a user is logged in
		if( parent::_isAllowed()){
		
			//check the user role again, to prevent login with a session from a privileged extension
	        $roleService = tao_models_classes_RoleService::singleton();
	        $userService = wfEngine_models_classes_UserService::singleton();
	        if(tao_helpers_Context::check('STANDALONE_MODE')){
	        	if($roleService->checkUserRole($userService->getCurrentUser(), new core_kernel_classes_Class(CLASS_ROLE_WORKFLOWUSERROLE))){
	        		return true;
	        	}
	        }
	       	
	        if($roleService->checkUserRole($userService->getCurrentUser())){
        		return true;
        	}
	       	
		}
		return false;
	}
	
/*
 * Shared Methods
 */
	
	/**
	 * get the current item class regarding the classUri' request parameter
	 * @return core_kernel_classes_Class the item class
	 */
	protected function getCurrentClass()
	{
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($classUri) || empty($classUri)){
			
			$clazz = null;
			$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
			$resource = new core_kernel_classes_Resource($uri);
			foreach($resource->getType() as $type){
				$clazz = $type;
				break;
			}
			if(is_null($clazz)){
				throw new Exception("No valid class uri found");
			}
			$returnValue = $clazz;
		}
		else{
			$returnValue = new core_kernel_classes_Class($classUri);
		}
		
		return $returnValue;
	}
	
	/**
	 *  ! Please override me !
	 * get the current instance regarding the uri and classUri in parameter
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentInstance()
	{
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$instance = $this->service->getOneInstanceBy($clazz, $uri, 'uri');
		if(is_null($instance)){
			throw new Exception("No instance found for the uri {$uri}");
		}
		
		return $instance;
	}

	/**
	 * get the main class
	 * @return core_kernel_classes_Classes
	 */
	protected abstract function getRootClass();

	/**
	 * Edit a class 
	 * Manage the form submit by saving the class
	 * @param core_kernel_classes_Class    $clazz
	 * @param core_kernel_classes_Resource $resource
	 * @return tao_helpers_form_Form the generated form
	 */
	protected function editClass(core_kernel_classes_Class $clazz, core_kernel_classes_Resource $resource, core_kernel_classes_Class $topClass = null)
	{
	
		$propMode = 'simple';
		if($this->hasSessionAttribute('property_mode')){
			$propMode = $this->getSessionAttribute('property_mode');
		}
		
		$options = array('property_mode' => $propMode);
		if(!is_null($topClass)){
			$options['topClazz'] = $topClass->uriResource;
		}
		$formContainer = new tao_actions_form_Clazz($clazz, $resource, $options);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
			
				$classValues = array();
				$propertyValues = array();
				
				//in case of deletion of just added properties
				foreach($_POST as $key => $value){
					if(preg_match("/^propertyUri/", $key)){
						$propNum = str_replace('propertyUri', '', $key);
						if(!isset($propertyValues[$propNum])){
							$propertyValues[$propNum] = array();
						}
					}
				}
				
				//create a table of property models
				foreach($myForm->getValues() as $key => $value){
					if(preg_match("/^class_/", $key)){
						$classKey =  tao_helpers_Uri::decode(str_replace('class_', '', $key));
						$classValues[$classKey] =  tao_helpers_Uri::decode($value);
					}
					if(preg_match("/^property_/", $key)){
						
						$posted = false;
						if(isset($_POST[$key])){
							$posted = true;
						}
						else{
							$expression = "/^".preg_quote($key, "/")."_[0-9]+/";
							foreach($_POST as $postKey => $postValue){
								if(preg_match($expression, $postKey)){
									$posted = true;
									break;
								}
							}
						}
						if($posted){
							$pkey = str_replace('property_', '', $key);
							$propNum = substr($pkey, 0, strpos($pkey, '_'));
							$propKey = tao_helpers_Uri::decode(str_replace($propNum.'_', '', $pkey));
							$propertyValues[$propNum][$propKey] = ((is_array($value)) ? array_map(array('tao_helpers_Uri', 'decode'), $value) : tao_helpers_Uri::decode($value));
						}
						else{
							
							$pkey = str_replace('property_', '', $key);
							$propNum = substr($pkey, 0, strpos($pkey, '_'));
							if(!isset($propertyValues[$propNum])){
								$propertyValues[$propNum] = array();
							}
						}
					}
				}
				
				$clazz = $this->service->bindProperties($clazz, $classValues);
				$propertyMap = tao_helpers_form_GenerisFormFactory::getPropertyMap();
				foreach($propertyValues as $propNum => $properties){
					if(isset($_POST['propertyUri'.$propNum]) && count($properties) == 0){
						
						//delete property mode
						foreach($clazz->getProperties() as $classProperty){
							if($classProperty->uriResource == tao_helpers_Uri::decode($_POST['propertyUri'.$propNum])){
								
								//delete property and the existing values of this property
								if($classProperty->delete(true)){
									$myForm->removeGroup("property_".$propNum);
									break;
								}
							}
						}
					}
					else{
						
						if($propMode == 'simple'){
							$type = $properties['type'];
							$range = (isset($properties['range']) ? $properties['range'] : RDFS_LABEL);
							unset($properties['type']);
							unset($properties['range']);
							
							if(isset($propertyMap[$type])){
								$properties[PROPERTY_WIDGET] = $propertyMap[$type]['widget'];
								if(is_null($propertyMap[$type]['range'])){
									$properties[RDFS_RANGE] = $range;
								}
								else{
									$properties[RDFS_RANGE] = $propertyMap[$type]['range'];
								}
							}
						}
						$property = new core_kernel_classes_Property(tao_helpers_Uri::decode($_POST['propertyUri'.$propNum]));
						$this->service->bindProperties($property, $properties);
						
						$myForm->removeGroup("property_".$propNum);
						
						//instanciate a property form
						$propFormClass = 'tao_actions_form_'.ucfirst(strtolower($propMode)).'Property';
						if(!class_exists($propFormClass)){
							$propFormClass = 'tao_actions_form_SimpleProperty';
						}
						
						$propFormContainer = new $propFormClass($clazz, $property, array('index' => $propNum));
						$propForm = $propFormContainer->getForm();
						
						//and get its elements and groups
						$myForm->setElements(array_merge($myForm->getElements(), $propForm->getElements()));
						$myForm->setGroups(array_merge($myForm->getGroups(), $propForm->getGroups()));
						
						unset($propForm);
						unset($propFormContainer);
					}
					//reload form
				}
			}
		}
		return $myForm;
	}
	
	
	
/*
 * Actions
 */
 
	
	/**
	 * Main action
	 * @return void
	 */
	public function index()
	{
		
		if($this->getData('reload') == true){
			$this->removeSessionAttribute('uri');
			$this->removeSessionAttribute('classUri');
		}
		$this->setView('index.tpl', false);
	}
	
	/**
	 * Render json data from the current ontology root class
	 * @return void
	 */
	public function getOntologyData()
	{
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$options = array(
			'subclasses' => true, 
			'instances' => true, 
			'highlightUri' => '', 
			'labelFilter' => '', 
			'chunk' => false,
			'offset' => 0,
			'limit' => 0
		);
		
		if($this->hasRequestParameter('filter')){
			$options['labelFilter'] = $this->getRequestParameter('filter');
		}
		
		if($this->hasSessionAttribute("showNodeUri")){
			$options['highlightUri'] = $this->getSessionAttribute("showNodeUri");
			$this->removeSessionAttribute("showNodeUri");
		}
		if($this->hasRequestParameter('hideInstances')){
			if((bool) $this->getRequestParameter('hideInstances')){
				$options['instances'] = false;
			}
		}
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			$options['chunk'] = true;
		}
		else{
			$clazz = $this->getRootClass();
		}
		if($this->hasRequestParameter('offset')){
			$options['offset'] = $this->getRequestParameter('offset');
		}
		if($this->hasRequestParameter('limit')){
			$options['limit'] = $this->getRequestParameter('limit');
		}
		if($this->hasRequestParameter('subclasses')){
			$options['subclasses'] = $this->getRequestParameter('subclasses');
		}
		
		echo json_encode($this->service->toTree($clazz, $options));
	}
	
	
	
	/**
	 * Add an instance of the selected class
	 * @return void
	 */
	public function addInstance()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$response = array();
		
		$clazz = $this->getCurrentClass();
		$label = $this->service->createUniqueLabel($clazz);
		
		$instance = $this->service->createInstance($clazz, $label);
		
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
			$response = array(
				'label'	=> $instance->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
			);
		}
		echo json_encode($response);
	}
	
	/**
	 * Edit property instance
	 * @return void
	 */
	public function editPropertyInstance()
	{
		if(!$this->hasRequestParameter('ownerUri') || !$this->hasRequestParameter('ownerClassUri')
			|| !$this->hasRequestParameter('propertyUri'))
		{
			var_dump('variables missing');
		} 
		else{
			
			$ownerClassUri = tao_helpers_Uri::decode($this->getRequestParameter('ownerClassUri'));
			$ownerUri = tao_helpers_Uri::decode($this->getRequestParameter('ownerUri'));
			$propertyUri = tao_helpers_Uri::decode($this->getRequestParameter('propertyUri'));
			
			$ownerInstance = new core_kernel_classes_Resource($ownerUri);
			$ownerClass = new core_kernel_classes_Class($ownerClassUri);
			$property = new core_kernel_classes_Property($propertyUri);
			$propertyRange = $property->getRange();
			
			// If the file does not exist, create it
			$instance = $ownerInstance->getOnePropertyValue($property);
			if(is_null($instance)){
				$instance = $propertyRange->createInstance();
				$ownerInstance->setPropertyValue($property, $instance->uriResource);
			}
			
			$formContainer = new tao_actions_form_Instance($propertyRange, $instance);
			$myForm = $formContainer->getForm();
			
			// Add hidden elements to the form
			$ownerClassUriElt = tao_helpers_form_FormFactory::getElement("ownerClassUri", "Hidden");
			$ownerClassUriElt->setValue(tao_helpers_Uri::encode($ownerClassUri));
			$myForm->addElement($ownerClassUriElt);
			
			$ownerUriElt = tao_helpers_form_FormFactory::getElement("ownerUri", "Hidden");
			$ownerUriElt->setValue(tao_helpers_Uri::encode($ownerUri));
			$myForm->addElement($ownerUriElt);
			
			$propertyUriElt = tao_helpers_form_FormFactory::getElement("propertyUri", "Hidden");
			$propertyUriElt->setValue(tao_helpers_Uri::encode($propertyUri));
			$myForm->addElement($propertyUriElt);
			
			//add an hidden elt for the instance Uri
			//usefull to render the revert action
			$instanceUriElt = tao_helpers_form_FormFactory::getElement('uri', 'Hidden');
			$instanceUriElt->setValue(tao_helpers_Uri::encode($ownerInstance->uriResource));
			$myForm->addElement($instanceUriElt);
			
			if($myForm->isSubmited()){
				if($myForm->isValid()){
					
					$properties = $myForm->getValues();
					$versionedContentInstance = $this->service->bindProperties($instance, $properties);
					
					$this->setData('message', __($propertyRange->getLabel().' saved'));
					$this->setData('reload', true);
				}
			}
		}
		
		$this->setData('formTitle', __('Manage item versioned content'));
		$this->setData('myForm', $myForm->render());
		
		$this->setView('form_content.tpl');
	}
	
	/**
	 * Edit a versioned file
	 * @todo refactor
	 */
	public function editVersionedFile()
	{
		if(!$this->hasRequestParameter('uri'))
		{
			var_dump('variables missing');
		} 
		else{
			
			$ownerUri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
			$propertyUri = tao_helpers_Uri::decode($this->getRequestParameter('propertyUri'));
			
			$ownerInstance = new core_kernel_classes_Resource($ownerUri);
			$property = new core_kernel_classes_Property($propertyUri);
			$propertyRange = $property->getRange();
			
			//get the versioned file resource
			$versionedFileResource = $ownerInstance->getOnePropertyValue($property);
			//if it does not exist already, create a new versioned file resource
			if(is_null($versionedFileResource)){
				//if the file resource does not exist, create it
				$versionedFileResource = $propertyRange->createInstance();
				$ownerInstance->setPropertyValue($property, $versionedFileResource->uriResource);
			}
			$versionedFile = new core_kernel_versioning_File($versionedFileResource->uriResource);
			
			//create the form
			$formContainer = new tao_actions_form_VersionedFile(null
				, array(
					'instanceUri' => $versionedFile->uriResource,
					'ownerUri' => $ownerInstance->uriResource,
					'propertyUri' => $propertyUri
				)
			);
			$myForm = $formContainer->getForm();
			
			//if the form was sent successfully
			if($myForm->isSubmited()){
				
				if($myForm->isValid()){
					
					// Extract data from form
					$data = $myForm->getValues();
					// Extracted values
					$content = '';
					$fileName = $data[PROPERTY_FILE_FILENAME];
					$filePath = tao_helpers_Uri::getUniqueId($ownerInstance->uriResource).'/'.tao_helpers_Uri::getUniqueId($propertyUri);
					
					$repositoryUri = $data[PROPERTY_VERSIONEDFILE_REPOSITORY];
					$version = isset($data['file_version']) ? $data['file_version'] : 0;
					
					//get the content
					if(isset($data['file_import']['uploaded_file'])){
						if(file_exists($data['file_import']['uploaded_file'])){
							$content = file_get_contents($data['file_import']['uploaded_file']);
						}
						else{
							throw new Exception(__('the file was not uploaded successfully'));
						}
					}
					
					//the file is already versioned
					if($versionedFile->isVersioned()){
						
						if($version){//version = [1..n]
							//revert to a version
//							$history = $versionedFile->getHistory();
							$topRevision = count($myForm->getElement('file_version')->getOptions());
							if($version < $topRevision){
								$versionedFile->revert($version, 'Revert to TAO version ' . $version);
							}
						}
						
						//a new content was sent
						if(!empty($content)){
							$versionedFile->setContent($content);
						}
					} 
					
					//the file is not already versioned
					else{
						//create the versioned file
						$versionedFile = core_kernel_versioning_File::create(
							$fileName,
							$filePath,
							new core_kernel_versioning_Repository($repositoryUri),
							$versionedFile->uriResource
					    );
					    					    
						//a content was sent
						if(!empty($content)){
							$versionedFile->setContent($content);
						}
						
						//add the file to the repository
						$versionedFile->add();
					}
					
				    //commit the file
				    $versionedFile->commit();
					
					$this->setData('message', __($propertyRange->getLabel().' saved'));
					$this->setData('reload', true);
					
					//reload the form to take in account the changes
					$ctx = Context::getInstance();
					$this->redirect(_url($ctx->getActionName(), $ctx->getModuleName(), $this->getSessionAttribute('currentExtension'), array(
						'uri'			=> tao_helpers_Uri::encode($ownerUri),
						'propertyUri'	=> tao_helpers_Uri::encode($propertyUri)
					)));
				}
			}
		}
		
		$this->setData('formTitle', __('Manage item versioned content'));
		$this->setData('myForm', $myForm->render());
		
		$this->setView('form/versioned_file.tpl', true);
	}
	
	/**
	 * Duplicate the current instance
	 * render a JSON response
	 * @return void
	 */
	public function cloneInstance()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$clone = $this->service->cloneInstance($this->getCurrentInstance(), $this->getCurrentClass());
		if(!is_null($clone)){
			echo json_encode(array(
				'label'	=> $clone->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($clone->uriResource)
			));
		}
	}
	
	/**
	 * Move an instance from a class to another
	 * @return void
	 */
	public function moveInstance()
	{
		
		if($this->hasRequestParameter('destinationClassUri')){
			
			if(!$this->hasRequestParameter('classUri') && $this->hasRequestParameter('uri')){
				$instance = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
				$clazz = $this->service->getClass($instance);
			}
			else{
				$clazz = $this->getCurrentClass();
				$instance = $this->getCurrentInstance();
			}	
			
			
			$destinationUri = $this->getRequestParameter('destinationClassUri');
			if(!empty($destinationUri) && $destinationUri != $clazz->uriResource){
				$destinationClass = new core_kernel_classes_Class(tao_helpers_Uri::decode($destinationUri));
				
				$confirmed = $this->getRequestParameter('confirmed');
				if($confirmed == 'false' || $confirmed ===  false){
					
					$diff = $this->service->getPropertyDiff($clazz, $destinationClass);
				
					if(count($diff) > 0){
						echo json_encode(array(
							'status'	=> 'diff',
							'data'		=> $diff
						));
						return true;
					}
				}
				
				$this->setSessionAttribute('showNodeUri', tao_helpers_Uri::encode($instance->uriResource));
				$status = $this->service->changeClass($instance, $destinationClass);
				echo json_encode(array('status'	=> $status));
			}
		}
	}
	
	/**
	 * Render the  form to translate a Resource instance
	 * @return void
	 */
	public function translateInstance()
	{
		
		$instance = $this->getCurrentInstance();
		
		$formContainer = new tao_actions_form_Translate($this->getCurrentClass(), $instance);
		$myForm = $formContainer->getForm();
		
		if($this->hasRequestParameter('target_lang')){
			
			$targetLang = $this->getRequestParameter('target_lang');
		
			if(in_array($targetLang, tao_helpers_I18n::getAvailableLangs())){
				$langElt = $myForm->getElement('translate_lang');
				$langElt->setValue($targetLang);
				$langElt->setAttribute('readonly', 'true');
				
				$trData = $this->service->getTranslatedProperties($instance, $targetLang);
				foreach($trData as $key => $value){
					$element = $myForm->getElement(tao_helpers_Uri::encode($key));
					if(!is_null($element)){
						$element->setValue($value);
					}
				}
			}
		}
		
		$datalang = core_kernel_classes_Session::singleton()->getLg();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$values = $myForm->getValues();
				if(isset($values['translate_lang'])){
					$lang = $values['translate_lang'];
					
					$translated = 0;
					foreach($values as $key => $value){
						if(preg_match("/^http/", $key)){
							$value = trim($value);
							$property = new core_kernel_classes_Property($key);
							if(empty($value)){
								if($datalang != $lang && $lang != ''){
									$instance->removePropertyValueByLg($property, $lang);
								}
							}
							else if($instance->editPropertyValueByLg($property, $value, $lang)){
								$translated++;
							}
						}
					}
					if($translated > 0){
						$this->setData('message', __('Translation saved'));
					}
				}
			}
		}
		
		$this->setData('myForm', $myForm->render());
		$this->setData('formTitle', __('Translate'));
		$this->setView('form.tpl', true);
	}
	
	/**
	 * load the translated data of an instance regarding the given lang 
	 * @return void
	 */
	public function getTranslatedData()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$data = array();
		if($this->hasRequestParameter('lang')){
			$data = tao_helpers_Uri::encodeArray(
				$this->service->getTranslatedProperties(
					$this->getCurrentInstance(),
					$this->getRequestParameter('lang') 
				), 
				tao_helpers_Uri::ENCODE_ARRAY_KEYS);
			}
		echo json_encode($data);
	}
	
	/**
	 * search the instances of an ontology
	 * @return 
	 */
	public function search()
	{
		$found = false;
		
		try{
			$clazz = $this->getCurrentClass();
		}
		catch(Exception $e){
			$clazz = $this->getRootClass();
		}
		
		$formContainer = new tao_actions_form_Search($clazz, null, array('recursive' => true));
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$filters = $myForm->getValues('filters');
				$properties = array();
				foreach($filters as $propUri => $filter){
					if(preg_match("/^http/", $propUri) && !empty($filter)){
						$properties[] = new core_kernel_classes_Property($propUri);
					}
					else{
						unset($filters[$propUri]);
					}
				}
				
				$hasLabel = false;
				foreach($properties as $property){
					if($property->uriResource == RDFS_LABEL){
						$hasLabel = true;
						break;
					}
				}
				if(!$hasLabel){
					$properties=array_merge(array(new core_kernel_classes_Property(RDFS_LABEL)), $properties);
				}
				$this->setData('properties', $properties);
				$params = $myForm->getValues('params');
				$params['like'] = false;

				$instances = $this->service->searchInstances($filters, $clazz, $params);
				if(count($instances) > 0 ){
					$found = array();
					$index = 1;
					foreach($instances as $instance){
						
						$instanceProperties = array();
						foreach($properties as $i => $property){
							$value = '';
							$propertyValues = $instance->getPropertyValuesCollection($property);
							foreach($propertyValues->getIterator() as $j => $propertyValue){
								if($propertyValue instanceof core_kernel_classes_Literal){
									$value .= (string) $propertyValue;
								}
								if($propertyValue instanceof core_kernel_classes_Resource){
									$value .= $propertyValue->getLabel();
								}
								if($j < $propertyValues->count()){
									$value .= "<br />";
								}
							}
							$instanceProperties[$i] = $value;
						}
						$found[$index]['uri'] = tao_helpers_Uri::encode($instance->uriResource);
						$found[$index]['properties'] = $instanceProperties;
						$index++;
					}
				}
			}
			$this->setData('openAction', 'GenerisAction.select');
			if(tao_helpers_Context::check('STANDALONE_MODE')){
				$this->setData('openAction', 'alert');
			}
			$this->setData('foundNumber', count($found));
			$this->setData('found', $found);
		}
		
		
		$this->setData('myForm', $myForm->render());
		$this->setData('formTitle', __('Search'));
		$this->setView('form/search.tpl', true);
	}

	/**
	 * filter class' instances
	 */
	public function filter()
	{
		$found = false;
		
		//get class to filter
		try{
			$clazz = $this->getCurrentClass();
		}
		catch(Exception $e){
			$clazz = $this->getRootClass();
		}
		$this->setData('clazz', $clazz);
		
		//get properties to filter on
		if($this->hasRequestParameter('properties')){
			$properties = $this->getRequestParameter('properties');
		}
		else{
			$properties = tao_helpers_form_GenerisFormFactory::getClassProperties($clazz);
		}
		// Remove item content property
		// Specific case
		if (array_key_exists(TAO_ITEM_CONTENT_PROPERTY, $properties)){
			unset ($properties[TAO_ITEM_CONTENT_PROPERTY]);
		}
		$this->setData('properties', $properties);
		$this->setData('formTitle', __('Filter'));
		$this->setView('form/filter.tpl', true);
	}
	
	/**
	 * Generis API searchInstances function as an action
	 * Developed for the facet based filter ...
	 * @todo Is it a dangerous action ?
	 */
	public function searchInstances()
	{
		$returnValue = array ();
		$filter = array ();
		$properties = array ();
		
		if(!tao_helpers_Request::isAjax()){
			//throw new Exception("wrong request mode");
		}
		
		// Get the class paramater
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
		} else {
			$clazz = $this->getRootClass();
		}
		// Get filter parameter
		$filterParam = array();
		if($this->hasRequestParameter('filter')){
			$filterParam = $this->getRequestParameter('filter');
			foreach ($filterParam as $key=>$values){
				$propertyUri = $key;
				if(!isset($filter['$propertyUri'])){
					$filter[$propertyUri] = array();
				}
				foreach($values as $value){
					$targetUri = tao_helpers_Uri::decode($value);
					array_push($filter[$propertyUri], $targetUri);
				}
			}
		}
		
		$properties = tao_helpers_form_GenerisFormFactory::getClassProperties($clazz);
		// ADD Label property
		if (!array_key_exists(RDFS_LABEL, $properties)){
			$new_properties = array();
			$new_properties[RDFS_LABEL] = new core_kernel_classes_Property(RDFS_LABEL);
			$properties = array_merge($new_properties, $properties);
		}
		// Remove item content property
		if (array_key_exists(TAO_ITEM_CONTENT_PROPERTY, $properties)){
			unset ($properties[TAO_ITEM_CONTENT_PROPERTY]);
		}
		
		$instances = $this->service->searchInstances($filter, $clazz, array ('recursive'=>true));
		$index = 0;
		foreach ($instances as $instance){
			$returnValue [$index]['uri'] = $instance->uriResource;
			$formatedProperties = array ();
			foreach ($properties as $property){
				//$formatedProperties[] = (string)$instance->getOnePropertyValue (new core_kernel_classes_Property($property));
				$value = $instance->getOnePropertyValue($property);
				if ($value instanceof core_kernel_classes_Resource) {
					$value = $value->getLabel();
				}else{
					$value = (string) $value;
				}
				$formatedProperties[] = $value;
			}
			$returnValue [$index]['properties'] = (Object) $formatedProperties;
			$index++;
		}
		
		echo json_encode ($returnValue);
	}
	
	/**
	 * Get property values for a sub set of filtered instances
	 * @param {RequestParameter|string} propertyUri Uri of the target property
	 * @param {RequestParameter|string} classUri Uri of the target class
	 * @param {RequestParameter|array} filter Array of propertyUri/propertyValue used to filter instances of the target class
	 * @param {RequestParameter|array} filterNodesOptions Array of options used by other filter nodes
	 * @return {array} formated for tree 
	 */
	public function getFilteredInstancesPropertiesValues()
	{
		$data = array();
		// The filter nodes options
		$filterNodesOptions = array();
		// The filter
		$filter = array();
        // Filter itself ?
        $filterItself = $this->hasRequestParameter('filterItself') ? ($this->getRequestParameter('filterItself')=='false'?false:true) : false;
        
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		
		// Get the target property
		if($this->hasRequestParameter('propertyUri')){
            $propertyUri = $this->getRequestParameter('propertyUri');
		} else {
            $propertyUri = RDFS_LABEL;
		}
		$property = new core_kernel_classes_Property($propertyUri);
		
		// Get the class paramater
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
		}
		else{
			$clazz = $this->getRootClass();
		}
		
		// Get filter nodes parameters
		if($this->hasRequestParameter('filterNodesOptions')){
			$filterNodesOptions = $this->getRequestParameter('filterNodesOptions');
		}
		// Get filter parameter
		if($this->hasRequestParameter('filter')){
			$filterParam = $this->getRequestParameter('filter');
			//foreach filter nodes
			foreach ($filterParam as $key=>$values){
                
				$filterNodePropertyUri = $filterNodesOptions[$key]['propertyUri'];
                if(!$filterItself && $filterNodePropertyUri == $propertyUri){
                    continue;
                }
				
				if (!isset($filter[$filterNodePropertyUri])){
					$filter[$filterNodePropertyUri] = array();
				}
				foreach($values as $value){
					$propertyValue = !common_Utils::isUri(tao_helpers_Uri::decode($value)) ? $value : tao_helpers_Uri::decode($value);
					array_push($filter[$filterNodePropertyUri], $propertyValue);
				}
			}
		}
		
		// Get used property values for a class functions of the given filter
		$propertyValues = $clazz->getInstancesPropertyValues($property, $filter, array("distinct"=>true, "recursive"=>true));
		
		$propertyValuesFormated = array ();
		foreach($propertyValues as $propertyValue){
			$value = "";
			$id = "";
			if ($propertyValue instanceof core_kernel_classes_Resource){
				$value = $propertyValue->getLabel();
				$id = tao_helpers_Uri::encode($propertyValue->uriResource);
			} else {
				$value = (string) $propertyValue;
				$id = $value;
			}
			$propertyValueFormated = array(
				'data' 	=> $value,
				'type'	=> 'instance',
				'attributes' => array(
					'id' => $id,
					'class' => 'node-instance'
				)
			);
			$propertyValuesFormated[] = $propertyValueFormated;
		}
		
		$data = array(
			'data' 	=> $this->hasRequestParameter('rootNodeName') ? $this->getRequestParameter('rootNodeName') : tao_helpers_Display::textCutter($property->getLabel(), 16),
			'type'	=> 'class',
			'count' => count($propertyValuesFormated),
			'attributes' => array(
				'id' => tao_helpers_Uri::encode($property->uriResource),
				'class' => 'node-class'
			),
			'children' => $propertyValuesFormated
 		);
		
		echo json_encode($data);
	}
	
	/**
	 * Render the add property sub form.
	 * @return void
	 */
	public function addClassProperty()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$clazz = $this->getCurrentClass();
		
		if($this->hasRequestParameter('index')){
			$index = $this->getRequestParameter('index');
		}
		else{
			$index = count($clazz->getProperties(false)) + 1;
		}
		
		$propMode = 'simple';
		if($this->hasSessionAttribute('property_mode')){
			$propMode = $this->getSessionAttribute('property_mode');
		}
		
		//instanciate a property form
		$propFormClass = 'tao_actions_form_'.ucfirst(strtolower($propMode)).'Property';
		if(!class_exists($propFormClass)){
			$propFormClass = 'tao_actions_form_SimpleProperty';
		}
		
		$propFormContainer = new $propFormClass($clazz, $clazz->createProperty('Property_'.$index), array('index' => $index));
		$myForm = $propFormContainer->getForm();
		
		$this->setData('data', $myForm->renderElements());
		$this->setView('blank.tpl', true);
	}	
	
	/**
	 * get the meta data of the selected resource
	 * Display the metadata. 
	 * @return void
	 */
	public function getMetaData()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$this->setData('metadata', false); 
		try{
			$instance = $this->getCurrentInstance();
			
			$date = $instance->getLastModificationDate();
			$this->setData('date', $date->format('d/m/Y H:i:s'));
			$this->setData('user', $instance->getLastModificationUser());
			$this->setData('comment', _dh($instance->getComment()));
			
			$this->setData('uri', $this->getRequestParameter('uri'));
			$this->setData('classUri', $this->getRequestParameter('classUri'));
			$this->setData('metadata', true); 
		}
		catch(Exception $e){
			print $e;
		}
		
		$this->setView('form/metadata.tpl', true);
	}
	
	/**
	 * save the comment field of the selected resource
	 * @return json response {saved: true, comment: text of the comment to refresh it}
	 */
	public function saveComment()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$response = array(
			'saved' 	=> false,
			'comment' 	=> ''
		);
		try{
			if($this->getRequestParameter('comment')){
				$instance = $this->getCurrentInstance();
				$instance->setComment($this->getRequestParameter('comment'));
				if($instance->getComment() == $this->getRequestParameter('comment')){
					$response['saved'] = true;
					$response['comment'] = _dh($instance->getComment());
				}
			}
		}
		catch(Exception $e){
			;// empty
		}
		echo json_encode($response);
	}
	
	
/*
 * Services actions methods
 */
	
	protected function getDataKind()
	{
		return Camelizer::camelize(explode(' ', strtolower(trim($this->getRootClass()->getLabel()))), false);
	}
	
	/**
	 * Service of class or instance selection with a tree.
	 * @return void
	 */
	public function sasSelect()
	{

		$kind = $this->getDataKind();
		
		$context = Context::getInstance();
		$module = $context->getModuleName();
		
		$this->setData('treeName', __('Select'));
		$this->setData('dataUrl', tao_helpers_Uri::url('getOntologyData', $module));
		$this->setData('editClassUrl', tao_helpers_Uri::url('sasSet', $module));
		
		if($this->getRequestParameter('selectInstance') == 'true'){
			$this->setData('editInstanceUrl', tao_helpers_Uri::url('sasSet', $module));
			$this->setData('editClassUrl', false);
		}
		else{
			$this->setData('editInstanceUrl', false);
			$this->setData('editClassUrl', tao_helpers_Uri::url('sasSet', $module));
		}
		
		$this->setData('instanceName', $kind);
		
		$this->setView("sas/select.tpl", true);
	}
	
	/**
	 * Save the uri or the classUri in parameter into the workflow engine by using the dedicated seervice
	 * @return void
	 */
	public function sasSet()
	{
		$message = __('Error');
		
		//init variable service:
		$variableService = wfEngine_models_classes_VariableService::singleton();
		
		//set the class uri
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			if(!is_null($clazz)){
				$variableService->save(array($this->getDataKind().'ClassUri' => $clazz->uriResource));
				$message = $clazz->getLabel().' '.__('class selected');
			}
		}
		
		//set the instance uri
		if($this->hasRequestParameter('uri')){
			$instance = $this->getCurrentInstance();
			if(!is_null($instance)){
				$variableService->save(array($this->getDataKind().'Uri' => $instance->uriResource));
				$message = $instance->getLabel().' '.__($this->getDataKind()).' '.__('selected');
			}
		}
		$this->setData('message', $message);
		
		//only for the notification
		$this->setView('header.tpl', true);
	}
	
	/**
	 * Add a new instance
	 * @return void
	 */
	public function sasAddInstance()
	{
		$clazz = $this->getCurrentClass();
		$label = $this->service->createUniqueLabel($clazz);
		$instance = $this->service->createInstance($clazz);
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
			
			//init variable service:
			$variableService = wfEngine_models_classes_VariableService::singleton();
			$variableService->save(array($this->getDataKind().'Uri' => $instance->uriResource));
			
			$params = array(
				'uri'		=> tao_helpers_Uri::encode($instance->uriResource),
				'classUri'	=> tao_helpers_Uri::encode($clazz->uriResource)
			);
			$this->redirect(_url('sasEditInstance', null, null, $params));
		}
	}
	
	
	/**
	 * Edit an instances 
	 * @return void
	 */
	public function sasEditInstance()
	{
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$formContainer = new tao_actions_form_Instance($clazz, $instance);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$instance = $this->service->bindProperties($instance, $myForm->getValues());
				$this->setData('message', __('Resource saved'));
			}
		}
		
		$this->setData('uri', tao_helpers_Uri::encode($instance->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setData('formTitle', __('Edit'));
		$this->setData('myForm', $myForm->render());
		$this->setView('form.tpl', true);
	}
	
	/**
	 * Delete an instance
	 * @return void
	 */
	public function sasDeleteInstance()
	{
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$this->setData('label', $instance->getLabel());
		
		$this->setData('uri', tao_helpers_Uri::encode($instance->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setView('form/delete.tpl', true);
	}
	
	/**
	 * delete an instance or a class
	 * called via ajax
	 */
	public function delete()
	{
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$deleted = false;
		if($this->hasRequestParameter('uri')){
			$instance = $this->getCurrentInstance();
			if(!is_null($instance)){
				$deleted = $instance->delete();
			}
		}
		elseif($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			if(!is_null($clazz)){
				$deleted = $clazz->delete();
			}
		}
		
		echo json_encode(array('deleted'	=> $deleted));
	}
	
	/**
	 * display the optimize interface
	 * @return void
	 */
	public function optimize()
	{
		$clazz = $this->getRootClass();
		$classes = $clazz->getSubClasses(true);
	}
}
?>