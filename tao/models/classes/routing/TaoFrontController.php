<?php
/**
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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\tao\model\routing;

use FrontController;
use HttpRequest;
use Context;
use InterruptedActionException;
use common_ext_ExtensionsManager;

/**
 * A simple controller to replace the ClearFw controller
 * 
 * @author Joel Bout, <joel@taotesting.com>
 */
class TaoFrontController implements FrontController
{
    /**
     * 
     * @var HttpRequest
     */
    private $httpRequest;
    
    /**
     * 
     * @param HttpRequest $pRequest
     */
    public function __construct( HttpRequest $pRequest ) {
        $this->httpRequest = $pRequest;
    }
    
    /**
     * Returns the request to be executed
     * 
     * @return HttpRequest
     */
    protected function getRequest() {
        return $this->httpRequest;
    }
    
    /**
     * (non-PHPdoc)
     * @see FrontController::loadModule()
     */
    public function loadModule() {
        $resolver = new Resolver($this->getRequest());
        
        // load the responsible extension
        common_ext_ExtensionsManager::singleton()->getExtensionById($resolver->getExtensionId());
        \Context::getInstance()->setExtensionName($resolver->getExtensionId());
        
        try
        {
            $enforcer = new ActionEnforcer($resolver->getExtensionId(), $resolver->getControllerClass(), $resolver->getMethodName(), $this->getRequest()->getArgs());
            $enforcer->execute();
        }
        catch (InterruptedActionException $iE)
        {
            // Nothing to do here.
        }
    }
}
