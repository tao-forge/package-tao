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
 * Copyright (c) 2017-2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\tao\model\security\xsrf;

use oat\oatbox\service\ConfigurableService;
use oat\tao\model\security\TokenGenerator;
use oat\oatbox\service\exception\InvalidService;

/**
 * This service let's you manage tokens to protect against XSRF.
 * The protection works using this workflow :
 *  1. Token pool gets generated and stored by front-end
 *  2. Front-end adds a token header using the token header "X-CSRF-Token"
 *  3. Back-end verifies the token using \oat\tao\model\security\xsrf\CsrfValidatorTrait
 *
 * @see \oat\tao\model\security\xsrf\CsrfValidatorTrait
 * @author Bertrand Chevrier <bertrand@taotesting.com>
 * @author Martijn Swinkels <martijn@taotesting.com>
 */
class TokenService extends ConfigurableService
{
    use TokenGenerator;

    const SERVICE_ID = 'tao/security-xsrf-token';

    // options keys
    const POOL_SIZE_OPT  = 'poolSize';
    const TIME_LIMIT_OPT = 'timeLimit';
    const OPTION_STORE = 'store';

    const DEFAULT_POOL_SIZE = 10;
    const DEFAULT_TIME_LIMIT = 0;

    const CSRF_TOKEN_HEADER = 'X-CSRF-Token';
    const FORM_POOL = 'form_pool';

    /**
     * Create a new TokenService
     *
     * @param array $options the configurations options
     *              - `poolSize` to limit the number of active tokens (0 means unlimited - default to 10)
     *              - `timeLimit` to limit the validity of tokens, in seconds (0 means unlimited - default 0)
     *              - `store` the TokenStore where the tokens are stored
     * @throws InvalidService
     */
    public function __construct($options = [])
    {
        parent::__construct($options);

        if ($this->getPoolSize() <= 0 && $this->getTimeLimit() <= 0) {
            \common_Logger::w('The pool size and the time limit are both unlimited. Tokens won\'t be invalidated. The store will just grow.');
        }

        $store = $this->getStore();
        if ($store === null || !$store instanceof TokenStore) {
            throw new InvalidService('The token service requires a TokenStore');
        }
    }

    /**
     * Generates, stores and return a brand new token
     * Triggers the pool invalidation.
     *
     * @deprecated
     * @return string the token
     * @throws \common_Exception
     */
    public function createToken()
    {
        $time = microtime(true);
        $token = $this->generate();

        $store = $this->getStore();

        $pool = $this->invalidate($store->getTokens());

        $pool[] = [
            'ts' => $time,
            'token' => $token
        ];

        $store->setTokens($pool);

        return $token;
    }

    /**
     * Check if the given token is valid
     * (does not revoke)
     *
     * @param string $token The given token to validate
     * @return boolean
     */
    public function checkToken($token)
    {
        $valid = false;
        $pool = $this->getStore()->getTokens();

        if ($pool !== null) {
            foreach ($pool as $savedToken) {
                if ($savedToken['token'] === $token && !$this->isExpired($token)) {
                    $valid = true;
                }
            }
        }

        return $valid;
    }

    /**
     * Check if the given token is valid
     *
     * @param string $token
     * @return boolean|string
     * @throws \common_Exception`
     * @throws \common_exception_Unauthorized
     */
    public function validateToken($token)
    {
        $isValid = false;
        $expired = false;
        $pool = $this->getStore()->getTokens();

        if ($pool !== null) {
            foreach ($pool as $savedToken) {
                if ($savedToken['token'] === $token) {
                    if ($this->isExpired($token)) {
                        $expired = true;
                        break;
                    }
                    $isValid = true;
                    break;
                }
            }
        }

        if ($expired === true) {
            $this->revokeToken($token);
        }

        if ($isValid !== true) {
            throw new \common_exception_Unauthorized();
        }

        $this->revokeToken($token);

        return $this->addNewToken();
    }

    /**
     * Check if the given token has expired.
     *
     * @param $token
     * @return bool
     */
    private function isExpired($token)
    {
        $expired = false;
        $actualTime = microtime(true);
        $timeLimit  = $this->getTimeLimit();

        if (($timeLimit > 0) && $token['ts'] + $timeLimit < $actualTime) {
            $expired = true;
        }

        return $expired;
    }

