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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\oatbox\log;

use oat\oatbox\service\ConfigurableService;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class LoggerService extends ConfigurableService
{
    const SERVICE_ID = 'generis/logger';

    const LOGGER_OPTION = 'logger';

    /**
     * @var LoggerInterface Logger where log are sent
     */
    protected $logger;

    /**
     * Get the current logger.
     * If options does not contain any Psr3 Logger, NullLogger is set by default
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (! $this->logger) {
            if ($this->hasOption(self::LOGGER_OPTION)) {
                $this->logger = $this->getSubService(self::LOGGER_OPTION, LoggerInterface::class);
            } else {
                $this->logger = new NullLogger();
            }
        }

        return $this->logger;
    }

    /**
     * Add a Psr3 logger to LoggerService instance
     * If a logger is already set, previous and new logger are encapsulated into a LoggerAggregator
     * If $replace is set to true, old logger is replaced
     *
     * @param LoggerInterface $logger
     * @param boolean $replace
     * @return LoggerInterface
     */
    public function addLogger(LoggerInterface $logger, $replace = false)
    {
        if (!$replace && (($currentLogger = $this->getLogger()) !== false)) {
            $logger = new LoggerAggregator([$logger, $currentLogger]);
        }

        return $this->logger = $logger;
    }
}