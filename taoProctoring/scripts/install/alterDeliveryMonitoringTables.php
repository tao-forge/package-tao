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
 *
 */

use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Type;
use oat\taoProctoring\model\monitorCache\implementation\DeliveryMonitoringService;

$persistence = common_persistence_Manager::getPersistence('default');


// Drop foreign key
/** @var common_persistence_sql_pdo_SchemaManager $schemaManager */
$schemaManager = $persistence->getDriver()->getSchemaManager();
$schema = $schemaManager->createSchema();
$fromSchema = clone $schema;
try {
    $tableData = $schema->getTable(DeliveryMonitoringService::KV_TABLE_NAME);
    $tableData->removeForeignKey(DeliveryMonitoringService::KV_FK_PARENT);
} catch(SchemaException $e) {
    common_Logger::i('Database Schema already up to date.');
}
$queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
foreach ($queries as $query) {
    $persistence->exec($query);
}

//change parent_id column type
$schemaManager = $persistence->getDriver()->getSchemaManager();
$schema = $schemaManager->createSchema();
$fromSchema = clone $schema;
try {
    $tableData = $schema->getTable(DeliveryMonitoringService::KV_TABLE_NAME);
    $tableData->changeColumn(DeliveryMonitoringService::KV_COLUMN_PARENT_ID, array('type' => Type::getType('string'), 'notnull' => true, 'length' => 255));
} catch(SchemaException $e) {
    common_Logger::i('Database Schema already up to date.');
}
$queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
foreach ($queries as $query) {
    $persistence->exec($query);
}

//update parent_id column values
$persistence->exec("UPDATE kv_delivery_monitoring SET parent_id=(
SELECT delivery_monitoring.delivery_execution_id FROM delivery_monitoring
  WHERE delivery_monitoring.id = CAST(kv_delivery_monitoring.parent_id AS INTEGER)
)");


//add foreign key.
$schemaManager = $persistence->getDriver()->getSchemaManager();
$schema = $schemaManager->createSchema();
$fromSchema = clone $schema;
try {
    $tableLog = $schema->getTable(DeliveryMonitoringService::TABLE_NAME);
    $tableData = $schema->getTable(DeliveryMonitoringService::KV_TABLE_NAME);

    $tableData->addForeignKeyConstraint(
        $tableLog,
        array(DeliveryMonitoringService::KV_COLUMN_PARENT_ID),
        array(DeliveryMonitoringService::COLUMN_DELIVERY_EXECUTION_ID),
        array(
            'onDelete' => 'CASCADE',
            'onUpdate' => 'CASCADE',
        ),
        DeliveryMonitoringService::KV_FK_PARENT
    );

} catch(SchemaException $e) {
    common_Logger::i('Database Schema already up to date.');
}
$queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
foreach ($queries as $query) {
    $persistence->exec($query);
}