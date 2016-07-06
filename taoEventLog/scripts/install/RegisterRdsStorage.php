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
 * Copyright (c) 2016  (original work) Open Assessment Technologies SA;
 *
 * @author Ivan Klimchuk <klimchuk@1pt.com>
 */

namespace oat\taoEventLog\scripts\install;

use common_exception_Error;
use common_ext_action_InstallAction;
use common_Logger;
use common_persistence_SqlPersistence;
use common_report_Report;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use oat\oatbox\action\Action;
use oat\taoEventLog\model\storage\RdsStorage;
use oat\taoEventLog\model\StorageInterface;

/**
 * Class RegisterRdsStorage
 * @package oat\taoEventLog\scripts\install
 */
class RegisterRdsStorage extends common_ext_action_InstallAction implements Action
{
    /**
     * @param $params
     * @return common_report_Report
     * @throws common_exception_Error
     */
    public function __invoke($params)
    {
        $persistenceId = count($params) > 0 ? reset($params) : 'default';

        $this->registerService(StorageInterface::SERVICE_ID, new RdsStorage([StorageInterface::OPTION_PERSISTENCE => $persistenceId]));

        $storageService = $this->getServiceManager()->get(StorageInterface::SERVICE_ID);

        /** @var common_persistence_SqlPersistence $persistence */
        $persistence = $storageService->getPersistence();
        
        /** @var AbstractSchemaManager $schemaManager */
        $schemaManager = $persistence->getDriver()->getSchemaManager();

        /** @var Schema $schema */
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;

        try {
            $table = $schema->createTable(StorageInterface::EVENT_LOG_TABLE_NAME);
            $table->addOption('engine', 'MyISAM');

            $table->addColumn(StorageInterface::EVENT_LOG_ID,          "integer",  ["notnull" => true, "autoincrement" => true, 'unsigned' => true]);
            $table->addColumn(StorageInterface::EVENT_LOG_EVENT_NAME,  "string",   ["notnull" => true, "length" => 255, 'comment' => 'Event name']);
            $table->addColumn(StorageInterface::EVENT_LOG_ACTION,      "string",   ["notnull" => true, "length" => 255, 'comment' => 'Current action']);
            $table->addColumn(StorageInterface::EVENT_LOG_USER_ID,     "string",   ["notnull" => false, "length" => 255, 'default' => '', 'comment' => 'User identifier']);
            $table->addColumn(StorageInterface::EVENT_LOG_USER_ROLES,  "string",   ["notnull" => true, "length" => 555, 'comment' => 'User roles']);
            $table->addColumn(StorageInterface::EVENT_LOG_OCCURRED,    "datetime", ["notnull" => true]);
            $table->addColumn(StorageInterface::EVENT_LOG_PROPERTIES,  "text",     ["notnull" => false, 'default' => '', 'comment' => 'Event properties in json']);

            $table->setPrimaryKey([StorageInterface::EVENT_LOG_ID]);
            $table->addIndex([StorageInterface::EVENT_LOG_EVENT_NAME], 'idx_event_name');
            $table->addIndex([StorageInterface::EVENT_LOG_ACTION], 'idx_action');
            $table->addIndex([StorageInterface::EVENT_LOG_USER_ID], 'idx_user_id');
            $table->addIndex([StorageInterface::EVENT_LOG_USER_ROLES], 'idx_user_roles');
            $table->addIndex([StorageInterface::EVENT_LOG_OCCURRED], 'idx_occurred');
        } catch (SchemaException $e) {
            common_Logger::i('Database Schema for EventLog already up to date.');
        }

        $queries = $persistence->getPlatForm()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }

        return new common_report_Report(common_report_Report::TYPE_SUCCESS, __('Registered and created EventLog Rds Storage'));
    }
}
