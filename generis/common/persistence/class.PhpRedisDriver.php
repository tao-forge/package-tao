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
 * Copyright (c) 2013-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package
 *
 */
class common_persistence_PhpRedisDriver implements common_persistence_AdvKvDriver
{

    const DEFAULT_PORT     = 6379;
    const DEFAULT_ATTEMPT  = 3;
    const DEFAULT_TIMEOUT  = 5; // in seconds
    const RETRY_DELAY      = 500000; // Eq to 0.5s

    /**
     * @var Redis
     */
    private $connection;

    /**
     * @var $params
     */
    private $params;

    /**
     * store connection params and try to connect
     * @see common_persistence_Driver::connect()
     */
    function connect($key, array $params)
    {
        $this->params = $params;
        $this->connectionSet($params);

        return new common_persistence_AdvKeyValuePersistence($params, $this);
    }

    /**
     * create a new connection using stored parameters
     * @param array $params
     * @throws common_exception_Error
     */
    function connectionSet(array $params)
    {
        $this->connection = new Redis();
        if ($this->connection == false) {
            throw new common_exception_Error("Redis php module not found");
        }
        if (!isset($params['host'])) {
            throw new common_exception_Error('Missing host information for Redis driver');
        }
        $host    = $params['host'];
        $port    = isset($params['port']) ? $params['port'] : self::DEFAULT_PORT;
        $timeout = isset($params['timeout']) ? $params['timeout'] : self::DEFAULT_TIMEOUT;
        $persist = isset($params['pconnect']) ? $params['pconnect'] : true;
        $this->params['attempt'] = isset($params['attempt']) ? $params['attempt'] : self::DEFAULT_ATTEMPT;

        if ($persist) {
            $this->connection->pconnect($host , $port , $timeout);
        } else {
            $this->connection->connect($host , $port , $timeout);
        }

        if (isset($params['password'])) {
            $this->connection->auth($params['password']);
        }
    }

    /**
     * @param $method
     * @param array $params
     * @param $retry
     * @param int $attempt
     * @return mixed
     * @throws Exception
     */
    protected function callWithRetry( $method , array $params , $retry , $attempt = 1) {

        $success       = false;
        $lastException = null;
        $result        = false;

        while (!$success && $attempt < $retry) {
            $attempt++;
            try {
                $result = call_user_func_array([$this->connection , $method] , $params);
                $success = true;
            } catch (\Exception $e) {
                $lastException = $e;
                \common_Logger::d('Redis  ' . $method . ' failed ' . $attempt . '/' . $retry . ' :  ' . $e->getMessage());
                $delay = rand(self::RETRY_DELAY , self::RETRY_DELAY*2);
                $this->connection->close();
                usleep($delay);
                $this->connectionSet($this->params);
            }
        }

        if (!$success) {
            throw $lastException;
        }
        return $result;

    }

    public function set($key, $value, $ttl = null)
    {
        if (! is_null($ttl)) {
            $params = [$key, $value, $ttl];
        } else {
            $params = [$key, $value];
        }
        return $this->callWithRetry('set' , $params , $this->params['attempt']);
        
    }
    
    public function get($key) {

        return $this->callWithRetry('get' , [$key] , $this->params['attempt']);

    }
    
    public function exists($key) {
        return $this->callWithRetry('exists' , [$key] , $this->params['attempt']);
    }
    
    public function del($key) {
        return $this->callWithRetry('del' , [$key] , $this->params['attempt']);
    }

    //O(N) where N is the number of fields being set.
    public function hmSet($key, $fields) {
        return $this->callWithRetry('hmSet' , [$key, $fields] , $this->params['attempt']);
    }
    //Time complexity: O(1)
    public function hExists($key, $field)
    {
        return (bool)$this->callWithRetry('hExists', [$key, $field], $this->params['attempt']);
    }

    //Time complexity: O(1)
    public function hSet($key, $field, $value){
        return $this->callWithRetry('hSet' , [$key, $field, $value] , $this->params['attempt']);
    }
    //Time complexity: O(1)
    public function hGet($key, $field){
        return $this->callWithRetry('hGet' , [$key, $field] , $this->params['attempt']);
    }
    //Time complexity: O(N) where N is the size of the hash.
    public function hGetAll($key){
        return $this->callWithRetry('hGetAll' , [$key] , $this->params['attempt']);
    }
    //Time complexity: O(N)
    public function keys($pattern) {
        return $this->callWithRetry('keys' , [$pattern] , $this->params['attempt']);
    }
    //Time complexity: O(1)
    public function incr($key) {
        return $this->callWithRetry('incr' , [$key] , $this->params['attempt']);
    }

}
