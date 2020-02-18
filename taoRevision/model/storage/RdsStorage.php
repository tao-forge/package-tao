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
 */

namespace oat\taoRevision\model\storage;

use common_ext_Namespace;
use common_ext_NamespaceManager;
use common_persistence_SqlPersistence;
use core_kernel_classes_Triple as Triple;
use Doctrine\DBAL\Schema\Schema;
use oat\generis\model\kernel\persistence\smoothsql\search\driver\TaoSearchDriver;
use oat\generis\model\OntologyRdfs;
use oat\generis\persistence\PersistenceManager;
use oat\taoRevision\model\RevisionNotFound;
use oat\taoRevision\model\Revision;
use oat\oatbox\service\ConfigurableService;
use oat\taoRevision\model\RevisionStorageInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use oat\taoRevision\model\SchemaProviderInterface;

/**
 * Storage class for the revision data
 *
 * @author Joel Bout <joel@taotesting.com>
 */
class RdsStorage extends ConfigurableService implements RevisionStorageInterface, SchemaProviderInterface
{
    public const REVISION_TABLE_NAME = 'revision';

    public const REVISION_RESOURCE = 'resource';
    public const REVISION_VERSION = 'version';
    public const REVISION_USER = 'user';
    public const REVISION_CREATED = 'created';
    public const REVISION_MESSAGE = 'message';

    public const DATA_TABLE_NAME = 'revision_data';

    public const DATA_RESOURCE = 'resource';
    public const DATA_VERSION = 'version';
    public const DATA_SUBJECT = 'subject';
    public const DATA_PREDICATE = 'predicate';
    public const DATA_OBJECT = 'object';
    public const DATA_LANGUAGE = 'language';

    /** @var common_persistence_SqlPersistence */
    private $persistence;

    public function getPersistence()
    {
        if ($this->persistence === null) {
            $this->persistence = $this->getServiceLocator()
                ->get(PersistenceManager::SERVICE_ID)
                ->getPersistenceById($this->getPersistenceId());
        }
        return $this->persistence;
    }

    /**
     * @param Revision $revision
     * @param Triple[] $data
     *
     * @return Revision
     */
    public function addRevision(Revision $revision, array $data)
    {
        $this->getPersistence()->insert(
            self::REVISION_TABLE_NAME,
            [
                self::REVISION_RESOURCE => $revision->getResourceId(),
                self::REVISION_VERSION => $revision->getVersion(),
                self::REVISION_USER => $revision->getAuthorId(),
                self::REVISION_MESSAGE => $revision->getMessage(),
                self::REVISION_CREATED => $revision->getDateCreated(),
            ]
        );

        if (!empty($data)) {
            $this->saveData($revision, $data);
        }

        return $revision;
    }

    /**
     *
     * @param string $resourceId
     * @param int    $version
     *
     * @return Revision
     * @throws RevisionNotFound
     */
    public function getRevision(string $resourceId, int $version): Revision
    {
        $sql = 'SELECT * FROM ' . self::REVISION_TABLE_NAME
            . ' WHERE (' . self::REVISION_RESOURCE . ' = ? AND ' . self::REVISION_VERSION . ' = ?)';
        $params = [$resourceId, $version];

        $variables = $this->getPersistence()->query($sql, $params)->fetchAll();

        if (count($variables) !== 1) {
            throw new RevisionNotFound($resourceId, $version);
        }
        $variable = reset($variables);

        return new Revision(
            $variable[self::REVISION_RESOURCE],
            $variable[self::REVISION_VERSION],
            $variable[self::REVISION_CREATED],
            $variable[self::REVISION_USER],
            $variable[self::REVISION_MESSAGE]
        );
    }

    /**
     * @param string $resourceId
     *
     * @return Revision[]
     */
    public function getAllRevisions(string $resourceId)
    {
        $sql = 'SELECT * FROM ' . self::REVISION_TABLE_NAME . ' WHERE ' . self::REVISION_RESOURCE . ' = ?';
        $params = [$resourceId];
        $variables = $this->getPersistence()->query($sql, $params)->fetchAll();

        return $this->buildRevisionCollection($variables);
    }

