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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA ;
 */
/**
 * @author Jean-Sébastien Conan <jean-sebastien.conan@vesperiagroup.com>
 */

namespace oat\taoQtiTest\models\runner\communicator;

use oat\taoQtiTest\models\runner\QtiRunnerServiceContext;
use qtism\runtime\tests\AssessmentTestSessionState;

/**
 * Class CommunicationChannel
 *
 * Describes the API of channel to process
 *
 * @package oat\taoQtiTest\models\runner\communicator
 */
class TestStateChannel implements CommunicationChannel
{
    /**
     * Get name of channel
     * @return string
     */
    public function getName()
    {
        return 'teststate';
    }

    /**
     * Processes the input or output message
     * @param QtiRunnerServiceContext $context - Needs the current runner context
     * @param array $data
     * @return array
     */
    public function process(QtiRunnerServiceContext $context, array $data = [])
    {
        $state = $context->getTestSession()->getState();

        if ($state == AssessmentTestSessionState::CLOSED) {
            $type = 'close';
            $label = __('This test has been terminated');
        }

        if ($state == AssessmentTestSessionState::SUSPENDED) {
            $type = 'pause';
            $label = __('This test has been suspended');
        }

        return [
            'type' => $type,
            'code' => $state,
            'message' => $label,
        ];
    }
}