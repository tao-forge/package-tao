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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\taoEventLog\model\requestLog\rds;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use oat\taoEventLog\model\requestLog\RequestLogStorage;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use oat\oatbox\user\User;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class RdsRequestLogStorage
 * @package oat\taoEventLog\model\requestLog\rds
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class RdsRequestLogStorage extends ConfigurableService implements RequestLogStorage
{
    const OPTION_PERSISTENCE = 'persistence_id';
    const TABLE_NAME = 'request_log';

    const COLUMN_USER_ID = self::USER_ID;
    const COLUMN_USER_ROLES = self::USER_ROLES;
    const COLUMN_ACTION = self::ACTION;
    const COLUMN_EVENT_TIME = self::EVENT_TIME;
    const COLUMN_DETAILS = self::DETAILS;

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    /**
     * @inheritdoc
     */
    public function log(Request $request = null, User $user = null)
    {
        if ($request === null) {
            $request = ServerRequest::fromGlobals();
        }

        if ($user === null) {
            $user = \common_session_SessionManager::getSession()->getUser();
        }

        $data = [
            self::USER_ID => $user->getIdentifier(),
            self::USER_ROLES => implode(',', $user->getRoles()),
            self::COLUMN_ACTION => $request->getUri(),
            self::COLUMN_EVENT_TIME => microtime(true),
            self::COLUMN_DETAILS => json_encode([
                'method' => $request->getMethod(),
            ]),
        ];
        $this->getPersistence()->insert(self::TABLE_NAME, $data);
    }

    /**
     * @inheritdoc
     */
    public function find(array $filters = [], array $options = [])
    {
        $queryBuilder = $this->getQueryBuilder();
        if (isset($options['limit'])) {
            $queryBuilder->setMaxResults(intval($options['limit']));
        }
        if (isset($options['offset'])) {
            $queryBuilder->setFirstResult(intval($options['offset']));
        }

        foreach ($filters as $filter) {
            $this->addFilter($queryBuilder, $filter);
        }
        return new RdsRequestLogIterator($this->getPersistence(), $queryBuilder);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array $filter
     */
    private function addFilter(QueryBuilder $queryBuilder, array $filter)
    {
        $colName = strtolower($filter[0]);
        $operation = strtolower($filter[1]);
        $val = $filter[2];
        $val2 = isset($filter[3]) ? $filter[3] : null;
        
        if (!in_array($colName, [
            self::USER_ID,
            self::USER_ROLES,
            self::COLUMN_ACTION,
            self::COLUMN_EVENT_TIME,
            self::COLUMN_DETAILS,
        ])) {
            return;
        }

        if (!in_array($operation, ['<', '>', '<>', '<=', '>=', '=', 'between', 'like'])) {
            return;
        }
        $params = [];
        if ($operation === 'between') {
            $queryBuilder->where("r.$colName between ? AND ?");
            $params[] = $val;
            $params[] = $val2;
        } else {
            $queryBuilder->where("r.$colName $operation ?");
            $params[] = $val;
        }
        $params = array_merge($queryBuilder->getParameters(), $params);
        $queryBuilder->setParameters($params);
    }

    /**
     * @return \common_persistence_SqlPersistence
     */
    private function getPersistence()
    {
        $persistenceManager = $this->getServiceManager()->get(\common_persistence_Manager::SERVICE_ID);
        return $persistenceManager->getPersistenceById($this->getOption(self::OPTION_PERSISTENCE));
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    private function getQueryBuilder()
    {
        if ($this->connection === null) {
            $this->connection = \Doctrine\DBAL\DriverManager::getConnection(
                $this->getPersistence()->getDriver()->getParams(),
                new \Doctrine\DBAL\Configuration()
            );
        }

        return $this->connection->createQueryBuilder()->select('*')->from(self::TABLE_NAME, 'r');
    }

    /**
     * Initialize RDS Request log storage and register serrvice.
     *
     * @param string $persistenceId
     * @return \common_report_Report
     */
    static function install($persistenceId = 'default')
    {
        $persistence = \common_persistence_Manager::getPersistence($persistenceId);

        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;

        try {
            $table = $schema->createTable(self::TABLE_NAME);
            $table->addOption('engine', 'InnoDB');
            $table->addColumn(static::COLUMN_USER_ID, "string", ["length" => 255]);
            $table->addColumn(static::COLUMN_USER_ROLES, "string", ["notnull" => true]);
            $table->addColumn(static::COLUMN_ACTION, "string", ["notnull" => false, "length" => 4096]);
            $table->addColumn(static::COLUMN_EVENT_TIME, 'decimal', ['precision' => 14, 'scale'=>4, "notnull" => true]);
            $table->addColumn(static::COLUMN_DETAILS, "text", ["notnull" => false]);
            $table->addIndex([static::COLUMN_USER_ID], 'IDX_' . static::TABLE_NAME . '_' . static::COLUMN_USER_ID);
            $table->addIndex([static::COLUMN_EVENT_TIME], 'IDX_' . static::TABLE_NAME . '_' . static::COLUMN_EVENT_TIME);
        } catch(SchemaException $e) {
            \common_Logger::i('Database Schema already up to date.');
        }

        $queries = $persistence->getPlatform()->getMigrateSchemaSql($fromSchema, $schema);
        foreach ($queries as $query) {
            $persistence->exec($query);
        }

        ServiceManager::getServiceManager()->register(
            self::SERVICE_ID,
            new self([self::OPTION_PERSISTENCE => $persistenceId])
        );
        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('User activity log successfully registered.'));
    }
}