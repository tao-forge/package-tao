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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 *
 */
namespace oat\taoTests\models\event;

use JsonSerializable;
use oat\oatbox\event\Event;

/**
 * A generic Test Execution event describing that a Test Execution with a given identifier has been paused.
 *
 */
class TestExecutionPausedEvent implements Event, JsonSerializable
{

    /** 
     * @var  string
     */
    protected $testExecutionId;

    /**
     * @param string $testExecutionId
     */
    public function __construct($testExecutionId)
    {
        $this->testExecutionId = $testExecutionId;
    }
    
    /**
     * Get the unique identifier of the Test Execution being paused.
     * 
     * @return string
     */
    public function getTestExecutionId()
    {
        return $this->testExecutionId;
    }


    /**
     * Return a unique name for this event.
     * 
     * @see \oat\oatbox\event\Event::getName()
     */
    public function getName()
    {
        return get_class($this);
    }

    /**
     * Specify data which should be serialized to JSON.
     * 
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'testExecutionId' => $this->getTestExecutionId()
        ];
    }
}
