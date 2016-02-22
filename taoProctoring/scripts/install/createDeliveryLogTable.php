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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *
 */

use Doctrine\DBAL\Schema\SchemaException;
use oat\taoProctoring\model\deliveryLog\implementation\RdsDeliveryLogService;

$persistence = common_persistence_Manager::getPersistence('default');

$schemaManager = $persistence->getDriver()->getSchemaManager();
$schema = $schemaManager->createSchema();
$fromSchema = clone $schema;

try {
    $tableLog = $schema->createTable(RdsDeliveryLogService::TABLE_NAME);
    $tableLog->addOption('engine', 'InnoDB');
    $tableLog->addColumn(RdsDeliveryLogService::ID, "integer", array("autoincrement" => true));
    $tableLog->addColumn(RdsDeliveryLogService::DELIVERY_EXECUTION_ID, "string", array("notnull" => true, "length" => 255));
    $tableLog->addColumn(RdsDeliveryLogService::EVENT_ID, "string", array("notnull" => true, "length" => 255));
    $tableLog->addColumn(RdsDeliveryLogService::DATA, "text", array("notnull" => true));
    $tableLog->addColumn(RdsDeliveryLogService::CREATED_AT, "string", array("notnull" => true, "length" => 255));
    $tableLog->addColumn(RdsDeliveryLogService::CREATED_BY, "string", array("notnull" => true, "length" => 255));

    $tableLog->setPrimaryKey(array(RdsDeliveryLogService::ID));

    $tableLog->addIndex(
        array(RdsDeliveryLogService::DELIVERY_EXECUTION_ID),
        'IDX_' . RdsDeliveryLogService::TABLE_NAME . '_' . RdsDeliveryLogService::DELIVERY_EXECUTION_ID
    );
} catch(SchemaException $e) {
    common_Logger::i('Database Schema already up to date.');
}

$queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
foreach ($queries as $query) {
    $persistence->exec($query);
}