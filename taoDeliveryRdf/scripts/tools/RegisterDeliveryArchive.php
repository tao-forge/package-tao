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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\taoDeliveryRdf\scripts\tools;

use oat\oatbox\event\EventManager;
use oat\oatbox\extension\AbstractAction;
use oat\oatbox\filesystem\FileSystemService;
use oat\taoDelivery\model\DeliveryArchiveService;
use oat\taoDeliveryRdf\model\DeliveryArchiveService as DeliveryArchiveServiceRdf;
use oat\taoDeliveryRdf\model\event\DeliveryCreatedEvent;
use oat\taoDeliveryRdf\model\event\DeliveryRemovedEvent;

/**
 * Class RegisterDeliveryArchiveEvent
 *
 * sudo -u www-data php index.php 'oat\taoDeliveryRdf\scripts\tools\RegisterDeliveryArchive'
 *
 * @package oat\taoDeliveryRdf\scripts\tools
 */
class RegisterDeliveryArchive extends AbstractAction
{
    /**
     * @param $params
     * @return \common_report_Report
     * @throws \common_Exception
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    public function __invoke($params)
    {

        $deliveryArchiveService = new DeliveryArchiveServiceRdf();
        $this->getServiceManager()->propagate($deliveryArchiveService);
        $this->registerService(DeliveryArchiveService::SERVICE_ID, $deliveryArchiveService);

        /** @var FileSystemService $fileSystemService */
        $fileSystemService = $this->getServiceLocator()->get(FileSystemService::SERVICE_ID);

        if (!$fileSystemService->hasDirectory(DeliveryArchiveService::BUCKET_DIRECTORY)) {
            $fileSystemService->createFileSystem(DeliveryArchiveService::BUCKET_DIRECTORY);
            $this->registerService(FileSystemService::SERVICE_ID, $fileSystemService);
        }

        /** @var EventManager $eventManager */
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->attach(DeliveryCreatedEvent::class, [
            DeliveryArchiveService::SERVICE_ID,
            'catchDeliveryCreated'
        ]);

        $eventManager->attach(DeliveryRemovedEvent::class, [
            DeliveryArchiveService::SERVICE_ID,
            'catchDeliveryRemoved'
        ]);

        $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS,
            DeliveryArchiveService::BUCKET_DIRECTORY . ' directory created and event attached');
    }
}