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
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\taoProctoring\model;

use oat\oatbox\service\ConfigurableService;

/**
 * Settings for the user interface of the proctoring
 *
 * Class GuiSettings
 * @package oat\taoProctoring\model
 */
class GuiSettingsService extends ConfigurableService
{
    const SERVICE_ID = 'taoProctoring/GuiSettings';

    /**
     * Refresh button can be configured as available or unavailable
     */
    const PROCTORING_REFRESH_BUTTON = 'refreshBtn';

    /**
     * Time between auto refresh
     * 0 - don't refresh
     */
    const PROCTORING_AUTO_REFRESH = 'autoRefresh';

    /**
     * Allow or not proctor to  pause a delivery
     */
    const PROCTORING_ALLOW_PAUSE = 'canPause';

    const OPTION_DIALOG_SETTINGS = 'dialogSettings';

    /**
     * @return array
     */
    public function asArray()
    {
        return [
            self::PROCTORING_REFRESH_BUTTON         => $this->hasOption(self::PROCTORING_REFRESH_BUTTON) ? $this->getOption(self::PROCTORING_REFRESH_BUTTON) : true,
            self::PROCTORING_AUTO_REFRESH           => $this->hasOption(self::PROCTORING_AUTO_REFRESH) ? $this->getOption(self::PROCTORING_AUTO_REFRESH) : 0,
            self::PROCTORING_ALLOW_PAUSE            => $this->hasOption(self::PROCTORING_ALLOW_PAUSE) ? $this->getOption(self::PROCTORING_ALLOW_PAUSE) : true,
            self::OPTION_DIALOG_SETTINGS            => $this->hasOption(self::OPTION_DIALOG_SETTINGS) ? $this->getOption(self::OPTION_DIALOG_SETTINGS) : [],
        ];
    }
}
