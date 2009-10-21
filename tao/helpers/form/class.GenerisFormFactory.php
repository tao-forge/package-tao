<?php

error_reporting(E_ALL);

/**
 * The GenerisFormFactory enables you to create Forms using rdf data and the
 * api to provide it. You can give any node of your ontology and the factory
 * create the appriopriate form. The Generis ontology (with the Widget Property)
 * required to use it.
 * Now only the xhtml rendering mode is implemented
 *
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package tao
 * @see core_kernel_classes_* packages
 * @subpackage helpers_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-45--70bace43:123ffff90e9:-8000:00000000000018CC-includes begin
// section 10-13-1-45--70bace43:123ffff90e9:-8000:00000000000018CC-includes end

/* user defined constants */
// section 10-13-1-45--70bace43:123ffff90e9:-8000:00000000000018CC-constants begin
// section 10-13-1-45--70bace43:123ffff90e9:-8000:00000000000018CC-constants end

/**
 * The GenerisFormFactory enables you to create Forms using rdf data and the
 * api to provide it. You can give any node of your ontology and the factory
 * create the appriopriate form. The Generis ontology (with the Widget Property)
 * required to use it.
 * Now only the xhtml rendering mode is implemented
 *
 * @access public
 * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
 * @package tao
 * @see core_kernel_classes_* packages
 * @subpackage helpers_form
 */
