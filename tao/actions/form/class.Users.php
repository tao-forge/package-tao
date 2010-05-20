<?php

error_reporting(E_ALL);

/**
 * This container initialize the user edition form.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DF8-includes begin
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DF8-includes end

/* user defined constants */
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DF8-constants begin
// section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DF8-constants end

/**
 * This container initialize the user edition form.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class tao_actions_form_Users
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  Resource user
     * @return mixed
     */
    public function __construct( core_kernel_classes_Class $clazz,  core_kernel_classes_Resource $user = null)
    {
        // section 127-0-1-1-7dfb074:128afd58ed5:-8000:0000000000001F43 begin
        
    	if(is_null($clazz)){
    		throw new Exception('Set the user class in the parameters');	
    	}
    	
    	$options = array();
    	$service = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
    	if(!is_null($user)){
			$options['mode'] = 'edit';
    	}
    	else{
    		$user = $service->createInstance($clazz);
			$options['mode'] = 'add';
    	}
    	tao_helpers_form_GenerisFormFactory::$topLevelClass = CLASS_GENERIS_USER;
    	$this->form = tao_helpers_form_GenerisFormFactory::instanceEditor($clazz, $user, 'users');
    	
    	parent::__construct(array(), $options);
    	
        // section 127-0-1-1-7dfb074:128afd58ed5:-8000:0000000000001F43 end
    }

    /**
     * Short description of method initForm
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initForm()
    {
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DFA begin
		
		$this->form->setActions(tao_helpers_form_FormFactory::getCommonActions('top'), 'top');
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DFA end
    }

    /**
     * Short description of method initElements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DFC begin
		
    	
    	
		if(!isset($this->options['mode'])){
			throw new Exception("Please set a mode into container options ");
		}
		
		//login field
		$loginElement = $this->form->getElement(tao_helpers_Uri::encode(PROPERTY_USER_LOGIN));
		$loginElement->setDescription(__('Login *'));
		if($this->options['mode'] == 'add'){
			$loginElement->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('NotEmpty'),
				tao_helpers_form_FormFactory::getValidator('Callback', array(
					'class' => 'tao_models_classes_UserService', 
					'method' => 'loginAvailable', 
					'message' => __('login already exist') 
				))
			));
		}
		else{
			$loginElement->setAttributes(array('readonly' => 'true'));
		}
		
		//password field
		if($this->options['mode'] == 'add'){
			$pass1Element = tao_helpers_form_FormFactory::getElement('password1', 'Hiddenbox');
			$pass1Element->setDescription(__('Password *'));
			$pass1Element->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('NotEmpty'),
				tao_helpers_form_FormFactory::getValidator('Length', array('min' => 4))
			));
			$this->form->addElement($pass1Element);
			
			$pass2Element = tao_helpers_form_FormFactory::getElement('password2', 'Hiddenbox');
			$pass2Element->setDescription(__('Repeat password *'));
			$pass2Element->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('NotEmpty'),
				tao_helpers_form_FormFactory::getValidator('Password', array('password2_ref' => $pass1Element)),
			));
			$this->form->addElement($pass2Element);
		}
		else{
			
			$validatePasswords = true;
			if(isset($_POST['users_sent']) && isset($_POST['password1'])){
				if(empty($_POST['password1'])) {
					$validatePasswords = false;
				}
			}
			
			$pass0Element = tao_helpers_form_FormFactory::getElement('password0', 'Hidden');
			if(isset($this->data['password'])){
				$pass0Element->setValue($this->data['password']);
			}
			$this->form->addElement($pass0Element);
			
			$pass1Element = tao_helpers_form_FormFactory::getElement('password1', 'Hiddenbox');
			$pass1Element->setDescription(__('Old Password'));
			$pass1Element->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('Md5Password', array('password2_ref' => $pass0Element)),
			));
			if(!$validatePasswords){
				$pass1Element->setForcedValid();
			}
			$this->form->addElement($pass1Element);
			
			$pass2Element = tao_helpers_form_FormFactory::getElement('password2', 'Hiddenbox');
			$pass2Element->setDescription(__('New password'));
			$pass2Element->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('Length', array('min' => 4))
			));
			if(!$validatePasswords){
				$pass2Element->setForcedValid();
			}
			$this->form->addElement($pass2Element);
			
			$pass3Element = tao_helpers_form_FormFactory::getElement('password3', 'Hiddenbox');
			$pass3Element->setDescription(__('Repeat new password'));
			$pass3Element->addValidators(array(
				tao_helpers_form_FormFactory::getValidator('Password', array('password2_ref' => $pass2Element)),
			));
			if(!$validatePasswords){
				$pass3Element->setForcedValid();
			}
			$this->form->addElement($pass3Element);
			
			$this->form->createGroup("pass_group", __("Change your password"), array('password0', 'password1', 'password2', 'password3'));
		}
		$this->form->removeElement(tao_helpers_Uri::encode(PROPERTY_USER_PASSWORD));
		
		
        // section 127-0-1-1-1f533553:1260917dc26:-8000:0000000000001DFC end
    }

} /* end of class tao_actions_form_Users */

?>