    /**
     * @param array $variables
     * @return Revision[]
     */
    public function buildRevisionCollection(array $variables)
    {
        $revisions = [];
        foreach ($variables as $variable) {
            $revisions[] = new Revision(
                $variable[self::REVISION_RESOURCE],
                $variable[self::REVISION_VERSION],
                $variable[self::REVISION_CREATED],
                $variable[self::REVISION_USER],
                $variable[self::REVISION_MESSAGE]
            );
        }

        return $revisions;
    }

    /**
     * @param Revision $revision
     *
     * @return Triple[]
     */
    public function getData(Revision $revision)
    {
        // retrieve data
        $query = 'SELECT * FROM ' . self::DATA_TABLE_NAME . ' WHERE ' . self::DATA_RESOURCE . ' = ? AND ' . self::DATA_VERSION . ' = ?';
        $result = $this->getPersistence()->query($query, [$revision->getResourceId(), $revision->getVersion()]);

        $triples = [];
        while ($statement = $result->fetch()) {
            $triple = $triple = $this->prepareDataObject($statement, $this->getLocalModel()->getModelId());
            $triples[] = $triple;
        }

        return $triples;
    }

    /**
     *
     * @param Revision $revision
     * @param Triple[] $data
     *
     * @return bool
     */
    protected function saveData(Revision $revision, array $data)
    {
        $dataToSave = [];

        foreach ($data as $triple) {
            $dataToSave[] = [
                self::DATA_RESOURCE => $revision->getResourceId(),
                self::DATA_VERSION => $revision->getVersion(),
                self::DATA_SUBJECT => $triple->subject,
                self::DATA_PREDICATE => $triple->predicate,
                self::DATA_OBJECT => $triple->object,
                self::DATA_LANGUAGE => $triple->lg,
            ];
        }

        return $this->getPersistence()->insertMultiple(self::DATA_TABLE_NAME, $dataToSave);
    }

    /**
     * @inheritDoc
     */
    public function getRevisionsDataByQuery(string $query)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->select('*');
        $queryBuilder->from(self::DATA_TABLE_NAME);

        $fieldName = self::DATA_OBJECT;
        $condition = "$fieldName {$this->getLike()} '%$query%'";
        $queryBuilder->where($condition);
        $queryBuilder->andWhere(sprintf('%s = \'%s\'', self::DATA_PREDICATE, OntologyRdfs::RDFS_LABEL));

        $result = $this->getPersistence()->query($queryBuilder->getSQL());
        $revisionsData = [];

        while ($statement = $result->fetch()) {
            $triple = $this->prepareDataObject($statement, $this->getLocalModel()->getModelId());
            $revisionsData[] = $triple;
        }
        return $revisionsData;
    }

    /**
     * @param array  $statement
     * @param string $modelId
     *
     * @return Triple
     */
    private function prepareDataObject(array $statement, string $modelId)
    {
        $triple = new Triple();
        $triple->modelid = $modelId;
        $triple->subject = $statement[self::DATA_SUBJECT];
        $triple->predicate = $statement[self::DATA_PREDICATE];
        $triple->object = $statement[self::DATA_OBJECT];
        $triple->lg = $statement[self::DATA_LANGUAGE];

        return $triple;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        return $this->getPersistence()->getPlatForm()->getQueryBuilder();
    }

    /**
     * @return common_ext_Namespace
     */
    protected function getLocalModel()
    {
        return common_ext_NamespaceManager::singleton()->getLocalNamespace();
    }

    /**
     * @return string
     */
    protected function getLike()
    {
        return (new TaoSearchDriver())->like();
    }

    /**
     * @inheritDoc
     */
    public function getSchema(Schema $schema)
    {
        return $this->getServiceLocator()->get(RdsSqlSchema::class)->getSchema($schema);
    }

    public function getPersistenceId()
    {
        return $this->getOption(self::OPTION_PERSISTENCE);
    }
}
