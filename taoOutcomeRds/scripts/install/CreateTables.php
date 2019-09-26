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
 * Copyright (c) 2014-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

namespace oat\taoOutcomeRds\scripts\install;

use common_Logger;
use common_persistence_SqlPersistence as Persistence;
use Doctrine\DBAL\Schema\SchemaException;
use oat\oatbox\extension\AbstractAction;
use oat\taoOutcomeRds\model\RdsResultStorage;

class CreateTables extends AbstractAction
{
    public function __invoke($params)
    {
        /** @var RdsResultStorage $resultStorage */
        $resultStorage = $this->getServiceLocator()->get(RdsResultStorage::SERVICE_ID);
        $persistence = $resultStorage->getPersistence();

        $this->generateTables($persistence, $resultStorage);
    }

    /**
     * @param Persistence $persistence
     * @param RdsResultStorage $resultStorage
     */
    public function generateTables(Persistence $persistence, RdsResultStorage $resultStorage)
    {
        /** @var \common_persistence_sql_dbal_SchemaManager $schemaManager */
        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;

        try {
            $resultsTable = $resultStorage->createResultsTable($schema);
            $variablesTable = $resultStorage->createVariablesTable($schema);
            $resultStorage->createTableConstraints($variablesTable, $resultsTable);
        } catch (SchemaException $e) {
            common_Logger::i('Database Schema already up to date.');
        }

        $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }
    }
}
