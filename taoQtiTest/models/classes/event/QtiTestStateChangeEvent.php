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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoQtiTest\models\event;

/**
 * Class QtiTestStateChangeEvent
 * @author Aleh Hutnikau <aleh@taotesting.com>
 */
class QtiTestStateChangeEvent extends QtiTestChangeEvent
{

    const EVENT_NAME = __CLASS__;

    /**
     * The state of the AssessmentTestSession before changing.
     *
     * @var integer
     */
    private $previousState;

    /**
     * QtiTestStateChangeEvent constructor.
     * @param \taoQtiTest_helpers_TestSession $testSession
     * @param $previousState
     */
    public function __construct(\taoQtiTest_helpers_TestSession $testSession, $previousState)
    {
        $this->session = $testSession;
        $this->previousState = $previousState;
    }

    public function getName()
    {
        return static::EVENT_NAME;
    }

    /**
     * @return int
     */
    public function getPreviousState()
    {
        return $this->previousState;
    }
}