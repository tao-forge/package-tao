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

namespace oat\taoTaskQueue\model\Task;

/**
 * TaskInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface TaskInterface extends \JsonSerializable
{
    // Fully Qualified Class Name
    const JSON_TASK_CLASS_NAME_KEY = 'taskFqcn';
    const JSON_METADATA_KEY = 'metadata';
    const JSON_METADATA_ID_KEY = '__id__';
    const JSON_METADATA_OWNER_KEY = '__owner__';
    const JSON_METADATA_CREATED_AT_KEY = '__created_at__';
    const JSON_PARAMETERS_KEY = 'parameters';

    /**
     * @param string $id Should be a unique id. Use \common_Utils::getNewUri() to get one.
     * @param string $owner
     */
    public function __construct($id, $owner);

    /**
     * @return \common_report_Report
     */
    public function __invoke();

    /**
     * @return string
     */
    public function __toString();

    /**
     * Gets the internally generated message id.
     *
     * @return string
     */
    public function getId();

    /**
     * Set message metadata
     *
     * @param  string|array|\Traversable $spec
     * @param  mixed $value
     */
    public function setMetadata($spec, $value = null);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMetadata($key, $default = null);

    /**
     * Set task parameter
     *
     * @param  string|array|\Traversable $spec
     * @param  mixed $value
     * @return TaskInterface
     */
    public function setParameter($spec, $value = null);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getParameter($key, $default = null);

    /**
     * Return all parameters
     *
     * @return array
     */
    public function getParameters();

    /**
     * @param \DateTime $dateTime
     * @return TaskInterface
     */
    public function setCreatedAt(\DateTime $dateTime);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string $owner
     * @return TaskInterface
     */
    public function setOwner($owner);

    /**
     * @return string
     */
    public function getOwner();
}