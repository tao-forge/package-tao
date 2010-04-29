<?php

require_once('wfEngine/actions/ServicesApi.class.php');

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
abstract class TaoModule extends CommonModule {

	/**
	 * when the action is called inside the application
	 * @var int
	 */
	const MODE_APP = 0;

	/**
	 * when the action is called in standalone mode
	 * @var int
	 */
	const MODE_STANDALONE = 1;
	
	/**
	 * define the current calling mode
	 * @var int
	 */
	protected $mode;
	
	/**
	 * Check the authentication
	 */
	public function __construct(){
		
		parent::__construct();
		
		(preg_match("/^SaS/", get_class($this))) ? $this->mode = self::MODE_STANDALONE : $this->mode = self::MODE_APP;
	}
	
	
	
	/**
     * @see Module::setView()
     * @param string $identifier view identifier
     * @param boolean set to true if you want to use the views in the tao extension instead of the current extension 
     */
    public function setView($identifier, $useMetaExtensionView = false) {
        parent::setView($identifier);
		if($useMetaExtensionView){
			Renderer::setViewsBasePath(TAOVIEW_PATH);
		}
		return;
	}
	
/*
 * Shared Methods
 */
	
	/**
	 * get the current item class regarding the classUri' request parameter
	 * @return core_kernel_classes_Class the item class
	 */
	protected function getCurrentClass(){
		$classUri = tao_helpers_Uri::decode($this->getRequestParameter('classUri'));
		if(is_null($classUri) || empty($classUri)){
			throw new Exception("No valid class uri found");
		}
		
		return  new core_kernel_classes_Class($classUri);
	}
	
	/**
	 *  ! Please override me !
	 * get the current instance regarding the uri and classUri in parameter
	 * @return core_kernel_classes_Resource
	 */
	protected function getCurrentInstance(){
		$uri = tao_helpers_Uri::decode($this->getRequestParameter('uri'));
		if(is_null($uri) || empty($uri)){
			throw new Exception("No valid uri found");
		}
		
		$clazz = $this->getCurrentClass();
		$instance = $this->service->getOneInstanceBy( $clazz, $uri, 'uri');
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
	 * Edit a class using the GenerisFormFactory::classEditor
	 * Manage the form submit by saving the class
	 * @param core_kernel_classes_Class    $clazz
	 * @param core_kernel_classes_Resource $resource
	 * @return tao_helpers_form_Form the generated form
	 */
	protected function editClass(core_kernel_classes_Class $clazz, core_kernel_classes_Resource $resource){
	
		
		$myForm = tao_helpers_form_GenerisFormFactory::classEditor($clazz, $resource);
		
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
						if(isset($_POST[$key])){
							$pkey = str_replace('property_', '', $key);
							$propNum = substr($pkey, 0, strpos($pkey, '_') );
							$propKey = tao_helpers_Uri::decode(str_replace($propNum.'_', '', $pkey));
							$propertyValues[$propNum][$propKey] = tao_helpers_Uri::decode($value);
						}
						else{
							$pkey = str_replace('property_', '', $key);
							$propNum = substr($pkey, 0, strpos($pkey, '_') );
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
								if($classProperty->delete()){
									$myForm->removeGroup("property_".$propNum);
									break;
								}
							}
						}
					}
					else{
						$simpleMode = false;
						if($_POST["propertyMode{$propNum}"] == 'simple'){
							$simpleMode = true;
						}
						if($simpleMode){
							$type = $properties['type'];
							$range = $properties['range'];
							unset($properties['type']);
							unset($properties['range']);
							
							if(isset($propertyMap[$type])){
								$properties['http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget'] = $propertyMap[$type]['widget'];
								if(is_null($propertyMap[$type]['range'])){
									$properties['http://www.w3.org/2000/01/rdf-schema#range'] = $range;
								}
								else{
									$properties['http://www.w3.org/2000/01/rdf-schema#range'] = $propertyMap[$type]['range'];
								}
							}
						}
						$property = new core_kernel_classes_Property(tao_helpers_Uri::decode($_POST['propertyUri'.$propNum]));
						$this->service->bindProperties($property, $properties);
						
						$myForm->removeGroup("property_".$propNum);
						tao_helpers_form_GenerisFormFactory::propertyEditor($property, $myForm, $propNum, $simpleMode );
					}
					//reload form
					//$myForm = tao_helpers_form_GenerisFormFactory::classEditor($clazz, $resource);
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
	public function index(){
		
		if($this->getData('reload') == true){
			unset($_SESSION[SESSION_NAMESPACE]['uri']);
			unset($_SESSION[SESSION_NAMESPACE]['classUri']);
		}
		$this->setView('index.tpl', false);
	}
	
