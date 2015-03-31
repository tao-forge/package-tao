<?php
/**
 * 
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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */
namespace oat\tao\model\messaging;
use oat\tao\model\messaging\transportStrategy\MailAdapter;
/**
 * Service to send messages to Tao Users
 * 
 * @author bout
 */
class MessagingService extends \tao_models_classes_Service
{
    const CONFIG_KEY = 'messaging';
    
    /**
     * @var Transport
     */
    private $transport = null;
    private $errors = '';
    
    
    /**
     * Get the current transport implementation
     * 
     * @return Transport
     */
    public function getTransport()
    {
        if (is_null($this->transport)) {
            $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
            $transport = $tao->getConfig(self::CONFIG_KEY);
            if (!is_object($transport) || !$transport instanceof Transport) {
                throw new \common_exception_InconsistentData('Transport strategy not correctly set for '.__CLASS__);
            }
            $this->transport = $transport;
        }
        return $this->transport;
    }
    
    /**
     * Set the transport implementation to use
     * 
     * @param Transport $transporter
     */
    public function setTransport(Transport $transporter)
    {
        $this->transport = $transporter;
        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        $tao->setConfig(self::CONFIG_KEY, $this->transport);
    }
    
    /**
     * Send a message (destination is part of the message)
     * 
     * @param Message $message
     * @return boolean
     */
    public function send(Message $message)
    {
        $result = $this->getTransport()->send($message);
        if (!$result) {
            $this->errors = $this->getTransport()->getErrors();
        }
        return $result;
    }
    
    /**
     * Test if messaging is available
     * 
     * @return boolean
     */
    public function isAvailable()
    {
        $result = false;
        $tao = \common_ext_ExtensionsManager::singleton()->getExtensionById('tao');
        if ($tao->hasConfig(self::CONFIG_KEY)) {
            $transport = $tao->getConfig(self::CONFIG_KEY);
            $result = (is_object($transport) && $transport instanceof Transport);
        }
        return $result;
    }
    
    /**
     * @return string The error message. Empty string if none.
     */
    public function getErrors()
    {
        return $this->errors;
    }
}