class tao_helpers_form_GenerisFormFactory
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * render mode constants
     *
     * @access public
     * @var string
     */
    const RENDER_MODE_XHTML = 'xhtml';

    /**
     * the default top level (to stop the recursivity look up) class commly used
     *
     * @access public
     * @var string
     */
    const DEFAULT_TOP_LEVEL_CLASS = 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject';

    /**
     * a list of forms currently instanciated with the factory (can be used to
     * multiple forms)
     *
     * @access private
     * @var array
     */
    private static $forms = array();

    // --- OPERATIONS ---

    /**
     * Create a form from a class of your ontology, the form data comes from the
     * The default rendering is in xhtml
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Class clazz
     * @param  Resource instance
     * @param  string renderMode
     * @return tao_helpers_form_Form
     */
    public static function instanceEditor( core_kernel_classes_Class $clazz,  core_kernel_classes_Resource $instance = null, $renderMode = 'xhtml')
    {
        $returnValue = null;

        // section 10-13-1-45--70bace43:123ffff90e9:-8000:00000000000018CD begin
		
		if(!is_null($clazz)){
			
			(strlen(trim($clazz->getLabel())) > 0) ? $name = strtolower($clazz->getLabel()) : $name = 'form_'.(count(self::$forms)+1);
			
			//use the right implementation (depending the render mode)
			switch($renderMode){
				case self::RENDER_MODE_XHTML:
					$myForm = new tao_helpers_form_xhtml_Form($name);
					$myForm->setDecorator(new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')));
					$hiddenEltClass = 'tao_helpers_form_elements_xhtml_Hidden';
					break;
				default: 
					return null;
			}
			
			$defaultProperties 	= self::getDefaultProperties();
			$classProperties	= self::getClassProperties($clazz, new core_kernel_classes_Class(self::DEFAULT_TOP_LEVEL_CLASS));
			
			//@todo take the properties ahead the class recursivly till the top level class  
			foreach(array_merge($defaultProperties, $classProperties) as $property){
				
				$property->feed();
				
				//map properties widgets to form elments 
				$element = self::elementMap($property, $renderMode);
				if(!is_null($element)){
					
					//take instance values to populate the form
					if(!is_null($instance)){
						$values = $instance->getPropertyValuesCollection($property);
						foreach($values->getIterator() as $value){
							if(!is_null($value)){
								if($value instanceof core_kernel_classes_Resource){
									$element->setValue($value->uriResource);
								}
								if($value instanceof core_kernel_classes_Literal){
									$element->setValue((string)$value);
								}
							}
						}
					}
					if(in_array($property, $defaultProperties)){
						$element->setLevel(0);
					}
					else{
						$element->setLevel(1);
					}
					$myForm->addElement($element);
				}
			}
			
			//add an hidden elt for the class uri
			$classUriElt = new $hiddenEltClass('classUri');
			$classUriElt->setValue(tao_helpers_Uri::encode($clazz->uriResource));
			$classUriElt->setLevel(2);
			$myForm->addElement($classUriElt);
			
			if(!is_null($instance)){
				//add an hidden elt for the instance Uri
				$instanceUriElt = new $hiddenEltClass('uri');
				$instanceUriElt->setValue(tao_helpers_Uri::encode($instance->uriResource));
				$instanceUriElt->setLevel(2);
				$myForm->addElement($instanceUriElt);
			}
			
			//form data evaluation
			$myForm->evaluate();		
				
			self::$forms[$name] = $myForm;
			$returnValue = self::$forms[$name];
		}
		
        // section 10-13-1-45--70bace43:123ffff90e9:-8000:00000000000018CD end

        return $returnValue;
    }

    /**
     * create a Form to add a subclass to the rdfs:Class clazz
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Class clazz
     * @param  string renderMode
     * @return tao_helpers_form_Form
     */
    public static function classEditor( core_kernel_classes_Class $clazz, $renderMode = 'xhtml')
    {
        $returnValue = null;

        // section 127-0-1-1-173d16:124524d2e59:-8000:0000000000001A5D begin
		
		if(!is_null($clazz)){
			
			(strlen(trim($clazz->getLabel())) > 0) ? $name = strtolower($clazz->getLabel()) : $name = 'form_'.(count(self::$forms)+1);
			
			//use the right implementation (depending the render mode)
			switch($renderMode){
				case self::RENDER_MODE_XHTML:
					$myForm = new tao_helpers_form_xhtml_Form($name);
					$myForm->setDecorator(new tao_helpers_form_xhtml_TagWrapper(array('tag' => 'div')));
					$hiddenEltClass = 'tao_helpers_form_elements_xhtml_Hidden';
					break;
				default: 
					return null;
			}
			
			$defaultProperties = self::getDefaultProperties();
			$otherProperties = self::getClassProperties(new core_kernel_classes_Class( 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property'));
			foreach(array_merge($defaultProperties, $otherProperties) as $property){
				
				//map properties widgets to form elments 
				$element = self::elementMap($property, $renderMode);
				if(!is_null($element)){
					
					//take property values to populate the form
					$values = $clazz->getPropertyValuesCollection($property);
					foreach($values->getIterator() as $value){
						if(!is_null($value)){
							if($value instanceof core_kernel_classes_Resource){
								$element->setValue($value->uriResource);
							}
							if($value instanceof core_kernel_classes_Literal){
								$element->setValue((string)$value);
							}
						}
					}
					if(in_array($property, $defaultProperties)){
						$element->setLevel(0);
					}
					else{
						$element->setLevel(1);
					}
					$myForm->addElement($element);
				}
			}
			
			//add an hidden elt for the class uri
			$classUriElt = new $hiddenEltClass('classUri');
			$classUriElt->setValue(tao_helpers_Uri::encode($clazz->uriResource));
			$classUriElt->setLevel(3);
			$myForm->addElement($classUriElt);
			
			
			//form data evaluation
			$myForm->evaluate();		
				
			self::$forms[$name] = $myForm;
			$returnValue = self::$forms[$name];
		}
		
        // section 127-0-1-1-173d16:124524d2e59:-8000:0000000000001A5D end

        return $returnValue;
    }

    /**
     * Enable you to map an rdf property to a form element using the Widget
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Property property
     * @param  string renderMode
     * @return tao_helpers_form_FormElement
     */
    public static function elementMap( core_kernel_classes_Property $property, $renderMode = 'xhtml')
    {
        $returnValue = null;

        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001937 begin
		
		//create the element from the right widget
		$widgetResource = $property->getWidget();
		$widget = ucfirst(strtolower(substr($widgetResource->uriResource, strrpos($widgetResource->uriResource, '#') + 1 )));
		$elementClass = 'tao_helpers_form_elements_'.$renderMode.'_'.$widget;
		
		if(!class_exists($elementClass)){
			return null;
		}
		
		$element = new $elementClass(); 	//instanciate
		
		//security checks for dynamic instantiation
		if(!$element instanceof tao_helpers_form_FormElement){
			return null;
		}
		if($element->getWidget() != $widgetResource->uriResource){
			return null;
		}
		
		//use uri as element name					
		$element->setName( tao_helpers_Uri::encode($property->uriResource));

		//use the property label as element description
		(strlen(trim($property->getLabel())) > 0) ? $propDesc = $property->getLabel() : $propDesc = 'field '.(count($myForm->getElements())+1);	
		$element->setDescription($propDesc);
		
		//multi elements use the property range as options
		if(method_exists($element, 'setOptions')){
			$range = $property->getRange();
			if($range != null){
				$options = array();
				foreach($range->getInstances() as $rangeInstance){
					$options[ tao_helpers_Uri::encode($rangeInstance->uriResource) ] = $rangeInstance->getLabel();
				}
				$element->setOptions($options);
			}
		}
		$returnValue = $element;
		
        // section 127-0-1-1-3ed01c83:12409dc285c:-8000:0000000000001937 end

        return $returnValue;
    }

    /**
     * Short description of method getClassProperties
     *
     * @access public
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @param  Class clazz
     * @param  Class topLevelClazz
     * @return array
     */
    public static function getClassProperties( core_kernel_classes_Class $clazz,  core_kernel_classes_Class $topLevelClazz = null)
    {
        $returnValue = array();

        // section 127-0-1-1-2db84171:12476b7fa3b:-8000:0000000000001AAB begin
		
		if(is_null($topLevelClazz)){
			$returnValue = $clazz->getProperties(true);
		}
		else{
			$returnValue = $clazz->getProperties(false);
			$top = false;
			do{
				$parents = $clazz->getParentClasses(false);
				if(count($parents) == 0){
					break;
				}
				foreach($parents as $parent){
					if( !($parent instanceof core_kernel_classes_Class) || is_null($parent)){
						$top = true; 
						break;
					}
					
					$returnValue = array_merge($returnValue, $parent->getProperties(false));
					if($parent->uriResource == $topLevelClazz->uriResource){
						$top = true; 
						break;
					}
				}
			} while(!$top);
		}
		
		
        // section 127-0-1-1-2db84171:12476b7fa3b:-8000:0000000000001AAB end

        return (array) $returnValue;
    }

    /**
     * get the default properties to add to every forms
     *
     * @access protected
     * @author Bertrand Chevrier, <chevrier.bertrand@gmail.com>
     * @return array
     */
    protected static function getDefaultProperties()
    {
        $returnValue = array();

        // section 127-0-1-1--5ce810e0:1244ce713f8:-8000:0000000000001A43 begin

		$defaultUris = array(
			'http://www.w3.org/2000/01/rdf-schema#label'
		);
		
		$resourceClass = new core_kernel_classes_Class('http://www.w3.org/2000/01/rdf-schema#Resource');
		foreach($resourceClass->getProperties() as $property){
			if(in_array($property->uriResource, $defaultUris)){
				array_push($returnValue, $property);
			}
		}
		
        // section 127-0-1-1--5ce810e0:1244ce713f8:-8000:0000000000001A43 end

        return (array) $returnValue;
    }

} /* end of class tao_helpers_form_GenerisFormFactory */

?>