	/**
	 * Render json data from the current ontology root class
	 * @return void
	 */
	public function getOntologyData(){
		
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$filter = '';
		if($this->hasRequestParameter('filter')){
			$filter = $this->getRequestParameter('filter');
		}
		$highlightUri = '';
		if($this->hasSessionAttribute("showNodeUri")){
			$highlightUri = $this->getSessionAttribute("showNodeUri");
			unset($_SESSION[SESSION_NAMESPACE]["showNodeUri"]);
		}
		$instances = true;
		if($this->hasRequestParameter('hideInstances')){
			if((bool)$this->getRequestParameter('hideInstances')){
				$instances = false;
			}
		}
		echo json_encode( $this->service->toTree( $this->getRootClass(), true, $instances, $highlightUri, $filter));
	}
	
	
	
	/**
	 * Add an instance of the selected class
	 * @return void
	 */
	public function addInstance(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$clazz = $this->getCurrentClass();
		$instance = $this->service->createInstance($clazz);
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
			echo json_encode(array(
				'label'	=> $instance->getLabel(),
				'uri' 	=> tao_helpers_Uri::encode($instance->uriResource)
			));
		}
	}
	
	/**
	 * Duplicate the current instance
	 * render a JSON response
	 * @return void
	 */
	public function cloneInstance(){
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
	public function moveInstance(){
		
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
				
				if(!$this->hasRequestParameter('confirmed')){
					
					$diff = $this->service->getPropertyDiff($clazz, $destinationClass);
				
					if(count($diff) > 0){
						echo json_encode(array(
							'status'	=> 'diff',
							'data'		=> $diff
						));
						return true;
					}
				}
				
				$status = $this->service->changeClass($instance, $destinationClass);
				echo json_encode(array('status'	=> $status));
			}
		}
	}
	
	/**
	 * Render the  form to translate a Resource instance
	 * @return void
	 */
	public function translateInstance(){
		
		$instance = $this->getCurrentInstance();
		$myForm = tao_helpers_form_GenerisFormFactory::translateInstanceEditor($this->getCurrentClass(), $instance);
		
		if($this->hasRequestParameter('target_lang')){
			
			$targetLang = $this->getRequestParameter('target_lang');
		
			if(in_array($targetLang, $GLOBALS['available_langs'])){
				$langElt = $myForm->getElement('translate_lang');
				$langElt->setValue($targetLang);
				
				$trData = $this->service->getTranslatedProperties($instance, $targetLang);
				foreach($trData as $key => $value){
					$element = $myForm->getElement(tao_helpers_Uri::encode($key));
					if(!is_null($element)){
						$element->setValue($value);
					}
				}
			}
		}
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$values = $myForm->getValues();
				if(isset($values['translate_lang'])){
					$lang = $values['translate_lang'];
					
					$translated = 0;
					foreach($values as $key => $value){
						if(preg_match("/^http/", $key) && !empty($value)){
							if($instance->editPropertyValueByLg(new core_kernel_classes_Property($key), $value, $lang)){
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
	public function getTranslatedData(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$data = array();
		if($this->hasRequestParameter('lang')){
			
			$trData = $this->service->getTranslatedProperties(
					$this->getCurrentInstance(),
					$this->getRequestParameter('lang') 
				);
			foreach($trData as $key => $value){
				$data[tao_helpers_Uri::encode($key)] = $value;
			}
		}
		echo json_encode($data);
	}
	
	/**
	 * search the instances of an ontology
	 * @return 
	 */
	public function search(){
		$found = false;
		$clazz = $this->getRootClass();
		$myForm = tao_helpers_form_GenerisFormFactory::searchInstancesEditor($clazz, true);
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				
				$filters = $myForm->getValues('filters');
				$properties = array();
				foreach($filters as $propUri => $filter){
					if(preg_match("/^http/", $propUri)){
						$properties[] = new core_kernel_classes_Property($propUri);
					}
					else{
						unset($filters[$propUri]);
					}
				}
				$this->setData('properties', $properties);
				$instances = $this->service->searchInstances($filters, $clazz, $myForm->getValues('params'));
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
									$value .= (string)$propertyValue;
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
		}
		
		$this->setData('openAction', 'GenerisAction.select');
		if($this->mode == self::MODE_STANDALONE){
			$this->setData('openAction', 'alert');
		}
		
		$this->setData('found', $found);
		$this->setData('myForm', $myForm->render());
		$this->setData('formTitle', __('Search'));
		$this->setView('form/search.tpl', true);
	}

	
	/**
	 * Render the add property sub form.
	 * @return void
	 */
	public function addClassProperty(){
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
		
		$myForm = tao_helpers_form_GenerisFormFactory::propertyEditor(
			$clazz->createProperty('Property_'.$index),
			tao_helpers_form_FormFactory::getForm('property_'.$index),
			$index,
			true
		);
		
		$this->setData('data', $myForm->renderElements());
		$this->setView('blank.tpl', true);
	}	
	
	/**
	 * get the meta data of the selected resource
	 * Display the metadata. 
	 * @return void
	 */
	public function getMetaData(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$this->setData('metadata', false); 
		try{
			$instance = $this->getCurrentInstance();
			
			$date = $instance->getLastModificationDate();
			$this->setData('date', $date->format('d/m/Y H:i:s'));
			$this->setData('user', $instance->getLastModificationUser());
			$this->setData('comment', $instance->comment);
			
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
	public function saveComment(){
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
				if($instance->comment == $this->getRequestParameter('comment')){
					$response['saved'] = true;
					$response['comment'] = $instance->comment;
				}
			}
		}
		catch(Exception $e){}
		echo json_encode($response);
	}
	
	
/*
 * Services actions methods
 */
	
	protected function getDataKind(){
		return Camelizer::camelize(explode(' ', strtolower(trim($this->getRootClass()->getLabel()))), false);
	}
	
	/**
	 * Service of class or instance selection with a tree.
	 * @return void
	 */
	public function sasSelect(){

		$kind = $this->getDataKind();
		
		$this->setData('treeName', __('Select'));
		$this->setData('dataUrl', tao_helpers_Uri::url('getOntologyData', get_class($this)));
		$this->setData('editClassUrl', tao_helpers_Uri::url('sasSet', get_class($this)));
		
		if($this->getRequestParameter('selectInstance') == 'true'){
			$this->setData('editInstanceUrl', tao_helpers_Uri::url('sasSet', get_class($this)));
			$this->setData('editClassUrl', false);
		}
		else{
			$this->setData('editInstanceUrl', false);
			$this->setData('editClassUrl', tao_helpers_Uri::url('sasSet', get_class($this)));
		}
		
		$this->setData('instanceName', $kind);
		
		$this->setView("sas/select.tpl", true);
	}
	
	/**
	 * Save the uri or the classUri in parameter into the workflow engine by using the dedicated seervice
	 * @return void
	 */
	public function sasSet(){
		$message = __('Error');
		
		//set the class uri
		if($this->hasRequestParameter('classUri')){
			$clazz = $this->getCurrentClass();
			if(!is_null($clazz)){
				ServiceApi::save( array($this->getDataKind().'ClassUri' => $clazz->uriResource) );
				$message = $clazz->getLabel().' '.__('class selected');
			}
		}
		
		//set the instance uri
		if($this->hasRequestParameter('uri')){
			$instance = $this->getCurrentInstance();
			if(!is_null($instance)){
				ServiceApi::save( array($kind.'Uri' => $instance->uriResource) );
				$message = $instance->getLabel().' '.__($kind).' '.__('selected');
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
	public function sasAddInstance(){
		$clazz = $this->getCurrentClass();
		$instance = $this->service->createInstance($clazz);
		if(!is_null($instance) && $instance instanceof core_kernel_classes_Resource){
			
			ServiceApi::save( array($this->getDataKind().'Uri' => $instance->uriResource) );
			
			$this->redirect('sasEditInstance?uri='.tao_helpers_Uri::encode($instance->uriResource).'&classUri='.tao_helpers_Uri::encode($clazz->uriResource));
		}
	}
	
	
	/**
	 * Edit an instances 
	 * @return void
	 */
	public function sasEditInstance(){
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$myForm = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $instance);
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
	public function sasDeleteInstance(){
		$clazz = $this->getCurrentClass();
		$instance = $this->getCurrentInstance();
		
		$this->setData('label', $instance->getLabel());
		
		$this->setData('uri', tao_helpers_Uri::encode($instance->uriResource));
		$this->setData('classUri', tao_helpers_Uri::encode($clazz->uriResource));
		$this->setView('form/delete.tpl', true);
	}
}
?>