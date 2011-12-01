<?php
/**
 * This controller provide the actions to manage the lists of data
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 * @package tao
 * @subpackage action
 *
 */
class tao_actions_Lists extends tao_actions_CommonModule {

	/**
	 * Constructor performs initializations actions
	 * @return void
	 */
	public function __construct(){		
		
		//add List stylesheet
		tao_helpers_Scriptloader::addCssFile(TAOBASE_WWW . 'css/lists.css');
		
		$this->service = tao_models_classes_ListService::singleton();
		$this->defaultData();
	}

	/**
	 * Show the list of users
	 * @return void
	 */
	public function index(){
		
		$myAdderFormContainer = new tao_actions_form_List();
		$myForm = $myAdderFormContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$values = $myForm->getValues();
				$newList = $this->service->createList($values['label']);
				$i = 0;
				while($i < $values['size']){
					$this->service->createListElement($newList, __('element'). ' '.($i + 1));
					$i++;
				}
			}
		}
		else{
			$myForm->getElement('label')->setValue(__('List').' '.(count($this->service->getLists()) + 1));
		}
		$this->setData('form', $myForm->render());
		
		$lists = array();
		foreach($this->service->getLists() as $listClass){
			$elements = array();
			foreach($this->service->getListElements($listClass) as $index => $listElement){
				$elements[$index] = array(
					'uri'		=> tao_helpers_Uri::encode($listElement->uriResource),
					'label'		=> $listElement->getLabel()
				);
				ksort($elements);
			}
			$lists[] = array(
				'uri'		=> tao_helpers_Uri::encode($listClass->uriResource),
				'label'		=> $listClass->getLabel(),
				'editable'	=> $listClass->isSubClassOf(new core_kernel_classes_Class(TAO_LIST_CLASS)),
				'elements'	=> $elements
			);
		}
		
		$this->setData('lists', $lists);
		$this->setView('list/index.tpl');
	}
	
	/**
	 * get the JSON data to populate the tree widget
	 * @return void
	 */
	public function getListsData(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$data = array();
		foreach($this->service->getLists() as $listClass){
			array_push($data, $this->service->toTree($listClass, array('subclasses' => false)));
		}
		echo json_encode(array(
			'data' 		=> __('Lists'),
			'attributes' => array('class' => 'node-root'),
			'children' 	=> $data,
			'state'		=> 'open'
		));
	}
	
	/**
	 * get the elements in JSON of the list in parameter
	 * @return void
	 */
	public function getListElements(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}	
		$data = array();
		if($this->hasRequestParameter('classUri')){
			$list = $this->service->getList(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
			if(!is_null($list)){
				foreach($this->service->getListELements($list, true) as  $listElement){
					$data[tao_helpers_Uri::encode($listElement->uriResource)] = $listElement->getLabel();
				}
			}
		}
		echo json_encode($data);
	}

	
	/**
	 * Save a list and it's elements
	 * @return void
	 */
	public function saveLists(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$saved = false;
		
		if($this->hasRequestParameter('uri')){
			
			$listClass = $this->service->getList(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
			if(!is_null($listClass)){
				$listClass->setLabel($this->getRequestParameter('label'));
				
				$setLevel = false;
				$levelProperty = new core_kernel_classes_Property(TAO_LIST_LEVEL_PROP);
				foreach($listClass->getProperties(true) as $property){
					if($property->uriResource == $levelProperty->uriResource){
						$setLevel = true;
						break;
					}
				}
				
				$elements = $this->service->getListElements($listClass);
				foreach($this->getRequestParameters() as $key => $value){
					if(preg_match("/^list\-element_/", $key)){
						$key = str_replace('list-element_', '', $key);
						$level = substr($key, 0, strpos('_', $key) + 1);
						$uri = tao_helpers_Uri::decode(preg_replace("/^{$level}_/", '', $key));
						
						$found = false;
						foreach($elements as $element){
							if($element->uriResource == $uri && !empty($uri)){
								$found = true;
								$element->setLabel($value);
								if($setLevel){
									$element->editPropertyValues($levelProperty, $level);
								}
								break;
							}
						}
						if(!$found){
							$element = $this->service->createListElement($listClass, $value);
							if($setLevel){
								$element->setPropertyValue($levelProperty, $level);
							}
						}
					}
				}
				$saved = true;
			}
		}
		echo json_encode(array('saved' => $saved));
	}
	
	/**
	 * Create a list or a list element
	 * @return void
	 */
	public function create(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$response = array();
		if($this->getRequestParameter('classUri')){
			
			if($this->getRequestParameter('type') == 'class' && $this->getRequestParameter('classUri') == 'root'){
				$listClass = $this->service->createList();
				if(!is_null($listClass)){
					$response['label']	= $listClass->getLabel();
					$response['uri'] 	= tao_helpers_Uri::encode($listClass->uriResource);
				}
			}
			
			if($this->getRequestParameter('type') == 'instance'){
				$listClass = $this->service->getList(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
				if(!is_null($listClass)){
					$listElt = $this->service->createListElement($listClass);
					if(!is_null($listElt)){
						$response['label']	= $listElt->getLabel();
						$response['uri'] 	= tao_helpers_Uri::encode($listElt->uriResource);
					}
				}
			}

		}
		echo json_encode($response);
	}

	/**
	 * Rename a list node: change the label of a resource
	 * Render the json response with the renamed status
	 * @return void
	 */
	public function rename(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		
		$data = array('renamed'	=> false);
		
		if($this->hasRequestParameter('uri') && $this->hasRequestParameter('newName')){
			
			if($this->hasRequestParameter('classUri')){
				$listClass = $this->service->getList(tao_helpers_Uri::decode($this->getRequestParameter('classUri')));
				$listElt = $this->service->getListElement($listClass, tao_helpers_Uri::decode($this->getRequestParameter('uri')));
				if(!is_null($listElt)){
					$listElt->setLabel($this->getRequestParameter('newName'));
					if($listElt->getLabel() == $this->getRequestParameter('newName')){
						$data['renamed'] = true;
					}
				}
			}
			else{
				$listClass = $this->service->getList(tao_helpers_Uri::decode($this->getRequestParameter('uri')));
				if(!is_null($listClass)){
					$listClass->setLabel($this->getRequestParameter('newName'));
					if($listClass->getLabel() == $this->getRequestParameter('newName')){
						$data['renamed'] = true;
					}
				}
			}
		}
		echo json_encode($data);
	}
	
	/**
	 * Removee the list in parameter
	 * @return void
	 */
	public function removeList(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$deleted = false;
		
		if($this->hasRequestParameter('uri')){
			$deleted = $this->service->removeList(
				$this->service->getList(tao_helpers_Uri::decode($this->getRequestParameter('uri')))
			);
		}
		echo json_encode(array('deleted' => $deleted));
	}
	
	/**
	 * Remove the list element in parameter
	 * @return void
	 */
	public function removeListElement(){
		if(!tao_helpers_Request::isAjax()){
			throw new Exception("wrong request mode");
		}
		$deleted = false;
		
		if($this->hasRequestParameter('uri')){
			$deleted = $this->service->removeListElement(
				new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('uri')))
			);
		}
		echo json_encode(array('deleted' => $deleted));
	}
	
}
?>