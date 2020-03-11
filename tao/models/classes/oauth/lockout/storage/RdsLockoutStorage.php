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
 * Copyright (c) 2020 (original work) (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT)
 */

namespace oat\tao\model\oauth\lockout\storage;

use common_persistence_SqlPersistence as SqlPersistence;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Exception;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\service\ConfigurableService;

/**
 * Class RdsLockoutStorage
 *
 * @author Ivan Klimchuk <ivan@taotesting.com>
 * @package oat\tao\model\oauth\lockout\storage
 */
class RdsLockoutStorage extends ConfigurableService implements LockoutStorageInterface
{
    public const TABLE_NAME = 'oauth_lti_failures';

    public const FIELD_ID = 'id';
    public const FIELD_ADDRESS = 'address';
    public const FIELD_EXPIRE_AT = 'expire_at';
    public const FIELD_ATTEMPTS = 'attempts';

    /** @var SqlPersistence */
    private $persistence;

    /**
     * @param string $ip
     * @param int    $ttl
     *
     * @return int|mixed
     * @throws Exception
     */
    public function store(string $ip, int $ttl = 0)
    {
        $id = ip2long($ip);
        $expiredAt = time() + $ttl;

        $addressInfo = $this->getAddressInfo($id);

        if (!$addressInfo) {
            return $this->getPersistence()->insert(
                self::TABLE_NAME,
                [
                    self::FIELD_ID => ip2long($ip),
                    self::FIELD_ADDRESS => $ip,
                    self::FIELD_ATTEMPTS => 1, // first failed attempt
                    self::FIELD_EXPIRE_AT => $expiredAt
                ]
            );
        }

        $attempts = $addressInfo[self::FIELD_ATTEMPTS] + 1;

        $data = [
            'conditions' => [self::FIELD_ID => $id],
            'updateValues' => [
                self::FIELD_EXPIRE_AT => $expiredAt,
                self::FIELD_ATTEMPTS => $attempts
            ]
        ];

        return $this->getPersistence()->updateMultiple(self::TABLE_NAME, [$data]);
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    protected function getAddressInfo(int $id)
    {
        $queryBuilder = $this->getQueryBuilder()
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(sprintf('%s = ?', self::FIELD_ID));

        $entries = $this->getPersistence()->query($queryBuilder->getSQL(), [$id])->fetchAll();

        return reset($entries);
    }

    /**
     * @param string $ip
     *
     * @return int
     */
    public function getFailedAttempts(string $ip)
    {
        $queryBuilder = $this->getQueryBuilder()
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(sprintf('%s = ?', self::FIELD_ID))
            ->andWhere(sprintf('%s > ?', self::FIELD_EXPIRE_AT));

        $found = $this->getPersistence()
            ->query($queryBuilder->getSQL(), [ip2long($ip), time()])
            ->fetchAll();

        if (count($found)) {
            $found = reset($found);
            return (int)$found[self::FIELD_ATTEMPTS];
        }

        return 0;
    }

    /**
     * @return string
     */
    public function getPersistenceId()
    {
        return $this->getOption(self::OPTION_PERSISTENCE);
    }

    /**
     * @param Schema $schema
     *
     * @return mixed
     */
    public function getSchema(Schema $schema)
    {
        return $this->getServiceLocator()->get(RdsLockoutSchema::class)->getSchema($schema);
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->getPersistence()->getPlatForm()->getQueryBuilder();
    }

    /**
     * @return SqlPersistence
     */
    protected function getPersistence()
    {
        if ($this->persistence === null) {
            $this->persistence = $this->getServiceLocator()
                ->get(PersistenceManager::SERVICE_ID)
                ->getPersistenceById($this->getPersistenceId());
        }

        return $this->persistence;
    }
}
