<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * This class provide service on user management
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001CFA-includes begin
// section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001CFA-includes end

/* user defined constants */
// section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001CFA-constants begin
// section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001CFA-constants end

/**
 * This class provide service on user management
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package tao
 * @subpackage models_classes
 */
class tao_models_classes_UserService
    extends tao_models_classes_GenerisService
    implements core_kernel_users_UsersManagement
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * the core user service
     *
     * @access protected
     * @var Service
     */
    protected $generisUserService = null;

    // --- OPERATIONS ---

    /**
     * constructor
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    protected function __construct()
    {
        // section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001D1E begin

		$this->generisUserService = core_kernel_users_Service::singleton();

        // section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001D1E end
    }

    /**
     * authenticate a user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login
     * @param  string password
     * @return boolean
     */
    public function loginUser($login, $password)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001D05 begin

        try{
        	if($this->generisUserService->login($login, $password, $this->getAllowedRoles())){
        		
        		// init languages
        		$currentUser = $this->getCurrentUser();
        		$valueProperty = new core_kernel_classes_Property(RDF_VALUE);
        		
        		try {
        			$uiLg = $currentUser->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_UILG));
        			if(!is_null($uiLg) && $uiLg instanceof core_kernel_classes_Resource) {
        				$code = $uiLg->getUniquePropertyValue($valueProperty);
						core_kernel_classes_Session::singleton()->setInterfaceLanguage($code);
        			}
        		} catch (common_exception_EmptyProperty $e) {
        			// leave it to default
        		}

        		try {
					$dataLg = $currentUser->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_DEFLG));
					if(!is_null($dataLg) && $dataLg instanceof core_kernel_classes_Resource){
        				$code = $dataLg->getUniquePropertyValue($valueProperty);
						core_kernel_classes_Session::singleton()->setDataLanguage($code);
					}
        		} catch (common_exception_EmptyProperty $e) {
					// leave it to default        				
        		}
				
        		$returnValue = true;				//roles order is important, we loggin with the first found
        	}
        }
        catch(core_kernel_users_Exception $ue){
        //	print $ue->getMessage();
        }
        
        // section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001D05 end

        return (bool) $returnValue;
    }

    /**
     * retrieve the logged in user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getCurrentUser()
    {
        $returnValue = null;

        // section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001D03 begin
    	if($this->generisUserService->isASessionOpened()){
        	$userUri = core_kernel_classes_Session::singleton()->getUserUri();
			if(!empty($userUri)){
        		$returnValue = new core_kernel_classes_Resource($userUri);
			} else {
				common_Logger::d('no userUri');
			}
    	}
        // section 127-0-1-1-37d8f507:12577bc7e88:-8000:0000000000001D03 end

        return $returnValue;
    }

    /**
     * Check if the login is already used
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string login
     * @param 
     * @return boolean
     */
    public function loginExists($login, core_kernel_classes_Class $class = null)
    {
        $returnValue = (bool) false;

        $returnValue = $this->generisUserService->loginExists($login, $class);

        return (bool) $returnValue;
    }

    /**
     * Check if the login is available (because it's unique)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login
     * @return boolean
     */
    public function loginAvailable($login)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D76 begin

		if(!empty($login)){
			$returnValue = !$this->loginExists($login);
		}

        // section 127-0-1-1-4660071d:12596d6b0e5:-8000:0000000000001D76 end

        return (bool) $returnValue;
    }

    /**
     * Get a user that has a given login.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @param string login the user login is the unique identifier to retrieve him.
     * @param core_kernel_classes_Class A specific class to search the user.
     * @return core_kernel_classes_Resource
     */
    public function getOneUser($login, core_kernel_classes_Class $class = null)
    {
        $returnValue = null;

        // section 127-0-1-1--54120360:125930cf6af:-8000:0000000000001D4E begin

		if (!empty($login)){
			
			$user = $this->generisUserService->getOneUser($login, $class);
			
			if (!empty($user)){
				
				$userRolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
				$userRoles = $user->getPropertyValuesCollection($userRolesProperty);
				$allowedRoles = $this->getAllowedRoles();
				
				if($this->generisUserService->userHasRoles($user, $allowedRoles)){
					$returnValue = $user;
				}
			}
		}

        // section 127-0-1-1--54120360:125930cf6af:-8000:0000000000001D4E end

        return $returnValue;
    }

    /**
     * Get the list of users by role(s)
     * options are: order, orderDir, start, end, search
     * with search consisting of: field, op, string
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array roles
     * @param  array options the user list options to order the list and paginate the results
     * @return array
     */
    public function getUsersByRoles($roles, $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--54120360:125930cf6af:-8000:0000000000001D44 begin

        //the users we want are instances of the role
		$fields = array('login' => PROPERTY_USER_LOGIN,
						'password' => PROPERTY_USER_PASSWORD,
						'uilg' => PROPERTY_USER_UILG,
						'deflg' => PROPERTY_USER_DEFLG,
						'mail' => PROPERTY_USER_MAIL,
		    			'email' => PROPERTY_USER_MAIL,
						'role' => RDF_TYPE,
						'roles' => PROPERTY_USER_ROLES,
						'firstname' => PROPERTY_USER_FIRSTNAME,
						'lastname' => PROPERTY_USER_LASTNAME,
						'name' => PROPERTY_USER_FIRSTNAME);
		
		$ops = array('eq' => "%s",
					 'bw' => "%s*",
					 'ew' => "*%s",
					 'cn' => "*%s*");
		
		$opts = array('recursive' => true, 'like' => false);
		if (isset($options['start'])) {
			$opts['offset'] = $options['start'];
		}
		if (isset($options['limit'])) {
			$opts['limit'] = $options['limit'];
		}
		
		$crits = array(PROPERTY_USER_LOGIN => '*');
		if (isset($options['search']) && !is_null($options['search']) && isset($options['search']['string']) && isset($ops[$options['search']['op']])) {
			$crits[$fields[$options['search']['field']]] = sprintf($ops[$options['search']['op']], $options['search']['string']);
		}
		// restrict roles
		$crits[PROPERTY_USER_ROLES] = $roles;
		
		if (isset($options['order'])) {
			$opts['order'] = $fields[$options['order']]; 
			if (isset($options['orderDir'])) {
				$opts['orderdir'] = $options['orderDir']; 
			}
		}
		
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		
		$returnValue = $userClass->searchInstances($crits, $opts);
        // section 127-0-1-1--54120360:125930cf6af:-8000:0000000000001D44 end

        return (array) $returnValue;
    }

    /**
     * Remove a user
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user
     * @return boolean
     */
    public function removeUser( core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--54120360:125930cf6af:-8000:0000000000001D56 begin
        if(!is_null($user)){
			$returnValue = $this->generisUserService->removeUser($user);
		}
        // section 127-0-1-1--54120360:125930cf6af:-8000:0000000000001D56 end

        return (bool) $returnValue;
    }

    /**
     * returns a list of all concrete roles(instances of CLASS_ROLE)
     * which are allowed to login
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAllowedRoles()
    {
        $returnValue = array();

        $returnValue = array(INSTANCE_ROLE_BACKOFFICE => new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE));

        return (array) $returnValue;
    }
    
    public function getDefaultRole()
    {
    	return new core_kernel_classes_Resource(INSTANCE_ROLE_BACKOFFICE);
    }

    /**
     * Short description of method logout
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function logout()
    {
        $returnValue = (bool) false;

        $returnValue = $this->generisUserService->logout();

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAllUsers
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array options
     * @return array
     */
    public function getAllUsers($options = array())
    {
        $returnValue = array();

        $userClass = new core_kernel_classes_Class(CLASS_TAO_USER);
		$options = array_merge(array('recursive' => true, 'like' => true), $options);
		$filters = array(PROPERTY_USER_LOGIN => '*');
		$returnValue = $userClass->searchInstances($filters, $options);

        return (array) $returnValue;
    }

    /**
     * returns the nr of users fullfilling the criterias,
     * uses the same syntax as getUsersByRole
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array roles
     * @param  array options
     * @return int
     */
    public function getUserCount($roles, $options = array())
    {
        $returnValue = (int) 0;

        $opts = array(
        	'recursive' => true,
        	'like' => false
        );

		$crits = array(PROPERTY_USER_LOGIN => '*');
		if (isset($options['search']['string']) && isset($options['search']['op'])
			&& !empty($options['search']['string']) && !empty($options['search']['op'])) {
			$fields = array('login' => PROPERTY_USER_LOGIN,
						'password' => PROPERTY_USER_PASSWORD,
						'uilg' => PROPERTY_USER_UILG,
						'deflg' => PROPERTY_USER_DEFLG,
						'mail' => PROPERTY_USER_MAIL,
		    			'email' => PROPERTY_USER_MAIL,
						'role' => RDF_TYPE,
						'roles' => PROPERTY_USER_ROLES,
						'firstname' => PROPERTY_USER_FIRSTNAME,
						'lastname' => PROPERTY_USER_LASTNAME,
						'name' => PROPERTY_USER_FIRSTNAME);
			$ops = array('eq' => "%s",
					 'bw' => "%s*",
					 'ew' => "*%s",
					 'cn' => "*%s*");
			$crits[$fields[$options['search']['field']]] = sprintf($ops[$options['search']['op']], $options['search']['string']);
		}
		
		$crits[PROPERTY_USER_ROLES] = $roles;
		
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$returnValue = $userClass->countInstances($crits, $opts);

        return (int) $returnValue;
    }

    /**
     * Short description of method toTree
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class clazz
     * @param  array options
     * @return array
     */
    public function toTree( core_kernel_classes_Class $clazz, $options)
    {
        $returnValue = array();

    	$users = $this->getAllUsers(array('order' => 'login'));
		foreach($users as $user){
			$login = (string) $user->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
			$returnValue[] = array(
					'data' 	=> tao_helpers_Display::textCutter($login, 16),
					'attributes' => array(
						'id' => tao_helpers_Uri::encode($user->getUri()),
						'class' => 'node-instance'
					)
				);

		}

        return (array) $returnValue;
    }
    
    /**
     * Add a new user.
     * 
     * @param string login The login to give the user.
     * @param string password the md5 hash of the password.
     * @param core_kernel_classes_Resource role A role to grant to the user.
     * @param core_kernel_classes_Class A specific class to use to instantiate the new user. If not specified, the class returned by the getUserClass method is used.
     * @throws core_kernel_users_Exception If an error occurs.
     */
    public function addUser($login, $password, core_kernel_classes_Resource $role = null, core_kernel_classes_Class $class = null){
		
    	if (empty($class)){
    		$class = $this->getUserClass();
    	}
    	
    	return $this->generisUserService->addUser($login, $password, $role, $class);
	}
	
	/**
	 * Indicates if a user session is currently opened or not.
	 * 
	 * @return boolean True if a session is opened, false otherwise.
	 */
	public function isASessionOpened(){
		return $this->generisUserService->isASessionOpened();
	}
	
	/**
	 * Indicates if a given user has a given password.
	 * 
	 * @param string password The password to check.
	 * @param core_kernel_classes_Resource user The user you want to check the password.
	 * @return boolean
	 */
	public function isPasswordValid($password,  core_kernel_classes_Resource $user){
		return $this->generisUserService->isPasswordValid();
	}
	
	/**
	 * Change the password of a given user.
	 * 
	 * @param core_kernel_classes_Resource user The user you want to change the password.
	 * @param string password The md5 hash of the new password.
	 */
	public function setPassword(core_kernel_classes_Resource $user, $password){
		return $this->generisUserService->setPassword($user, $password);
	}
	
	/**
	 * Get the roles of a given user.
	 * 
	 * @param core_kernel_classes_Resource $user The user you want to retrieve the roles.
	 * @return array An array of core_kernel_classes_Resource.
	 */
	public function getUserRoles(core_kernel_classes_Resource $user){
		return $this->generisUserService->getUserRoles($user);
	}
	
	/**
	 * Indicates if a user is granted with a set of Roles.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user The User instance you want to check Roles.
	 * @param  roles Can be either a single Resource or an array of Resource that are instances of Role.
	 * @return boolean
	 */
	public function userHasRoles(core_kernel_classes_Resource $user, $roles){
		return $this->generisUserService->userHasRoles($user, $roles);
	}
	
	/**
	 * Attach a Generis Role to a given TAO User. A UserException will be
	 * if an error occurs. If the User already has the role, nothing happens.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user The User you want to attach a Role.
	 * @param  Resource role A Role to attach to a User.
	 * @throws core_kernel_users_Exception If an error occurs.
	 */
	public function attachRole(core_kernel_classes_Resource $user, core_kernel_classes_Resource $role)
	{
		$this->generisUserService->attachRole($user, $role);
	}
	
	/**
	 * Unnatach a Role from a given TAO User.
	 *
	 * @access public
	 * @author Jerome Bogaerts, <jerome@taotesting.com>
	 * @param  Resource user A TAO user from which you want to unnattach the Role.
	 * @param  Resource role The Role you want to Unnatach from the TAO User.
	 * @throws core_kernel_users_Exception If an error occurs.
	 */
	public function unnatachRole(core_kernel_classes_Resource $user, core_kernel_classes_Resource $role)
	{
		$this->generisUserService->unnatachRole();
	}
	
	/**
	 * Get the class to use to instantiate users.
	 * 
	 * @return core_kernel_classes_Class The user class.
	 */
	public function getUserClass(){
		return new core_kernel_classes_Class(CLASS_GENERIS_USER);
	}
}

?>