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
 *
 */

namespace oat\taoProctoring\scripts\update;

use common_ext_ExtensionUpdater;
use oat\tao\scripts\update\OntologyUpdater;
use oat\tao\model\entryPoint\EntryPointService;
use oat\oatbox\event\EventManager;
use oat\taoTests\models\event\TestChangedEvent;
use oat\taoTestCenter\model\eligibility\EligiblityChanged;
use oat\taoDeliveryRdf\model\GroupAssignment;
use oat\taoDelivery\model\AssignmentService;
use oat\taoProctoring\model\ReasonCategoryService;
use oat\oatbox\service\ServiceNotFoundException;
use oat\taoProctoring\model\monitorCache\DeliveryMonitoringService;
use oat\taoProctoring\model\monitorCache\implementation\MonitoringStorage;
use oat\taoProctoring\model\ProctorService;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionState;
use oat\taoDelivery\models\classes\execution\event\DeliveryExecutionCreated;
use oat\tao\model\event\MetadataModified;
use oat\taoQtiTest\models\event\QtiTestStateChangeEvent;
use oat\tao\model\accessControl\func\AclProxy;
use oat\tao\model\accessControl\func\AccessRule;
use oat\taoProctoring\controller\DeliverySelection;
use oat\taoProctoring\controller\Monitor;
use oat\tao\model\user\TaoRoles;
use oat\taoProctoring\model\authorization\AuthorizationGranted;

/**
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends common_ext_ExtensionUpdater
{

    /**
     * @param string $initialVersion
     * @return string string
     */
    public function update($initialVersion)
    {
        if ($this->isBetween('0.0.0', '3.11.0')) {
            throw new \common_ext_UpdateException('Please first update to 3.15.0 using taoProctoring 3.15.0');
        }

        $this->skip('3.12.0', '3.12.1');

        if ($this->isVersion('3.12.1')) {
            OntologyUpdater::syncModels();
            $this->setVersion('3.13.0');
        }
        $this->skip('3.13.0', '3.13.7');

        if ($this->isVersion('3.13.7')) {
            try {
                $this->getServiceManager()->get(ReasonCategoryService::SERVICE_ID);
            } catch (ServiceNotFoundException $e) {
                $service = new ReasonCategoryService();
                $service->setServiceManager($this->getServiceManager());
                $this->getServiceManager()->register(ReasonCategoryService::SERVICE_ID, $service);
            }
            $this->setVersion('3.14.0');
        }

        if ($this->isBetween('3.14.0', '3.16.0')) {
            // ignore eligibility service
            
            try {
                // drop unused columns
                $monitorService = $this->getServiceManager()->get(DeliveryMonitoringService::SERVICE_ID);
                $persistenceManager = $this->getServiceManager()->get(\common_persistence_Manager::SERVICE_ID);
                $persistence = $persistenceManager->getPersistenceById($monitorService->getOption(MonitoringStorage::OPTION_PERSISTENCE));
                $schemaManager = $persistence->getDriver()->getSchemaManager();
                $schema = $schemaManager->createSchema();
                $fromSchema = clone $schema;
                $tableData = $schema->getTable(MonitoringStorage::TABLE_NAME);
                $tableData->dropColumn('remaining_time');
                $tableData->dropColumn('extra_time');
                $tableData->dropColumn('consumed_extra_time');
                $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
                foreach ($queries as $query) {
                    $persistence->exec($query);
                }
            } catch (SchemaException $e) {
                        \common_Logger::i('Database Schema already up to date.');
            }
            
            // update model
            OntologyUpdater::syncModels();

            // correct event listeners
            $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
            $eventManager->detach(TestChangedEvent::EVENT_NAME,
                array('oat\\taoProctoring\\model\\monitorCache\\update\\TestUpdate', 'testStateChange')
            );
            $eventManager->detach('oat\\taoDelivery\\models\\classes\\execution\\event\\DeliveryExecutionState',
                ['oat\\taoProctoring\\model\\monitorCache\\update\\DeliveryExecutionStateUpdate', 'stateChange']
            );
            $eventManager->detach('oat\\taoProctoring\\model\\event\\EligiblityChanged',
                ['oat\\taoProctoring\\model\\monitorCache\\update\\EligiblityUpdate', 'eligiblityChange']
            );
            $eventManager->detach(\oat\tao\model\event\MetadataModified::class,
                ['oat\\taoProctoring\\model\\monitorCache\\update\\DeliveryUpdate', 'labelChange']
            );
            $eventManager->attach(DeliveryExecutionState::class, [DeliveryMonitoringService::SERVICE_ID, 'executionStateChanged']);
            $eventManager->attach(DeliveryExecutionCreated::class, [DeliveryMonitoringService::SERVICE_ID, 'executionCreated']);
            $eventManager->attach(MetadataModified::class, [DeliveryMonitoringService::SERVICE_ID, 'deliveryLabelChanged']);
            $eventManager->attach(TestChangedEvent::EVENT_NAME, [DeliveryMonitoringService::SERVICE_ID, 'testStateChanged']);
            $eventManager->attach(QtiTestStateChangeEvent::EVENT_NAME, [DeliveryMonitoringService::SERVICE_ID, 'qtiTestStatusChanged']);
            $eventManager->attach(AuthorizationGranted::EVENT_NAME, [DeliveryMonitoringService::SERVICE_ID, 'deliveryAuthorized']);
            $this->getServiceManager()->register(EventManager::SERVICE_ID, $eventManager);

            // unregister testcenter services
            $this->getServiceManager()->register(AssignmentService::SERVICE_ID, new GroupAssignment());
            $this->getServiceManager()->register(ProctorService::SERVICE_ID, new ProctorService());

            // access rights
            AclProxy::applyRule(new AccessRule('grant', ProctorService::ROLE_PROCTOR, DeliverySelection::class));
            AclProxy::applyRule(new AccessRule('grant', ProctorService::ROLE_PROCTOR, Monitor::class));

            $old = array(
                ['http://www.tao.lu/Ontologies/TAOProctor.rdf#TestCenterManager',array('oat\\taoProctoring\\controller\\TestCenterManager')],
                ['http://www.tao.lu/Ontologies/TAOProctor.rdf#TestCenterAdministratorRole',array('oat\\taoProctoring\\controller\\ProctorManager')],
                [ProctorService::ROLE_PROCTOR,'oat\\taoProctoring\\controller\\Delivery'],
                [ProctorService::ROLE_PROCTOR,'oat\\taoProctoring\\controller\\Diagnostic'],
                [ProctorService::ROLE_PROCTOR,'oat\\taoProctoring\\controller\\TestCenter'],
                ['http://www.tao.lu/Ontologies/generis.rdf#taoClientDiagnosticManager','oat\\taoProctoring\\controller\\DiagnosticChecker'],
                [TaoRoles::ANONYMOUS, 'oat\\taoProctoring\\controller\\DiagnosticChecker']
            );
            foreach ($old as $row) {
                list($role, $acl) = $row;
                AclProxy::revokeRule(new AccessRule('grant', $role, $acl));
            }
            $this->setVersion('4.0.0');
        }
        
        $this->skip('4.0.0', '4.1.1');

        if ($this->isVersion('4.1.1')) {
            AclProxy::applyRule(new AccessRule(
                'grant',
                INSTANCE_ROLE_SYSADMIN,
                ['ext'=>'taoProctoring', 'mod' => 'Tools', 'act' => 'pauseActiveExecutions']
            ));
            $this->setVersion('4.2.0');
        }

        $this->skip('4.2.0', '4.3.0');
    }
}
