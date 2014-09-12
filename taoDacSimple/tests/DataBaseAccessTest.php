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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * Test database access
 * 
 * @author Antoine Robin, <antoine.robin@vesperiagroup.com>
 * @package taodacSimple
 *
 */
class DataBaseAccessTest extends PHPUnit_Framework_TestCase {


    /**
     * @var \oat\taoDacSimple\model\DataBaseAccess
     */
    protected $instance;

    public function setUp() {
        $this->instance = new \oat\taoDacSimple\model\DataBaseAccess();
    }

    public function tearDown() {
        $this->instance = null;
    }

    /**
     * Return a persistence Mock object
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    public function getPersistenceMock($queryParams, $queryFixture, $resultFixture) {


        $statementMock = $this->getMock('PDOStatementFake', ['fetchAll'],[],'', false, false, true, false);
        $statementMock->expects($this->once())
            ->method('fetchAll')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($resultFixture));

        $persistenceMock = $this->getMockForAbstractClass('common_persistence_sql_pdo_mysql_Driver', [], 'common_persistence_Driver_Mock', false, false, true, ['query'], false);
        $persistenceMock->expects($this->once())
            ->method('query')
            ->with($queryFixture, $queryParams)
            ->will($this->returnValue($statementMock));

        return $persistenceMock;
    }

    /**
     * @return array
     */
    public function resourceIdsProvider() {
        return [
            [[1]],
            [[1, 2, 3, 4]],
            [[1, 2]],
        ];
    }

    /**
     * @dataProvider resourceIdsProvider
     * @preserveGlobalState disable
     * @param $resourceIds
     */
    public function testGetUsersWithPermissions($resourceIds)
    {



        $inQuery = implode(',', array_fill(0, count($resourceIds), '?'));
        $queryFixture = "SELECT resource_id, user_id, privilege FROM " . \oat\taoDacSimple\model\DataBaseAccess::TABLE_PRIVILEGES_NAME . "
        WHERE resource_id IN ($inQuery)";

        $resultFixture = [
            ['fixture']
        ];

        $persistenceMock = $this->getPersistenceMock($resourceIds, $queryFixture, $resultFixture);

        $this->instance->setPersistence($persistenceMock);

        $this->assertSame($resultFixture, $this->instance->getUsersWithPermissions($resourceIds));
    }


    /**
     * @return array
     */
    public function getPermissionProvider() {
        return [
            [[1,2,3], [1, 2, 3]],
            [[1], [2]],
        ];
    }
    /**
     * Get the permissions a user has on a list of ressources
     * @dataProvider getPermissionProvider
     * @access public
     * @param  array $userIds
     * @param  array $resourceIds
     * @return array()
     */
    public function testGetPermissions($userIds, array $resourceIds)
    {
        // get privileges for a user/roles and a resource
        $returnValue = array();

        $inQueryResource = implode(',', array_fill(0, count($resourceIds), '?'));
        $inQueryUser = implode(',', array_fill(0, count($userIds), '?'));
        $query = "SELECT resource_id, privilege FROM " . \oat\taoDacSimple\model\DataBaseAccess::TABLE_PRIVILEGES_NAME
            . " WHERE resource_id IN ($inQueryResource) AND user_id IN ($inQueryUser)";


        $fetchResultFixture = [
            ['resource_id' => 1, 'privilege' => 'open'],
            ['resource_id' => 2, 'privilege' => 'close'],
            ['resource_id' => 3, 'privilege' => 'create'],
            ['resource_id' => 3, 'privilege' => 'delete'],
        ];

        $resultFixture = [
            1 =>  ['open'],
            2 => ['close'],
            3 => ['create', 'delete']
        ];

        $params = $resourceIds;
        foreach ($userIds as $userId) {
            $params[] = $userId;
        }
        $persistenceMock = $this->getPersistenceMock($params, $query, $fetchResultFixture);

        $this->instance->setPersistence($persistenceMock);

        $this->assertSame($resultFixture, $this->instance->getPermissions($userIds, $resourceIds));
    }

    /**
     * add permissions of a user to a resource
     *
     * @access public
     * @param  string $user
     * @param  string $resourceId
     * @param  array $rights
     * @return boolean
     */
    public function addPermissions($user, $resourceId, $rights)
    {

        foreach ($rights as $privilege) {
            // add a line with user URI, resource Id and privilege
            $this->persistence->insert(
                self::TABLE_PRIVILEGES_NAME,
                array('user_id' => $user, 'resource_id' => $resourceId, 'privilege' => $privilege)
            );
        }
        return true;
    }

    /**
     * remove permissions to a resource for a user
     *
     * @access public
     * @param  string $user
     * @param  string $resourceId
     * @param  array $rights
     * @return boolean
     */
    public function removePermissions($user, $resourceId, $rights)
    {
        //get all entries that match (user,resourceId) and remove them
        $inQueryPrivilege = implode(',', array_fill(0, count($rights), '?'));
        $query = "DELETE FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id = ? AND privilege IN ($inQueryPrivilege) AND user_id = ?";
        $params = array($resourceId);
        foreach ($rights as $rightId) {
            $params[] = $rightId;
        }
        $params[] = $user;

        $this->persistence->exec($query, $params);

        return true;
    }

    /**
     * Remove all permissions from a resource
     *
     * @access public
     * @param  array $resourceIds
     * @return boolean
     */
    public function removeAllPermissions($resourceIds)
    {
        //get all entries that match (resourceId) and remove them
        $inQuery = implode(',', array_fill(0, count($resourceIds), '?'));
        $query = "DELETE FROM " . self::TABLE_PRIVILEGES_NAME . " WHERE resource_id IN ($inQuery)";
        $this->persistence->exec($query, $resourceIds);

        return true;
    }


}