    /**
     * Revokes the given token
     *
     * @param string $token
     * @return true
     */
    public function revokeToken($token)
    {
        $revoked = false;
        $store = $this->getStore();
        $pool = $store->getTokens();

        if ($pool !== null) {
            foreach ($pool as $key => $savedToken) {
                if ($savedToken['token'] === $token) {
                    unset($pool[$key]);
                    $revoked = true;
                    break;
                }
            }
        }

        $store->setTokens($pool);

        return $revoked;
    }

    /**
     * Gets this session's name for token
     * @return string
     */
    public function getTokenName()
    {
        $session = \PHPSession::singleton();

        if ($session->hasAttribute(TokenStore::TOKEN_NAME)) {
            $name = $session->getAttribute(TokenStore::TOKEN_NAME);
        } else {
            $name = 'tao_' . substr(md5(microtime()), rand(0, 25), 7);
            $session->setAttribute(TokenStore::TOKEN_NAME, $name);
        }

        return $name;
    }

    /**
     * Invalidate the tokens in the pool :
     *  - remove the oldest if the pool raises it's size limit
     *  - remove the expired tokens
     * @return array the invalidated pool
     */
    protected function invalidate($pool)
    {
        $actualTime = microtime(true);
        $timeLimit  = $this->getTimeLimit();

        $reduced = array_filter($pool, function ($token) use ($actualTime, $timeLimit) {
            if (!isset($token['ts'], $token['token'])) {
                return false;
            }
            if ($timeLimit > 0) {
                return $token['ts'] + $timeLimit > $actualTime;
            }
            return true;
        });

        if ($this->getPoolSize() > 0 && count($reduced) > 0) {
            usort($reduced, function ($a, $b) {
                if ($a['ts'] === $b['ts']) {
                    return 0;
                }
                return $a['ts'] < $b['ts'] ? -1 : 1;
            });

            //remove the elements at the begining to fit the pool size
            while (count($reduced) >= $this->getPoolSize()) {
                array_shift($reduced);
            }
        }
        return $reduced;
    }

    /**
     * Get the configured pool size
     * @return int the pool size, 10 by default
     */
    public function getPoolSize()
    {
        $poolSize = self::DEFAULT_POOL_SIZE;
        if ($this->hasOption(self::POOL_SIZE_OPT)) {
            $poolSize = (int)$this->getOption(self::POOL_SIZE_OPT);
        }
        return $poolSize;
    }

    /**
     * Get the configured time limit in seconds
     * @return int the limit
     */
    protected function getTimeLimit()
    {
        $timeLimit = self::DEFAULT_TIME_LIMIT;
        if ($this->hasOption(self::TIME_LIMIT_OPT)) {
            $timeLimit = (int)$this->getOption(self::TIME_LIMIT_OPT);
        }
        return $timeLimit;
    }

    /**
     * Get the configured store
     * @return TokenStore the store
     */
    protected function getStore()
    {
        $store = null;
        if ($this->hasOption(self::OPTION_STORE)) {
            $store = $this->getOption(self::OPTION_STORE);
        }
        return $store;
    }

    /**
     * Generate a token pool, and return it.
     *
     * @return string[]
     * @throws \common_Exception
     */
    public function generateTokenPool()
    {
        $store = $this->getStore();
        $pool = $store->getTokens();

        if ($this->getTimeLimit() > 0) {
            foreach ($pool as $key => $token) {
                if ($this->isExpired($token)) {
                    $this->revokeToken($token);
                }
            }
        }

        $pool = $store->getTokens();
        $remainingPoolSize = $this->getPoolSize() - count($pool);

        for ($i = 0; $i < $remainingPoolSize; $i++) {
            $pool[] = [
                'ts' => microtime(true),
                'token' => $this->generate()
            ];
        }

        $store->setTokens($pool);

        return $pool;
    }

    /**
     * Add a new token, and return it.
     *
     * @return string
     * @throws \common_Exception
     */
    private function addNewToken()
    {
        $time = microtime(true);
        $newToken = $this->generate();
        $store = $this->getStore();
        $pool = $store->getTokens();

        $pool[] = [
            'ts' => $time,
            'token' => $newToken
        ];

        $store->setTokens($pool);

        return $newToken;
    }

    /**
     * Add and return a token that can be used for forms.
     * @return string[]
     * @throws \common_Exception
     */
    public function addFormToken()
    {
        $store = $this->getStore();
        $tokenPool = $store->getTokens();

        $tokenPool[self::FORM_POOL] = [
            'ts' => microtime(true),
            'token' => $this->generate()
        ];

        $store->setTokens($tokenPool);

        return $tokenPool[self::FORM_POOL];
    }
}
