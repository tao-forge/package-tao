<?php
/**
 * QtiAuthoring Controller provide actions to edit a QTI item
 * 
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package taoItems
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
 
class QtiAuthoring extends CommonModule {
	
	protected $debugMode = false;
	
	/**
	 * constructor: initialize the service and the default data
	 * @return Delivery
	 */
	public function __construct(){
		
		parent::__construct();
		
		$this->debugMode = false;
		$this->qtiService = tao_models_classes_ServiceFactory::get("taoItems_models_classes_QTI_Service");
		$this->service = tao_models_classes_ServiceFactory::get('taoItems_models_classes_QtiAuthoringService');
		$this->defaultData();
		
		taoItems_models_classes_QTI_Data::setPersistance(true);
	}
	
	public function getCurrentItemResource(){
	
		$itemResource = null;
		
		if($this->hasRequestParameter('itemUri')){
			$itemResource = new core_kernel_classes_Resource(tao_helpers_Uri::decode($this->getRequestParameter('itemUri')));
		}else{
			throw new Exception('no item rsource uri found');
		}
		
		return $itemResource;
	}
	/*
	* get the current item object either from the file or from session or create a new one
	*/
	public function getCurrentItem(){
		
		$item = null;
		
		$itemUri = tao_helpers_Uri::decode($this->getRequestParameter('instance'));;
		$itemSerial = '';
		$itemIdentifier = tao_helpers_Uri::getUniqueId($itemUri);//TODO: remove coopling to TAO
		
		//when actions are executed in the authroing tool, retrieve the item with the serial:
		if($this->hasRequestParameter('itemSerial')){
			$itemSerial = tao_helpers_Uri::decode($this->getRequestParameter('itemSerial'));
			$item = $this->qtiService->getItemBySerial($itemSerial);
		}else{
			//try creating a new item:
			$itemFile = html_entity_decode($this->getRequestParameter('xml'));//gonna be ignored
			
			if(empty($itemFile)){
				
				//check to allow page reloading without xml file: debug mode on
				if(!empty($itemUri) && $this->debugMode){
					if(isset($_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)])){
						$item = $this->qtiService->getItemBySerial($_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)]);
					}
				}
				if(empty($item)){
					$itemResource = new core_kernel_classes_Resource($itemUri);
					
					if(!$this->debugMode) {
						$item = $this->qtiService->getDataItemByRdfItem($itemResource);//i1282039875024462900
					}
					
					if(is_null($item)){
						
						//create a new item object:
						$item = $this->service->createNewItem($itemIdentifier, $itemResource->getLabel());
						$_SESSION['tao_qti_item_uris'][tao_helpers_Uri::getUniqueId($itemUri)] = $item->getSerial();
					}
				}
				
				if(empty($item)){
					throw new Exception('a new qti item xml cannot be created');
				}
			}else{
				//intermediate state??
				
				//import it:
				// $qtiParser = new taoItems_models_classes_QTI_Parser($itemFile);
				// $qtiParser->validate();
				// if(!$qtiParser->isValid()){
					// var_dump($itemFile);
					// echo $qtiParser->displayErrors();
					// return null;
				// }
				// $item = $qtiParser->load();
				// if(empty($item)){
					// throw new Exception('cannot load the item from the file: '.$itemFile);
				// }
			}
		}
				
		if(is_null($item)){
			throw new Exception('there is no item');
		}
		
		return $item;
	}

	public function index(){
		
		//required for saving the item in tao:
		$this->setData('itemUri', tao_helpers_Uri::encode($this->getRequestParameter('instance')));
		
		$currentItem = $this->getCurrentItem();
		// if($this->debugMode) var_dump($currentItem);
		$itemData = $this->service->getItemData($currentItem);
		
		$this->setData('itemSerial', $currentItem->getSerial());
		$this->setData('itemForm', $currentItem->toForm()->render());
		$this->setData('itemData', $itemData);
		
		$this->setData('jsFramework_path', BASE_WWW.'js/jsframework/');
		$this->setData('jwysiwyg_path', BASE_WWW.'js/jwysiwyg/');
		$this->setData('simplemodal_path', BASE_WWW.'js/simplemodal/');
		$this->setData('qtiAuthoring_path', BASE_WWW.'js/qtiAuthoring/');
		$this->setData('qtiAuthoring_img_path', BASE_WWW.'img/qtiAuthoring/');
		$this->setView("QTIAuthoring/authoring.tpl");
		// $this->setView("QTIAuthoring/authoring_with_fw.tpl");
	}
	
	public function saveItemData(){
		$saved = false;
		
		$itemData = $this->getPostedItemData();
		
		if(!empty($itemData)){
			//save to qti:
			$this->service->saveItemData($this->getCurrentItem(), $itemData);
			$saved = true;
		}
		
		echo json_encode(array(
			'saved'=>$saved
		));
	}
	
	public function saveInteractionData(){
		$saved = false;
		
		$interactionData = $this->getPostedInteractionData();
		if(!empty($interactionData)){
			$this->service->setInteractionData($this->getCurrentInteraction(), $interactionData);
			$saved = true;
		}
		
		if(tao_helpers_Request::isAjax()){
			echo json_encode(array(
				'saved'=>$saved
			));
		}else{
			return $saved;
		}
	}
	
	public function saveItem(){
		$saved = false;
		
		// $itemData = html_entity_decode($this->getRequestParameter('itemData'));
		$itemData = $this->getPostedItemData();
		// print_r($itemData);exit;
		// error_log($itemData);
		
		$itemObject = $this->getCurrentItem();
		//save item properties in the option array:
		$options = array(
			'title' => $itemObject->getIdentifier(),
			'label' => '',
			'timeDependent' => false,
			'adaptive' => false
		);
		if($this->getRequestParameter('title') != '') $options['title'] = $this->getRequestParameter('title');
		if($this->hasRequestParameter('label')) $options['label'] = $this->getRequestParameter('label');
		if($this->hasRequestParameter('timeDependent')) $options['timeDependent'] = $this->getRequestParameter('timeDependent');
		if($this->hasRequestParameter('adaptive')) $options['adaptive'] = $this->getRequestParameter('adaptive');
		$this->service->setOptions($itemObject, $options);
		
		if(!empty($itemData)){
			//save item data:
			$this->service->saveItemData($itemObject, $itemData);
			//save to qti:
		}
		
		$itemResource = $this->getCurrentItemResource();
		$this->qtiService->saveDataItemToRdfItem($itemObject, $itemResource);
		// print_r($itemObject);
		// echo '<pre>'.$itemResource->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
		$saved = true;		
		
		if(tao_helpers_Request::isAjax()){
			echo json_encode(array(
				'saved' => $saved
			));
		}
		
		return $saved;
	}
	
	public function preview(){
		$parameters = array(
			'root_url' 				=> ROOT_URL,
        	'base_www' 				=> BASE_WWW,
        	'taobase_www' 			=> TAOBASE_WWW,
			'delivery_server_mode' 	=> false,
			'raw_preview'			=> true
		);
		taoItems_models_classes_QTI_TemplateRenderer::setContext($parameters, 'ctx_');
		
		$output = $this->qtiService->renderItem($this->getCurrentItem());
		
		$output = taoItems_models_classes_QtiAuthoringService::filteredData($output);
		
		$this->setData('output', $output);
		$this->setView("QTIAuthoring/preview.tpl");
	}
	
	protected function getPostedItemData(){
		return $this->getPostedData('itemData');
	}
	
	protected function getPostedInteractionData(){
		return $this->getPostedData('interactionData');
	}
	
	protected function getPostedData($key, $required = false){
		$returnValue = '';
		
		if($this->hasRequestParameter($key)){
			$returnValue = html_entity_decode(urldecode($this->getRequestParameter($key)), null, "UTF-8");
		}else{
			if($required){
				throw new Exception('the request data "'.$key.'" cannot be found');
			}
		}
		
		return $returnValue;
	}
	
	public function addInteraction(){
		$added = false;
		$interactionSerial = '';
		
		$interactionType = $this->getRequestParameter('interactionType');
		$itemData = $this->getPostedItemData();
		// echo "<pre>$itemData</pre>";
		
		$item = $this->getCurrentItem();
		if(!empty($interactionType)){
			$interaction = $this->service->addInteraction($item, $interactionType);
			
			if(!is_null($interaction)){
				//save the itemData, i.e. the location at which the new interaction shall be inserted
				//the location has been marked with {qti_interaction_new}
				$itemData = preg_replace("/{qti_interaction_new}/", "{{$interaction->getSerial()}}", $itemData, 1);
				$this->service->saveItemData($item, $itemData);
				$itemData = $this->service->getItemData($item);//do not convert to html entities...
				
				//everything ok:
				$added = true;
				$interactionSerial = $interaction->getSerial();
			}
		}
		
		echo json_encode(array(
			'added' => $added,
			'interactionSerial' => $interactionSerial,
			'itemData' => $itemData
		));
	}
	
	public function addHotText(){
		$added = false;
		$choiceSerial = '';//the hot text basically is a "choice"
		$textContent = '';
		
		$interactionData = $this->getPostedInteractionData();
		// echo "<pre>$interactionData</pre>";
		
		$interaction = $this->getCurrentInteraction();
		
		$choice = $this->service->addChoice($interaction, '', null, null, $interactionData);
		
		if(!is_null($choice)){
			$interactionData = $this->service->getInteractionData($interaction);//do not convert to html entities...
			
			//everything ok:
			$added = true;
			$choiceSerial = $choice->getSerial();
		}
		
		
		echo json_encode(array(
			'added' => $added,
			'choiceSerial' => $choiceSerial,
			'choiceForm' => $choice->toForm()->render(),
			'interactionData' => html_entity_decode($interactionData)
		));
	}
	
	public function addChoice(){
		$added = false;
		$choiceSerial = '';
		$choiceForm = '';
		$groupSerial = '';
		
		$interaction = $this->getCurrentInteraction();
		if(!is_null($interaction)){
			try{
				//not null in case of a match or gapmatch interaction:
				$group = null;
				$group = $this->getCurrentGroup();
			}catch(Exception $e){}
		
			$choice = $this->service->addChoice($interaction, '', null, $group);
			
			//return id and form:
			if(!is_null($group)) $groupSerial = $group->getSerial();
			$choiceSerial = $choice->getSerial();
			$choiceForm = $choice->toForm()->render();
			$added = true;
		}
		
		echo json_encode(array(
			'added' => $added,
			'choiceSerial' => $choiceSerial,
			'choiceForm' => $choiceForm,
			'groupSerial' => $groupSerial,
			'reload' => ($added)?$this->requireChoicesUpdate($interaction):false
		));
	}
	
	
	public function deleteInteractions(){
		// var_dump($this->getCurrentItem(), $this->getRequestParameter('interactionSerials'));
		
		$deleted = false;
		
		$interactionSerials = array();
		if($this->hasRequestParameter('interactionSerials')){
			$interactionSerials = $this->getRequestParameter('interactionSerials');
		}
		if(empty($interactionSerials)){
			throw new Exception('no interaction ids found to be deleted');
		}else{
			$item = $this->getCurrentItem();
			$deleteCount = 0;
			
			//delete interactions:
			foreach($interactionSerials as $interactionSerial){
				$interaction = $this->qtiService->getInteractionBySerial($interactionSerial);
				if(!empty($interaction)){
					$this->service->deleteInteraction($item, $interaction);
					$deleteCount++;
				}else{
					throw new Exception('no interaction found to be deleted with the serial: '.$interactionSerial);
				}
			}
			
			if($deleteCount == count($interactionSerials)){
				$deleted = true;
			}
		}
		
		echo json_encode(array(
			'deleted' => $deleted
		));
		
	}
	
	public function deleteChoice(){
		$interaction = $this->getCurrentInteraction();
		$deleted = false;
		
		try{
			$choice = null;
			$choice = $this->getCurrentChoice();
		}catch(Exception $e){}
		if(!is_null($interaction) && !is_null($choice)){
			$this->service->deleteChoice($interaction, $choice);
			$deleted = true;
		}
		
		if(!$deleted){
			try{
				//for gapmatch interaction, where a gorup is considered as a choice:
				$group = null;
				$group = $this->getCurrentGroup();
			
				if(!is_null($interaction) && !is_null($group)){
					$this->service->deleteGroup($interaction, $group);
					$deleted = true;
				}
			}catch(Exception $e){
				throw new Exception('cannot delete the choice');
			}
		}
		
		echo json_encode(array(
			'deleted' => $deleted,
			'reload' => ($deleted)?$this->requireChoicesUpdate($interaction):false,
			'reloadInteraction' => ($deleted)?$this->requireInteractionUpdate($interaction):false
		));
	}
	
	protected function requireChoicesUpdate(taoItems_models_classes_QTI_Interaction $interaction){
	
		$reload = false;
		
		//basically, interactions that have choices with the "matchgroup" property
		if(!is_null($interaction)){
			switch(strtolower($interaction->getType())){
				case 'associate':
				case 'match':
				case 'gapmatch':
				case 'graphicgapmatch':{
					$reload = true;
					break;
				}
			}
		}
		
		return $reload;
	}
	
	protected function requireInteractionUpdate(taoItems_models_classes_QTI_Interaction $interaction){
	
		$reload = false;
		
		//basically, interactions that need a wysiwyg data editor:
		if($this->getRequestParameter('reloadInteraction')){
			if(!is_null($interaction)){
				switch(strtolower($interaction->getType())){
					case 'hottext':
					case 'gapmatch':{
						$reload = true;
						break;
					}
				}
			}
		}
		
		return $reload;
	}
	
	//to be used to dynamically update the main itemData editor frame:
	public function getInteractionTag(){
		$interaction = $this->getCurrentInteraction();
		echo $this->service->getInteractionTag($interaction);
	}
	
	
	public function getCurrentInteraction(){
		$returnValue = null;
		if($this->hasRequestParameter('interactionSerial')){
			$interaction = $this->qtiService->getInteractionBySerial($this->getRequestParameter('interactionSerial'));
			
			if(!empty($interaction)){
				$returnValue = $interaction;
			}
		}else{
			throw new Exception('no request parameter "interactionSerial" found');
		}
		
		return $returnValue;
	}
	
	public function getCurrentChoice(){
		$returnValue = null;
		if($this->hasRequestParameter('choiceSerial')){
			$choice = $this->qtiService->getDataBySerial($this->getRequestParameter('choiceSerial'), 'taoItems_models_classes_QTI_Choice');
			if(!empty($choice)){
				$returnValue = $choice;
			}
		}else{
			throw new Exception('no request parameter "choiceSerial" found');
		}
		
		return $returnValue;
	}
	
	public function getCurrentGroup(){
		$returnValue = null;
		if($this->hasRequestParameter('groupSerial')){
			$group = $this->qtiService->getDataBySerial($this->getRequestParameter('groupSerial'), 'taoItems_models_classes_QTI_Group');
			if(!empty($group)){
				$returnValue = $group;
			}
		}else{
			throw new Exception('no request parameter "groupSerial" found');
		}
		
		return $returnValue;
	}
	
	public function getCurrentResponse(){
		$returnValue = null;
		if($this->hasRequestParameter('responseSerial')){
			$response = $this->qtiService->getDataBySerial($this->getRequestParameter('responseSerial'), 'taoItems_models_classes_QTI_Response');
			if(!empty($response)){
				$returnValue = $response;
			}
		}else{
			try{
				//second chance: try getting the response from the interaction, is set in the request parameter
				$interaction = $this->getCurrentInteraction();
				if(!empty($interaction)){
					$response = $this->service->getInteractionResponse($interaction);
					if(!empty($response)){
						$returnValue = $response;
					}
				}
			}catch(Exception $e){
				throw new Exception('cannot find the response no request parameter "responseSerial" found');
			}
			
		}
		
		return $returnValue;
	}
	
	public function editItem(){
	
	}
	
	
	//to be called at the same time as edit response
	public function editInteraction(){
		
		$interaction = $this->getCurrentInteraction();
		
		//build the form with its method "toForm"
		$myForm = $interaction->toForm();
		
		//get the itnteraction's choices
		$choices = $this->service->getInteractionChoices($interaction);
		$choiceForms = array();
		
		$interactionType = strtolower($interaction->getType());
		switch($interactionType){
			case 'match':{
				$i = 0;
				$groupSerials = array();
				foreach($choices as $groupSerial=>$group){
					
					$groupSerials[$i] = $groupSerial;
					$choiceForms[$groupSerial] = array();
					foreach($group as $choice){
						$choiceForms[$groupSerial][$choice->getSerial()] = $choice->toForm()->render();
					}
					$i++;
				}
				$this->setData('groupSerials', $groupSerials);
				break;
			}
			case 'gapmatch':{
				/*
				//get group form:
				$groupForms = array();
				foreach($this->service->getInteractionGroups($interaction) as $group){
					//order does not matter:
					$groupForms[] = $group->toForm($interaction)->render();
				}
				$this->setData('formGroups', $groupForms);
				
				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
				*/
				break;
			}
			//graphic interactions:
			case 'graphicgapmatch':{
				$groups = array();
				foreach($interaction->getGroups() as $group){
					$groups[] = $group->getSerial();
				}
				
				$this->setData('groups', $groups);
			}
			case 'hotspot':
			case 'graphicorder':
			case 'graphicassociate':{
				$object = $interaction->getObject();
				$this->setData('backgroundImagePath', isset($object['data'])?$object['data']:'');
				$this->setData('width', isset($object['width'])?$object['width']:'');
				$this->setData('height', isset($object['height'])?$object['height']:'');
				break;
			}
			default:{
				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
			}
		}
				
		//display the template, according to the type of interaction
		$templateName = 'QTIAuthoring/form_interaction_'.strtolower($interaction->getType()).'.tpl';
		$this->setData('interactionSerial', $interaction->getSerial());
		$this->setData('formInteraction', $myForm->render());
		$this->setData('formChoices', $choiceForms);
		$this->setData('interactionData', $this->service->getInteractionData($interaction));
		$this->setData('orderedChoices', $choices);
		$this->setView($templateName);
	}
	
	//called on interaction edit form loaded
	//called when the choices forms need to be reloaded
	public function editChoices(){
		
		$interaction = $this->getCurrentInteraction();
		
		//get the itnteraction's choices
		$choices = $this->service->getInteractionChoices($interaction);
		$choiceForms = array();
		
		$interactionType = strtolower($interaction->getType());
		switch($interactionType){
			case 'match':{
				$i = 0;
				$groupSerials = array();
				foreach($choices as $groupSerial=>$group){
					
					$groupSerials[$i] = $groupSerial;
					$choiceForms[$groupSerial] = array();
					foreach($group as $choice){
						$choiceForms[$groupSerial][$choice->getSerial()] = $choice->toForm()->render();
					}
					$i++;
				}
				$this->setData('groupSerials', $groupSerials);
				break;
			}
			case 'gapmatch':
			case 'graphicgapmatch':{
				//get group form:
				$groupForms = array();
				foreach($this->service->getInteractionGroups($interaction) as $group){
					//order does not matter:
					$groupForms[$group->getSerial()] = $group->toForm($interaction)->render();
				}
				$this->setData('formGroups', $groupForms);
				
				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
				break;
			}
			default:{
				//get choice forms:
				foreach($choices as $order=>$choice){
					$choiceForms[$choice->getSerial()] = $choice->toForm()->render();
				}
			}
		}
		
		$templateName = 'QTIAuthoring/form_choices_'.strtolower($interaction->getType()).'.tpl';
		$this->setData('formChoices', $choiceForms);
		$this->setData('orderedChoices', $choices);
		$this->setView($templateName);
	}
	
	public function saveInteraction(){
	
		$interaction = $this->getCurrentInteraction();
		
		$myForm = $interaction->toForm();
		
		$saved = false;
		$reloadResponse = false;
		$newGraphicObject = array();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				// var_dump($myForm->getValues());
				$values = $myForm->getValues();
				
				if(isset($values['interactionIdentifier'])){
					// die('set identifier');
					if($values['interactionIdentifier'] != $interaction->getIdentifier()){
						$this->service->setIdentifier($interaction, $values['interactionIdentifier']);
					}
					unset($values['interactionIdentifier']);
				}
				
				//for block interactions
				if(isset($values['prompt'])){
					$this->service->setPrompt($interaction, $this->getPostedData('prompt'));
					unset($values['prompt']);
				}
				
				//for graphic interactions:
				if(isset($values['object_width'])){
					$newGraphicObject['width'] = intval($values['object_width']);
				}
				
				if(isset($values['object_height'])){
					$newGraphicObject['height'] = intval($values['object_height']);
				}
					
				if(isset($values['object_data'])){
				
					$oldObject = $interaction->getObject();
					
					//get mime type
					$imageFilePath = trim($values['object_data']);
					$mimeType = tao_helpers_File::getMimeType($imageFilePath);
					
					$validImageType = array(
						'image/png',
						'image/jpeg',
						'image/bmp'
					);
					
					if(in_array($mimeType, $validImageType)){
					
						// $newObject = array(
							// 'data' => trim($values['object_data']),
							// 'width' => isset($values['object_width'])? intval($values['object_width']):0,
							// 'height' => isset($values['object_height'])? intval($values['object_height']):0
						// );
						$newGraphicObject['data'] = $imageFilePath;
						// $newObject['data'] = $imageFilePath;
						// if(isset($oldObject['data'])){
							// if($oldObject['data'] != $newObject['data']){
								// $newGraphicObject['data'] = $newObject['data'];
							// }
						// }else{
							// $newGraphicObject['data'] = $newObject['data'];
						// }
						
						
					}else{
						$newGraphicObject['errorMessage'] = __('invalid image mime type');
					}
				}
				$interaction->setObject($newGraphicObject);
				
				//hottext and gapmatch interaction:
				$data = '';
				if($this->hasRequestParameter('data')){//the content "data" is not included in the interacion form but with the wysiwyg editor so need to get it this way
					$data = urldecode($this->getRequestParameter('data'));
				}
				
				unset($values['interactionSerial']);
				
				
				foreach($values as $key=>$value){
					if(preg_match('/^max/', $key)){
						if($interaction->getOption($key) != $value){
							$reloadResponse = true;
						}
						break;
					}
				}
				
				//save all options before updating the interaction response
				$this->service->editOptions($interaction, $values);
				if($reloadResponse){
					//update the cardinality, just in case it has been changed:
					//may require upload of the response form, since the maximum allowed response may have changed!
					$this->service->updateInteractionResponseOptions($interaction);
					
					//costly...
					//then simulate get+save response data to filter affected response variables
					$this->service->saveInteractionResponse($interaction, $this->service->getInteractionResponseData($interaction));
				}
				
				$choiceOrder = array();
				if(isset($_POST['choiceOrder'])){
				
					$choiceOrder = $_POST['choiceOrder'];
					
				}elseif( isset($_POST['choiceOrder0']) && isset($_POST['choiceOrder1'])){//for match interaction
					
					for($i=0; $i<2; $i++){//TODO: to be tested...
						$groupOrder = $_POST['choiceOrder'.$i];
						if(isset($groupOrder['groupSerial'])){
							$groupSerial = $groupOrder['groupSerial'];
							unset($groupOrder['groupSerial']);
							$choiceOrder[$groupSerial] = $groupOrder;
						}
					}
					
				}
				$this->service->setInteractionData($interaction, $data, $choiceOrder);
				
				$saved  = true;
			}
		}
		
		echo json_encode(array(
			'saved' => $saved,
			'reloadResponse' => $reloadResponse,
			'newGraphicObject' => $newGraphicObject
		));
		
	}
	public function saveChoice(){
		$choice = $this->getCurrentChoice();
		
		$myForm = $choice->toForm();
		$saved = false;
		$identifierUpdated = false;
		if($myForm->isSubmited()){
			if($myForm->isValid()){
			
				$values = $myForm->getValues();
				unset($values['choiceSerial']);//choiceSerial to be deleted since only used to get the choice qti object
				
				if(isset($values['choiceIdentifier'])){
					if($values['choiceIdentifier'] != $choice->getIdentifier()){
						$this->service->setIdentifier($choice, $values['choiceIdentifier']);
						$identifierUpdated = true;
					}
					unset($values['choiceIdentifier']);
				}
				
				if(isset($values['data'])){
					$this->service->setData($choice, $this->getPostedData('data'));
					unset($values['data']);
				}
				
				//for graphic interactions:
				$newGraphicObject = array();
				if(intval($values['object_width'])){
					$newGraphicObject['width'] = intval($values['object_width']);
				}
				if(intval($values['object_height'])){
					$newGraphicObject['height'] = intval($values['object_height']);
				}
				if(isset($values['object_data'])){
					//get mime type
					$imageFilePath = trim($values['object_data']);
					$mimeType = tao_helpers_File::getMimeType($imageFilePath);
					
					$validImageType = array(
						'image/png',
						'image/jpeg',
						'image/bmp'
					);
					
					if(in_array($mimeType, $validImageType)){
						$newGraphicObject['data'] = $imageFilePath;
					}else{
						$errorMessage = __('invalid image mime type for the image file '+$imageFilePath);
					}
				}
				$choice->setObject($newGraphicObject);
				unset($values['object_width']);
				unset($values['object_height']);
				unset($values['object_data']);
				
				//finally save the other options:
				$this->service->setOptions($choice, $values);
				
				$saved = true;
			}
		}
		
		$choiceFormReload = false;
		if($identifierUpdated){
			$interaction = $this->qtiService->getComposingData($choice);
			$choiceFormReload = $this->requireChoicesUpdate($interaction);
			$interaction->addChoice($choice);
			$interaction = null;
		}
		
		echo json_encode(array(
			'saved' => $saved,
			'choiceSerial' => $choice->getSerial(),
			'identifierUpdated' => $identifierUpdated,
			'reload' => $choiceFormReload,
			'errorMessage' => $errorMessage
		));
	}
	
	//save the group properties, specific to gapmatch interaction where a group is considered as a gap:
	//not called when the choice order has been changed, such changes are done by saving the itneraction data
	public function saveGroup(){
		$group = $this->getCurrentGroup();
		
		$myForm = $group->toForm();
		$saved = false;
		$identifierUpdated = false;
		if($myForm->isSubmited()){
			if($myForm->isValid()){
			
				$values = $myForm->getValues();
				
				if(isset($values['groupIdentifier'])){
					if($values['groupIdentifier'] != $group->getIdentifier()){
						$identifierUpdated = $this->service->setIdentifier($group, $values['groupIdentifier']);
					}
				}
				
				$matchGroup = array();
				if(!empty($values['matchGroup']) && is_array($values['matchGroup'])){
					$matchGroup = $values['matchGroup'];
				}
				unset($values['matchGroup']);
				$group->setChoices($matchGroup);
				
				$choiceOrder = array();
				if(isset($_POST['choiceOrder'])){
					$choiceOrder = $_POST['choiceOrder'];
				}
				$this->service->setGroupData($group, $choiceOrder, null, true);//the 3rd parameter interaction is not required as the method only depends on the group
				
				unset($values['groupSerial']);
				unset($values['groupIdentifier']);
				$this->service->setOptions($group, $values);
				
				$saved = true;
			}
		}
		
		$choiceFormReload = false;
		if($identifierUpdated){
			$interaction = $this->qtiService->getComposingData($group);
			$choiceFormReload = $this->requireChoicesUpdate($interaction);
			$interaction->addGroup($group);
			$interaction = null;
		}
		
		echo json_encode(array(
			'saved' => $saved,
			'groupSerial' => $group->getSerial(),
			'identifierUpdated' => $identifierUpdated,
			'reload' => $choiceFormReload
		));
	}
	
	public function addGroup(){
		$added = false;
		$groupSerial = '';//a gap basically is a "group", the content of which is by default all available choices in the interaction
		$textContent = '';
		$interaction = null;
		$interaction = $this->getCurrentInteraction();
		$interactionData = $this->getPostedInteractionData();
		
		$group = $this->service->addGroup($interaction, $interactionData);
		
		if(!is_null($group)){
			$interactionData = $this->service->getInteractionData($interaction);//do not convert to html entities...
			
			//everything ok:
			$added = true;
			$groupSerial = $group->getSerial();
		}
		
		echo json_encode(array(
			'added' => $added,
			'groupSerial' => $groupSerial,
			'groupForm' => $group->toForm()->render(),
			'interactionData' => html_entity_decode($interactionData),
			'reload' => ($added)?$this->requireChoicesUpdate($interaction):false
		));
	}
		
	public function editResponseProcessing(){
	
		$item = $this->getCurrentItem();
		
		$formContainer = new taoItems_actions_QTIform_ResponseProcessing($item);
		$myForm = $formContainer->getForm();
		
		$this->setData('form', $myForm->render());
		$processingType = $formContainer->getProcessingType();
		
		// $responseMappingMode = false;
		// if($processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE || $processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT){
			// $responseMappingMode = true;
		// }
		// $this->setData('responseMappingMode', $responseMappingMode);//no longer definied in the item response proc:
		
		$warningMessage = '';
		if($processingType != 'template'){
			$warningMessage = __('The custom response processing type is currently not fully supported in this tool. Removing interactions or choices is not recommended.');
		}
		
		$this->setData('warningMessage', $warningMessage);
		$this->setView('QTIAuthoring/form_response_processing.tpl');
	}
	
	public function saveResponseProcessing(){
		
		$item = $this->getCurrentItem();
		$responseProcessingType = tao_helpers_Uri::decode($this->getRequestParameter('responseProcessingType'));
		$customRule = $this->getRequestParameter('customRule');
		
		$saved = $this->service->setResponseProcessing($item, $responseProcessingType, $customRule);
		
		echo json_encode(array(
			'saved' => $saved,
			'responseMode' => $this->isResponseMappingMode($responseProcessingType)
		));
	}
	
	protected function isResponseMappingMode($processingType){
		$responseMappingMode = false;
		if($processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE || $processingType == QTI_RESPONSE_TEMPLATE_MAP_RESPONSE_POINT){
			$responseMappingMode = true;
		}
		
		return $responseMappingMode;
	}
	
	
	public function editMappingOptions(){
		$response = $this->getCurrentResponse();
		
		$formContainer = new taoItems_actions_QTIform_Mapping($response);
		// var_dump($formContainer->getForm()->render());
		$this->setData('form', $formContainer->getForm()->render());
		$this->setView('QTIAuthoring/form_response_mapping.tpl');
		
	}
	
	public function saveMappingOptions(){
		$response = $this->getCurrentResponse();
		
		$mappingOptions = $_POST;
		
		$this->service->setMappingOptions($response, $mappingOptions);
		$saved = true;
		
		echo json_encode(array(
			'saved' => $saved
		));
	}
	
	public function saveResponse(){
		
		$saved = false;
		
		//get the response from the interaction:
		$interaction = $this->getCurrentInteraction();
		
		if($this->hasRequestParameter('responseDataString')){
			
			$responseData = json_decode(html_entity_decode($this->getRequestParameter('responseDataString')));
			
			$saved = $this->service->saveInteractionResponse($interaction, $responseData);
		}
		
		echo json_encode(array(
			'saved' => $saved
		));
	}
	
	public function saveResponseProperties(){
		
		$saved = false;
		$templateHasChanged = false;
		$setResponseMappingMode = false;
		$response = $this->getCurrentResponse();
		
		if(!is_null($response)){
		
			if($this->hasRequestParameter('baseType')){
				if($this->hasRequestParameter('baseType')){
					$this->service->editOptions($response, array('baseType'=>$this->getRequestParameter('baseType')));
					$saved = true;
				}
				
				if($this->hasRequestParameter('ordered')){
					if(intval($this->getRequestParameter('ordered')) == 1){
						$this->service->editOptions($response, array('cardinality'=>'ordered'));
					}else{
						//reset the cardinality:
						$parentInteraction = $this->qtiService->getComposingData($response);
						if(!is_null($parentInteraction)){
							$this->service->editOptions($response, array('cardinality' => $parentInteraction->getCardinality() ));
						}else{
							throw new Exception('cannot find the parent interaction');
						}
						
					}
					$saved = true;
				}
			}
			
			if($this->hasRequestParameter('processingTemplate')){
				$processingTemplate = tao_helpers_Uri::decode($this->getRequestParameter('processingTemplate'));
				if($response->getHowMatch() != $processingTemplate){
					$templateHasChanged = true;
				}
				$saved = $this->service->setResponseTemplate($response, $processingTemplate);
				if($saved) $setResponseMappingMode = $this->isResponseMappingMode($processingTemplate);
			}
		}
		// var_dump($response);
		
		echo json_encode(array(
			'saved' => $saved,
			'setResponseMappingMode' => $setResponseMappingMode,
			'templateHasChanged' => $templateHasChanged
		));
	}
	
	//edit the interaction response:
	public function editResponse(){
		$interaction = $this->getCurrentInteraction();
		$item = $this->getCurrentItem();
		$responseProcessing = $item->getResponseProcessing();
		
		$displayGrid = false;
		$columnModel = array();
		$responseData = array();
		$xhtmlForm = '';
		$interactionType = strtolower($interaction->getType());
			
		//check the type...
		//only display response grid when the response template is templates driven:
		if($responseProcessing instanceof taoItems_models_classes_QTI_response_TemplatesDriven){
			
			//only allow the selection of a template for "templates driven" response processing:
			$responseForm = $this->service->getInteractionResponse($interaction)->toForm();
			if(!is_null($responseForm)){
				$xhtmlForm = $responseForm->render();
			}
			
			//prepare data for the response grid:
			$displayGrid = true;
			//get model:
			$columnModel = $this->service->getInteractionResponseColumnModel($interaction);
			$responseData = $this->service->getInteractionResponseData($interaction);
			
			if($interactionType == 'order'){
				//special case for order interaction:
				
			}
		}else{
			$xhtmlForm .= '<b>';
			$xhtmlForm .= __('The response form is available for templates driven item only.<br/>');
			$xhtmlForm .= '</b>';
		}
		
		
		echo json_encode(array(
			'ok' => true,
			'displayGrid' => $displayGrid,
			'interactionType' => $interactionType,
			'colModel' => $columnModel,
			'data' => $responseData,
			'maxChoices' => intval($interaction->getCardinality(true)),
			'responseForm' => $xhtmlForm
		));
		
	}
	
	public function manageStyleSheets(){
		//create upload form:
		$item = $this->getCurrentItem();
		$formContainer = new taoItems_actions_QTIform_CSSuploader($item);
		$myForm = $formContainer->getForm();
		
		if($myForm->isSubmited()){
			if($myForm->isValid()){
				$data = $myForm->getValues();
				
				if(isset($data['css_import']['uploaded_file'])){
					//get the file and store it in the proper location:
					$baseName = basename($data['css_import']['uploaded_file']);
					
					$fileData = $this->getCurrentStyleSheet($baseName);
					
					if(!empty($fileData)){
						tao_helpers_File::move($data['css_import']['uploaded_file'], $fileData['path']);
					
						$cssFiles = $item->getStyleSheets();
						$cssFiles[] = array(
							'title' => $data['title'],
							'href' => $fileData['href']
						);
						$item->setStyleSheets($cssFiles);
					}
				}
				
			}
		}
		
		$cssFiles = array();
		foreach($item->getStyleSheets() as $file){
			$cssFiles[] = array(
				'href' => $file['href'],
				'title' => $file['title'],
				'downloadUrl' => _url('getStyleSheet', null, null, array(
						'itemSerial' => tao_helpers_Uri::encode($item->getSerial()),
						'itemUri' 	=> tao_helpers_Uri::encode($this->getCurrentItemResource()->uriResource),
						'css_href' => $file['href']
				))
			);
		}
		
		$this->setData('formTitle', __('Manage item content'));
		$this->setData('myForm', $myForm->render());
		$this->setData('cssFiles', $cssFiles);
		$this->setView('QTIAuthoring/css_manager.tpl');
	}
	
	public function deleteStyleSheet(){
	
		$deleted = false;
		
		$fileData = $this->getCurrentStyleSheet();
		
		//get the full path of the file and unlink the file:
		if(!empty($fileData)){
			tao_helpers_File::remove($fileData['path']);
			
			$item = $this->getCurrentItem();
			
			$files = $item->getStylesheets();
			
			foreach($files as $key=>$file){
				if($file['href'] == $fileData['href']){
					unset($files[$key]);
				}
			}
			
			$item->setStylesheets($files);
			
			$deleted = true;
		}
		
		echo json_encode(array('deleted' => $deleted));
	}
	
	public function getStyleSheet(){
		$fileData = $this->getCurrentStyleSheet();
		if(!empty($fileData)){
			$fileName = basename($fileData['path']);
		
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: public");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Description: File Transfer");
			header("Content-Type: text/css");
			header("Content-Disposition: attachment; filename=\"$fileName\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . filesize($fileData['path']));

			// download
			// @readfile($file_path);
			$file = @fopen($fileData['path'], "rb");
			if ($file) {
				while ( ! feof($file) ) {
					print(fread($file, 1024 * 8));
					flush();
					if (connection_status() != 0) {
						@fclose($file);
						die();
					}
				}
				@fclose($file);
			}
		}else{
			throw new Exception('The style file cannot be found');
		}
		
	}
	
	public function getCurrentStyleSheet($baseName=''){
		$returnValue = array();
		
		$itemResource = $this->getCurrentItemResource();
		$folderName = substr($itemResource->uriResource, strpos($itemResource->uriResource, '#') + 1);
		$basePath = BASE_PATH.'/views/runtime/'.$folderName.'/';
		$baseWWW = BASE_WWW.'runtime/'.$folderName.'/';
		
		if(!empty($baseName)){
			//creation mode:
			$css_href = 'style/'.$baseName;
			
			$returnValue = array(
				'title' => $baseName,
				'href' => $css_href,
				'path' => $basePath.$css_href,
				'hrefAbsolute' => $baseWWW.$css_href
			);
					
		}else{
			//get mode:
			$css_href = $this->getRequestParameter('css_href');
			if(!empty($css_href)){
				$files = $this->getCurrentItem()->getStylesheets();
				foreach($files as $file){
					if($file['href'] == $css_href){
						$returnValue = $file;
						$returnValue['path'] = $basePath.$css_href;
						$returnValue['hrefAbsolute'] = $baseWWW.$css_href;
						break;
					}
				}
			}
		}
		
		return $returnValue;
	}
	
	
}
?>