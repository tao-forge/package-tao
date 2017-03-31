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
 * Copyright (c) 2017 Open Assessment Technologies SA
 *
 */

namespace oat\tao\model\maintenance;

use oat\oatbox\service\ConfigurableService;
use oat\tao\model\metadata\exception\InconsistencyConfigException;

class Maintenance extends ConfigurableService
{
    const SERVICE_ID = 'tao/maintenance';

    const OPTION_PERSISTENCE = 'persistence';

    protected $storage;

    public function isPlatformReady()
    {
        return ($this->getPlatformState()->getBooleanStatus() === true);
    }

    public function isPlatformOnMaintenance()
    {
        return ($this->getPlatformState()->getBooleanStatus() === false);
    }

    public function enablePlatform()
    {
        $this->setPlatformState(MaintenanceState::LIVE_MODE);
    }

    public function disablePlatform()
    {
        $this->setPlatformState(MaintenanceState::OFFLINE_MODE);
    }

    protected function getPlatformState()
    {
        try {
            return $this->getStorage()->getCurrentPlatformState();
        } catch (\common_exception_NotFound $e) {
            return new MaintenanceState(
                array(MaintenanceState::STATUS => MaintenanceState::LIVE_MODE)
            );
        }
    }

    protected function setPlatformState($status)
    {
        $state = new MaintenanceState(array(
            MaintenanceState::STATUS => $status
        ));

        $this->getStorage()->setPlatformState($state);
    }

    /**
     * @return MaintenanceStorage
     * @throws InconsistencyConfigException
     */
    public function getStorage()
    {
        if (! $this->storage) {
            if (! $this->hasOption(self::OPTION_PERSISTENCE)) {
                throw new InconsistencyConfigException(__('Maintenance service must have a persistence option.'));
            }
            $this->storage = new MaintenanceStorage(
                \common_persistence_Manager::getPersistence($this->getOption(self::OPTION_PERSISTENCE))
            );
        }
        return $this->storage;
    }
}