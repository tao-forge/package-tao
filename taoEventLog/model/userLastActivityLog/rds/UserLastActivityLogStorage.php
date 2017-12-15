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

namespace oat\taoEventLog\model\userLastActivityLog\rds;

use oat\oatbox\user\User;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\taoEventLog\model\RdsLogIterator;
use oat\taoEventLog\model\userLastActivityLog\UserLastActivityLog;
use oat\oatbox\service\ConfigurableService;

/**
 * Class UserLastActivityLogStorage
 * @package oat\taoEventLog\model\userLastActivityLog\rds
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class UserLastActivityLogStorage extends ConfigurableService implements UserLastActivityLog
{
    const OPTION_PERSISTENCE = 'persistence_id';
    const TABLE_NAME = 'user_last_activity_log';

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
    public function log(User $user, $action, array $details = [])
    {
        $userId = $user->getIdentifier();
        if ($userId === null) {
            $userId = get_class($user);
        }

        $data = [
            self::USER_ID => $userId,
            self::USER_ROLES => ','. implode(',', $user->getRoles()). ',',
            self::COLUMN_ACTION => strval($action),
            self::COLUMN_EVENT_TIME => microtime(true),
            self::COLUMN_DETAILS => json_encode($details),
        ];
        $this->getPersistence()->exec('DELETE FROM ' . self::TABLE_NAME . ' WHERE ' . self::USER_ID . ' = \'' . $userId . '\'');
        $this->getPersistence()->insert(self::TABLE_NAME, $data);
    }

    /**
     * @inheritdoc
     */
    public function find(array $filters = [], array $options = [])
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->select('*');
        if (isset($options['limit'])) {
            $queryBuilder->setMaxResults(intval($options['limit']));
        }
        if (isset($options['offset'])) {
            $queryBuilder->setFirstResult(intval($options['offset']));
        }
        if (isset($options['group']) && in_array($options['group'], $this->getColumnNames())) {
            $queryBuilder->groupBy($options['group']);
        }

        foreach ($filters as $filter) {
            $this->addFilter($queryBuilder, $filter);
        }
        return new RdsLogIterator($this->getPersistence(), $queryBuilder);
    }

    /**
     * @inheritdoc
     */
    public function count(array $filters = [], array $options = [])
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->select('user_id');

        foreach ($filters as $filter) {
            $this->addFilter($queryBuilder, $filter);
        }
        if (isset($options['group']) && in_array($options['group'], $this->getColumnNames())) {
            $queryBuilder->select($options['group']);
            $queryBuilder->groupBy($options['group']);
        }

        $stmt = $this->getPersistence()->query(
            'SELECT count(*) as count FROM (' .$queryBuilder->getSQL() . ') as group_q', $queryBuilder->getParameters());
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        return intval($data['count']);
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
        
        if (!in_array($colName, $this->getColumnNames())) {
            return;
        }

        if (!in_array($operation, ['<', '>', '<>', '<=', '>=', '=', 'between', 'like'])) {
            return;
        }
        $params = [];
        if ($operation === 'between') {
            $condition = "r.$colName between ? AND ?";
            $params[] = $val;
            $params[] = $val2;
        } else {
            $condition = "r.$colName $operation ?";
            $params[] = $val;
        }

        $queryBuilder->andWhere($condition);

        $params = array_merge($queryBuilder->getParameters(), $params);
        $queryBuilder->setParameters($params);
    }

    /**
     * @return array
     */
    private function getColumnNames()
    {
        return [
            self::USER_ID,
            self::USER_ROLES,
            self::COLUMN_ACTION,
            self::COLUMN_EVENT_TIME,
            self::COLUMN_DETAILS,
        ];
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
     * @throws
     */
    private function getQueryBuilder()
    {
        if ($this->connection === null) {
            $this->connection = \Doctrine\DBAL\DriverManager::getConnection(
                $this->getPersistence()->getDriver()->getParams(),
                new \Doctrine\DBAL\Configuration()
            );
        }

        return $this->connection->createQueryBuilder()->from(self::TABLE_NAME, 'r');
    }

    /**
     * Initialize log storage
     *
     * @param \common_persistence_Persistence $persistence
     * @return \common_report_Report
     */
    public static function install($persistence)
    {
        $schemaManager = $persistence->getDriver()->getSchemaManager();
        $schema = $schemaManager->createSchema();
        $fromSchema = clone $schema;

        try {
            $table = $schema->createTable(self::TABLE_NAME);
            $table->addOption('engine', 'InnoDB');
            $table->addColumn(static::COLUMN_USER_ID, "string", ["length" => 255]);
            $table->addColumn(static::COLUMN_USER_ROLES, "string", ["notnull" => true, "length" => 4096]);
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

        return new \common_report_Report(\common_report_Report::TYPE_SUCCESS, __('User activity log successfully registered.'));
    }
}